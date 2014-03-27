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
 * Showroom Question Adminhtml Controller
 * 
 * @category 	Magestore
 * @package 	Magestore_Showroom
 * @author  	Magestore Developer
 */
class Magestore_Showroom_Adminhtml_QuestionController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * init layout and set active for current menu
	 *
	 * @return Magestore_Showroom_Adminhtml_ShowroomController
	 */
	protected function _initAction(){
		$this->loadLayout()
			->_setActiveMenu('showroom/question')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Questions Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		return $this;
	}
 
	/**
	 * index action
	 */
	public function indexAction(){
		$this->_initAction()
			->renderLayout();
	}

	/**
	 * view and edit item action
	 */
	public function editAction() {
		$id	 = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('showroom/question');
		if ($storeId = $this->getRequest()->getParam('store',0))
			$model->setStoreId($storeId);
		$model->load($id);
		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data))
				$model->setData($data);

			Mage::register('question_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('showroom/question');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Question Manager'), Mage::helper('adminhtml')->__('Question Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Question News'), Mage::helper('adminhtml')->__('Question News'));

			//$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->getLayout()->getBlock('head')
				->setCanLoadExtJs(true)
				->setCanLoadRulesJs(true);
			$this->_addContent($this->getLayout()->createBlock('showroom/adminhtml_question_edit'))
				->_addLeft($this->getLayout()->createBlock('showroom/adminhtml_question_edit_tabs'));
			
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('showroom')->__('Question does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	/**
	 * save question action
	 */
	public function save_2Action() {
		if ($data = $this->getRequest()->getPost()) {
			//Zend_Debug::dump($data);die();
			$model = Mage::getModel('showroom/question');
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL)
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				else
					$model->setUpdateTime(now());
				if ($storeId = $this->getRequest()->getParam('store',0))
					$model->setStoreId($storeId);
				$model->load($this->getRequest()->getParam('id'));
				foreach ($model->getStoreAttributes() as $attribute)
					$model->setData($attribute.'_default',false);
					
				$model	->addData($data)
						->setId($this->getRequest()->getParam('id'))
						->save();
				/*Save answer*/
				if(isset($data['answer_render_']) && is_array($data['answer_render_'])){
					foreach($data['answer_render_'] as $index=>$item){
						$answer_data = array();
						$answer = Mage::getModel('showroom/answer');
						if ($storeId = $this->getRequest()->getParam('store',0))
							$answer->setStoreId($storeId);
						$answer->load($item['id']);
						//Zend_Debug::dump($answer->getData());die('a');
						foreach ($answer->getStoreAttributes() as $attribute)
							$answer->setData($attribute.'_default',false);
						if($item['delete'] && $answer->getId()){
							$answer->delete();
						}else{
							$answer_data['question_id'] = $model->getId();
							$answer_data['title'] = $item['title'];
							$answer_data['sort'] = $item['sort'];
							$answer_data['status'] = $item['status'];
							
							if ($answer->getCreatedTime == NULL || $answer->getModifiedTime() == NULL)
								$answer->setCreatedTime(now())
									->setModifiedTime(now());
							else
								$answer->setModifiedTime(now());
							//Zend_Debug::dump($_FILES['answer_render_']['name'][$index]);
							if(isset($_FILES['answer_render_']['name'][$index]) && is_array($_FILES['answer_render_']['name'][$index])) {
							try {
								if($_FILES['answer_render_']['name'][$index]['image']){
									/* Starting upload */
									$_FILES['image']=array(
											'name'	=>	$_FILES['answer_render_']['name'][$index]['image'],
											'type'	=>	$_FILES['answer_render_']['type'][$index]['image'],
											'tmp_name'	=>	$_FILES['answer_render_']['tmp_name'][$index]['image'],
											'error'	=>	$_FILES['answer_render_']['error'][$index]['image'],
											'size'	=>	$_FILES['answer_render_']['size'][$index]['image'],
									);
									$uploader = new Varien_File_Uploader('image');
									// Any extention would work
									$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
									$uploader->setAllowRenameFiles(false);
									
									// Set the file upload mode 
									// false -> get the file directly in the specified folder
									// true -> get the file in the product like folders 
									//	(file.jpg will go in something like /media/f/i/file.jpg)
									$uploader->setFilesDispersion(false);
											
									// We set media as the upload dir
									$path = Mage::getBaseDir('media') . DS.'showroom'.DS ;
									$result = $uploader->save($path, $_FILES['image']['name']);
									$answer_data['image'] = $result['file'];
								}
							} catch (Exception $e) {
								$answer_data['image'] = $_FILES['answer_render_']['name'][$index]['image'];
							}
								if (isset($data['rule']['answer'.$item['id']])){
									$rules = $data['rule'];
									if (isset($rules['answer'.$item['id']]))
										$answer_data['conditions'] = $rules['answer'.$item['id']];
									unset($data['rule']['answer'.$item['id']]);
								}
								try{
									$answer->loadPost($answer_data);
									Zend_Debug::dump($answer->getData());die('1');
									$answer->save();
									$answer->setId(null);
								}catch(Exception $e){
								
								}
						}
					}
				}
				
			}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('showroom')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId(),'store' => $this->getRequest()->getParam('store')));
					return;
				}
				$this->_redirect('*/*/');
				return;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('showroom')->__('Unable to find item to save'));
		$this->_redirect('*/*/');
	}
 
	/**
	 * save question action
	 */
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			//Zend_Debug::dump($data);die();
			$model = Mage::getModel('showroom/question');
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL)
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				else
					$model->setUpdateTime(now());
				if ($storeId = $this->getRequest()->getParam('store',0))
					$model->setStoreId($storeId);
				$model->load($this->getRequest()->getParam('id'));
				foreach ($model->getStoreAttributes() as $attribute)
					$model->setData($attribute.'_default',false);
					
				$model	->addData($data)
						->setId($this->getRequest()->getParam('id'))
						->save();
				/*Save answer*/
				if(isset($data['answer']['render']) && is_array($data['answer']['render'])){
					foreach($data['answer']['render'] as $index=>$item){
						$answer_data = array();
						$answer = Mage::getModel('showroom/answer');
						if ($storeId = $this->getRequest()->getParam('store',0))
							$answer->setStoreId($storeId);
						$answer->load($item['id']);
						foreach ($answer->getStoreAttributes() as $attribute)
							$answer_data[$attribute.'_default'] = false;
						
						if($item['delete']){
							if($answer->getId())
								$answer->delete();
						}else{
							
							$answer_data['question_id'] = $model->getId();
							$answer_data['title'] = $item['title'];
							$answer_data['sort'] = $item['sort'];
							$answer_data['status'] = $item['status'];
							
							if ($answer->getCreatedTime == NULL || $answer->getModifiedTime() == NULL)
								$answer->setCreatedTime(now())
									->setModifiedTime(now());
							else
								$answer->setModifiedTime(now());
							if(isset($_FILES['answer']['name']['render'][$index]) && is_array($_FILES['answer']['name']['render'][$index])) {
							try {
								if($_FILES['answer']['name']['render'][$index]['image']){
									/* Starting upload */
									$_FILES['image']=array(
											'name'	=>	$_FILES['answer']['name']['render'][$index]['image'],
											'type'	=>	$_FILES['answer']['type']['render'][$index]['image'],
											'tmp_name'	=>	$_FILES['answer']['tmp_name']['render'][$index]['image'],
											'error'	=>	$_FILES['answer']['error']['render'][$index]['image'],
											'size'	=>	$_FILES['answer']['size']['render'][$index]['image'],
									);
									/*$uploader = new Varien_File_Uploader('image');
									// Any extention would work
									$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
									$uploader->setAllowRenameFiles(false);
									
									// Set the file upload mode 
									// false -> get the file directly in the specified folder
									// true -> get the file in the product like folders 
									//	(file.jpg will go in something like /media/f/i/file.jpg)
									$uploader->setFilesDispersion(false);
											
									// We set media as the upload dir
									$path = Mage::getBaseDir('media') . DS.'showroom'.DS ;
									$result = $uploader->save($path, $_FILES['image']['name']);
									$answer_data['image'] = $result['file'];*/
									$image=Mage::helper('showroom')->upload('image','showroom'.DS.'question'.DS.$model->getId());						
									$answer_data['image'] =$image;
								}
							} catch (Exception $e) {
								$answer_data['image'] = $_FILES['answer']['name']['render'][$index]['image'];
							}
							}
							if (isset($data['rule']['answer'.$item['id']])){
								$rules = $data['rule'];
								if (isset($rules['answer'.$item['id']]))
									$answer_data['conditions'] = $rules['answer'.$item['id']];
								unset($data['rule']['answer'.$item['id']]);
							}
							try{
								$answer->loadPost($answer_data);
								$answer->addData($item);
								$answer->save();
								$answer->setId(null);
							}catch(Exception $e){
							
							}
					}
				}
				
			}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('showroom')->__('Question was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId(),'store' => $this->getRequest()->getParam('store')));
					return;
				}
				$this->_redirect('*/*/');
				return;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		//die('aa');
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('showroom')->__('Unable to find item to save'));
		$this->_redirect('*/*/');
	}
	
	/**
	 * delete item action
	 */
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('showroom/question');
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Question was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

	/**
	 * mass delete item(s) action
	 */
	public function massDeleteAction() {
		$showroomIds = $this->getRequest()->getParam('question');
		if(!is_array($showroomIds)){
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
		}else{
			try {
				foreach ($showroomIds as $showroomId) {
					$question = Mage::getModel('showroom/question')->load($showroomId);
					$question->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($showroomIds)));
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}
	
	/**
	 * mass change status for item(s) action
	 */
	public function massStatusAction() {
		$questionIds = $this->getRequest()->getParam('question');
		if(!is_array($questionIds)) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select question(s)'));
		} else {
			try {
				foreach ($questionIds as $questionId) {
					$question = Mage::getSingleton('showroom/question')
						->load($questionId)
						->setStatus($this->getRequest()->getParam('status'))
						->setIsMassupdate(true)
						->save();
				}
				$this->_getSession()->addSuccess(
					$this->__('Total of %d record(s) were successfully updated', count($questionIds))
				);
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}

	/**
	 * export grid item to CSV type
	 */
	public function exportCsvAction(){
		$fileName   = 'showroom.csv';
		$content	= $this->getLayout()->createBlock('showroom/adminhtml_showroom_grid')->getCsv();
		$this->_prepareDownloadResponse($fileName,$content);
	}

	/**
	 * export grid item to XML type
	 */
	public function exportXmlAction(){
		$fileName   = 'showroom.xml';
		$content	= $this->getLayout()->createBlock('showroom/adminhtml_showroom_grid')->getXml();
		$this->_prepareDownloadResponse($fileName,$content);
	}
	
	protected function _isAllowed(){
		return Mage::getSingleton('admin/session')->isAllowed('showroom');
	}
}