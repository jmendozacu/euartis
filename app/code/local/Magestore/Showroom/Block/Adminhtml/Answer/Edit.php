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
 * Showroom Question Edit Block
 * 
 * @category 	Magestore
 * @package 	Magestore_Showroom
 * @author  	Magestore Developer
 */
class Magestore_Showroom_Block_Adminhtml_Answer_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct(){
		parent::__construct();
		
		$this->_objectId = 'id';
		$this->_blockGroup = 'answer';
		$this->_controller = 'adminhtml_showroom';
		
		$this->_updateButton('save', 'label', Mage::helper('showroom')->__('Save Answer'));
		$this->_updateButton('delete', 'label', Mage::helper('showroom')->__('Delete Answer'));
		
		$this->_addButton('saveandcontinue', array(
			'label'		=> Mage::helper('adminhtml')->__('Save And Continue Edit'),
			'onclick'	=> 'saveAndContinueEdit()',
			'class'		=> 'save',
		), -100);

		$this->_formScripts[] = "
			function toggleEditor() {
				if (tinyMCE.getInstanceById('showroom_content') == null)
					tinyMCE.execCommand('mceAddControl', false, 'showroom_content');
				else
					tinyMCE.execCommand('mceRemoveControl', false, 'showroom_content');
			}

			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
	}
	
	/**
	 * get text to show in header when edit an item
	 *
	 * @return string
	 */
	public function getHeaderText(){
		if(Mage::registry('question_data') && Mage::registry('question_data')->getId())
			return Mage::helper('showroom')->__("Edit Question '%s'", $this->htmlEscape(Mage::registry('question_data')->getTitle()));
		return Mage::helper('showroom')->__('Add Question');
	}
}