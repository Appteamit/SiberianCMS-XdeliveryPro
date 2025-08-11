<?php

class Xdelivery_Model_Store extends Core_Model_Default {

    /**
     * @var null
     */
    public static $acl = null;

    /**
     * @var string
     */
    protected $_db_table = Xdelivery_Model_Db_Table_Store::class;

    /**
     * @param $valuesId
     * @param array $params
     * @return Xdelivery_Model_Store[]
     */
    public function findByValueId($valuesId, $params = [])
    {
        return $this->getTable()->findByValueId($valuesId, $params);
    }

    /**
     * @param array $values
     * @param null $order
     * @param array $params
     * @return Xdelivery_Model_Store[]
     */
    public function findAll($values = [], $order = null, $params = [])
    {
        // If you need special handling for valuesId, you can add it here
        return $this->getTable()->findAll($values, $order, $params);
    }

    public function countAllForApp($valuesId, $params = [])
    {
        return $this->getTable()->countAllForApp($valuesId, $params = []);
    }
}