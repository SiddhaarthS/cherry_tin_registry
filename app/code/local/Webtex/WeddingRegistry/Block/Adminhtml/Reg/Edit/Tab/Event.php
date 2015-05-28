<?php

class Webtex_Weddingregistry_Block_Adminhtml_Reg_Edit_Tab_Event extends Mage_Adminhtml_Block_Widget_Form
{
    public function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $oRegistry = Mage::getSingleton('webtexweddingregistry/session')->getRegistry();
        if($oRegistry->getRegistryId())
        {
            $aData = $this->getFormData($oRegistry);
        }
        else
        {
            $aData = array();
        }


        $fieldset = $form->addFieldset('form_form', array(
            'legend' => 'Event' //todo: add localization
        ));

        $fieldset->addField('wedding_exp_arriv_date', 'date', array(
            'label'     => 'Event Date',
            'required'  => true,
            'name'      => 'wedding_exp_arriv_date',
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => 'M/d/y',
            //'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
        ));

        $fieldset->addField('event_location', 'text', array(
            'label' => 'Event location',
            'class'     => 'input-text',
            'required'  => false,
            'name'      => 'event_location',
        ));

        $fieldset->addField('parent_notes', 'textarea', array(
            'label' => 'Message for Registry Guests',
            'class'     => 'input-text',
            'required'  => false,
            'name'      => 'parent_notes',
        ));

        $this->setForm($form);
        $form->setValues($aData);
        return parent::_prepareForm();
    }

    //date in db stores in string format 'mm/dd/yyyy'
    private function getFormData($oRegistry)
    {
        $aData = array();
        $time = strtotime($oRegistry->getWeddingExpArrivDate());
        $newformat = date('m/d/Y',$time);
        $aData['wedding_exp_arriv_date'] = $newformat;
        $aData['event_location'] = $oRegistry->getEventLocation();
        $aData['parent_notes'] = $oRegistry->getParentNotes();
        return $aData;
    }
}