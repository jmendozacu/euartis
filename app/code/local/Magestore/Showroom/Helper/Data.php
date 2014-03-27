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
 * Showroom Helper
 * 
 * @category 	Magestore
 * @package 	Magestore_Showroom
 * @author  	Magestore Developer
 */
class Magestore_Showroom_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getShowroomUrl()
	{
		$url = $this->_getUrl('showroom/listing/index', array());

		return $url;	
	}
	
	public function getQuestionTypeOptionArray(){
		return array(
			'1'	=> Mage::helper('showroom')->__('No'),
			'2'   => Mage::helper('showroom')->__('Yes')
		);
	}
	public function getQuestionTypeOptionHash(){
		$options = array();
		foreach (self::getQuestionTypeOptionArray() as $value => $label)
			$options[] = array(
				'value'	=> $value,
				'label'	=> $label
			);
		return $options;
	}
	
	public function getEquivalents(){
		$result = array(
			'=='	=>	'eq',
			'!='	=>	'neq',
			'>='	=>	'gteq',
			'<='	=>	'lteq',
			'<'		=>	'lt',
			'>'		=>	'gt',
			'{}'	=>	'like',
			'!{}'	=>	'nlike',
			'()'	=>	'in',
			'!()'	=>	'nin'
		);
		return $result;
	}
	
	public function getFalseEquivalents(){
		$result = array(
			'=='	=>	'neq',
			'!='	=>	'eq',
			'>='	=>	'lt',
			'<='	=>	'gt',
			'<'		=>	'gteq',
			'>'		=>	'lteq',
			'{}'	=>	'nlike',
			'!{}'	=>	'like',
			'()'	=>	'nin',
			'!()'	=>	'in'
		);
		return $result;
	}
	
	public function getEquivalentsForCat(){
		$result = array(
			'=='	=>	'contain',
			'!='	=>	'ncontain',
			'{}'	=>	'contain',
			'!{}'	=>	'ncontain',
			'()'	=>	'in',
			'!()'	=>	'nin'
		);
		return $result;
	}
	public function getFalseEquivalentsForCat(){
		$result = array(
			'=='	=>	'ncontain',
			'!='	=>	'contain',
			'{}'	=>	'nin',
			'!{}'	=>	'in',
			'()'	=>	'ncontain',
			'!()'	=>	'contain'
		);
		return $result;
	}
		
	public function getAnswerStatusOptionArray(){
		return array(
		'1' => Mage::helper('showroom')->__('Enable'),
		'2'   => Mage::helper('showroom')->__('Disable')
		);
	}
	public function getAnswerStatusOptionHash(){
		$options = array();
		foreach (self::getQuestionTypeOptionArray() as $value => $label)
			$options[] = array(
			'value' => $value,
			'label' => $label
		);
		return $options;
	}
	
	public function getShowroomConfig($name){
		return Mage::getStoreConfig('showroom/showroom/'.$name);
	}
	
	public function getGeneralConfig($name){
		return Mage::getStoreConfig('showroom/general/'.$name);
	}
	public function getPopupConfig($name){
		return Mage::getStoreConfig('showroom/popup/'.$name);
	}
	public function getReferConfig($name){
		return Mage::getStoreConfig('showroom/refer/'.$name);
	}
	
	public function getAnswerCollectionByQuestionId($questionId,$storeId=0){
		$answerCollection = Mage::getModel('showroom/answer')->getCollection()
							->addFieldToFilter('question_id',$questionId)
							//->addFieldToFilter('store_id',$storeId)
							//->addFieldToFilter('status',Magestore_Showroom_Model_Answer::ENABLE_STATUS)
							->setStoreId($storeId)
							;
		return $answerCollection;
	}

	public function getProfileByCustomerId($id){
		$profile = Mage::getModel('showroom/showroomprofile');
		$collection = Mage::getModel('showroom/showroomprofile')->getCollection()->addFieldToFilter('customer_id',$id);
		if($collection->getSize()){
			$profile->load($collection->getFirstItem()->getId());
        }
        return $profile;
	}
	public function getShowroomByProfileId($id){
		$showroom=Mage::getModel('showroom/showroom');
		$collection=Mage::getModel('showroom/showroom')->getCollection()
					->addFieldToFilter('profile_id',$id)
					->addFieldToFilter('status',2)
					->addFieldToFilter('store_group_id',Mage::app()->getStore()->getGroupId());
        if($collection->getSize()){
			$showroom->load($collection->getFirstItem()->getId());
        }
        return $showroom;
	}
	/**
     * update image after risze
     *
     * @param  file $name,int $widht,string $path
     * @return string $image
     */
	public function upload($name,$path){
		if(isset($_FILES[$name]['name']) && $_FILES[$name]['name'] != '') {
			$_backgroundColor  = array(255, 255, 255);
			try {
					$pathBaseDir = Mage::getBaseDir('media') . DS .$path.DS.$_FILES[$name]['name'];
            		$imageObj = new Varien_Image($_FILES[$name]['tmp_name']);
            		$imageObj->backgroundColor($_backgroundColor);
            		$imageObj->constrainOnly(TRUE);
            		$imageObj->keepAspectRatio(TRUE);
    				$imageObj->keepFrame(TRUE);
            		$imageObj->save($pathBaseDir);
            		$image=$_FILES[$name]['name'];
				} catch (Exception $e) {
				}
		}
		return $image;
	}
	public function getSwitchedTemplate($file)
 	{
  		$package=$this->getGeneralConfig('template');
  		if($package!='default')
  			$package='pink';
  		$styledir = 'style';
  		$filename = substr($file,strrpos($file,DS)+1);
 		$offsetdir = str_replace('showroom'.DS,'',substr($file,strrpos($file,'showroom'.DS)));
  		$basedir = substr($file,0,strrpos($file,'showroom'.DS)).'showroom';
  		$switchedfile = $basedir.DS.$package.DS.$offsetdir;
  		return $switchedfile;
 	}
 	public function getSwitchedSkinFile(){
  		$package=$this->getGeneralConfig('template');
  		$styledir = 'css/magestore';
  		$cssfile=$styledir.'/'.$package.'/'.'showroom.css';
   		return $cssfile;
 	}
	
	/*
	** get module, controller and action not display pop up
	*/
	
	public function getModuleArray(){
		return array(
			0	=>	'customer',
			1	=>	'showroom'
		);
	}
	
	public function getControllerArray(){
		return array(
			0	=>	'account',
			1	=>	'create',
			2	=>	'index',
			3	=>	'listing'
		);
	}
	public function getShowroomLogin(){
		$showroom=Mage::getModel('showroom/showroom');
        $customer= Mage::getSingleton('customer/session')->getCustomer();
        $customer_id=$customer->getId();
        $profile=$this->getProfileByCustomerId($customer_id);
        if($profile->getProfileId())
            $showroom=$this->getShowroomByProfileId($profile->getProfileId());
        return $showroom;
    }
    public function getShowroomCurrent($id=null){
        $showroom=$this->getShowroomLogin();
        if($id)
            $showroom=Mage::getModel('showroom/showroom')->load($id);
        return $showroom;
    }
    public function getFollowing($showroom_id,$customer_id){
    	$following=Mage::getModel('showroom/followers');
		$collection = Mage::getModel('showroom/followers')
					->getCollection()
					->addFieldToFilter('showroom_id',$showroom_id)
					->addFieldToFilter('customer_id',$customer_id);
		if($collection->getSize()){
			$following->load($collection->getFirstItem()->getId());
        }
        return $following;
    }
	
	/*
	** delete product from showroom
	*/
	
	public function deleteProducts($showroomId){
		$products = Mage::getModel('showroom/showroomproduct')
						->getCollection()
						->addFieldToFilter('showroom_id',$showroomId)
						;
		if($products->getSize()){
			foreach($products as $product){
				$product->delete();
			}
		}
	}
	
	/*
	** delete showroom answers when update showroom
	*/
	
	public function deleteShowroomAnswers($showroomId){
		$showroomAnswers = Mage::getModel('showroom/showroomanswer')
						->getCollection()
						->addFieldToFilter('showroom_id',$showroomId)
						;
		if($showroomAnswers->getSize()){
			foreach($showroomAnswers as $showroomAnswer){
				$showroomAnswer->delete();
			}
		}
	}
	
	/*
	** get image by profile
	*/
	
	public function getImageByProfile($profile){
    	if($profile->getImage())
            $image=Mage::getBaseUrl('media').$profile->getImage();
        else{
            $image=Mage::getBaseUrl('media').'showroom/avatar.jpg';
        }
        return $image;
    }
    /*
	** get getProductCollection all answer
	*/
    public function _getProductCollection(){
			$this->_productCollection=Mage::getResourceModel('catalog/product_collection')
											->setStoreId($this->getStoreId())
											->addFieldToFilter('entity_id',$this->getArrayFilter())
											->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
											->addMinimalPrice()
											->addTaxPercents()
											->addStoreFilter()
											;
								
            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($this->_productCollection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($this->_productCollection);	
									
		return $this->_productCollection;
	}
	public function getArrayFilter(){
		$model=Mage::getModel('showroom/answer')->getCollection()->setStoreId($this->getStoreId());
        $array=array();
        foreach($model as $a) {
        	$answer=Mage::getModel('showroom/answer')->load($a->getAnswerId());
        	if($answer->getProductCollection())
     	   	$array[]=array('in'=>$answer->getProductCollection()->getAllIds());
        }
        return $array;
	}
	public function getStoreId()
    {
        return Mage::app()->getStore()->getId();
    }
    /*
    ** 
    */
    public function isAddToShowroom($productId){

		$model=Mage::getModel('showroom/answer')->getCollection()->setStoreId($this->getStoreId());
        foreach($model as $a) {
			$answer=Mage::getModel('showroom/answer')->load($a->getAnswerId());
			if($answer->getProductCollection())
				$productIds=$answer->getProductCollection()->getAllIds();
			if (in_array($productId,$productIds)){
				return true;
			}
		}
		return false;
    }
	/*
	**
	*/

	public function getShowroomProduct($productId,$showroomId){
		$model=Mage::getModel('showroom/showroomproduct');
		$collection = Mage::getModel('showroom/showroomproduct')
					->getCollection()
					->addFieldToFilter('product_id',$productId)
					->addFieldToFilter('showroom_id',$showroomId);
		if($collection->getSize()){
			$model->load($collection->getFirstItem()->getId());
        }
        return $model;
	}
	public function preProcessStr($str){
		return str_replace('\'','\\\'',$str);
	}
}