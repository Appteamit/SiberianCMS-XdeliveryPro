<?php

class Xdelivery_Model_Db_Table_Coupons extends Core_Model_Db_Table {
    protected $_name                    = "xdelivery_coupon";
    protected $_primary                 = "id";

     /**
     * @param $app_id
     * @param int $limit
     * @return array
     */
    public function findByValueId($value_id, $params = []) {
        $select = $this->_db->select()
            ->from(['main' => $this->_name], [
                "id",
                "name",
                "status",
                "coupon_code",
                "discount_type",
                "discount_value",
                "allow_free_shipping",
                "min_spend",
                "max_spend",
                "usage_limit_per_coupon",
                "usage_limit_per_customer",
                "start_date",                
                "end_date",
                "start_time",
                "end_time",
                "created_at",         
            ]);

            $select->where("main.value_id = ?", $value_id);
            $select->where("main.is_delete != ?", 1);
                  

          if (array_key_exists("limit", $params) && array_key_exists("offset", $params)) {
                $select->limit($params["limit"], $params["offset"]);
            }

            if (array_key_exists("filter", $params)) {
                $select->where("(main.name LIKE ?)", "%" . $params["filter"] . "%");
            }
  
          if (array_key_exists("sorts", $params) && !empty($params["sorts"])) {
                $orders = [];
                foreach ($params["sorts"] as $key => $dir) {
                    $order = ($dir == -1) ? "DESC" : "ASC";
                    $orders = "main.{$key} {$order}";
                }
                $select->order($orders);
            } else {
                $select->order('main.created_at ASC');
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
                 'COUNT(main.id)'
                ])
            ->where('main.value_id = ?', $value_id);

        $select->where("main.is_delete != ?", 1);
        if (array_key_exists("filter", $params)) {
            $select->where("(main.name LIKE ?)", "%" . $params["filter"] . "%");
        }
 
        return $this->_db->fetchCol($select);
    }
    public function findAll($value_id=0){        
        $select = "SELECT * FROM `xdelivery_coupon` WHERE  value_id=$value_id AND `is_delete`=0;";
        return $this->_db->fetchAll($select);
    }
}