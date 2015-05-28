<?php

class  Webtex_Weddingregistry_Block_Adminhtml_Reg_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'webtexweddingregistry';
        $this->_controller = 'adminhtml_reg';
        //$this->_mode = 'edit';

        $this->_updateButton('save', 'label', 'Save');
    }

    public function getHeaderText()
    {
        $oSession = Mage::getSingleton('webtexweddingregistry/session');
        $oRegistry = $oSession->getRegistry();

        if($oRegistry->getRegistryId())
        {
            return 'Edit Registry';
        }
        else
        {
            return 'Create Registry';
        }
    }
}