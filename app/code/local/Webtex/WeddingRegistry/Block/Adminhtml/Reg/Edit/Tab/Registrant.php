<?php

class Webtex_Weddingregistry_Block_Adminhtml_Reg_Edit_Tab_Registrant extends Mage_Adminhtml_Block_Widget_Form
{
    public function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $oRegistry = Mage::getSingleton('webtexweddingregistry/session')->getRegistry();
        if($oRegistry->getRegistryId())
        {
            $aData = $this->getFormData($oRegistry, true);
        }
        else
        {
            $oCustomer = Mage::getSingleton('customer/customer')->load($oRegistry->getCustomerId());
            $aData = $this->getFormData($oCustomer, false);
        }


        $fieldset = $form->addFieldset('form_form', array(
            'legend' => 'Registrant' //todo: add localization
        ));

        $fieldset->addField('firstname', 'text', array(
            'label'     => 'First Name',
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'firstname',
        ));

        $fieldset->addField('lastname', 'text', array(
            'label'     => 'Last Name',
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'lastname',
        ));

        $fieldset->addField('maidenname', 'text', array(
            'label'     => 'Maiden Name (if applicable)',
            'class'     => 'input-text',
            'required'  => false,
            'name'      => 'maidenname',
        ));

        $fieldset->addField('email', 'text', array(
            'label'     => 'Email',
            'class'     => 'validate-email required-entry',
            'required'  => true,
            'name'      => 'email',
        ));

        $fieldset->addField('isedit', 'hidden', array(
            'name' => 'isedit',
        ));

        $this->setForm($form);
        $form->setValues($aData);
        return parent::_prepareForm();
    }

    private function getFormData($oObject, $isEdit)
    {
        $aData = array();
        $aData['firstname'] = $oObject->getFirstname();
        $aData['lastname'] = $oObject->getLastname();
        $aData['maidenname'] = $oObject->getMaidenname();
        $aData['email'] = $oObject->getEmail();

        if(!$isEdit) {
            $aData['isedit'] = 0;
        } else {
            $aData['isedit'] = 1;
        }

        return $aData;
    }
}