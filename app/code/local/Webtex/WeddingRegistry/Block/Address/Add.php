<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Saa
 * Date: 19.10.12
 * Time: 13:36
 * To change this template use File | Settings | File Templates.
 */

class Webtex_WeddingRegistry_Block_Address_Add extends Mage_Customer_Block_Address_Edit
{
    public function getNameBlockHtml()
    {
        $nameBlock = $this->getLayout()
            ->createBlock('webtexweddingregistry/address_widget_name')
            ->setObject($this->getAddress());

        return $nameBlock->toHtml();
    }
}