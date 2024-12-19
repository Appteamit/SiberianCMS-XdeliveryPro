angular.module("starter").directive("comipleDeeplinks", ['$timeout', function($timeout) {
    return {
        restrict: 'A',  // Attribute only
        link: function(scope, element, attrs) {
            // Timeout to ensure the DOM has updated
            $timeout(function() {
                // Query for links within the element only
                var inAppLinks = element[0].querySelectorAll("a[data-state]");
                angular.forEach(inAppLinks, function(link) {
                    angular.element(link).on('click', function() {
                        var state = link.getAttribute("data-state");
                        var offline = link.getAttribute("data-offline");
                        var params = link.getAttribute("data-params");
                        var message = `state-go=state:${state},offline:${offline},${params}`;
                        window.parent.postMessage(message, "*");
                    });
                });
            }, 0);
        }
    };
}]);