<?php
require "app/local/modules/Xdelivery/libs/vendor/autoload.php";
use Siberian\Exception; 
use Siberian\File;
use Siberian\Json;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use PaymentStripe\Model\Customer as PaymentStripeCustomer;
use PaymentStripe\Model\Currency as PaymentStripeCurrency;

/**
 * Class Xdelivery_Mobile_OrderController
 */
class Xdelivery_Mobile_OrderController extends Application_Controller_Mobile_Default {

    const SET_EXPRESS_CHECKOUT = 'SetExpressCheckout';
    private $__pay_url = "";

    public function findAllOrderAction() {              
         try {
            // print_r($this->getRequest()->getParam('value_id'));exit;
               if($value_id = $this->getRequest()->getParam('value_id')){
                    $customerId = $this->_getCustomerId(true);
                    $status = (new Xdelivery_Model_Orders)->getStatus();
                    $settingModel = (new Xdelivery_Model_Settings())->find(['value_id' => $value_id ]);                    
                    $settings = $settingModel->getData();
                    $currency = Core_Model_Language::getCurrencySymbol();
                    $params = $this->getRequest()->getParams();
                    
                    $orders = (new Xdelivery_Model_Orders())->findAllOrder($params);
                    // print_r($orders);exit;
                    $ordersJson = [];
                    foreach ($orders as $order) {
                        $data = $order->getData();
                        $order_status = $data['order_status'];  
            
                        $data['order_date']=  date($settings['date_format']. ' '. $settings['time_format'], strtotime($data['created_at']));
                        $data['order_status'] = $status[$data['order_status']];
                        $data['order_status']  = ucfirst(p__('xdelivery', $data['order_status']));
                        $data['currency'] = $currency;
                        $data['delivery_method'] = $data['delivery_method'] == 'delivery' ? p__('xdelivery', 'Home  Delivery') : p__('xdelivery', 'Pick-up');
                        $data['payment_status'] = $data['payment_status'] == 'success' ? p__('xdelivery', 'Paid') : p__('xdelivery', 'Unpaid');

                        $data['sub_amount_with_currency'] = Xdelivery_Model_Utility::displayPrice($data['sub_amount'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                        $data['total_tax_with_currency'] = Xdelivery_Model_Utility::displayPrice($data['total_tax'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                        $data['delivery_cost_with_currency'] = Xdelivery_Model_Utility::displayPrice($data['delivery_cost'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                        $data['discount_amount_with_currency'] = Xdelivery_Model_Utility::displayPrice($data['discount_amount'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                        $data['tips_amount_with_currency'] = Xdelivery_Model_Utility::displayPrice($data['tips_amount'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                        $data['total_amount_with_currency'] = Xdelivery_Model_Utility::displayPrice($data['total_amount'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                        $data['is_return_request'] = (integer) $data['is_return_request'];

                        if($data['is_return_request'] && $order_status != 'refunded'){
                            if($data['is_return_request'] == 1){
                                $data['is_return_request_message'] =  p__('xdelivery', 'Return request');
                            }
                            if($data['is_return_request'] == 2){
                                $data['is_return_request_message'] =  p__('xdelivery', 'Return processing');
                            }
                            if($data['is_return_request'] == 3){
                                $data['is_return_request_message'] =  p__('xdelivery', 'Return Rejected');
                            }
                        }else{
                            $data['is_return_request']  = 0 ;   
                        }

                        $ordersJson[] = $data;

                    }
                // Admin order queue filter lables
                $order_queue_filter[]=['filter_id'=>'3','date_range'=>__("Past 3 Days")];              
                $order_queue_filter[]=['filter_id'=>'7','date_range'=>__("Past 7 Days")];                            
                $order_queue_filter[]=['filter_id'=>'15','date_range'=>__("Past 15 Days")];              
                $order_queue_filter[]=['filter_id'=>'30','date_range'=>__("Past 30 Days")];              
                $order_queue_filter[]=['filter_id'=>'60','date_range'=>__("Past 60 Days")];     
                $order_queue_filter[]=['filter_id'=>'90','date_range'=>__("Past 90 Days")];     
                $order_queue_filter[]=['filter_id'=>'0','date_range'=>__("All Time")];  

                $order_payment_status[]=['payment_status_id'=>'pending','status_title'=>__("Pending")];              
                $order_payment_status[]=['payment_status_id'=>'success','status_title'=>__("Success")];                            
                $order_payment_status[]=['payment_status_id'=>'failed','status_title'=>__("Failed")];      
                                        
                $status = (new Xdelivery_Model_Orders)->getStatus();
                foreach ($status as $key => $value) {
                    $order_status_filter[]=['filter_id'=>$key,'status_title'=>$value];
                }
                $order_status_filter[]=['filter_id'=>'all','status_title'=>'All'];
                $order_max_id=35;
                $payload = [
                    'success' => true,
                    'orders' => $ordersJson,
                    'settings' => $settings,
                    'order_queue_filter' => $order_queue_filter,
                    'order_payment_status' => $order_payment_status,
                    'order_status' => $order_status_filter,
                    'params' => $params,
                    'order_max_id' => $order_max_id,
                    ];
                
                }else{
                    $payload = [
                        'error' => true,
                        'message' => p__('xdelivery', 'Values required')
                    ];  
                }

            } catch (\Exception $e) {
            $payload = [
                "error" => true,
                "message" => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);
    }


    public function _payStripe($secret_key, $charge_array){

        try
        {
            Stripe::setApiKey($secret_key);
            return \Stripe\Charge::create($charge_array);

        } catch (\Exception $e) {
            $error =  (array) $e;

            return [
              //  "e" => $e,
                "error" => true,
                "message" => $e->getMessage(),
                "status" => $error['declineCode'],
                "id" => $error['jsonBody']['error']['charge'],
            ];
        }
    }


     /**
     * authorization Stripe Success
     *
     */
    public function authorizationStripeSuccessAction() {
        try {
            $param = $this->getRequest()->getBodyParams();
            $paymentIntentId = $param['paymentIntentId'];
            //$orderId = $param['order_id'];
            $value_id = $param['value_id'];

            $stripeModel = (new Xdelivery_Model_PaymentMethod())->find(['value_id' => $value_id, 'method_type' => 'stripe']);
                   
            if ($stripeModel->getId()) {
                $settings_stripe = $stripeModel->getData();
                Stripe::setApiKey($settings_stripe['secret_key']);

                // Attach the card (PaymentMethod) to the customer!
                $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
                $payment_status = 'success';
                $booking_status = 'processing';

                if ($paymentIntent->status === 'requires_capture') {
                    $paymentIntent->capture();                   
                }

                // Else nothing to do!
                if ($paymentIntent->status === 'requires_confirmation') {
                    $paymentIntent->confirm();                   
                }

                $orderModel = (new Xdelivery_Model_Orders())
                    ->find(['id' => $param['order_id']])
                    ->setStatus($booking_status)
                    ->save();
                
                    $txnModel = (new Xdelivery_Model_OrderTransactions())
                        ->find(['order_id' => $param['order_id']])
                    ->setGatewayTransactionId($param['paymentIntentId'])
                    ->setGatewayInfo($paymentIntent)
                    ->setStatus($payment_status)
                    ->save();

                $payload = [
                    'success' => true,
                    'paymentIntent' => $paymentIntent
                ];

            }           

        }catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
                'trace' => $e->getTrace()
            ];
        }

        $this->_sendJson($payload);
    }

     /**
     * authorization Stripe Success
     *
     */
    public function authorizationStripeErrorAction() {
        try {
            $param = $this->getRequest()->getBodyParams();
            $paymentIntentId = $param['paymentIntentId'];
            $orderId = $param['order_id'];
            $error = $param['error'];
            $value_id = $param['value_id'];

            $stripeModel = (new Xdelivery_Model_PaymentMethod())->find(['value_id' => $value_id, 'method_type' => 'stripe']);
                   
            if ($stripeModel->getId()) {
                $settings_stripe = $stripeModel->getData();
                Stripe::setApiKey($settings_stripe['secret_key']);
 
                // Attach the card (PaymentMethod) to the customer!
                $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

                // Else nothing to do!
               // if ($paymentIntent->status === 'requires_confirmation') {
                    $paymentIntent->cancel([
                        'cancellation_reason' => 'abandoned'
                    ]);
                //}
                $payment_status = 'failed';
                $booking_status = 'failed';
                $orderModel = (new Xdelivery_Model_Orders())
                ->find(['id' => $param['order_id']])
                ->setStatus($booking_status)
                ->save();
            
                $txnModel = (new Xdelivery_Model_OrderTransactions())
                    ->find(['order_id' => $param['order_id']])
                ->setGatewayTransactionId($param['paymentIntentId'])
                ->setGatewayInfo($paymentIntent)
                ->setStatus($payment_status)
                ->save();

                $payload = [
                    'success' => true,
                    'paymentIntent' => $paymentIntent
                ];              

            }    

        }catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
                'trace' => $e->getTrace()
            ];
        }

        $this->_sendJson($payload);
    }

    /**
     * Save Order
     *
     */
    public function saveAction() {
        try {

            if($param = $this->getRequest()->getBodyParams()){
                $customerId = $this->_getCustomerId(true);
                $value_id = $this->getRequest()->getParam('value_id');
                $application = $this->getApplication();
                $onlineGateways = ['ewallet', 'paypal'];
                $settingModel = (new Xdelivery_Model_Settings())->find(['value_id' => $value_id ]);
                $settings = $settingModel->getData();
                $customer = new Customer_Model_Customer();
                            $customer->find($customerId);
                            
                if(!empty($param['address']) && $param['delivery'] != 'pickup'){
                   
                    $address = (new Xdelivery_Model_Address())->find($param['address']);
                    $address = $address->getData();
                    $param['customer']['firstname'] = $address['customer_name'];
                    $param['customer']['lastname'] = '';
                    $param['customer']['email'] = $customer->getEmail();
                    $param['customer']['phone_number'] = $address['phone_number'];

                    $param['customer']['pec'] = $address['pec'];
                    $param['customer']['sdi'] = $address['sdi'];
                    $param['customer']['cod_fiscale'] = $address['cod_fiscale'];                    
                    $param['customer']['company_address'] = $address['company_address'];
                }

                $store_id = 0;
                if(!empty($param['store_id'])){
                    $storeModel = (new Xdelivery_Model_Store())->find(['store_id' =>  $param['store_id']]);
                    $store_id = $storeModel->getId();
                }else{
                    $storeModel = (new Xdelivery_Model_Store())->find(['value_id' =>  $value_id]);
                    $store_id = $storeModel->getId(); 
                }

                $paymentMethod = (new Xdelivery_Model_PaymentMethod())->find(['id' => $param["payment_method"]]);

                if($paymentMethod->getMethodType() == 'stripe'){
                   $stripeModel = (new Xdelivery_Model_PaymentMethod())->find(['value_id' => $value_id, 'method_type' => 'stripe']);
                   
                    if ($stripeModel->getId()) {
                        $settings_stripe = $stripeModel->getData();
                        if(empty($settings_stripe['secret_key'])) {
                            throw new Exception(p__('xdelivery', 'This payment methods are temporarily unavailable'));
                        } 
                    } 
                }                
         
                $orderModel = (new Xdelivery_Model_Orders())
                        ->setValueId($value_id)
                        ->setOrderNumber(time())
                        ->setStoreId($store_id)
                        ->setCustomerId($customerId)
                        ->setTotalQty($param["main"]['total_item'])
                        ->setSubAmount($param["main"]['sub_amount'])
                        ->setSubAmountWithVat($param["main"]['sub_amount_with_vat'])
                        ->setTotalVatTax($param["main"]['total_vat_tax_amount'])
                        ->setTotalTax($param["main"]['total_tax_amount'])
                        ->setDeliveryCost($param["shipping"]['shipping_amount'])
                        ->setPaidAmount(0)
                        ->setTotalAmount($param["main"]['total_amount'])
                        ->setNotes($param["order_note"])
                        ->setDeliveryDate($param["delivery_date"])
                        ->setDeliveryTime($param["delivery_time"])
                        ->setDeliveryMethodId(0)
                        ->setDeliveryMethod($param["delivery"])
                        ->setPromocodeId($param["main"]['promocode_id'])
                        ->setDiscountCode($param["main"]['discount_code'])
                        ->setDiscountAmount($param["main"]['discount_amount'])
                        ->setTipsAmount($param["main"]["tips_amount"])
                        ->setCustomerFirstname($param['customer']['firstname'])
                        ->setCustomerLastname($param['customer']['lastname'])
                        ->setCustomerEmail($param['customer']['email'])
                        ->setCustomerInfo(json_encode($param['customer']))
                        ->setCustomerPhone($param['customer']['phone_number']);


                    if(in_array($paymentMethod->getMethodType(), $onlineGateways) || $paymentMethod->getMethodType() == 'stripe'){
                        $orderModel->setStatus('pending_payment');
                    }else{
                        if(!(double)$settings['enable_acceptance_rejection']){
                            $orderModel->setStatus('accepted');
                        }else{
                            $orderModel->setStatus('processing');
                        }                     

                    }    
                        
                    if(!empty($address)){

                        $customer_street=$address['address'];
                        if(!empty($address['address_two'])){
                            $customer_street .= ', '.$address['address_two'];
                        }
                        if(!empty($address['locality'])){
                            $customer_street .= ', '.$address['locality'];
                        }
                        $city = (!empty($address['city'])) ? ', '.$address['city'] : '' ;
                        $orderModel->setCustomerStreet($customer_street);
                        $orderModel->setCustomerPostcode($address['pincode']);
                        $orderModel->setCustomerCity($address['city']);
                    }
                       
                $orderModel->save();                       
                $order_id = $orderModel->getId(); 

            if($order_id) {
              
               foreach ($param['main']['cart_products'] as $key => $value) {

                    $product_id = $value['product_id'];
                    if(!empty($value['child_product_id'])){
                        $product_id = $value['child_product_id'];
                    }

                    //deduct qty from product
                    $this->updateProductInventory($product_id, $value['qty'], 'descrease');

                    $orderItemModel = (new Xdelivery_Model_OrderItems())
                        ->setOrderId($order_id)
                        ->setProductId($product_id)
                        ->setName($value['product_name'])
                        ->setBasePrice($value['price'])
                        ->setBasePriceInclTax(((double)$value['price'] + ($value['tax_amount']/$value['qty']))) 
                        ->setChoicePrice($value['sub_options_total'])
                        ->setPrice($value['selling_price'])
                        ->setPriceInclTax(((double)$value['selling_price'] + ($value['tax_amount']/$value['qty'])))
                        ->setQty($value['qty'])
                        ->setTotal($value['sub_total'])
                        ->setTotalInclTax($value['sub_total']+$value['tax_amount'])
                        ->setChoices(json_encode($value['optionsjson']))
                        ->setTaxRate(empty($value['tax_rate']) ? 0 : $value['tax_rate'])
                        ->setTaxAmount($value['tax_amount'])
                        ->setTaxId($value['tax_id'])
                        ->save();
                    }  

                    
            $orderTransactionsModel = (new Xdelivery_Model_OrderTransactions())
                ->setOrderId($order_id)
                ->setAmount($param["main"]['total_amount'])
                ->setPaymentMethodId($param["payment_method"])
                ->setPaymentMethod($paymentMethod->getMethodType())
                ->setStatus('pending')
                ->save();

                $is_stripe = false;
                if($paymentMethod->getMethodType() == 'stripe'){
                    $is_stripe = true;
                    /*Charge at stripe */                   
                    // $charge_array = array(
                    //     "currency" => Core_Model_Language::getCurrentCurrency()->getShortName(),
                    //     "amount" => (float) $param["main"]['total_amount'] * 100,
                    //     "source" =>  $param["stripe"]['token'],
                    //     "description" =>  p__('xdelivery', 'Order N').' '.$order_id. " from app " . $this->getApplication()->getName(),
                    //     "metadata" => array(
                    //         "Payment from app" => $this->getApplication()->getName(),
                    //         "Order N" => $order_id,
                    //         "First Name" => $customer->getFirstname(),
                    //         "Last Name" => $customer->getLastname(),
                    //         "Email" => $customer->getEmail(),
                    //         "Order N" => p__('xdelivery', 'Order N').' '.$order_id,
                    //     ),
                    // );
                        
                // Stripe 3D-secure
                    Stripe::setApiKey($settings_stripe['secret_key']);
                    
                    $currency = $application->getCurrency();
                    $customerId = $this->_getCustomerId(true);
                    $stripeCustomer = PaymentStripeCustomer::getForCustomerId($customerId);

                    if (!$stripeCustomer || !$stripeCustomer->getId()) {
                        throw new \Siberian\Exception(p__('payment_stripe', 'There is an issue retrieving your card!'));
                    }
                    $stripeAmount = PaymentStripeCurrency::getAmountForCurrency($param["main"]['total_amount'], $currency);
                    $stripeData = PaymentIntent::create([
                            'currency' => $currency,
                            'confirmation_method' => 'automatic',
                            'confirm' => true,
                            'capture_method' => 'manual',
                            'setup_future_usage' => 'off_session',
                            'amount' => $stripeAmount,
                            'customer' => $stripeCustomer->getToken(),
                            'payment_method_types' => ['card'],
                            'payment_method_data' => [
                                'type' => 'card',
                                'card' => [
                                    'token' => $param["stripe"]['token']
                                ]
                            ]
                        ]);              
                    
                   // $stripeData = $this->_payStripe($settings_stripe['secret_key'], $charge_array);
                    $payment_status = $stripeData['status'];
                    
                    if($payment_status == 'succeeded'){
                        $payment_status = 'success';
                        $booking_status = 'processing';
                    }
                    if($payment_status == 'processing' || $payment_status == 'requires_action'){
                        $payment_status = 'inprogress';
                        $booking_status = 'processing';
                    }
                    if($payment_status == 'payment_failed'){
                        $payment_status = 'failed';
                        $booking_status = 'failed';
                    }
                    $orderModel = (new Xdelivery_Model_Orders())
                            ->find(['id' => $order_id ])
                            ->setStatus($booking_status)
                            ->save();
                       
                        $txnModel = (new Xdelivery_Model_OrderTransactions())
                            ->find(['order_id' => $order_id ])
                           ->setStatus($payment_status)
                           ->setGatewayTransactionId($stripeData['id'])
                           ->save();

                }

                $settingModel = (new Xdelivery_Model_Settings())->find(['value_id' => $value_id ]);
                $settings = $settingModel->getData();

                $modelOrder = (new Xdelivery_Model_Orders()); 
                $order = $modelOrder->findOrderById($order_id); 
                $order['order_date']=  date('jS M, Y g:i A', strtotime($order['created_at']));
                $order['today_date']=  date('jS M, Y g:i A');
                $order['currency'] = $this->getApplication()->getCurrency();
                $currency = $this->getApplication()->getCurrency();
                $order['delivery_date']=  date('jS M, Y', $order['delivery_date']);
                $order['delivery_time']=  date('g:i A', $order['delivery_time']);

                $items = (new Xdelivery_Model_OrderItems())->findItemByOrderId($order_id);
                $itemsJson = [];

                foreach ($items as $key => $value) {
                    $value['choices'] = json_decode($value['choices']);
                    $value['price_with_currency'] = Xdelivery_Model_Utility::displayPrice($value['price'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                    $value['choice_price_with_currency'] = Xdelivery_Model_Utility::displayPrice($value['choice_price'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                    $value['total_with_currency'] = Xdelivery_Model_Utility::displayPrice($value['total'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                    $value['varients'] = (new Xdelivery_Model_ProductVariant())->getProductVariantValues($value['product_id']);
                    $itemsJson[] = $value;
                }
                $order['items'] = $itemsJson;
                $status = (new Xdelivery_Model_Orders)->getStatus();
                $order['order_status'] = $status[$order['order_status']];
                $order['order_status']  = p__('xdelivery', ucfirst($order['order_status']));

                 /*with currency */
                $order['sub_amount_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['sub_amount'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                $order['total_tax_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['total_tax'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                $order['total_amount_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['total_amount'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                $order['delivery_cost_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['delivery_cost'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                $order['tips_cost_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['tips_amount'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);

               /// Send Mail to customer
                $mailParams = [];
                $mailParams['order'] = $order;
                $mailParams['sender_email'] = $order['store_email'];
                $mailParams['sender_name'] = $order['store_name'];
                $mailParams['email'] = $order['customer_email'];
                $mailParams['to'] = 'customer';               
                $message = '';
                if(!in_array($paymentMethod->getMethodType(), $onlineGateways)){
                    $mailParams['subject'] = p__('xdelivery', "%s Order #%s is %s!", $this->getApplication()->getName(), $mailParams['order']['order_number'], $mailParams['order']['order_status']);
                    $mailParams['headline'] = p__('xdelivery', "Your order is %s!", $mailParams['order']['order_status']);
                    if($param['delivery'] == 'pickup'){
                        $message =  "Thank you for placing your order with %s. This email is to confirm your order has been placed successfully, and is ready for pickup.";
                        $mailParams['message'] = p__('xdelivery', $message, $this->getApplication()->getName());
                    }else{ 
                        $message = "Thank you for placing your order with %s. This email is to confirm your order has been placed successfully, and will be processed & deliver to you soon.";
                        $mailParams['message'] = p__('xdelivery',  $message , $this->getApplication()->getName());
                    }
                }else{
                    $mailParams['subject'] = p__('xdelivery', "%s Order #%s your payment has been initiated", $this->getApplication()->getName(), $mailParams['order']['order_number']);
                    $mailParams['headline'] = p__('xdelivery', "Your order status has been %s!",  $mailParams['order']['order_status']);
                    $mailParams['message'] = "";
                }
                
                $this->_sendCustomerOrderEmail($mailParams);

                // if(!empty($settings['whatsender_key']) && !empty($param['customer']['phone_number'])){
                //     //Whatsapp sent                   
                   
                //     if(!empty($mailParams['message'])){
                //         $message = str_replace("email","message", $message);
                //          $message = p__('xdelivery',  $message , $this->getApplication()->getName());

                //         $wParams = ['phone' => $param['customer']['phone_number'], 'message' =>  addslashes($message), 'sender' => $settings['whatsender_sender']];
                       
                //         $whatsapp_status = (new Xdelivery_Model_Whatsender)->sent($settings['whatsender_key'], $wParams);
                //     }

                //     $wParams = ['phone' => $param['customer']['phone_number'], 'message' => addslashes($mailParams['subject']), 'sender' => $settings['whatsender_sender']];
                   
                //     $whatsapp_status = (new Xdelivery_Model_Whatsender)->sent($settings['whatsender_key'], $wParams);
                // }
                

                if(!in_array($paymentMethod->getMethodType(), $onlineGateways)){

                    // Print the order
                    $this->print($order_id);
                    
                    /// Send Mail to vendor
                    $mailParams = [];
                    $mailParams['order'] = $order;
                    $mailParams['sender_email'] = $order['customer_email'];
                    $mailParams['sender_name'] = $order['customer_firstname'];
                    $mailParams['email'] = $order['store_email'];
                    $mailParams['to'] = 'merchant';
                    $mailParams['subject'] = p__('xdelivery', '%s New Order #%s has been received!', $this->getApplication()->getName(), $mailParams['order']['order_number']);
                    $mailParams['headline'] = p__('xdelivery', 'You have received a new order!');
                    $mailParams['message'] = '';
                    // $this->_sendCustomerOrderEmail($mailParams);
                }                
                    
                    //Clear cart 
                    $cartModel = (new Xdelivery_Model_Carts())->deleteQuery('xdelivery_shopping_carts', 'customer_id', $customerId); 
            }

            $message = p__('xdelivery', 'Order successfully Placed');
            $template = (new Xdelivery_Model_Notification())->find([
                'notification_order_status'=> $order['order_status'],                            
                'value_id' => $value_id
            ])->getData();  
            $admins_list = (new Xdelivery_Model_Admins())->findAll(['store_id' => $order['store_id']])->toArray();   
            $tagLabels = [
                "@@order_no@@",
                "@@order_date@@",
                "@@order_status@@",
                "@@customer_name@@",
                "@@customer_phone@@",
                "@@customer_email@@",                            
                "@@total_amount@@",
                "@@store_name@@",
                "@@store_email@@",
                "@@store_phone@@",
                "@@admin_name@@",
                "@@admin_email@@",
                "@@admin_phone@@",
                "@@app_name@@",
                "@@payment_status@@",
                "@@payment_mode@@",
                "@@product_list@@",//Only Email
                "@@delivery_info@@",//Only Email
            ];                        
            /* IMPORTENT
                Admin Tags (admin_name, admin_email, admin_phone) 
                If these tags are used in Customer message we will replace them with current user data(Admin)
                (If the order status is changed from admin side then we will replace with first)
                If these tags are used in Admin message we will replace them with corrosponding Admin data
            */
            $tagValues = [];
            $tagValues['order_no'] = '#'.$order['order_number'];
            $tagValues['order_date'] = $order['created_at'];
            $tagValues['order_status'] = $order['order_status'];
            $tagValues['customer_name'] = $order['customer_firstname'].' '.$order['customer_lastname'];
            $tagValues['customer_phone'] = $order['customer_phone'];
            $tagValues['customer_email'] = $order['customer_email'];                    
            $tagValues['total_amount'] = $order['total_amount_with_currency'];
            $tagValues['store_name'] = $order['store_name'];
            $tagValues['store_email'] = $order['store_email'];
            $tagValues['store_phone'] = $order['store_phone'];
            $tagValues['app_name'] = $this->getApplication()->getName();  
            $tagValues['payment_status'] = $order['payment_status'];
            $tagValues['payment_mode'] = $order['payment_method'];                  
            $tagValues['product_list'] = "";//Only Email                  
            $tagValues['delivery_info'] = "";//Only Email        
            $notification_logger=[];          
            $notification_logger['value_id']=$order['value_id'];          
            $notification_logger['order_id']=$order['order_id'];   
            // dd($settings, $order, $customer, $tagLabels, $tagValues, $template);
            /*
                Whatsender
            */  
            if ($settings['is_enable_whatsender']) {   
                $pattern = '/^\+[0-9]{11}$/';// The pattern checks for a string starting with + and followed by 11 digits                     
                $customer_phone = $order['customer_phone'];
                $admin_phone = $order['store_phone'];
                $sender = $settings['whatsender_sender'];
                $whatsender_key = $settings['whatsender_key'];
                //Whatsender to Customer
                if ($template['notification_is_customer'] && $template['notification_customer_is_whatsender'] && !empty($template['notification_customer_whatsender_text'])) {
                    // $tagValues['admin_name'] = $customer->getFirstname().' '.$customer->getLasttname();
                    // $tagValues['admin_email'] = $customer->getEmail();
                    // $tagValues['admin_phone'] = $customer->getMobile();

                    $customer_what_message=$template['notification_customer_whatsender_text'];  
                    $customer_what_message = str_replace($tagLabels, $tagValues, $customer_what_message);                            
                    $wParams = ['phone' => $customer_phone, 'message' => addslashes($customer_what_message), 'sender' => $sender];
                    $whatsapp_respone = (new Xdelivery_Model_Whatsender)->sent($whatsender_key, $wParams);
                    $decodedResponse = json_decode($whatsapp_respone, true);
                    if ($decodedResponse['success'] === true) {
                        $notification_logger['status']='success';  
                        $notification_logger['additional_info']=$customer_what_message; 
                    } else {
                        $notification_logger['status']='error';  
                        $notification_logger['additional_info']=$decodedResponse['message']; 
                    }
                    $notification_logger['type']="whatsender";                            
                    $notification_logger['user_id']=$order['customer_id'];                                                                         
                    (New Xdelivery_Model_Notificationlogs)->setData($notification_logger)->save();
                }
                //Whatsender to Admin 
                if ($template['notification_is_admin'] && $template['notification_admin_is_whatsender'] && !empty($template['notification_admin_whatsender_text'])) {                          
                    $customer_what_message=$template['notification_customer_whatsender_text'];  
                    foreach ($admins_list as $key => $value) {
                        $admin = (new Customer_Model_Customer())->find(['customer_id' => $value['customer_id']]);                         
                        $tagValues['admin_name'] = $admin->getFirstname().' '.$admin->getLasttname();
                        $tagValues['admin_email'] = $admin->getEmail();
                        $tagValues['admin_phone'] = $admin->getMobile();

                        $customer_what_message = str_replace($tagLabels, $tagValues, $customer_what_message);                            
                        $wParams = ['phone' => $customer_phone, 'message' => addslashes($customer_what_message), 'sender' => $sender];
                        $whatsapp_respone = (new Xdelivery_Model_Whatsender)->sent($whatsender_key, $wParams);
                        $decodedResponse = json_decode($whatsapp_respone, true);
                        if ($decodedResponse['success'] === true) {
                            $notification_logger['status']='success';  
                            $notification_logger['additional_info']=$customer_what_message; 
                        } else {
                            $notification_logger['status']='error';  
                            $notification_logger['additional_info']=$decodedResponse['message']; 
                        }
                        $notification_logger['type']="whatsender";                            
                        $notification_logger['user_id']=$order['customer_id'];                                                                         
                        (New Xdelivery_Model_Notificationlogs)->setData($notification_logger)->save();

                        $notification_logger['type']="email";
                        $notification_logger['status']='success';
                        $notification_logger['user_id']=$admin->getCustomerId();;
                        $notification_logger['additional_info']=$tagValues['message'];
                        (New Xdelivery_Model_Notificationlogs)->setData($notification_logger)->save();
                    }
                }                
            }
            /*
                Twillio
            */  
            $this->twilioMessage($settings, $order, $customer, $tagLabels, $tagValues, $template);
            /*
                PUSH
            */  
            $this->pushMessage($settings, $order, $customer, $tagLabels, $tagValues, $template);     
            
            $paypalURL = '';
            $is_paypal = false;
        
            $responsePaypal = [];
            if($paymentMethod->getMethodType() == 'paypal'){
                    $is_paypal = true;
                    /*check paypal access setting*/
                    $gatewayModel = (new Xdelivery_Model_PaymentMethod())->find(['value_id' =>  $value_id, 'method_type' => 'paypal']);
                    if($gatewayModel->getId()){

                    /*get PayPal payment URL*/
                    $paypalData = [];
                    $paypalData["PAYMENTREQUEST_0_CURRENCYCODE"] = Core_Model_Language::getCurrentCurrency()->getShortName();
                    $base_url = $this->getRequest()->getBaseUrl();

                    if ($param['is_webview']) {
                        $paypalData["RETURNURL"] = trim($base_url . '/var/apps/browser/index-prod.html#' . $param['BASE_PATH'] .'/xdelivery/mobile_paypalreturn/index/value_id/'. $value_id );
                        $paypalData["CANCELURL"] = trim($base_url . '/var/apps/browser/index-prod.html#' . $param['BASE_PATH'] .'/xdelivery/mobile_paypalreturn/index/value_id/'. $value_id);
                    } else {
                        $paypalData["RETURNURL"] = trim($base_url. '/var/apps/browser/index-prod.html#' . $param['current_url'] . 'confirm');
                        $paypalData["CANCELURL"] = trim($base_url . '/var/apps/browser/index-prod.html#' . $param['current_url'] . 'cancel');
                    }
                    $paypalData["PAYMENTREQUEST_0_AMT"] = number_format($param["main"]['total_amount'] , 2,'.','');
                  
                    if ($gatewayModel->getPaymentMode() == 'sandbox') {
                        $paypal_api_user = $gatewayModel->getSandboxusername();
                        $paypal_api_user_pwd = $gatewayModel->getSandboxpassword();
                        $paypal_api_user_signature = $gatewayModel->getSandboxsignature();
                    } else {
                        $paypal_api_user = $gatewayModel->getUsername();
                        $paypal_api_user_pwd = $gatewayModel->getPassword();
                        $paypal_api_user_signature = $gatewayModel->getSignature();
                    }
                    

                    $responsePaypal = $this->requestPayPal(self::SET_EXPRESS_CHECKOUT, $paypalData, $gatewayModel->getPaymentMode(), $paypal_api_user, $paypal_api_user_pwd, $paypal_api_user_signature);
                    
                   
                    /*Failed case*/
                    if($responsePaypal['ACK'] == 'Failure'){
                        $param['status'] = "failed";
                      
                        $orderModel = (new Xdelivery_Model_Orders())
                            ->find(['id' => $order_id ])
                            ->setStatus($param['status'])
                            ->save();
                       
                        $txnModel = (new Xdelivery_Model_OrderTransactions())
                            ->find(['order_id' => $order_id ])
                           ->setStatus($param['status'])
                           ->save();

                        $message = p__('xdelivery', 'Payment is failed, Please try again later');
                    }else{
                       if($responsePaypal['ACK'] == 'Success'){
                            $param['status'] = "success";
                            $paypalURL = $responsePaypal['pay_url'].'&webview=1';
                        }                        
                    }

                    }else{
                        $message = p__('xdelivery', 'Payment is failed, Please try again later');
                
                    }
                }

                $payload = [
                    'success' => true,
                    'data' => $param,
                    'is_paypal' => $is_paypal,
                    'order_id' => $order_id,
                    'message' => $message,
                    'paypal_url' => $paypalURL,
                    'whatsapp_status' => $whatsapp_status,
                    'stripeData' => $stripeData,
                    'is_stripe' => $is_stripe,
                    'payment_status' => $payment_status
                    ];
            
            }else{
                $payload = [
                    'error' => true,
                    'message' => p__('xdelivery', 'Values required')
                ];  
            }

            } catch (\Exception $e) {
            $payload = [
                "error" => true,
                "message" => $e->getMessage(),
                "e" => $e
            ];
        }

        $this->_sendJson($payload);
    }


    /*Function for paypal request*/
    public function requestPayPal($method, $params, $paypal_payment_mode, $paypal_api_user, $paypal_api_user_pwd, $paypal_api_user_signature) {
        $logger = Zend_Registry::get("logger");

        //for paypal in live mode
        if ($paypal_payment_mode == 'live') {
            $api_url = "https://api-3t.paypal.com/nvp";
            $paypal_url = "https://www.paypal.com/webscr?cmd=_express-checkout&useraction=commit&token=";
        } else {
            //paypalpay in test mode
            $api_url = "https://api-3t.sandbox.paypal.com/nvp";
            $paypal_url = "https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&useraction=commit&token=";
        }
        $params = array_merge($params, array(
            'METHOD' => $method,
            'VERSION' => '74.0',
            'USER' => $paypal_api_user,
            'PWD' => $paypal_api_user_pwd,
            'SIGNATURE' => $paypal_api_user_signature,
        ));

        $orig_params = $params;

        $params = http_build_query($params);
        $curl = curl_init();
        $curlParams = array(
            CURLOPT_URL             => $api_url,
            CURLOPT_POST            => 1,
            CURLOPT_POSTFIELDS      => $params,
            CURLOPT_RETURNTRANSFER  => 1,
            CURLOPT_VERBOSE         => 1,
            CURLOPT_SSL_VERIFYPEER  => false, //si certificat SSL => true
            CURLOPT_SSL_VERIFYHOST  => false, //si certificat SSL => 2
        );
        
        curl_setopt_array($curl, $curlParams);
        /** @todo testing in production for integration */
        if (APPLICATION_ENV == "development") {
            curl_setopt($curl, CURLOPT_SSLVERSION, 6);
        }
        $response = curl_exec($curl);
        $responseArray = array();
        parse_str($response, $responseArray);
        $responseArray['paypal_url'] = $paypal_url;
        if (curl_errno($curl)) {
            $responseArray['errorMessage'] = curl_error($curl);
            $responseArray['error'] = true;
            curl_close($curl);
            $logger->log("CURL error n° " . print_r($this->_errors, true) . ' - response: ' . print_r($response, true), Zend_Log::DEBUG);
            return $responseArray;
        } else {
            
            if ($responseArray['ACK'] === 'Success') {
                curl_close($curl);                
                if (!empty($responseArray['TOKEN']) AND $token = $responseArray['TOKEN']) {
                    $responseArray['pay_url']  = $paypal_url . $responseArray['TOKEN'];
                }  
                return $responseArray;
            } else {
               $responseArray['errorMessage'] = $responseArray;
               $responseArray['error'] = true;
                curl_close($curl);
                $logger->log("CURL error: " . print_r($this->_errors, true), Zend_Log::DEBUG);
                 return $responseArray;
            }
        }
    }

    public function findOrderByIdAction() {
        try {
         
           if($order_id = $this->getRequest()->getParam('order_id')) {
                $value_id = $this->getRequest()->getParam('value_id');
                $settingModel = (new Xdelivery_Model_Settings())->find(['value_id' => $value_id ]);
                $orderModel = (new Xdelivery_Model_Orders())
                    ->find(['id' => $order_id])
                    ->setIsManaged(1)
                    ->save();
                $settings = $settingModel->getData();
                $currency = Core_Model_Language::getCurrencySymbol();
                $customerId = $this->_getCustomerId(true);

                $orders = (new Xdelivery_Model_Orders())
                        ->findAllOrderByCustomerId($customerId);

                $status = (new Xdelivery_Model_Orders)->getStatus();
                $order = (new Xdelivery_Model_Orders())->findOrderById($order_id);
                $order['is_enable_cancel']  = false;
                $order_status = $order['order_status'];
                if($order['order_status']=='processing' || $order['order_status']=='pending_payment'){
                    $order['is_enable_cancel']  = true;
                }
                $order['is_enable_reTryPayment'] = false;
                if($order['order_status']=='pending_payment'){
                    $order['is_enable_reTryPayment'] = true;
                }
                $order['is_return_request'] = (integer) $order['is_return_request'];
                $order['order_status'] = $status[$order['order_status']];
                $order['order_date']=  date($settings['date_format']. ' '. $settings['time_format'], strtotime($order['created_at']));
                $order['currency'] = Core_Model_Language::getCurrencySymbol();
                $order['order_status']  = ucfirst(p__('xdelivery', $order['order_status']));
                $order['payment_status']  = ucfirst(p__('xdelivery', $order['payment_status']));
                $order['delivery_date']=  date($settings['date_format'], $order['delivery_date']);
                $order['delivery_time']=  date($settings['time_format'], $order['delivery_time']);
                $order['status_message'] = p__('xdelivery', 'Your order is now').' '.$order['order_status'];
                $order['tracking_type'] = (integer) $order['tracking_type'];

                $items = (new Xdelivery_Model_OrderItems())->findAll(['order_id' => $order_id])->toArray();
                
                $itemsJson = [];
                foreach ($items as $key => $value) {
                    $value['choices'] = json_decode($value['choices']);
                    $value['price_with_currency'] = Xdelivery_Model_Utility::displayPrice($value['price'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                     $value['choice_price_with_currency'] = Xdelivery_Model_Utility::displayPrice($value['choice_price'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                      $value['total_with_currency'] = Xdelivery_Model_Utility::displayPrice($value['total'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);

                    $value['varients'] = (new Xdelivery_Model_ProductVariant())->getProductVariantValues($value['product_id']);

                    $itemsJson[] = $value;
                }
                $order['items'] = $itemsJson;


                $ewallet_value_id = 0; 
                if(class_exists("Ewallet_Model_Ewallet")) {
                    $ewalletValueId = (new Ewallet_Model_Ewallet)->getCurrentValueId();
                    if($ewalletValueId != '' && $ewalletValueId != null && $ewalletValueId > 0){
                        $ewallet_value_id = $ewalletValueId;
                    }
                }                
                if ($order['sub_amount_with_vat']==0 || $order['sub_amount_with_vat']==null) {
                    $order['is_vat']=false;
                }else {
                    $order['is_vat']=true;
                }
                /*with currency */
                if ($settings['discount_tax_calculation'] == 1) {                     
                     $order['discounted_amount'] = ($order['is_vat']) ? $order['sub_amount'] - $order['discount_amount']+$order['total_tax'] : $order['sub_amount'] - $order['discount_amount'] ;
                }else {                    
                    $order['discounted_amount'] = ($order['is_vat']) ? $order['sub_amount'] - $order['discount_amount'] + $order['total_tax'] : $order['sub_amount'] - $order['discount_amount'] + $order['total_tax'] ;
                }

                $order['discounted_amount_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['discounted_amount'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']); 
                $order['discounted_amount_with_vat_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['discounted_amount'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']); 
                $order['sub_amount_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['sub_amount'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                $order['total_tax_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['total_tax'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                $order['sub_amount_with_vat_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['sub_amount_with_vat'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                $order['total_vat_tax_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['total_vat_tax'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                $order['total_amount_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['total_amount'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                $order['delivery_cost_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['delivery_cost'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                $order['discount_cost_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['discount_amount'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                $order['tips_cost_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['tips_amount'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                
                $return_within = (integer) $settings['return_within'];
                $order['is_enable_return']  = false;
                $createdAt = $this->calculate_time_span($order['created_at']);
                $settings['enable_retrun'] = (boolean) $settings['enable_retrun'];
                
                if(($createdAt['months'] == 0) &&  ($createdAt['day'] <= $return_within) && $settings['enable_retrun'] && $order['is_return_request'] == 0){
                    $order['is_enable_return'] = true;
                }

                if($order['is_return_request'] && $order_status != 'refunded' ){
                    if($order['is_return_request'] == 1){
                        $order['is_return_request_message'] =  p__('xdelivery', 'Return request');
                        $order['status_message'] =  p__('xdelivery', 'Return request send successfully');
                    }
                    if($order['is_return_request'] == 2){
                        $order['is_return_request_message'] =  p__('xdelivery', 'Return processing');
                        $order['status_message'] =  p__('xdelivery', 'Return processing is in progress');
                    }
                    if($order['is_return_request'] == 3){
                        $order['is_return_request_message'] =  p__('xdelivery', 'Return Rejected');
                        $order['status_message'] =  p__('xdelivery', 'Return request is Rejected');
                    }
                    
                }
   
                $payload = [
                    'success' => true,
                    'order_number' => $order['order_number'],
                    'order_date' => date($settings['date_format'].' '.$settings['time_format'], strtotime($order['created_at'])),
                    'amount' => Core_Model_Language::getCurrencySymbol().$order['total_amount'],
                    // 'payment_status' => ucfirst($order['payment_status']),
                    'payment_status' => lcfirst($order['payment_status']),
                    // 'order_status' => ucfirst($order['order_status']),
                    'order_status' => $order_status,
                    'payment_mode' => $order['label_name'],
                    'order_id' => $order_id,
                    'order' => $order,
                    'ewallet_value_id' => $ewallet_value_id
                ];  
            }

        } catch (\Exception $e) {
            $payload = [
                "error" => true,
                "message" => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);
    }


    /**
     * @param $date
     * @return string
     * @throws Exception
     * @throws Zend_Session_Exception
     */
    function calculate_time_span($date){
            $seconds  = strtotime(date('Y-m-d H:i:s')) - strtotime($date);

            $time['months'] = floor($seconds / (3600*24*30));
            $time['day'] = floor($seconds / (3600*24));
            $time['hours'] = floor($seconds / 3600);
            $time['mins'] = floor(($seconds - ($time['hours']*3600)) / 60);
            $time['secs'] = floor($seconds % 60);

         
            return $time;
    }

    public function updateorderpaymentstatusAction(){
        if ($param = $this->getRequest()->getBodyParams()) {            
                try {
                    $order_id = $param['order_id'];
                    $status = $param['payment_status'];
                    (new Xdelivery_Model_OrderTransactions())->find(['order_id' => $order_id])
                    ->setStatus(trim($status))                    
                    ->save();                    
                    // Check if the status is correctly updated
                    $payload = [
                        'success' => true,
                        'message' => p__('xdelivery', 'Update Status successfully'),
                    ];                    

                    } catch (\Exception $e) {
                    $payload = [
                        'error' => true,
                        'message' => $e->getMessage(),
                    ];
                }

                $this->_sendJson($payload);
            }
    }
  /**
     * update Payment Status order
     *
     */
    public function updatePaymentStatusAction() {
        try {                    
            if($param = $this->getRequest()->getBodyParams()) {
                $value_id = $this->getRequest()->getParam('value_id');
                $customerId = $this->_getCustomerId(true);
                $payerId = $param['txn'];
                $tokenId = $param['tokenId'];
                $paymentStatus = $param['status'];

                $orderModel = (new Xdelivery_Model_OrderTransactions())
                    ->find(['order_id' => $param['order_id']]);
                if($orderModel->getPaymentMethod() == 'paypal' && $paymentStatus == 'success'){
                    $paymentResult = $this->GetPaypalCheckoutDetails($param['order_id'], $tokenId, $payerId);
                    if($paymentResult){
                        $param['status'] = 'success';
                    }else{
                        $param['status'] = 'failed';
                    }
                }
           
                if($param['status'] != "success"){
                  $booking_status = 'failed';
                  $payment_status = 'failed';
                  $message =  p__('xdelivery', 'Your payment has been failed');
                }else{
                  $booking_status = 'processing';
                  $payment_status = 'success';
                  $message =  p__('xdelivery', 'Your payment has been received successfully');
                }               

                $orderModel = (new Xdelivery_Model_Orders())
                    ->find(['id' => $param['order_id']])
                    ->setStatus($booking_status)
                    ->save();
               
                $txnModel = (new Xdelivery_Model_OrderTransactions())
                    ->find(['order_id' => $param['order_id']])
                   ->setGatewayTransactionId($param['txn'])
                   ->setGatewayInfo($param['tokenId'])
                   ->setStatus($payment_status)
                   ->save();

                $settingModel = (new Xdelivery_Model_Settings())->find(['value_id' => $value_id ]);
                $settings = $settingModel->getData();

                $modelOrder = (new Xdelivery_Model_Orders()); 
                $order = $modelOrder->findOrderById($param['order_id']); 
                $order['order_date']=  date('jS M, Y g:i A', strtotime($order['created_at']));
                $order['today_date']=  date('jS M, Y g:i A');
                $order['currency'] = $this->getApplication()->getCurrency();
                $currency = $this->getApplication()->getCurrency();
                $order['delivery_date']=  date('jS M, Y', $order['delivery_date']);
                $order['delivery_time']=  date('g:i A', $order['delivery_time']);                

                $items = (new Xdelivery_Model_OrderItems())->findAll(['order_id' => $param['order_id'] ])->toArray();
                $itemsJson = [];
                foreach ($items as $key => $value) {
                    $value['choices'] = json_decode($value['choices']);
                    $value['price_with_currency'] = Xdelivery_Model_Utility::displayPrice($value['price'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                    $value['choice_price_with_currency'] = Xdelivery_Model_Utility::displayPrice($value['choice_price'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                    $value['total_with_currency'] = Xdelivery_Model_Utility::displayPrice($value['total'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);

                    if($param['status'] != "success"){
                        $this->updateProductInventory($value['product_id'], $value['qty'], 'increase');
                    }
                    $value['varients'] = (new Xdelivery_Model_ProductVariant())->getProductVariantValues($value['product_id']);
                   $itemsJson[] = $value;
                }

                $order['items'] = $itemsJson;
                $status = (new Xdelivery_Model_Orders)->getStatus();
                $order['order_status'] = $status[$order['order_status']];
                $order['order_status']  = p__('xdelivery', ucfirst($order['order_status']));

                  /*with currency */
                $order['sub_amount_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['sub_amount'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                $order['total_tax_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['total_tax'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                $order['total_amount_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['total_amount'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                $order['delivery_cost_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['delivery_cost'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                $order['tips_cost_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['tips_amount'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);

                /// Send Mail to customer
                $mailParams = [];
                $mailParams['order'] = $order;
                $mailParams['sender_email'] = $order['store_email'];
                $mailParams['sender_name'] = $order['store_name'];
                $mailParams['email'] = $order['customer_email'];               
                $mailParams['subject'] = p__('xdelivery', "%s Order #%s has been %s!", $this->getApplication()->getName(), $mailParams['order']['order_number'], $mailParams['order']['order_status']);
                $mailParams['headline'] =  $message;
                $mailParams['to'] = 'customer';

                $wsMessage = '';
                if($param['delivery'] == 'pickup'){
                    $wsMessage = "Thank you for placing your order with %s. This email is to confirm your order has been placed successfully, and is ready for pickup.";
                    $mailParams['message'] = p__('xdelivery', $wsMessage, $this->getApplication()->getName());
                }else{ 
                    $wsMessage = "Thank you for placing your order with %s. This email is to confirm your order has been placed successfully, and will be processed & deliver to you soon.";
                    $mailParams['message'] = p__('xdelivery', $wsMessage, $this->getApplication()->getName());
                }
            
            $this->_sendCustomerOrderEmail($mailParams);

            if(!empty($settings['whatsender_key']) && !empty($order['customer_phone'])){
                    //Whatsapp sent                   
                   
                    if(!empty($mailParams['message'])){
                         $wsMessage = str_replace("email","message", $wsMessage);
                         $wsMessage = p__('xdelivery',  $wsMessage , $this->getApplication()->getName());

                        $wParams = ['phone' => $order['customer_phone'], 'message' =>  addslashes($wsMessage),  'sender' => $settings['whatsender_sender']];
                       
                        $whatsapp_status = (new Xdelivery_Model_Whatsender)->sent($settings['whatsender_key'], $wParams);
                    }

                    $wParams = ['phone' => $order['customer_phone'], 'message' => addslashes($mailParams['subject']),  'sender' => $settings['whatsender_sender']];
                   
                    $whatsapp_status = (new Xdelivery_Model_Whatsender)->sent($settings['whatsender_key'], $wParams);
                }


            if($param['status'] == "success"){
                // Print the order
                $this->print($param['order_id']);
                
                /// Send Mail to vendor
                $mailParams = [];
                $mailParams['order'] = $order;
                $mailParams['sender_email'] = $order['customer_email'];
                $mailParams['sender_name'] = $order['customer_firstname'];
                $mailParams['email'] = $order['store_email'];
                
                $mailParams['subject'] = p__('xdelivery', "%s New Order #%s has been received!", $this->getApplication()->getName(), $mailParams['order']['order_number']);
                $mailParams['headline'] = p__('xdelivery', "You have received new order!");
                $mailParams['message'] = '';
                $mailParams['to'] = 'merchant';
                // $this->_sendCustomerOrderEmail($mailParams);
            }
                $payload = [
                    'success' => true,
                    'message' => $message,
                    'order_id' => $param['order_id'],
                    'paymentResult' => $paymentResult
                    ]; 

                }else {
                    
                    $payload = [
                          "error" => true,
                          "message" => p__('xdelivery', 'Something went wrong!'),
                      ];
            }
       
           
        } catch (\Exception $e) {
            $payload = [
                "error" => true,
                "message" => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);
    }


   public function cancelAction() {
       
        if ($order_id = $this->getRequest()->getParam('order_id')) {
                try {
                    
                    $modal = (new Xdelivery_Model_Orders())
                        ->find(['id' => $order_id]);
                    $modal->setStatus('canceled');
                    $modal->save();

                    $currency = $this->getApplication()->getCurrency();
                    $modelOrder = (new Xdelivery_Model_Orders()); 
                    $order = $modelOrder->findOrderById($order_id);

                    $settingModel = (new Xdelivery_Model_Settings())->find(['value_id' => $order['value_id'] ]);
                    $settings = $settingModel->getData();

                    $status = (new Xdelivery_Model_Orders)->getStatus();
                    $order['order_status'] = $status[$order['order_status']];
                    $order['order_status']  = p__('xdelivery', ucfirst($order['order_status']));
                    $order['order_date']=  date('jS M, Y g:i A', strtotime($order['created_at']));
                    $order['today_date']=  date('jS M, Y g:i A');
                    $order['currency'] = $this->getApplication()->getCurrency();
                    $items = (new Xdelivery_Model_OrderItems())->findAll(['order_id' => $order_id])->toArray();
                    $itemsJson = [];
                    foreach ($items as $key => $value) {
                        $value['choices'] = json_decode($value['choices']);
                        $value['price_with_currency'] = Xdelivery_Model_Utility::displayPrice($value['price'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                        $value['choice_price_with_currency'] = Xdelivery_Model_Utility::displayPrice($value['choice_price'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                        $value['total_with_currency'] = Xdelivery_Model_Utility::displayPrice($value['total'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                        $value['varients'] = (new Xdelivery_Model_ProductVariant())->getProductVariantValues($value['product_id']);
                        $itemsJson[] = $value;
                    }
                    $order['items'] = $itemsJson;

                          /*with currency */
                    $order['sub_amount_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['sub_amount'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                    $order['total_tax_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['total_tax'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                    $order['total_amount_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['total_amount'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                    $order['delivery_cost_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['delivery_cost'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                    $order['tips_cost_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['tips_amount'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);

                    $mailParams = [];
                    $mailParams['order'] = $order;
                    $mailParams['sender_email'] = $order['store_email'];
                    $mailParams['sender_name'] = $order['store_name'];
                    $mailParams['email'] = $order['customer_email'];
                    $mailParams['order'] = $order;
                    $mailParams['to'] = 'customer';
                    $mailParams['subject'] = p__('xdelivery', '%s Order #%s is %s!', $this->getApplication()->getName(), $mailParams['order']['order_number'], $mailParams['order']['order_status']);
                    $mailParams['headline'] = p__('xdelivery', 'Your order is %s!',  $mailParams['order']['order_status']);
                    $mailParams['message'] = p__('xdelivery', 'This email is to confirm your order has been %s successfully.', $mailParams['order']['order_status']);
                    
                    $this->_sendCustomerOrderEmail($mailParams);

                     // product qty increase
                    $items = (new Xdelivery_Model_OrderItems())->findAll(['order_id' => $order_id])->toArray();
                    
                    foreach ($items as $key => $value) {
                        $this->updateProductInventory($value['product_id'], $value['qty'], 'increase');
                    }

                    if($order['payment_status'] == 'success'){
                        $modalTxn = (new Xdelivery_Model_OrderTransactions());
                        $modalTxn->setOrderId($order_id);
                        $modalTxn->setStatus('refunded');
                        $modalTxn->setAmount($order['total_amount']);
                        $modalTxn->setPaymentMethodId($order['payment_method_id']);
                        $modalTxn->setPaymentMethod($order['payment_method']);
                        $modalTxn->save();                    
                        $this->cancelOrderWalletFuncations($order);
                    }
 
                    $message = p__('xdelivery', 'Order canceled successfully');                    
                    if($order['payment_status'] == 'success'){
                        $message = p__('xdelivery', 'Order canceled successfully, You will get the full refund in your Wallet');
                    }

                    $mailParams = [];
                    $mailParams['order'] = $order;
                    $mailParams['sender_email'] = $order['customer_email'];
                    $mailParams['sender_name'] = $order['customer_firstname'];
                    $mailParams['email'] = $order['store_email'];
                    $mailParams['order'] = $order;
                    $mailParams['to'] = 'merchant';
                    $mailParams['subject'] = p__('xdelivery', '%s - Cancellation of order number #%s', $this->getApplication()->getName(), $mailParams['order']['order_number']);
                    $mailParams['headline'] = '';
                    $mailParams['message'] =  p__('xdelivery', 'This email is to formally inform you that I am cancelling order number %s. The amount of the order is %s %s. I placed the order on %s',  $mailParams['order']['order_number'], $mailParams['order']['currency'], $mailParams['order']['total_amount'], $mailParams['order']['order_date']);
                    
                    $this->_sendCustomerOrderEmail($mailParams);

                    $payload = [
                        'success' => true,
                        'message' => $message
                    ];

                    } catch (\Exception $e) {
                    $payload = [
                        'error' => true,
                        'message' => $e->getMessage(),
                    ];
                }

                $this->_sendJson($payload);
            }
    }


       public function returnAction() {
       
        if ($order_id = $this->getRequest()->getParam('order_id')) {
                try {
                    
                    $modal = (new Xdelivery_Model_Orders())
                        ->find(['id' => $order_id]);
                    $modal->setIsReturnRequest(1);
                    $modal->save();
                   
                    $modelOrder = (new Xdelivery_Model_Orders()); 
                    $order = $modelOrder->findOrderById($order_id); 
                    $status = (new Xdelivery_Model_Orders)->getStatus();
                    
                    $order['order_status']  = p__('xdelivery', 'Return request');
                    $mailParams = [];
                    $mailParams['order'] = $order;
                    $mailParams['sender_email'] = $order['store_email'];
                    $mailParams['sender_name'] = $order['store_name'];
                    $mailParams['email'] = $order['customer_email'];
                    $mailParams['order'] = $order;
                    $mailParams['to'] = 'customer';
                    $mailParams['subject'] = p__('xdelivery', '%s Order #%s %s!', $this->getApplication()->getName(), $mailParams['order']['order_number'], $mailParams['order']['order_status']);
                    $mailParams['headline'] = p__('xdelivery', 'We have processed the %s!',  $mailParams['order']['order_status']);
                    $mailParams['message'] = '';
                    
                    $this->_sendCustomerOrderEmail($mailParams);
                    $message = p__('xdelivery', 'Order return request send successfully');
                      
                    $payload = [
                        'success' => true,
                        'message' => $message
                    ];

                    } catch (\Exception $e) {
                    $payload = [
                        'error' => true,
                        'message' => $e->getMessage(),
                    ];
                }

                $this->_sendJson($payload);
            }
    }




    /**
     * @param @array $order
     * @return bool
     * @throws Exception
     * @throws Zend_Session_Exception
     */
    private function cancelOrderWalletFuncations($param){

        $value_id = (new Ewallet_Model_Ewallet())->getCurrentValueId();
        $current_value_id = (new Xdelivery_Model_Xdelivery())->getCurrentValueId();
                $appId = $this->getApplication()->getId();
                $receiverId = $this->_getCustomerId(false);               
                $senderId = 0;
                $date = new Siberian_Date();
                $last_updated= $date->toString("yyyy-MM-dd HH:mm:ss");
              
               /* receiver  customer with debit amount */
                $receiverCustomerWallet = (new Ewallet_Model_Wallet())
                                ->find(['customer_id' => $receiverId ]);
                $receiverCustomerWalletId = $receiverCustomerWallet->getWalletId();
                $receiverCustomerCurrentBalance = number_format( $receiverCustomerWallet->getBalance() , 2,'.','');
                $receiverNewBalance = $receiverCustomerCurrentBalance + $param['total_amount'];
                $senderTransacrtionId = $senderCustomerWalletId.''.time();
                $senderWalletHistory = (new Ewallet_Model_History())
                            ->setWalletId($receiverCustomerWalletId)
                            ->setValueId($value_id)
                            ->setCustomerId($receiverId)
                            ->setAppId($appId)                          
                            ->setAmount($param['total_amount'])
                            ->setPreviousBalance($receiverCustomerCurrentBalance)
                            ->setNewBalance($receiverNewBalance)
                            ->setPaymentType('credit')
                            ->setPaymentMode('transfer')
                            ->setPaymentMethod('wallet')
                            ->setModuleValueId($current_value_id)
                            ->setStatus('success')
                            ->setTransacrtionId($senderTransacrtionId)
                            ->setShotRemark('Refund Order #'.$param['order_number'])
                            ->setOrderId($param['order_id'])
                            ->setLastUpdated($last_updated)
                            ->setToCustomerId(0)
                            ->setToStoreId($senderId)
                            ->setRemark('Refund Order #'.$param['order_number'])
                            ->setMethod('payToModule')
                            ->save();

                $receiverWallet = (new Ewallet_Model_Wallet())
                    ->find(['customer_id' => $receiverId ])
                    ->setBalance($receiverNewBalance)
                    ->setLastUpdated($last_updated)
                    ->save();
        return true;
     }

    
   /**
     * @param bool $throw
     * @return mixed|null
     * @throws Exception
     * @throws Zend_Session_Exception
     */
    private function _getCustomerId($throw = true) {
        $request = $this->getRequest();
        $session = $this->getSession();
        $customerId = $session->getCustomerId();
        if ($throw && empty($customerId)) {
            throw new Exception(p__('xdelivery', 'Customer login required!'));
        }
        return $customerId;
    }


     private function _sendCustomerOrderEmail($param){
        if(empty($param)){
            return false;
        }

        $config = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $sender = $config->getOption('sendermail');
        $layout = $this->getLayout()->loadEmail('xdelivery', 'xdelivery_new_order');

        $layout->getPartial('content_email')
            ->setEmail($param['sender_email'])
            ->setHeadline($param['headline'])
            ->setMessage($param['message'])
            ->setOrder($param['order'])
            ->setTo($param['to'])
            ->setApp($this->getApplication()->getName())->setIcon($this->getApplication()->getIcon());

        $content = $layout->render(); 
        $mail = new Siberian_Mail();
        $mail->_is_default_mailer = false;
        $mail->setBodyHtml($content);
        $mail->setFrom($param['sender_email'], $this->getApplication()->getName());
        $mail->_sender_name = $param['sender_name'].' via '.$this->getApplication()->getName();
        $mail->addTo($param['email'], "");
        $mail->setSubject($param['subject']);

        $mail->send();

    }
     private function _sendEmail($param){
        if(empty($param)){
            return false;
        }

        $config = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $sender = $config->getOption('sendermail');
        
        $mail = new Siberian_Mail();
        $mail->_is_default_mailer = false;
        $mail->setBodyHtml($param['message']);
        $mail->setFrom($param['store_email'], $this->getApplication()->getName());
        $mail->_sender_name = $param['store_name'].' via '.$this->getApplication()->getName();
        $mail->addTo($param['customer_email'], "");
        $mail->setSubject($param['subject']);
        $mail->send();
    }


    public function updateProductInventory($product_id, $qty, $type = 'increase'){

        $product = (new Xdelivery_Model_Products());
        $product->find($product_id);

        if ($product->getId()) {
            if($product->getManageStock()){
                $qty = (integer) $qty;
                $product_qty = (integer) $product->getQty();
                
                if($type == 'increase'){
                    $new_qty = $product_qty + $qty;
                }
                if($type == 'descrease'){
                    $new_qty = $product_qty - $qty;
                }

                $product->setQty($new_qty);
                $product->save();
            }
        }
    }


        /*get paypal status*/
    public function GetPaypalCheckoutDetails($order_id, $token, $payerId ){
        $modelOrder = (new Xdelivery_Model_Orders()); 
        $order =  $modelOrder->findOrderById($order_id);
         
        /*check paypal access setting*/
        $gatewayModel = (new Xdelivery_Model_PaymentMethod())->find(['method_type' => 'paypal', 'value_id' => $order['value_id']]);

        if ($gatewayModel->getPaymentMode() == 'sandbox') {
            $paypal_api_user = $gatewayModel->getSandboxusername();
            $paypal_api_user_pwd = $gatewayModel->getSandboxpassword();
            $paypal_api_user_signature = $gatewayModel->getSandboxsignature();
            $api_url = "https://api-3t.sandbox.paypal.com/nvp";
         } else {
            $paypal_api_user = $gatewayModel->getUsername();
            $paypal_api_user_pwd = $gatewayModel->getPassword();
            $paypal_api_user_signature = $gatewayModel->getSignature();
            $api_url = "https://api-3t.paypal.com/nvp";
        }

        $params = array(
            'METHOD' => 'GetExpressCheckoutDetails',
            'VERSION' => '124.0',
            'USER' => $paypal_api_user,
            'PWD' => $paypal_api_user_pwd,
            'SIGNATURE' => $paypal_api_user_signature,
            'TOKEN' => $token
        );

        $response = $this->setCurl($params, $api_url);
        $response['value_id'] =  $order['value_id'];
        
        if ($response['CHECKOUTSTATUS'] === 'PaymentActionCompleted') {
            return true;
        }

        if($response['CHECKOUTSTATUS'] != 'PaymentActionCompleted'){
            $params = array(
                'METHOD' => 'DoExpressCheckoutPayment',
                'VERSION' => '124.0',
                'USER' => $paypal_api_user,
                'PWD' => $paypal_api_user_pwd,
                'SIGNATURE' => $paypal_api_user_signature,
                'TOKEN' => $token,
                'PAYERID' => $payerId,
                'PAYMENTREQUEST_0_AMT' => $order['total_amount'],
                'PAYMENTREQUEST_0_CURRENCYCODE' => Core_Model_Language::getCurrentCurrency()->getShortName()
            );

            $result2 = $this->setCurl($params, $api_url);
            if ($result2) {
                $response['result'] = $result2;
                return $response;
            } else {
                return $response;
            }
        }       
    }


    public function setCurl($params, $api_url) {

        $params = http_build_query($params);
        $curl = curl_init();
        $curlParams = array(
            CURLOPT_URL             => $api_url,
            CURLOPT_POST            => 1,
            CURLOPT_POSTFIELDS      => $params,
            CURLOPT_RETURNTRANSFER  => 1,
            CURLOPT_VERBOSE         => 1,
            CURLOPT_SSL_VERIFYPEER  => false, //si certificat SSL => true
            CURLOPT_SSL_VERIFYHOST  => false, //si certificat SSL => 2
        );                
        curl_setopt_array($curl, $curlParams);
        $response = curl_exec($curl);
        if($response){
            $responseArray = array();
            parse_str($response, $responseArray);
            return $responseArray;
        }
    }



    public function print($order_id) {
       
        try {

            if(!class_exists("Migaprintv2_Model_Config")) {
               return false;  
            }
            $status = (new Xdelivery_Model_Orders)->getStatus();
            $application = $this->getApplication();
            $modelOrder = (new Xdelivery_Model_Orders()); 
            $order = $modelOrder->findOrderById($order_id);
            $items = (new Xdelivery_Model_OrderItems())->findAll(['order_id' => $order_id])->toArray();
   
            $postvars = array('app_id' => $application->getId(),
                'key' => $application->getKey(),
                'commerce' => 'Xdelivery',
                'order[store_id]' => $order['store_id'],
                'order[customer_id]' => $order['customer_id'],
                'order[sib_order_id]' => $order['order_id'],
                'order[order_number]' => $order['order_number'],
                'order[order_payment_method]' => $order['label_name'],
                'order[order_delivery_method]' => $order['delivery_method'],
                'order[order_customer_firstname]' => $order['customer_firstname'],
                'order[order_customer_lastname]' => $order['customer_lastname'],
                'order[order_customer_email]' => $order['customer_email'],
                'order[order_customer_street]' => $order['customer_street'],
                'order[order_customer_postcode]' => $order['customer_postcode'],
                'order[order_customer_city]' => $order['customer_city'],
                'order[order_customer_phone]' => $order['customer_phone'],
                'order[order_deliverytime]' =>  date('Y-m-d', $order['delivery_date']).' '.date(' H:i:s', $order['delivery_time']),
                'order[order_delivery_note]' => $order['notes'],
                'order[order_total]' => $order['total_amount'],
                'order[order_paid_amount]' => $order['total_amount'],
                'order[order_date]' => $order['created_at'],
                'order[order_module]' => '3',
                'order[order_commerce]' => 'Xdelivery',
                'order[order_total]' => $order['total_amount'],
                //'order[order_status]' => $status[$order['order_status']]
            );

            foreach ($items as $key => $value) {
                $postvars['products[sib_product_id]['.$key.']'] = $value['product_id'];
                $postvars['products[product_name]['.$key.']'] = $value['name'];
                $postvars['products[product_quantity]['.$key.']'] = $value['qty'];
                $postvars['products[product_total]['.$key.']']  = $value['price_incl_tax'];

                $optionsNew = [];
                foreach (json_decode($value['choices']) as $ckey => $cvalue) {
                    $cData = [  'option_id' => $cvalue->id,
                                'name' => $cvalue->name, 
                                'base_price' => $cvalue->price,
                                'price' =>  $cvalue->price,
                                'price_incl_tax' => $cvalue->price,
                                'qty' => $value['qty']
                            ];
                    $optionsNew[] = $cData;
                }

                $varients = (new Xdelivery_Model_ProductVariant())->getProductVariantValues($value['product_id']);

                $choiceNew = [];
                foreach ($varients as $vKey => $vValue) {
                    $vData = ['format_id' => $order_id.''.$value['product_id'],
                              'format_name' => $vValue['attribute_name'].': '.$vValue['value_name'],
                          ];
                    $choiceNew[] = $vData;
                }

                $postvars['products[product_choices]['.$key.']']  = serialize($choiceNew);
                $postvars['products[product_options]['.$key.']']  = serialize($optionsNew);
                $postvars['products[product_format]['.$key.']']  = 'a:0:{}';
            }
          
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $this->getRequest()->getBaseUrl()."/migaprintv2/services/neworder");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_ENCODING, '');
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postvars);
            $response = curl_exec($curl);
           
            if(curl_errno($curl)){
                return false;
            }

            curl_close($curl);

            $response = json_decode($response, true);
           
            if(!empty($response['error'])){
                return false;
            }else{ 
                return true;
            } 

        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
    public function statusOrderUpdateAction(){
        if ($params = $this->getRequest()->getBodyParams()) {            
                try {
                    // dd('ok');
                    // Initialize params
                    $order_id=$params['id'];
                    $status=$params['status'];
                    // Update order status
                    $customerId = $this->_getCustomerId(true);
                    $customer = new Customer_Model_Customer();
                            $customer->find($customerId);
                   $modal = (new Xdelivery_Model_Orders())
                        ->find(['id' => $order_id]);
                    $modal->setStatus($status);
                    $modal->save();
                    
                    $modelOrder = (new Xdelivery_Model_Orders()); 
                    $order = $modelOrder->findOrderById($order_id); 

                    $settingModel = (new Xdelivery_Model_Settings())->find(['value_id' => $order['value_id'] ]);
                    $settings = $settingModel->getData();
                    $currency = $this->getApplication()->getCurrency();
                    //Update wallet: Refund and cancele or failed
                    if($order['payment_status'] == 'success' && ($order['order_status'] == 'canceled' || $order['order_status'] == 'refunded' || $order['order_status'] == 'failed')){
                        $modalTxn = (new Xdelivery_Model_OrderTransactions());
                        $modalTxn->setOrderId($order_id);
                        $modalTxn->setStatus('refunded');
                        $modalTxn->setAmount($order['total_amount']);
                        $modalTxn->setPaymentMethodId($order['payment_method_id']);
                        $modalTxn->setPaymentMethod($order['payment_method']);
                        $modalTxn->save();                    
                        $this->cancelOrderWalletFuncations($order);
                    }

                    $order['order_date']=  date('jS M, Y g:i A', strtotime($order['created_at']));
                    $order['today_date']=  date('jS M, Y g:i A');
                    $order['currency'] = $this->getApplication()->getCurrency() ;
                    $order['delivery_date']=  date('jS M, Y', $order['delivery_date']);
                    $order['delivery_time']=  date('g:i A', $order['delivery_time']);
                    $items = (new Xdelivery_Model_OrderItems())->findAll(['order_id' => $order_id])->toArray();
                    $itemsJson = [];
                    // Updtae product invetory if order is canceled
                    foreach ($items as $key => $value) {
                        $value['choices'] = json_decode($value['choices']);
                        $value['price_with_currency'] = Xdelivery_Model_Utility::displayPrice($value['price'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                        $value['choice_price_with_currency'] = Xdelivery_Model_Utility::displayPrice($value['choice_price'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                        $value['total_with_currency'] = Xdelivery_Model_Utility::displayPrice($value['total'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                        if($order['order_status'] == 'canceled' || $order['order_status'] == 'refunded' || $order['order_status'] == 'failed'){
                            $this->updateProductInventory($value['product_id'], $value['qty'], 'increase');
                        }
                        $itemsJson[] = $value;
                    }
                    $order['items'] = $itemsJson;

                    $statusList = (new Xdelivery_Model_Orders)->getStatus();
                    $order['order_status'] = $statusList[$order['order_status']];
                    $order['order_status']  = p__('xdelivery', ucfirst($order['order_status']));

                      /*with currency */
                    $order['sub_amount_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['sub_amount'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                    $order['total_tax_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['total_tax'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                    $order['total_amount_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['total_amount'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                    $order['delivery_cost_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['delivery_cost'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                    $order['discount_amount_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['discount_amount'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                    $order['tips_amount_with_currency'] = Xdelivery_Model_Utility::displayPrice($order['tips_amount'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                    // (NOTIFICATION MANAFER)
                    $template = (new Xdelivery_Model_Notification())->find([
                        // 'notification_order_status'=> $order['order_status'],                            
                        'notification_order_status'=> $status,                            
                        'value_id' => $params['value_id']
                    ])->getData();  
                    // echo $params['value_id'];
                    // echo "<br>";
                    // echo $order['order_status'];
                    // print_r($template);exit;
                    $admins_list = (new Xdelivery_Model_Admins())->findAll(['store_id' => $order['store_id']])->toArray();   
                    $tagLabels = [
                        "@@order_no@@",
                        "@@order_date@@",
                        "@@order_status@@",
                        "@@customer_name@@",
                        "@@customer_phone@@",
                        "@@customer_email@@",                            
                        "@@total_amount@@",
                        "@@store_name@@",
                        "@@store_email@@",
                        "@@store_phone@@",
                        "@@admin_name@@",
                        "@@admin_email@@",
                        "@@admin_phone@@",
                        "@@app_name@@",
                        "@@payment_status@@",
                        "@@payment_mode@@",
                        "@@product_list@@",//Only Email
                        "@@delivery_info@@",//Only Email
                    ];                        
                    /* IMPORTENT
                        Admin Tags (admin_name, admin_email, admin_phone) 
                        If these tags are used in Customer message we will replace them with current user data(Admin)
                        (If the order status is changed from admin side then we will replace with first)
                        If these tags are used in Admin message we will replace them with corrosponding Admin data
                    */
                    $tagValues = [];
                    $tagValues['order_no'] = '#'.$order['order_number'];
                    $tagValues['order_date'] = $order['created_at'];
                    $tagValues['order_status'] = $order['order_status'];
                    $tagValues['customer_name'] = $order['customer_firstname'].' '.$order['customer_lastname'];
                    $tagValues['customer_phone'] = $order['customer_phone'];
                    $tagValues['customer_email'] = $order['customer_email'];                    
                    $tagValues['total_amount'] = $order['total_amount_with_currency'];
                    $tagValues['store_name'] = $order['store_name'];
                    $tagValues['store_email'] = $order['store_email'];
                    $tagValues['store_phone'] = $order['store_phone'];
                    $tagValues['app_name'] = $this->getApplication()->getName();  
                    $tagValues['payment_status'] = $order['payment_status'];
                    $tagValues['payment_mode'] = $order['payment_method'];                  
                    $tagValues['product_list'] = "";//Only Email                  
                    $tagValues['delivery_info'] = "";//Only Email        
                    $notification_logger=[];          
                    $notification_logger['value_id']=$order['value_id'];          
                    $notification_logger['order_id']=$order['order_id'];          
                    /*
                    EMAIL
                    */                    
                    if ($settings['is_enable_email']) { 
                        // Prepare template for Product List
                            // Start the table with a header                            
                            $productListHtml = '<table style="width: 100%; border-collapse: collapse;">';
                            $productListHtml .= '<thead>';
                            $productListHtml .= '<tr style="background-color: #4A90E2; color: #fff;">';
                            $productListHtml .= '<th style="padding: 8px; border: 1px solid #ddd;">Product Name</th>';
                            $productListHtml .= '<th style="padding: 8px; border: 1px solid #ddd;">Quantity</th>';
                            $productListHtml .= '<th style="padding: 8px; border: 1px solid #ddd;">Unit Price</th>';
                            $productListHtml .= '<th style="padding: 8px; border: 1px solid #ddd;">Total Price</th>';
                            $productListHtml .= '</tr>';
                            $productListHtml .= '</thead>';
                            $productListHtml .= '<tbody>';
                            foreach ($items as $item) {
                                $item['choices'] = json_decode($item['choices']);
                                $item['price_with_currency'] = Xdelivery_Model_Utility::displayPrice($item['price'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                                $item['choice_price_with_currency'] = Xdelivery_Model_Utility::displayPrice($item['choice_price'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                                $item['total_with_currency'] = Xdelivery_Model_Utility::displayPrice($item['total'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);                          
                               
                                $productListHtml .= '<tr>';
                                $productListHtml .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($item['name']) . '</td>';
                                $productListHtml .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($item['qty']) . '</td>';
                                $productListHtml .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($item['price_with_currency']) . '</td>';
                                $productListHtml .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($item['total_with_currency']) . '</td>';
                                $productListHtml .= '</tr>';
                            }                            
                            $productListHtml .= '<tr><td colspan="3" style="padding: 8px; border: 1px solid #ddd; text-align: right;">Item Total (Sub Total):</td>';
                            $productListHtml .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($order['sub_amount_with_currency']) . '</td></tr>';

                            $productListHtml .= '<tr><td colspan="3" style="padding: 8px; border: 1px solid #ddd; text-align: right;">Taxes:</td>';
                            $productListHtml .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($order['total_tax_with_currency']) . '</td></tr>';

                            $productListHtml .= '<tr><td colspan="3" style="padding: 8px; border: 1px solid #ddd; text-align: right;">Discount:</td>';
                            $productListHtml .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($order['discount_amount_with_currency']) . '</td></tr>';

                            $productListHtml .= '<tr><td colspan="3" style="padding: 8px; border: 1px solid #ddd; text-align: right;">Gratuity Amount:</td>';
                            $productListHtml .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($order['tips_amount_with_currency']) . '</td></tr>';

                            $productListHtml .= '<tr><td colspan="3" style="padding: 8px; border: 1px solid #ddd; text-align: right;">Delivery Charges:</td>';
                            $productListHtml .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($order['delivery_cost_with_currency']) . '</td></tr>';

                            $productListHtml .= '<tr><td colspan="3" style="padding: 8px; border: 1px solid #ddd; text-align: right; font-weight: bold;">Total:</td>';
                            $productListHtml .= '<td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">' . htmlspecialchars($order['total_amount_with_currency']) . '</td></tr>';
                            // Close the tbody and table after adding the additional details
                            $productListHtml .= '</tbody>';
                            $productListHtml .= '</table>';
                        $tagValues['product_list'] = $productListHtml;//@tag
                        $customer_delivery_info = json_decode($order['customer_info']);
                        $deliveryInfoHtml = '<p><strong>Date to Delivery:</strong> ' . htmlspecialchars($order['delivery_date']) . '</p>';
                        $deliveryInfoHtml .= '<p><strong>Time to Delivery:</strong> ' . htmlspecialchars($order['delivery_time']) . '</p>';
                        $deliveryInfoHtml .= '<p><strong>Phone:</strong> ' . htmlspecialchars($order['customer_phone']) . '</p>';
                        // $deliveryInfoHtml .= '<p><strong>Address:</strong> ' . htmlspecialchars($customer_delivery_info['customer_firstname']).' '.htmlspecialchars($customer_delivery_info['customer_street']).' '.htmlspecialchars($customer_delivery_info['customer_city']).' '.htmlspecialchars($customer_delivery_info['customer_postcode']). '</p>';
                        $tagValues['delivery_info'] = $deliveryInfoHtml;//@tag
                        if ($template['notification_is_customer'] && $template['notification_customer_is_email']) { //Eamil To Customer
                            $tagValues['admin_name'] = $customer->getFirstname().' '.$customer->getLasttname();
                            $tagValues['admin_email'] = $customer->getEmail();
                            $tagValues['admin_phone'] = $customer->getMobile();

                            $emailBody=$template['notification_customer_email_body'];  
                            $emailBody = str_replace($tagLabels, $tagValues, $emailBody);
                            $tagValues['subject'] = $template['notification_customer_email_subject'];
                            $tagValues['message'] = $emailBody;                  
                            $this->_sendEmail($tagValues);

                            $notification_logger['type']="email";
                            $notification_logger['status']='success';
                            $notification_logger['user_id']=$order['customer_id'];
                            $notification_logger['additional_info']=$tagValues['message'];
                            (New Xdelivery_Model_Notificationlogs)->setData($notification_logger)->save();
                        }
                        if ($template['notification_is_admin'] && $template['notification_admin_is_email']) { //Email to Admin
                            $emailBody=$template['notification_admin_email_body'];  
                            foreach ($admins_list as $key => $value) {
                                $admin = (new Customer_Model_Customer())->find(['customer_id' => $value['customer_id']]);                         
                                $tagValues['admin_name'] = $admin->getFirstname().' '.$admin->getLasttname();
                                $tagValues['admin_email'] = $admin->getEmail();
                                $tagValues['admin_phone'] = $admin->getMobile();

                                $emailBody = str_replace($tagLabels, $tagValues, $emailBody);
                                $tagValues['subject'] = $template['notification_admin_email_subject'];
                                $tagValues['message'] = $emailBody;                  
                                $this->_sendEmail($tagValues); 

                                $notification_logger['type']="email";
                                $notification_logger['status']='success';
                                $notification_logger['user_id']=$admin->getCustomerId();;
                                $notification_logger['additional_info']=$tagValues['message'];
                                (New Xdelivery_Model_Notificationlogs)->setData($notification_logger)->save();
                            }
                        }  
                    }
                    $tagValues['product_list'] = "";//@This tag is only for Email so reset it
                    $tagValues['delivery_info'] = "";//@This tag is only for Email so reset it
                    /*
                        Whatsender
                    */  
                    if ($settings['is_enable_whatsender']) {   
                        $pattern = '/^\+[0-9]{11}$/';// The pattern checks for a string starting with + and followed by 11 digits                     
                        $customer_phone = $order['customer_phone'];
                        $admin_phone = $order['store_phone'];
                        $sender = $settings['whatsender_sender'];
                        $whatsender_key = $settings['whatsender_key'];
                        //Whatsender to Customer
                        if ($template['notification_is_customer'] && $template['notification_customer_is_whatsender'] && !empty($template['notification_customer_whatsender_text'])) {
                            $tagValues['admin_name'] = $customer->getFirstname().' '.$customer->getLasttname();
                            $tagValues['admin_email'] = $customer->getEmail();
                            $tagValues['admin_phone'] = $customer->getMobile();

                            $customer_what_message=$template['notification_customer_whatsender_text'];  
                            $customer_what_message = str_replace($tagLabels, $tagValues, $customer_what_message);                            
                            $wParams = ['phone' => $customer_phone, 'message' => addslashes($customer_what_message), 'sender' => $sender];
                            $whatsapp_respone = (new Xdelivery_Model_Whatsender)->sent($whatsender_key, $wParams);
                            $decodedResponse = json_decode($whatsapp_respone, true);
                            if ($decodedResponse['success'] === true) {
                                $notification_logger['status']='success';  
                                $notification_logger['additional_info']=$customer_what_message; 
                            } else {
                                $notification_logger['status']='error';  
                                $notification_logger['additional_info']=$decodedResponse['message']; 
                            }
                            $notification_logger['type']="whatsender";                            
                            $notification_logger['user_id']=$order['customer_id'];                                                                         
                            (New Xdelivery_Model_Notificationlogs)->setData($notification_logger)->save();
                        }
                        //Whatsender to Admin 
                        if ($template['notification_is_admin'] && $template['notification_admin_is_whatsender'] && !empty($template['notification_admin_whatsender_text'])) {                          
                            $customer_what_message=$template['notification_customer_whatsender_text'];  
                            foreach ($admins_list as $key => $value) {
                                $admin = (new Customer_Model_Customer())->find(['customer_id' => $value['customer_id']]);                         
                                $tagValues['admin_name'] = $admin->getFirstname().' '.$admin->getLasttname();
                                $tagValues['admin_email'] = $admin->getEmail();
                                $tagValues['admin_phone'] = $admin->getMobile();

                                $customer_what_message = str_replace($tagLabels, $tagValues, $customer_what_message);                            
                                $wParams = ['phone' => $customer_phone, 'message' => addslashes($customer_what_message), 'sender' => $sender];
                                $whatsapp_respone = (new Xdelivery_Model_Whatsender)->sent($whatsender_key, $wParams);
                                $decodedResponse = json_decode($whatsapp_respone, true);
                                if ($decodedResponse['success'] === true) {
                                    $notification_logger['status']='success';  
                                    $notification_logger['additional_info']=$customer_what_message; 
                                } else {
                                    $notification_logger['status']='error';  
                                    $notification_logger['additional_info']=$decodedResponse['message']; 
                                }
                                $notification_logger['type']="whatsender";                            
                                $notification_logger['user_id']=$order['customer_id'];                                                                         
                                (New Xdelivery_Model_Notificationlogs)->setData($notification_logger)->save();

                                $notification_logger['type']="email";
                                $notification_logger['status']='success';
                                $notification_logger['user_id']=$admin->getCustomerId();;
                                $notification_logger['additional_info']=$tagValues['message'];
                                (New Xdelivery_Model_Notificationlogs)->setData($notification_logger)->save();
                            }
                        }                
                    }
                    /*
                        Twillio
                    */  
                    
                    // dd($settings, $order, $customer, $tagLabels, $tagValues, $template);
                    $this->twilioMessage($settings, $order, $customer, $tagLabels, $tagValues, $template);
                    /*
                        PUSH
                    */  
                    $this->pushMessage($settings, $order, $customer, $tagLabels, $tagValues, $template);
                    

                   

                $payload = [
                        'success' => true,
                        'message' => p__('xdelivery', 'Update Status successfully'),
                        'order' => $order,                        
                        'values' => $values,                        
                        'whatsapp_status' => $whatsapp_status,                        
                    ];

                    } catch (\Exception $e) {
                    $payload = [
                        'error' => true,
                        'message' => $e->getMessage(),
                        'order' => $order,
                        'values' => $values,                        
                        'whatsapp_status' => $whatsapp_status,                        
                    ];
                }

                $this->_sendJson($payload);
            }
    }
    public function pushMessage($settings, $order, $customer, $tagLabels, $tagValues, $template){
        $admins_list = (new Xdelivery_Model_Admins())->findAll(['store_id' => $order['store_id']])->toArray();
        if ((int)$settings['order_status_push']) {   
            //push to Customer
            if ($template['notification_is_customer'] && $template['notification_customer_is_push'] && !empty($template['notification_customer_push_text']) && !empty($template['notification_customer_push_title'])) {
                $tagValues['admin_name'] = $customer->getFirstname().' '.$customer->getLasttname();
                $tagValues['admin_email'] = $customer->getEmail();
                $tagValues['admin_phone'] = $customer->getMobile();
                $notification_customer_push_text = addslashes(str_replace($tagLabels, $tagValues, $template['notification_customer_push_text']));
                // dd($notification_customer_push_text);
                $notification_customer_push_title = addslashes(str_replace($tagLabels, $tagValues, $template['notification_customer_push_title']));
                $response = $this->sendPush2([
                    'push_title'=> $notification_customer_push_title,
                    'push_message'=> $notification_customer_push_text,
                    'customer_id'=> $order['customer_id'],
                ]);
                if ($response['success'] === true) {
                    $notification_logger['status']='success';  
                    $notification_logger['additional_info']=$notification_customer_push_text; 
                } else {
                    $notification_logger['status']='error';  
                    $notification_logger['additional_info']=$response['message']; 
                }
                $notification_logger['order_id']=$order['order_id'];                            
                $notification_logger['value_id']=$order['value_id'];                            
                $notification_logger['type']="customer-push";                            
                $notification_logger['user_id']=$order['customer_id'];                                                                         
                (new Xdelivery_Model_Notificationlogs)->setData($notification_logger)->save();
            }
            //Push to Admin
            if ($template['notification_is_admin'] && $template['notification_admin_is_push'] && !empty($template['notification_admin_push_title']) && !empty($template['notification_admin_push_text'])) {
                foreach ($admins_list as $key => $value) {
                    $admin = (new Customer_Model_Customer())->find(['customer_id' => $value['customer_id']]);                         
                    $tagValues['admin_name'] = $admin->getFirstname().' '.$admin->getLasttname();
                    $tagValues['admin_email'] = $admin->getEmail();
                    $tagValues['admin_phone'] = $admin->getMobile();

                    $notification_admin_push_title = addslashes(str_replace($tagLabels, $tagValues, $template['notification_admin_push_title']));                            
                    $notification_admin_push_text = addslashes(str_replace($tagLabels, $tagValues, $template['notification_admin_push_text']));                            

                    $response = $this->sendPush2([
                        'push_title'=> $notification_admin_push_title,
                        'push_message'=> $notification_admin_push_text,
                        'customer_id'=> $value['customer_id'],
                    ]);
                    
                    if ($response['success'] === true) {
                        $notification_logger['status']='success';  
                        $notification_logger['additional_info']=$notification_admin_push_text; 
                    } else {
                        $notification_logger['status']='error';  
                        $notification_logger['additional_info']=$response['message']; 
                    }
                    $notification_logger['type']="admin-push";
                    $notification_logger['order_id']=$order['order_id'];                            
                    $notification_logger['value_id']=$order['value_id'];                             
                    $notification_logger['user_id']=$value['customer_id'];                                                                         
                    (new Xdelivery_Model_Notificationlogs)->setData($notification_logger)->save();
                }
            }                
        }
    }
    public function twilioMessage($settings, $order, $customer, $tagLabels, $tagValues, $template){
        // dd($template);
        $admins_list = (new Xdelivery_Model_Admins())->findAll(['store_id' => $order['store_id']])->toArray();
        if ((int)$settings['is_enable_sms'] && !empty($settings['twillio_sid'])&& !is_null($settings['twillio_sid']) && !empty($settings['twillio_sim_id']) && !is_null($settings['twillio_sim_id']) && !empty($settings['twillio_auth_token']) && !is_null($settings['twillio_auth_token'])) {   
            //push to Customer
            if ($template['notification_is_customer'] && $template['notification_customer_is_sms'] && !empty($template['notification_customer_sms_text'])) {
                $tagValues['admin_name'] = $customer->getFirstname().' '.$customer->getLasttname();
                $tagValues['admin_email'] = $customer->getEmail();
                $tagValues['admin_phone'] = $customer->getMobile();
                $notification_customer_sms_text = addslashes(str_replace($tagLabels, $tagValues, $template['notification_customer_sms_text']));
                // dd($order);
                $data['mobile'] = $order['customer_phone'];
                $data['sms_text'] = $notification_customer_sms_text;
                $data['twillio_sid'] = $settings['twillio_sid'];
                $data['twillio_sim_id'] = $settings['twillio_sim_id'];
                $data['twillio_token'] = $settings['twillio_auth_token'];
                
                $response = $this->sendSms($data);
                if ($response == 'OK') {
                    $notification_logger['status']='success';  
                    $notification_logger['additional_info']=$response; 
                } else {
                    $notification_logger['status']='error';  
                    $notification_logger['additional_info']=$response; 
                }
                $notification_logger['order_id']=$order['order_id'];                            
                $notification_logger['value_id']=$order['value_id'];                            
                $notification_logger['type']="customer-sms";                            
                $notification_logger['user_id']=$order['customer_id'];                                                                         
                (new Xdelivery_Model_Notificationlogs)->setData($notification_logger)->save();
            }
            //Push to Admin
            if ($template['notification_is_admin'] && $template['notification_admin_is_sms'] && !empty($template['notification_admin_sms_text'])) {
                foreach ($admins_list as $key => $value) {
                    $admin = (new Customer_Model_Customer())->find(['customer_id' => $value['customer_id']]);                         
                    $tagValues['admin_name'] = $admin->getFirstname().' '.$admin->getLasttname();
                    $tagValues['admin_email'] = $admin->getEmail();
                    $tagValues['admin_phone'] = $admin->getMobile();

                    $notification_admin_sms_text = addslashes(str_replace($tagLabels, $tagValues, $template['notification_admin_sms_text']));                            
                    $data['mobile'] = $admin->getMobile();
                    $data['sms_text'] = $notification_admin_sms_text;
                    $data['twillio_sid'] = $settings['twillio_sid'];
                    $data['twillio_sim_id'] = $settings['twillio_sim_id'];
                    $data['twillio_token'] = $settings['twillio_auth_token'];
            
                    // $response = $this->sendSms($data);
                    
                    
                    if ($response == 'OK') {
                        $notification_logger['status']='success';  
                        $notification_logger['additional_info']=$response; 
                    } else {
                        $notification_logger['status']='error';  
                        $notification_logger['additional_info']=$response; 
                    }
                    $notification_logger['type']="admin-sms";
                    $notification_logger['order_id']=$order['order_id'];                            
                    $notification_logger['value_id']=$order['value_id'];                             
                    $notification_logger['user_id']=$value['customer_id'];                                                                         
                    (new Xdelivery_Model_Notificationlogs)->setData($notification_logger)->save();
                }
            }                
        }
    }
    public function sendSms($data)
    {
        // print_r($data);exit;
        // dd($data, 'okoko');
        $to=$data['mobile'];                    
        $messagebody=$data['sms_text'];
        $sid=$data['twillio_sid'];
        $sim_id=$data['twillio_sim_id'];
        $token=$data['twillio_token'];
        try {
            if (class_exists('Twilio\Rest\Client'))
            {
                $client = new Twilio\Rest\Client($sid,$token);
                $balance=$client->balance->fetch()->balance;
                if ($balance>1) {
                    if ($to!='' && $to!=NULL) {
                        if (substr($to, 0, 1)!='+'&& substr($to, 0, 2)!='00') {
                            $to='+'.$to;
                        }
                    $message = $client->messages->create(
                        $to,
                        array( "from" => $sim_id,
                        "body" =>$messagebody)
                    );
                    // dd($message);
                    $response="OK";
                }else {
                    $response="Invalid Phone ".$to;
                } 
            }            
            }else {
            $response="Unable to Connect API";
            }
            
        } catch (\Throwable $th) {
            $response=$th->getMessage();
        }
        return $response;
        // dd($response);
    }
    public function sendPush2($data)
    {
        try {
            // dd($data);
            $application = $this->getApplication();
            $app_id = $application->getId();  
            // $application = (new \Application_Model_Application())->find($data['app_id']);
            // Required for the Message
            $values = [
                'app_id' => $app_id, // The application ID, required
                'value_id' => null, // The value ID, optional
                'title' => $data['push_title'], // Required
                'body' => $data['push_message'], // Required
                'send_after' => null,
                'delayed_option' => null,
                'delivery_time_of_day' => null,
                'is_for_module' => true, // If true, the message is linked to a module, push will not be listed in the admin
                'is_test' => false, // If true, the message is a test push, it will not be listed in the admin
                'open_feature' => false, // If true, the message will open a feature, it works with feature_id
                'feature_id' => null, // The feature ID, required if open_feature is true
                // 'big_picture'=>empty($data['cover']) ? null : '/' . $data['app_id'] . '/features/migaprontelevator/' . $data['cover']
            ];
            
            $scheduler = new Push2\Model\Onesignal\Scheduler($application);
            $scheduler->buildMessageFromValues($values);
            $scheduler->sendToCustomer($data['customer_id']); // This part will automatically sets the player_id and is_individual to true
            $payload = [
                'success' => true,
                'message' => p__('push2', 'Push sent'),
            ];
            $responce = ['success'=>true,'message_id'=>$scheduler->message->getId()];
        } catch (onesignal\client\ApiException $e) {
            $body = Siberian\Json::decode($e->getResponseBody());
            $payload = [
                'error' => true,
                'message' => "<b>[OneSignal]</b><br/>" . $body["errors"][0],
            ];
            $responce = ['success'=>false, 'message' => $body["errors"][0]];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
            $responce = ['success'=>false, 'message' => $e->getMessage()];
        }
        return $responce;
    }
    // public function sendSms($params = [])
    //   {                         
    //     $sid=$params['twillio_sid'];
    //     $sim_id=$params['twillio_sim_id'];
    //     $token=$params['twillio_auth_token'];
    //     $to=$params['phone'];
    //     $message=$params['message'];
    //        try {
    //             if (class_exists('Twilio\Rest\Client')){
    //                 $client = new Twilio\Rest\Client($sid,$token);                    
    //                 $message = $client->messages->create($to,array( "from" => $sim_id,"body" =>$message));                                                
    //                 $response="Success";
    //             }             
    //        } catch (\Throwable $th) {
    //          $response=$th->getMessage();
    //        }
    //       return $response;
    //   }
    // }
    public function saveadditionalinfoAction(){
        if ($params = $this->getRequest()->getBodyParams()) {          
                try {                               
                    $model = (new Xdelivery_Model_Orders())
                    ->find(['id' => $params['order_id']])                    
                    ->setAdminRemark($params['admin_remark'])
                    ->setTrackingType($params['tracking_type'])
                    ->setTrackingNumberUrl($params['tracking_number_url']);
                    $model->save();  
                $payload = [
                        'success' => true,
                        'message' => p__('xdelivery', 'Info successfully saved'),                                         
                    ];

                    } catch (\Exception $e) {
                    $payload = [
                        'error' => true,
                        'message' => $e->getMessage()                                            
                    ];
                }

                $this->_sendJson($payload);
            }
    }

}