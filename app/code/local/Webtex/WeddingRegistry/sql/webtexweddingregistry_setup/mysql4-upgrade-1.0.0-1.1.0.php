<?php
/**
 * Webtex
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.webtexsoftware.com/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@webtexsoftware.com and we will send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to http://www.webtexsoftware.com for more information, 
 * or contact us through this email: info@webtexsoftware.com.
 *
 * @category   Webtex
 * @package    Webtex_WeddingRegistry
 * @copyright  Copyright (c) 2011 Webtex Solutions, LLC (http://www.webtexsoftware.com/)
 * @license    http://www.webtexsoftware.com/LICENSE.txt End-User License Agreement
 */
 
$installer = $this;

$installer->startSetup();

$installer->run("


 DROP TABLE IF EXISTS {$this->getTable('webtexweddingregistry_orders')};
CREATE TABLE {$this->getTable('webtexweddingregistry_orders')} (
  `weddingregistry_order_id` int(10) unsigned NOT NULL auto_increment,
  `weddingregistry_id` int(10) unsigned NOT NULL default '0',
  `product_id` int(10) unsigned NOT NULL default '0',
  `order_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`weddingregistry_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='WeddingRegistry orders';

ALTER TABLE  {$this->getTable('webtexweddingregistry')} ADD `maidenname` varchar(255) NOT NULL default '';
ALTER TABLE  {$this->getTable('webtexweddingregistry')} ADD `co_maidenname` varchar(255) NOT NULL default '';
ALTER TABLE  {$this->getTable('webtexweddingregistry')} ADD `active` INT( 10 ) NOT NULL default '0';

ALTER TABLE  {$this->getTable('webtexweddingregistry_item')} ADD  `purchased` INT( 10 ) NOT NULL default '0';

    ");

$installer->endSetup(); 