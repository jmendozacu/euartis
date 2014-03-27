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
ALTER TABLE  `{$this->getTable('webforms/fields')}` ADD  `validate_length_min` int( 11 ) NOT NULL DEFAULT '0' AFTER `css_style`;
ALTER TABLE  `{$this->getTable('webforms/fields')}` ADD  `validate_length_max` int( 11 ) NOT NULL DEFAULT '0' AFTER `css_style`;
ALTER TABLE  `{$this->getTable('webforms/fields')}` ADD  `validate_regex` varchar( 255 ) NOT NULL AFTER `css_style`;
ALTER TABLE  `{$this->getTable('webforms/fields')}` ADD  `validate_message` text NOT NULL AFTER `css_style`;
");

$installer->endSetup();
?>