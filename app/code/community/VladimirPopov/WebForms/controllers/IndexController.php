<?php
/**
 * Feel free to contact me via Facebook
 * http://www.facebook.com/rebimol
 *
 *
 * @author 		Vladimir Popov
 * @copyright  	Copyright (c) 2011 Vladimir Popov
 */
require_once 'Mage/Contacts/controllers/IndexController.php';

class VladimirPopov_WebForms_IndexController extends Mage_Contacts_IndexController{
	
	public function indexAction()
	{
		// make it compatible with aheadworks help desk
		if(strstr($this->getFullActionName(),"contacts") && Mage::getStoreConfig('helpdeskultimate/modules/cf_enabled')){
			parent::indexAction();
			return;
		};
		
		Mage::register('show_form_name',true);
		$this->loadLayout();
		if(Mage::getStoreConfig('webforms/contacts/enable') && $this->getFullActionName() == 'contacts_index_index'){
			
			// remove default contacts
			$this->getLayout()->getBlock('contactForm')->setTemplate(false);
			
			// remove aheadworks antibot
			$aw_antibot = $this->getLayout()->getBlock('antibot');
			if($aw_antibot) 
				$aw_antibot->setTemplate(false);
			
			// add web-form to the layout
			$block = $this->getLayout()->createBlock('webforms/webforms','webforms',array(
				'template' => 'webforms/default.phtml',
				'webform_id' => Mage::getStoreConfig('webforms/contacts/webform')
			));
			$this->getLayout()->getBlock('content')->append($block);
		}
		$this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('catalog/session');
		$this->renderLayout();
	}
	
	public function iframeAction()
	{
		$webform = Mage::getModel('webforms/webforms')->load(Mage::app()->getRequest()->getPost("webform_id"));

		$success = false;
		$result = array("success" => false, "errors" => array());
		if(Mage::app()->getRequest()->getPost('submitWebform_'.$webform->getId())){
			$result["success"] = $webform->savePostResult();
			if($result["success"]){
				$result["success_text"] = Mage::helper('cms')->getPageTemplateProcessor()->filter($webform->getSuccessText());
				if($webform->getRedirectUrl()){
					if(strstr($webform->getRedirectUrl(),'://'))	
						$redirectUrl = $webform->getRedirectUrl();
					else
						$redirectUrl = Mage::app()->getStore()->getUrl($webform->getRedirectUrl());
					$result["redirect_url"] = $redirectUrl;
				}
			} else {
				$errors = Mage::getSingleton('core/session')->getMessages(true)->getItems();
				foreach($errors as $err){
					$result["errors"][] = $err->getCode();
				}
				$html_errors = "";
				if(count($result["errors"])>1){
					foreach($result["errors"] as $err){
						$html_errors.= '<li>'.$err.'</li>';
					}
					$result["errors"] = '<ul class="webforms-errors-list">'.$html_errors.'</ul>';
				} else {
					$result["errors"] = '<p class="webforms-error-message">'.$result["errors"][0].'</p>';
				}
			}
		}
		
		Mage::dispatchEvent('webforms_controllers_index_iframe_action',array('result'=>$result, 'webform'=>$webform));

		$this->getResponse()->setBody(htmlspecialchars(Mage::helper('core')->jsonEncode($result), ENT_NOQUOTES));
	}
	
}  
?>
