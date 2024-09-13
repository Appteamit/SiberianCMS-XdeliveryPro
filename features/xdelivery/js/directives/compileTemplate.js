angular.module("starter").directive("compileTemplate", [
    "$compile",
    "$parse",
    function ($compile, $parse) {
      return {
        restrict: "A",
        link: function ($scope, element, attr) {
          var parse = $parse(attr.ngBindHtml);
          function value() {
            return (parse($scope) || "").toString();
          }
          $scope.$watch(value, function () {
            $compile(element, null, -9999)($scope);
          });
        },
      };
    },
  ]);