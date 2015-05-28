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

class Webtex_WeddingRegistry_Block_Editregistry extends Mage_Core_Block_Template
{
    protected $_registryData = null;
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('webtexweddingregistry/editregistry.phtml');
    }
    
    public function getDateFormat()
    {
        return Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
    }
    
    public function getRegistryData()
    {
        if (is_null($this->_registryData)) 
        {
            $this->_registryData = Mage::helper('webtexweddingregistry')->getRegistry();
        }
        
        return $this->_registryData;
    }
    
    public function getSaveUrl()
    {
        $loadFromSSL = $_SERVER['SERVER_PORT']==443?true:false;
        return $this->getUrl('*/*/saveRegistry', array('_secure'=>$loadFromSSL));
    }
    
    public function getBackUrl()
    {
        return $this->getUrl('*/*/viewItems');
    }
    
    public function getCancelUrl()
    {
        return $this->getUrl('*/*/viewItems');
    }
    
    public function getDeleteRegistryUrl()
    {
        return $this->getUrl('*/*/deleteRegistry');
    }
    
    public function getAddressesHtmlSelect()
    {
        $options = array();
        $options[] = array(
                'value'=>'',
                'label'=>$this->__('None')
            );
        
        $addresses = Mage::helper('webtexweddingregistry')->getCurrentCustomer()->getAddresses();
        
        foreach ($addresses as $address) {
            $options[] = array(
                'value'=>$address->getId(),
                'label'=>$address->format('oneline')
            );
        }

        $addressId = $this->getRegistryData()->getAddressId();       
        

        $select = $this->getLayout()->createBlock('core/html_select');

        $select->setName('address_id')
            ->setId('address_id')
            ->setClass('address-select')
            //->setExtraParams('onchange="newAddress(!this.value)"')
            ->setValue($addressId)
            ->setOptions($options);

        
        if(count($addresses))
        {
            return $select->getHtml();
        }
        else
        {
            return false;
        }       
    }
}