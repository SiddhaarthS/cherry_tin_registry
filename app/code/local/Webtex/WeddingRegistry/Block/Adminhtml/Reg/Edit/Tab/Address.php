<?php

class Webtex_Weddingregistry_Block_Adminhtml_Reg_Edit_Tab_Address extends Mage_Adminhtml_Block_Widget_Form
{
    private $_oRegistry;

    public function __construct()
    {
        parent::__construct();
        //$this->setTemplate('webtexweddingregistry/reg/address.phtml');
    }

    public function _prepareForm()
    {
        $this->getCountryList();
        $this->_oRegistry = Mage::getSingleton('webtexweddingregistry/session')->getRegistry();
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('form_form', array(
            'legend' => 'Address' //todo: add localization
        ));
        $options = $this->getAddresses();

        if(!($this->_oRegistry->getAddressId())) {
            $none = array('value' => '0', 'label' => 'None');
            array_unshift($options, $none);
            $default = 0;
        }
        else {
            $default = $this->_oRegistry->getAddressId();
        }

        $fieldset->addField('address_id', 'select', array(
                'name'  => 'address_id',
                'label' => Mage::helper('adminhtml')->__('Registry Address'),
                'title' => Mage::helper('adminhtml')->__('Registry Address'),
                'required' => false,
                'class' => '',
                'values' => $options,
                'value' => $default,
            )
        );


        $fieldset->addField('specify-address', 'checkbox', array(
            'name' => 'specify-address',
            'class' => 'specify-address',
            'label' => 'Add new address',
            'after_element_html' => '',
        ));

        $fieldset->addField('shipping-firstname', 'text', array(
            'name' => 'shipping-firstname',
            'label' => 'First Name',
            'required' => true,

        ));

        $fieldset->addField('shipping-lastname', 'text', array(
            'name' => 'shipping-lastname',
            'label' => 'Last Name',
            'required' => true,
        ));

        $fieldset->addField('company', 'text', array(
            'name' => 'company',
            'label' => 'Company',
        ));

        $fieldset->addField('telephone', 'text', array(
            'name' => 'telephone',
            'label' => 'Telephone',
            'required' => true,
        ));

        $fieldset->addField('Fax', 'text', array(
            'name' => 'fax',
            'label' => 'Fax',
        ));

        $fieldset->addField('street_1', 'text', array(
            'name' => 'street[]',
            'label' => 'Street',
            'required' => true,
        ));

        $countryCode = Mage::getStoreConfig('general/country/default');
        $fieldset->addField('country', 'select', array(
            'name' => 'country_id',
            'required' => true,
            'label' => 'Country',
            'values' => $this->getCountryList(),
            'value' => $countryCode,

        ));

        $fieldset->addField('region_id', 'select', array(
            'name' => 'region_id',
            'required' => true,
            'label' => 'State/Province',
        ));

        $fieldset->addField('region', 'text', array(
            'name' => 'region',
            'required' => true,
            'label' => 'State/Province',
        ));

        $fieldset->addField('city', 'text', array(
            'name' => 'city',
            'label' => 'City',
            'required' => true,
        ));

        $fieldset->addField('zip', 'text', array(
            'name' => 'postcode',
            'label' => 'Zip/Postal Code',
            'after_element_html' => $this->getJs(),
            'required' => true,
        ));



        $this->setForm($form);
        return parent::_prepareForm();
    }

    public function getAddresses()
    {

        $customer = Mage::getModel('customer/customer')
            ->load($this->_oRegistry->getCustomerId());
        $options = array();
        foreach ($customer->getAddresses() as $address) {
            $options[] = array(
                'value' => $address->getId(),
                'label' => $address->format('oneline')
            );
        }
        return $options;
    }

    public function getJs()
    {
        $regions = $this->helper('directory')->getRegionJson();
        $js = <<<JSSCRIPT

        <script type="text/javascript">
        (function(){
            function hideAddressFields() {
            $('address_id').disabled = false;
                var tBody = $('specify-address').up(2).select('tr');
                for(i=2; i<tBody.length; i++) {
                    var tr = tBody[i];
                    var el = tr.select('td.value')[0].childElements();
                    $(el)[0].disabled = true;
                    tr.hide();
                }
            }

            function showAddressFields() {
                $('address_id').disabled = true;
                var tBody = $('specify-address').up(2).select('tr');
                for(i=2; i<tBody.length; i++) {
                    var tr = tBody[i];
                    var el = tr.select('td.value')[0].childElements();
                    if($(el)[0].style.display == 'none') {
                        tr.hide();
                    } else {
                        $(el)[0].disabled = false;
                        tr.show();
                    }
                }
            }

            $$(".specify-address")[0].observe('click', function(){
                    $$(".specify-address")[0].checked ? showAddressFields() : hideAddressFields();
                });

            $('region_id').setAttribute('defaultValue',  "0");
            var regionTextParentNode = $('region').up(1);
            var regionSelectParentNode = $('region_id').up(1);
            var regionSelectEl = $('region_id');
            var regionTextEl = $('region');
            var countryEl = $('country');

            new RegionUpdater('country', 'region', 'region_id', $regions, undefined, 'zip');

            countryEl.observe('change', function() {
                if(regionSelectEl.style.display == 'none') {
                    regionTextParentNode.show();
                    regionSelectParentNode.hide();
                }
                if(regionTextEl.style.display == 'none') {
                    regionSelectParentNode.show();
                    regionTextParentNode.hide();
                }
            }
            );

            hideAddressFields();

         })();
        </script>
JSSCRIPT;

        return $js;
    }

    public function getCountryList()
    {
        $countryList = Mage::getModel('directory/country')->getResourceCollection() ->loadByStore() ->toOptionArray(true);
        return $countryList;
    }
}