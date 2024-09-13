<?php

class Xdelivery_Model_Db_Table_OrderTransactions extends Core_Model_Db_Table {
    protected $_name                    = "xdelivery_order_transactions";
    protected $_primary                 = "id";


     /**
     * @param $value_id
     * @param int $limit
     * @return array
     */
    public function findByValueId($value_id, $params = []) {
        $select = $this->_db->select()
            ->from(['main' => $this->_name], [
                "id",
                "order_id",
                "transaction_id",
                "payment_method_id",
                "payment_method",       
                "status as payment_status",
                "gateway_transaction_id",
                "amount",               
                "created_at"
            ]);

          $select->joinLeft(['o' => 'xdelivery_orders'], 'main.order_id = o.id', ["o.store_id", "o.store_id", "o.customer_id",
                "o.order_number", "o.sub_amount", "o.total_tax", "o.delivery_cost", "o.total_amount", "o.paid_amount", "o.notes", "o.delivery_date", "o.delivery_time", "o.delivery_method_id", "o.delivery_method", "o.customer_firstname", "o.customer_lastname", "o.customer_email", "o.customer_street", "o.customer_postcode", "o.customer_city", "o.customer_phone", "o.total_qty", "o.status as order_status",]);

          $select->joinLeft(['pm' => 'xdelivery_payment_method'], 'pm.id = main.payment_method_id', ['pm.method_type', 'pm.label_name']);

          $select->joinLeft(['s' => 'xdelivery_store'], 's.store_id = o.store_id', ['s.store_name', 's.store_phone', 's.store_email', 's.store_city', 's.store_address', 's.store_zip']);

          if (array_key_exists("limit", $params) && array_key_exists("offset", $params)) {
            $select->limit($params["limit"], $params["offset"]);
          }

          $select->joinLeft(['c' => 'customer'], 'c.customer_id = o.customer_id', ['c.firstname', 'c.lastname', 'c.email']);

          if (array_key_exists("search", $params)) {
             $term = $params["search"];
             $select->where("o.order_number LIKE '%$term%' OR o.customer_firstname LIKE '%$term%' OR o.customer_postcode LIKE '%$term%'  OR o.customer_phone LIKE '%$term%'  OR o.customer_email LIKE '%$term%'");
          }
  
          if (array_key_exists("sorts", $params) && !empty($params["sorts"])) {
            $orders = [];
            foreach ($params["sorts"] as $key => $dir) {
                $order = ($dir == -1) ? "DESC" : "ASC";
                $orders = "main.{$key} {$order}";
            }
            $select->order($orders);

          } else {
            $select->order('main.id DESC');
          }

          $select->order('main.id DESC');

          $select->where("o.value_id = ?", $value_id); 

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
            ->where('o.value_id = ?', $value_id);

            $select->joinLeft(['o' => 'xdelivery_orders'], 'main.order_id = o.id', ["o.store_id", "o.store_id", "o.customer_id",
                "o.order_number", "o.sub_amount", "o.total_tax", "o.delivery_cost", "o.total_amount", "o.paid_amount", "o.notes", "o.delivery_date", "o.delivery_time", "o.delivery_method_id", "o.delivery_method", "o.customer_firstname", "o.customer_lastname", "o.customer_email", "o.customer_street", "o.customer_postcode", "o.customer_city", "o.customer_phone", "o.total_qty", "o.status as order_status",]);

          $select->joinLeft(['pm' => 'xdelivery_payment_method'], 'pm.id = main.payment_method_id', ['pm.method_type', 'pm.label_name']);

          $select->joinLeft(['s' => 'xdelivery_store'], 's.store_id = o.store_id', ['s.store_name', 's.store_phone', 's.store_email', 's.store_city', 's.store_address', 's.store_zip']);
      
           
          $select->joinLeft(['c' => 'customer'], 'c.customer_id = o.customer_id', ['c.firstname', 'c.lastname', 'c.email']);

          if (array_key_exists("search", $params)) {
             $term = $params["search"];
             $select->where("o.order_number LIKE '%$term%' OR o.customer_firstname LIKE '%$term%' OR o.customer_postcode LIKE '%$term%'  OR o.customer_phone LIKE '%$term%'  OR o.customer_email LIKE '%$term%'");
          }
     
        return $this->_db->fetchCol($select);
    }

    public function getOrderPaymentMethodName($order_id){
      $select = "SELECT xdelivery_payment_method.label_name FROM `xdelivery_order_transactions` 
      join xdelivery_payment_method on xdelivery_order_transactions.payment_method_id = xdelivery_payment_method.id
      WHERE  xdelivery_order_transactions.order_id=$order_id;";
      $data =  $this->_db->fetchAll($select);
      if($data){
        return $data[0]['label_name'];
      }
      return '';
    }


}