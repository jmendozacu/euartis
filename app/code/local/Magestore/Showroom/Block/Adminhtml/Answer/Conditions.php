<?php

class Magestore_Showroom_Block_Adminhtml_Answer_Conditions extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
		$answer_id = $this->getAnswerId();
		$answer = Mage::getModel('showroom/answer')->load($answer_id);
		if(!$answer_id) $answer_id = $this->getPrefix();
		$form = new Varien_Data_Form();
		$conditions = $answer->getConditions()->getConditions();
		$form->setHtmlIdPrefix('showroom'.$answer_id.'_');
		$answer->getConditions()->setPrefix('answer'.$answer_id);
		$answer->getConditions()->setData('answer'.$answer_id,$conditions);
		$answer->getConditions()->setJsFormObject('showroom'.$answer_id.'_conditions_fieldset');
		foreach($answer->getConditions()->getConditions() as $cond){
			$cond->setPrefix('answer'.$answer_id);
			$cond->setJsFormObject('showroom'.$answer_id.'_conditions_fieldset');
		}
		$renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')->setAnswerId($answer_id)
		->setTemplate('showroom/fieldset.phtml')
		->setNewChildUrl($this->getUrl('showroom/adminhtml_answer/newConditionHtml/form/showroom'.$answer_id.'_conditions_fieldset',array('prefix'=>'answer'.$answer_id)));
		//->setNewChildUrl($this->getUrl('adminhtml/promo_catalog/newConditionHtml/form/showroom'.$answer_id.'_conditions_fieldset',array('prefix'=>'answer'.$answer_id)));
		$fieldset = $form->addFieldset('conditions_fieldset', array('legend'=>Mage::helper('showroom')->__('<span>Product Conditions (leave blank for no product) &nbsp; <a style="float:right" href="javascript:void(0)"><div id="help-img-'.$answer_id.'"><img src="'.$this->getSkinUrl('images/fam_help.gif').'"/></div></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>')))->setRenderer($renderer);
		$blockSingleton = Mage::getBlockSingleton('rule/conditions');
		$fieldset->addField('conditions','text',array(
		'name'	=> 'conditions',
		'label'	=> Mage::helper('showroom')->__('Conditions'),
		'title'	=> Mage::helper('showroom')->__('Conditions'),
		'required'	=> true,
		))->setRule($answer)->setRenderer($blockSingleton);
		$form->setValues($answer->getData());
		$this->setForm($form);
		return parent::_prepareForm();
	}
}