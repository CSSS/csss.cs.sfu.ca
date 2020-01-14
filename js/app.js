'use strict';


// Declare app level module which depends on filters, and services
angular.module('myApp', [
  'ngRoute',
  'myApp.directives',
  'myApp.controllers'
]).
config(['$routeProvider', '$locationProvider', function($routeProvider, $locationProvider) {
  $locationProvider.html5Mode(true);
  $routeProvider.when('/', {templateUrl: 'partials/root.html', controller: 'EventController'});
  $routeProvider.when('/minutes', {templateUrl: 'partials/minutes.html', controller: 'MinutesController'});
  $routeProvider.when('/constitution', {templateUrl: 'partials/constitution.html'});
//  $routeProvider.when('/minutes', {templateUrl: 'partials/minutes.php'});
  $routeProvider.when('/gallery', {templateUrl: 'partials/gallery.php'});
  $routeProvider.when('/awards', {templateUrl: 'partials/awards.html'});
  $routeProvider.otherwise({redirectTo: '/'});
}]);
