<?php

class Webtex_Weddingregistry_Block_Adminhtml_Reg_Edit_Tab_Coregistrant extends Mage_Adminhtml_Block_Widget_Form
{
    public function _prepareForm()
    {
        $form = new Varien_Data_Form();

        if($oRegistry = Mage::getSingleton('webtexweddingregistry/session')->getRegistry()->getRegistryId())
        {
            $aData = $this->getFormData($oRegistry);
        }
        else
        {
            $aData = array();
        }

        $fieldset = $form->addFieldset('form_form', array(
            'legend' => 'Co-registrant' //todo: add localization
        ));

        $fieldset->addField('co_firstname', 'text', array(
            'label'     => 'First Name',
            'class'     => 'input-text',
            'required'  => false,
            'name'      => 'co_firstname',
        ));

        $fieldset->addField('co_lastname', 'text', array(
            'label'     => 'Last Name',
            'class'     => 'input-text',
            'required'  => false,
            'name'      => 'co_lastname',
        ));

        $fieldset->addField('co_maidenname', 'text', array(
            'label'     => 'Maiden Name (if applicable)',
            'class'     => 'input-text',
            'required'  => false,
            'name'      => 'co_maidenname',
        ));

        $fieldset->addField('co_email', 'text', array(
            'label'     => 'Email',
            'class'     => 'validate-email',
            'required'  => false,
            'name'      => 'co_email',
        ));

        $this->setForm($form);
        $form->setValues($aData);
        return parent::_prepareForm();
    }

    private function getFormData($oRegistry)
    {
        $oRegistry = Mage::getSingleton('webtexweddingregistry/session')->getRegistry();

        $aData = array();
        $aData['co_firstname'] = $oRegistry->getCoFirstname();
        $aData['co_lastname'] = $oRegistry->getCoLastname();
        $aData['co_maidenname'] = $oRegistry->getCoMaidenname();
        $aData['co_email'] = $oRegistry->getCoEmail();

        return $aData;
    }
}