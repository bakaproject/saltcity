<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
* @package Amasty_Easyblocks
*/

$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE `{$this->getTable('ameasyblocks/block')}` ADD `from_date` DATE NULL , ADD `to_date` DATE NULL ;
");

$installer->endSetup();