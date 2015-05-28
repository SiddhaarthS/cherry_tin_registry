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
  
class Webtex_Weddingregistry_IndexController extends Mage_Core_Controller_Front_Action
{
    
    public function addRegistryAction()
    {
    	if(!Mage::helper('webtexweddingregistry')->isCustomerLogIn())
        {
            Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('webtexweddingregistry/index/addRegistry'));
            $this->_redirect('customer/account/login');
        }
        
        if(Mage::helper('webtexweddingregistry')->isRegistry())
        {
            $this->_redirect('*/*/viewItems');
        }
        
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');      
		$this->renderLayout(); 
    }
    
    public function editRegistryAction()
    {
    	if(!Mage::helper('webtexweddingregistry')->isCustomerLogIn())
        {
            $this->_redirect('customer/account/login');
        }
        
        if(!Mage::helper('webtexweddingregistry')->isRegistry())
        {
            $this->_redirect('*/*/addRegistry');
        }
        
        $this->loadLayout();     
		$this->renderLayout(); 
    }
    
    
    public function saveRegistryAction()
    {
    	if(!Mage::helper('webtexweddingregistry')->isCustomerLogIn())
        {
            $this->_redirect('customer/account/login');
        }
            
            if ($data = $this->getRequest()->getPost())
            {
                $data['customer_id'] = Mage::helper('webtexweddingregistry')->getCurrentCustomerId();

                if(!isset($data['address_id']) && isset($data['specify-address']))
                {
                    $shippingData = $this->_beforeSave($data);

                    unset($data['shipping-firstname']);
                    unset($data['shipping-lastname']);
                    foreach($shippingData as $k => $v)
                    {
                        if(!(($k == 'firstname') || ($k == 'lastname')))
                        {
                            unset($data[$k]);
                        }
                    }

                    $data['address_id'] = $this->_saveShippingAddress($shippingData, $data['customer_id']);
                }

                if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '')
                {
				    try {	
					   /* Starting upload */	
					   $uploader = new Varien_File_Uploader('filename');
					
					   // Any extention would work
	           		  $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					   $uploader->setAllowRenameFiles(true);
					
					   // Set the file upload mode 
					   // false -> get the file directly in the specified folder
					   // true -> get the file in the product like folders 
					   //	(file.jpg will go in something like /media/f/i/file.jpg)
					   $uploader->setFilesDispersion(false);
							
					   // We set media as the upload dir
					   $path = Mage::getBaseDir('media') . DS . 'webtexweddingregistry' . DS;
                 
					   $uploader->save($path, $_FILES['filename']['name'] );
					
				    } catch (Exception $e) 
                    {
                        Mage::getSingleton('customer/session')->addError($this->__('Ooops! Something wrong. Image was not saved.'));
                    }
	        
                    //this way the name is saved in DB
                    $data['filename'] = $uploader->getUploadedFileName();
                }
            
                $data['customer_id'] = Mage::helper('webtexweddingregistry')->getCurrentCustomerId();
                $data['firstname'] = trim($data['firstname']);
                $data['lastname'] = trim($data['lastname']);
                $data['email'] = trim($data['email']);
                $data['co_firstname'] = trim($data['co_firstname']);
                $data['co_lastname'] = trim($data['co_lastname']);
                $data['co_email'] = trim($data['co_email']);
                
                if(!Mage::helper('webtexweddingregistry')->isRegistry())
                {
                    $data['created_at'] = date('y-m-d h:i:s', time());
                    $data['weddingregistry_id'] = $this->gen_uuid();
                    $data['active'] = 0;
                }
                
            
                $model = Mage::getModel('webtexweddingregistry/webtexweddingregistry');
                $model->setData($data);
                
                if(Mage::helper('webtexweddingregistry')->isRegistry())
                {
                    $model->setId(Mage::helper('webtexweddingregistry')->getRegistryId());
                }
            
           	    try 
                {
   	                $model->save();
                    Mage::getSingleton('customer/session')->addSuccess($this->__('Registry was successfully saved.'));
   	            }
                catch(Exception $e)
                {
                    Mage::getSingleton('customer/session')->addError($this->__('Ooops! Something wrong. Registry was not saved.'));
                }
            }   
        
        
       $this->_redirect('*/*/viewItems');  
    }
    
    public function gen_uuid($len=12)
    {
        $hex = md5("webtexweddingregistry" . uniqid("", true));

        $pack = pack('H*', $hex);

        $uid = base64_encode($pack);        // max 22 chars

        //$uid = ereg_replace("[^A-Za-z0-9]", "", $uid);    // mixed case
        $uid = ereg_replace("[^A-Z0-9]", "", strtoupper($uid));    // uppercase only

        if ($len<4)
            $len=4;
        if ($len>128)
            $len=128;                       // prevent silliness, can remove

        while (strlen($uid)<$len)
            $uid = $uid . gen_uuid(22);     // append until length achieved

        return substr($uid, 0, $len);
    }
    
    public function deleteRegistryAction()
    {
        if(!Mage::helper('webtexweddingregistry')->isCustomerLogIn())
        {
            $this->_redirect('customer/account/login');
        }
        
        if(Mage::helper('webtexweddingregistry')->isRegistry())
        {
            
            $model = Mage::getModel('webtexweddingregistry/webtexweddingregistry');
            
            $data['weddingregistry_id'] = '';
            $data['customer_id'] = '';
            $data['active'] = 1;

            $model->setData($data);
            
            $model->setId(Mage::helper('webtexweddingregistry')->getRegistryId());
            
            try 
            {
                $model->save();
                Mage::getSingleton('customer/session')->addSuccess($this->__('Registry was successfully deleted.'));
            }
            catch(Exception $e)
            {
                Mage::getSingleton('customer/session')->addError($this->__('Ooops! Something wrong. Registry was not deleted.'));
            }
            
            $this->_redirect('customer/account');
        }
        
    }
     
     
    public function addItemAction()
    {
    	if(Mage::helper('webtexweddingregistry')->isCustomerLogIn() && Mage::helper('webtexweddingregistry')->isRegistry()) 
        {            

                $params = $this->getRequest()->getParams();
                $params['qty'] = 1;

                
                $data['weddingregistry_id'] = Mage::helper('webtexweddingregistry')->getRegistryId();
                $data['product_id'] = $this->getRequest()->getParam('product');
                $data['store'] = Mage::app()->getStore()->getId();
                $data['params'] = serialize($params);
                $data['desired'] = $this->getRequest()->getParam('qty') ? $this->getRequest()->getParam('qty') : 1;

            
                $model = Mage::getModel('webtexweddingregistry/item');
                $model->setData($data);
                
                $product = Mage::getModel('catalog/product')->load($data['product_id']);
                
                if ($product->getTypeInstance(true)->hasOptions($product))
                {
                    if(!Mage::helper('webtexweddingregistry')->isProductInRegistry($data['product_id'], null, $data['params']))
                    {
                        try 
                        {
           	            $model->save();
                            $this->getResponse()->setBody('{"success":true}');
       	                } 
                        catch(Exception $e) {}
                    }
                    else
                    {
                        Mage::getSingleton('customer/session')->addError($this->__('You selected a product that was already on your registry list.'));
                    }
                }
                else
                {
                    if(!Mage::helper('webtexweddingregistry')->isProductInRegistry($data['product_id']))
                    {
                        try 
                        {
           	            $model->save();
                            $this->getResponse()->setBody('{"success":true}');
       	                } 
                        catch(Exception $e) {}
                    }
                    else
                    {
                        Mage::getSingleton('customer/session')->addError($this->__('You selected a product that was already on your registry list.'));
                    }
                }   
                $this->getResponse()->setBody('{"error":true}');
        }
    }
    
    public function removeItemAction()
    {
    	if(Mage::helper('webtexweddingregistry')->isCustomerLogIn() && Mage::helper('webtexweddingregistry')->isRegistry()) 
        {            
            $item_id = $this->getRequest()->getParam('item');
            
            if(Mage::helper('webtexweddingregistry')->isItemInRegistry($item_id))
            {
            
                $model = Mage::getModel('webtexweddingregistry/item');
                $model->setId($item_id);

                try 
                {
                    $model->delete();
                    Mage::getSingleton('customer/session')->addSuccess($this->__('Item was successfully deleted.'));
                } 
                catch(Exception $e) 
                {
                    Mage::getSingleton('customer/session')->addError($this->__('Ooops! Something wrong. Item was not deleted.'));
                }
            } 
            
            $this->_redirect('*/*/viewItems');
        }
    }
    
    public function viewItemsAction()
    {
        if(!Mage::helper('webtexweddingregistry')->isCustomerLogIn())
        { 
            Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('webtexweddingregistry/index/viewItems'));
            $this->_redirect('customer/account/login');
        }
        
        if(!Mage::helper('webtexweddingregistry')->isRegistry())
        {
            $this->_redirect('*/*/addRegistry');
        }
        
        $this->loadLayout(); 
        $this->_initLayoutMessages('customer/session'); 
        $this->_initLayoutMessages('checkout/session');   
		$this->renderLayout(); 
    }
    
    public function registryAction()
    {    
        Mage::register('registry_id', $this->getRequest()->getParam('id'));
        
        $this->loadLayout(); 
        $this->_initLayoutMessages('customer/session'); 
        $this->_initLayoutMessages('checkout/session');   
		$this->renderLayout(); 
    }
    
    public function updateItemAction()
    {
        if(!Mage::helper('webtexweddingregistry')->isCustomerLogIn())
        {
            $this->_redirect('customer/account/login');
        }
        
        if(Mage::helper('webtexweddingregistry')->isRegistry())
        {
            $post = $this->getRequest()->getPost();
            
            foreach(Mage::helper('webtexweddingregistry')->getItemsIds() as $item_id)
            {
                if(isset($post['product'][$item_id]))
                {
                    $_array = $post['product'][$item_id];
                    
                    $data['description'] = isset($_array['description']) ? trim($_array['description']) : '';
                    $data['priority'] = isset($_array['priority']) ? $_array['priority'] : 0;
                    
                    $data['desired'] = isset($_array['desired']) ? $_array['desired'] : 0;
                    $data['received'] = isset($_array['received']) ? $_array['received'] : 0;
                
                
                    $model = Mage::getModel('webtexweddingregistry/item');
                    $model->setData($data)->setId($item_id);
            
                    try 
                    {
                        $model->save();
   	                } 
                    catch(Exception $e) 
                    {
                        $error = true;
                    }
                }
            }
            
            if(isset($error) && $error) Mage::getSingleton('customer/session')->addError($this->__('Ooops! Something wrong. Changes were not saved.'));
            else Mage::getSingleton('customer/session')->addSuccess($this->__('Changes were successfully saved.'));
            
            $this->_redirect('*/*/viewItems');
        }    
    }
    
    public function tellAboutAction()
    {    
        if(!Mage::helper('webtexweddingregistry')->isCustomerLogIn())
        {
            $this->_redirect('customer/account/login');
        }
        
        if(!Mage::helper('webtexweddingregistry')->isRegistry())
        {
            $this->_redirect('*/*/addRegistry');
        }
        
        $this->loadLayout(); 
        $this->_initLayoutMessages('customer/session');    
		$this->renderLayout(); 
    }
    
    public function searchRegistryAction()
    {            
        $collection = Mage::getModel('webtexweddingregistry/webtexweddingregistry')->getCollection();
            
        if($id = $this->getRequest()->getPost('registry_id'))
        {
            $collection->getSelect()->where('`weddingregistry_id` = ?' , $id);
                
            if($collection->getSize())
            {
                $this->_redirect('*/*/registry', array(
                    'id' => $id
                ));
            }
        }
        
        $this->loadLayout(); 
        $this->_initLayoutMessages('customer/session');    
		$this->renderLayout(); 
    }
    
    
    public function sendEmailsAction()
    {    
        if(!Mage::helper('webtexweddingregistry')->isCustomerLogIn())
        {
            $this->_redirect('customer/account/login');
        }
        
        if(!Mage::helper('webtexweddingregistry')->isRegistry())
        {
            $this->_redirect('*/*/addRegistry');
        }
        
        if($post = $this->getRequest()->getPost())
        {   
            $error = false;
            
            $emails = explode(',', $this->getRequest()->getPost('emails'));								
            
            if (empty($emails)) {
                $error = $this->__('Email address can\'t be empty.');
            }
            else {
                foreach ($emails as $index => $email) {
                    $email = trim($email);
                    if (!Zend_Validate::is($email, 'EmailAddress')) {
                        $error = $this->__('You input not valid email address.');
                        break;
                    }
                    $emails[$index] = $email;
                }
            }
            if ($error) {
                Mage::getSingleton('customer/session')->addError($error);
                Mage::getSingleton('customer/session')->setSharingForm($this->getRequest()->getPost());
                $this->_redirect('*/*/tellAbout');
                return;
            }
            
            try 
            {
            
                //Create an array of variables to assign to template
				$emailTemplateVariables['email_body'] = $this->getRequest()->getPost('email_body');
                $emailTemplateVariables['website_domain'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
                $emailTemplateVariables['registry_search_page'] = Mage::getUrl('*/*/searchRegistry');
                $emailTemplateVariables['registry_id'] = Mage::helper('webtexweddingregistry')->getRegistryCode();
                $emailTemplateVariables['registry_public_url'] = Mage::getUrl('*/*/registry', array('id' => Mage::helper('webtexweddingregistry')->getRegistryCode()));
                $emailTemplateVariables['registry_title'] = $this->getRegistrantTitle();
            
                $storeId = Mage::app()->getStore()->getId();
                
                $sender = array(    'email' => Mage::helper('webtexweddingregistry')->getRegistry()->getEmail(),
                                    'name' =>  Mage::helper('webtexweddingregistry')->getRegistry()->getFirstname() . ' ' . Mage::helper('webtexweddingregistry')->getRegistry()->getLastname());
                
                foreach($emails as $email) 
                {              
                    Mage::getModel('core/email_template')->setDesignConfig(array('area'=>'frontend', 'store'=>$storeId))
                    ->sendTransactional('webtexweddingregistry_template', $sender, $email, null, $emailTemplateVariables);   
                }
                
                Mage::getSingleton('customer/session')->addSuccess($this->__('Emails have been successfully sent.'));
            }
            catch (Exception $e) {}
        
        }              
        
        $this->_redirect('*/*/viewItems'); 
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
            $answer .= ' And ';
            $answer .= $_array['cofirstname'] . ' ' . $_array['colastname'];
        }
        
        return $answer;
    }
    
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }
    
    public function cartAction()
    {
    
        $id         = (int) $this->getRequest()->getParam('item');
        $item       = Mage::getModel('webtexweddingregistry/item')->load($id);
        $params     = unserialize($item->getParams());
        $params['webtex_weddingregistry_id'] = $item->getWeddingregistryId();  
        $params['webtex_weddingregistry_item_id'] = $id;
        
        
        try {
                $product = Mage::getModel('catalog/product')->load($item->getProductId())->setQty(1);
                $quote = Mage::getSingleton('checkout/cart')
                   ->addProduct($product, $params)
                   ->save();
                
                $reg = Mage::helper('webtexweddingregistry')->getRegistryById($item->getWeddingregistryId())->getWeddingregistryId();
                
                $this->_getSession()->setContinueShoppingUrl(Mage::getModel('core/url')->getUrl('webtexweddingregistry/index/registry', array('id' => $reg)));                       
                
            }
            catch(Exception $e) 
            {                
                Mage::getSingleton('checkout/session')->addError($e->getMessage());
                $url = Mage::getSingleton('checkout/session')->getRedirectUrl(true);
                if ($url) {
                    $url = Mage::getModel('core/url')->getUrl('catalog/product/view', array(
                        'id'=>$item->getProductId(),
                        'wishlist_next'=>1
                    ));
                    Mage::getSingleton('checkout/session')->setSingleWishlistId($item->getId());
                    $this->getResponse()->setRedirect($url);
                }
                else {
                    $this->getResponse()->setRedirect($this->_getRefererUrl());
                }
                return;
            }


        if (Mage::getStoreConfig('checkout/cart/redirect_to_cart')) {
            $this->_redirect('checkout/cart');
        } else {
            if ($this->getRequest()->getParam(self::PARAM_NAME_BASE64_URL)) {
                $this->getResponse()->setRedirect(
                    Mage::helper('core')->urlDecode($this->getRequest()->getParam(self::PARAM_NAME_BASE64_URL))
                );
            } else {
                if (!$quote->getHasError()){
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName()));
                    Mage::getSingleton('checkout/session')->addSuccess($message);
                }
                $this->getResponse()->setRedirect($this->_getRefererUrl());
            }
        }
    }

    public function getAddressFieldsAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    private function _saveShippingAddress($shippingData, $customerId)
    {
        $errors = array();

        $address  = Mage::getModel('customer/address');
        $addressForm = Mage::getModel('customer/form');
        $addressForm->setFormCode('customer_address_edit')
            ->setEntity($address);

        $addressErrors  = $addressForm->validateData($shippingData);

        if ($addressErrors !== true) {
            $errors = $addressErrors;
        }

        try {
            $address->setCustomerId($customerId)
                ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
            $address->addData($shippingData);
            $addressErrors = $address->validate();
            if ($addressErrors !== true) {
                $errors = array_merge($errors, $addressErrors);
            }
            if (count($errors) === 0) {
                $address->save();


                return $address->getId();
            } else {
                $this->_getSession()->setAddressFormData($this->getRequest()->getPost());
                foreach ($errors as $errorMessage) {
                    $this->_getSession()->addError($errorMessage);
                }
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
                ->addException($e, $e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
                ->addException($e, $this->__('Cannot save address.'));
        }
    }

    /*
     * prepare data for submit when new shipping address is adding
     */
    private function _beforeSave($data)
    {
        $shippingData = array();
        $shippingData['firstname'] = $data['shipping-firstname'];
        $shippingData['lastname'] = $data['shipping-lastname'];
        $shippingData['company'] = $data['company'];
        $shippingData['telephone'] = $data['telephone'];
        $shippingData['fax'] = $data['fax'];
        $shippingData['street'] = $data['street'];
        $shippingData['city'] = $data['city'];
        $shippingData['region_id'] = $data['region_id'];
        $shippingData['region'] = $data['region'];
        $shippingData['postcode'] = $data['postcode'];
        $shippingData['country_id'] = $data['country_id'];

        return $shippingData;
    }
}