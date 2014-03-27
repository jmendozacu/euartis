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
class Magestore_Showroom_Block_Adminhtml_Profile_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * prepare tab form's information
	 *
	 * @return Magestore_Showroom_Block_Adminhtml_Showroom_Edit_Tab_Form
	 */
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$this->setForm($form);
		
		if (Mage::getSingleton('adminhtml/session')->getProfileData()){
			$data = Mage::getSingleton('adminhtml/session')->getProfileData();
			Mage::getSingleton('adminhtml/session')->getProfileData(null);
		}elseif(Mage::registry('profile_data'))
			$data = Mage::registry('profile_data')->getData();
		
		$fieldset = $form->addFieldset('profile_form', array('legend'=>Mage::helper('showroom')->__('Profile Information')));

		$fieldset->addField('name', 'text', array(
			'label'		=> Mage::helper('showroom')->__('Name'),
			'required'	=> false,
			'name'		=>'name',
		));
		$fieldset->addField('customer_email', 'text', array(
			'label'		=> Mage::helper('showroom')->__('Owner Email'),
			'name'		=>'customer_email',
			'note'		=>'<a href="'.$this->getUrl('adminhtml/customer/edit/id/'.$data['customer_id']).'">'.$data['customer_email'].'</a>'
		));
		if($data['image'])
			$data['image']='showroom/profile/'.$data['profile_id'].'/'.$data['image'];
		$fieldset->addField('image', 'image', array(
			'label'		=> Mage::helper('showroom')->__('Image'),
			'title'		=> Mage::helper('showroom')->__('Image'),
			'name'		=>'images'
		));
		$fieldset->addField('is_receive_mail', 'select', array(
			'label'		=> Mage::helper('showroom')->__('Configured to receive mail'),
			'required'	=> false,
			'name'		=>'is_receive_mail',
			'values'	=>array(
							0=>'Not receiving mail',
							1=>'Receive mail',
							)
		));

		$form->setValues($data);
		return parent::_prepareForm();
	}
}