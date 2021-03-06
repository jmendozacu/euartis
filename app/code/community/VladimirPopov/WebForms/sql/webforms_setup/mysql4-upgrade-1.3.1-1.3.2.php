<?php
/**
 * Feel free to contact me via Facebook
 * http://www.facebook.com/rebimol
 *
 *
 * @author 		Vladimir Popov
 * @copyright  	Copyright (c) 2011 Vladimir Popov
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$webforms_table = 'webforms';
if((float)substr(Mage::getVersion(),0,3)>1.1)
	$webforms_table = $this->getTable('webforms/webforms');

$installer->run("
ALTER TABLE  `{$this->getTable('webforms/fields')}` ADD  `result_display` varchar( 10 ) NOT NULL DEFAULT 'on' AFTER `result_label`;
ALTER TABLE  `{$this->getTable('webforms/fieldsets')}` ADD  `result_display` varchar( 10 ) NOT NULL DEFAULT 'on' AFTER `name`;
ALTER TABLE  `{$webforms_table}` ADD  `add_header` tinyint(1) NOT NULL DEFAULT '1' AFTER `send_email`;
ALTER TABLE  `{$webforms_table}` ADD  `menu`  tinyint(1) NOT NULL DEFAULT '1' AFTER `is_active`;
");

$installer->endSetup();
?>