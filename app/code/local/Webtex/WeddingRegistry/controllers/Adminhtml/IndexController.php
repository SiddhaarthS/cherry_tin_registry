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
 * @package    Webtex_AttributeManager
 * @copyright  Copyright (c) 2011 Webtex Solutions, LLC (http://www.webtexsoftware.com/)
 * @license    http://www.webtexsoftware.com/LICENSE.txt End-User License Agreement
 */

require_once('Mage/Adminhtml/controllers/Sales/Order/CreateController.php');
class Webtex_Weddingregistry_Adminhtml_IndexController extends Mage_Adminhtml_Sales_Order_CreateController
{

    public function indexAction()
    {

        $this->loadLayout();
        
        $this->_setActiveMenu('customer/webtexweddingregistry');

        $this->_addContent($this->getLayout()->createBlock('webtexweddingregistry/adminhtml_reg_toolbar'));
        $this->_addContent($this->getLayout()->createBlock('webtexweddingregistry/adminhtml_reg_grid'));

        
        $this->renderLayout();
    }
    
    
    public function productsAction()
    {

        $this->loadLayout();
        
        $this->_setActiveMenu('customer/webtexweddingregistry');

        $this->_addContent($this->getLayout()->createBlock('webtexweddingregistry/adminhtml_products_toolbar'));
        $this->_addContent($this->getLayout()->createBlock('webtexweddingregistry/adminhtml_products_grid'));
		
		Mage::getModel('adminhtml/session')->setRedirectUrl(Mage::helper('core/url')->getCurrentUrl());
        
        $this->renderLayout();
    }
    
    public function ordersAction()
    {

        $this->loadLayout();
        
        $this->_setActiveMenu('customer/webtexweddingregistry');

        $this->_addContent($this->getLayout()->createBlock('webtexweddingregistry/adminhtml_orders_toolbar'));
        $this->_addContent($this->getLayout()->createBlock('webtexweddingregistry/adminhtml_orders_grid'));

        
        $this->renderLayout();
    }
	
	public function deleteRegAction()
    {
		$regId = $this->getRequest()->getParam('reg_id');
        
		if(isset($regId) && $regId)
		{
			try 
            {
				$model = Mage::getModel('webtexweddingregistry/webtexweddingregistry');
                $model->setId($regId);
			
			
   	            $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Registry was successfully deleted.'));
   	        }
            catch(Exception $e)
            {
                Mage::getSingleton('adminhtml/session')->addError($this->__('Ooops! Something wrong. Registry was not deleted.'));
            }
		}
		
		$this->_redirect('*/*/index'); 
    }
	
	public function adchangeAction()
	{
		$data = $this->getRequest()->getParams();
		
		$received = $data['received'];
		
		if(!is_array($received)) $received = array();
		
		foreach($received as $item_id => $zn)
		{
			$model = Mage::getModel('webtexweddingregistry/item');
			
			$model->load($item_id);
			$model->setReceived($zn);
			
			try 
            {
           	 $model->save();
           	} 
            catch(Exception $e) {}
		}
		
		$this->getResponse()->setRedirect(Mage::getModel('adminhtml/session')->getRedirectUrl());
        return;
	}

    public function editRegistryAction()
    {

        $oRegistry = Mage::getModel('webtexweddingregistry/webtexweddingregistry');
        $oSession = Mage::getSingleton('webtexweddingregistry/session');
        if($regId = $this->getRequest()->getParam('reg_id')) {
            $oRegistry->load($regId);
        } else {
            $customerId = $this->getRequest()->getParam('id');
            //if customer registry already exist
            $oTempReg = $oRegistry->loadByCustomer($customerId);
            if($oTempReg->getId()) {
                $oRegistry = $oTempReg;
            }
            else {
                $oRegistry->setCustomerId($customerId);
            }
        }
        $oSession->setRegistry($oRegistry);
        $this->loadLayout();

        $this->_addLeft($this->getLayout()->createBlock('webtexweddingregistry/adminhtml_reg_edit_tabs'))
            ->_addContent($this->getLayout()->createBlock('webtexweddingregistry/adminhtml_reg_edit'));


        $this->renderLayout();
    }

    private function _iniSessionVars()
    {
        $regId = $this->getRequest()->getParam('regId');
        $oRegistry = Mage::getModel('webtexweddingregistry/webtexweddingregistry')->load($regId);
        $oCustomer = Mage::getModel('customer/customer')->load($oRegistry->getCustomerId());
        $oSession = Mage::getSingleton('adminhtml/session_quote');
        $oSession->setCustomerId($oRegistry->getCustomerId());
        if($oCustomer->getStoreId() == 0)
        {
            //if customer created from admin
            $oSession->setStoreId((int) $oCustomer->getWebsiteId());
        }
        else
        {
            $oSession->setStoreId((int) $oCustomer->getStoreId());
        }

        $oSession->setRegistryId($regId);

    }

    public function initAddProductsAction()
    {
        $this->_iniSessionVars();
        $this->loadLayout();

        $this->renderLayout();
    }

    public function loadBlockAction()
    {
        $request = $this->getRequest();
        try {
            $this->_initSession()
                ->_processData();
        }
        catch (Mage_Core_Exception $e){
            $this->_reloadQuote();
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e){
            $this->_reloadQuote();
            $this->_getSession()->addException($e, $e->getMessage());
        }


        $asJson= $request->getParam('json');
        $block = $request->getParam('block');

        $update = $this->getLayout()->getUpdate();
        if ($asJson) {
            $update->addHandle('adminhtml_sales_order_create_load_block_json');
        } else {
            $update->addHandle('adminhtml_sales_order_create_load_block_plain');
        }

        if ($block) {
            $blocks = explode(',', $block);
            if ($asJson && !in_array('message', $blocks)) {
                $blocks[] = 'message';
            }

            foreach ($blocks as $block) {
                $update->addHandle('webtexweddingregistry_product_add_load_block_' . $block);
            }
        }
        $this->loadLayoutUpdates()->generateLayoutXml()->generateLayoutBlocks();
        $result = $this->getLayout()->getBlock('content')->toHtml();
        if ($request->getParam('as_js_varname')) {
            Mage::getSingleton('adminhtml/session')->setUpdateResult($result);
            $this->_redirect('*/*/showUpdateResult');
        } else {
            $this->getResponse()->setBody($result);
        }
    }

    public function saveAction()
    {
        $oSession = Mage::getSingleton('adminhtml/session_quote');
        $data['weddingregistry_id'] = $oSession->getRegistryId();
        $data['store'] = $oSession->getStoreId();

        $quote = $this->_getOrderCreateModel()->getQuote();
        $items = $quote->getAllVisibleItems();

        $aTempData = array();
        foreach($items as $item)
        {
            $data['product_id'] = $aTempData['product'] = $item->getProductId();
            $data['desired'] = $item->getQty();
            $oOption = $item->getOptionByCode('info_buyRequest');
            $aInfoBuyRequest = unserialize($oOption->getValue());
            $result = array_merge($aTempData, $aInfoBuyRequest);
            $data['params'] = serialize($result);

            $model = Mage::getModel('webtexweddingregistry/item');
            $model->setData($data);
            $model->save();
        }
        $this->_redirect('*/*/products', array('id' => $oSession->getRegistryId()));
    }

    public function deleteItemAction()
    {
        $itemId = $this->getRequest()->getParam('iid');

        $model = Mage::getModel('webtexweddingregistry/item')->load($itemId);
        $regId = $model->getGiftregistryId();
        $model->delete();
        $this->_redirect('*/*/products', array('id' => $regId));
    }

    public function createRegistryAction()
    {
        $this->loadLayout();

        $this->_setActiveMenu('customer/webtexweddingregistry');

        $this->_addContent($this->getLayout()->createBlock('webtexweddingregistry/adminhtml_reg_create_toolbar'));
        $this->_addContent($this->getLayout()->createBlock('webtexweddingregistry/adminhtml_reg_create_grid'));


        $this->renderLayout();
    }

    public function updateCustomersGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

}