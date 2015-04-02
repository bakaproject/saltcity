<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
* @package Amasty_Easyblocks
*/

$installer = $this;
$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('ameasyblocks/block')}` (
  `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `content` text NOT NULL,
  `is_active` tinyint(1) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `views` int(10) unsigned NOT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;
");

$installer->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('ameasyblocks/placeholder')}` (
  `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `place` smallint(5) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `alias` varchar(64) NOT NULL,
  `is_active` tinyint(1) unsigned NOT NULL,
  `store_ids` varchar(196) NOT NULL,
  `category_ids` varchar(255) NOT NULL,
  `width` varchar(12) NOT NULL,
  `height` varchar(12) NOT NULL,
  `fullscreen_onlyhome` tinyint(1) NOT NULL,
  `fullscreen_onlyonce` tinyint(1) NOT NULL,
  `fullscreen_effect` float unsigned NOT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;
");

$installer->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('ameasyblocks/block_place')}` (
  `entity_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `placeholder_id` int(10) unsigned NOT NULL,
  `block_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`entity_id`),
  UNIQUE KEY `placeholder_id` (`placeholder_id`,`block_id`),
  KEY `block_id` (`block_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;
");

$installer->endSetup(); 