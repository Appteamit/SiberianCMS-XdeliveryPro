<?php

class Xdelivery_Model_Db_Table_OrderItems extends Core_Model_Db_Table {
    protected $_name                    = "xdelivery_order_items";
    protected $_primary                 = "id";

        /**
     * @param $order_id
     * @param int $limit
     * @return array
     */
    public function findItemByOrderId($order_id, $params = []) {
        $select = $this->_db->select()
            ->from(['main' => $this->_name], [
                "id",
                "order_id",
                "product_id",
                "name",
                "base_price",
                "base_price_incl_tax",
                "choice_price",
                "price",
                "price_incl_tax",
                "qty",
                "total",
                "total_incl_tax",
                "choices",
                "options",
                "tax_id",
                "tax_rate",
                "tax_amount"
            ]);

			$select->joinLeft(['p' => 'xdelivery_products'], 'main.product_id = p.id', ['p.sku']);         
          
            $select->where("main.order_id = ?", $order_id);
           
            return $this->toModelClass($this->_db->fetchAll($select))->toArray();
        }
}