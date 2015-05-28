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


class Webtex_WeddingRegistry_Helper_Data extends Mage_Core_Helper_Abstract
{
    
    protected $_registry = null;
    protected $_registryId = null;
    protected $_isRegistry = null;
    protected $_registrybycode = null;
    protected $_registryCode = null;
    
    
    public function isCustomerLogIn()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }
    
    public function getCurrentCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }
        
    public function getCurrentCustomerId()
    {
        return Mage::getSingleton('customer/session')->getCustomer()->getId();
    }
    
    
    
    
    
    
    public function getRegistry()
    {
        if (is_null($this->_registry)) {
            $this->_registry = Mage::getModel('webtexweddingregistry/webtexweddingregistry')
				->loadByCustomer($this->getCurrentCustomerId());
        }
        return $this->_registry;
    }
    
    public function getRegistryId()
    {
        if (is_null($this->_registryId)) {
            $this->_registryId = $this->getRegistry()->getId();
        }
        return $this->_registryId;
    }
    
    public function getRegistryCode()
    {
        if (is_null($this->_registryCode)) {
            $this->_registryCode = $this->getRegistry()->getWeddingregistryId();
        }
        return $this->_registryCode;
    }
    
    public function isRegistry()
    {
        if ($this->isCustomerLogIn() && is_null($this->_isRegistry)) {
            $this->_isRegistry = !$this->getRegistry()->isEmpty() ? true : false; 
        }
        
        return $this->_isRegistry;
    }
    
    public function getRegistryById($id)
    {
        return Mage::getModel('webtexweddingregistry/webtexweddingregistry')
				->loadById($id);
    }
    
    public function getRegistryByCode($id)
    {
        if (is_null($this->_registrybycode)) {
            $this->_registrybycode = Mage::getModel('webtexweddingregistry/webtexweddingregistry')
				->loadByCode($id);
        }
        return $this->_registrybycode;
    }
    
    public function getItemsCollection($_id = null)
    {
        if(is_null($_id)) $_id = $this->getRegistryId() ? $this->getRegistryId() : Mage::registry('registry_id');

        
        return Mage::getModel('webtexweddingregistry/item')->getCollection()
            ->addFieldToFilter('weddingregistry_id', $_id);     
    }
    
    public function getItemsCount()
    {
        return $this->getItemsCollection()->getSize();    
    }
    
    public function getProduct($product_id, $registry_id = null, $params = null)
    {     
        if(!is_null($registry_id))
        {
             $answer = $this->getItemsCollection($registry_id)
                ->addFieldToFilter('product_id', $product_id);
             
             if(!is_null($params)) $answer->addFieldToFilter('params', $params);
        }
        else
        {
            $answer = $this->getItemsCollection()
                ->addFieldToFilter('product_id', $product_id);
             
             if(!is_null($params)) $answer->addFieldToFilter('params', $params);

        }
        
        return $answer;
    }
    
    public function isProductInRegistry($product_id, $registry_id = null, $params = null)
    {     
       
       return $this->getProduct($product_id, $registry_id, $params)->getSize();
       
    }
    
    public function getItem($item_id, $registry_id)
    {     
        if(!is_null($registry_id))
        {
             $answer = $this->getItemsCollection($registry_id)
                ->addFieldToFilter('weddingregistry_item_id', $item_id);
        }
        else
        {
            $answer = $this->getItemsCollection()
                ->addFieldToFilter('weddingregistry_item_id', $item_id);
        }
        
        return $answer;
    }
    
    public function isItemInRegistry($item_id, $registry_id = null)
    {     
        return $this->getItem($item_id, $registry_id)->getSize();
    }
    
    public function getItemsIds()
    {
        return $this->getItemsCollection()->getAllIds();
    }
    
}