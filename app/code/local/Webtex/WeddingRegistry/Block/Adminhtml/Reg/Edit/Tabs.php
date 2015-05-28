<?php

class Webtex_Weddingregistry_Block_Adminhtml_Reg_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('webtexweddingregistry_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle('Edit');
    }

    protected function _beforeToHtml()
    {

        $this->addTab('registrant', array(
            'label' => Mage::helper('webtexweddingregistry')->__('Registrnat'),
            'title' => Mage::helper('webtexweddingregistry')->__('Registrant'),
            'content' => $this->getLayout()->createBlock('webtexweddingregistry/adminhtml_reg_edit_tab_registrant')->toHtml(),
        ));

        $this->addTab('coregistrant', array(
            'label' => Mage::helper('webtexweddingregistry')->__('Co-Registrant'),
            'title' => Mage::helper('webtexweddingregistry')->__('Co-Registrant'),
            'content' => $this->getLayout()->createBlock('webtexweddingregistry/adminhtml_reg_edit_tab_coregistrant')->toHtml(),
        ));

        $this->addTab('event', array(
            'label' => Mage::helper('webtexweddingregistry')->__('Event'),
            'title' => Mage::helper('webtexweddingregistry')->__('Event'),
            'content' => $this->getLayout()->createBlock('webtexweddingregistry/adminhtml_reg_edit_tab_event')->toHtml(),
        ));

        $this->addTab('shipping', array(
            'label' => Mage::helper('webtexweddingregistry')->__('Shipping Address'),
            'title' => Mage::helper('webtexweddingregistry')->__('Shipping Address'),
            'content' => $this->getLayout()->createBlock('webtexweddingregistry/adminhtml_reg_edit_tab_address')->toHtml(),
        ));

        $this->addTab('photo', array(
            'label' => Mage::helper('webtexweddingregistry')->__('Photo'),
            'title' => Mage::helper('webtexweddingregistry')->__('Photo'),
            'content' => $this->getLayout()->createBlock('webtexweddingregistry/adminhtml_reg_edit_tab_photo')->toHtml(),
        ));

        $this->addTab('test', array(
            'label' => Mage::helper('webtexweddingregistry')->__('Registry Status'),
            'title' => Mage::helper('webtexweddingregistry')->__('Registry Status'),
            'content' => $this->getLayout()->createBlock('webtexweddingregistry/adminhtml_reg_edit_tab_status')->toHtml(),
        ));

        /*$this->addTab('test', array(
            'label' => Mage::helper('webtexweddingregistry')->__('Registry Items'),
            'title' => Mage::helper('webtexweddingregistry')->__('Registry Items'),
            'content' => $this->getLayout()->createBlock('adminhtml/sales_order_create_items')->toHtml(),
        ));*/

        return parent::_beforeToHtml();
    }
}