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

require_once 'Mage/Adminhtml/controllers/Sales/OrderController.php'; 
 
class Webtex_Weddingregistry_OrderController extends Mage_Adminhtml_Sales_OrderController
{
    /**
     * Cancel order
     */
    public function cancelAction()
    {                       
        if ($order = $this->_initOrder()) {
            try {
                $order->cancel()
                    ->save();
                $this->_getSession()->addSuccess(
                    $this->__('The order has been cancelled.')
                );
                
                
                
                
                $collection = Mage::getModel('webtexweddingregistry/orders')->getCollection()
                    ->addFieldToFilter('order_id', $order->getId());
                
                $product_id = array();
                $registry_id = array();
                $parms = array();
                
                foreach ($collection as $item)
                {
                    $product_id[] = $item->getProductId();
                    $registry_id[] = $item->getWeddingregistryId();
                    $params[] = $item->getParams();
                }
                
                
                $ii = array();
                $qq = array();
                $items = $order->getAllItems();
                foreach ($items as $itemId => $item)
                {
                    $ii[$item->getProductId()] = $item->getQtyOrdered();
                }
    
                
                $qty = array();
                for($i=0; $i<count($product_id); $i++)
                {
                    if(isset($ii[$product_id[$i]]))
                    {
                        $qty[$i] = $ii[$product_id[$i]];    
                    }
                }    
                   
                
                
                
                for($i=0; $i<count($product_id); $i++)
                {
                    $collection = Mage::getModel('webtexweddingregistry/item')->getCollection()
                        ->addFieldToFilter('weddingregistry_id', $registry_id[$i])
                        ->addFieldToFilter('product_id', $product_id[$i])
                        ->addFieldToFilter('params', $params[$i]);
                        
                        
                    foreach($collection as $item)
                    {
                        $received = $item->getReceived();
                        $purchased = $item->getPurchased();
            
                        $data['received'] = $received - $qty[$i];
                        $data['purchased'] = $purchased - $qty[$i];
            
                        $model = Mage::getModel('webtexweddingregistry/item');
                        $model->setData($data)->setId($item->getId());
            
                        $result = array();
                
                        if(Mage::helper('webtexweddingregistry')->isItemInRegistry($item->getId(), $registry_id[$i]))
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
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError($this->__('The order has not been cancelled.'));
                Mage::logException($e);
            }
            $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
        }
    }
    
    
    public function massCancelAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $countCancelOrder = 0;
        $countNonCancelOrder = 0;
        foreach ($orderIds as $orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            if ($order->canCancel()) {
                $order->cancel()
                    ->save();
                $countCancelOrder++;
                
                
                
                $collection = Mage::getModel('webtexweddingregistry/orders')->getCollection()
                    ->addFieldToFilter('order_id', $order->getId());
                
                $product_id = array();
                $registry_id = array();
                $parms = array();
                
                foreach ($collection as $item)
                {
                    $product_id[] = $item->getProductId();
                    $registry_id[] = $item->getWeddingregistryId();
                    $params[] = $item->getParams();
                }
                
                
                $ii = array();
                $qq = array();
                $items = $order->getAllItems();
                foreach ($items as $itemId => $item)
                {
                    $ii[$item->getProductId()] = $item->getQtyOrdered();
                }
    
                
                $qty = array();
                for($i=0; $i<count($product_id); $i++)
                {
                    if(isset($ii[$product_id[$i]]))
                    {
                        $qty[$i] = $ii[$product_id[$i]];    
                    }
                }    
                   
                
                
                
                for($i=0; $i<count($product_id); $i++)
                {
                    $collection = Mage::getModel('webtexweddingregistry/item')->getCollection()
                        ->addFieldToFilter('weddingregistry_id', $registry_id[$i])
                        ->addFieldToFilter('product_id', $product_id[$i])
                        ->addFieldToFilter('params', $params[$i]);
                        
                        
                    foreach($collection as $item)
                    {
                        $received = $item->getReceived();
                        $purchased = $item->getPurchased();
            
                        $data['received'] = $received - $qty[$i];
                        $data['purchased'] = $purchased - $qty[$i];
            
                        $model = Mage::getModel('webtexweddingregistry/item');
                        $model->setData($data)->setId($item->getId());
            
                        $result = array();
                
                        if(Mage::helper('webtexweddingregistry')->isItemInRegistry($item->getId(), $registry_id[$i]))
                        {  
                            try 
                            {
                                $model->save();
                            } 
                            catch(Exception $e) {}
                        }                                      
                    }                                                                                                              
                }  
                
                
                
            } else {
                $countNonCancelOrder++;
            }
        }
        if ($countNonCancelOrder) {
            if ($countCancelOrder) {
                $this->_getSession()->addError($this->__('%s order(s) cannot be canceled', $countNonCancelOrder));
            } else {
                $this->_getSession()->addError($this->__('The order(s) cannot be canceled'));
            }
        }
        if ($countCancelOrder) {
            $this->_getSession()->addSuccess($this->__('%s order(s) have been canceled.', $countCancelOrder));
        }
        $this->_redirect('*/*/');
    }
}
