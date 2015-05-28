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
 * @package    Webtex_GiftRegistry
 * @copyright  Copyright (c) 2011 Webtex Solutions, LLC (http://www.webtexsoftware.com/)
 * @license    http://www.webtexsoftware.com/LICENSE.txt End-User License Agreement
 */

class Webtex_WeddingRegistry_Block_Addresses extends Mage_Checkout_Block_Multishipping_Addresses
{
    /**
     * Retrieve HTML for addresses dropdown
     *
     * @param  $item
     * @return string
     */
    public function getAddressesHtmlSelect($item, $index)
    {
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName('ship['.$index.']['.$item->getQuoteItemId().'][address]')
            ->setId('ship_'.$index.'_'.$item->getQuoteItemId().'_address')
            ->setValue($item->getCustomerAddressId())
            ->setOptions(array_merge($this->getAddressOptions(), $this->getRegistryAddressOptions($item)));

        //$select = $this->getRegistryAddressOptions($item, $select);
        return $select->getHtml();
    }

    /**
     * Retrieve options for addresses dropdown
     *
     * @return array
     */
    public function getAddressOptions()
    {
        $options = $this->getData('address_options');
        if (is_null($options)) {
            $options = array();
            foreach ($this->getCustomer()->getAddresses() as $address) {
                $options[] = array(
                    'value' => $address->getId(),
                    'label' => $address->format('oneline')
                );
            }
            $this->setData('address_options', $options);
        }
        
        return $options;
    }

    public function getRegistryAddressOptions($item)
    {
      $customOptions = $item->getQuoteItem()->getOptionsByCode();

      if ($customOptions['info_buyRequest']) 
      {
        $info_buyRequest = unserialize($customOptions['info_buyRequest']->getValue());
        $webtex_weddingregistry_id = isset($info_buyRequest['webtex_weddingregistry_id']) && $info_buyRequest['webtex_weddingregistry_id'] ? $info_buyRequest['webtex_weddingregistry_id'] : false;       
      }

        if(isset($webtex_weddingregistry_id) && $webtex_weddingregistry_id)
        {
            $registry = Mage::helper('webtexweddingregistry')->getRegistryById($webtex_weddingregistry_id);

            if($registry->getData('address_id'))
            {
                $address = Mage::getModel('customer/address')->load($registry->getData('address_id'));
                $options = array(); //$this->getData('address_options');
                $options[] = array(
                    'value' => $address->getId(),
                    'label' => $address->format('oneline')
                );
                //$this->setData('address_options', $options);
                Mage::log($options);
                return $options;
            }
        }  
        return array();
    }

}
