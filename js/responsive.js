var bodyApp = angular.module('bodyApp', []);
bodyApp.controller('tablesController', function($scope, $window){
  $scope.rdrct = function(t, f, v){ //table, field, value
    var tmp = 'http://YOUR WEBSERVER URL HERE?table=' + t
              + '&where_field=' + f
              + '&where_value=' + v;
    $window.location.href = tmp;
  }
});
bodyApp.controller('headerController', function($scope, $window){
  $scope.rstrt = function(){
    $window.location.href = 'http://YOUR WEBSERVER URL HERE';
  }
});
