/**
 * Xdelivery Home version 1 controllers
 */
angular.module('starter')
    .controller('XdeliveryProductController', function (Dialog, Loader, $timeout, $controller, $ionicModal, Customer, $rootScope, SB, $scope, $state, $stateParams,$sce, $translate, Xdelivery) {
       angular.extend(this, $controller('XdeliveryProductCommanController', {
            $ionicModal: $ionicModal,
            Dialog: Dialog,
            $rootScope: $rootScope,
            $scope: $scope,
            $sce:$sce,
            $stateParams: $stateParams
        }));

        $scope.value_id = Xdelivery.value_id = $stateParams.value_id;               
        $scope.store_id = Xdelivery.store_id;
        $scope.category_id = $stateParams.category_id;
        $scope.is_loading = false;
        $scope.payout = {};
        $scope.settings = Xdelivery.settings;
        $scope.pull_to_refresh = false;
     
        $scope.loadContent = function () {
            if(!$scope.category_id){
                    return false;
            }
            $scope.payout.products = {};
            $scope.is_loading = true;
            Xdelivery
                .getProductsByCategoryId($scope.category_id, $scope.payout.products.length, $scope.store_id , false)
                .then(function (data) {
                    $scope.can_load_older_items = !!data.products.length;
                    $scope.payout = data;
                }, function (error) {
                    $scope.is_loading = false;
                    Dialog.alert($translate.instant("Error", "xdelivery"), error.message, $translate.instant("OK") , -1);
                })
                .then(function () { // Finally!
                    $scope.is_loading = false;
                });
        };

    $scope.loadContent(); // Load content

    $scope.loadMoreProducts = function() {
            Xdelivery
                .getProductsByCategoryId($scope.category_id, $scope.payout.products.length, $scope.store_id , false)
                .then(function (data) {
                    $scope.can_load_older_items = !!data.products.length;
                    $scope.payout.products = $scope.payout.products.concat(data.products);
                    $rootScope.$broadcast("refreshPageSize");

                }, function (error) {                    
                    $scope.can_load_older_items = false; 
                    Dialog.alert($translate.instant("Error", "xdelivery"), error.message, $translate.instant("OK") , -1);
                })
                .then(function () { // Finally!
                    $scope.$broadcast('scroll.infiniteScrollComplete');                    
                });
        }

}).controller('XdeliveryCategoryController', function (Dialog, Loader, Customer, $controller, $ionicModal, $rootScope, SB, $scope, $state, $stateParams, $translate, Xdelivery) {
            
        angular.extend(this, $controller('XdeliveryProductCommanController', {
            $ionicModal: $ionicModal,
            Dialog: Dialog,
            $rootScope: $rootScope,
            $scope: $scope,
            $stateParams: $stateParams
         }));

        $scope.value_id = Xdelivery.value_id = $stateParams.value_id;        
        $scope.store_id = Xdelivery.store_id;
        $scope.parent_id = $stateParams.parent_id;
        $scope.is_loading = false;
        $scope.payout = {};
        $scope.pull_to_refresh = false;
        $scope.settings = Xdelivery.settings;
        
        $scope.loadContent = function (refresh) {
                $scope.is_loading = true;
                $scope.payout.products = {};
                Xdelivery
                .getCategoryByParentId($scope.parent_id, $scope.payout.products.length, $scope.store_id, refresh)
                .then(function (data) {
                    $scope.can_load_older_items = !!data.products.length;
                    $scope.payout = data;
                    if ($scope.pull_to_refresh) {
                        $scope.$broadcast('scroll.refreshComplete');
                        $scope.pull_to_refresh = false;
                    }
                }, function (error) {
                    $scope.is_loading = false;
                    Dialog.alert($translate.instant("Error", "xdelivery"), error.message, $translate.instant("OK") , -1);
                })
                .then(function () { // Finally!
                    $scope.is_loading = false;
                });
        };

        $scope.loadContent(false); // Load content


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

        $scope.pullToRefresh = function () {
             $scope.pull_to_refresh = true;
             $scope.loadContent(true);
        };

        $scope.loadMoreProducts = function() {
            Xdelivery
                .getCategoryByParentId($scope.parent_id, $scope.payout.products.length, $scope.store_id, false)
                .then(function (data) {
                    $scope.can_load_older_items = !!data.products.length;
                    $scope.payout.products = $scope.payout.products.concat(data.products);
                    $rootScope.$broadcast("refreshPageSize");

                }, function (error) {                    
                    $scope.can_load_older_items = false; 
                    Dialog.alert($translate.instant("Error", "xdelivery"), error.message, $translate.instant("OK") , -1);
                })
                .then(function () { // Finally!
                    $scope.$broadcast('scroll.infiniteScrollComplete');                    
                });
        }
 
}).controller('XdeliveryProductCommanController', function (Dialog,$window, Loader, Customer, $ionicModal,$sce, $filter, $timeout, $rootScope, SB, $scope, $state, $stateParams, $translate, Xdelivery, $ionicPopup) {
        $scope.value_id = Xdelivery.value_id = $stateParams.value_id;
        $scope.is_loading = false;
        $scope.payout = {};
        $scope.cart = {};
        $scope.settings = Xdelivery.settings;

        $scope.getProductDetailsInfo = function(product_id){
            Xdelivery.findByProductId(product_id).success(function (data) {
                $timeout(function () {
                    $scope.product_details = data.product;
                    if($scope.product_details.is_multiple_options) {
                        $scope.cart.child_product_id = data.product.selected_product_id;
                    }else {
                        $scope.cart.child_product_id = 0;
                        $scope.cart.product_id = product_id;
                    }
                    $scope.product_details.fixed_image = $scope.product_details.image;
                    $scope.payout.cart_count = $scope.product_details.cart_count;
                    $scope.product_details.description=$sce.trustAsHtml($scope.product_details.description);
                
                    Loader.hide();
                    $scope.is_loading = false; 
                }, 300);
            }).error(function (error ) {
                Loader.hide();
                $scope.is_loading = false; 
                Dialog.alert($translate.instant("Error", "xdelivery") ,error.message , "OK", -1, "xdelivery");
            });
        }

       /**
         * product details
         */
        $scope.productDeails = function(product_id) {
            $scope.settings = Xdelivery.settings;
            $scope.is_loading = true;
            Loader.show();
            $scope.product_details = {};
            $scope.cart = {};
            $scope.cart.product_id = product_id;
            $scope.cart.qty = 1;
            $scope.cart.options = [];         
            $ionicModal.fromTemplateUrl('features/xdelivery/assets/templates/l1/modal/product_details.html', {
                scope: $scope,
                animation: 'slide-in-right-left'        
            }).then(function(modal) {         
              $scope.getProductDetailsInfo(product_id);
              $scope.modalProductDetails = modal;
              $scope.modalProductDetails.show();

            });
        };
      
       /**
         * close details modal 
         */
        $scope.closeModalProductDetails = function ($event){
            console.log("Method called update");
            $scope.modalProductDetails.remove();
            // Extract attributes directly from the event target
            console.log($event);
        var target = $event.currentTarget;
        console.log(target);
        var state = target.getAttribute('data-state');
        var offline = target.getAttribute('data-offline');
        var params = target.getAttribute('data-params');

        console.log("Closing modal and moving to state:", state);
        console.log("Closing modal and moving to params:", params);

        // Close modal logic here (if needed)
        
        // Send message to parent frame
        $window.parent.postMessage(`state-go=state:${state},offline:${offline},${params}`, '*');
        }

        /**
         * Product details
         */
        $scope.toppingOptionsModal = function(topping) {
            Loader.show();
            $scope.product_options = {
                topping : topping,
                values : {}
            };
            $ionicModal.fromTemplateUrl('features/xdelivery/assets/templates/l1/modal/options_values.html', {
                scope: $scope,
                animation: 'slide-in-right-left'        
            }).then(function(modal) {
                Xdelivery.findOptionsValuesById(topping.option_id).success(function (data) {
                    $scope.product_options.values = data.values;
                    Loader.hide();                       
                }).error(function (error ) {
                    Loader.hide();                   
                    Dialog.alert($translate.instant("Error", "xdelivery") ,error.message , "OK", -1, "xdelivery");
                });

              $scope.modalToppingOptions = modal;
              $scope.modalToppingOptions.show();
           });
        };
      
       /**
         * close details modal 
         */
        $scope.closeToppingOptionsModal = function (){
             
             if($scope.product_options.topping.category_type == 'checkbox-limitedchoice' && $scope.cart.options.length > 0){
                if(($scope.cart.options.length < parseInt($scope.product_options.topping.minimum_option)) || ($scope.cart.options.length > parseInt($scope.product_options.topping.maximum_option))){
                    var wrmessage = $translate.instant("Selected the Minimum $2 Or Maximum $1", "xdelivery");
                    wrmessage = wrmessage.replace('$1', $scope.product_options.topping.maximum_option).replace('$2', $scope.product_options.topping.minimum_option);
                    Dialog.alert($translate.instant("Error", "xdelivery") ,$translate.instant(wrmessage, "xdelivery") , "OK", -1, "xdelivery");
                }else{
                    $scope.modalToppingOptions.remove();
                }

             }else{
                $scope.modalToppingOptions.remove();
             }
        }
 
         /**
         *Product Image 
         */
        $scope.ProductImage = function (image) {      
            if (image != '' && image != null && image != "null") {
                return IMAGE_URL + 'images/application' + image;
            } else {
                return "./features/xdelivery/assets/media/default-image.png"
            }
        };

        /**
         *Store Image 
         */
        $scope.storeImage = function (image) {      
            if (image != '' && image != null && image != "null") {
                return IMAGE_URL + 'images/application' + image;
            } else {
                return "./features/xdelivery/assets/media/default-image.png"
            }
        };

        $scope.addToOption = function(option, input_type) { 
            if(input_type == 'radio'){
                var optionsExits = $filter('filter')($scope.cart.options, {option_id: option.option_id });
                if(optionsExits.length){
                    for(var item in $scope.cart.options) { 
                    if(($scope.cart.options[item].option_id === option.option_id) && ($scope.cart.options[item].id != option.id)) {
                        $scope.cart.options.splice(item, 1);
                      }
                    }
                }
            }

            for(var item in $scope.cart.options) { 
            if($scope.cart.options[item].id === option.id) {
                $scope.cart.options.splice(item, 1);
                console.log("$scope.cart.options 2", $scope.cart.options);
                return;
              }
            }

            var pdata = {};
            pdata.id =  option.id;
            pdata.option_id =  option.option_id;
            pdata.name =  option.name;
            pdata.price =  option.price;
            $scope.cart.options.push(pdata);
            console.log("$scope.cart.options 3", $scope.cart.options);
        }

        $scope.checkedOption = function(option) {            
            for(var item in $scope.cart.options) { 
                if($scope.cart.options[item].id === option.id) {
                   return true;
                }
            }
            return false;       
        }

        $scope.toppingSelectedSum = function(option_id) {
            var sum = 0;
            for(var item in $scope.cart.options) {
                if($scope.cart.options[item].option_id === option_id) {
                   sum = sum + parseFloat($scope.cart.options[item].price);
                }
            }
            return sum;
        }


        $scope.totalToppingSum = function(options) {
            var sum = 0;
            for(var item in options) {
                sum = sum + parseFloat(options[item].price);
            }
            return sum;
        }

        $scope.choiceVariantProduct = function(varient_product) {
            $scope.cart.child_product_id = varient_product.varient_product_id;
            $scope.product_details.selected_product_id = varient_product.varient_product_id;
            $scope.product_details.active_special_price = varient_product.active_special_price;
            $scope.product_details.in_stock = varient_product.in_stock;
            $scope.product_details.low_stock_threshold = varient_product.low_stock_threshold;
            $scope.product_details.manage_stock = varient_product.manage_stock;
            $scope.product_details.price = varient_product.price;
            $scope.product_details.qty = varient_product.qty;
            $scope.product_details.selling_price = varient_product.selling_price;
            $scope.product_details.sku = varient_product.sku;
            $scope.product_details.special_price = varient_product.special_price;
            $scope.product_details.special_price_end = varient_product.special_price_end;
            $scope.product_details.special_price_start = varient_product.special_price_start;
            $scope.product_details.tax_rate = varient_product.tax_rate;
            $scope.product_details.special_price_with_currency = varient_product.special_price_with_currency;
            $scope.product_details.price_with_currency = varient_product.price_with_currency;
            
            if(varient_product.product_image != '' && varient_product.product_image != null){
                $scope.product_details.image = varient_product.product_image;
            }else{
                $scope.product_details.image = $scope.product_details.fixed_image;
            }
        }   

        $scope.checkedProduct = function(product_id) {
            if($scope.cart.child_product_id == product_id) {
                return true;
            }       
        }

        /*add to cart*/
        $scope.addToCart = function(cart) {        
            
            var optionsRequired = $filter('filter')($scope.product_details.options, {is_required: "1" });
            if(optionsRequired.length){
                for(var Optreq in optionsRequired) {
                    var is_exist_optons =  $filter('filter')($scope.cart.options, {option_id: optionsRequired[Optreq].option_id });
                    if(is_exist_optons.length == 0){
                        var warringMessages = $translate.instant('Please select $1 options before adding this product to your cart.' , "xdelivery" ).replace('$1', '"'+optionsRequired[Optreq].name+'"');
                        Dialog.alert($translate.instant("Error"), warringMessages, $translate.instant("OK"));
                        return false;
                    }
                }
            }
      
            $scope.cart.amount = parseFloat($scope.product_details.selling_price);
            $scope.cart.sub_amount = ($scope.cart.qty * $scope.product_details.selling_price);
            $scope.cart.total_amount = $scope.cart.sub_amount + $scope.totalToppingSum($scope.cart.options);
            $scope.cart.tax_amount = 0 ;           

            Loader.show();
            Xdelivery
                .addToCart($scope.cart)
                .success(function (data) {
                    $scope.getProductDetailsInfo($scope.cart.product_id);
                     
                $ionicPopup.show({
                    title: $translate.instant('Success', "xdelivery"),
                    template: $translate.instant('Product is added to cart!', "xdelivery"),
                    cssClass: 'delete-status',
                    scope: $scope,
                    buttons: [{
                        text: $translate.instant('Continue Shopping', "xdelivery"),
                        type: 'button-default',
                        onTap: function (e) {
                            return false;
                        }
                    }, {
                        text: $translate.instant('Go To Cart', "xdelivery"),
                        type: 'button-positive',
                        onTap: function (e) {
                            return true;
                        }
                    }]
                }).then(function (result) {
                     $scope.closeModalProductDetails();
                    if (result) {
                        $state.go("xdelivery-cart", { value_id: $scope.value_id }, { reload: true } );
                    }
                });

            }, function (error) {
                Loader.hide();
                Dialog.alert($translate.instant("Error"), error.message, $translate.instant("OK") , -1);
            })
            .then(function () { // Finally!
                Loader.hide();
            });

        }

        $scope.saveForLater = function (product_id) {
            Loader.show();
                Xdelivery
                .saveForLater(product_id)
                .success(function (data) {
                   $scope.getProductDetailsInfo(product_id);
                }, function (error) {
                    Loader.hide();
                    Dialog.alert($translate.instant("Error"), error.message, $translate.instant("OK") , -1);
                })
                .then(function () { // Finally!
                    Loader.hide();
                });
        };

        $scope.addAddressModal = function(isEdit, address_post = {}){
            if(isEdit){
                $scope.page_title = $translate.instant('Edit Address', "xdelivery");
                $scope.address_post = address_post;
            }else{
                $scope.page_title = $translate.instant('Add Address', "xdelivery");
                $scope.address_post = {
                    customer_name: '',
                    phone_number: '',
                    is_default: 0,
                    address_type: 'home',
                    address : '',
                    address_two : '',
                    pincode: '',
                    city:'',
                    company_address:'',
                    sdi:'',
                    pec:'',
                    cod_fiscale:'',
                    address_lat: '',
                    address_lng: '',
                    locality:''

                };
            }

            $ionicModal.fromTemplateUrl('features/xdelivery/assets/templates/l1/account/add_address.html', {
                scope: $scope,
                animation: 'slide-in-right-left'        
            }).then(function(modal) {    
              $scope.settings = Xdelivery.settings;     
              $scope.modalAddress = modal;
              $scope.modalAddress.show();
            });
        }

        /**
         * close modal 
         */
        $scope.closeAddAddressModal = function (){
             $scope.modalAddress.remove();
        }
 
        $scope.addAddress = function(param) {
            Loader.show();
            Xdelivery
                .saveAddress(param)
                .then(function (data) {
                    Dialog.alert($translate.instant("Success"), data.message, $translate.instant("OK") , -1);
                    $scope.closeAddAddressModal();
                }, function (error) {
                    Loader.hide();
                    Dialog.alert($translate.instant("Error"), error.message, $translate.instant("OK") , -1);
                })
                .then(function () { // Finally!
                    Loader.hide(); 
                });
        }

       $scope.disableTap = function() {
            var container = angular.element(document.getElementsByClassName('pac-container'));
            // disable ionic data tab
            container.attr('data-tap-disabled', 'true');
            // leave input field if google-address-entry is selected
            container.on("click", function(){
                document.getElementById('pac-input').blur();
            });
        };


       $scope.updateAddress = function() {
  
            $scope.address_post.address_lat = $scope.address_post.address_lng = null;
            $scope.address_post.zipcode = null;
            console.log('locality', $scope.address_post.place);
            if(_.isObject($scope.address_post.place)) {
                var loc = _.get($scope.address_post, "place.geometry.location");
                console.log('loc',$scope.loc);
                if(loc) {
                    $scope.address_post.address_lat = loc.lat();
                    $scope.address_post.address_lng = loc.lng();
                    _.forEach(_.get($scope.address_post.place, "address_components"), function(adco) {
                        if(_.includes(_.get(adco, "types")||[], "postal_code")) {
                            $scope.address_post.zipcode = _.get(adco, "long_name");
                        }
                        if(_.includes(_.get(adco, "types")||[], "country")) {
                            $scope.address_post.country = _.get(adco, "short_name");
                        }
                    });
                }
            } 
            console.log('address_post',$scope.address_post);

        };

        $scope.goToHome = function(){
            Loader.show();
            $state
            .go("home", { value_id: $scope.value_id }, { reload: true })
            .then(function () {
                Loader.hide();
                $state.go("xdelivery-home", { value_id: $scope.value_id }, { reload: true });
            });
        }


        $scope.goButton = function(type){
            $scope.closeModalProductDetails();
            if(type == 'home'){
                 Loader.show();
                $state
                .go("home", { value_id: $scope.value_id }, { reload: true })
                .then(function () {
                    Loader.hide();
                    $state.go("xdelivery-home", { value_id: $scope.value_id }, { reload: true });
                });
            }

            if(type == 'search'){
                Loader.show();
                $state
                .go("home", { value_id: $scope.value_id }, { reload: true })
                .then(function () {
                        $state.go("xdelivery-home", { value_id: $scope.value_id }, { reload: true })
                        .then(function () {
                            Loader.hide();
                            $state.go("xdelivery-search", { value_id: $scope.value_id }, { reload: true });
                        });
                });
            }

            if(type == 'cart'){
                $state.go("xdelivery-cart", { value_id: $scope.value_id }, { reload: true });
            }
           
        }

 
}).controller('XdeliverySearchController', function (Dialog, $controller, $ionicModal, Loader, Customer, $rootScope, SB, $scope, $state, $stateParams, $translate, Xdelivery) {
        $scope.value_id = Xdelivery.value_id = $stateParams.value_id;
        $scope.is_loading = false;
        $scope.settings = Xdelivery.settings;
        $scope.store_id = Xdelivery.store_id;
        $scope.use_pull_refresh = true;
        $scope.pull_to_refresh = false;
        $scope.payout = {
            products: {},
            search_text: '',
            popular_suggestions : {}
        };

        angular.extend(this, $controller('XdeliveryProductCommanController', {
            $ionicModal: $ionicModal,
            Dialog: Dialog,
            $rootScope: $rootScope,
            $scope: $scope,
            $stateParams: $stateParams
         }));

        $scope.loadContent = function () {
                $scope.is_loading = true;
                Xdelivery
                .popularSuggestions($scope.value_id)
                .then(function (data) {
                    $scope.payout.popular_suggestions = data.popular_suggestions;              
                }, function (error) {
                    $scope.is_loading = false;
                    Dialog.alert($translate.instant("Error"), error.message, $translate.instant("OK") , -1);
                })
                .then(function () { // Finally!
                    $scope.is_loading = false;
                });
        };

        $scope.loadContent(); // Load content


       $scope.loadSearchContent = function (pullToRefresh = true) {
            $scope.payout.search_text = document.getElementById("productSearch").value;  
            if($scope.payout.search_text == '') {
                $scope.payout.products = {};
                if ($scope.pull_to_refresh) {
                    $scope.$broadcast('scroll.refreshComplete');
                    $scope.pull_to_refresh = false;
                }
                return false;
            }

            $scope.is_loading = true;          
            $scope.payout.products = {};
            Xdelivery.findProductsSearch(pullToRefresh, $scope.payout.products.length, $scope.store_id, $scope.payout.search_text.trim()).success(function (data) {
                $scope.can_load_older_items = !!data.products.length;
                $scope.payout.products = data.products;
                $scope.is_loading = false;
                if ($scope.pull_to_refresh) {
                    $scope.$broadcast('scroll.refreshComplete');
                    $scope.pull_to_refresh = false;
                }

            }, function (error) {
                $scope.pull_to_refresh = false;
                $scope.is_loading = false;
                Dialog.alert($translate.instant("Error", "xdelivery"), error.message, $translate.instant("OK") , -1);
            });
        };

        
        $scope.pullToRefresh = function () {
            $scope.pull_to_refresh = true;
            $scope.loadSearchContent(true);
        };

        $scope.loadMoreProducts = function() {
           $scope.payout.search_text = document.getElementById("productSearch").value;  
           
           Xdelivery.findProductsSearch(false, $scope.payout.products.length, $scope.store_id, $scope.payout.search_text).success(function (data) {
                $scope.can_load_older_items = !!data.products.length;
                $scope.payout.products = $scope.payout.products.concat(data.products);
                $rootScope.$broadcast("refreshPageSize");        
            }).error(function (error) {
                $scope.can_load_older_items = false; 
                Dialog.alert($translate.instant("Error", "xdelivery") ,error.message , "OK", -1, "xdelivery");         
            }).finally(function () {          
                $scope.$broadcast('scroll.infiniteScrollComplete');
            });
        }
        

        $scope.forTranslate = function(text){
            return $translate.instant(text, "xdelivery");
        }
 
});