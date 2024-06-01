<?php

class Xdelivery_Model_Stats extends Core_Model_Default
{
    public function __construct($datas = array())
    {
        parent::__construct($datas);
        $this->_db_table = 'Xdelivery_Model_Db_Table_Stats'; //Db Model Name
    }    
    public function setIntervals()
    {
        return $this->getTable()->setIntervals();
    }
    // Sales
    public function totalGraphSalesGnl($value_id = 0,$from_date='',$to_date='',$label_formate='',$group_by_foramt='',$store_id=0)
    {
      return $this->getTable()->totalGraphSalesGnl($value_id,$from_date,$to_date,$label_formate,$group_by_foramt,$store_id);
    }
    public function countTotalSales($value_id = 0,$from_date='',$to_date='',$store_id=0)
    {
      return $this->getTable()->countTotalSales($value_id,$from_date,$to_date,$store_id);
    }  
    // Orders
    public function totalGraphOrdersGnl($value_id = 0,$from_date='',$to_date='',$label_formate='',$group_by_foramt='',$store_id=0,$order_status='')
    {
      return $this->getTable()->totalGraphOrdersGnl($value_id,$from_date,$to_date,$label_formate,$group_by_foramt,$store_id,$order_status);
    }
    public function countTotalOrders($value_id = 0,$from_date='',$to_date='',$store_id=0,$order_status='')
    {
      return $this->getTable()->countTotalOrders($value_id,$from_date,$to_date,$store_id,$order_status);
    }  
    // Coupons
    public function totalGraphCouponsGnl($value_id = 0,$from_date='',$to_date='',$label_formate='',$group_by_foramt='',$store_id=0,$coupon_id=0)
    {
      return $this->getTable()->totalGraphCouponsGnl($value_id,$from_date,$to_date,$label_formate,$group_by_foramt,$store_id,$coupon_id);
    }
    public function countTotalCoupons($value_id = 0,$from_date='',$to_date='',$store_id=0,$coupon_id=0)
    {
      return $this->getTable()->countTotalCoupons($value_id,$from_date,$to_date,$store_id,$coupon_id);
    }  
    // Tax
    public function totalGraphTaxGnl($value_id = 0,$from_date='',$to_date='',$label_formate='',$group_by_foramt='',$store_id=0,$tax_id=0)
    {
      return $this->getTable()->totalGraphTaxGnl($value_id,$from_date,$to_date,$label_formate,$group_by_foramt,$store_id,$tax_id);
    }
    public function countTotalTax($value_id = 0,$from_date='',$to_date='',$store_id=0,$tax_id=0)
    {
      return $this->getTable()->countTotalTax($value_id,$from_date,$to_date,$store_id,$tax_id);
    }  
    // Products
    public function topProducts($value_id = 0,$from_date='',$to_date='',$store_id=0,$report_type=0)
    {
      return $this->getTable()->topProducts($value_id,$from_date,$to_date,$store_id,$report_type);
    }
    public function topProductsCount($value_id = 0,$from_date='',$to_date='',$store_id=0,$report_type=0)
    {
      return $this->getTable()->topProductsCount($value_id,$from_date,$to_date,$store_id,$report_type);
    }
    // Customers
    public function topCustomers($value_id = 0,$from_date='',$to_date='',$store_id=0,$report_type=0)
    {
      return $this->getTable()->topCustomers($value_id,$from_date,$to_date,$store_id,$report_type);
    }
    public function topCustomersCount($value_id = 0,$from_date='',$to_date='',$store_id=0,$report_type=0)
    {
      return $this->getTable()->topCustomersCount($value_id,$from_date,$to_date,$store_id,$report_type);
    }
}
