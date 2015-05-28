<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Saa
 * Date: 19.10.12
 * Time: 16:08
 * To change this template use File | Settings | File Templates.
 */

class Webtex_WeddingRegistry_Block_Address_Widget_Name extends Mage_Customer_Block_Widget_Name
{
    public function _construct()
    {
        Mage_Customer_Block_Widget_Abstract::_construct();
        $this->setTemplate('webtexweddingregistry/address/widget/name.phtml');
    }
}