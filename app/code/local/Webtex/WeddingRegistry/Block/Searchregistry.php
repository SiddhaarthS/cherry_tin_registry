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

class Webtex_WeddingRegistry_Block_Searchregistry extends Mage_Core_Block_Template
{
    private $_resCollection = null;
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('webtexweddingregistry/searchregistry.phtml');
    }
    
    public function getSearchUrl()
    {
        return $this->getUrl('webtexweddingregistry/index/searchRegistry');
    }
    
    public function getResultsCount()
    {
        return $this->getResults()->getSize();
    }
    
    public function getResultTitle()
    {   
        if($id = strip_tags($this->getRequest()->getPost('registry_id')))
            {
                $answer = $this->__('Search Result for Registry Id:') . ' ';
                $answer .= $this->__('<b>'. $id .'</b>');
            }
            else if($lastname = strip_tags($this->getRequest()->getPost('lastname')))
            {
                $answer = $this->__('Search Result for:') . ' \'';
                if($firstname = strip_tags($this->getRequest()->getPost('firstname')))
                {
                    $answer .= strip_tags($this->getRequest()->getPost('firstname')) . ' ';
                }
                $answer .= $lastname;
                $answer .= '\'';
            }
            else
            {
                $answer = '';
            }
        
        return $answer;
    }
    
    public function isRequest()
    {
         return $this->getRequest()->getPost('registry_id') || $lastname = $this->getRequest()->getPost('lastname') ? true : false;
    }
    
    public function getResults()
    {
        if(is_null($this->_resCollection))
        {
            $this->_resCollection = Mage::getModel('webtexweddingregistry/webtexweddingregistry')->getCollection();
            

            
            if($id = $this->getRequest()->getPost('registry_id'))
            {
                $this->_resCollection->getSelect()->where('`weddingregistry_id` = ?' , $id);
            }
            else if($lastname = $this->getRequest()->getPost('lastname'))
            {
                $this->_resCollection->getSelect()->where('`active` = 0');
                
                $this->_resCollection->getSelect()->where('`lastname` LIKE ? OR `co_lastname` LIKE ? OR `maidenname` LIKE ? OR `co_maidenname` LIKE ?' , $lastname);
                    
                if($firstname = $this->getRequest()->getPost('firstname'))
                {
                    $this->_resCollection->getSelect()->where('`firstname` LIKE ? OR `co_firstname` LIKE ?' , $firstname);
                }
            }
            else
            {
                $this->_resCollection->getSelect()->where('0 = 1');
            }
        }           
            
        return $this->_resCollection;   
    }
    
    public function getRegistryName($registry)
    {     
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
        $answer .= ' ' . $this->__('Wedding Registry');
        
        if(!$registry->getStatus())
        {
            $url = $this->getUrl('*/*/registry', array(
                'id' => $registry->getWeddingregistryId()
            ));
            
            $answer = '<a href="'. $url .'">' . $answer . '</a>';
        }
        else
        {
            $answer .= '<br /><span style="font-size: 10px;">' . $this->__('To access this Registry you need to know it\'s ID or direct link to it.') . '</span>';
        }
        
        return $answer;
    }
    
    public function getSearchFirstName()
    {
        if($firstname = strip_tags($this->getRequest()->getPost('firstname')))
        {
            return $firstname;
        }
        else
        {
            return '';
        }
    }
    
    public function getSearchLastName()
    {
        if($firstname = strip_tags($this->getRequest()->getPost('lastname')))
        {
            return $firstname;
        }
        else
        {
            return '';
        }
    }
    
    public function getSearchRegistryId()
    {
        if($firstname = strip_tags($this->getRequest()->getPost('registry_id')))
        {
            return $firstname;
        }
        else
        {
            return '';
        }
    }
}







