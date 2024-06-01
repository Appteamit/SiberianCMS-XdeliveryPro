/**
 * xdelivery Home version 1 controllers
 */
angular.module('starter')
    .controller('XdeliveryCheckoutController', function (Dialog, Loader, $controller, Location, Customer, $rootScope, SB, $scope, $state, $stateParams, $translate, Xdelivery) {
        $scope.value_id = Xdelivery.value_id = $stateParams.value_id;
        $scope.is_loading = false;
        $scope.carts = {};
        $scope.payout = {};
        $scope.is_logged_in = Customer.isLoggedIn();
	    $scope.settings = Xdelivery.settings;
        $scope.store_id = Xdelivery.store_id;
        $scope.is_today = false;
        $scope.is_display_all_address = false;
        $scope.is_allow_select_date = false;
        $scope.is_allow_select_time = false;
        $scope.postParams = {};

        angular.extend(this, $controller('XdeliveryProductCommanController', {
            Dialog: Dialog,
            $rootScope: $rootScope,
            $scope: $scope,
            $stateParams: $stateParams
        }));

        $scope.isFloatOrInt = function (n) {
            return !isNaN(n) && n.toString().match(/^-?\d*(\.\d+)?$/);
        }

	    $scope.loadContent = function () {
            $scope.is_loading = true;
            $scope.carts = Xdelivery.carts;
          
            if($scope.carts.delivery == 'pickup'){
               $scope.carts.customer = Customer.customer;
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

                    if(!$scope.isFloatOrInt($scope.carts.delivery_time)){          
                          $scope.carts.main.total_amount =  $scope.carts.main.total_amount;            
                   
                    }
               }
            }
             // Assuming you have a controller where $scope is defined
            // Check if is_delivery_applied is undefined
            if (angular.isUndefined($scope.carts.is_delivery_applied)) {
                // Set it to false
                console.log("set it to false");
                $scope.carts.is_delivery_applied = false;
            } else if ($scope.carts.is_delivery_applied === false) {
                // If it's false, set it to true
                console.log("set it to true");
                $scope.carts.is_delivery_applied = true;
            }

            console.log('Cart 1', $scope.carts);
            console.log('Cart delivery', $scope.carts.address);
            console.log('Cart delivery', $scope.carts.is_delivery_applied);
            console.log('Cart 1', $scope.carts);
            $scope.postParams.store_id = $scope.store_id;
            Xdelivery
            .checkoutScreenv2($scope.carts.delivery, $scope.carts.main.total_amount,$scope.postParams,$scope.carts.is_delivery_applied)
            .then(function (data) {
                $scope.payout = data;
                if($scope.payout.address.length){
                    $scope.carts.address = $scope.payout.address[0].id;
                }
         
                //$scope.carts.delivery_date = data.today_date; 
                $scope.carts.delivery_date = '';
                if(Xdelivery.isUndefined($scope.carts.delivery_time)){
                    $scope.carts.delivery_time = '';
                }                              
                $scope.is_today = true;
                $scope.carts.main.total_amount_with_currency = data.total_amount_with_currency;
                
                if(data.shipping.is_shipping && $scope.carts.delivery != 'pickup'){
                    $scope.carts.shipping = data.shipping;
                    if (!$scope.carts.is_delivery_applied) {
                        $scope.carts.main.total_amount =  $scope.carts.main.total_amount + data.shipping.shipping_amount;
                    }
                    $scope.carts.is_delivery_applied=true;
                }else{
                    $scope.carts.main.total_amount = data.total_amount; 
                }
                console.log('Cart 2', $scope.carts);
             }, function (error) {
                $scope.is_loading = false;
                Dialog.alert($translate.instant("Error", "xdelivery"), error.message, $translate.instant("OK", "xdelivery") , -1);
            })
            .then(function () { // Finally!
                $scope.is_loading = false;
            });

	    };

       // $scope.loadContent();


         $scope.getLocation = function() {

            if (Location.isEnabled) {
                Location
                    .getLocation({timeout: 10000}, true)
                    .then(function (position) {
                        $scope.postParams.latitude = position.coords.latitude;
                        $scope.postParams.longitude = position.coords.longitude;
                        $scope.loadContent();
                    }, function () {
                        $scope.postParams.latitude = '';
                        $scope.postParams.longitude = '';
                        $scope.requestLocation();
                    });

            } else {
                $scope.postParams.latitude = '';
                $scope.postParams.longitude = '';
                $scope.requestLocation();
                $scope.loadContent();
            }
        }

        $scope.requestLocation = function(){
            Location.requestLocation(function () {
                $scope.loadContent();
                Loader.hide();
            }, function () {
                $scope.loadContent();
                Loader.hide();
            }); 
        }
        
        $scope.$on("$ionicView.beforeEnter", function(event, data) {  
           $scope.getLocation();
        });

        $scope.forTranslate = function(text){
            return $translate.instant(text, "xdelivery");
        }

        $scope.isTimeAvailable = function(time){  
            if($scope.carts.delivery_date == $scope.payout.today_date){
                $scope.is_today = true;  
                if((time < $scope.payout.current_time)){
                    return true;
                }else{
                    return false;
                }                 
            }
            $scope.is_today = false;
            return false;
        }

   $scope.deliveryDateChange = function() {
        $scope.is_time_loading = true;
        Xdelivery
            .getSelectTime($scope.carts.delivery, $scope.carts.delivery_date, $scope.postParams.store_id)
            .then(function (data) {
                $scope.payout.times = data.delivery_time;
             }, function (error) {
                $scope.is_time_loading = false;
                Dialog.alert($translate.instant("Error", "xdelivery"), error.message, $translate.instant("OK", "xdelivery") , -1);
            })
            .then(function () { // Finally!
                $scope.is_time_loading = false;
            });

           $scope.carts.delivery_time = '';
           $scope.isTimeAvailable();
        }

        $scope.showHideAddress = function() {
            if($scope.is_display_all_address){
                $scope.is_display_all_address = false;
            }else{
                $scope.is_display_all_address = true;
            }            
        }

        $scope.continueOrder = function() {
             if($scope.carts.delivery_date == '' && $scope.is_allow_select_date){
                Dialog.alert($translate.instant("Error", "xdelivery"), $translate.instant("Please select a date", "xdelivery"), $translate.instant("OK", "xdelivery") , -1);
                return true;
            }  
            if($scope.carts.delivery_time == '' && $scope.is_allow_select_time){
                Dialog.alert($translate.instant("Error", "xdelivery"), $translate.instant("Please select a time", "xdelivery"), $translate.instant("OK", "xdelivery") , -1);
                return true;
            }

            if($scope.carts.delivery == 'delivery' && ($scope.carts.address == '' || $scope.carts.address == null)){
                Dialog.alert($translate.instant("Error", "xdelivery"), $translate.instant("Please select a address", "xdelivery"), $translate.instant("OK", "xdelivery") , -1);
                return true;
            }
            $scope.carts.is_delivery_applied=false;
            Xdelivery.carts = $scope.carts;
            $state.go("xdelivery-payment", { value_id: $scope.value_id }, { reload: true } );
       }




       $scope.$on('modal.hidden', function() {
             $scope.loadContent();
        });

}).controller('XdeliveryPaymentController', function (Dialog, $timeout, Application, $window, Loader, $ionicLoading, Customer, $rootScope, SB, $scope, $state, $stateParams, $translate, Xdelivery) {
        $scope.value_id = Xdelivery.value_id = $stateParams.value_id;
        $scope.is_loading = false;
        $scope.carts = {};
        $scope.payout = {};
        $scope.settings = Xdelivery.settings;
        $scope.method_type = null;
        $scope.years = [];
        $scope.cardElement = null;


        $scope.loadContent = function () {
            $scope.is_loading = true;
            $scope.carts = Xdelivery.carts;
            console.log('Cart payment', $scope.carts);
            if(Xdelivery.isUndefined($scope.carts.main) || $scope.carts.main.cart_products.length == 0){
                Loader.show();
                $state
                    .go("home", { value_id: $scope.value_id }, { reload: true })
                    .then(function () {
                        Loader.hide();
                        $state.go("xdelivery-home", { value_id: $scope.value_id }, { reload: true });
                    });
            }

            $scope.carts.payment_method = '';
            Xdelivery
            .paymentScreen($scope.carts)
            .then(function (data) {
                $scope.payout = data;
                $scope.carts.payment_method = "";
            }, function (error) {
                $scope.is_loading = false;
                Dialog.alert($translate.instant("Error", "xdelivery"), error.message, $translate.instant("OK", "xdelivery") , -1);
            })
            .then(function () { // Finally!
                $scope.is_loading = false;
            });


           // if (typeof Stripe === "undefined") {
                  var stripeJS = document.createElement("script");
                stripeJS.type = "text/javascript";
                stripeJS.src = "https://js.stripe.com/v3/";
                stripeJS.onload = function () {
                    Xdelivery.isReadyPromise.resolve(Stripe);
                };
                document.body.appendChild(stripeJS);
          /*  } else {
                Xdelivery.isReadyPromise.resolve(Stripe);
            }*/
          
        };

        $scope.loadContent();

        $scope.updateOrderStatus = function(param){
            var order_id = param.order_id;
            Loader.show();        
            Xdelivery
                .updatePaymentStatus(param)
                .then(function (data) {
                    Loader.hide(); 
                    if(param.status == 'success'){
                         Dialog.alert($translate.instant("Success", "xdelivery"), $translate.instant("Payment has been successfully", "xdelivery"), $translate.instant("OK", "xdelivery"), -1, "xdelivery");  
                    }
                    if(param.status == 'failed'){
                         Dialog.alert($translate.instant("Error", "xdelivery"), $translate.instant("Payment has been failed", "xdelivery"), $translate.instant("OK", "xdelivery"), -1, "xdelivery");  
                    }                
                    
                    $state
                        .go("home", { value_id: $scope.value_id }, { reload: true })
                        .then(function () {
                            $state.go("xdelivery-home", { value_id: $scope.value_id }, { reload: true }).then(function () {
                                $state.go("xdelivery-orders", { value_id: $scope.value_id, order_id: order_id }, { reload: true })
                            });
                        });
             
                }, function (error) {
                    Loader.hide();
                    Dialog.alert($translate.instant("Error", "xdelivery"), error.message, $translate.instant("OK", "xdelivery"), -1, "xdelivery");
           });
        }

        $scope.populateURL = function(param) {
            if (!Application.is_webview) {
                $ionicLoading.show({content: 'Please Wait...', animation: 'fade-in', showBackdrop: true, maxWidth: 200, showDelay: 0 });
                var browser = $window.open(param.paypal_url, $rootScope.getTargetForLink(), 'location=yes');
                browser.addEventListener('loadstart', function(event) {
                    var newurl = event.url.split('/?__goto__=');                
                    var res = newurl[0].concat(newurl[1]);
                    if(/(confirm)/.test(event.url)) {
                        var url = event.url;
                        var first_split = url.split('?');
                        var second_split = first_split[1].split('&');
                        var token_split = second_split[0].split('=');
                        var tokenId = token_split[1];
                        var payer_split = second_split[1].split('=');
                        var payerId = payer_split[1];
                        if(tokenId !='' && payerId !='') {
                            $scope.cart = param;
                            $scope.cart.status = 'success';
                            $scope.cart.tokenId = tokenId;
                            $scope.cart.txn = payerId;
                            $scope.updateOrderStatus($scope.cart);
                            browser.close();
                            $ionicLoading.hide();
                        }else{
                            $scope.cart = param;
                            $scope.cart.status = 'failed';
                            $scope.cart.tokenId = '';
                            $scope.cart.txn = '';                           
                            $scope.updateOrderStatus($scope.cart);
                            browser.close();
                            $ionicLoading.hide();
                        }
                    } else if(/(cancel)/.test(event.url)) {
                        $scope.cart = param;
                        $scope.cart.status = 'failed';
                        $scope.cart.tokenId = '';
                        $scope.cart.txn = '';
                        $scope.updateOrderStatus($scope.cart);
                        browser.close();
                        $ionicLoading.hide();
                    }
                });

            } else {
                $window.location = param.paypal_url;
            }        
        }


        $scope.handleServerResponse = function(order_id){
            $state
            .go("home", { value_id: $scope.value_id }, { reload: true })
            .then(function () {
                $state.go("xdelivery-home", { value_id: $scope.value_id }, { reload: true }).then(function(){
                    Loader.hide();
                    $state.go("xdelivery-order-success", { value_id: $scope.value_id, order_id: order_id  }, { reload: true } );
                });
            });
        }

        $scope.placeOrder = function() {
            if($scope.carts.payment_method == ''){
                Dialog.alert($translate.instant("Error", "xdelivery"), $translate.instant("Please select a payment method!", "xdelivery"), $translate.instant("OK", "xdelivery") , -1);
                return true;
            }
            $scope.carts.is_delivery_applied=false;
            Xdelivery.carts = $scope.carts;
            $scope.carts.store_id = Xdelivery.store_id;
            Loader.show();
            Xdelivery
            .orderSubmit($scope.carts)
            .then(function (data) {
                console.log('res data --------->');
                Loader.hide();
                if(data && data.is_paypal && data.data.status == 'success'){
                    $scope.carts.order_id = data.order_id;
                    $scope.carts.paypal_url = data.paypal_url;
                    Xdelivery.set_local('PAYPAL_BEFORE_DATA', JSON.stringify($scope.carts));
                    $scope.populateURL($scope.carts);
                }else{
                    console.log('data ->', data);
                    if(data && data.is_stripe && data.stripeData.status == 'requires_action'){
                        
                        Xdelivery.StripeInstance.confirmCardPayment(
                            data.stripeData.client_secret
                          ).then(function (ccpResponse) {
                                Loader.show();
                                console.log('stripeResponse', ccpResponse);
                                if (ccpResponse.paymentIntent &&
                                    ccpResponse.paymentIntent.status === 'requires_capture') {
                                    // Continue to save card infos!
                                    console.log(' paymentIntent data 2-->', data);
                                    Xdelivery
                                        .authorizationStripeSuccess(data.order_id, data.stripeData.id)
                                        .then(function (asSuccess) {
                                            Loader.hide();
                                            console.log(asSuccess);
                                            $scope.handleServerResponse(data.order_id);                                         

                                        }, function (asError) {
                                            Loader.hide();
                                            console.log(asError);
                                            $scope.handleServerResponse(data.order_id);
                                        });
                                }

                                if (ccpResponse.error) {
                                    Loader.hide();
                                    Dialog.alert('Error', ccpResponse.error.message, 'OK', -1, 'payment_stripe');

                                    Xdelivery
                                        .authorizationStripeError(data.order_id, data.stripeData.id, ccpResponse.error)
                                        .then(function (aeSuccess) {
                                            console.log(aeSuccess);
                                            $scope.handleServerResponse(data.order_id);
                                        }, function (aeError) {
                                            console.log(aeError);
                                            $scope.handleServerResponse(data.order_id);
                                        });
                                    return;
                                }
                          });

                    }else{

                        $scope.handleServerResponse(data.order_id);
                    }
                }
 
            }, function (error) {
                Loader.hide();
                Dialog.alert($translate.instant("Error", "xdelivery"), error.message, $translate.instant("OK", "xdelivery") , -1);
            });

        }
  

        // Select Payment method
        $scope.selectPaymentMethod = function(method) {
            $scope.method_type = method.method_type;

            if(method.method_type == 'stripe'){

                if(!Xdelivery.publishable_key || Xdelivery.publishable_key == null){
                    Dialog.alert($translate.instant("Error", "xdelivery"), $translate.instant("This payment methods are temporarily unavailable", "xdelivery"), $translate.instant("OK", "xdelivery") , -1);
                    return false;
                }

                var current_year = new Date().getFullYear();
                for(var i = current_year; i <= current_year + 20; i++){
                    $scope.years.push({value: i + ''});
                }

            
            $timeout(function () {                
                try {  
                    Xdelivery.StripeInstance = Stripe(Xdelivery.publishable_key);
                   
                    var elements = Xdelivery.StripeInstance.elements();
                    var style = {
                        base: {
                            color: "#32325d",
                            fontFamily: "'Helvetica Neue', Helvetica, sans-serif",
                            fontSmoothing: "antialiased",
                            fontSize: "16px",
                            "::placeholder": {
                                color: "#aab7c4"
                            }
                        },
                        invalid: {
                            color: "#fa755a",
                            iconColor: "#fa755a"
                        }
                    };

                    $scope.cardElement = elements.create("card", {
                        hidePostalCode: true,
                        style: style
                    });

                    var saveElement = document.getElementById("xdelivery_save_element");
                    var displayError = document.getElementById("xdelivery_card_errors");
                    var displayErrorParent = document.getElementById("xdelivery_card_errors_parent");

                    saveElement.setAttribute("disabled", "disabled");

                    $scope.cardElement.removeEventListener("change");
                    $scope.cardElement.addEventListener("change", function (event) {
                        if (event.error) {
                            displayErrorParent.classList.remove("ng-hide");
                            displayError.textContent = event.error.message;
                            saveElement.setAttribute("disabled", "disabled");
                        } else {
                            displayErrorParent.classList.add("ng-hide");
                            displayError.textContent = "";
                            saveElement.removeAttribute("disabled");
                        }
                    });

                    $scope.cardElement.mount("#xdelivery_card_element");
                    
                    } catch (error) {
                        Dialog.alert($translate.instant("Error", "xdelivery"), error.message, $translate.instant("Ok", "xdelivery"));
                    }
                   }, 300);
                
           
            }
            
            $rootScope.$broadcast("refreshPageSize");
            $scope.$broadcast('scroll.infiniteScrollComplete');

        }


        $scope.payStripeNow = function(){
            Xdelivery.StripeInstance
                .createToken($scope.cardElement)
                .then(function (result) {
                    _stripeResponseHandler(result);
                });         
        }

        var _stripeResponseHandler = function (result) {
            if (result.error) {
                Dialog.alert("", result.error.message, "OK");
                $scope.is_loading = false;
                $scope.isProcessing = false;
                Loader.hide();
            } else {
                $scope.card = {
                    token: result.token.id,
                    last4: result.token.card.last4,
                    brand: result.token.card.brand,
                    exp_month: result.token.card.exp_month,
                    exp_year: result.token.card.exp_year,
                    exp: Math.round(+(new Date((new Date(result.token.card.exp_year, result.token.card.exp_month, 1)) - 1)) / 1000) | 0
                };
                 
                $scope.carts.stripe = $scope.card;
                $scope.placeOrder();
            }
        };

        // for range
        $scope.range = function(min, max, step) {
            step = step || 1;
            var input = [];
            for (var i = min; i <= max; i += step) input.push(i);
            return input;
        };

        //get stripe publishable keys
        $scope.getPublishableKey = function() {
            Xdelivery
            .stripeSettings()
            .then(function (payload) { 
                if(payload.success){
                    Xdelivery.publishable_key = payload.publishable_key;
                }
             }, function (error) {
                console.error(error.message);
            });
        };
        $scope.getPublishableKey();

      
}).controller('XdeliveryOrderSuccessController', function (Dialog, Loader, Customer, $rootScope, SB, $scope, $state, $stateParams, $translate, Xdelivery) {
        $scope.value_id = Xdelivery.value_id = $stateParams.value_id;
        $scope.order_id = $stateParams.order_id;
        $scope.is_loading = false;
        $scope.payout = {};
        $scope.carts = Xdelivery.carts;
        Xdelivery.carts = {};
        $scope.settings = Xdelivery.settings;
        
        $scope.loadContent = function () {
            $scope.is_loading = true;
            Xdelivery
            .findOrderById($scope.order_id)
            .then(function (data) {
                $scope.payout = data;

                 if(data.order.method_type == 'ewallet'){
                    Loader.show();
                    $state
                        .go("home", { value_id: $scope.value_id }, { reload: true })
                        .then(function () {
                            $state.go("xdelivery-home", { value_id: $scope.value_id }, { reload: true }).then(function(){
                                Loader.hide();
                                $state.go("xdelivery-ewallet", { value_id: $scope.value_id, order_id: $scope.order_id  }, { reload: true } );
                            });
                        });
                }

            }, function (error) {
                Loader.hide();
                $scope.is_loading = false;
                Dialog.alert($translate.instant("Error", "xdelivery"), error.message, $translate.instant("OK", "xdelivery") , -1);
            })
            .then(function () { // Finally!
                $scope.is_loading = false;
            });
        };

        $scope.loadContent();

        $scope.goHome = function(){
            Loader.show();
            $state
                .go("home", { value_id: $scope.value_id }, { reload: true })
                .then(function () {
                    Loader.hide();
                    $state.go("xdelivery-home", { value_id: $scope.value_id }, { reload: true });
                });
        }

        $scope.goToOrderDetails = function(){
            Loader.show();
            $state
                .go("home", { value_id: $scope.value_id }, { reload: true })
                .then(function () {
                    Loader.hide();
                    $state.go("xdelivery-home", { value_id: $scope.value_id }, { reload: true }).then(function () {
                        $state.go("xdelivery-orders", { value_id: $scope.value_id, order_id: $scope.order_id }, { reload: true });
                    });
                });
        }

}).controller('XdeliveryPaypalReturnController', function (Dialog, Xdelivery, SB, $ionicHistory, $ionicLoading, $location, Application, $ionicPlatform, $rootScope,$ionicScrollDelegate, Loader, $timeout, $ionicSlideBoxDelegate, Customer, $scope, $filter, $state, $stateParams, $translate, $ionicModal) {
        Loader.show();
        $scope.value_id = Xdelivery.value_id = $stateParams.value_id;
        $scope.cart = JSON.parse(Xdelivery.get_local('PAYPAL_BEFORE_DATA'));
        Xdelivery.unset_local('PAYPAL_BEFORE_DATA');

        $scope.updateOrderStatus = function(param){
            var order_id = param.order_id;
            Loader.show();        
            Xdelivery
                .updatePaymentStatus(param)
                .then(function (data) {
                    Loader.hide(); 
                    if(param.status == 'success'){
                         Dialog.alert($translate.instant("Success", "xdelivery"), $translate.instant("Payment has been successfully", "xdelivery"), $translate.instant("OK", "xdelivery"), -1, "xdelivery");  
                    }
                    if(param.status == 'failed'){
                         Dialog.alert($translate.instant("Error", "xdelivery"), $translate.instant("Payment has been failed", "xdelivery"), $translate.instant("OK", "xdelivery"), -1, "xdelivery");  
                    }                
                    
                    $state
                        .go("home", { value_id: $scope.value_id }, { reload: true })
                        .then(function () {
                            $state.go("xdelivery-home", { value_id: $scope.value_id }, { reload: true }).then(function () {
                                $state.go("xdelivery-orders", { value_id: $scope.value_id, order_id: order_id }, { reload: true })
                            });
                        });
             
                }, function (error) {
                    Loader.hide();
                    Dialog.alert($translate.instant("Error", "xdelivery"), error.message, $translate.instant("OK", "xdelivery"), -1, "xdelivery");
           });
        }

        $scope.loadContent = function() {
            Loader.show();
            /*Check web or mobile device*/
            /*Add in wallet cart clear*/
             if(Application.is_webview) {
              var url = $location.absUrl();
              if (url.indexOf("?") > -1) {
                 
                var first_split = url.split('?');
                var second_split = first_split[1].split('&');

                if (second_split.length > 1) {

                  var token_split = second_split[0].split('=');
                  var tokenId = token_split[1];

                  var payer_split = second_split[1].split('=');
                  var payerId = payer_split[1];

                   $scope.cart.status = 'success';
                   $scope.cart.tokenId = tokenId;
                   $scope.cart.txn = payerId;
                   $scope.updateOrderStatus($scope.cart);
                
                } else {
                   $scope.cart.status = 'failed';
                   $scope.cart.tokenId = '';
                   $scope.cart.txn = '';
                   $scope.updateOrderStatus($scope.cart);

                 }
              }
            }
        }

        Application.loaded.then(function () {
            Loader.show();
            $scope.loadContent();
        });
   
});