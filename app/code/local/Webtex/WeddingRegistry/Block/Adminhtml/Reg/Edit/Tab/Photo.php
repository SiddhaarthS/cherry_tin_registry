<?php

class Webtex_Weddingregistry_Block_Adminhtml_Reg_Edit_Tab_Photo extends Mage_Adminhtml_Block_Widget_Form
{
    public function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $aData = $this->getFormData();

        $fieldset = $form->addFieldset('form_form', array(
            'legend' => 'Photo' //todo: add localization
        ));

        $fieldset->addField('filename', 'image', array(
            'label' => 'Image',
            'required' => false,
            'name' => 'filename',
        ));

        $this->setForm($form);
        $form->setValues($aData);
        return parent::_prepareForm();
    }

    private function getFormData()
    {
        $oRegistry = Mage::getSingleton('webtexweddingregistry/session')->getRegistry();

        $aData = array();
        $aData['firstname'] = $oRegistry->getFirstname();
        $aData['lastname'] = $oRegistry->getLastname();
        $aData['maidenname'] = $oRegistry->getMaidenname();
        $aData['email'] = $oRegistry->getEmail();

        return $aData;
    }
}