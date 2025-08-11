<?php 

/**
 * Class Xdelivery_Model_Tax
 * @package Xdelivery\Model
 */
class Xdelivery_Model_Tax extends Core_Model_Default
{
    /**
     * @var null
     */
    public static $acl = null;

    /**
     * @var string
     */
    protected $_db_table = Xdelivery_Model_Db_Table_Tax::class;

    /**
     * @param array $values
     * @param null $order
     * @param array $params
     * @return []
     */
    public function findAll($values = [], $order = null, $params = [])
    {
        // If you specifically need to find by value_id, you can handle it here
        $value_id = isset($values['value_id']) ? $values['value_id'] : 0;
        return $this->getTable()->findAll($values, $order, $params);
    }

    /**
     * Alternative method if you specifically need to find by value_id
     * @param int $value_id
     * @return []
     */
    public function findAllByValueId($value_id = 0)
    {
        return $this->getTable()->findAll(['value_id' => $value_id]);
    }
}