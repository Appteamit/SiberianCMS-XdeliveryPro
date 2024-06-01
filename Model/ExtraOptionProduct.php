<?php 

/**
 * Class Xdelivery_Model_ExtraOptionProduct
 * @package Xdelivery\Model
 */
class Xdelivery_Model_ExtraOptionProduct extends Core_Model_Default
{
    /**
     * @var null
     */
    public static $acl = null;

 
    /**
     * @var string
     */
    protected $_db_table = Xdelivery_Model_Db_Table_ExtraOptionProduct::class;

    /**
     * @param $Product_id
     * @param array $params
     * @return Xdelivery_Model_Category[]
     */
    public function getProductOptions($product_id, $params = [])
    {
        return $this->getTable()->getProductOptions($product_id, $params);
    }


}