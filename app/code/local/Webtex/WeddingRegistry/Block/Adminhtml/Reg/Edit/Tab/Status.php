<?php

class Webtex_Weddingregistry_Block_Adminhtml_Reg_Edit_Tab_Status extends Mage_Adminhtml_Block_Widget_Form
{
    public function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('form_form', array(
            'legend' => 'Registry Status' //todo: add localization
        ));

        $fieldset->addField('publ_registry', 'radio', array(
            'name'      => 'status',
            'value'  => '0',
            'after_element_html' => '<small>Public Registry (available to everyone through registry search, and direct access using direct link)</small>',
        ));

        $fieldset->addField('priv_registry', 'radio', array(
            'name'      => 'status',
            'value'  => '1',
            'after_element_html' => '<small>Private Registry (available only to those, who have your unique ID or direct link to your Gift Registry)</small>'.$this->getJsSctipt(),
            'checked' => true,
        ));


        $this->setForm($form);
        //$form->setValues($aData);
        return parent::_prepareForm();
    }

    private function getJsSctipt()
    {

        $html = '';
        $oRegistry = Mage::getSingleton('webtexweddingregistry/session')->getRegistry();
        if($oRegistry->getStatus() == 1)
        {
            $html .= '<script type="text/javascript">$("priv_registry").checked = true;</script>';
        }
        elseif($oRegistry->getStatus() == 0) {
            $html .= '<script type="text/javascript">$("publ_registry").checked = true;</script>';
        }
        return $html;
    }
}