<?php
class Magestore_Showroom_Model_Indexer extends Mage_Index_Model_Indexer_Abstract
{
	const EVENT_MATCH_RESULT_KEY = 'showroom_product_match_result';
    protected $_matchedEntities = array(
        'showroom_product' => array(
            Mage_Index_Model_Event::TYPE_SAVE
        )
    );
	
    public function getName(){
        return Mage::helper('showroom')->__('Showroom Product');
    }

    public function getDescription(){
        return Mage::helper('showroom')->__('Showroom Product');
    }
	
	 public function matchEvent(Mage_Index_Model_Event $event)
    {
        $data      = $event->getNewData();
		if (isset($data[self::EVENT_MATCH_RESULT_KEY])) {
            return $data[self::EVENT_MATCH_RESULT_KEY];
        }
        $entity = $event->getEntity();
		if ($entity == Mage_Core_Model_Store::ENTITY) {
            $store = $event->getDataObject();
            if ($store && ($store->isObjectNew() || $store->dataHasChangedFor('group_id'))) {
                $result = true;
            } else {
                $result = false;
            }
        } elseif ($entity == Mage_Core_Model_Store_Group::ENTITY) {
            $storeGroup = $event->getDataObject();
            $hasDataChanges = $storeGroup && ($storeGroup->dataHasChangedFor('root_category_id')
                || $storeGroup->dataHasChangedFor('website_id'));
            if ($storeGroup && !$storeGroup->isObjectNew() && $hasDataChanges) {
                $result = true;
            } else {
                $result = false;
            }
        } else {
            $result = parent::matchEvent($event);
        }
		$event->addNewData(self::EVENT_MATCH_RESULT_KEY, true);
		return $result;
	}
    /**
     * Register data required by process in event object
     * Check if category ids was changed
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
		die('222');
        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, true);
        $entity = $event->getEntity();
        switch ($entity) {
            case Mage_Catalog_Model_Product::ENTITY:
               $this->_registerProductEvent($event);
                break;

            case Mage_Catalog_Model_Category::ENTITY:
                $this->_registerCategoryEvent($event);
                break;

            case Mage_Catalog_Model_Convert_Adapter_Product::ENTITY:
                $event->addNewData('catalog_category_product_reindex_all', true);
                break;

            case Mage_Core_Model_Store::ENTITY:
            case Mage_Core_Model_Store_Group::ENTITY:
                $process = $event->getProcess();
                $process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
                break;
        }
        return $this;
    }

    protected function _processEvent(Mage_Index_Model_Event $event){
		die('aa');
        // process index event
    }

    public function reindexAll(){
        // reindex all data
    }
}