<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Saa
 * Date: 14.03.13
 * Time: 17:43
 * To change this template use File | Settings | File Templates.
 */

class Webtex_Weddingregistry_Block_Adminhtml_Products_Add_Toadd_Items extends Mage_Adminhtml_Block_Sales_Order_Create_Items
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getHeaderText()
    {
        return 'Products To Add';
    }
}