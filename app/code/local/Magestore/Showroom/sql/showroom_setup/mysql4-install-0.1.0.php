<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	Magestore
 * @package 	Magestore_Showroom
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * create showroom table
 */
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('showroom_question')};

CREATE TABLE {$this->getTable('showroom_question')} (
  `question_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `type` smallint(6) NOT NULL default '1',
  `sort` int(10) NOT NULL default '1',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `modified_time` datetime NULL,
  PRIMARY KEY (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('showroom_question_value')};

CREATE TABLE {$this->getTable('showroom_question_value')}(
  `value_id` int(10) unsigned NOT NULL auto_increment,
  `question_id` int(10) unsigned NOT NULL,
  `store_id` smallint(5) unsigned  NOT NULL,
  `attribute_code` varchar(63) NOT NULL default '',
  `value` text NOT NULL,
  UNIQUE(`question_id`,`store_id`,`attribute_code`),
  INDEX (`question_id`),
  INDEX (`store_id`),
  FOREIGN KEY (`question_id`) REFERENCES {$this->getTable('showroom_question')} (`question_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core/store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY (`value_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('showroom_question_answer')};

CREATE TABLE {$this->getTable('showroom_question_answer')}(
	`answer_id` int(11) unsigned NOT NULL auto_increment,
	`question_id` int(11) unsigned NOT NULL,
	`title` varchar(50) NOT NULL default '',
	`conditions_serialized` text NULL,
	`sort` int(10) NOT NULL default '1',
	`image` text  NULL default '',
	`status` smallint(6) NOT NULL default '0',
	`created_time` datetime NULL,
	`modified_time` datetime NULL,
	INDEX (`question_id`),
	FOREIGN KEY(`question_id`) REFERENCES {$this->getTable('showroom_question')} (`question_id`) ON DELETE CASCADE ON UPDATE CASCADE,
	PRIMARY KEY (`answer_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('showroom_question_answer_value')};

CREATE TABLE {$this->getTable('showroom_question_answer_value')}(
  `value_id` int(10) unsigned NOT NULL auto_increment,
  `answer_id` int(10) unsigned NOT NULL,
  `store_id` smallint(5) unsigned  NOT NULL,
  `attribute_code` varchar(63) NOT NULL default '',
  `value` text NOT NULL,
  UNIQUE(`answer_id`,`store_id`,`attribute_code`),
  INDEX (`answer_id`),
  INDEX (`store_id`),
  FOREIGN KEY (`answer_id`) REFERENCES {$this->getTable('showroom_question_answer')} (`answer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core/store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY (`value_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('showroom_profile')};

CREATE TABLE {$this->getTable('showroom_profile')}(
	`profile_id` int(11) unsigned NOT NULL auto_increment,
	`name` varchar(50) NOT NULL default '',
	`customer_id` int(11) unsigned NOT NULL,
	`customer_email` varchar(50) NOT NULL,
	`image` varchar(50) NULL default '',
	`is_receive_mail` smallint(1) NOT NULL default '1',
	`facebook_id` varchar(50) NULL,
	UNIQUE(`customer_id`),
	INDEX(`customer_id`),
	FOREIGN KEY (`customer_id`) REFERENCES {$this->getTable('customer/entity')}(`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
	PRIMARY KEY (`profile_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('showroom')};

CREATE TABLE {$this->getTable('showroom')}(
	`showroom_id` int(11) unsigned NOT NULL auto_increment,
	`type` smallint(1) NOT NULL default '0',
	`profile_id` int(11) unsigned NOT NULL,
	`is_private` smallint(1) NOT NULL default '0',
	`is_receive_mail` smallint(1) NOT NULL default '1',
	`follow_number` int(10) unsigned NOT NULL default '0',
	`store_group_id` smallint(5) unsigned  NOT NULL,
	`status` smallint(6) NOT NULL default '2',
	`created_time` datetime NULL,
	`modified_time` datetime NULL,	
	INDEX(`profile_id`),
	FOREIGN KEY (`profile_id`) REFERENCES {$this->getTable('showroom_profile')}(`profile_id`) ON DELETE CASCADE ON UPDATE CASCADE,
	PRIMARY KEY (`showroom_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('showroom_answer')};
CREATE TABLE {$this->getTable('showroom_answer')}(
	`showroom_answer_id` int(11) unsigned auto_increment,
	`showroom_id` int(11) unsigned NOT NULL,
	`answer_id` int(11) unsigned NOT NULL,
	`status` smallint(6) NOT NULL default '1',
	INDEX (`showroom_id`),
	INDEX (`answer_id`),
	UNIQUE (`showroom_id`,`answer_id`),
	FOREIGN KEY (`showroom_id`) REFERENCES {$this->getTable('showroom')} (`showroom_id`) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (`answer_id`) REFERENCES {$this->getTable('showroom_question_answer')} (`answer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
	PRIMARY KEY (`showroom_answer_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('showroom_product')};

CREATE TABLE {$this->getTable('showroom_product')}(
	`showroom_product_id` int(11) unsigned NOT NULL auto_increment,
	`showroom_id` int(11) unsigned NOT NULL,
	`product_id` int(10) unsigned NOT NULL,
	`point` int(10) unsigned NOT NULL default '1',
	`status` smallint(1) NOT NULL default '1',
	`type` smallint(1) NOT NULL default '2',
	INDEX(`product_id`),
	INDEX(`showroom_id`),
	FOREIGN KEY (`product_id`) REFERENCES {$this->getTable('catalog/product')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (`showroom_id`) REFERENCES {$this->getTable('showroom')} (`showroom_id`) ON DELETE CASCADE ON UPDATE CASCADE,
	PRIMARY KEY (`showroom_product_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('showroom_following')};

CREATE TABLE {$this->getTable('showroom_following')}(
	`showroom_following_id` int(11) unsigned NOT NULL auto_increment,
	`showroom_id` int(11) unsigned NOT NULL,
	`customer_id` int(11) unsigned NOT NULL,
	`status` smallint(1) NOT NULL default '1',
	INDEX(`customer_id`),
	INDEX(`showroom_id`),
	FOREIGN KEY (`showroom_id`) REFERENCES {$this->getTable('showroom')} (`showroom_id`) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (`customer_id`) REFERENCES {$this->getTable('customer/entity')}(`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
	PRIMARY KEY(`showroom_following_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

");



$installer->endSetup();

