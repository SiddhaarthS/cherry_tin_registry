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
 
class Webtex_WeddingRegistry_Model_Mysql4_Item_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('webtexweddingregistry/item');
 
    }
    
    public function _afterLoader()
    {
        parent::_afterLoad();
   
        foreach ($this as $item) 
        {
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            
            $answer = Mage::getModel('webtexweddingregistry/product')->getOptionsHtml($item, $product);

            $originPrice = $product->getFinalPrice();
        
            $price = $originPrice + $answer['webtexPrice'];
            
            
            $item->setProduct($product);
            $item->setProductName($product->getName());
            if($product->getTypeId() == 'configurable')
            {
                $params = unserialize($item->getParams());
                $oChildProduct = $product->getTypeInstance(true)->getProductByAttributes($params['super_attribute'], $product);
                $item->setSku($oChildProduct->getSku());
            }
            else
            {
                $item->setSku($product->getSku());
            }
            $item->setPrice(Mage::helper('core')->currency($price, true, false));
        }
        
        return $this;
    }
}