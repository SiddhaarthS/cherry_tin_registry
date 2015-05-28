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
 
class Webtex_Weddingregistry_Block_Adminhtml_Orders_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('weddingregistryGrid');
        $this->setDefaultSort('desired');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        
        $id = $this->getRequest()->getParam('id', 0);
        $product_id = $this->getRequest()->getParam('product_id', 0);
        $params = $this->getRequest()->getParam('params', 0);
       
        $collection = Mage::getModel('webtexweddingregistry/orders')->getCollection()
        ->addFieldToFilter('weddingregistry_id', $id)
        ->addFieldToFilter('product_id', $product_id)
        ->addFieldToFilter('params', $params)
        ->load()->getItems();

        $_ordersArray = array();
        
        foreach($collection as $item)
        {
            $_ordersArray[] = $item -> getOrderId();
        }    
       
        
        $collection = Mage::getResourceModel('sales/order_grid_collection');
                
        $collection->addAttributeToFilter('entity_id', array('in' => $_ordersArray));        
                
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Purchased from (store)'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
            ));
        }

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));

        /*$this->addColumn('billing_firstname', array(
            'header' => Mage::helper('sales')->__('Bill to First name'),
            'index' => 'billing_firstname',
        ));

        $this->addColumn('billing_lastname', array(
            'header' => Mage::helper('sales')->__('Bill to Last name'),
            'index' => 'billing_lastname',
        ));*/
        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
        ));

        /*$this->addColumn('shipping_firstname', array(
            'header' => Mage::helper('sales')->__('Ship to First name'),
            'index' => 'shipping_firstname',
        ));

        $this->addColumn('shipping_lastname', array(
            'header' => Mage::helper('sales')->__('Ship to Last name'),
            'index' => 'shipping_lastname',
        ));*/
        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
        ));

        $this->addColumn('base_grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $this->addColumn('action',
                array(
                    'header'    => Mage::helper('sales')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'     => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('sales')->__('View'),
                            'url'     => array('base'=>'adminhtml/sales_order/view'),
                            'field'   => 'order_id'
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true,
            ));
        }

        return parent::_prepareColumns();
    }
    
}
