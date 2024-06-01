<?php

class Xdelivery_Model_Db_Table_Orders extends Core_Model_Db_Table {
    protected $_name                    = "xdelivery_orders";
    protected $_primary                 = "id";


     /**
     * @param $order_id
     * @param int $limit
     * @return array
     */
    public function findOrderById($order_id, $params = []) {
        $select = $this->_db->select()
            ->from(['main' => $this->_name], [
            	  "id as order_id",
                "store_id",
                "value_id",
                "customer_id",
                "order_number",       
                "sub_amount",
                "sub_amount_with_vat",
                "total_tax",
                "total_vat_tax",
                "delivery_cost",
                "total_amount",
                "paid_amount",
                "notes",
                "delivery_date",
                "delivery_time",
                "delivery_method_id",
                "delivery_method",
                "customer_firstname",
                "customer_lastname",
                "customer_email",
                "customer_street",
                "customer_postcode",
                "customer_city",
				        "customer_phone",
                "customer_info",
                "total_qty",
				        "status as order_status",
                "is_return_request",
                "promocode_id",
                "discount_code",
                "discount_amount",
                "admin_remark",
                "tracking_type",
                "tracking_number_url",
                "tips_amount",
			 "created_at"
            ]);

          $select->joinLeft(['txn' => 'xdelivery_order_transactions'], 'txn.order_id = main.id', ['txn.transaction_id', 'txn.payment_method_id', 'txn.payment_method', 'txn.status as payment_status']);

          $select->joinLeft(['pm' => 'xdelivery_payment_method'], 'pm.id = txn.payment_method_id', ['pm.method_type', 'pm.label_name']);

          $select->joinLeft(['s' => 'xdelivery_store'], 's.store_id = main.store_id', ['s.store_name', 's.store_phone', 's.store_email', 's.store_city', 's.store_address', 's.store_zip']);

          $select->where("main.id = ?", $order_id); 

        return $this->_db->fetchRow($select);

    }

     /**
     * @param $customer_id
     * @param int $limit
     * @return array
     */
    public function findAllOrderByCustomerId($customer_id, $params = []) {
        $select = $this->_db->select()
            ->from(['main' => $this->_name], [
                "id as order_id",
                "store_id",
                "customer_id",
                "order_number",       
                "sub_amount",
                "total_tax",
                "delivery_cost",
                "tips_amount",
                "total_amount",
                "paid_amount",
                "notes",
                "is_managed",
                "delivery_date",
                "delivery_time",
                "delivery_method_id",
                "delivery_method",
                "customer_firstname",
                "customer_lastname",
                "customer_email",
                "customer_street",
                "customer_postcode",
                "customer_city",
                "customer_phone",
                "customer_info",
                "total_qty",
                "status as order_status",
                "is_return_request",
                "created_at"
            ]);

          $select->joinLeft(['txn' => 'xdelivery_order_transactions'], 'txn.order_id = main.id', ['txn.transaction_id', 'txn.payment_method_id', 'txn.payment_method', 'txn.status as payment_status']);

          $select->joinLeft(['pm' => 'xdelivery_payment_method'], 'pm.id = txn.payment_method_id', ['pm.method_type', 'pm.label_name']);

          $select->joinLeft(['s' => 'xdelivery_store'], 's.store_id = main.store_id', ['s.store_name', 's.store_phone', 's.store_email', 's.store_city', 's.store_address', 's.store_zip']);

          $select->order('main.id DESC');

          $select->where("main.customer_id = ?", $customer_id); 

        return $this->toModelClass($this->_db->fetchAll($select));
       
    }
     /**
     * @param $value_id, days_range, order_status     
     * @return array
     */
    public function findAllOrder($params = []) {
      $value_id=$params['value_id'];
        $select = $this->_db->select()
            ->from(['main' => $this->_name], [
                "id as order_id",
                "store_id",
                "customer_id",
                "order_number",       
                "sub_amount",
                "total_tax",
                "delivery_cost",
                "discount_amount",
                "tips_amount",
                "total_amount",
                "paid_amount",
                "notes",
                "delivery_date",
                "delivery_time",
                "delivery_method_id",
                "delivery_method",
                "customer_firstname",
                "customer_lastname",
                "customer_email",
                "customer_street",
                "is_managed",
                "customer_postcode",
                "customer_city",
                "customer_phone",
                "customer_info",
                "total_qty",
                "status as order_status",
                "is_return_request",
                "created_at"
            ]);

          $select->joinLeft(['txn' => 'xdelivery_order_transactions'], 'txn.order_id = main.id', ['txn.transaction_id', 'txn.payment_method_id', 'txn.payment_method', 'txn.status as payment_status']);

          $select->joinLeft(['pm' => 'xdelivery_payment_method'], 'pm.id = txn.payment_method_id', ['pm.method_type', 'pm.label_name']);

          $select->joinLeft(['s' => 'xdelivery_store'], 's.store_id = main.store_id', ['s.store_name', 's.store_phone', 's.store_email', 's.store_city', 's.store_address', 's.store_zip']);

          $select->order('main.id DESC');

          if (array_key_exists("order_status",$params) && $params['order_status']!='all') {
            $select->where("main.status = ?", $params['order_status']);  
          }
          if (array_key_exists("days_range",$params) && $params['days_range']) {
            // Calculate the date x days ago
            $daysAgo = date('Y-m-d', strtotime('-' . $params['days_range'] . ' days'));        
            $select->where("DATE(main.created_at) >= ?", $daysAgo);
        }        
          $select->where("main.value_id = ?", $value_id);  

        return $this->toModelClass($this->_db->fetchAll($select));
       
    }

     /**
     * @param $value_id
     * @param int $limit
     * @return array
     */
    public function findByValueId($value_id, $params = []) {
        $select = $this->_db->select()
            ->from(['main' => $this->_name], [
                "id as order_id",
                "store_id",
                "customer_id",
                "order_number",       
                "sub_amount",
                "total_tax",
                "delivery_cost",
                "tips_amount",
                "total_amount",
                "paid_amount",
                "notes",
                "is_managed",
                "delivery_date",
                "delivery_time",
                "delivery_method_id",
                "delivery_method",
                "customer_firstname",
                "customer_lastname",
                "customer_email",
                "customer_street",
                "customer_postcode",
                "customer_city",
                "customer_phone",
                "customer_info",
                "total_qty",
                "status as order_status",
                "is_return_request",
                "created_at"
            ]);

          $select->joinLeft(['txn' => 'xdelivery_order_transactions'], 'txn.order_id = main.id', ['txn.transaction_id', 'txn.payment_method_id', 'txn.payment_method', 'txn.status as payment_status']);

          $select->joinLeft(['pm' => 'xdelivery_payment_method'], 'pm.id = txn.payment_method_id', ['pm.method_type', 'pm.label_name']);

          $select->joinLeft(['s' => 'xdelivery_store'], 's.store_id = main.store_id', ['s.store_name', 's.store_phone', 's.store_email', 's.store_city', 's.store_address', 's.store_zip']);

          if (array_key_exists("limit", $params) && array_key_exists("offset", $params)) {
            $select->limit($params["limit"], $params["offset"]);
          }

           if (array_key_exists("store_id", $params) && !empty($params['store_id'])) {
              $select->where("(main.store_id = ?)", $params["store_id"] );
            }

            
           if (array_key_exists("status", $params) && !empty($params['status'])) {
              $select->where("(main.status = ?)", $params["status"] );
            }

          $select->joinLeft(['c' => 'customer'], 'c.customer_id = main.customer_id', ['c.firstname', 'c.lastname', 'c.email']);

          if (array_key_exists("search", $params)) {
             $term = $params["search"];
             $select->where("main.order_number LIKE '%$term%' OR main.customer_firstname LIKE '%$term%' OR main.customer_postcode LIKE '%$term%'  OR main.customer_phone LIKE '%$term%'  OR main.customer_email LIKE '%$term%'");
          }
 
          if (array_key_exists("filter", $params) && !empty($params["filter"])) {    
              $filter = $params['filter'];

              if(!empty($filter["date_type"]) && $filter["date_type"] == 'booking_date'){
                  if (!empty($filter["from"])) { 
                        $select->where("main.created_at >= ?",  $filter["from"]);
                  }
                  if (!empty($filter["to"])) {
                        $select->where("main.created_at <= ?",  $filter["to"]);
                  }
              }else{

                if (!empty($filter["from"])) {
                        $select->where("main.delivery_date >= ?",  $filter["from"]);
                  }
                  if (!empty($filter["to"])) {
                        $select->where("main.delivery_date <= ?",  $filter["to"]);
                  }

              }
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

          $select->where("main.value_id = ?", $value_id); 

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
      
            if (array_key_exists("search", $params)) {
               $term = $params["search"];
              $select->where("main.order_number LIKE '%$term%' OR main.customer_firstname LIKE '%$term%' OR main.customer_postcode LIKE '%$term%'  OR main.customer_phone LIKE '%$term%'  OR main.customer_email LIKE '%$term%'");
            }

            if (array_key_exists("filter", $params) && !empty($params["filter"])) {    
              $filter = $params['filter'];

              if(!empty($filter["date_type"]) && $filter["date_type"] == 'booking_date'){
                  if (!empty($filter["from"])) { 
                        $select->where("main.created_at >= ?",  $filter["from"]);
                  }
                  if (!empty($filter["to"])) {
                        $select->where("main.created_at <= ?",  $filter["to"]);
                  }
              }else{

                if (!empty($filter["from"])) {
                        $select->where("main.delivery_date >= ?",  $filter["from"]);
                  }
                  if (!empty($filter["to"])) {
                        $select->where("main.delivery_date <= ?",  $filter["to"]);
                  }

              }
          }
 
        return $this->_db->fetchCol($select);
    }
}