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
 * Showroom Answer Adminhtml Controller
 * 
 * @category 	Magestore
 * @package 	Magestore_Showroom
 * @author  	Magestore Developer
 */
class Magestore_Showroom_Adminhtml_AnswerController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * init layout and set active for current menu
	 *
	 * @return Magestore_Showroom_Adminhtml_ShowroomController
	 */
	protected function _initAction(){
		$this->loadLayout()
			->_setActiveMenu('showroom/answer')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Answers Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		return $this;
	}
 
	/**
	 * index action
	 */
	public function index2Action(){
		$id = $this->getRequest()->getParam('answer_id');
		$answer = Mage::getModel('showroom/answer')->load($id);
		$collection = $answer->getProductCollection();
		//Zend_Debug::dump($collection->getSize());
		if($collection){
			Zend_Debug::dump($collection->getSize());
			foreach($collection->getAllIds() as $id){
				$p = Mage::getModel('catalog/product')->load($id);
				Zend_Debug::dump($p->getId());
				Zend_Debug::dump($p->getName());
				Zend_Debug::dump($p->getSku());
				echo "<hr/>";
			}
		}
		/*$conditions = $answer->getConditions();
		//Zend_Debug::dump($conditions->getConditions());
		$productCollection = Mage::getResourceModel('catalog/product_collection');
		Zend_Debug::dump(count($productCollection));
		$conds = array();
		foreach($conditions->getConditions() as $cond){
			if($cond->getAttribute()=='category_ids'){
				$vals = explode(',',$cond->getValue());
				$values = array();
				foreach($vals as $k=>$v){
					$cat = Mage::getModel('catalog/category')->load(trim($v));
					$productCollection->addCategoryFilter($cat);
				}
			}else{
				$op = 'in';
				if($cond->getOperator()=='!='){
					$op = 'nin';
				}
				$conds[$cond->getAttribute()] = array('operator'=>$op,'value'=>$cond->getValue());
			}
			//$product->addCategoryIds(3)
			//Zend_Debug::dump($productCollection->getSelectSql()->__toString());
			//Zend_Debug::dump($productCollection->getSize());
			//die('1');
			//Zend_Debug::dump(get_class_methods($cond));die();
		}
		foreach($conds as $attribute=>$condition){
			$vals = explode(',',$condition['value']);
			$values = array();
			foreach($vals as $k=>$v){
				$values[$k] = trim($v);
			}
			$productCollection->addAttributeToFilter($attribute,array($condition['operator']=>$values));
		}	
		Zend_Debug::dump($productCollection->getSelectSql()->__toString());
		Zend_Debug::dump($productCollection->getSize());
		die();
		Zend_Debug::dump(get_class_methods($answer->getConditions()));*/
	}
	
	public function indexAction(){
		$this->_initAction();
		$head = $this->getLayout()->getBlock('head')
				->setCanLoadExtJs(true)
				->setCanLoadRulesJs(true);
		$block = $this->getLayout()->createBlock('showroom/adminhtml_question_edit_tab_answers_products');
		//$this->getResponse()->setHeader();
		$this->getResponse()->setBody($head->toHtml().$block->toHtml());
	}

	/**
	 * view and edit item action
	 */
	public function editAction() {
		$id	 = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('showroom/answer')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data))
				$model->setData($data);

			Mage::register('showroom_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('showroom/answer');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('showroom/adminhtml_showroom_edit'))
				->_addLeft($this->getLayout()->createBlock('showroom/adminhtml_showroom_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('showroom')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	/**
	 * save item action
	 */
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
				try {
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('filename');
					
					// Any extention would work
			   		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS ;
					$result = $uploader->save($path, $_FILES['filename']['name'] );
					$data['filename'] = $result['file'];
				} catch (Exception $e) {
					$data['filename'] = $_FILES['filename']['name'];
				}
			}
	  		
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
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('showroom')->__('Item was successfully saved'));
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
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('showroom')->__('Unable to find item to save'));
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
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
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
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
		}else{
			try {
				foreach ($showroomIds as $showroomId) {
					$showroom = Mage::getModel('showroom/showroom')->load($showroomId);
					$showroom->delete();
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
		$showroomIds = $this->getRequest()->getParam('showroom');
		if(!is_array($showroomIds)) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
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
					$this->__('Total of %d record(s) were successfully updated', count($showroomIds))
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
	
	/**
	 * new condition action
	 */
	public function newConditionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
		$prefix = $this->getRequest()->getParam('prefix');
        $type = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('catalogrule/rule'))
            ->setPrefix($prefix)
			;
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }
	
	public function renderAction(){
		$this->loadLayout();
		$block = $this->getLayout()->createBlock('showroom/adminhtml_question_edit_tab_answers_render');
		$index = $this->getRequest()->getParam('index');
		$prefix = 'new_'.$index;
		$block->setPrefix($prefix);
		$block->setIndex($index);
		$this->getResponse()->setBody($block->toHtml());
	}
	
	public function productGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}