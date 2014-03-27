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
 * Showroom Edit Form Content Tab Block
 * 
 * @category 	Magestore
 * @package 	Magestore_Showroom
 * @author  	Magestore Developer
 */
class Magestore_Showroom_Block_Adminhtml_Question_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * prepare tab form's information
	 *
	 * @return Magestore_Showroom_Block_Adminhtml_Question_Edit_Tab_Form
	 */
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$dataObj = new Varien_Object(array(
      	'question_id' => '',
      	'title_in_store'	=> '',
      	'status_in_store'	=> '',
		'type_in_store'		=> '',
		'sort_in_store'		=> ''
	  ));
		$this->setForm($form);
		if (Mage::getSingleton('adminhtml/session')->getQuestionData()){
			$data = Mage::getSingleton('adminhtml/session')->getQuestionData();
			Mage::getSingleton('adminhtml/session')->setQuestionData(null);
		}elseif(Mage::registry('question_data'))
			$data = Mage::registry('question_data')->getData();
		//Zend_Debug::dump($data);
		if (isset($data)) $dataObj->addData($data);
			$data = $dataObj->getData();
		//Zend_Debug::dump($data);
		$fieldset = $form->addFieldset('question_data', array('legend'=>Mage::helper('showroom')->__('Question information')));

		$inStore = $this->getRequest()->getParam('store');
		$defaultLabel = Mage::helper('showroom')->__('Use Default');
		$defaultTitle = Mage::helper('showroom')->__('-- Please Select --');
		$scopeLabel = Mage::helper('showroom')->__('STORE VIEW');
		
		$fieldset->addField('title', 'text', array(
			'label'		=> Mage::helper('showroom')->__('Title'),
			'class'		=> 'required-entry',
			'required'	=> true,
			'name'		=> 'title',
			'disabled'  => ($inStore && !$data['title_in_store']),
			'after_element_html' => $inStore ? '</td><td class="use-default">
			<input id="title_default" name="title_default" type="checkbox" value="1" class="checkbox config-inherit" '.($data['title_in_store'] ? '' : 'checked="checked"').' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="title_default" class="inherit" title="'.$defaultTitle.'">'.$defaultLabel.'</label>
			</td><td class="scope-label">
			['.$scopeLabel.']
			' : '</td><td class="scope-label">
			['.$scopeLabel.']',
		));

		$fieldset->addField('type', 'select', array(
			'label'		=> Mage::helper('showroom')->__('Is Required'),
			'required'	=> false,
			'name'		=> 'type',
			'values'	=> Mage::helper('showroom')->getQuestionTypeOptionHash(),
			'disabled'  => ($inStore && !$data['type_in_store']),
			'after_element_html' => $inStore ? '</td><td class="use-default">
			<input id="type_default" name="type_default" type="checkbox" value="1" class="checkbox config-inherit" '.($data['type_in_store'] ? '' : 'checked="checked"').' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="type_default" class="inherit" title="'.$defaultTitle.'">'.$defaultLabel.'</label>
			</td><td class="scope-label">
			['.$scopeLabel.']
			' : '</td><td class="scope-label">
			['.$scopeLabel.']',
		));
		
		/*if (!Mage::app()->isSingleStoreMode()) {
			$fieldset->addField('store_ids', 'multiselect', array(
						'name'      => 'stores[]',
						'label'     => Mage::helper('cms')->__('Store View'),
						'title'     => Mage::helper('cms')->__('Store View'),
						'required'  => true,
						'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
				));
		}
		else {
				$fieldset->addField('store_ids', 'hidden', array(
						'name'      => 'stores[]',
						'value'     => Mage::app()->getStore(true)->getId()
				));					
		}
		*/
		$fieldset->addField('status', 'select', array(
			'label'		=> Mage::helper('showroom')->__('Status'),
			'name'		=> 'status',
			'values'	=> Mage::getSingleton('showroom/status')->getOptionHash(),
			'disabled'  => ($inStore && !$data['status_in_store']),
			'after_element_html' => $inStore ? '</td><td class="use-default">
			<input id="status_default" name="status_default" type="checkbox" value="1" class="checkbox config-inherit" '.($data['status_in_store'] ? '' : 'checked="checked"').' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="status_default" class="inherit" title="'.$defaultTitle.'">'.$defaultLabel.'</label>
			</td><td class="scope-label">
			['.$scopeLabel.']
			' : '</td><td class="scope-label">
			['.$scopeLabel.']',
		));

		$fieldset->addField('sort', 'text', array(
			'label'		=> Mage::helper('showroom')->__('Sort'),
			'class'		=> 'required-entry',
			'required'	=> true,
			'name'		=> 'sort',
			'disabled'  => ($inStore && !$data['sort_in_store']),
			'after_element_html' => $inStore ? '</td><td class="use-default">
			<input id="sort_default" name="sort_default" type="checkbox" value="1" class="checkbox config-inherit" '.($data['sort_in_store'] ? '' : 'checked="checked"').' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="sort_default" class="inherit" title="'.$defaultTitle.'">'.$defaultLabel.'</label>
			</td><td class="scope-label">
			['.$scopeLabel.']
			' : '</td><td class="scope-label">
			['.$scopeLabel.']',
		));	
		$form->setValues($data);
		
		return parent::_prepareForm();
	}
}