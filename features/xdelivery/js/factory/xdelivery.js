/**
 * xdelivery factory
 */
angular
    .module('starter')
    .factory('Xdelivery', function (Application, Pages, $window, Customer,  $location, $state, $pwaRequest, $ionicPlatform, $session, Dialog, SB, $q, $ionicPopup, $translate) {
        var factory = {};
        factory.value_id = null;
        factory.store_id = null;
        factory.order_status = null;
        factory.settings = {};
        factory.carts = {};
        factory.StripeInstance =  null;
        factory.publishable_key = null;
        factory.isReadyPromise  = $q.defer();
         
        factory.setValueId = function (valueId) {
            factory.value_id = valueId;
            return factory;
        };

        factory.getValueId = function () {
            return factory.value_id;
        };

        factory.get_local = function(key){
            return $window.localStorage.getItem(key) || null;
        }

        factory.set_local = function(key, id) {
            $window.localStorage.setItem(key, id);
        }

        factory.unset_local = function(key){
            $window.localStorage.removeItem(key);
        }

        factory.isUndefined = function (thing) {
          return (typeof thing === "undefined");
        }

        factory.fetchStripeSettings = function () {
            return $pwaRequest.post("/paymentstripe/mobile_cards/fetch-settings");
        };

        factory.stripeSettings = function () {
            return $pwaRequest.post("/Xdelivery/mobile_view/fetch-stripe-settings", {
                urlParams: {
                   value_id: factory.value_id
                },
                cache: false,
                refresh: true
            });
        };

        factory.findAll = function () {
            return $pwaRequest.post('Xdelivery/mobile_view/find-all', {
                data: {
                   value_id: factory.value_id,
                   device_uid: $session.getDeviceUid(),
                },
                cache: false,
                refresh: true
            });
        };
        factory.updateOrderPaymentStatus = function (payment_status, order_id) {
            console.log(payment_status, order_id);
            return $pwaRequest.post('Xdelivery/mobile_order/updateorderpaymentstatus', {
                data: {
                   value_id: factory.value_id,
                   payment_status: payment_status,
                   order_id: order_id,
                },
                cache: false,
                refresh: true
            });
        };
        factory.statusOrderUpdate = function (order_status,order_id) {
            return $pwaRequest.post('Xdelivery/mobile_order/status-order-update', {
                data: {
                   value_id: factory.value_id,
                   status: order_status,
                   id: order_id,
                },
                cache: false,
                refresh: true
            });
        };
        factory.saveAdditionalInfo = function (additional_info) {
            return $pwaRequest.post('Xdelivery/mobile_order/saveadditionalinfo', {
                data: additional_info,
                cache: false,
                refresh: true
            });
        };


        factory.authorizationStripeSuccess = function (order_id, paymentIntentId) {
            return $pwaRequest.post('Xdelivery/mobile_order/authorization-stripe-success', {
                data: {
                   value_id: factory.value_id,
                   paymentIntentId: paymentIntentId,
                   order_id: order_id
                },
                cache: false,
                refresh: true
            });
        };

        factory.authorizationStripeError = function (order_id, paymentIntentId, error) {
            return $pwaRequest.post('Xdelivery/mobile_order/authorization-stripe-error', {
                data: {
                   value_id: factory.value_id,
                   paymentIntentId: paymentIntentId,
                   order_id: order_id,
                   error: error
                },
                cache: false,
                refresh: true
            });
        };

        factory.getCategoryByParentId = function (parent_id, offset = 0, store_id, refresh) {
            return $pwaRequest.post('xdelivery/mobile_product/get-category-by-parent-id', {
                data: {
                   value_id: factory.value_id,
                   parent_id: parent_id,
                   offset: offset,
                   store_id: store_id,
                   device_uid: $session.getDeviceUid()
                },
                cache: false,
                refresh: refresh
            });
        };

        factory.getProductsByCategoryId = function (category_id, offset = 0, store_id, refresh) {
            return $pwaRequest.post('xdelivery/mobile_product/get-products-by-category-id', {
                data: {
                   value_id: factory.value_id,
                   category_id: category_id,
                   offset:offset,
                   store_id: store_id
                },
                cache: false,
                refresh: refresh
            });
        };

        factory.popularSuggestions = function () {
            return $pwaRequest.post('xdelivery/mobile_view/popular-suggestions', {
                data: {
                   value_id: factory.value_id
                },
                cache: false,
                refresh: true
            });
        };

        factory.findProductsSearch = function (refresh, offset = 0, store_id, search = null) {
            return $pwaRequest.post('xdelivery/mobile_product/load-products', {
                data: {
                    value_id: factory.value_id,
                    offset: offset,
                    search: search,
                    store_id: store_id                 
                },
                refresh: refresh
            });
        };

        factory.cartProduct = function () {
            return $pwaRequest.post('xdelivery/mobile_cart/find-all', {
                data: {
                   value_id: factory.value_id,
                   device_uid: $session.getDeviceUid()
                },
                cache: false,
                refresh: true
            });
        };

        factory.findByProductId = function (product_id) {
            return $pwaRequest.post('xdelivery/mobile_product/find-by-product-id', {
                data: {
                   value_id: factory.value_id,
                   device_uid: $session.getDeviceUid(),
                   product_id: product_id                  
                },
                cache: false,
                refresh: true
            });
        };

        factory.findOptionsValuesById = function (option_id) {
            return $pwaRequest.post('xdelivery/mobile_product/find-options-values-by-id', {
                data: {
                   value_id: factory.value_id,                   
                   option_id: option_id                  
                },
                cache: false,
                refresh: true
            });
        };

        factory.saveForLater = function (product_id) {
            return $pwaRequest.post('xdelivery/mobile_cart/save-for-later', {
                data: {
                    value_id: factory.value_id,
                    device_uid: $session.getDeviceUid(),
                    product_id: product_id
                },
                cache: false,
                refresh: true
            });
        };

        factory.addToCart = function (param) {
            param.device_uid =  $session.getDeviceUid();
            param.store_id = factory.store_id;
            
            return $pwaRequest.post('xdelivery/mobile_cart/add-to-cart', {
                urlParams: {
                    value_id: factory.value_id                   
                },
                data: param,
                cache: false,
                refresh: true
            });
        }; 

        factory.decreaseQuantity = function (product_id, child_product_id, cart_key) {
            return $pwaRequest.post('xdelivery/mobile_cart/decrease-quantity', {
                data: {
                    value_id: factory.value_id,
                    device_uid: $session.getDeviceUid(),
                    product_id: product_id,
                    cart_key: cart_key,
                    child_product_id: child_product_id
                },
                cache: false,
                refresh: true
            });
        };

         factory.increaseQuantity = function (product_id, child_product_id, cart_key) {
            return $pwaRequest.post('xdelivery/mobile_cart/increase-quantity', {
                data: {
                    value_id: factory.value_id,
                    device_uid: $session.getDeviceUid(),
                    product_id: product_id,
                    cart_key: cart_key,
                    child_product_id: child_product_id
                },
                cache: false,
                refresh: true
            });
        };

        factory.saveForLater = function (product_id) {
            return $pwaRequest.post('xdelivery/mobile_cart/save-for-later', {
                data: {
                    value_id: factory.value_id,
                    device_uid: $session.getDeviceUid(),
                    product_id: product_id
                },
                cache: false,
                refresh: true
            });
        };

        factory.syncDeviceAndCustomerCart = function (device_uid) {
            return $pwaRequest.post('xdelivery/mobile_cart/sync-device-customer', {
                urlParams: {
                    value_id: factory.value_id,
                    device_uid: device_uid,
                },
                cache: false,
                refresh: true
            });
        };

        /*My Account*/
        factory.findWishlist = function (offset = 0 ) {
            return $pwaRequest.post('xdelivery/mobile_account/fetch-all-wishlist', {
                data: {
                   value_id: factory.value_id,
                   device_uid: $session.getDeviceUid(),
                   offset: offset
                },
                cache: false,
                refresh: true
            });
        }; 

        factory.findAddress = function () {
            return $pwaRequest.post('xdelivery/mobile_account/fetch-all-address', {
                data: {
                   value_id: factory.value_id,
                   device_uid: $session.getDeviceUid()
                },
                cache: false,
                refresh: true
            });
        };

        factory.saveAddress = function (param) {
            return $pwaRequest.post('xdelivery/mobile_account/save-address', {
                urlParams: {
                    value_id: factory.value_id
                },
                data: param,
                cache: false,
                refresh: true
            });
        };

        factory.deleteAddress = function (address_id) {
            return $pwaRequest.post('xdelivery/mobile_account/delete-address', {
                urlParams: {
                    value_id: factory.value_id,
                    address_id: address_id
                },                 
                cache: false,
                refresh: true
            });
        };

        factory.checkoutScreen = function (delivery_type) {
            return $pwaRequest.post('xdelivery/mobile_cart/checkout-screen', {
                urlParams: {
                    value_id: factory.value_id,
                    delivery_type: delivery_type                    
                },                 
                cache: false,
                refresh: true
            });
        };

        factory.checkoutScreenv2 = function (delivery_type, amount,param = [],is_delivery_applied) {
            console.log("Factory secreen 2");
            console.log(is_delivery_applied);
            param.amount = amount;
            param.is_delivery_applied = is_delivery_applied;
            return $pwaRequest.post('xdelivery/mobile_cart/checkout-screenv2', {
                urlParams: {
                    value_id: factory.value_id,
                    delivery_type: delivery_type                    
                }, 
                data: param,               
                cache: false,
                refresh: true
            });
        };

         factory.getSelectTime = function (delivery_type, delivery_date, store_id) {
            return $pwaRequest.post('xdelivery/mobile_cart/get-select-time', {
                urlParams: {
                    value_id: factory.value_id,
                    delivery_type: delivery_type,
                    delivery_date: delivery_date,
                    store_id: store_id                   
                },                 
                cache: false,
                refresh: true
            });
        };


        factory.paymentScreen = function (params) {
            var data = {
                        delivery_date: params.delivery_date, 
                        delivery_time: params.delivery_time,
                        address_id: params.address,
                        delivery_type: params.delivery,
                    };

            return $pwaRequest.post('xdelivery/mobile_cart/payment-screen', {
                urlParams: {
                    value_id: factory.value_id                    
                },
                data: data,               
                cache: false,
                refresh: true
            });
        }; 

        factory.orderSubmit = function (params) {
            params.is_webview = Application.is_webview;
            params.current_url = $location.url();
            params.BASE_PATH = BASE_PATH;

            return $pwaRequest.post('xdelivery/mobile_order/save', {
                urlParams: {
                    value_id: factory.value_id                    
                },
                data: params,               
                cache: false,
                refresh: true
            });
        };

        factory.findOrderById = function (order_id) {
            return $pwaRequest.post('xdelivery/mobile_order/find-order-by-id', {
                urlParams: {
                    value_id: factory.value_id,
                    order_id: order_id                   
                },                              
                cache: false,
                refresh: true
            });
        };

        factory.findAllOrders = function (days_range,order_status) {
            return $pwaRequest.post('xdelivery/mobile_order/find-all-order', {
                urlParams: {
                    value_id: factory.value_id,
                    days_range: days_range,
                    order_status: order_status
                },                              
                cache: false,
                refresh: true
            });
        };  
        factory.findAllOrderByCustomerId = function () {
            return $pwaRequest.post('xdelivery/mobile_order/find-all-order-by-customer-id', {
                urlParams: {
                    value_id: factory.value_id,                    
                },                              
                cache: false,
                refresh: true
            });
        };  
        factory.userType = function () {
            return $pwaRequest.post('xdelivery/mobile_view/user-type', {
                urlParams: {
                    value_id: factory.value_id,
                },                              
                cache: false,
                refresh: true
            });
        };  

        factory.updatePaymentStatus = function (params) {
            return $pwaRequest.post('xdelivery/mobile_order/update-payment-status', {
                urlParams: {
                    value_id: factory.value_id,
                },
                data: params,
                refresh: true
            });
        };   


        factory.cancelOrder = function (order_id) {
            return $pwaRequest.post('xdelivery/mobile_order/cancel', {
                urlParams: {
                    value_id: factory.value_id,
                    order_id: order_id
                },
                refresh: true
            });
        };  

        factory.returnOrder = function (order_id) {
            return $pwaRequest.post('xdelivery/mobile_order/return', {
                urlParams: {
                    value_id: factory.value_id,
                    order_id: order_id
                },
                refresh: true
            });
        };

        factory.findStore = function (store_id) {
            return $pwaRequest.post('xdelivery/mobile_store/find-store', {
                urlParams: {
                    value_id: factory.value_id,
                    store_id: store_id,
                    device_uid: $session.getDeviceUid()
                },
                refresh: true
            });
        };      
 

        factory.applyDiscont = function (discount_code,tips_amount,call_type) {//tips_amount by DN
            return $pwaRequest.post('xdelivery/mobile_cart/applypromo', {
                data: {
                   value_id: factory.value_id,
                   device_uid: $session.getDeviceUid(),
                   discount_code: discount_code,
                   tips_amount: tips_amount,
                   call_type: call_type
                },
                cache: false,
                refresh: true
            });
        };    
        
        factory.applyTips = function (tips_amount) {
            return $pwaRequest.post('xdelivery/mobile_cart/applytips', {
                data: {
                   value_id: factory.value_id,
                   device_uid: $session.getDeviceUid(),
                   tips_amount: tips_amount,
                },
                cache: false,
                refresh: true
            });
        };   

        return factory;

    });