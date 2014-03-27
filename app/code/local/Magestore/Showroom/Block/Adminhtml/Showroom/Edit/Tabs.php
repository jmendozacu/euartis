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
class Magestore_Showroom_Block_Adminhtml_Showroom_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('showroom_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('showroom')->__('Show room Information'));
	}
	
	/**
	 * prepare before render block to html
	 *
	 * @return Magestore_Showroom_Block_Adminhtml_Showroom_Edit_Tabs
	 */
	protected function _beforeToHtml(){
		$this->addTab('general_information', array(
			'label'	 => Mage::helper('showroom')->__('General Information'),
			'title'	 => Mage::helper('showroom')->__('General Information'),
			'content'	 => $this->getLayout()->createBlock('showroom/adminhtml_showroom_edit_tab_form')->toHtml(),
		));

		$this->addTab('style_form', array(
			'label'	 => Mage::helper('showroom')->__('Style'),
			'title'	 => Mage::helper('showroom')->__('Style'),
			'content'	 => $this->getLayout()->createBlock('Magestore_Showroom_Block_Adminhtml_Showroom_Edit_Tab_Styleform')->toHtml(),
		));

		$this->addTab('preferred_products', array(
			'label'	 => Mage::helper('showroom')->__('Preferred Products'),
			'title'	 => Mage::helper('showroom')->__('Preferred Products'),
			'content'	 => $this->getLayout()->createBlock('Magestore_Showroom_Block_Adminhtml_Showroom_Edit_Tab_Preferredproducts')->toHtml(),
		));

		$this->addTab('followers_form', array(
			'label'	 => Mage::helper('showroom')->__('Followers'),
			'title'	 => Mage::helper('showroom')->__('Followers'),
			'content'	 => $this->getLayout()->createBlock('Magestore_Showroom_Block_Adminhtml_Showroom_Edit_Tab_Followers')->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}