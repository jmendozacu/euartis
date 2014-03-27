<?php 
class Magestore_Showroom_Block_Adminhtml_Question_Edit_Tab_Answers_Renderer_Products
extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 public function render(Varien_Object $row){
	//Zend_Debug::dump((string)Mage::helper('catalog/image')->init($row, 'small_image')->resize(75));
	//Zend_Debug::dump(is_file((string)Mage::helper('catalog/image')->init($row, 'small_image')->resize(75)));
	try{
		return sprintf('<img src="%s" title="%s" />',Mage::helper('catalog/image')->init($row, 'small_image')->resize(75),$row->getName());
	}catch(Exception $e){
		return "";
	}
 }
}