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
class Magestore_Showroom_Block_Adminhtml_Question_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('question_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('showroom')->__('Question Information'));
	}
	
	/**
	 * prepare before render block to html
	 *
	 * @return Magestore_Showroom_Block_Adminhtml_Showroom_Edit_Tabs
	 */
	protected function _beforeToHtml(){
		$this->addTab('form_section', array(
			'label'	 => Mage::helper('showroom')->__('Question Information'),
			'title'	 => Mage::helper('showroom')->__('Question Information'),
			'content'	 => $this->getLayout()->createBlock('showroom/adminhtml_question_edit_tab_form')->toHtml(),
		));
		$this->addTab('answer_section', array(
			'label'	 => Mage::helper('showroom')->__('Answers'),
			'title'	 => Mage::helper('showroom')->__('Answers'),
			'content'	 => $this->getLayout()->createBlock('showroom/adminhtml_question_edit_tab_answers')->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}