<?php

class Webtex_Weddingregistry_Adminhtml_RegistryController extends Mage_Adminhtml_Controller_Action
{
    public function saveRegistryAction()
    {
        if ($data = $this->getRequest()->getPost())
        {

            $oSession = Mage::getSingleton('webtexweddingregistry/session');
            $oRegistry = $oSession->getRegistry();
            $data['customer_id'] = $oRegistry->getCustomerId();

            if(!$data['isedit'])
            {
                $data['created_at'] = date('y-m-d h:i:s', time());
                $data['weddingregistry_id'] = $this->gen_uuid();
                $data['active'] = 0;
            }



            if(!isset($data['address_id']) && isset($data['specify-address']))
            {

                $shippingData = $this->_beforeSave($data);

                unset($data['shipping-firstname']);
                unset($data['shipping-lastname']);
                foreach($shippingData as $k => $v)
                {
                    if(!(($k == 'firstname') || ($k == 'lastname')))
                    {
                        unset($data[$k]);
                    }
                }

                $data['address_id'] = $this->_saveShippingAddress($shippingData, $data['customer_id']);
            }

            if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '')
            {
                try {
                    /* Starting upload */
                    $uploader = new Varien_File_Uploader('filename');

                    // Any extention would work
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(true);

                    // Set the file upload mode
                    // false -> get the file directly in the specified folder
                    // true -> get the file in the product like folders
                    //	(file.jpg will go in something like /media/f/i/file.jpg)
                    $uploader->setFilesDispersion(false);

                    // We set media as the upload dir
                    $path = Mage::getBaseDir('media') . DS . 'webtexweddingregistry' . DS;

                    $uploader->save($path, $_FILES['filename']['name'] );

                } catch (Exception $e)
                {
                    Mage::getSingleton('customer/session')->addError($this->__('Ooops! Something wrong. Image was not saved.'));
                }

                //this way the name is saved in DB
                $data['filename'] = $uploader->getUploadedFileName();
            }

            $model = Mage::getModel('webtexweddingregistry/webtexweddingregistry');
            $model->setData($data);
            $regId = $this->getRequest()->getParam('reg_id');
            if(!empty($regId))
            {
                $model->setId($regId);
            }

            try
            {
                $model->save();
                //Mage::getSingleton('customer/session')->addSuccess($this->__('Registry was successfully saved.'));
            }
            catch(Exception $e)
            {
                //Mage::getSingleton('customer/session')->addError($this->__('Ooops! Something wrong. Registry was not saved.'));
            }
        }


        $this->_redirect('webtexweddingregistry_admin/adminhtml_index/index/');
    }

    public function gen_uuid($len=12)
    {
        $hex = md5("webtexweddingregistry" . uniqid("", true));

        $pack = pack('H*', $hex);

        $uid = base64_encode($pack);        // max 22 chars

        //$uid = ereg_replace("[^A-Za-z0-9]", "", $uid);    // mixed case
        $uid = ereg_replace("[^A-Z0-9]", "", strtoupper($uid));    // uppercase only

        if ($len<4)
            $len=4;
        if ($len>128)
            $len=128;                       // prevent silliness, can remove

        while (strlen($uid)<$len)
            $uid = $uid . gen_uuid(22);     // append until length achieved

        return substr($uid, 0, $len);
    }

    /*
     * prepare data for submit when new shipping address is adding
     */
    private function _beforeSave($data)
    {
        $shippingData = array();
        $shippingData['firstname'] = $data['shipping-firstname'];
        $shippingData['lastname'] = $data['shipping-lastname'];
        $shippingData['company'] = $data['company'];
        $shippingData['telephone'] = $data['telephone'];
        $shippingData['fax'] = $data['fax'];
        $shippingData['street'] = $data['street'];
        $shippingData['city'] = $data['city'];
        $shippingData['region_id'] = $data['region_id'];
        $shippingData['region'] = $data['region'];
        $shippingData['postcode'] = $data['postcode'];
        $shippingData['country_id'] = $data['country_id'];

        return $shippingData;
    }

    private function _saveShippingAddress($shippingData, $customerId)
    {
        $errors = array();

        $address  = Mage::getModel('customer/address');
        $addressForm = Mage::getModel('customer/form');
        $addressForm->setFormCode('customer_address_edit')
            ->setEntity($address);

        $addressErrors  = $addressForm->validateData($shippingData);

        if ($addressErrors !== true) {
            $errors = $addressErrors;
        }

        try {
            $address->setCustomerId($customerId)
                ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
            $address->addData($shippingData);
            $addressErrors = $address->validate();
            if ($addressErrors !== true) {
                $errors = array_merge($errors, $addressErrors);
            }
            if (count($errors) === 0) {
                $address->save();


                return $address->getId();
            } else {
                $this->_getSession()->setAddressFormData($this->getRequest()->getPost());
                foreach ($errors as $errorMessage) {
                    $this->_getSession()->addError($errorMessage);
                }
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
                ->addException($e, $e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
                ->addException($e, $this->__('Cannot save address.'));
        }
    }
}