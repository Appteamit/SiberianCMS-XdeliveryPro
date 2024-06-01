/**
 * xdelivery Store version 1 controllers
 */
angular.module('starter')
    .controller('XdeliveryStoreController', function (Dialog, $timeout, Loader, Customer, $rootScope, SB, $scope, $state, $stateParams, $translate, Xdelivery, $controller) {
        
        angular.extend(this, $controller('XdeliveryProductCommanController', {
            Dialog: Dialog,
            $rootScope: $rootScope,
            $scope: $scope,
            $stateParams: $stateParams
        }));
        
        $scope.value_id = Xdelivery.value_id = $stateParams.value_id;
        $scope.store_id = Xdelivery.store_id = $stateParams.store_id;
        $scope.is_loading = false;
        $scope.payout = {};
        $scope.is_logged_in = Customer.isLoggedIn();
	    $scope.customer = Customer.customer;
	    $scope.settings = Xdelivery.settings;
 
	    $scope.loadContent = function () {
            $scope.is_loading = true;
            Xdelivery
            .findStore($scope.store_id)
            .then(function (data) {
                $scope.payout = data;
            }, function (error) {
                $scope.is_loading = false;
                Dialog.alert($translate.instant("Error", "xdelivery"), error.message, $translate.instant("OK", "xdelivery") , -1);
            })
            .then(function () { // Finally!
                $scope.is_loading = false;
            });           

	    };

    $scope.loadContent();


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


});