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

 /**
 * Showroom Popup Block
 * 
 * @category 	Magestore
 * @package 	Magestore_Showroom
 * @author  	Magestore Developer
 */

class Magestore_Showroom_Block_Popup extends Mage_Core_Block_Template

{
	/**
	 * prepare block's layout
	 *
	 * @return Magestore_Showroom_Block_Showroom
	 */
	public function __construct(){
		$this->setTemplate('showroom/popup.phtml');
	}	
	
	public function _prepareLayout(){
		parent::_prepareLayout();
		$this->setTemplate('showroom/popup.phtml');
		return $this;
	}
	
	public function isDisplayed(){
		$display_type = Mage::helper('showroom')->getGeneralConfig('required_register');
		if(($display_type == Magestore_Showroom_Model_Popup::REQUIRED) || ($display_type == Magestore_Showroom_Model_Popup::CAN_TURN_OFF)){
			return true;
		}
		return false;
	}

}