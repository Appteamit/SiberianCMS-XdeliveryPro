<?php 

/**
 * Class Xdelivery_Model_Carts
 * @package Xdelivery\Model
 */
class Xdelivery_Model_Carts extends Core_Model_Default
{
    /**
     * @var null
     */
    public static $acl = null;

 
    /**
     * @var string
     */
    protected $_db_table = Xdelivery_Model_Db_Table_Carts::class;


    /**
     * @param $valuesId
     * @param array $device_uid
     * @return Xdelivery_Model_Carts[]
     */
    public function findByDeviceId($valuesId, $device_uid)
    {
        return $this->getTable()->findByDeviceId($valuesId, $device_uid);
    }


    /**
     * @param $valuesId
     * @param array $device_uid
     * @param array $customer_id
     * @return Xdelivery_Model_Carts[]
     */
    public function findByDeviceAndUserId($valuesId, $customer_id, $device_uid)
    {
        return $this->getTable()->findByDeviceAndUserId($valuesId, $customer_id, $device_uid);
    }
  
  /**
     * @param $valuesId
     * @param array $device_uid
     * @param array $customer_id
     * @return Xdelivery_Model_Carts[]
     */
    public function countByDeviceAndUserId($valuesId, $customer_id, $device_uid)
    {
        return $this->getTable()->findByDeviceAndUserId($valuesId, $customer_id, $device_uid);
    }
  
   /**
     * @param $valuesId
     * @param array $device_uid
     * @param array $customer_id
     * @return Xdelivery_Model_Carts[]
     */
    public function syncDeviceCustomer($device_uid, $customer_id, $value_id)
    {
        return $this->getTable()->syncDeviceCustomer($device_uid, $customer_id, $value_id);
    }

    public function deleteQuery($tableName, $columnName, $value){
        $db = Zend_Db_Table::getDefaultAdapter();
        return $db->query('DELETE FROM '.$tableName.' WHERE '.$columnName.' = "' . $value . '";');
    }

}