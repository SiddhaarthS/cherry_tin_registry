<?php
/**
 * Webtex
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.webtexsoftware.com/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@webtexsoftware.com and we will send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to http://www.webtexsoftware.com for more information, 
 * or contact us through this email: info@webtexsoftware.com.
 *
 * @category   Webtex
 * @package    Webtex_WeddingRegistry
 * @copyright  Copyright (c) 2011 Webtex Solutions, LLC (http://www.webtexsoftware.com/)
 * @license    http://www.webtexsoftware.com/LICENSE.txt End-User License Agreement
 */

class Webtex_WeddingRegistry_Block_Registry extends Mage_Core_Block_Template
{
    private $_productCollection = null;
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('webtexweddingregistry/registry.phtml');
    }
    
    public function getRegistry()
    {
        $id = $this->getRequest()->getParam('id');
        return Mage::helper('webtexweddingregistry')->getRegistryByCode($id);
    }
    
    public function getRegistrantTitle()
    {
        if($id = $this->getRequest()->getParam('id'))
        {
            $registry = $this->getRegistry();
            $_array['firstname'] = $registry->getData('firstname');
            $_array['lastname'] = $registry->getData('lastname');
            $_array['cofirstname'] = $registry->getData('co_firstname');
            $_array['colastname'] = $registry->getData('co_lastname');
        
            $answer = '<b>' . $_array['firstname'] . ' ' . $_array['lastname'] . '</b>';
        
            if($_array['cofirstname'] || $_array['colastname'])
            {
                $answer .= ' ' . $this->__('and') . ' ';
                $answer .= '<b>' . $_array['cofirstname'] . ' ' . $_array['colastname'] . '</b>';
            }
            $answer .= ' ' . $this->__('Wedding Registry');
        
            return $answer;
        } 
    }
    
    public function isRegistry()
    {
        return !$this->getRegistry()->isEmpty() ? true : false; 
    }
    
    public function getProducts()
    {
        if (is_null($this->_productCollection)) 
        {
            $registry = $this->getRegistry();
            
            $this->_productCollection = Mage::helper('webtexweddingregistry')->getItemsCollection($registry->getData('registry_id'));                        
        }
                
        return $this->_productCollection;
    }
    
    public function getSortedProducts()
    {   
        $_array = array();
                        
        foreach($this->_productCollection as $item)
        {
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            
                                                                        
            $product->setOptionList($this->getOptionList($product, $item));
            $product->setMPrice($this->getPriceHtml($product, $item));
            $product->setDescription($this->getDescription($item));
            $product->setPriority($this->getPriority($item));
            $product->setDesired($this->getDesired($item));
            $product->setReceived($this->getReceived($item));
            if($product->getDesired() <= $product->getReceived())
            {
                $product->setPurchased(1);
            }
            else
            {
                $product->setPurchased(0);
            }
            
            $product->setItemRemoveUrl($this->getItemRemoveUrl($item));
            $product->setItemAddToCartUrl($this->getItemAddToCartUrl($item));
            $product->setItemId($item->getWeddingregistryItemId());

            $_array[] = $product;
        }               
        
        $sort_metod = $this->getRequest()->getParam('sort');
        
        if($sort_metod == 'default') usort($_array, array($this, 'def_sort'));
        elseif($sort_metod == 'price-to-high') usort($_array, array($this, 'price_to_high'));
        elseif($sort_metod == 'price-to-low') usort($_array, array($this, 'price_to_low'));
        elseif($sort_metod == 'purchased') usort($_array, array($this, 'sort_purchased'));
        else usort($_array, array($this, 'def_sort'));
        
        return $_array;                        
    }
    
    function def_sort($a, $b) 
    {
        $pos1=$a->getPriority();
        $pos2=$b->getPriority();

        if ($pos1==$pos2)
            return 0;
        else
            return ($pos1 > $pos2 ? -1 : 1);
    }
    
    function price_to_high($a, $b) 
    {
        $pos1=$a->getMPrice();
        $pos2=$b->getMPrice();

        if ($pos1==$pos2)
            return 0;
        else
            return ($pos1 < $pos2 ? -1 : 1);
    }
    
    function price_to_low($a, $b) 
    {
        $pos1=$a->getMPrice();
        $pos2=$b->getMPrice();

        if ($pos1==$pos2)
            return 0;
        else
            return ($pos1 > $pos2 ? -1 : 1);
    }
    
    function sort_purchased($a, $b) 
    {
        $pos1=$a->getPurchased();
        $pos2=$b->getPurchased();

        if ($pos1==$pos2)
            return 0;
        else
            return ($pos1 > $pos2 ? 1 : -1);
    }

    
    public function getProductsCount()
    {
        return count($this->getProducts());
    }
    
    public function getOptionList($product, $item)
    {
        if($product->getTypeInstance(true)->hasOptions($product))
        {
            $answer = Mage::getModel('webtexweddingregistry/product')->getOptionsHtml($item, $product);
            
            Mage::unregister('webtexweddingregistry_additional_price');
            Mage::register('webtexweddingregistry_additional_price', $answer['webtexPrice']);
            
            unset($answer['webtexPrice']);
            
            return $answer;
        }
        else
        {
            return false;
        }  
    }
    
public function getPriceHtml($product, $item)
	{

        $originalPrice = 0;
        if($product->getTypeId()=="grouped"){
            $params = unserialize($item->getParams());
            if(array_key_exists('super_group',$params)) {
                foreach($params['super_group'] as $key => $value) {
                    $prod = Mage::getModel('catalog/product')->load($key);
                    $originPrice += $prod->getFinalPrice() * $value;
                }
            }
        } else {
            $originPrice = $product->getFinalPrice();
        }

        if (Mage::registry('webtexweddingregistry_additional_price'))
        {
            $originPrice += Mage::registry('webtexweddingregistry_additional_price');
            Mage::unregister('webtexweddingregistry_additional_price');
        }
        
        return $originPrice;
	}
    
    public function getPriority($item)
    {         
        return $item->getPriority();
    }
    
    public function getDesired($item)
    {      
        return $item->getDesired();
    }
    
    public function getReceived($item)
    {    
        return $item->getReceived();
    }
    
    public function getDescription($item)
    {          
        return $item->getDescription();
    }
    
    public function getPriorityList($_id)
    {
        $_array = array(
            '0' => $this->__("Would Be Nice To Have"),
            '1' => $this->__("Would Like To Have"),
            '2' => $this->__("Must Have")
        );
        
        $_answer = $_array[$_id];
        
        return $_answer;
    }
    
    public function getItemRemoveUrl($item)
    {
        return $this->getUrl('*/*/removeItem',array('item'=>$item->getWeddingregistryItemId()));
    }
    
    public function getItemAddToCartUrl($item)
    {
        return $this->getUrl('*/*/cart',array('item'=>$item->getWeddingregistryItemId()));
    }
    
    public function getEditRegistryUrl()
    {
        return $this->getUrl('*/*/editRegistry');
    }
    
    public function getPublicRegistryUrl()
    {
        return $this->getUrl('*/*/registry', array(
                'id' => Mage::helper('webtexweddingregistry')->getRegistryCode()
            ));
    }
    
    public function getTellAboutUrl()
    {
        return $this->getUrl('*/*/tellAbout');
    }
    
    public function getPurchasedProduct()
    {
        foreach($this->getProducts() as $items)
        {
            if($this->getDesired($items) <= $this->getReceived($items))
            {
                $this->_countPurchasedProducts++;
            }
        }
        
        return $this->_countPurchasedProducts;
    }
    
    public function getFormatedOptionValue($optionValue)
    {
        $optionInfo = array();

        // define input data format
        if (is_array($optionValue)) {
            if (isset($optionValue['option_id'])) {
                $optionInfo = $optionValue;
                if (isset($optionInfo['value'])) {
                    $optionValue = $optionInfo['value'];
                }
            } elseif (isset($optionValue['value'])) {
                $optionValue = $optionValue['value'];
            }
        }

        // render customized option view
        if (isset($optionInfo['custom_view']) && $optionInfo['custom_view']) {
            $_default = array('value' => $optionValue);
            if (isset($optionInfo['option_type'])) {
                try {
                    $group = Mage::getModel('catalog/product_option')->groupFactory($optionInfo['option_type']);
                    return array('value' => $group->getCustomizedView($optionInfo));
                } catch (Exception $e) {
                    return $_default;
                }
            }
            return $_default;
        }

        // truncate standard view
        $result = array();
        if (is_array($optionValue)) {
            $_truncatedValue = implode("\n", $optionValue);
            $_truncatedValue = nl2br($_truncatedValue);
            return array('value' => $_truncatedValue);
        } else {
            $_truncatedValue = Mage::helper('core/string')->truncate($optionValue, 55, '');
            $_truncatedValue = nl2br($_truncatedValue);
        }

        $result = array('value' => $_truncatedValue);

        if (Mage::helper('core/string')->strlen($optionValue) > 55) {
            $result['value'] = $result['value'] . ' <a href="#" class="dots" onclick="return false">...</a>';
            $optionValue = nl2br($optionValue);
            $result = array_merge($result, array('full_view' => $optionValue));
        }

        return $result;
    }
    
    public function getImage()
    {
        $registry = $this->getRegistry();
        return $registry->getData('filename');
    }
    
    public function getResizeImage()
    {
        $image = $this->getImage();
        
        // actual path of image
        $imageUrl = Mage::getBaseDir('media'). DS .'webtexweddingregistry'. DS .$image;
     
        // path of the resized image to be saved
        // here, the resized image is saved in media/resized folder
        $imageResized = Mage::getBaseDir('media'). DS .'webtexweddingregistry'. DS .'resized'. DS .$image;
     
        // resize image only if the image file exists and the resized image file doesn't exist
        // the image is resized proportionally with the width/height 135px
     
        if (!file_exists($imageResized) && file_exists($imageUrl)) 
        {
            $imageObj = new Varien_Image($imageUrl);
            $imageObj->constrainOnly(TRUE);
            $imageObj->keepAspectRatio(TRUE);
            $imageObj->keepFrame(FALSE);
            $imageObj->resize(300, null);
            $imageObj->save($imageResized); 
        }
        
        return Mage::getBaseUrl('media').'webtexweddingregistry/resized/' .$image;
     }
}







