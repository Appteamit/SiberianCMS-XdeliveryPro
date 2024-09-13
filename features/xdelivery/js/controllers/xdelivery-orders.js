/**
 * xdelivery Orders version 1 controllers
 */
angular.module('starter')
    .controller('XdeliveryOrdersController', function (Dialog, $ionicPopup, Loader, $timeout, $ionicModal, $filter, Customer, $rootScope, SB, $scope, $state, $stateParams, $translate, Xdelivery, $window) {
        $scope.value_id = Xdelivery.value_id = $stateParams.value_id;
        $scope.order_id = $stateParams.order_id;
        $scope.is_loading = false;
        $scope.payout = {};
        $scope.settings = Xdelivery.settings;

        $scope.loadContent = function () {
            console.log("This is the right point");
            $scope.is_loading = true;
            Xdelivery
            .findAllOrderByCustomerId()
            .then(function (data) {
                $scope.payout = data;
            }, function (error) {
                $scope.is_loading = false;
                Dialog.alert($translate.instant("Error", "xdelivery"), error.message, $translate.instant("OK", "xdelivery") , -1);
            })
            .then(function () { // Finally!
                $scope.is_loading = false;
            });

            $timeout(function () {
                if(!Xdelivery.isUndefined($scope.order_id) && $scope.order_id != '' && $scope.order_id != null){
                   $scope.orderDetails($scope.order_id); 
                   $scope.order_id = '';
                } 
            }, 300);  

	    };

    $scope.loadContent();

     /**
     *   date convert
     */
    $scope.convertDate = function (date) {
        return $filter('moment_calendar')((new Date(date)).getTime());
    };

     /**
     *  Order Tracking Url 
     *
     * */
    $scope.openURL = function (url) {
         $window.open(url, $rootScope.getTargetForLink(), 'location=yes');
    };
    

    /**
     * details
     */
    $scope.orderDetails = function(order_id) {
        $scope.isLoading = true;
        Loader.show();
        $ionicModal.fromTemplateUrl('features/xdelivery/assets/templates/l1/modal/order_details.html', {
            scope: $scope,
            animation: 'slide-in-right-left'        
        }).then(function(modal) {

           Xdelivery
            .findOrderById(order_id)
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
    }

    $scope.cancelOrder = function(order_id){
            $ionicPopup.show({
                title: $translate.instant('Confirm', "xdelivery"),
                template: $translate.instant('Are you sure want to Cancel Order?', "xdelivery"),
                cssClass: 'cancel-status',
                scope: $scope,
                buttons: [{
                    text: $translate.instant('No', "xdelivery"),
                    type: 'button-default',
                    onTap: function (e) {
                        return false;
                    }
                }, {
                    text: $translate.instant('Yes', "xdelivery"),
                    type: 'button-positive',
                    onTap: function (e) {
                        return true;
                    }
                }]
            }).then(function (result) {
                if (result) {
                Loader.show();
                Xdelivery
                  .cancelOrder(order_id)
                  .then(function (data) {
                      Dialog.alert($translate.instant("Success", "xdelivery"), data.message, $translate.instant("OK", "xdelivery") , -1);
                      $scope.loadContent();
                      $scope.closeModalOrderDetails();
                    }, function (error) {
                      Loader.hide();
                      Dialog.alert($translate.instant("Error", "xdelivery"), error.message, $translate.instant("OK", "xdelivery") , -1);
                  })
                  .then(function () { 
                    Loader.hide(); 
                  });
                }
            });
       }


     $scope.returnOrder = function(order_id){
            $ionicPopup.show({
                title: $translate.instant('Confirm', "xdelivery"),
                template: $translate.instant('Are you sure want to Return Order?', "xdelivery"),
                cssClass: 'cancel-status',
                scope: $scope,
                buttons: [{
                    text: $translate.instant('No', "xdelivery"),
                    type: 'button-default',
                    onTap: function (e) {
                        return false;
                    }
                }, {
                    text: $translate.instant('Yes', "xdelivery"),
                    type: 'button-positive',
                    onTap: function (e) {
                        return true;
                    }
                }]
            }).then(function (result) {
                if (result) {
                Loader.show();
                Xdelivery
                  .returnOrder(order_id)
                  .then(function (data) {
                      Dialog.alert($translate.instant("Success", "xdelivery"), data.message, $translate.instant("OK", "xdelivery") , -1);
                      $scope.loadContent();
                      $scope.closeModalOrderDetails();
                    }, function (error) {
                      Loader.hide();
                      Dialog.alert($translate.instant("Error", "xdelivery"), error.message, $translate.instant("OK", "xdelivery") , -1);
                  })
                  .then(function () { 
                    Loader.hide(); 
                  });
                }
            });
       }


        $scope.reTryPaymentNow = function(order_id){
            $scope.closeModalOrderDetails();
            $state.go("xdelivery-ewallet", { value_id: $scope.value_id, order_id: order_id  }, { reload: true } );
        }

        $scope.goHome = function(){
            Loader.show();
            $state
            .go("home", { value_id: $scope.value_id }, { reload: true })
            .then(function () {
                Loader.hide();
                $state.go("xdelivery-home", { value_id: $scope.value_id }, { reload: true });
            });
        }
 
});