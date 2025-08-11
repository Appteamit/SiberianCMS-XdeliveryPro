<?php
/**
 * Class Xdelivery_Model_Db_Table_Tax
 * @package Xdelivery\Model\Db\Table
 */
class Xdelivery_Model_Db_Table_Tax extends Core_Model_Db_Table
{
    protected $_name = "xdelivery_taxes";
    protected $_primary = "id";

    /**
     * @param array|int $values Can be an array or value_id
     * @param null|string $order
     * @param array $params
     * @return array
     */
    public function findAll($values = [], $order = null, $params = [])
    {
        $select = $this->select()
            ->from($this->_name)
            ->where("status != ?", "deleted");

        // Handle both array input and direct value_id for backward compatibility
        $value_id = is_array($values) ? ($values['value_id'] ?? 0) : $values;
        if ($value_id) {
            $select->where("value_id = ?", $value_id);
        }

        if ($order) {
            $select->order($order);
        }

        // Handle additional params like limit, offset
        if (isset($params['limit'])) {
            $offset = $params['offset'] ?? 0;
            $select->limit($params['limit'], $offset);
        }

        return $this->_db->fetchAll($select);
    }
}