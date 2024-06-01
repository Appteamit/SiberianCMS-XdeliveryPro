/**
 * xdelivery Home version 1 controllers
 */
angular.module('starter')
    .controller('XdeliveryAccountController', function (Dialog, $timeout, Loader, Customer, $rootScope, SB, $scope, $state, $stateParams, $translate, Xdelivery) {
        $scope.value_id = Xdelivery.value_id = $stateParams.value_id;
        $scope.is_loading = false;
        $scope.payout = {};
        $scope.is_logged_in = Customer.isLoggedIn();
	    $scope.customer = Customer.customer;
	    $scope.settings = Xdelivery.settings;

        /**
	     *login
	     */
	    $scope.login = function(){
	        if (!Customer.isLoggedIn()) {
	            Customer.loginModal($scope, function () { 
	                Loader.show();
	                $timeout(function () {
	                    $scope.is_logged_in = Customer.isLoggedIn();
	                    $scope.customer = Customer.customer;
	                    $state
                        .go('home')
                        .then(function () {
                            $state
                                .go("xdelivery-home", { value_id: $scope.value_id }, { reload: true })
                                .then(function () {
                                	Loader.hide();
                                    $state.go("xdelivery-account", { value_id: $scope.value_id }, { reload: true });
                                });
                        });
	                }, 1000);
	            });
	        } 
        }


	   $rootScope.$on(SB.EVENTS.AUTH.loginSuccess, function () {
            if($scope.value_id){
                $scope.loadContent();
            }
	        $scope.is_logged_in = Customer.isLoggedIn();
	        $scope.customer = Customer.customer;
	    });

	    $rootScope.$on(SB.EVENTS.AUTH.logoutSuccess, function () {
            if($scope.value_id){
                $scope.loadContent();
            }
	        $scope.loadContent();
	        $scope.is_logged_in = Customer.isLoggedIn();
	        $scope.customer = Customer.customer;
	    });


	    /**
	     *Customer avatar
	     */
	    $scope.customer_avatar = function (image) {      
	        if (image != '' && image != null && image != "null") {
	            return IMAGE_URL + 'images/customer' + image;
	        } else {
	            return "./features/xdelivery/assets/media/customer-placeholder.png"
	        }
	    };

	    $scope.loadContent = function () {           
      		console.log('Customer',  $scope.customer);
        };



}).controller('XdeliveryWishlistController', function (Dialog, $controller, Loader, Customer, $ionicModal,  $timeout, $rootScope, SB, $scope, $state, $stateParams, $translate, Xdelivery) {
        $scope.value_id = Xdelivery.value_id = $stateParams.value_id;
        $scope.is_loading = false;
        $scope.payout = {};
        $scope.is_favourite = true;
        
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
	            .findWishlist()
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

        $scope.$on('modal.hidden', function() {
		     $scope.loadContent()
		});

		$scope.removeWishlist = function (product_id) {
            Loader.show();
                Xdelivery
                .saveForLater(product_id)
                .success(function (data) {
                   $scope.loadContent();
                }, function (error) {
                    Loader.hide();
                    Dialog.alert($translate.instant("Error", "xdelivery"), error.message, $translate.instant("OK") , -1);
                })
                .then(function () { // Finally!
                    Loader.hide();
                });
        };

}).controller('XdeliveryAddressController', function (Dialog, $controller, Loader, Customer, $ionicModal,  $timeout, $rootScope, SB, $scope, $state, $stateParams, $translate, Xdelivery, $ionicPopup) {
        $scope.value_id = Xdelivery.value_id = $stateParams.value_id;
        $scope.is_loading = false;
        $scope.payout = {};
      	$scope.address_post = {};

        angular.extend(this, $controller('XdeliveryProductCommanController', {
            Dialog: Dialog,
            $rootScope: $rootScope,
            $scope: $scope,
            $stateParams: $stateParams
        }));
	    $scope.loadContent = function () {
            $scope.is_loading = true;
            Xdelivery
	            .findAddress()
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

        $scope.$on('modal.hidden', function() {
		     $scope.loadContent();
		});

		$scope.forTranslate = function(text){
			return $translate.instant(text, "xdelivery");
		}
 
		$scope.deleteAddress = function(address_id) {
		    $ionicPopup.show({
                title: $translate.instant('Confirm', "xdelivery"),
                template: $translate.instant('Are you sure want to Delete?', "xdelivery"),
                cssClass: 'delete-status',
                scope: $scope,
                buttons: [{
                    text: $translate.instant('Cancel', "xdelivery"),
                    type: 'button-default',
                    onTap: function (e) {
                        return false;
                    }
                }, {
                    text: $translate.instant('OK', "xdelivery"),
                    type: 'button-positive',
                    onTap: function (e) {
                        return true;
                    }
                }]
            }).then(function (result) {
                if (result) {
                	Loader.show();
                    Xdelivery
			            .deleteAddress(address_id)
			            .then(function (data) {
						    Dialog.alert($translate.instant("Success", "xdelivery"), data.message, $translate.instant("OK", "xdelivery") , -1);
			                $scope.loadContent();
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

});