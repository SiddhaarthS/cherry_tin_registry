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
 
class Webtex_WeddingRegistry_Block_Rewrites_CheckoutCartItemRendererGrouped extends Mage_Checkout_Block_Cart_Item_Renderer_Grouped
{
    public function getRegistryTitle()
    {
        $customOptions = $this->getItem()->getOptionsByCode();
        if ($customOptions['info_buyRequest']) 
        {
            $info_buyRequest = unserialize($customOptions['info_buyRequest']->getValue());
                            
            $webtex_weddingregistry_id = isset($info_buyRequest['webtex_weddingregistry_id']) && $info_buyRequest['webtex_weddingregistry_id'] ? $info_buyRequest['webtex_weddingregistry_id'] : false;       
        }
        
        $answer = '';
        
        if(isset($webtex_weddingregistry_id) && $webtex_weddingregistry_id)
        {
            $registry = Mage::helper('webtexweddingregistry')->getRegistryById($webtex_weddingregistry_id);
            $_array['firstname'] = $registry->getData('firstname');
            $_array['lastname'] = $registry->getData('lastname');
            $_array['cofirstname'] = $registry->getData('co_firstname');
            $_array['colastname'] = $registry->getData('co_lastname');
        
            $url = $this->getUrl('webtexweddingregistry/index/registry', array('id'=>$registry->getData('weddingregistry_id')));
        
            $answer = '<a href="'. $url .'">' . $this->__('For') . ' ' . $_array['firstname'] . ' ' . $_array['lastname'];
        
            if($_array['cofirstname'] || $_array['colastname'])
            {
                $answer .= ' ' . $this->__('And') . ' ';
                $answer .= $_array['cofirstname'] . ' ' . $_array['colastname'];
            }
            
            $answer .= ' ' . $this->__('Wedding Registry') . '</a>';
        }
        
        return $answer;
    }
    
}