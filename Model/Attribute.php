<?php

class Xdelivery_Model_Attribute extends Core_Model_Default {

    /**
     * @var null
     */
    public static $acl = null;

    /**
     * @var string
     */
    protected $_db_table = Xdelivery_Model_Db_Table_Attribute::class;

    /**
     * @param $valuesId
     * @param array $params
     * @return Xdelivery_Model_Attribute[]
     */
    public function findByValueId($valuesId, $params = [])
    {
        return $this->getTable()->findByValueId($valuesId, $params);
    }

    /**
     * @param $valuesId
     * @param array $params
     * @return Xdelivery_Model_Attribute[]
     */
    public function countAllForApp($valuesId, $params = [])
    {
        return $this->getTable()->countAllForApp($valuesId, $params);
    }

    /**
     * @param $valuesId
     * @param array $params
     * @return Xdelivery_Model_Attribute[]
     */
    public function maxPosition($valuesId)
    {
        return $this->getTable()->maxPosition($valuesId);
    }

      /**
     * @param $valuesId
     * @param array $params
     * @return Xdelivery_Model_Attribute[]
     */
    public function sortable($params, $parent_id)
    {
        return $this->getTable()->sortable($params, $parent_id);
    }
   
}