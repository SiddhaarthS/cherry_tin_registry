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

 DROP TABLE IF EXISTS {$this->getTable('webtexweddingregistry')};
CREATE TABLE {$this->getTable('webtexweddingregistry')} (
  `registry_id` int(11) unsigned NOT NULL auto_increment,
  `weddingregistry_id` varchar(255) NOT NULL default '',
  `customer_id` int(11) UNSIGNED NOT NULL DEFAULT  '0',
  `firstname` varchar(255) NOT NULL default '',
  `lastname` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `co_firstname` varchar(255) NOT NULL default '',
  `co_lastname` varchar(255) NOT NULL default '',
  `co_email` varchar(255) NOT NULL default '',
  `wedding_exp_arriv_date` varchar(255) NOT NULL default '',
  `parent_notes` text NOT NULL default '',
  `event_location` varchar(255) NOT NULL default '',
  `address_id` varchar(255) NOT NULL default '',
  `status` varchar(1) NOT NULL default '',
  `filename` varchar(256) NOT NULL default '',
  `created_at` datetime NOT NULL DEFAULT  '0000-00-00 00:00:00',
  PRIMARY KEY (`registry_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 DROP TABLE IF EXISTS {$this->getTable('webtexweddingregistry_item')};
CREATE TABLE {$this->getTable('webtexweddingregistry_item')} (
  `weddingregistry_item_id` int(10) unsigned NOT NULL auto_increment,
  `weddingregistry_id` int(10) unsigned NOT NULL default '0',
  `product_id` int(10) unsigned NOT NULL default '0',
  `store_id` int(10) unsigned NOT NULL default '0',
  `priority` int(10) unsigned NOT NULL default '0',
  `description` text,
  `params` text,
  `desired` int(10) unsigned NOT NULL default '1',
  `received` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`weddingregistry_item_id`),
  KEY `WEBTEX_WEDDING_PRODUCT` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='WeddingRegistry items';

    ");


    
$installer->run("

ALTER TABLE {$this->getTable('webtexweddingregistry_item')} ADD CONSTRAINT `WEBTEX_WEDDING_REG_PRODUCT` FOREIGN KEY ( `product_id` ) REFERENCES {$this->getTable('catalog/product')} ( `entity_id` ) ON UPDATE CASCADE ON DELETE CASCADE; 

");    

$installer->endSetup(); 