<?php
class Xdelivery_StatsController extends Application_Controller_Default{
    // Pages Initilizations
    public function salesAction(){
        $application = $this->getApplication();
        $this->loadPartials();
    }
    public function ordersAction(){
        $application = $this->getApplication();
        $this->loadPartials();
    }
    public function productsAction(){
        $application = $this->getApplication();
        $this->loadPartials();
    }
    public function customersAction(){
        $application = $this->getApplication();
        $this->loadPartials();
    }
    public function couponsAction(){
        $application = $this->getApplication();
        $this->loadPartials();
    }
    public function taxesAction(){
        $application = $this->getApplication();
        $this->loadPartials();
    }
    // Sales Stats
    public function salesstatsAction() {
        if ($data = $this->getRequest()->getPost()) {
            try {                
                    $value_id =  $data['value_id'];                
                    $range_filter =  $data['range_filter'];   
                    $store_id =  $data['store_id'];                                                                                                               
                    $date_range             = $data["date_range_params"];                                                                                                                                
                    $stats_obj           = new Xdelivery_Model_Stats();                                    
                    $intervas            = $stats_obj->setIntervals();//this method enures that intervals exist                                                                        
                    $params              = $this->formateStatParams($range_filter,$date_range['start'],$date_range['end']);
                   
                    // // **********START SECTION 1  (Totals Sales)*******//                     
                    if (true) {// SubSection 1.1 (Total Graph Sales)                                                                    
                        $total_sales_graph = $stats_obj->totalGraphSalesGnl($value_id,$params['from_date'],$params['to_date'],$params['label_formate'],$params['group_by_foramt'],$store_id);                        
                    }
                    if (true) {// SubSection 1.1 (Total Count Sales)                                                                    
                        $total_sales_count = $stats_obj->countTotalSales($value_id,$params['from_date'],$params['to_date'],$store_id);                                                
                    }                                                           
                    $html = [
                        'success' => true,
                        'total_graph_sales' => $total_sales_graph,
                        'total_sales_count' => $total_sales_count,                        
                    ];
                } catch (Exception $e) {
                    $html = [
                        'error' => true,
                        'message' => $e->getMessage(),
                        'message_button' => 1,                    
                        'message_loader' => 1
                    ];
                }
                $this->getLayout()->setHtml(Zend_Json::encode($html));
            }
    }  
    // Order Stats
    public function orderstatsAction() {
        if ($data = $this->getRequest()->getPost()) {
            try {                
                    $value_id =  $data['value_id'];                
                    $range_filter =  $data['range_filter'];   
                    $date_range             = $data["date_range_params"];                                                                                                                               
                    $store_id =  $data['store_id'];                                                                                                               
                    $order_status =  $data['order_status'];                                                                                                               
                    $stats_obj           = new Xdelivery_Model_Stats();                                    
                    $params              = $this->formateStatParams($range_filter,$date_range['start'],$date_range['end']);                                                       
                    if (true) {// SubSection 1.1 (Total Graph Orders)                                                                    
                        $total_orders_graph = $stats_obj->totalGraphOrdersGnl($value_id,$params['from_date'],$params['to_date'],$params['label_formate'],$params['group_by_foramt'],$store_id,$order_status);                        
                    }
                    if (true) {// SubSection 1.1 (Total Count Orders)                                                                    
                        $total_orders_count = $stats_obj->countTotalOrders($value_id,$params['from_date'],$params['to_date'],$store_id,$order_status);                                                
                    }                                                           
                    $html = [
                        'success' => true,
                        'total_graph_orders' => $total_orders_graph,
                        'total_orders_count' => $total_orders_count,                        
                    ];
                } catch (Exception $e) {
                    $html = [
                        'error' => true,
                        'message' => $e->getMessage(),
                        'message_button' => 1,                    
                        'message_loader' => 1
                    ];
                }
                $this->getLayout()->setHtml(Zend_Json::encode($html));
            }
    }      
    // Coupon Stats
    public function couponstatsAction() {
        if ($data = $this->getRequest()->getPost()) {
            try {                
                    $value_id =  $data['value_id'];                
                    $range_filter =  $data['range_filter'];   
                    $date_range = $data["date_range_params"];                                                                                                                               
                    $store_id =  $data['store_id'];                                                                                                               
                    $coupon_id =  $data['coupon_id'];                                                                                                               
                    $stats_obj           = new Xdelivery_Model_Stats();                                    
                    $params              = $this->formateStatParams($range_filter,$date_range['start'],$date_range['end']);                                                       
                    if (true) {// SubSection 1.1 (Total Graph coupons)                                                                    
                        $total_coupons_graph = $stats_obj->totalGraphCouponsGnl($value_id,$params['from_date'],$params['to_date'],$params['label_formate'],$params['group_by_foramt'],$store_id,$coupon_id);                        
                    }
                    if (true) {// SubSection 1.1 (Total Count coupons)                                                                    
                        $total_coupons_count = $stats_obj->countTotalCoupons($value_id,$params['from_date'],$params['to_date'],$store_id,$coupon_id);                                                
                    }                                                           
                    $html = [
                        'success' => true,
                        'total_graph_coupons' => $total_coupons_graph,
                        'total_coupons_count' => $total_coupons_count,                        
                    ];
                } catch (Exception $e) {
                    $html = [
                        'error' => true,
                        'message' => $e->getMessage(),
                        'message_button' => 1,                    
                        'message_loader' => 1
                    ];
                }
                $this->getLayout()->setHtml(Zend_Json::encode($html));
            }
    }      
    // Tax Stats
    public function taxstatsAction() {
        if ($data = $this->getRequest()->getPost()) {
            try {                
                    $value_id =  $data['value_id'];                
                    $range_filter =  $data['range_filter'];   
                    $date_range = $data["date_range_params"];                                                                                                                               
                    $store_id =  $data['store_id'];                                                                                                               
                    $tax_id =  $data['tax_id'];                                                                                                               
                    $stats_obj           = new Xdelivery_Model_Stats();                                    
                    $params              = $this->formateStatParams($range_filter,$date_range['start'],$date_range['end']);                                                       
                    if (true) {// SubSection 1.1 (Total Graph tax)                                                                    
                        $total_tax_graph = $stats_obj->totalGraphTaxGnl($value_id,$params['from_date'],$params['to_date'],$params['label_formate'],$params['group_by_foramt'],$store_id,$tax_id);                        
                    }
                    if (true) {// SubSection 1.1 (Total Count tax)                                                                    
                        $total_tax_count = $stats_obj->countTotalTax($value_id,$params['from_date'],$params['to_date'],$store_id,$tax_id);                                                
                    }                                                           
                    $html = [
                        'success' => true,
                        'total_graph_tax' => $total_tax_graph,
                        'total_tax_count' => $total_tax_count,                        
                    ];
                } catch (Exception $e) {
                    $html = [
                        'error' => true,
                        'message' => $e->getMessage(),
                        'message_button' => 1,                    
                        'message_loader' => 1
                    ];
                }
                $this->getLayout()->setHtml(Zend_Json::encode($html));
            }
    }      
    // Product Stats    
    public function productcountstatsAction() {
        if ($data = $this->getRequest()->getPost()) {
            try {                
                    $value_id =  $data['value_id'];                
                    $range_filter =  $data['range_filter'];   
                    $date_range             = $data["date_range_params"];                                                                                                                               
                    $store_id =  $data['store_id'];                                                                                                                                                                                                                                                
                    $report_type =  $data['report_type'];                                                                                                                                                                                                                                                
                    $stats_obj           = new Xdelivery_Model_Stats();                                    
                    $params              = $this->formateStatParams($range_filter,$date_range['start'],$date_range['end']);                                                                           
                    if (true) {// SubSection 1.1 (Total Count Orders)                                                                    
                        $total_products_count = $stats_obj->topProductsCount($value_id,$params['from_date'],$params['to_date'],$store_id,$report_type);                                                
                    }                                                           
                    $html = [
                        'success' => true,                        
                        'total_products_count' => $total_products_count,                        
                    ];
                } catch (Exception $e) {
                    $html = [
                        'error' => true,
                        'message' => $e->getMessage(),
                        'message_button' => 1,                    
                        'message_loader' => 1
                    ];
                }
                $this->getLayout()->setHtml(Zend_Json::encode($html));
            }
    }      
    public function productstatsAction()
    {
      if ($data = $this->getRequest()->getQuery()) {
        $value_id          =  $data['value_id'];                
        $range_filter    =  $data['range_filter'];                                                   
        //START  Custom Date
        $date_range = $data["date_range_params"];        
        $store_id =  $data['store_id'];                                                                                                                       
        $report_type =  $data['report_type'];//1 for based on revenue, 2 for based on quantity                                                                                                                       
        // END Custom Date
        $stats_obj           = new Xdelivery_Model_Stats();                                    
        $params              = $this->formateStatParams($range_filter,$data['date_range_params_start'],$data['date_range_params_end']);                                                       
        if (true) {// SubSection 1.1 (Top 10 Revenuve Products)                                                                    
            $top_revenue_products = $stats_obj->topProducts($value_id,$params['from_date'],$params['to_date'],$store_id,$report_type);                        
        }        

        $ajax_responce = [];       
        foreach ($top_revenue_products as $key => $value) {
            $ajax_responce[] = [
                $value['id'],     
                $value['product_name'],     
                $value['total_revenue'],     
                $value['total_count'],     
                $value['total_tax']
                ];
        } 
        $fin_res = [
            "data"=>$ajax_responce,                      
            "top_revenue_products"=>$top_revenue_products
        ];
        $this->_sendJson($fin_res);
      }
    }   
    // Product Stats    
    public function customercountstatsAction() {
        if ($data = $this->getRequest()->getPost()) {
            try {                
                    $value_id =  $data['value_id'];                
                    $range_filter =  $data['range_filter'];   
                    $date_range = $data["date_range_params"];                                                                                                                               
                    $store_id =  $data['store_id'];                                                                                                                                                                                                                                                
                    $report_type =  $data['report_type'];                                                                                                                                                                                                                                                
                    $stats_obj = new Xdelivery_Model_Stats();                                    
                    $params = $this->formateStatParams($range_filter,$date_range['start'],$date_range['end']);                                                                           
                    if (true) {// SubSection 1.1 (Total Count Orders)                                                                    
                        $total_customers_count = $stats_obj->topCustomersCount($value_id,$params['from_date'],$params['to_date'],$store_id,$report_type);                                                
                    }                                                           
                    $html = [
                        'success' => true,                        
                        'total_customers_count' => $total_customers_count,                        
                    ];
                } catch (Exception $e) {
                    $html = [
                        'error' => true,
                        'message' => $e->getMessage(),
                        'message_button' => 1,                    
                        'message_loader' => 1
                    ];
                }
                $this->getLayout()->setHtml(Zend_Json::encode($html));
            }
    }      
    public function customerstatsAction()
    {
      if ($data = $this->getRequest()->getQuery()) {
        $value_id          =  $data['value_id'];                
        $range_filter    =  $data['range_filter'];                                                   
        //START  Custom Date
        $date_range = $data["date_range_params"];        
        $store_id =  $data['store_id'];                                                                                                                       
        $report_type =  $data['report_type'];//1 for based on revenue, 2 for based on quantity                                                                                                                       
        // END Custom Date
        $stats_obj           = new Xdelivery_Model_Stats();                                    
        $params              = $this->formateStatParams($range_filter,$data['date_range_params_start'],$data['date_range_params_end']);                                                       
        if (true) {// SubSection 1.1                                                                
            $top_customers = $stats_obj->topCustomers($value_id,$params['from_date'],$params['to_date'],$store_id,$report_type);                        
        }        

        $ajax_responce = [];       
        foreach ($top_customers as $key => $value) {
            $ajax_responce[] = [
                $value['customer_id'],     
                $value['firstname'].' '.$value['lastname'],     
                $value['total_revenue'],     
                $value['total_count'],     
                $value['total_tax'],     
                $value['total_tips']     
                ];
        } 
        $fin_res = [
            "data"=>$ajax_responce                               
        ];
        $this->_sendJson($fin_res);
      }
    }   
    // Util Methods
    private function formateStatParams($range_filter,$date_range_start,$date_range_end)
    {                    
        $from_date='';
        $to_date='';
        $label_formate='';
        $group_by_foramt='';
        $days_diff_custom_date=0;
        switch ($range_filter) {
                    case 1://1 day or today
                        $from_date=date("Y-m-d", strtotime("-1 day"));
                        $to_date=date('Y-m-d', strtotime(' +1 day'));
                        $var_from_date=date("Y-m-d", strtotime("-2 day"));
                        $var_to_date=date("Y-m-d", strtotime("-1 day"));
                        $label_formate="MIN(setdate) AS labels,";
                        $group_by_foramt="GROUP BY DATEDIFF(setdate, "."'$from_date'".") DIV 1";
                        break;                    
                    case 2://1 day or today
                        $from_date=date("Y-m-d", strtotime("-7 day"));
                        $to_date=date('Y-m-d', strtotime(' +7 day'));
                        $var_from_date=date("Y-m-d", strtotime("-14 day"));
                        $var_to_date=date("Y-m-d", strtotime("-7 day"));
                        $label_formate="MIN(setdate) AS labels,";
                        $group_by_foramt="GROUP BY DATEDIFF(setdate, "."'$from_date'".") DIV 1";
                        break; 
                    case 3: //3 Month
                        $from_date=date("Y-m-d", strtotime("-1 months"));
                        $to_date=date('Y-m-d', strtotime(' +1 day'));
                        $var_from_date=date("Y-m-d", strtotime("-2 months"));
                        $var_to_date=date("Y-m-d", strtotime("-1 months"));
                        $label_formate="DATE_FORMAT(xdelivery_stats_interval.setdate,'%Y-%m-%d') AS labels,";
                        $group_by_foramt="GROUP BY DATE_FORMAT(xdelivery_stats_interval.setdate,'%Y-%M-%d')";
                        break;                    
                    case 4://3 month
                        $from_date=date("Y-m-d", strtotime("-3 months"));
                        $to_date=date('Y-m-d', strtotime(' +1 day'));
                        $var_from_date=date("Y-m-d", strtotime("-6 months"));
                        $var_to_date=date("Y-m-d", strtotime("-3 months"));
                        $label_formate="MIN(setdate) AS labels,";
                        $group_by_foramt="GROUP BY DATEDIFF(setdate, "."'$from_date'".") DIV 7";
                        break;                                                                                                  
                    // case 4://current Year
                    //     $from_date= date("Y").'-01-01';                        
                    //     $to_date=date('Y-m-d', strtotime(' +1 day'));
                    //     $var_from_date=date("Y-m-d", strtotime("-2 months"));
                    //     $var_to_date=date("Y").'-01-01';                 
                    //     $last_visit_date = strtotime($from_date);
                    //     $last_visit_days = time() - $last_visit_date;
                    //     $days_in_current_year = round($last_visit_days / (60 * 60 * 24));
                    //         if ($days_in_current_year<=30) {
                    //             $label_formate="DATE_FORMAT(xdelivery_stats_interval.setdate,'%Y-%m-%d') AS labels,";
                    //             $group_by_foramt="GROUP BY DATE_FORMAT(xdelivery_stats_interval.setdate,'%Y-%M-%d')";
                    //         } elseif($days_in_current_year>30 && $days_in_current_year<180) {
                    //             $label_formate="MIN(setdate) AS labels,";
                    //             $group_by_foramt="GROUP BY DATEDIFF(setdate, "."'$from_date'".") DIV 7";
                    //         }elseif($days_in_current_year>180) {
                    //             $label_formate="DATE_FORMAT(xdelivery_stats_interval.setdate,'%Y-%M') AS labels,";
                    //             $group_by_foramt="GROUP BY DATE_FORMAT(xdelivery_stats_interval.setdate,'%Y-%M')";
                    //         }                        
                    //     break;                    
                    // case 5://12 month
                    //     $from_date=date("Y-m-d", strtotime("-12 months"));
                    //     $to_date=date('Y-m-d', strtotime(' +1 day'));
                    //     $var_from_date=date("Y-m-d", strtotime("-24 months"));
                    //     $var_to_date=date("Y-m-d", strtotime("-12 months"));                
                    //     $label_formate="DATE_FORMAT(xdelivery_stats_interval.setdate,'%Y-%M') AS labels,";
                    //     $group_by_foramt="GROUP BY DATE_FORMAT(xdelivery_stats_interval.setdate,'%Y-%M')";
                    //     break;                    
                    // case 6://All (The creation date of Application to today)                                                
                    //     $from_date=date("Y-m-d", strtotime($this->getApplication()->getCreatedAt()));
                    //     $to_date=date('Y-m-d', strtotime(' +1 day'));
                    //     $var_from_date=date('Y-m-d', strtotime(' -2 day'));
                    //     $var_to_date=$from_date;
                    //     $last_visit_date = strtotime($from_date);
                    //     $last_visit_days = time() - $last_visit_date;
                    //     $days_in_current_year = round($last_visit_days / (60 * 60 * 24));                        
                    //         if ($days_in_current_year<=30) {
                    //             $label_formate="DATE_FORMAT(xdelivery_stats_interval.setdate,'%Y-%m-%d') AS labels,";
                    //             $group_by_foramt="GROUP BY DATE_FORMAT(xdelivery_stats_interval.setdate,'%Y-%M-%d')";
                    //         } elseif($days_in_current_year>30 && $days_in_current_year<180) {
                    //             $label_formate="MIN(setdate) AS labels,";
                    //             $group_by_foramt="GROUP BY DATEDIFF(setdate, "."'$from_date'".") DIV 7";
                    //         }elseif($days_in_current_year>180) {
                    //             $label_formate="DATE_FORMAT(xdelivery_stats_interval.setdate,'%Y-%M') AS labels,";
                    //             $group_by_foramt="GROUP BY DATE_FORMAT(xdelivery_stats_interval.setdate,'%Y-%M')";
                    //         }
                    //     break;                    
                    case 100://For custom date
                        $from_date  = date('Y-m-d', $date_range_start);
                        $from_date  = date('Y-m-d', strtotime("+1 days",strtotime($from_date)));
                        $to_date    = date('Y-m-d', $date_range_end);                                                                          
                        $datediff   = $date_range_end-$date_range_start;
                        $days_diff_custom_date  = round($datediff / (60 * 60 * 24));
                        $var_from_date=date('Y-m-d', strtotime("-".$days_diff_custom_date." days",strtotime($from_date)));
                        $var_to_date=$from_date;                                         
                        if ($days_diff_custom_date<=31) {
                            $label_formate="DATE_FORMAT(xdelivery_stats_interval.setdate,'%Y-%m-%d') AS labels,";
                            $group_by_foramt="GROUP BY DATE_FORMAT(xdelivery_stats_interval.setdate,'%Y-%M-%d')";
                        } elseif($days_diff_custom_date>31 && $days_diff_custom_date<181) {
                            $label_formate="MIN(setdate) AS labels,";
                            $group_by_foramt="GROUP BY DATEDIFF(setdate, "."'$from_date'".") DIV 7";
                        }elseif($days_diff_custom_date>181) {
                            $label_formate="DATE_FORMAT(xdelivery_stats_interval.setdate,'%Y-%M') AS labels,";
                            $group_by_foramt="GROUP BY DATE_FORMAT(xdelivery_stats_interval.setdate,'%Y-%M')";
                        }
                        break;                    
                }
                $data=[ 
                        'from_date'=>$from_date,
                        'to_date'=>$to_date,
                        'label_formate'=>$label_formate,
                        'group_by_foramt'=>$group_by_foramt,
                        'var_from_date'=>$var_from_date,
                        'var_to_date'=>$var_to_date,
                        'filter_type'=>$range_filter,
                        'days_diff_custom_date'=>$days_diff_custom_date,
                    ];
                return $data;
    }   
}
?>