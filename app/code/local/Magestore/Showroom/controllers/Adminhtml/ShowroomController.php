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
 * Showroom Adminhtml Controller
 * 
 * @category 	Magestore
 * @package 	Magestore_Showroom
 * @author  	Magestore Developer
 */
class Magestore_Showroom_Adminhtml_ShowroomController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * init layout and set active for current menu
	 *
	 * @return Magestore_Showroom_Adminhtml_ShowroomController
	 */
	protected function _initAction(){
		$this->loadLayout()
			->_setActiveMenu('showroom/showroom')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Showroom Manager'), Mage::helper('adminhtml')->__('Showroom Manager'));
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
		$model  = Mage::getModel('showroom/showroom')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data))
				$model->setData($data);

			Mage::register('showroom_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('showroom/showroom');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Showroom Manager'), Mage::helper('adminhtml')->__('Showroom Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Showroom News'), Mage::helper('adminhtml')->__('Showroom News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('showroom/adminhtml_showroom_edit'))
				->_addLeft($this->getLayout()->createBlock('showroom/adminhtml_showroom_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('showroom')->__('Showroom does not exist'));
			$this->_redirect('*/*/');
		}
	}

 
	/**
	 * save item action
	 */
	public function saveAction() {
		
		if ($data = $this->getRequest()->getPost()) {
			/*
			if(isset($_FILES['images']['name']) && $_FILES['images']['name'] != '') {
				Zend_Debug::dump($_FILES);die();
				try {
					/* Starting upload 	
					$uploader = new Varien_File_Uploader('images');
					
					// Any extention would work
			   		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS .'showroom';
					$result = $uploader->save($path, $_FILES['images']['name'] );
					//var_dump($result);die();
					$data['image'] = 'showroom/'.$result['file'];
				} catch (Exception $e) {
				}
			}
			*/
			
            $data['type']=$data['type_showroom'];
            $data['status']=$data['status_showroom'];
	  		
			$model = Mage::getModel('showroom/showroom');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL)
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				else
					$model->setUpdateTime(now());
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('showroom')->__('Showroom was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
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
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('showroom')->__('Unable to find Showroom to save'));
		$this->_redirect('*/*/');
	}
 
	/**
	 * delete item action
	 */
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('showroom/showroom');
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Showroom was successfully deleted'));
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
		$showroomIds = $this->getRequest()->getParam('showroom');
		if(!is_array($showroomIds)){
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Showroom(s)'));
		}else{
			try {
				foreach ($showroomIds as $showroomId) {
					$showroom = Mage::getModel('showroom/showroom')->load($showroomId);
					$showroom->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d Showroom(s) were successfully deleted', count($showroomIds)));
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
		$showroomIds = $this->getRequest()->getParam('showroom');
		if(!is_array($showroomIds)) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Showroom(s)'));
		} else {
			try {
				foreach ($showroomIds as $showroomId) {
					$showroom = Mage::getSingleton('showroom/showroom')
						->load($showroomId)
						->setStatus($this->getRequest()->getParam('status'))
						->setIsMassupdate(true)
						->save();
				}
				$this->_getSession()->addSuccess(
					$this->__('Total of %d Showroom(s) were successfully updated', count($showroomIds))
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

	public function preferredproductsgridAction() 
	{        
		$this->loadLayout();
        $this->renderLayout();		
	}

	public function listfollowgridAction() 
	{        
		$this->loadLayout();
        $this->renderLayout();		
	}

	public function stylegridAction() 
	{        
		$this->loadLayout();
        $this->renderLayout();		
	}
}