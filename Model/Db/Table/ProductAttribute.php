<?php
/**
 * Class Xdelivery_Model_Db_Table_ProductAttribute
 * @package Xdelivery\Model\Db\Table
 */
class Xdelivery_Model_Db_Table_ProductAttribute extends Core_Model_Db_Table
{
    protected $_name                    = "xdelivery_product_attribute";
    protected $_primary                 = "id";

	  /**
     * @param $app_id
     * @param int $limit
     * @return array
     */
    public function getProductAttribute($product_id) {
        $select = $this->_db->select()
            ->from(['main' => $this->_name], [
            	"id",
                "attribute_value_id",
                "attribute_id",
                "is_variant",
                "product_id",
                "is_active",
                "created_at"                       
            ]);

			$select->where("main.product_id = ?", $product_id);
            $select->where("main.is_active = ?", 1);
            		 
            $select->joinLeft(['a' => 'xdelivery_attributes'], 'a.id = main.attribute_id', ['a.attribute_name']);
            $select->joinLeft(['v' => 'xdelivery_attribute_values'], 'v.id = main.attribute_value_id', ['v.value_name']);
            
           
         return $this->toModelClass($this->_db->fetchAll($select))->toArray();
        }


}