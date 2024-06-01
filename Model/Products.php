<?php 

/**
 * Class Xdelivery_Model_Products
 * @package Xdelivery\Model
 */
class Xdelivery_Model_Products extends Core_Model_Default
{
    /**
     * @var null
     */
    public static $acl = null;

 
     /**
     * @var string
     */
    protected $_db_table = Xdelivery_Model_Db_Table_Products::class;

    /**
     * @param $valuesId
     * @param array $params
     * @return Xdelivery_Model_Products[]
     */
    public function findByValueId($valuesId, $params = [])
    {
        return $this->getTable()->findByValueId($valuesId, $params);
    }

        /**
     * @param $valuesId
     * @param array $params
     * @return Xdelivery_Model_Products[]
     */
    public function findAppByValueId($valuesId, $params = [])
    {
        return $this->getTable()->findAppByValueId($valuesId, $params);
    }
    public function getProducts($valuesId, $params = [])
    {
        return $this->getTable()->getProducts($valuesId, $params);
    }


   /**
     * @param $valuesId
     * @param array $params
     * @return Xdelivery_Model_Products[]
     */
    public function findByWishlistByCustomer($valuesId, $params = [])
    {
        return $this->getTable()->findByWishlistByCustomer($valuesId, $params);
    }

    /**
     * @param $valuesId
     * @param array $params
     * @return Xdelivery_Model_Products[]
     */
    public function countAllForApp($valuesId, $params = [])
    {
        return $this->getTable()->countAllForApp($valuesId, $params);
    }

    public function deleteQuery($tableName, $columnName, $value){
        $db = Zend_Db_Table::getDefaultAdapter();
        return $db->query('DELETE FROM '.$tableName.' WHERE '.$columnName.' = "' . $value . '";');
    }

    public function updateStatusQuery($tableName, $columnName, $value){
        $db = Zend_Db_Table::getDefaultAdapter();
        return $db->query('UPDATE '.$tableName.' SET is_active = 0 WHERE '.$columnName.' = "' . $value . '";');
    }
 

}