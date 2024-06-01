<?php
class Xdelivery_Model_Db_Table_Stats extends Core_Model_Db_Table
{	
        public function setIntervals()
        {
                $query_option = "SELECT * FROM  xdelivery_stats_interval WHERE 1";
                $res_option   = $this->_db->fetchAll($query_option);        
                if (!count($res_option)) {
                        $start_date = "2018-08-01";
                        do {
                                $interval_date=date('Y-m-d', strtotime($start_date. ' + 1 days'));         
                                $start_date=$interval_date;                        
                                $data['setdate']=$start_date;
                                $this->_db->insert("xdelivery_stats_interval", $data);                
                        } while ($interval_date < '2030-12-31');
                }                        
        }
    // Sales
    public function totalGraphSalesGnl($value_id = 0,$from_date='',$to_date='',$label_formate='',$group_by_foramt='',$store_id=0)
	{
        $query_option = "SELECT ".$label_formate;
        $query_option .= "COALESCE(ROUND(SUM(xdelivery_orders.sub_amount), 2), 0) AS total_net_sales
                        FROM `xdelivery_stats_interval`
                        LEFT JOIN xdelivery_orders  ON xdelivery_orders.value_id=$value_id AND date(xdelivery_orders.created_at)=xdelivery_stats_interval.setdate";
        if ($store_id) {
            $query_option .=" AND xdelivery_orders.store_id=".$store_id;
        }
        $query_option .=" WHERE xdelivery_stats_interval.setdate>='$from_date' AND xdelivery_stats_interval.setdate<='$to_date' "; 
        $query_option .=$group_by_foramt;
        $query_option .=' ORDER BY xdelivery_stats_interval.setdate';
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
	}
    public function countTotalSales($value_id = 0,$from_date='',$to_date='',$store_id=0)
	{        
        $select = "SELECT 
        COUNT(`id`) AS total_count,
        ROUND(SUM(`sub_amount`), 2) AS total_gross,
        ROUND(SUM(`total_tax`), 2) AS total_tax,
        ROUND(SUM(`discount_amount`), 2) AS total_discount,
        ROUND(SUM(`tips_amount`), 2) AS total_tip,
        ROUND(SUM(`delivery_cost`), 2) AS total_delivery
        FROM `xdelivery_orders` 
        WHERE xdelivery_orders.value_id=$value_id
        AND DATE(xdelivery_orders.created_at) >= '$from_date'
        AND DATE(xdelivery_orders.created_at) <= '$to_date'";  
        if ($store_id) {
            $select .=" AND xdelivery_orders.store_id=".$store_id;
        }          
        return $this->_db->fetchAll($select);
	}
    // Orders
    public function totalGraphOrdersGnl($value_id = 0,$from_date='',$to_date='',$label_formate='',$group_by_foramt='',$store_id=0,$order_status='')
	{
        $query_option = "SELECT ".$label_formate;
        $query_option .= "COALESCE(ROUND(SUM(xdelivery_orders.sub_amount), 2), 0) AS total_net_orders
                        FROM `xdelivery_stats_interval`
                        LEFT JOIN xdelivery_orders  ON xdelivery_orders.value_id=$value_id AND date(xdelivery_orders.created_at)=xdelivery_stats_interval.setdate";
        if ($store_id) {
            $query_option .=" AND xdelivery_orders.store_id=".$store_id;
        }
        if ($order_status) {
            $query_option .=" AND xdelivery_orders.status="."'$order_status'";
        }
        $query_option .=" WHERE xdelivery_stats_interval.setdate>='$from_date' AND xdelivery_stats_interval.setdate<='$to_date' "; 
        $query_option .=$group_by_foramt;
        $query_option .=' ORDER BY xdelivery_stats_interval.setdate';
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
	}
    public function countTotalOrders($value_id = 0,$from_date='',$to_date='',$store_id=0,$order_status='')
	{        
        $select = "SELECT 
        COUNT(`id`) AS total_count,
        ROUND(SUM(`sub_amount`), 2) AS total_gross,
        ROUND(SUM(`sub_amount`) / COUNT(`id`), 2) AS average_gross,
        ROUND(SUM(`total_tax`), 2) AS total_tax,
        ROUND(SUM(`total_tax`) / COUNT(`id`), 2) AS average_tax,
        ROUND(SUM(`discount_amount`), 2) AS total_discount,
        ROUND(SUM(`discount_amount`) / COUNT(`id`), 2) AS average_discount,
        ROUND(SUM(`tips_amount`), 2) AS total_tip,
        ROUND(SUM(`tips_amount`) / COUNT(`id`), 2) AS average_tip,
        ROUND(SUM(`delivery_cost`), 2) AS total_delivery,
        ROUND(SUM(`delivery_cost`) / COUNT(`id`), 2) AS average_delivery
        FROM `xdelivery_orders` 
        WHERE xdelivery_orders.value_id = $value_id        
            AND DATE(xdelivery_orders.created_at) >= '$from_date'
            AND DATE(xdelivery_orders.created_at) <= '$to_date'";  
            if ($store_id) {
                $select .=" AND xdelivery_orders.store_id=".$store_id;
            }          
            if ($order_status) {
                $select .=" AND xdelivery_orders.status="."'$order_status'";
            }          
            return $this->_db->fetchAll($select);
	}
    // Coupons
    public function totalGraphCouponsGnl($value_id = 0,$from_date='',$to_date='',$label_formate='',$group_by_foramt='',$store_id=0,$coupon_id=0)
	{
        $query_option = "SELECT ".$label_formate;
        $query_option .= "COALESCE(ROUND(SUM(xdelivery_orders.discount_amount), 2), 0) AS total_redemption_value
                        FROM `xdelivery_stats_interval`
                        LEFT JOIN xdelivery_orders  ON xdelivery_orders.value_id=$value_id AND date(xdelivery_orders.created_at)=xdelivery_stats_interval.setdate";
        if ($store_id) {
            $query_option .=" AND xdelivery_orders.store_id=".$store_id;
        }
        if ($coupon_id) {
            $select .=" AND xdelivery_orders.promocode_id=".$coupon_id;
        }else {
            $select .=" AND xdelivery_orders.promocode_id!=0";                
        }  
        $query_option .=" WHERE xdelivery_stats_interval.setdate>='$from_date' AND xdelivery_stats_interval.setdate<='$to_date' "; 
        $query_option .=$group_by_foramt;
        $query_option .=' ORDER BY xdelivery_stats_interval.setdate';
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
	}
    public function countTotalCoupons($value_id = 0,$from_date='',$to_date='',$store_id=0,$coupon_id=0)
	{        
        $select = "SELECT 
        COUNT(DISTINCT xdelivery_orders.customer_id) AS total_unique_redeemers,
        COUNT(xdelivery_orders.promocode_id) AS total_redemptions,
        ROUND(SUM(xdelivery_orders.discount_amount), 2) AS total_redemption_value
        FROM `xdelivery_orders`    
        WHERE xdelivery_orders.value_id = $value_id        
        AND DATE(xdelivery_orders.created_at) >= '$from_date'
        AND DATE(xdelivery_orders.created_at) <= '$to_date'";  
        if ($store_id) {
            $select .=" AND xdelivery_orders.store_id=".$store_id;
        }          
        if ($coupon_id) {
            $select .=" AND xdelivery_orders.promocode_id=".$coupon_id;
        }else {
            $select .=" AND xdelivery_orders.promocode_id!=0";                
        }          
        return $this->_db->fetchAll($select);
	}
    // Tax
    public function totalGraphTaxGnl($value_id = 0,$from_date='',$to_date='',$label_formate='',$group_by_foramt='',$store_id=0,$tax_id=0)
	{
        $query_option = "SELECT ".$label_formate;
        $query_option .= "COALESCE(ROUND(SUM(xdelivery_order_items.tax_amount), 2), 0) AS total_tax_collection
                        FROM `xdelivery_stats_interval`
                        LEFT JOIN xdelivery_orders  ON xdelivery_orders.value_id=$value_id AND date(xdelivery_orders.created_at)=xdelivery_stats_interval.setdate
                        LEFT JOIN xdelivery_order_items ON xdelivery_order_items.order_id=xdelivery_orders.id";
        if ($store_id) {
            $query_option .=" AND xdelivery_orders.store_id=".$store_id;
        }
        if ($tax_id) {
            $query_option .=" AND xdelivery_order_items.tax_id=".$tax_id;
        }else {
            $query_option .=" AND xdelivery_order_items.tax_id!=0";                
        }   
        $query_option .=" WHERE xdelivery_stats_interval.setdate>='$from_date' AND xdelivery_stats_interval.setdate<='$to_date' "; 
        $query_option .=$group_by_foramt;
        $query_option .=' ORDER BY xdelivery_stats_interval.setdate';
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
	}
    public function countTotalTax($value_id = 0,$from_date='',$to_date='',$store_id=0,$tax_id=0)
	{        
        $select = "SELECT 
        COUNT(xdelivery_order_items.tax_id) AS total_taxed_transactions,
        ROUND(SUM(xdelivery_order_items.tax_amount), 2) AS total_taxed_collected,
        COUNT(DISTINCT xdelivery_orders.customer_id) AS total_unique_taxed
        FROM `xdelivery_orders`   
        JOIN xdelivery_order_items ON xdelivery_order_items.order_id=xdelivery_orders.id  
        WHERE xdelivery_orders.value_id = $value_id        
        AND DATE(xdelivery_orders.created_at) >= '$from_date'
        AND DATE(xdelivery_orders.created_at) <= '$to_date'";  
        if ($store_id) {
            $select .=" AND xdelivery_orders.store_id=".$store_id;
        }          
        if ($tax_id) {
            $select .=" AND xdelivery_order_items.tax_id=".$tax_id;
        }else {
            $select .=" AND xdelivery_order_items.tax_id!=0";                
        }          
        return $this->_db->fetchAll($select);
	}
    // Products
    public function topProductsCount($value_id = 0,$from_date='',$to_date='',$store_id=0,$report_type=0)
	{        
        $select = "SELECT                     
                ROUND(SUM(xdelivery_order_items.total), 2) AS total_revenue,
                COUNT(xdelivery_order_items.qty) AS total_count,
                ROUND(SUM(xdelivery_order_items.tax_amount), 2) AS total_tax
            FROM xdelivery_orders
            JOIN xdelivery_order_items ON xdelivery_orders.id=xdelivery_order_items.order_id
            JOIN xdelivery_products ON xdelivery_products.id=xdelivery_order_items.product_id         
            WHERE xdelivery_orders.value_id = $value_id        
            AND DATE(xdelivery_orders.created_at) >= '$from_date'
            AND DATE(xdelivery_orders.created_at) <= '$to_date'";

            if ($store_id) {
                $select .= " AND xdelivery_orders.store_id = ".$store_id;
            }

            if ($order_status) {
                $select .= " AND xdelivery_orders.status = '".$order_status."'";
            }
            if ($report_type==1) {
                $select .=" ORDER BY `total_revenue` DESC";     
            }elseif ($report_type==2) {
                $select .=" ORDER BY `total_count` DESC";     
            }else {
                return []; //Unknown type;
            }  
            $select .= " LIMIT 10"; 
  
            return $this->_db->fetchAll($select);
    }
    public function topProducts($value_id = 0,$from_date='',$to_date='',$store_id=0,$report_type=0)
	{        
        $select = "SELECT 
                    xdelivery_products.id,
                    xdelivery_products.product_name,
                     SUM(xdelivery_order_items.total) AS total_revenue,
                     COUNT(xdelivery_order_items.qty) AS total_count,
                     SUM(xdelivery_order_items.tax_amount) total_tax
                    FROM xdelivery_orders
                    JOIN xdelivery_order_items ON xdelivery_orders.id=xdelivery_order_items.order_id
                    JOIN xdelivery_products ON xdelivery_products.id=xdelivery_order_items.product_id         
            WHERE xdelivery_orders.value_id = $value_id        
            AND DATE(xdelivery_orders.created_at) >= '$from_date'
            AND DATE(xdelivery_orders.created_at) <= '$to_date'";  
            if ($store_id) {
                $select .=" AND xdelivery_orders.store_id=".$store_id;
            }          
            if ($order_status) {
                $select .=" AND xdelivery_orders.status="."'$order_status'";
            }          
            $select .=" GROUP BY xdelivery_products.id";
            if ($report_type==1) {
                $select .=" ORDER BY `total_revenue` DESC";     
            }elseif ($report_type==2) {
                $select .=" ORDER BY `total_count` DESC";     
            }else {
                return []; //Unknown type;
            }   
            $select .= " LIMIT 10";    
            return $this->_db->fetchAll($select);
    }
    public function topCustomersCount($value_id = 0,$from_date='',$to_date='',$store_id=0,$report_type=0)
	{        
        $select = "SELECT 
                    customer.customer_id,
                    customer.firstname,
                    customer.lastname,
                     ROUND(SUM(xdelivery_order_items.total),2) AS total_revenue,
                     COUNT(xdelivery_order_items.qty) AS total_count,
                     ROUND(SUM(xdelivery_order_items.tax_amount),2) total_tax,
                     ROUND(SUM(xdelivery_orders.tips_amount),2) total_tips
                    FROM xdelivery_orders
                    JOIN xdelivery_order_items ON xdelivery_orders.id=xdelivery_order_items.order_id
                    JOIN customer ON customer.customer_id=xdelivery_orders.customer_id         
            WHERE xdelivery_orders.value_id = $value_id        
            AND DATE(xdelivery_orders.created_at) >= '$from_date'
            AND DATE(xdelivery_orders.created_at) <= '$to_date'";  
            if ($store_id) {
                $select .=" AND xdelivery_orders.store_id=".$store_id;
            }          
            if ($order_status) {
                $select .=" AND xdelivery_orders.status="."'$order_status'";
            }                      
            if ($report_type==1) {
                $select .=" ORDER BY `total_revenue` DESC";     
            }elseif ($report_type==2) {
                $select .=" ORDER BY `total_count` DESC";     
            }else {
                return []; //Unknown type;
            }   
            $select .= " LIMIT 10";   
  
            return $this->_db->fetchAll($select);
    }
    public function topCustomers($value_id = 0,$from_date='',$to_date='',$store_id=0,$report_type=0)
	{        
        $select = "SELECT 
                    customer.customer_id,
                    customer.firstname,
                    customer.lastname,
                     SUM(xdelivery_order_items.total) AS total_revenue,
                     COUNT(xdelivery_order_items.qty) AS total_count,
                     SUM(xdelivery_order_items.tax_amount) total_tax,
                     SUM(xdelivery_orders.tips_amount) total_tips
                    FROM xdelivery_orders
                    JOIN xdelivery_order_items ON xdelivery_orders.id=xdelivery_order_items.order_id
                    JOIN customer ON customer.customer_id=xdelivery_orders.customer_id         
            WHERE xdelivery_orders.value_id = $value_id        
            AND DATE(xdelivery_orders.created_at) >= '$from_date'
            AND DATE(xdelivery_orders.created_at) <= '$to_date'";  
            if ($store_id) {
                $select .=" AND xdelivery_orders.store_id=".$store_id;
            }          
            if ($order_status) {
                $select .=" AND xdelivery_orders.status="."'$order_status'";
            }          
            $select .=" GROUP BY customer.customer_id";
            if ($report_type==1) {
                $select .=" ORDER BY `total_revenue` DESC";     
            }elseif ($report_type==2) {
                $select .=" ORDER BY `total_count` DESC";     
            }else {
                return []; //Unknown type;
            }   
            $select .= " LIMIT 10";    
            return $this->_db->fetchAll($select);
    }
}
