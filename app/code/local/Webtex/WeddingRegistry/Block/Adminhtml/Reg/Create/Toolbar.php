<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Saa
 * Date: 15.03.13
 * Time: 11:21
 * To change this template use File | Settings | File Templates.
 */

class Webtex_Weddingregistry_Block_Adminhtml_Reg_Create_Toolbar extends Mage_Adminhtml_Block_Template
{

    public function __construct()
    {
        parent::__construct();

        $this->setTemplate('webtexweddingregistry/reg/create/toolbar.phtml');
    }

    protected function _getHeader()
    {
        return Mage::helper('webtexweddingregistry')->__("Choose Customer");
    }
}