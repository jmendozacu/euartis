<?php

class Magestore_Showroom_Block_Adminhtml_Question_Edit_Tab_Answers_Render extends Mage_Core_Block_Template
{

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('showroom/answers/render.phtml');
    }
	
	/*
	** get answer
	*/
    
	public function getAnswer(){
		$answer_id = $this->getAnswerId();
		$storeId = $this->getRequest()->getParam('store',0);
		$answer = Mage::getModel('showroom/answer');
		$answer->setStoreId($storeId);
		$answer->load($answer_id);
		return $answer;
	}
}
