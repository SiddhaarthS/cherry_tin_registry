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
 
class Webtex_Weddingregistry_Block_Adminhtml_Reg_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('setGrid');
        $this->setDefaultSort('customer_since');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('webtexweddingregistry/webtexweddingregistry_collection');
        
        $this->setCollection($collection);
        parent::_prepareCollection();
        
        foreach($this->getCollection() as $col)
        {
            $col->setLastname($col->getLastname() . ' ' . $col->getFirstname());
        }
        
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('weddingregistry_id', array(
            'header'    => Mage::helper('webtexweddingregistry')->__('Registry ID'),
            'align'     => 'center',
            'width'     => '120px',
            'renderer'  => 'Webtex_WeddingRegistry_Block_Adminhtml_Reg_Idrenderer',
            'sortable'  => true,
            'index'     => 'weddingregistry_id',
        ));
        
        $this->addColumn('lastname', array(
            'header'    => Mage::helper('webtexweddingregistry')->__('Registrant Name'),
            'align'     => 'left',
            'sortable'  => true,
            'index'     => 'lastname',
        ));
        
        $this->addColumn('email', array(
            'header'    => Mage::helper('webtexweddingregistry')->__('Registrant E-mail'),
            'align'     => 'left',
            'width'     => '300px',
            'sortable'  => true,
            'index'     => 'email',
        ));
        
        $this->addColumn('customer_since', array(
            'header'    => Mage::helper('customer')->__('Date Registry Was Created'),
            'type'      => 'datetime',
            'width'     => '200px',
            'align'     => 'center',
            'index'     => 'created_at',
            'gmtoffset' => true
        ));
        
        $this->addColumn('active', array(
            'header'    => Mage::helper('customer')->__('Active/Inactive'),
            'width'     => '100px',
            'type'      => 'options',
            'align'     => 'center',
            'index'     => 'active',
            'options'   => array(
                                '1' => 'Inactive',
                                '0' => 'Active'
                                )
        ));
		
		$this->addColumn('action',
                array(
                    'header'    => Mage::helper('sales')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'     => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('sales')->__('Delete'),
                            'url'     => array('base'=>'*/*/deleteReg'),
                            'field'   => 'reg_id'
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'is_system' => true,
        ));

        $this->addColumn('edit',
            array(
                'header'    => Mage::helper('sales')->__('Edit'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('sales')->__('Edit'),
                        'url'     => array('base'=>'*/*/editRegistry'),
                        'field'   => 'reg_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true,
            ));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/products', array('id'=>$row->getRegistryId()));
    }

}
