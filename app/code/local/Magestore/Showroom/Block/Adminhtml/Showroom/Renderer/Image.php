<?php
class Magestore_Showroom_Block_adminhtml_showroom_renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row){
            $id = $row->getQuestionId();
            $image=$row->getImage();
            $str='<div style="margin-left: opx;margin-top: opx" >'.$row->getAnswerTitle().'</div><img style="width:100px;height:100px" src="'.Mage::getBaseUrl('media').'showroom/question/'.$id.'/'.$image.' "/>';
		return $str;
	}
}