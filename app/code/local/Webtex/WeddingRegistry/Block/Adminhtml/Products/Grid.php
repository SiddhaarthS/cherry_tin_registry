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
 
class Webtex_Weddingregistry_Block_Adminhtml_Products_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        
        $this->setId('weddingregistryProductsGrid');
        $this->setDefaultSort('desired');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $this->setId = $this->getRequest()->getParam('id', 0);
            
        $collection = Mage::getModel('webtexweddingregistry/item')->getCollection()
            ->addFieldToFilter('weddingregistry_id', $this->setId); 
            
        $collection->_afterLoader();                     

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {     
        $this->addColumn('product_name', array(
            'header'    => Mage::helper('webtexweddingregistry')->__('Product Name'),
            'align'     =>'left',
            'index'     => 'product_name',
            'filter'    => false,
            'sortable'  => false,
        ));
        
       $this->addColumn('sku', array(
          'header'    => Mage::helper('webtexweddingregistry')->__('SKU'),
          'align'     =>'left',
          'index'     => 'sku',
          'filter'    => false,
          'sortable'  => false,
        ));
        
        $this->addColumn('price', array(
          'header'    => Mage::helper('webtexweddingregistry')->__('Price'),
          'align'     =>'left',
          'index'     => 'price',
          'filter'    => false,
          'sortable'  => false,
        ));
        
        $this->addColumn('desired', array(
          'header'    => Mage::helper('webtexweddingregistry')->__('Desired'),
          'align'     =>'left',
          'index'     => 'desired',
        ));
        
        $this->addColumn('received', array(
          'header'    => Mage::helper('webtexweddingregistry')->__('Received'),
          'align'     =>'left',
          'index'     => 'received',
		  'renderer'  => 'Webtex_WeddingRegistry_Block_Adminhtml_Products_Inputrenderer',
        ));
        
         $this->addColumn('purchased', array(
          'header'    => Mage::helper('webtexweddingregistry')->__('Purchased'),
          'align'     =>'left',
          'index'     => 'purchased',
        ));

        $this->addColumn('edit',
            array(
                'header'    => 'Delete',
                'width'     => '50px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption' => 'Delete',
                        'url'     => array('base'=>'*/*/deleteItem'),
                        'field'   => 'iid',
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true,
            ));

        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/orders', array(
            'id'=>$row->getWeddingregistryId(),
            'product_id' => $row->getProductId(),
            'params' => $row->getParams())
        );
    }

}
