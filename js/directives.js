'use strict';

/* Directives */


angular.module('myApp.directives', []).
  directive('currentYear', [function() {
    return function(scope, elm, attrs) {
      elm.text(new Date().getFullYear());
    };
  }]);
