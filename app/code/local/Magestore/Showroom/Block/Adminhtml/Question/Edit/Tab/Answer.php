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
class Magestore_Showroom_Block_Adminhtml_Question_Edit_Tab_Answer extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * prepare tab form's information
	 *
	 * @return Magestore_Showroom_Block_Adminhtml_Question_Edit_Tab_Form
	 */
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$this->setForm($form);
		if (Mage::getSingleton('adminhtml/session')->getQuestionData()){
			$data = Mage::getSingleton('adminhtml/session')->getQuestionData();
			Mage::getSingleton('adminhtml/session')->setQuestionData(null);
		}elseif(Mage::registry('question_data'))
			$data = Mage::registry('question_data')->getData();
		$answers = Mage::getModel('showroom/answer')->getAnswersByQuestionId($data['question_id']);
		/*$form->setHtmlIdPrefix('showroom_');
      
      $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('adminhtml/promo_quote/newConditionHtml/form/showroom_conditions_fieldset'));
      
      $fieldset = $form->addFieldset('conditions_fieldset', array('legend'=>Mage::helper('showroom')->__('Use the program only if the following conditions are met (leave blank for all products)')))->setRenderer($renderer);
      
      $fieldset->addField('conditions','text',array(
      	'name'	=> 'conditions',
      	'label'	=> Mage::helper('showroom')->__('Conditions'),
      	'title'	=> Mage::helper('showroom')->__('Conditions'),
      	'required'	=> true,
	  ))->setRule(Mage::getModel('showroom/answer'))->setRenderer(Mage::getBlockSingleton('rule/conditions'));
      
      $form->setValues($data);*/
		$fieldset = $form->addFieldset('answer', array('legend'=>Mage::helper('showroom')->__('Answer')));
		$fieldset->addField('answer_render', 'text', array(
                'name'=>'answer_render ',
                'class'=>'requried-entry',
                'value'=>$answers
        ));
		$form->getElement('answer_render')->setRenderer(
            $this->getLayout()->createBlock('showroom/adminhtml_question_edit_tab_answer_render')
        );
		$this->setForm($form);
		return parent::_prepareForm();
	}
	
	
}