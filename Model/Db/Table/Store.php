<?php

class xdelivery_Model_Db_Table_Store extends Core_Model_Db_Table {
    protected $_name = "xdelivery_store";
    protected $_primary = "store_id";

    /**
     * @param $app_id
     * @param int $limit
     * @return array
     */
    public function findByValueId($value_id, $params = []) {
        $day = strtolower(date("l"));
        $select = $this->_db->select()
            ->from(['main' => $this->_name], [
                "store_id",
                "store_name",
                "store_sub_title",
                "store_phone",
                "store_email",
                "store_address",
                "store_city",
                "store_zip",
                "store_location",
                "address_lng",
                "address_lat",
                "is_default",
                "commision_percentage",
                "image",
                "is_active",
                "created_at",
                "total_product" => new Zend_Db_Expr('('.$this->_db->select()->from(array('p'=>  'xdelivery_products'),array(new Zend_Db_Expr('COUNT(p.id)')))->where('p.store_id = main.store_id')->where('p.is_active != ?', 2).')')        
            ]);

        $select->where("main.value_id = ?", $value_id);          

        if (array_key_exists("limit", $params) && array_key_exists("offset", $params)) {
            $select->limit($params["limit"], $params["offset"]);
        }

        if (array_key_exists("filter", $params)) {
            $select->where("(main.store_name LIKE ?)", "%" . $params["filter"] . "%");
        }

        if (array_key_exists("is_active", $params)) {
            $select->where("main.is_active = ?", 1);
        } else {
            $select->where("main.is_active != ?", 2);
        }

        if (array_key_exists("sorts", $params) && !empty($params["sorts"])) {
            $orders = [];
            foreach ($params["sorts"] as $key => $dir) {
                $order = ($dir == -1) ? "DESC" : "ASC";
                $orders = "main.{$key} {$order}";
            }
            $select->order($orders);
        } else {
            $select->order('main.store_name ASC');
        }

        return $this->toModelClass($this->_db->fetchAll($select));
    }

    /**
     * @param $value_id
     */
    public function countAllForApp($value_id, $params = [])
    {
        $select =$this->_db->select()
            ->from(['main' => $this->_name], [ 
                'COUNT(main.store_id)'
                ])
            ->where('main.value_id = ?', $value_id);

        $select->where("main.is_active != ?", 2);

        if (array_key_exists("filter", $params)) {
            $select->where("(main.store_name LIKE ?)", "%" . $params["filter"] . "%");
        }

        return $this->_db->fetchCol($select);
    }

    /**
     * @param array $values
     * @param null $order
     * @param array $params
     * @return array
     */
    public function findAll($values = [], $order = null, $params = [])
    {
        $select = $this->_db->select()
            ->from(['main' => $this->_name])
            ->where("main.is_active != ?", 2);

        if (!empty($values)) {
            if (isset($values['value_id'])) {
                $select->where("main.value_id = ?", $values['value_id']);
            }
            // Add other possible value conditions here
        }

        if ($order) {
            $select->order($order);
        }

        if (!empty($params)) {
            // Handle additional params like limit, offset, etc.
            if (isset($params['limit'])) {
                $offset = isset($params['offset']) ? $params['offset'] : 0;
                $select->limit($params['limit'], $offset);
            }
        }

        return $this->_db->fetchAll($select);
    }
}