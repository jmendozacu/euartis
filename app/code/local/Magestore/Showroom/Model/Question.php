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
 * Showroom Question Model
 * 
 * @category 	Magestore
 * @package 	Magestore_Showroom
 * @author  	Magestore Developer
 */
class Magestore_Showroom_Model_Question extends Mage_Core_Model_Abstract
{
	const STATUS_ENABLED	= 1;
	const STATUS_DISABLED	= 2;
	protected $_store_id = null;
	
	public function _construct(){
		parent::_construct();
		$this->_init('showroom/question');
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
    		'status',
			'type',
			'sort'
    	);
    }
	
	/**
     * load data for question model
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
     * function load store value     *
     * @param int $storeId
     * @return Magestore_Showroom_Model_Question
     */
    public function loadStoreValue($storeId = null){
    	if (!$storeId)
    		$storeId = $this->getStoreId();
   		if (!$storeId)
   			return $this;
    	$storeValues = Mage::getModel('showroom/questionvalue')->getCollection()
			->addFieldToFilter('question_id',$this->getId())
			->addFieldToFilter('store_id',$storeId);
		
    	foreach ($storeValues as $value){
    		$this->setData($value->getAttributeCode().'_in_store',true);
    		$this->setData($value->getAttributeCode(),$value->getValue());
    	}
		
		foreach ($this->getStoreAttributes() as $attribute)
			if (!$this->getData($attribute.'_in_store'))
				$this->setData($attribute.'_default',true);
		
    	return $this;
    }
	
	protected function _beforeSave(){
    	$defaultProgram = Mage::getModel('showroom/question')->load($this->getId());
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
    			$attributeValue = Mage::getModel('showroom/questionvalue')
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
}