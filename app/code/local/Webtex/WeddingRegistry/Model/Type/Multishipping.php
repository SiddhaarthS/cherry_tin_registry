<?php
class Webtex_WeddingRegistry_Model_Type_Multishipping extends Mage_Checkout_Model_Type_Multishipping
{
    public function createOrders()
    {
        $orderIds = array();
        $this->_validate();
        $shippingAddresses = $this->getQuote()->getAllShippingAddresses();
        $orders = array();

        if ($this->getQuote()->hasVirtualItems()) {
            $shippingAddresses[] = $this->getQuote()->getBillingAddress();
        }

        try {
            foreach ($shippingAddresses as $address) {
                $order = $this->_prepareOrder($address);

                $orders[] = $order;
                Mage::dispatchEvent(
                    'checkout_type_multishipping_create_orders_single',
                    array('order'=>$order, 'address'=>$address)
                );
            }

            foreach ($orders as $order) {
                $order->place();
                $order->save();
                $this->_setRegistry($order);
                if ($order->getCanSendNewEmailFlag()){
                    $order->sendNewOrderEmail();
                }
                $orderIds[$order->getId()] = $order->getIncrementId();
            }

            Mage::getSingleton('core/session')->setOrderIds($orderIds);
            Mage::getSingleton('checkout/session')->setLastQuoteId($this->getQuote()->getId());

            $this->getQuote()
                ->setIsActive(false)
                ->save();

            Mage::dispatchEvent('checkout_submit_all_after', array('orders' => $orders, 'quote' => $this->getQuote()));

            return $this;
        } catch (Exception $e) {
            Mage::dispatchEvent('checkout_multishipping_refund_all', array('orders' => $orders));
            throw $e;
        }
    }

    protected function _setRegistry($order)
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

        $lastOrderId = $order->getIncrementId();
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
    }

}
