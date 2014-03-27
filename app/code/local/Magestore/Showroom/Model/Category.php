<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Magestore
 * @package     Magestore_Showroom
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Config category source
 *
 * @category   Magestore
 * @package    Magestore_Showroom
 * @author     Magestore Developer
 */
class Magestore_Showroom_Model_Category
{
    protected static $options=array();
    public function pust_array($category){
        $level=$category->getLevel();

        self::$options[] = array(
               'label' => Mage::helper('showroom')->__(str_repeat(' --- ',$level).$category->getName()),
               'value' => $category->getId()
            );
    }
    public function toOptionArray()
    {
        $tree = Mage::getResourceModel('catalog/category_tree');
        $collection = Mage::getResourceModel('catalog/category_collection');

        $collection->addAttributeToSelect('name')
                   // ->addRootLevelFilter()
                    ->load();
        foreach ($collection as $category) {
            $this->getArray($category);
            break;
            }
        return self::$options;
    }
    public function getArray($category){
        $collection = Mage::getResourceModel('catalog/category_collection')
                        ->addFieldToFilter('parent_id',$category->getId());
        $collection->addAttributeToSelect('name')->load();
        $this->pust_array($category);
        foreach ($collection as $category) {
            $this->getArray($category);
        }
    }

}
