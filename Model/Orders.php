<?php 

/**
 * Class Xdelivery_Model_Orders
 * @package Xdelivery\Model
 */
class Xdelivery_Model_Orders extends Core_Model_Default
{
    /**
     * @var null
     */
    public static $acl = null;

 
    /**
     * @var string
     */
    protected $_db_table = Xdelivery_Model_Db_Table_Orders::class;

 

    public function getStatus()
    {
        $status = [];
        $status['pending_payment'] = 'Pending Payment';        
        $status['processing'] = 'Processing';
        $status['accepted'] = 'Accepted';
        $status['shipped'] = 'Shipped';
        $status['delivered'] = 'Delivered';
        $status['completed'] = 'Completed';
        $status['failed'] = 'Failed';
        $status['canceled'] = 'Cancelled';
        $status['on_hold'] = 'On Hold';        
        $status['refunded'] = 'Return received';      
    
        return $status;
    }

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
     * @param $order_id
     * @param array
     * @return Xdelivery_Model_Orders[]
     */
    public function findOrderById($order_id, $param = [])
    {
        return $this->getTable()->findOrderById($order_id, $param);
    }

    /**
     * @param $customer_id
     * @param array
     * @return Xdelivery_Model_Orders[]
     */
    public function findAllOrderByCustomerId($customer_id, $params = [])
    {
        return $this->getTable()->findAllOrderByCustomerId($customer_id, $params);
    }
    /**     
     * @param array
     * @return Xdelivery_Model_Orders[]
     */
    public function findAllOrder($params = [])
    {
        return $this->getTable()->findAllOrder($params);
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