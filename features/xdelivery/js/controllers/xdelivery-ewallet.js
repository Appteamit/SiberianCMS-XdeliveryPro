/**
 * Xdelivery controllers
 */
angular
    .module('starter')
    .controller('XdeliveryEwalletController', function (Dialog, Loader, Customer, $filter, Modal, $ionicPopup,
                                                   $ionicScrollDelegate, $timeout, $ionicHistory, $rootScope, SB,
                                                   Xdelivery, $scope, $state, $stateParams, $translate, Ewallet, $ionicHistory, $ionicSideMenuDelegate) {

        $scope.value_id = Xdelivery.value_id = $stateParams.value_id;
        $scope.order_id = $stateParams.order_id;       
        $scope.is_loading = false;
      
        /**
	     *Load content
	     */
	    $scope.loadContent = function() {
            $scope.cart = {};
            Loader.show();
            Xdelivery
                .findOrderById($scope.order_id)
                .then(function (data) {
                    $scope.payout = data;
                    $scope.cart.method == 'payToModule';
                    $scope.cart.order_id = $scope.order_id;
                    $scope.cart.amount = data.order.total_amount;
                    $scope.cart.currency_code = data.order.currency;
                    $scope.cart.currency_symbol = data.order.currency;
                    $scope.cart.remark = $translate.instant("Order", "xdelivery")+"# "+data.order.order_number;
                    $scope.cart.return_state_name = 'xdelivery-ewallet-return';
                    $scope.cart.return_value_id = $scope.value_id;
                    Ewallet.setPaycart($scope.cart);
                    
                    $ionicHistory.nextViewOptions({
                        disableBack: true
                    });
                    $ionicSideMenuDelegate.canDragContent(false);
                    
                    $state.go("ewallet-module-payment", { value_id: data.ewallet_value_id }, { reload: true });
                    Loader.hide();

                 }, function (error) {                
                    Dialog.alert($translate.instant("Error", "xdelivery"), error.message, $translate.instant("Ok", "xdelivery"), -1, 'xdelivery');
                })
                .then(function () { // Finally!
                   $scope.is_loading = false;
                }); 
        }


	    $scope.loadContent();

}).controller('XdeliveryEwalletReturnController', function (Dialog, Loader, Customer, $filter, Modal, $ionicPopup,
                                                   $ionicScrollDelegate, $timeout, $ionicHistory, $rootScope, SB,
                                                   Xdelivery, $scope, $state, $stateParams, $translate, Ewallet, $ionicHistory, $ionicSideMenuDelegate) {
        $scope.value_id = Xdelivery.value_id = $stateParams.value_id;
        $scope.cart = Ewallet.getPaycart();
       
        $ionicHistory.nextViewOptions({
                                historyRoot: true,
                                disableAnimate: false,
                                disableBack: true
                        });
        $ionicSideMenuDelegate.canDragContent(true);

        Loader.show();        
        Xdelivery
            .updatePaymentStatus($scope.cart)
            .then(function (data) {
                Loader.hide(); 
                if($scope.cart.status == 'success'){
                     Dialog.alert($translate.instant("Success", "xdelivery"), $translate.instant("Payment has been successfully", "xdelivery"), $translate.instant("OK", "xdelivery"), -1, "xdelivery");  
                }
                if($scope.cart.status == 'failed'){
                     Dialog.alert($translate.instant("Error", "xdelivery"), $translate.instant("Payment has been failed", "xdelivery"), $translate.instant("OK", "xdelivery"), -1, "xdelivery");  
                }                
                Ewallet.setPaycart({});
                $state
                    .go("home", { value_id: $scope.value_id }, { reload: true })
                    .then(function () {
                        $state.go("xdelivery-home", { value_id: $scope.value_id }, { reload: true }).then(function () {
                            $state.go("xdelivery-orders", { value_id: $scope.value_id, order_id: $scope.cart.order_id }, { reload: true })
                        });
                    });
         
            }, function (error) {
                Loader.hide();
                Dialog.alert($translate.instant("Error", "xdelivery"), error.message, $translate.instant("OK", "xdelivery"), -1, "xdelivery");
       });
 
 
});