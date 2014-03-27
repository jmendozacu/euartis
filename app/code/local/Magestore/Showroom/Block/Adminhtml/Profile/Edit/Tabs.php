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
 * Showroom Edit Tabs Block
 * 
 * @category 	Magestore
 * @package 	Magestore_Showroom
 * @author  	Magestore Developer
 */
class Magestore_Showroom_Block_Adminhtml_Profile_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('profile_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('showroom')->__('Profile Information'));
	}
	
	/**
	 * prepare before render block to html
	 *
	 * @return Magestore_Showroom_Block_Adminhtml_Showroom_Edit_Tabs
	 */
	protected function _beforeToHtml(){
			$this->addTab('profile_form', array(
			'label'	 => Mage::helper('showroom')->__('Profile Information'),
			'title'	 => Mage::helper('showroom')->__('Profile Information'),
			'content'	 => $this->getLayout()->createBlock('Magestore_Showroom_Block_Adminhtml_Profile_Edit_Tab_Form')->toHtml(),
			));
			$this->addTab('showroom_grid', array(
			'label'	 => Mage::helper('showroom')->__('Showroom Information'),
			'title'	 => Mage::helper('showroom')->__('Showroom Information'),
			'content'	 => $this->getLayout()->createBlock('Magestore_Showroom_Block_Adminhtml_Profile_Edit_Tab_Showrooms')->toHtml(),
			));
		return parent::_beforeToHtml();
	}
}