<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Magestore
 * @package     Magestore_Showroom
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Config category source
 *
 * @category   Magestore
 * @package    Magestore_Showroom
 * @author     Magestore Developer
 */
class Magestore_Showroom_Model_Template
{
	const _DEFAULT= 'default';
	const _PINK= 'pink';
	const _BLACK= 'black';
	const _BLUE= 'blue';
	const _GREEN= 'green';
	const _ORANGE= 'orange';
	
	/**
	 * get model option as array
	 *
	 * @return array
	 */
	static public function toOptionArray(){
		return array(
			self::_DEFAULT=> Mage::helper('showroom')->__('Default'),
			self::_PINK=> Mage::helper('showroom')->__('Pink'),
			self::_BLACK=> Mage::helper('showroom')->__('Black'),
			self::_BLUE=> Mage::helper('showroom')->__('Blue'),
			self::_GREEN=> Mage::helper('showroom')->__('Green'),
			self::_ORANGE=> Mage::helper('showroom')->__('Orange'),
		);
	}
}