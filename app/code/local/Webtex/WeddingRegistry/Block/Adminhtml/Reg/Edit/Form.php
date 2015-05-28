<?php

class Webtex_Weddingregistry_Block_Adminhtml_Reg_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->_getActionUrl(),
            'method' => 'post',
            'enctype' => 'multipart/form-data',
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    private function _getActionUrl()
    {

        $oSession = Mage::getSingleton('webtexweddingregistry/session');
        $oRegistry = $oSession->getRegistry();$oRegistry->getCustomerId();

        if($oRegistry->getRegistryId())
        {
            $url = $this->getUrl('*/adminhtml_registry/saveRegistry', array('reg_id' => $oRegistry->getRegistryId()));
        }
        else
        {
            $url = $this->getUrl('*/adminhtml_registry/saveRegistry', array('cust_id' => $oRegistry->getCustomerId()));
        }
        return $url;
    }
}