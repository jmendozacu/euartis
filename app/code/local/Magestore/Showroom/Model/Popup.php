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
class Magestore_Showroom_Model_Popup
{
	const REQUIRED	= 1;
	const CAN_TURN_OFF= 2;
	const NOT_SHOW= 3;
	
	/**
	 * get model option as array
	 *
	 * @return array
	 */
	static public function toOptionArray(){
		return array(
			self::REQUIRED	=> Mage::helper('showroom')->__('Required'),
			self::CAN_TURN_OFF   => Mage::helper('showroom')->__('Able to turn off popup'),
			self::NOT_SHOW   => Mage::helper('showroom')->__('Not show')
		);
	}
}