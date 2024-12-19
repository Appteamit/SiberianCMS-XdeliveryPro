<?php
class Xdelivery_Public_OrderController extends Xdelivery_Controller_Default
{
    public function getorderAction()
    {                                                    
        // IF thei is a valid request
        if ($datas = $this->getRequest()->getQuery()) {            
            try {
                $query_string = implode('&', array_map(
                    function ($v, $k) { return $k . '=' . $v; }, 
                    $datas, 
                    array_keys($datas)
                ));
                // Get IP Address
                $ipaddress = '';
                if (getenv('HTTP_CLIENT_IP'))
                    $ipaddress = getenv('HTTP_CLIENT_IP');
                else if(getenv('HTTP_X_FORWARDED_FOR'))
                    $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
                else if(getenv('HTTP_X_FORWARDED'))
                    $ipaddress = getenv('HTTP_X_FORWARDED');
                else if(getenv('HTTP_FORWARDED_FOR'))
                    $ipaddress = getenv('HTTP_FORWARDED_FOR');
                else if(getenv('HTTP_FORWARDED'))
                $ipaddress = getenv('HTTP_FORWARDED');
                else if(getenv('REMOTE_ADDR'))
                    $ipaddress = getenv('REMOTE_ADDR');
                else
                    $ipaddress = 'UNKNOWN';
                    Xdelivery_Model_Db_Table_PrinterLogs::saveLog([
                        'query_string' => $query_string,
                        'call_type' => 'Printer Call Record(IP: '.$ipaddress.').',
                        'log_type' => 1
                    ]);                 
                // Local Variables
                $order = new Xdelivery_Model_Orders();
				$product = new Xdelivery_Model_OrderItems();
                $owner_object = new Xdelivery_Model_Admins();                
                $is_printed = false;                
                $is_printer_new = false;
                // Validate Username and Password of printer
                $unique_key = trim($datas['u']);
                $password = trim($datas['p']);
                $printer = new Xdelivery_Model_Printers();                
                $printer->find(['printer_username'=>$unique_key]);                              
                if($printer->getPrinterId()) {                   
					$start_fetching_orders_from = $printer->getStartFetchingOrdersFrom();                					
					$owners = $owner_object->findAll(['value_id = ?' => $printer->getValueId(),]);                  
					if (!count($owners)) {echo ''; exit;} // No owner found Exit                    
                    // Fetch order as per Store ID and Fetching date, Only Processing Orders
                    $start_fetching_orders_from = (new DateTime($start_fetching_orders_from))->format('Y-m-d');
                    $order_array = $order->find([
                        'value_id' => $printer->getValueId(),
                        'store_id' => $printer->getStoreId(),
                        // 'DATE(created_at) >= ?' => '2024-01-01',
                        'status' => 'processing'
                    ], 'id ASC');
                    $order_status = $order_array->getData()['status'];
                    
                    if($order_array->getId()) { //Order found                                 
                        // Fetch Products of the Order
                        $products = $product->findAll(['order_id = ?' => $order_array->getId()]);
                        $have_prod = false;
                        foreach($products as $product_i) {
                            if($product_i->getOrderId()){
                                $have_prod = true;                            
                            }
                        }
						$total_products = $total_options = 0;
                        if($have_prod) {//Products found
                            $address = $order_array->getCustomerStreet() ? $order_array->getCustomerStreet().',' : '';
                            $address .= $order_array->getCustomerCity() ? $order_array->getCustomerCity() : '';
                            $address .= $order_array->getCustomerPostcode() ? $order_array->getCustomerPostcode() : '';
                            $phone = $order_array->getCustomerPhone() ? $order_array->getCustomerPhone() : '';

                            $order_products_array = [];
                            foreach($products as $product_obj) {
                                $order_choices_array=[];							                                    
                               
								if (!is_null($product_obj->getChoices()) && count(json_decode($product_obj->getChoices(),true))) {
									$choices_data = [];
									foreach (json_decode($product_obj->getChoices(),true) as $id => $choice) {											
                                        $order_choices_array[]=[
                                            "opt_name" => $choice['name'],
                                            "opt_quantity" => 1,
                                            "opt_price" => $printer->getCurrencyCode(). number_format((float)$choice['price'], 2, '.', '')
                                        ];
									}								
								}	                                

                                $order_products_array[]=[
                                        "item_title" => $product_obj->getName(),
                                        "item_quantity" => $product_obj->getQty(),
                                        "item_name" => $product_obj->getName(),
                                        "item_price" => $printer->getCurrencyCode().$product_price,
                                        "item_opt" => $order_choices_array
                                    ];                                
							}
                            // Prepare the order data response
                            $order_data = [
                                "create_time" => $order_array->getCreatedAt(), // Use current timestamp or dynamic value
                                "id" => $order_array->getId(), // Replace with dynamic or incoming ID
                                // "id" => 100, //only for test purpose
                                "products" =>  $order_products_array,
                                "discount" =>  $order_array->getDiscountAmount(),
                                "total" =>  $order_array->getTotalAmount(),
                                "payment_status" =>  "Pending",
                                "cust_name" => ucfirst($order_array->getCustomerFirstname())." ".ucfirst($order_array->getCustomerLastname()),
                                "cust_addr" => $address,
                                "cust_phone" =>  $phone,
                                "delivery_type" => ucfirst($order_array->getDeliveryMethod()), // Default to 'Delivery'
                                "cust_ins" => $order_array->getNotes(),
                                "comment" =>  "",
                            ];

                        // Convert the response data to JSON
                        $json_response = json_encode($order_data);

                        // Set headers
                        header("HTTP/1.1 200 OK");
                        header("Content-Type: application/json; charset=UTF-8");
                        header("Content-Length: " . strlen($json_response));

                        // Send the JSON response
                        echo $json_response;
                        exit;

                        } else { //No product found
                            Xdelivery_Model_Db_Table_PrinterLogs::saveLog([
                                'app_id' => $printer->getAppId(),
                                'value_id' => $printer->getValueId(),
                                'order_id' => 1,
                                'printer_username' => $printer->getPrinterUsername(),
                                'query_string' => $query_string,
                                'call_type' => 'No product found..',
                                'status' => 'canceled',
                                'log_type' => 1,
                            ]);
                            
                            echo '';
                            exit;
                        }
                    } else { //No order found                       
                        Xdelivery_Model_Db_Table_PrinterLogs::saveLog([
                            'app_id' => $printer->getAppId(),
                            'value_id' => $printer->getValueId(),
                            'order_id' => 0,
                            'printer_username' => $printer->getPrinterUsername(),
                            'query_string' => $query_string,
                            'call_type' => 'No order found..'.$start_fetching_orders_from,
                            'status' => 'canceled',
                            'log_type' => 1,
                        ]);                        
                        echo '';
                        exit;
                    }
                } else {
                    Xdelivery_Model_Db_Table_PrinterLogs::saveLog([
                        'call_type' => 'Prtiner validation failed.',
                        'status' => 'canceled',
                        'log_type' => 1,
                    ]);
                    echo '';
                    exit;
                }
            } catch (\Exception $e) {                
                Xdelivery_Model_Db_Table_PrinterLogs::saveLog([
                    'query_string' => $query_string,
                    'call_type' =>  $e,
                    'status' => 'canceled',
                    'log_type' => 1,
                ]);
                
                echo '';
                exit;
            }
        } else {
            Xdelivery_Model_Db_Table_PrinterLogs::saveLog([
                'call_type' => 'No query string was present.',
                'status' => 'canceled',
                'log_type' => 1,
            ]);
            echo '';
            exit;
        }
        /// start get order process       
    }
    // a=31&o=31_233&ak=Accepted&m=OK&dt=00:48&dd=230824&u=123456&bat=33&p=12345678&cur=0048542308
    public function updateorderstatusAction()
    {           
        if ($datas = $this->getRequest()->getQuery()) {            
            try {
                $printer = new Xdelivery_Model_Printers();
                $order = new Xdelivery_Model_Orders();
                $status = '';
                $printer_pdt = '';
                $log_called_by = 'Printer';

                $query_string = implode('&', array_map(
                    function ($v, $k) { return $k . '=' . $v; }, 
                    $datas, 
                    array_keys($datas)
                ));                               

                $store_id = $datas['a'];
                $unique_key = trim($datas['u']);
                $password = trim($datas['p']);
                $custom_order_id = trim($datas['o']);

                if($datas['ak'] && in_array($datas['ak'], ['rejected', 'Rejected'])) {
                    $status = 'rejected';
                }
                if($datas['dt']) {
                    $printer_pdt = $datas['dt'];	
                }                                
                
                $printer->find(['printer_username' => $unique_key,]);
                
                if($printer->getId()) {                										
                    $order->find([
                        'value_id' => $printer->getValueId(),
                        'store_id' => $printer->getStoreId(),
                        'id' => $custom_order_id,
                    ]);
                    if($order->getId()) {                   
                        if(($datas['log_called_by'] && $datas['pdt'] && $datas['pdt'] != date('H:i',$order->getDeliverytime())) || ($printer_pdt && $printer_pdt != date('H:i',$order->getDeliverytime() && date($printer_pdt) > date('H:i',$order->getDeliverytime())))) { 
								$order->addData([
									'order_deliverytime' =>  date("Y-m-d H:i:s",strtotime($printer_pdt)),
	                                'id' => $custom_order_id
								])->save();
                        }
                        if($status == "rejected") {
                            $status = "Rejected";
                            Xdelivery_Model_Db_Table_PrinterLogs::saveLog([
                                'app_id' => $printer->getAppId(),    
                                'value_id' => $printer->getValueId(),    
                                'order_id' => $order->getId(),
                                'printer_username' => $printer->getPrinterUsername(),        
                                'query_string' => $query_string,        
                                'call_type' => 'Call back.',        
                                'status' => 'Rejected',        
                                'log_type' => 1,
                            ]);
                                
                            $order->addData([
                                'order_response_time' => date('Y-m-d H:i:s'),
                                'status' => 'canceled',
                                'id' => $custom_order_id
                            ])->save();
                        } else {
                            $status = "Confirmed";
                            Xdelivery_Model_Db_Table_PrinterLogs::saveLog([
                                'app_id' => $printer->getAppId(),
                                'value_id' => $printer->getValueId(),
                                'order_id' => $order->getId(),
                                'printer_username' => $printer->getPrinterUsername(),
                                'query_string' => $query_string,
                                'call_type' => 'Call back.',
                                'status' => $status,
                                'log_type' => 1,
                            ]);
                            $order->addData([
                                'order_response_time' => date('Y-m-d H:i:s'),
								'status' => 'accepted',
                                'id' => $custom_order_id
                            ])->save();
                        }
                        echo '';
                        exit;
                    } else {
                        Xdelivery_Model_Db_Table_PrinterLogs::saveLog([
                            'app_id' => $printer->getAppId(),
                            'value_id' => $printer->getValueId(),
                            'order_id' => $order->getId(),
                            'printer_username' => $printer->getPrinterUsername(),
                            'query_string' => $query_string,
                            'call_type' => 'Call back. Wrong order number',
                            'status' => 'cancled',
                            'log_type' => 1,
                        ]);
                        echo '';
                        exit;
                    }
                } else {
                    throw new Exception();
                }
            } catch (\Exception $e) {
                dd($e);
                Xdelivery_Model_Db_Table_PrinterLogs::saveLog([
                   'order_id' => 1,
                    'query_string' => $query_string,
                    'log_call_type' => 'Invalid printer credentials.',
                    'log_type' => 2
                ]);
                echo '';
                exit;
            }
        } else {
            Xdelivery_Model_Db_Table_PrinterLogs::saveLog([
                'order_id' => 1,
                 'log_call_type' => 'No query string was present',
                 'log_type' => 2
             ]);
            echo '';
            exit;
        }
    }
	private function _error( $error ) {
		
		if ( $this->_isJson( $error ) ) {
			$error = json_decode( $error );
		}
		
		return $this->_output( __($error) , null );
	}
	
	private function _data( $data ) {
		
		if ( $this->_isJson( $data ) ) {
			$data = json_decode( $data );
		}
		
		return $this->_output( null , $data );
	}
	
	private function _output( $error , $data ) {
		
		header( 'Content-Type: application/json' );
		
		echo json_encode( [ 'error' => $error , 'data' => $data ] , JSON_PRETTY_PRINT );
	}
	
	private function _isJson( $string ) {
		
		if ( is_string( $string ) ) {
			json_decode( $string );
			
			return ( json_last_error() == JSON_ERROR_NONE );
		}
		
		return false;
	}
	private function _substrWords( $text, $maxchar , $end = '...' ) {
		if(strlen($text) > $maxchar || $text == '') {
			$words = preg_split('/\s/', $text);      
			$output = '';
			$i      = 0;
			while (1) {
				$length = strlen($output)+strlen($words[$i]);
				if ($length > $maxchar) {
					break;
				} else {
					$output .= " " . $words[$i];
					++$i;
				}
			}
			$output .= $end;
		} else {
			$output = $text;
        }
        
		return $output;
    }
	private function _datesToMinutes( $date1 = "", $date2 = "" ) {
        $datetime1 = strtotime($date1);
        $datetime2 = strtotime($date2);
        $interval  = abs($datetime2 - $datetime1);
        return round($interval / 60);
	}
}
