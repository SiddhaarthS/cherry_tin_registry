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
class Webtex_WeddingRegistry_Block_Billing extends Mage_Checkout_Block_Onepage_Billing
{
    public function getCheckBoxRegistryAddress()
    {
        foreach(Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems() as $visitem)
        {
            $customOptions = $visitem->getOptionsByCode();
            if ($customOptions['info_buyRequest']) 
            {
                $info_buyRequest = unserialize($customOptions['info_buyRequest']->getValue());
                            
                $webtex_weddingregistry_id = isset($info_buyRequest['webtex_weddingregistry_id']) && $info_buyRequest['webtex_weddingregistry_id'] ? $info_buyRequest['webtex_weddingregistry_id'] : false;       
            }
        }
        
        $answer = '';
        
        if(isset($webtex_weddingregistry_id) && $webtex_weddingregistry_id)
        {
            $registry = Mage::helper('webtexweddingregistry')->getRegistryById($webtex_weddingregistry_id);
            
            $name = $this->__('Ship to') . ' ';
            
            if($registry->getData('address_id'))
            {
                $_array['firstname'] = $registry->getData('firstname');
                $_array['lastname'] = $registry->getData('lastname');
                $_array['cofirstname'] = $registry->getData('co_firstname');
                $_array['colastname'] = $registry->getData('co_lastname');
        
                $name .= $_array['firstname'] . ' ' . $_array['lastname'];
        
                if($_array['cofirstname'] || $_array['colastname'])
                {
                    $name .= ' ' . $this->__('and') . ' ';
                    $name .= $_array['cofirstname'] . ' ' . $_array['colastname'];
                }
                $name .= ' ' . $this->__('Address');
        
        
                $answer = '<li class="control"><input type="radio" name="billing[use_for_shipping]" id="billing:use_for_shipping_weddingregistry" value="2" onclick="$(\'shipping:same_as_billing\').checked = false; $(\'shippingweddingregistry\').checked = true; setSameAsRedistry();"  class="radio"/><label for="billing:use_for_shipping_weddingregistry">';
                $answer .= $name; 
                $answer .= '</label></li>';
            }            
        }  
        return $answer;
    }
}