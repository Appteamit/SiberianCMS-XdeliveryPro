<?php 

/**
 * Class Xdelivery_Model_ProductVariant
 * @package Xdelivery\Model
 */
class Xdelivery_Model_ProductVariant extends Core_Model_Default
{
    /**
     * @var null
     */
    public static $acl = null;

 
     /**
     * @var string
     */
    protected $_db_table = Xdelivery_Model_Db_Table_ProductVariant::class;


    
     /**
     * @param $product_id
     * @param array $params
     * @return Xdelivery_Model_Products[]
     */
    public function getProductVariants($product_id, $params = [])
    {
        return $this->getTable()->getProductVariants($product_id, $params);
    }

    /**
     * @param $product_id
     * @param array $params
     * @return Xdelivery_Model_Products[]
     */
    public function getProductAttributeById($product_id, $params = [])
    {
        return $this->getTable()->getProductAttributeById($product_id, $params);
    }

   /**
     * @param $product_id
     * @param array $params
     * @return Xdelivery_Model_Products[]
     */
    public function getProductVariantValues($product_id)
    {
        return $this->getTable()->getProductVariantValues($product_id);
    }

}