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
class Magestore_Showroom_Block_Adminhtml_Showroom_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * prepare tab form's information
	 *
	 * @return Magestore_Showroom_Block_Adminhtml_Showroom_Edit_Tab_Form
	 */
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$this->setForm($form);
		
		if (Mage::getSingleton('adminhtml/session')->getShowroomData()){
			$data = Mage::getSingleton('adminhtml/session')->getShowroomData();
			Mage::getSingleton('adminhtml/session')->setShowroomData(null);
		}elseif(Mage::registry('showroom_data'))
			$data = Mage::registry('showroom_data')->getData();
		
		$fieldset = $form->addFieldset('showroom_form', array('legend'=>Mage::helper('showroom')->__('Showroom information')));

		$url=$this->getUrl('showroomadmin/adminhtml_profile/edit', array('id' => $data['profile_id']));
		$name=Mage::getModel('showroom/showroomprofile')->load($data['profile_id'])->getName();
		$fieldset->addField('profile_id', 'note', array(
			'label'		=> Mage::helper('showroom')->__('Showroom owner'),
			'text'		=>'<a href="'.$url.'">'.Mage::helper('showroom')->__($name).'</a>'
		));
		$fieldset->addField('type', 'select', array(
			'label'		=> Mage::helper('showroom')->__('Type'),
			'required'	=> false,
			'name'		=> 'type_showroom',
			'values'	=>array(
							0=>'Personal',
							1=>'Stylist',
							)
		));

		$fieldset->addField('is_private', 'select', array(
			'label'		=> Mage::helper('showroom')->__('Is Private'),
			'required'	=> false,
			'name'		=> 'is_private',
			'values'	=>array(
							0=>'No',
							1=>'Yes',
							)
		));

		$fieldset->addField('follow_number', 'label', array(
			'label'		=> Mage::helper('showroom')->__('Follows')
		));

		$fieldset->addField('status', 'select', array(
			'label'		=> Mage::helper('showroom')->__('Status'),
			'name'		=> 'status_showroom',
			'values'	=> array(
				1=> 'Processing',
				2=> 'Active',
				3=>'Disable'
			)
		));

		

		$form->setValues($data);
		return parent::_prepareForm();
	}
}