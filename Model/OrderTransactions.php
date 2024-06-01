<?php 

/**
 * Class Xdelivery_Model_OrderTransactions
 * @package Xdelivery\Model
 */
class Xdelivery_Model_OrderTransactions extends Core_Model_Default
{
    /**
     * @var null
     */
    public static $acl = null;

 
    /**
     * @var string
     */
    protected $_db_table = Xdelivery_Model_Db_Table_OrderTransactions::class;

      /**
     * @param $value_id
     * @param array
     * @return Xdelivery_Model_Orders[]
     */
    public function findByValueId($value_id, $param = [])
    {
        return $this->getTable()->findByValueId($value_id, $param);
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

}