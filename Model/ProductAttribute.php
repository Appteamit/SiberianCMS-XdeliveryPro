<?php 

/**
 * Class Xdelivery_Model_ProductAttribute
 * @package Xdelivery\Model
 */
class Xdelivery_Model_ProductAttribute extends Core_Model_Default
{
    /**
     * @var null
     */
    public static $acl = null;

 
     /**
     * @var string
     */
    protected $_db_table = Xdelivery_Model_Db_Table_ProductAttribute::class;

     /**
     * @param $product_id
     * @param array $params
     * @return Xdelivery_Model_Products[]
     */
    public function getProductAttribute($product_id)
    {
        return $this->getTable()->getProductAttribute($product_id);
    }

}