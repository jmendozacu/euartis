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
 * Showroom Answer Model
 * 
 * @category 	Magestore
 * @package 	Magestore_Showroom
 * @author  	Magestore Developer
 */
class Magestore_Showroom_Model_Answer extends Mage_Rule_Model_Rule
{
	protected $_productCollection;
	protected $_store_id = null;
	const ENABLE_STATUS = 1;
	const DISABLE_STATUS = 2;
	protected $_eventPrefix = 'showroom_question_answer';
    protected $_eventObject = 'showroom_question_answer';
	
	public function _construct(){
		parent::_construct();
		$this->_init('showroom/answer');
		
	}
	
	public function getStoreId(){
    	return $this->_store_id;
    }
	
	public function setStoreId($value){
    	$this->_store_id = $value;
    	return $this;
    }
	
	public function getStoreAttributes(){
    	return array(
    		'title',
    		'image',
			'sort',
    		'status'
    	);
    }
	
	public function loadStoreValue($storeId = null){
		
    	if (!$storeId)
    		$storeId = $this->getStoreId();
   		if (!$storeId)
   			return $this;
    	$storeValues = Mage::getModel('showroom/answervalue')->getCollection()
			->addFieldToFilter('answer_id',$this->getId())
			->addFieldToFilter('store_id',$storeId);
		
    	foreach ($storeValues as $value){
    		$this->setData($value->getAttributeCode().'_in_store',true);
    		$this->setData($value->getAttributeCode(),$value->getValue());
    	}
		//Zend_Debug::dump($this->getStoreAttributes());
		
		foreach ($this->getStoreAttributes() as $attribute)
			if (!$this->getData($attribute.'_in_store'))
				$this->setData($attribute.'_default',true);
		
    	return $this;
    }
	
	protected function _beforeSave(){
    	$defaultProgram = Mage::getModel('showroom/answer')->load($this->getId());
    	if ($storeId = $this->getStoreId()){
    		$storeAttributes = $this->getStoreAttributes();
    		foreach ($storeAttributes as $attribute){
	    		if ($this->getData($attribute.'_default')){
	    			$this->setData($attribute.'_in_store',false);
	    		}else{
	    			$this->setData($attribute.'_in_store',true);
	    			$this->setData($attribute.'_value',$this->getData($attribute));
	    		}
	    		if ($defaultProgram->getId())
	    			$this->setData($attribute,$defaultProgram->getData($attribute));
	    	}
    	}
    	return parent::_beforeSave();
    }
	
	protected function _afterSave(){
    	if ($storeId = $this->getStoreId()){
    		$storeAttributes = $this->getStoreAttributes();
    		foreach ($storeAttributes as $attribute){
    			$attributeValue = Mage::getModel('showroom/answervalue')
    				->loadAttributeValue($this->getId(),$storeId,$attribute);
    			if ($this->getData($attribute.'_in_store')){
					
	    			try{
	    				$attributeValue->setValue($this->getData($attribute.'_value'))->save();
	    			}catch(Exception $e){
	    				
	    			}
	    		}elseif($attributeValue && $attributeValue->getId()){
	    			try{
	    				$attributeValue->delete();
	    			}catch(Exception $e){
	    				
	    			}
	    		}
    		}
    	}
    	return parent::_afterSave();
    }
	
	/**
     * load data for answer model
     *
     * @param mixed $id
     * @param string $field
     * @return Magestore_Showroom_Model_Program
     */
    public function load($id, $field=null){
    	parent::load($id,$field);
    	if ($this->getStoreId())
    		$this->loadStoreValue();
    	return $this;
    }
	
	/**
	 * get answer collection by question id
	*/
	
	public function getAnswerCollectionByQuestionId($question_id){
		$collection = $this	->getCollection()
							->addFieldToFilter('question_id',$question_id)
							;
		return $collection;
	}
	
	/**
	 * get answer list by question id
	*/
	
	public function getAnswersByQuestionId($question_id){
		$answers = array();
		$collection = $this	->getAnswerCollectionByQuestionId($question_id);
		foreach($collection as $answer){
			$answers[] = $answer->getData();
		}
		return $answers;
	}
	
	
	/**
	 * get conditions instance
	*/
	
	public function getConditionsInstance(){
    	return Mage::getModel('showroom/rule_condition_combine');
    }
	
	/**
	 * get Product Collection
	*/
	public function getProductCollection($storeId=null){
		if(!$this->_productCollection){
			$productCollection = Mage::getResourceModel('catalog/product_collection');
			$categoryCollection = Mage::getResourceModel('catalog/category_collection');
			$conds = array();
			$conditions = $this->getConditions();
			if($conditions->getConditions()==null){
				$this->_productCollection = null;
				return $this->_productCollection;
			}
			
			/*if condition is all*/
			if($conditions->getAggregator()=='all'){
				$productCollection = $this->getProductsByAll($conditions,$storeId);
			}else{
				/*if condition is any*/
				$productCollection = $this->getProductsByAny($conditions,$storeId);
				
			}
			$this->_productCollection= $productCollection;
		}
		return $this->_productCollection;
	}
	
	
	protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
	
	public function getProductsByAll($conditions,$storeId=null){
		$productCollection = Mage::getResourceModel('catalog/product_collection');
		$categoryCollection = Mage::getResourceModel('catalog/category_collection');
		if($conditions->getValue()){
				$productIds = Mage::getResourceModel('catalog/product_collection')->getAllIds();
				foreach($conditions->getConditions() as $cond){
					if($cond->getAttribute()=='category_ids'){
						$subProductIds = Mage::getResourceModel('catalog/product_collection')->getAllIds();
						$equiv = $this->getEquivFromOperatorForCat($cond->getOperator(),$conditions->getValue());
						$conds[] = array('attribute'=>$cond->getAttribute(),'operator'=>$equiv,'value'=>$cond->getValue());
						foreach($conds as $index=>$condition){
							$vals = explode(',',$condition['value']);
							$values = array();
							foreach($vals as $k=>$v){
								$values[$k] = trim($v);
							}
							if($condition['operator']=='contain'){
								$p1Ids = Mage::getResourceModel('catalog/product_collection')->getAllIds();
								if(count($values)){
									foreach($values as $catId){
										$category = Mage::getModel('catalog/category')->load($catId);
										if($storeId)
											$category->setStoreId($storeId);
										$p1Ids = array_intersect($p1Ids,$category->getProductCollection()->getAllIds());
										//Zend_Debug::dump($p1Ids);
									}
									$subProductIds = array_intersect($subProductIds,$p1Ids);
									//Zend_Debug::dump($p1Ids);
								}
							}else if($condition['operator']=='ncontain'){
								$p2Ids = Mage::getResourceModel('catalog/product_collection')->getAllIds();
								if(count($values)){
									foreach($values as $catId){
										$category = Mage::getModel('catalog/category')->load($catId);
										if($storeId)
											$category->setStoreId($storeId);
										$p2Ids = array_unique(array_merge($p2Ids,$category->getProductCollection()->getAllIds()));
									}
									$subProductIds = array_diff($subProductIds,$p2Ids);
								}
							}
							else{
								$categoryCollection = Mage::getResourceModel('catalog/category_collection')->addFieldToFilter('entity_id',array('in'=>$values));
								$p3Ids = array();
								foreach($categoryCollection as $cat){
									if($storeId)
											$cat->setStoreId($storeId);
									$p3Ids = array_unique(array_merge($p3Ids,$cat->getProductCollection()->getAllIds()));
								}
								if($condition['operator']=='in')
									$subProductIds = array_intersect($subProductIds,$p3Ids);
								else
									$subProductIds = array_diff($subProductIds,$p3Ids);
							}
							//$productCollection->addAttributeToFilter('entity_id',array($condition['operator']=>$pIds));
						}
						//Zend_Debug::dump($subProductIds);
						$productIds = array_unique(array_intersect($productIds,$subProductIds));
					}else{
						/* conditions attribute is not 'category_ids'*/
						$subProductIds = Mage::getResourceModel('catalog/product_collection')->getAllIds();
						$equiv = $this->getEquivFromOperator($cond->getOperator(),$conditions->getValue());
						$cond2s[] = array('attribute'=>$cond->getAttribute(),'operator'=>$equiv,'value'=>$cond->getValue());
						$p4Ids = Mage::getResourceModel('catalog/product_collection')->getAllIds();
						foreach($cond2s as $index=>$condition){
							if(($condition['operator']=='in') || ($condition['operator']=='nin')){
								$vals = explode(',',$condition['value']);
								$values = array();
								foreach($vals as $k=>$v){
									$values[$k] = trim($v);
								}
							}else if(($condition['operator']=='like')||($condition['operator']=='nlike')){
								$values = '%'.$condition['value'].'%';
							}else{
								$values = $condition['value'];
							}
							$p4Ids = Mage::getResourceModel('catalog/product_collection')->addAttributeToFilter($condition['attribute'],array($condition['operator']=>$values))->getAllIds();
							//Zend_Debug::dump($p4Ids);
							$subProductIds = array_intersect($subProductIds,$p4Ids);
							
						}
						//Zend_Debug::dump($subProductIds);						
						$productIds = array_unique(array_intersect($productIds,$subProductIds));
					}
					
				}
				//Zend_Debug::dump($productIds);
				$productCollection = Mage::getResourceModel('catalog/product_collection')->addFieldToFilter('entity_id',array('in'=>$productIds));
			}else{
				/* if condition value is false*/
				$productIds = array();
				
				$cond3s = array();
				foreach($conditions->getConditions() as $cond){
					/*if attribute is category ids*/
					if($cond->getAttribute()=='category_ids'){
						$subProductIds = Mage::getResourceModel('catalog/product_collection')->getAllIds();
						//Zend_Debug::dump($conditions->getValue());
						$equiv = $this->getEquivFromOperatorForCat($cond->getOperator(),$conditions->getValue());
						$cond3s[] = array('attribute'=>$cond->getAttribute(),'operator'=>$equiv,'value'=>$cond->getValue());						
						foreach($cond3s as $index=>$condition){
							$vals = explode(',',$condition['value']);
							$values = array();
							foreach($vals as $k=>$v){
								$values[$k] = trim($v);
							}
							if($condition['operator']=='contain'){
								$p1Ids = Mage::getResourceModel('catalog/product_collection')->getAllIds();
								if(count($values)){
									foreach($values as $catId){
										$category = Mage::getModel('catalog/category')->load($catId);
										if($storeId)
											$category->setStoreId($storeId);
										$p1Ids = array_intersect($p1Ids,$category->getProductCollection()->getAllIds());
									}
									$subProductIds = array_intersect($subProductIds,$p1Ids);
								}
							}else if($condition['operator']=='ncontain'){
								$p2Ids = Mage::getResourceModel('catalog/product_collection')->getAllIds();
								if(count($values)){
									foreach($values as $catId){
										$category = Mage::getModel('catalog/category')->load($catId);
										if($storeId)
											$category->setStoreId($storeId);
										$p2Ids = array_unique(array_intersect($p2Ids,$category->getProductCollection()->getAllIds()));
									}
									$subProductIds = array_diff($subProductIds,$p2Ids);
								}
							}
							else{
								$categoryCollection = Mage::getResourceModel('catalog/category_collection')->addFieldToFilter('entity_id',array('in'=>$values));
								$p3Ids = array();
								foreach($categoryCollection as $cat){
									if($storeId)
											$cat->setStoreId($storeId);
									$p3Ids = array_unique(array_merge($p3Ids,$cat->getProductCollection()->getAllIds()));
								}
								if($condition['operator']=='in')
									$subProductIds = array_intersect($subProductIds,$p3Ids);
								else
									$subProductIds = array_diff($subProductIds,$p3Ids);
							}
							//$productCollection->addAttributeToFilter('entity_id',array($condition['operator']=>$pIds));
						}
						$productIds = array_unique(array_intersect($productIds,$subProductIds));
					}else{
						/* conditions attribute is not 'category_ids'*/
						$subProductIds = Mage::getResourceModel('catalog/product_collection')->getAllIds();
						$equiv = $this->getEquivFromOperator($cond->getOperator(),$conditions->getValue());
						$cond4s[] = array('attribute'=>$cond->getAttribute(),'operator'=>$equiv,'value'=>$cond->getValue());
						
						$p4Ids = Mage::getResourceModel('catalog/product_collection')->getAllIds();
						foreach($cond4s as $index=>$condition){
							if(($condition['operator']=='in') || ($condition['operator']=='nin')){
								$vals = explode(',',$condition['value']);
								$values = array();
								foreach($vals as $k=>$v){
									$values[$k] = trim($v);
								}
							}else if(($condition['operator']=='like')||($condition['operator']=='nlike')){
								$values = '%'.$condition['value'].'%';
							}else{
								$values = $condition['value'];
							}
							$p4Ids = Mage::getResourceModel('catalog/product_collection')->addAttributeToFilter($condition['attribute'],array($condition['operator']=>$values))->getAllIds();							
							$subProductIds = array_intersect($subProductIds,$p4Ids);
						}
						$productIds = array_unique(array_merge($productIds,$subProductIds));
					}
				}
				//$productCollection = Mage::getResourceModel('catalog/product_collection')->addFieldToFilter('entity_id',array('nin'=>$productIds));
				$productCollection = Mage::getResourceModel('catalog/product_collection')->addFieldToFilter('entity_id',array('in'=>$productIds));
				
			}
			return $productCollection;
	}
	
	public function getProductsByAny($conditions,$storeId=null){
		$productCollection = Mage::getResourceModel('catalog/product_collection');
		$categoryCollection = Mage::getResourceModel('catalog/category_collection');
		if($conditions->getValue()){
				$productIds = array();
				foreach($conditions->getConditions() as $cond){
					if($cond->getAttribute()=='category_ids'){
						$subProductIds = Mage::getResourceModel('catalog/product_collection')->getAllIds();
						$equiv = $this->getEquivFromOperatorForCat($cond->getOperator(),$conditions->getValue());
						$conds[] = array('attribute'=>$cond->getAttribute(),'operator'=>$equiv,'value'=>$cond->getValue());
						foreach($conds as $index=>$condition){
							$vals = explode(',',$condition['value']);
							$values = array();
							foreach($vals as $k=>$v){
								$values[$k] = trim($v);
							}
							if($condition['operator']=='contain'){
								$p1Ids = Mage::getResourceModel('catalog/product_collection')->getAllIds();
								if(count($values)){
									foreach($values as $catId){
										$category = Mage::getModel('catalog/category')->load($catId);
										if($storeId)
											$category->setStoreId($storeId);
										$p1Ids = array_intersect($p1Ids,$category->getProductCollection()->getAllIds());
									}
									$subProductIds = array_intersect($subProductIds,$p1Ids);
								}
							}else if($condition['operator']=='ncontain'){
								$p2Ids = Mage::getResourceModel('catalog/product_collection')->getAllIds();
								if(count($values)){
									foreach($values as $catId){
										$category = Mage::getModel('catalog/category')->load($catId);
										if($storeId)
											$category->setStoreId($storeId);
										$p2Ids = array_unique(array_merge($p1Ids,$category->getProductCollection()->getAllIds()));
									}
									$subProductIds = array_diff($subProductIds,$p2Ids);
								}
							}
							else{
								$categoryCollection = Mage::getResourceModel('catalog/category_collection')->addFieldToFilter('entity_id',array('in'=>$values));
								$p3Ids = array();
								foreach($categoryCollection as $cat){
									if($storeId)
											$cat->setStoreId($storeId);
									$p3Ids = array_unique(array_merge($p3Ids,$cat->getProductCollection()->getAllIds()));
								}
								if($condition['operator']=='in')
									$subProductIds = array_intersect($subProductIds,$p3Ids);
								else
									$subProductIds = array_diff($subProductIds,$p3Ids);
							}
							//Zend_Debug::dump($subProductIds);
							//$productCollection->addAttributeToFilter('entity_id',array($condition['operator']=>$pIds));
						}
						$productIds = array_unique(array_merge($productIds,$subProductIds));
					}else{
						/* conditions attribute is not 'category_ids'*/
						$cond2s = array();
						$subProductIds = Mage::getResourceModel('catalog/product_collection')->getAllIds();
						$equiv = $this->getEquivFromOperator($cond->getOperator(),$conditions->getValue());
						$cond2s[] = array('attribute'=>$cond->getAttribute(),'operator'=>$equiv,'value'=>$cond->getValue());
						$p4Ids = Mage::getResourceModel('catalog/product_collection')->getAllIds();
						foreach($cond2s as $index=>$condition){
							if(($condition['operator']=='in') || ($condition['operator']=='nin')){
								$vals = explode(',',$condition['value']);
								$values = array();
								foreach($vals as $k=>$v){
									$values[$k] = trim($v);
								}
							}else if(($condition['operator']=='like')||($condition['operator']=='nlike')){
								$values = '%'.$condition['value'].'%';
							}else{
								$values = $condition['value'];
							}
							$p4Ids = Mage::getResourceModel('catalog/product_collection')->addAttributeToFilter($condition['attribute'],array($condition['operator']=>$values))->getAllIds();
							//Zend_Debug::dump($p4Ids);
							$subProductIds = array_intersect($subProductIds,$p4Ids);
							
						}
						$productIds = array_unique(array_merge($productIds,$subProductIds));
					}
				}
				$productCollection = Mage::getResourceModel('catalog/product_collection')->addFieldToFilter('entity_id',array('in'=>$productIds));
			}else{
				/* if condition value is false*/
				
				$productIds = array();
				foreach($conditions->getConditions() as $cond){
					/*if attribute is category ids*/
					if($cond->getAttribute()=='category_ids'){
						$subProductIds = Mage::getResourceModel('catalog/product_collection')->getAllIds();
						$cond3s = array();
						$equiv = $this->getEquivFromOperatorForCat($cond->getOperator(),$conditions->getValue());
						$cond3s[] = array('attribute'=>$cond->getAttribute(),'operator'=>$equiv,'value'=>$cond->getValue());
						foreach($cond3s as $index=>$condition){
							$vals = explode(',',$condition['value']);
							$values = array();
							foreach($vals as $k=>$v){
								$values[$k] = trim($v);
							}
							if($condition['operator']=='contain'){
								$p1Ids = Mage::getResourceModel('catalog/product_collection')->getAllIds();
								if(count($values)){
									foreach($values as $catId){
										$category = Mage::getModel('catalog/category')->load($catId);
										if($storeId)
											$category->setStoreId($storeId);
										$p1Ids = array_intersect($p1Ids,$category->getProductCollection()->getAllIds());
									}
									$subProductIds = array_intersect($subProductIds,$p1Ids);
								}
							}else if($condition['operator']=='ncontain'){
								$p2Ids = Mage::getResourceModel('catalog/product_collection')->getAllIds();
								if(count($values)){
									foreach($values as $catId){
										$category = Mage::getModel('catalog/category')->load($catId);
										$p2Ids = array_unique(array_intersect($p2Ids,$category->getProductCollection()->getAllIds()));
									}
									$subProductIds = array_diff($subProductIds,$p2Ids);
								}
							}
							else{
								$categoryCollection = Mage::getResourceModel('catalog/category_collection')->addFieldToFilter('entity_id',array('in'=>$values));
								$p3Ids = array();
								foreach($categoryCollection as $cat){
									if($storeId)
											$cat->setStoreId($storeId);
									$p3Ids = array_unique(array_merge($p3Ids,$cat->getProductCollection()->getAllIds()));
								}
								if($condition['operator']=='in')
									$subProductIds = array_intersect($subProductIds,$p3Ids);
								else
									$subProductIds = array_diff($subProductIds,$p3Ids);
							}
							//$productCollection->addAttributeToFilter('entity_id',array($condition['operator']=>$pIds));
						}
						$productIds = array_unique(array_merge($productIds,$subProductIds));
					}else{
						/* conditions attribute is not 'category_ids'*/
						$subProductIds = Mage::getResourceModel('catalog/product_collection')->getAllIds();
						$cond4s = array();
						$equiv = $this->getEquivFromOperator($cond->getOperator(),$conditions->getValue());
						$cond4s[] = array('attribute'=>$cond->getAttribute(),'operator'=>$equiv,'value'=>$cond->getValue());
						
						$p4Ids = Mage::getResourceModel('catalog/product_collection')->getAllIds();
						foreach($cond4s as $index=>$condition){
							if(($condition['operator']=='in') || ($condition['operator']=='nin')){
								$vals = explode(',',$condition['value']);
								$values = array();
								foreach($vals as $k=>$v){
									$values[$k] = trim($v);
								}
							}else if(($condition['operator']=='like')||($condition['operator']=='nlike')){
								$values = '%'.$condition['value'].'%';
							}else{
								$values = $condition['value'];
							}
							$p4Ids = Mage::getResourceModel('catalog/product_collection')->addAttributeToFilter($condition['attribute'],array($condition['operator']=>$values))->getAllIds();
							$subProductIds = array_intersect($subProductIds,$p4Ids);
						}
						$productIds = array_unique(array_merge($productIds,$subProductIds));
					}
					
				}
				//$productCollection = Mage::getResourceModel('catalog/product_collection')->addFieldToFilter('entity_id',array('nin'=>$productIds));
				$productCollection = Mage::getResourceModel('catalog/product_collection')->addFieldToFilter('entity_id',array('in'=>$productIds));
				
			}
			//Zend_Debug::dump($productCollection->getAllIds());die();
			return $productCollection;
	}
	
	
	
	/*
	** get sql equivalent from operator
	*/
	
	public function getEquivFromOperator($operator,$parentValue='1'){
		if($parentValue){
			$equivs = Mage::helper('showroom')->getEquivalents();
		}else{
			$equivs = Mage::helper('showroom')->getFalseEquivalents();
		}
		return $equivs[$operator]?$equivs[$operator]:'in';
	}
	
	public function getEquivFromOperatorForCat($operator,$parentValue='1'){
		if($parentValue){
			$equivs = Mage::helper('showroom')->getEquivalentsForCat();
		}else{
			$equivs = Mage::helper('showroom')->getFalseEquivalentsForCat();
		}
		return $equivs[$operator]?$equivs[$operator]:'in';
	}
	

	
}