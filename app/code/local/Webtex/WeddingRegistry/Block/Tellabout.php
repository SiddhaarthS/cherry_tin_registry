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

class Webtex_WeddingRegistry_Block_Tellabout extends Mage_Core_Block_Template
{
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('webtexweddingregistry/tellabout.phtml');
    }
    
    public function getSearchRegistryUrl()
    {
        return $this->getUrl('*/*/searchRegistry');
    }
    
    public function getPublicRegistryUrl()
    {
        return $this->getUrl('*/*/registry', array(
                'id' => Mage::helper('webtexweddingregistry')->getRegistryCode()
            ));
    }
    
    public function getSendEmailsUrl()
    {
        $loadFromSSL = $_SERVER['SERVER_PORT']==443?true:false;
        return $this->getUrl('*/*/sendEmails', array('_secure'=>$loadFromSSL));
    }
    
    public function getBackUrl()
    {
        return $this->getUrl('*/*/viewItems');
    }
    
    public function getRegistrantTitle()
    {
        $registry = Mage::helper('webtexweddingregistry')->getRegistry();
        $_array['firstname'] = $registry->getData('firstname');
        $_array['lastname'] = $registry->getData('lastname');
        $_array['cofirstname'] = $registry->getData('co_firstname');
        $_array['colastname'] = $registry->getData('co_lastname');
        
        $answer = $_array['firstname'] . ' ' . $_array['lastname'];
        
        if($_array['cofirstname'] || $_array['colastname'])
        {
            $answer .= ' ' . $this->__('And') . ' ';
            $answer .= $_array['cofirstname'] . ' ' . $_array['colastname'];
        }
        
        return $answer;
    }
    
    public function getDomainUrl()
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
    }
    
    public function getTextMessage()
    {
        $answer = $this->__('Hello') . ',<br /> ' .
$this->__('I thought you might like to know that I have created a Wedding Registry at') . ' <a href="'. $this->getDomainUrl() .'" >'. $this->getDomainUrl() .'</a>.<br /> '.
$this->__('You can find it at').' <a href="'. $this->getSearchRegistryUrl() .'" >'.$this->__('here').'</a> '.$this->__('by searching for my name or for my Registry ID:').' <b>'. Mage::helper('webtexweddingregistry')->getRegistryCode() .'</b>.'.
$this->__('You can also access my registry by this link:').' <a href="'. $this->getPublicRegistryUrl() .'">'. $this->getPublicRegistryUrl() .'</a>
<br /><br /> '.$this->__('Sincerely').',<br />'. $this->getRegistrantTitle();
        
        return $answer;
    }
    public function getFacebookAppId()
    {
        return Mage::getStoreConfig('webtexweddingregistry_webtexweddingregistry/webtexweddingregistry/appid');
    }

    public function getLogoSrc()
    {
        if (empty($this->_data['logo_src'])) {
            $this->_data['logo_src'] = Mage::getStoreConfig('design/header/logo_src');
        }
        return $this->getSkinUrl($this->_data['logo_src']);
    }


}







