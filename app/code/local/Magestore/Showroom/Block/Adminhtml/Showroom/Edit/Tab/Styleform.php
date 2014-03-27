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
class Magestore_Showroom_Block_Adminhtml_Showroom_Edit_Tab_Styleform extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('stylegrid');
        $this->setDefaultSort('question_id');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection=Mage::getModel('showroom/question')->getCollection();
        $collection->getSelect()
                    ->join(array('sqa'=>Mage::getModel('core/resource')->getTableName('showroom_question_answer')),'sqa.question_id=main_table.question_id',array('image'=>'sqa.image'))
                    ->join(array('sa'=>Mage::getModel('core/resource')->getTableName('showroom_answer')),'sqa.answer_id=sa.answer_id AND sa.showroom_id='.$this->getRequest()->getParam('id'),array('answer_title'=>'sqa.title'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('question_id', array(
            'header'    => Mage::helper('showroom')->__('ID'),
            'sortable'  => true,
            'width'     => 60,
            'index'     => 'question_id',
            'filter_index'  =>  'main_table.question_id'
        ));

        $this->addColumn('title', array(
            'header'    => Mage::helper('showroom')->__('Question title'),
            'sortable'  => true,
            'width'     => '50%',
            'index'     => 'title',
            'filter_index'  =>  'main_table.title'
        ));

        $this->addColumn('image', array(
            'header'    => Mage::helper('showroom')->__('Answer'),
            'sortable'  => true,
            'width'     => '50%',
            'index'     => 'image',
            'filter_index'  =>  'sqa.image',
            'renderer'  => 'showroom/adminhtml_showroom_renderer_Image',
        ));

       
        return parent::_prepareColumns();
    }
	
	public function getRowUrl(){
		return;
	}
	
    public function getGridUrl()
    {
        return $this->getData('grid_url') ? $this->getData('grid_url') : $this->getUrl('*/*/stylegrid', array('_current'=>true));
    }
    public function getShowroom()
	{
		return Mage::getModel('showroom/showroom')
			->load($this->getRequest()->getParam('id'))
			;		
	}
}