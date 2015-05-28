<?php
/**
 * Webtexension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Webtexension EULA 
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.webtexension.com/LICENSE.txt
 * If you did not receive a copy of the license && are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@webtexension.com so we can send you a copy.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to http://www.webtexension.com for more information.
 *
 * @category   Webtex
 * @package    Webtex_WeddingRegistry
 * @copyright  Copyright (c) 2011 Webtex Solutions, LLC (http://www.webtexsoftware.com/)
 * @license    http://www.webtexsoftware.com/LICENSE.txt End-User License Agreement
 */
 ?>

<?php
class Webtex_WeddingRegistry_Model_Product extends Mage_Core_Model_Abstract
{
    protected $_item;
    protected $_product;
    protected $_request;
    protected $_typeInstance;
    protected $_price = 0;
    
    public function getOptionsHtml($item, $product)
    {
        $this->_item = $item;
        $this->_product = $product;
        $this->_request = unserialize($this->_item->getParams());

        $this->typeInstance = $this->_product->getTypeInstance(true);
        
        if ($this->_item->getParams())
        {
            $bundleOptions = $this->_getBundleOptions();
            $superAttributes = $this->_getProductOptions();
            $customOptions = $this->_getCustomOptions();
        }

        $_array = array_merge($bundleOptions, $superAttributes, $customOptions);
        
        $_array['webtexPrice'] = $this->_price;

        return $_array;
    }
    
    protected function _getCustomOptions()
    {
        $customOptions = array();
        
        if(isset($this->_request['options']))
        {
            foreach ($this->_request['options'] as $optionId => $value)
            {
                if ($value AND $option = $this->_product->getOptionById($optionId)) 
                {
                    $group = $option->groupFactory($option->getType())
                    ->setOption($option)
                    ->setProduct($this->_product);
                    
                    if (in_array($option->getType(), array('date', 'date_time', 'time')))
                    {
                        $group->setUserValue($value);
                        
                        $group->setUserValue($value);
                                    
                        $group->setIsValid(true);                                
                        $group->setRequest(new Varien_Object($this->_request));                                
    
                        $value = $group->prepareForCart();
                        
                        $this->_price += $option->getPrice();
                    } 
                    elseif ($option->getType() == 'file')
                    {
                        if ($value['width'] > 0 && $value['height'] > 0) 
                        {
                            $sizes = $value['width'] . ' x ' . $value['height'] . ' ' . Mage::helper('catalog')->__('px.');
                        }
                        else
                        {
                            $sizes = '';
                        }
                                
                        $value['print_value'] =  sprintf('%s %s', Mage::helper('core')->htmlEscape($value['title']), $sizes);
                        
                        $this->_price += $option->getPrice();
                    }
                    else
                    {   
                        foreach ($option->getValues() as $_value)
                        {
                            if (is_array($value) AND in_array($_value->getId(), $value))
                            {
                                $this->_price += $_value->getPrice(true);
                            }
                            elseif (!is_array($value) AND $value == $_value->getOptionTypeId()) 
                            {
                                $this->_price += $_value->getPrice(true);
                            }
                        }
                        
                        $value = $group->getPrintableOptionValue($value);
                    }
                    $this->_price += $option->getPrice();
                
                    $customOptions[] = array('label'=>$option->getTitle(), 'value'=>$value); 
                }
                
            }
        }
        
        return $customOptions;
    }
    
    
    protected function _getBundleOptions()
    {
        $options = array();

        if(isset($this->_request['bundle_option']))
        {
            $bundleOptionsIds = array_keys($this->_request['bundle_option']);
   
            if ($bundleOptionsIds) 
            {  
                $optionsCollection = $this->typeInstance->getOptionsByIds($bundleOptionsIds, $this->_product);
            
                $selectionsQuoteItemOption = $this->_getArrayRecursive($this->_request['bundle_option']);

                $selectionsCollection = $this->typeInstance->getSelectionsByIds(
                    $selectionsQuoteItemOption,
                    $this->_product
                );

                $bundleOptions = $optionsCollection->appendSelections($selectionsCollection, true);
                foreach ($bundleOptions as $bundleOption) 
                {
                    if ($bundleOption->getSelections()) 
                    {
                        $option = array('label' => $bundleOption->getTitle(), "value" => array());
                        $bundleSelections = $bundleOption->getSelections();

                        foreach ($bundleSelections as $bundleSelection) {
                            $option['value'][] = $this->_getSelectionQty($bundleSelection->getOptionId()).' x '. Mage::helper('core')->htmlEscape($bundleSelection->getName()). ' ' .Mage::helper('core')->currency($this->_getSelectionFinalPrice($bundleSelection));
                        }

                        $options[] = $option;
                    }
                } 
            }
        }

        return $options;
    }
    
    
    public function _getProductOptions()
    {
        $attributesArray = array();
        
        if (isset($this->_request['super_attribute'])) {
            $data = $this->_request['super_attribute'];
            
            $attributes = $this->typeInstance->getConfigurableAttributes($this->_product);
            
            $usedAttributes = array();
            
            foreach($attributes as $value)
            {
                $usedAttributes[$value->getProductAttribute()->getId()] = $value;
            }

            foreach ($data as $attributeId => $attributeValue) 
            {
                if (isset($usedAttributes[$attributeId])) 
                {
                    $attribute = $usedAttributes[$attributeId];
                    $label = $attribute->getLabel();
                    $value = $attribute->getProductAttribute();
                    
                    $prices = $attribute->getPrices();                                                            
                                                                                
                    for($i=0; $i<count($prices); $i++)                                                            
                    {                                                
                        if($prices[$i]['value_index'] == $attributeValue)
                        {
                            $this->_price += $prices[$i]['pricing_value'];
                        }
                    }
                   
                    if ($value->getSourceModel()) {
                        $value = $value->getSource()->getOptionText($attributeValue);
                    }
                    else {
                        $value = '';
                    }

                    $attributesArray[] = array('label'=>$label, 'value'=>$value);
                }
            }
        }
        
        return $attributesArray;
    }
    
    
    
    
    protected function _getArrayRecursive($array)
    {
        if(is_array($array))
        {
            foreach($array as $value)
            {
                if(is_array($value))
                {
                    foreach($this->_getArrayRecursive($value) as $val)
                    {
                        if($val) $answer[] = $val;
                    }
                } 
                else if($value) $answer[] = $value;
            }
            return $answer;
        }
        return false;
    }
    
    protected function _getSelectionQty($selectionId)
    {
         return isset($this->_request['bundle_option_qty'][$selectionId]) ? $this->_request['bundle_option_qty'][$selectionId] : 1;
    }
    
    protected function _getSelectionFinalPrice($selectionProduct)
    {
        $bundleProduct = $this->_product;
        $price = $bundleProduct->getPriceModel()->getSelectionFinalPrice(
            $bundleProduct, $selectionProduct,
            $this->_request['qty'],
            $this->_getSelectionQty($selectionProduct->getOptionId())
        );
        
        $this->_price += $price;
        
        return $price;
    }
}
