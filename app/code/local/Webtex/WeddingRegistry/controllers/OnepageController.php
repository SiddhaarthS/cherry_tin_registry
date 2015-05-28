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
 
require_once 'Mage/Checkout/controllers/OnepageController.php'; 
 
class Webtex_Weddingregistry_OnepageController extends Mage_Checkout_OnepageController
{         
    public function indexAction()
    {
        parent::indexAction();
       
        foreach(Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems() as $visitem)
        {
            $customOptions = $visitem->getOptionsByCode();
            if ($customOptions['info_buyRequest']) 
            {
                $info_buyRequest = unserialize($customOptions['info_buyRequest']->getValue());
                
                $webtex_weddingregistry_id = isset($info_buyRequest['webtex_weddingregistry_id']) ? $info_buyRequest['webtex_weddingregistry_id'] : '';
                $webtex_weddingregistry_item_id = $info_buyRequest['webtex_weddingregistry_item_id'];
               
                $item = Mage::getModel('webtexweddingregistry/item')->load($webtex_weddingregistry_item_id); 
            
            
                if(Mage::helper('webtexweddingregistry')->isItemInRegistry($webtex_weddingregistry_item_id))
                {
                    if($item->getReceived() >= $item->getDesired())
                    {
                        Mage::getSingleton('checkout/cart')->removeItem($visitem->getId())->save();
                        
                        Mage::getSingleton('checkout/session')->addError($this->__('Sorry, but one of the products you wanted to buy has already been purched by someone else. We\'ve removed this product from Shopping Cart for you.'));
                        
                        $this->_redirect('checkout/cart');
                    }
                }                       
            }
        }  
    }
    
    
    
    
    
    public function saveOrderAction()
    {
        foreach(Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems() as $visitem)
        {
            $customOptions = $visitem->getOptionsByCode();
            if ($customOptions['info_buyRequest']) 
            {
                $info_buyRequest = unserialize($customOptions['info_buyRequest']->getValue());
                
                $webtex_weddingregistry_id = isset($info_buyRequest['webtex_weddingregistry_id']) ? $info_buyRequest['webtex_weddingregistry_id'] : '';
                $webtex_weddingregistry_item_id = isset($info_buyRequest['webtex_weddingregistry_item_id']) ? $info_buyRequest['webtex_weddingregistry_item_id'] : '';
                
                $item = Mage::getModel('webtexweddingregistry/item')->load($webtex_weddingregistry_item_id);  
                     
                $received = $item->getReceived();
                $purchased = $item->getPurchased();       
            
                $data['received'] = $received + $visitem->getQty();
                $data['purchased'] = $purchased + $visitem->getQty();
            
                $model = Mage::getModel('webtexweddingregistry/item');
                $model->setData($data)->setId($webtex_weddingregistry_item_id);
            
                $result = array();
                
                if(Mage::helper('webtexweddingregistry')->isItemInRegistry($webtex_weddingregistry_item_id, $webtex_weddingregistry_id))
                {
                    if($received >= $item->getDesired())
                    {
                        Mage::getSingleton('checkout/cart')->removeItem($visitem->getId())->save();
                        
                        Mage::getSingleton('checkout/session')->addError($this->__('Sorry, but one of the products you wanted to buy has already been purched by someone else. We\'ve removed this product from Shopping Cart for you.'));
                        
                        $result['success'] = false;
                        $result['error'] = true;
                        $result['redirect'] = Mage::getUrl('checkout/cart');
                        $this->getResponse()->setBody(Zend_Json::encode($result));
                        return;
                    }
                    else
                    {
                        try 
                        {
   	                        $model->save();
       	                } 
                        catch(Exception $e) {}
                    }
                }         
            }
        }

        
        
        
        if ($this->_expireAjax()) {
            return;
        }

        $result = array();
        try {
            if ($requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds()) {
                $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
                if ($diff = array_diff($requiredAgreements, $postedAgreements)) {
                    $result['success'] = false;
                    $result['error'] = true;
                    $result['error_messages'] = $this->__('Please agree to all the terms and conditions before placing the order.');
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    return;
                }
            }
            if ($data = $this->getRequest()->getPost('payment', false)) {
                $this->getOnepage()->getQuote()->getPayment()->importData($data);
            }
            $this->getOnepage()->saveOrder();
            $redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();
            $result['success'] = true;
            $result['error']   = false;
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $result['success'] = false;
            $result['error'] = true;
            $result['error_messages'] = $e->getMessage();

            if ($gotoSection = $this->getOnepage()->getCheckout()->getGotoSection()) {
                $result['goto_section'] = $gotoSection;
                $this->getOnepage()->getCheckout()->setGotoSection(null);
            }

            if ($updateSection = $this->getOnepage()->getCheckout()->getUpdateSection()) {
                if (isset($this->_sectionUpdateFunctions[$updateSection])) {
                    $updateSectionFunction = $this->_sectionUpdateFunctions[$updateSection];
                    $result['update_section'] = array(
                        'name' => $updateSection,
                        'html' => $this->$updateSectionFunction()
                    );
                }
                $this->getOnepage()->getCheckout()->setUpdateSection(null);
            }
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $result['success']  = false;
            $result['error']    = true;
            $result['error_messages'] = $this->__('There was an error processing your order. Please contact us or try again later.');
        }
        $this->getOnepage()->getQuote()->save();
        /**
         * when there is redirect to third party, we don't want to save order yet.
         * we will save the order in return action.
         */
        if (isset($redirectUrl)) {
            $result['redirect'] = $redirectUrl;
        }
        
        $lastOrderId = $this->getOnepage()->getCheckout()->getLastOrderId();
        if($lastOrderId)
        {
            $model = Mage::getModel('webtexweddingregistry/orders');
            
            foreach(Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems() as $visitem)
            {
                $customOptions = $visitem->getOptionsByCode();
                if ($customOptions['info_buyRequest']) 
                {
                    $info_buyRequest = unserialize($customOptions['info_buyRequest']->getValue());
                
                    $webtex_weddingregistry_id = isset($info_buyRequest['webtex_weddingregistry_id']) ? $info_buyRequest['webtex_weddingregistry_id'] : '';
                    $webtex_weddingregistry_item_id = $info_buyRequest['webtex_weddingregistry_item_id'];
                    
                    $item = Mage::getModel('webtexweddingregistry/item')->load($webtex_weddingregistry_item_id);
                    
                    $data['weddingregistry_id'] = $webtex_weddingregistry_id;
                    $data['product_id'] = $visitem->getProductId();
                    $data['order_id'] = $lastOrderId;
                    unset($info_buyRequest['webtex_weddingregistry_id']);
                    unset($info_buyRequest['webtex_weddingregistry_item_id']);
                    $data['params'] = serialize($info_buyRequest);
                
                    $model->setData($data);
                
                    try 
                    {
                        $model->save();
   	                } 
                    catch(Exception $e) {}
                }
            }
            
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

    }
    
    
    
    public function saveShippingAction()
    {
        $this->_expireAjax();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            
            
            
            if(isset($data['weddingregistry'])&& $data['weddingregistry'])
            {
                $address = Mage::getModel('customer/address')->load((int)$data['weddingregistry']);
                $adr = $address->getData();
                $str = $adr['street'];
                unset($adr['street']);
                $adr['street'][] = $str;

                $result = $this->getOnepage()->saveShipping($adr, '');
            }
            else
            {
                $result = $this->getOnepage()->saveShipping($data, $customerAddressId);
            }
            

            if (!isset($result['error'])) {
                $result['goto_section'] = 'shipping_method';
                $result['update_section'] = array(
                    'name' => 'shipping-method',
                    'html' => $this->_getShippingMethodsHtml()
                );
            }

//            $this->loadLayout('checkout_onepage_shippingMethod');
//            $result['shipping_methods_html'] = $this->getLayout()->getBlock('root')->toHtml();
//            $result['shipping_methods_html'] = $this->_getShippingMethodsHtml();

            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }
    
    public function saveBillingAction()
    {
        $this->_expireAjax();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('billing', array());
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);

            if (!isset($result['error'])) {
                /* check quote for virtual */
                if ($this->getOnepage()->getQuote()->isVirtual()) {
                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );
                }
                elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {

                   $result['goto_section'] = 'shipping_method';

                    $result['update_section'] = array(
                        'name' => 'shipping-method',
                        'html' => $this->_getShippingMethodsHtml()
                    );

                    $result['allow_sections'] = array('shipping');
                    $result['duplicateBillingInfo'] = 'true';
                }
                elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 2) {

                  
                    foreach(Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems() as $visitem)
                    {
                        $customOptions = $visitem->getOptionsByCode();
                        if ($customOptions['info_buyRequest']) 
                        {
                            $info_buyRequest = unserialize($customOptions['info_buyRequest']->getValue());
                            
                            $webtex_weddingregistry_id = isset($info_buyRequest['webtex_weddingregistry_id']) && $info_buyRequest['webtex_weddingregistry_id'] ? $info_buyRequest['webtex_weddingregistry_id'] : false;
                            
                        }
                    }
        
                    if($webtex_weddingregistry_id)
                    {
                        $registry = Mage::helper('webtexweddingregistry')->getRegistryById($webtex_weddingregistry_id);
            
                        if($registry->getData('address_id'))
                        {
                            $address = Mage::getModel('customer/address')->load((int)$registry->getData('address_id'));
                            
                            $adr = $address->getData();
                            $str = $adr['street'];
                            unset($adr['street']);
                            $adr['street'][] = $str;
                  
                            
                            $this->getOnepage()->saveShipping($adr, '');
                            
                            $result['goto_section'] = 'shipping_method'; 
                            $result['update_section'] = array(
                                                            'name' => 'shipping-method',
                                                            'html' => $this->_getShippingMethodsHtml()
                                                            );
                                                                                                                        
                            $result['allow_sections'] = array('shipping');                            
                        }
                                   
                    }  

                  
                }
                else {
                    $result['goto_section'] = 'shipping';
                }
            }

            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }

       
}     
        