/**
 * xdelivery Home version 1 controllers
 */
angular.module('starter')
    .controller('XdeliveryHomeController', function (Dialog, Modal, $ionicModal, $interval, Loader, Customer, $rootScope, SB, $scope, $state, $stateParams, $translate, Xdelivery, $controller, Application, $cordovaBarcodeScanner, $timeout, $ionicPopup) {
        $scope.value_id = Xdelivery.value_id = $stateParams.value_id;
        $scope.is_loading = false;
        $scope.payout = {};
        $scope.settings = {};
        $scope.order_filter = {days_range_filter:'3',order_status_filter:'all'};

     angular.extend(this, $controller('XdeliveryProductCommanController', {
            Dialog: Dialog,
            $rootScope: $rootScope,
            $scope: $scope,
            $stateParams: $stateParams
        }));
    $scope.loadContent = function () {
            $scope.is_loading = true;          
            Xdelivery
            .findAll()
            .then(function (data) {
			    $scope.page_title = data.page_title;
                $scope.payout = data;
                $scope.settings = Xdelivery.settings = data.settings;
                if($scope.settings.is_food_app){
                    Xdelivery.store_id = data.store_id;
                }else{
                    Xdelivery.store_id = null;
                }
                
            }, function (error) {
                $scope.is_loading = false;
                Dialog.alert($translate.instant("Error"), error.message, $translate.instant("OK") , -1);
            })
            .then(function () { // Finally!
                $scope.is_loading = false;
            });
    };
    /**
     *category Image
     */
    $scope.CategoryImage = function (image) {      
        if (image != '' && image != null && image != "null") {
            return IMAGE_URL + 'images/application' + image;
        } else {
            return "./features/xdelivery/assets/media/default-image.png"
        }
    }; 
    /**
     *Slider Image 
     */
    $scope.SliderImage = function (image) {      
        if (image != '' && image != null && image != "null") {
            return IMAGE_URL + 'images/application' + image;
        } else {
            return "./features/xdelivery/assets/media/default-image.png"
        }
    }; 

    $scope.forTranslate = function(text){
        return $translate.instant(text, "xdelivery");
    }


   $scope.productScanCamera = function () {
    if (!Application.is_webview) {

        $cordovaBarcodeScanner.scan().then(function (barcodeData) {                   
            if (barcodeData.text !== '') {
                $timeout(function () {
                    var qrCode = barcodeData.text.replace('sendback:', ''); 
                    
                    if(qrCode != ''){
                        var productId = qrCode;                        
                        //verify scan
                       $scope.productDeails(productId); 

                    }else{
                         Dialog.alert($translate.instant("Error", "xdelivery"), $translate.instant('Invalid code.', "xdelivery") , $translate.instant('OK', "xdelivery"), -1);
                    }                            
                });

            }else{
                Dialog.alert($translate.instant("Error", "xdelivery") , $translate.instant("Unreadable QRCode, sorry", "xdelivery"), $translate.instant('OK', "xdelivery"), -1, "xdelivery");
            }
            
        }, function (error) {
            Dialog.alert($translate.instant("Error", "xdelivery"), 'An error occurred while reading the code.', $translate.instant('OK', "xdelivery"), -1);
        });

     } else {
        Dialog.alert($translate.instant("Info", "xdelivery") , $translate.instant("This will open the code scan camera on your device", "xdelivery"), $translate.instant("Ok", "xdelivery"), -1);
    }
};
// D: First identiy type of user Admin or Customer
$scope.payout={};
$scope.user_type='';
$scope.is_admin=false;
$scope.type_loaded=false;
$scope.order_max_id=0;
$scope.admin_name='';
$scope.order_status={};
$scope.order_queue_filter = {};
$scope.order_payment_status = {};
$scope.manage_order_status={update_order_status:'',update_payment_status:''};
$scope.autoloader=true;
$scope.alertsound=true;
$scope.toggleAutoload=function () {    
    $scope.autoloader = !$scope.autoloader;        
}
$scope.toggleAlertSound=function () {    
    $scope.alertsound = !$scope.alertsound;        
}
$scope.userType = function () {
    Xdelivery.userType()
      .success(function (data) {
		$scope.page_title = data.page_title;
        if (data.is_admin) {
            $scope.user_type='admin';   
            $scope.is_admin=true;         
            $scope.adminOrderQueue();
            $scope.admin_name=Customer.customer.firstname+" "+Customer.customer.lastname;
        }else{
            $scope.user_type='client';
            $scope.is_admin=false;
            $scope.loadContent(); // Load customer content
        }
      })
      .error(function () {
        $scope.is_loading = false;
      })
      .finally(function () {
        $scope.is_loading = false;
        $scope.type_loaded=true;
      });
  };
  $scope.userType();
  //content for admin
 
  $scope.isFirstCall = true; // Initialize the flag

  $scope.adminOrderQueue = function () {
      console.log("Loader Order Queue");
      Loader.show();
      if ($scope.isFirstCall) {
        $scope.is_loading = true;
      }
      Xdelivery
      .findAllOrders($scope.order_filter.days_range_filter, $scope.order_filter.order_status_filter)
      .then(function (data) {
            $scope.checkPrinterStatus();
          $scope.payout = data;
          $scope.settings = Xdelivery.settings = data.settings;
          if ($scope.isFirstCall) {
              // These lines will only run on the first call
              $scope.order_queue_filter = data.order_queue_filter;
              $scope.order_payment_status = data.order_payment_status;
              $scope.order_status = Xdelivery.order_status= data.order_status;
              // Set the flag to false after first call is complete
              $scope.isFirstCall = false;
              $scope.order_max_id=data.order_max_id;
          }else{
            console.log(data.order_max_id+"@"+$scope.order_max_id);
            if (data.order_max_id>$scope.order_max_id) {
              for (let index = $scope.order_max_id; index < data.order_max_id; index++) {
                  console.log("Loop");
                 $scope.playAudio();
              }
              $scope.order_max_id=data.order_max_id;
            }
          }
      }, function (error) {
          $scope.is_loading = false;
          Dialog.alert($translate.instant("Error", "xdelivery"), error.message, $translate.instant("OK", "xdelivery"), -1);
      })
      .then(function () { // Finally!
          if ($scope.isFirstCall) {
              $scope.order_filter.order_status_filter = 'all';
              $scope.order_filter.days_range_filter = '3';
          }
          $scope.is_loading = false;
          Loader.hide();
      });
  };
  

    var stop = $interval(function() {
        if ($scope.autoloader) {
            $scope.is_loading = true;
            Loader.show();
            $scope.adminOrderQueue();
        }
    }, 60 * 1000);
    $scope.$on("$destroy", function() {
        $interval.cancel(stop)
    });
    $scope.playAudio = function(base_url) {   
        if ($scope.alertsound) {
            $scope.audio = new Audio("https://sae.appteam.it" + "/app/local/modules/Xdelivery/resources/sounds/default_sound.mp3");     
            $scope.audio.play();
        }     
    };
    /**
     * details
     */
    $scope.checkPrinterStatus = function(){
        if (typeof sunmiInnerPrinter === "undefined" || sunmiInnerPrinter === null) {
            console.log("Sunmi Inner Printer is not available.");
            $scope.printerAvailable = false; // Set to false to disable the print button
        } else {
            $scope.printerAvailable = true; // Set to true to enable the print button
        }
    }
    $scope.checkPrinterStatus();
    $scope.printReceipet = function (order){
        console.log("Printer method called");
        console.log(order);
        order.is_managed=1;
        Xdelivery
        .findOrderById(order.order_id)
        .then(function (data) {
          $scope.order_info = data.order;
          $scope.settings = Xdelivery.settings;
          console.log("Settings");
          console.log($scope.settings);
          if ($scope.settings.enable_print_customer_details==1) {
            console.log("Customer Details");
        }else{
            console.log("Customer Details Not Enabled");
        }
                    
                    $scope.customerName="Name"+":"+$scope.order_info.customer_firstname+" "+$scope.order_info.customer_lastname+ "\n";
                    $scope.customerEamil="Eamil"+":"+$scope.order_info.customer_email+ "\n";
                    $scope.customerPhone=$scope.order_info.customer_phone ? "Phone"+":"+$scope.order_info.customer_phone+"\n" : "Phone"+":"+'N/A'+"\n";
                    $scope.customerDevider="--------------------------------" + "\n";
             
        
          console.log("Printable order details");
          console.log($scope.order_info.store_name);
                    var bold = [0x1b, 0x45, 0x1];
                    var binary = '';
                    var bytes = new Uint8Array(bold);
                    var len = bytes.byteLength;
                    for (var i = 0; i < len; i++) {
                        binary += String.fromCharCode(bytes[i])
                    };
                    var unbold = [0x1B, 0x45, 0x0];
                    var unbinary = '';
                    var unbytes = new Uint8Array(unbold);
                    var unlen = unbytes.byteLength;
                    for (var i = 0; i < unlen; i++) {
                        unbinary += String.fromCharCode(unbytes[i])
                    };
                     if (!$scope.printerAvailable) {
                        console.log("Sunmi Inner Printer is not available.");
                        return;
                    }
                    sunmiInnerPrinter.setAlignment(1,function (success) {
                        sunmiInnerPrinter.sendRAWData(btoa(binary));                              
                        sunmiInnerPrinter.printTextWithFont( $scope.order_info.store_name+"\n\n","gh",45,function (success) { 
                            sunmiInnerPrinter.printTextWithFont("ORDER RECEIPET" +"\n\n","gh",45,function (success) {                                 
                                // Order Info
                                sunmiInnerPrinter.sendRAWData(btoa(unbinary));                              
                                sunmiInnerPrinter.setAlignment(0,function (success) {
                                    sunmiInnerPrinter.printString("Order"+"#"+$scope.order_info.order_number+ "\n",function (success) {
                                        sunmiInnerPrinter.printString("Order Status"+":"+$scope.order_info.order_status +"\n",function (success) { 
                                            sunmiInnerPrinter.printString("Order At"+":" +$scope.order_info.order_date+ "\n",function (success) {
                                                sunmiInnerPrinter.printString("--------------------------------" + "\n",function (success) {
                                                    // Client or Customer Info  
                                                    if ($scope.settings.enable_print_customer_details==1) {
                                                        sunmiInnerPrinter.printString($scope.customerName+'@'+$scope.settings.enable_print_customer_details,function (success) {
                                                            sunmiInnerPrinter.printString($scope.customerEamil,function (success) {                                                            
                                                                sunmiInnerPrinter.printString($scope.customerPhone,function (success) {
                                                                    sunmiInnerPrinter.printString($scope.customerDevider,function (success) {
    
                                                                        // Itmes Info + Caluclation
                                                                        sunmiInnerPrinter.sendRAWData(btoa(binary));                              
                                                                        sunmiInnerPrinter.printColumnsText(["Name","Qty","Price"],[12, 9, 9],[0, 0, 2],function (success) {
                                                                        sunmiInnerPrinter.sendRAWData(btoa(unbinary));                              
                                                                            sunmiInnerPrinter.printString("--------------------------------" + "\n",function (success) {  
                                                                                // Print item list
                                                                                var products = $scope.order_info.items
                                                                                for (let i = 0; i < products.length; i++) {
                                                                                    sunmiInnerPrinter.printColumnsText(
                                                                                        [
                                                                                            products[i].name,
                                                                                            products[i].qty+"*"+products[i].price_incl_tax,
                                                                                            products[i].total,
                                                                                        ],
                                                                                        [18, 3, 9],
                                                                                        [0, 0, 2],
                                                                                        function (success) {});
                                                                                    }
                                                                                sunmiInnerPrinter.printString("--------------------------------" + "\n",function (success) {  
                                                                                    sunmiInnerPrinter.printColumnsText(["Total Before Tax","",$scope.order_info.sub_amount_with_currency],[18, 1, 11],[0, 0, 2],function (success) {
                                                                                        sunmiInnerPrinter.printColumnsText(["Taxes","",$scope.order_info.total_tax_with_currency],[18, 1, 11],[0, 0, 2],function (success) {
                                                                                            sunmiInnerPrinter.printColumnsText(["Delivery Charges","",$scope.order_info.delivery_cost_with_currency],[18, 1, 11],[0, 0, 2],function (success) {
                                                                                                sunmiInnerPrinter.printColumnsText(["Discount","",$scope.order_info.discount_cost_with_currency],[18, 1, 11],[0, 0, 2],function (success) {
                                                                                                    sunmiInnerPrinter.printColumnsText(["Tip","",$scope.order_info.tips_cost_with_currency],[18, 1, 11],[0, 0, 2],function (success) {
                                                                                                        sunmiInnerPrinter.printString("--------------------------------" + "\n",function (success) {  
                                                                                                            sunmiInnerPrinter.sendRAWData(btoa(binary));                              
                                                                                                            sunmiInnerPrinter.printColumnsText(["Total","",$scope.order_info.total_amount_with_currency],[18, 1, 11],[0, 0, 2],function (success) {
                                                                                                            sunmiInnerPrinter.sendRAWData(btoa(unbinary));                              
                                                                                                                sunmiInnerPrinter.printString("--------------------------------" + "\n",function (success) {  
                                                                                                                // Delivery Info + Payment status   
                                                                                                                    sunmiInnerPrinter.printString("Payment Method:" +$scope.order_info.label_name+ "\n",function (success) {                                                                             
                                                                                                                        sunmiInnerPrinter.printString("Payment Status:" +$scope.order_info.payment_status+ "\n",function (success) {                                                                             
                                                                                                                            sunmiInnerPrinter.printString("Delivery Method:" +$scope.order_info.delivery_method+ "\n",function (success) {                                                                             
                                                                                                                                // sunmiInnerPrinter.printString("Delivery Time:" +$scope.order_info.created_at+ "\n",function (success) {                                                                             
                                                                                                                                    sunmiInnerPrinter.printString("--------------------------------" + "\n",function (success) {  
                                                                                                                                        sunmiInnerPrinter.setAlignment(1,function (success) {  
                                                                                                                                            sunmiInnerPrinter.sendRAWData(btoa(binary));                              
                                                                                                                                            sunmiInnerPrinter.printTextWithFont("---"+"Check Closed"+"----"+ "\n","gh",36,function (success) {  
                                                                                                                                            sunmiInnerPrinter.sendRAWData(btoa(unbinary));                                                      
                                                                                                                                                sunmiInnerPrinter.setAlignment(0,function (success) {   
                                                                                                                                                    // Admin Remarks + Closing                                                                                                                                                                                                                   
                                                                                                                                                    sunmiInnerPrinter.printString("No Remarks"+"!"+"\n\n",function (success) {   
                                                                                                                                                        sunmiInnerPrinter.printString("--------------------------------" + "\n",function (success) {   
                                                                                                                                                            sunmiInnerPrinter.setAlignment(1,function (success) { 
                                                                                                                                                            sunmiInnerPrinter.sendRAWData(btoa(binary));                              
                                                                                                                                                                sunmiInnerPrinter.printTextWithFont("----"+"Thank You!"+"----"+ "\n","gh",36,function (success) {                                                                                                                                                
                                                                                                                                                                    sunmiInnerPrinter.printString("\n\n\n",function (success) {                                                                                                                                                
                                                                                                                                                                    }); 
                                                                                                                                                                }); 
                                                                                                                                                            }); 
                                                                                                                                                        }); 
                                                                                                                                                    }); 
                                                                                                                                                }); 
                                                                                                                                            }); 
                                                                                                                                        }); 
                                                                                                                                    }); 
                                                                                                                                // }); 
                                                                                                                            }); 
                                                                                                                        }); 
                                                                                                                    }); 
                                                                                                                });                                                                                       
                                                                                                            });                                                                                       
                                                                                                        });                                                                                       
                                                                                                    });                                                                                       
                                                                                                });                                                                                       
                                                                                            });                                                                                       
                                                                                        });                                                                                       
                                                                                    });                                                                                       
                                                                                });                                                                                       
                                                                            });                                                                                       
                                                                        });                                                                                       
                                                                    });                                                                                       
                                                                });                                                                                       
                                                            });                                                                                       
                                                        });  
                                                    }   else{
                                              
    
                                                                        // Itmes Info + Caluclation
                                                                        sunmiInnerPrinter.sendRAWData(btoa(binary));                              
                                                                        sunmiInnerPrinter.printColumnsText(["Name","Qty","Price"],[15, 6, 9],[0, 0, 2],function (success) {
                                                                        sunmiInnerPrinter.sendRAWData(btoa(unbinary));                              
                                                                            sunmiInnerPrinter.printString("--------------------------------" + "\n",function (success) {  
                                                                                // Print item list
                                                                                var products = $scope.order_info.items
                                                                                for (let i = 0; i < products.length; i++) {
                                                                                    sunmiInnerPrinter.printColumnsText(
                                                                                        [
                                                                                            products[i].name,
                                                                                            products[i].qty+"*"+products[i].price_incl_tax,
                                                                                            products[i].total,
                                                                                        ],
                                                                                        [15, 6, 9],
                                                                                        [0, 0, 2],
                                                                                        function (success) {});
                                                                                    }
                                                                                sunmiInnerPrinter.printString("--------------------------------" + "\n",function (success) {  
                                                                                    sunmiInnerPrinter.printColumnsText(["Total Before Tax","",$scope.order_info.sub_amount_with_currency],[18, 1, 11],[0, 0, 2],function (success) {
                                                                                        sunmiInnerPrinter.printColumnsText(["Taxes","",$scope.order_info.total_tax_with_currency],[18, 1, 11],[0, 0, 2],function (success) {
                                                                                            sunmiInnerPrinter.printColumnsText(["Delivery Charges","",$scope.order_info.delivery_cost_with_currency],[18, 1, 11],[0, 0, 2],function (success) {
                                                                                                sunmiInnerPrinter.printColumnsText(["Discount","",$scope.order_info.discount_cost_with_currency],[18, 1, 11],[0, 0, 2],function (success) {
                                                                                                    sunmiInnerPrinter.printColumnsText(["Tip","",$scope.order_info.tips_cost_with_currency],[18, 1, 11],[0, 0, 2],function (success) {
                                                                                                        sunmiInnerPrinter.printString("--------------------------------" + "\n",function (success) {  
                                                                                                            sunmiInnerPrinter.sendRAWData(btoa(binary));                              
                                                                                                            sunmiInnerPrinter.printColumnsText(["Total","",$scope.order_info.total_amount_with_currency],[18, 1, 11],[0, 0, 2],function (success) {
                                                                                                            sunmiInnerPrinter.sendRAWData(btoa(unbinary));                              
                                                                                                                sunmiInnerPrinter.printString("--------------------------------" + "\n",function (success) {  
                                                                                                                // Delivery Info + Payment status   
                                                                                                                    sunmiInnerPrinter.printString("Payment Method:" +$scope.order_info.label_name+ "\n",function (success) {                                                                             
                                                                                                                        sunmiInnerPrinter.printString("Payment Status:" +$scope.order_info.payment_status+ "\n",function (success) {                                                                             
                                                                                                                            sunmiInnerPrinter.printString("Delivery Method:" +$scope.order_info.delivery_method+ "\n",function (success) {                                                                             
                                                                                                                                // sunmiInnerPrinter.printString("Delivery Time:" +$scope.order_info.created_at+ "\n",function (success) {                                                                             
                                                                                                                                    sunmiInnerPrinter.printString("--------------------------------" + "\n",function (success) {  
                                                                                                                                        sunmiInnerPrinter.setAlignment(1,function (success) {  
                                                                                                                                            sunmiInnerPrinter.sendRAWData(btoa(binary));                              
                                                                                                                                            sunmiInnerPrinter.printTextWithFont("---"+"Check Closed"+"----"+ "\n","gh",36,function (success) {  
                                                                                                                                            sunmiInnerPrinter.sendRAWData(btoa(unbinary));                                                      
                                                                                                                                                sunmiInnerPrinter.setAlignment(0,function (success) {   
                                                                                                                                                    // Admin Remarks + Closing                                                                                                                                                                                                                   
                                                                                                                                                    sunmiInnerPrinter.printString("No Remarks"+"!"+"\n\n",function (success) {   
                                                                                                                                                        sunmiInnerPrinter.printString("--------------------------------" + "\n",function (success) {   
                                                                                                                                                            sunmiInnerPrinter.setAlignment(1,function (success) { 
                                                                                                                                                            sunmiInnerPrinter.sendRAWData(btoa(binary));                              
                                                                                                                                                                sunmiInnerPrinter.printTextWithFont("----"+"Thank You!"+"----"+ "\n","gh",36,function (success) {                                                                                                                                                
                                                                                                                                                                    sunmiInnerPrinter.printString("\n\n\n",function (success) {                                                                                                                                                
                                                                                                                                                                    }); 
                                                                                                                                                                }); 
                                                                                                                                                            }); 
                                                                                                                                                        }); 
                                                                                                                                                    }); 
                                                                                                                                                }); 
                                                                                                                                            }); 
                                                                                                                                        }); 
                                                                                                                                    }); 
                                                                                                                                // }); 
                                                                                                                            }); 
                                                                                                                        }); 
                                                                                                                    }); 
                                                                                                                });                                                                                       
                                                                                                            });                                                                                       
                                                                                                        });                                                                                       
                                                                                                    });                                                                                       
                                                                                                });                                                                                       
                                                                                            });                                                                                       
                                                                                        });                                                                                       
                                                                                    });                                                                                       
                                                                                });                                                                                       
                                                                            });                                                                                       
                                                                        });                                                                                                                                              
                                                    }                                              
                                                                                                                                       
                                                });                                                                                                                                                                                                                              
                                            });                                                                                       
                                        });                                                                                       
                                    });                                                                                       
                                });                                                                                       
                            });                                                                                       
                        });                                                                                       
                    });                                                                                       
                                                                           
                        
        }, function (error) {
            $scope.isLoading = false;
            Dialog.alert($translate.instant("Error", "xdelivery"), error.message, $translate.instant("OK", "xdelivery") , -1);
        })
        .then(function () { // Finally!
            $scope.isLoading = false;
             Loader.hide(); 
        });
   }
    $scope.orderDetails = function(order) {
        order.is_managed=1;
        $scope.isLoading = true;
        Loader.show();
        $ionicModal.fromTemplateUrl('features/xdelivery/assets/templates/l1/modal/order_manage.html', {
            scope: $scope,
            animation: 'slide-in-right-left'        
        }).then(function(modal) {

           Xdelivery
            .findOrderById(order.order_id)
            .then(function (data) {
              $scope.order_info = data.order;
              $scope.settings = Xdelivery.settings;
              if($scope.order_info.delivery_method == 'pickup'){ 
                 if($scope.settings.enable_to_pickup){
                      if($scope.settings.enable_date_to_pickup){
                          $scope.is_allow_select_date = true;
                      }
                      if($scope.settings.enable_time_to_pickup){
                          $scope.is_allow_select_time = true;
                      }            
                 }               
              }else{
                  if($scope.settings.enable_to_deliver){
                      if($scope.settings.enable_date_to_deliver){
                          $scope.is_allow_select_date = true;
                      }
                      if($scope.settings.enable_time_to_deliver){
                          $scope.is_allow_select_time = true;
                      }             
                 }
              }
              $scope.manage_order_status={update_order_status:data.order_status,update_payment_status:data.payment_status,order_id:order.order_id};
            }, function (error) {
                $scope.isLoading = false;
                Dialog.alert($translate.instant("Error", "xdelivery"), error.message, $translate.instant("OK", "xdelivery") , -1);
            })
            .then(function () { // Finally!
                $scope.isLoading = false;
                 Loader.hide(); 
            });
           
          $scope.modalOrderDetails = modal;
          $scope.modalOrderDetails.show();
        });
    };

    /**
     * close details modal 
     */
    $scope.closeModalOrderDetails = function (){
         $scope.modalOrderDetails.remove();
         $scope.adminOrderQueue();
    }
    // Order Mangement
    $scope.statusOrderUpdate = function (){
        console.log("Update Order Status");
        console.log($scope.manage_order_status);
        // confirmPopup
        var confirmPopup = $ionicPopup.confirm({
            scope:$scope,
            title: 'Are you sure you want to update order status for this Order?'
        });
        confirmPopup.then(function(res) {
            if(res) {
                $scope.is_loading = true;
                Loader.show();
                Xdelivery.statusOrderUpdate($scope.manage_order_status.update_order_status,$scope.manage_order_status.order_id)
                .success(function (data) {
                    Dialog.alert(
                        'Success',
                        data.message,
                        'Ok'
                    );
                    $scope.closeModalOrderDetails();
                    console.log("Order Sttus Updated");
                    console.log(data);
                })
                .error(function () {
                    $scope.is_loading = false;
                    Loader.hide();
                })
                .finally(function () {
                    $scope.is_loading = false;
                    Loader.hide();
                });
            } else {
            }
        });
        // confirmPopup end
         
         
    }
    $scope.updatePaymentStatus = function (){
         console.log("Update Order Status");
         console.log($scope.manage_order_status);
                 // confirmPopup
        var confirmPaymentPopup = $ionicPopup.confirm({
            scope:$scope,
            title: 'Are you sure you want to update payment status?'
        });
        confirmPaymentPopup.then(function(res) {
            if(res) {
                $scope.is_loading = true;
                Loader.show();
                // confirmPopup end
                Xdelivery.updatePaymentStatus($scope.manage_order_status)
                .success(function (data) {
                    Dialog.alert(
                        'Success',
                        data.message,
                        'Ok'
                    );
                    console.log("Order Sttus Updated");
                    console.log(data);
                })
                .error(function () {
                    $scope.is_loading = false;
                    Loader.hide();
                })
                .finally(function () {
                    $scope.is_loading = false;
                    Loader.hide();
                });
            } else {
            }
        });
        
    }

}).controller('XdeliveryCartController', function (Dialog, Loader, $timeout, Customer, $rootScope, SB, $scope, $state, $stateParams, $translate, Xdelivery, $ionicActionSheet, $session) {
        $scope.value_id = Xdelivery.value_id = $stateParams.value_id;
        $scope.is_loading = false;
        $scope.payout = {};
        $scope.settings = Xdelivery.settings;
        $scope.carts = Xdelivery.carts;        

        $scope.forTranslate = function(text){
             return $translate.instant(text, "xdelivery");
        }

        /**
         *Image
         */
        $scope.ProductImage = function (image) {      
            if (image != '' && image != null && image != "null") {
                return IMAGE_URL + 'images/application' + image;
            } else {
                return "./features/xdelivery/assets/media/default-image.png"
            }
        }; 

        $scope.loadContent = function (is_loading = true) {
                $scope.is_loading = is_loading;
                Xdelivery
                .cartProduct()
                .then(function (data) {
                    $scope.payout = data;
                    Xdelivery.carts.main = data;
                    if(data.in_out_of_stock){
                         Dialog.alert($translate.instant("Warring"), data.in_out_of_stock+' '+$translate.instant("Item out of stock, Please remove from your cart!"), $translate.instant("OK") , -1);
                    }
                    console.log('store_id', Xdelivery.store_id);
                    if(Xdelivery.store_id == null){
                        Xdelivery.store_id = data.store_id;
                    }

                }, function (error) {
                    $scope.is_loading = false;
                    Dialog.alert($translate.instant("Error"), error.message, $translate.instant("OK") , -1);
                })
                .then(function () { // Finally!
                    $scope.is_loading = false;
                });
        };

        $scope.loadContent(true); // Load content

        $scope.increaseQuantity = function (product_id, child_product_id, cart_key, current_qty) {
                if($scope.settings.max_qty_per_product < current_qty){
                    Dialog.alert($translate.instant("Error"), $translate.instant("Quantity of products in cart must be "+$scope.settings.max_qty_per_product+" or less", "xdelivery"), $translate.instant("OK") , -1);
                    return true;
                }

                Loader.show();
                Xdelivery
                .increaseQuantity(product_id, child_product_id, cart_key)
                .success(function (data) {
                     $scope.loadContent(false);
                }, function (error) {
                    Loader.hide();
                    Dialog.alert($translate.instant("Error"), error.message, $translate.instant("OK") , -1);
                }).error(function (error) {
                    Loader.hide();
                    Dialog.alert($translate.instant("Error"), error.message, $translate.instant("OK") , -1);
                })
                .then(function () { // Finally!
                    Loader.hide();
                });
        };
  

        $scope.decreaseQuantity = function (product_id, child_product_id, cart_key) {
                Loader.show();
                Xdelivery
                .decreaseQuantity(product_id, child_product_id, cart_key)
                .success(function (data) {
                     $scope.loadContent(false);
                }, function (error) {
                    Loader.hide();
                    Dialog.alert($translate.instant("Error"), error.message, $translate.instant("OK") , -1);
                }).error(function (error) {
                    Loader.hide();
                    Dialog.alert($translate.instant("Error"), error.message, $translate.instant("OK") , -1);
                }).then(function () { // Finally!
                    Loader.hide();
                });
        };
        
        $scope.login = function(){
            var oldDeviceUid = $session.getDeviceUid();
           if (!Customer.isLoggedIn()) {
                Customer.loginModal($scope, function () { 
                    Loader.show();
                    $timeout(function () {
                       
                        Xdelivery
                        .syncDeviceAndCustomerCart(oldDeviceUid)
                        .success(function (data) {
                             $scope.loadContent(true);
                        }, function (error) {
                            Loader.hide();
                            Dialog.alert($translate.instant("Error"), error.message, $translate.instant("OK") , -1);
                        }).error(function (error) {
                            Loader.hide();
                            Dialog.alert($translate.instant("Error"), error.message, $translate.instant("OK") , -1);
                        }).then(function () { // Finally!
                            Loader.hide();
                        });

                        $scope.is_logged_in = Customer.isLoggedIn();
                        $scope.customer = Customer.customer;
                        $state
                        .go('home')
                        .then(function () {
                            $state
                                .go("xdelivery-home", { value_id: $scope.value_id }, { reload: true })
                                .then(function () {
                                    Loader.hide();
                                    $state.go("xdelivery-cart", { value_id: $scope.value_id }, { reload: true });
                                });;
                        });
                    }, 1000);
                });
            } 
        }

        $scope.checkoutNow = function () {

            if(!Customer.isLoggedIn()){
                $scope.login(); return true;
            }
            Xdelivery.carts.is_delivery_applied = undefined;
            if($scope.settings.min_qty_shopping_cart > $scope.payout.total_item){
                Dialog.alert($translate.instant("Error"), $translate.instant("Minimum order quantity required", "xdelivery")+' '+$scope.settings.min_qty_shopping_cart, $translate.instant("OK") , -1);
                return true;
            }

            if($scope.settings.max_qty_shopping_cart < $scope.payout.total_item){
                Dialog.alert($translate.instant("Error"), $translate.instant("Maximum order quantity allowed is", "xdelivery")+' '+$scope.settings.min_qty_shopping_cart, $translate.instant("OK") , -1);
                return true;
            }

            if($scope.settings.min_order_value > $scope.payout.total_amount){
                Dialog.alert($translate.instant("Error"), $translate.instant("Minimum order amount required", "xdelivery")+' '+$scope.settings.min_order_value, $translate.instant("OK") , -1);
                return true;
            }

            if($scope.settings.max_order_value < $scope.payout.total_amount){
                Dialog.alert($translate.instant("Error"), $translate.instant("Maximum order amount allowed", "xdelivery")+' '+$scope.settings.min_order_value, $translate.instant("OK") , -1);
                return true;
            }

            if(!$scope.settings.enable_to_deliver && !$scope.settings.enable_to_pickup){
                Xdelivery.carts.delivery = 'delivery';
                $state.go("xdelivery-checkout", { value_id: $scope.value_id }, { reload: true } );
                return true;
            }

            if(!$scope.settings.enable_to_deliver && $scope.settings.enable_to_pickup){
                Xdelivery.carts.delivery = 'pickup';
                $state.go("xdelivery-checkout", { value_id: $scope.value_id }, { reload: true } );
                return true;
            }

            if($scope.settings.enable_to_deliver && !$scope.settings.enable_to_pickup){
                Xdelivery.carts.delivery = 'delivery';
                $state.go("xdelivery-checkout", { value_id: $scope.value_id }, { reload: true } );
                return true;
            }


            var buttonIndexing = [];
            var buttonPositon = 0;
            var sheetButtons =  [];

            if($scope.settings.enable_to_deliver){
                sheetButtons[buttonPositon] = { text: $translate.instant("Home Delivery", 'xdelivery') };
                buttonIndexing[buttonPositon] = 'delivery';
                buttonPositon = buttonPositon + 1;
            }

            if($scope.settings.enable_to_pickup){
                sheetButtons[buttonPositon] = { text: $translate.instant("Pick-up", 'xdelivery') };
                buttonIndexing[buttonPositon] = 'pickup';
                buttonPositon = buttonPositon + 1;
            }

            // Show the action sheet
            var hideSheet = $ionicActionSheet.show({
                buttons: sheetButtons,
                cancelText: $translate.instant("Cancel", "xdelivery"),
                titleText: $translate.instant("Your order", "xdelivery"),
                cancel: function () {
                    hideSheet();
                },
                buttonClicked: function (index) {
                    Xdelivery.carts.delivery = 'delivery';
                    
                    if(buttonIndexing[index] == "delivery"){
                       Xdelivery.carts.delivery = 'delivery';
                    }
                    
                    if(buttonIndexing[index] == "pickup"){
                        Xdelivery.carts.delivery = 'pickup';
                    }                    
                    $state.go("xdelivery-checkout", { value_id: $scope.value_id }, { reload: true } );
                }
            });
        }


        $scope.applyDiscont = function(call_type){
            console.log($scope.payout.discount_code);
               Loader.show();
                Xdelivery
                .applyDiscont($scope.payout.discount_code,$scope.payout.tips_amount,call_type) //tips_amount,call_type by DN
                .success(function (data) {
                    if(data.status){
                        $scope.payout = data.cart;
                        Xdelivery.carts.main = data.cart;
                        Dialog.alert($translate.instant("Success"), data.message, $translate.instant("OK") , -1);
                    }else{
                        Dialog.alert($translate.instant("Error"), data.message, $translate.instant("OK") , -1);
                    }
                }, function (error) {
                    Loader.hide();
                    Dialog.alert($translate.instant("Error"), error.message, $translate.instant("OK") , -1);
                }).error(function (error) {
                    Loader.hide();
                    Dialog.alert($translate.instant("Error"), error.message, $translate.instant("OK") , -1);
                }).then(function () { // Finally!
                    Loader.hide();
                });
        }

        $scope.applyTips = function(action){
               console.log($scope.payout.tips_amount);
               Loader.show();
            $timeout(function () {                            
                Xdelivery
                .applyTips($scope.payout.tips_amount)
                .success(function (data) {
                    if(data.status){
                        $scope.payout = data.cart;
                        Xdelivery.carts.main = data.cart;
                        Dialog.alert($translate.instant("Success"), data.message, $translate.instant("OK") , -1);
                    }else{
                        Dialog.alert($translate.instant("Error"), data.message, $translate.instant("OK") , -1);
                    }
                }, function (error) {
                    Loader.hide();
                    Dialog.alert($translate.instant("Error"), error.message, $translate.instant("OK") , -1);
                }).error(function (error) {
                    Loader.hide();
                    Dialog.alert($translate.instant("Error"), error.message, $translate.instant("OK") , -1);
                }).then(function () { // Finally!
                    Loader.hide();
                });

            }, 2500);
        }


    $scope.removeDiscont = function () {
       $scope.loadContent(false);
    }
    
});
