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

require_once 'Mage/Paypal/controllers/StandardController.php';

class Webtex_Weddingregistry_StandardController extends Mage_Paypal_StandardController
{
    public function cancelAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getPaypalStandardQuoteId(true));
        if ($session->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
            if ($order->getId()) {
                $order->cancel()->save();
                
                
                
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
        }
        $this->_redirect('checkout/cart');
    }
}
