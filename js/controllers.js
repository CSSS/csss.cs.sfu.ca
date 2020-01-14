'use strict';

/* Controllers */

var a;

angular.module('myApp.controllers', []).
  controller('MyCtrl1', [function() {

  }])
  .controller('MyCtrl2', [function() {

  }])
  .controller('MinutesController', ['$http', '$scope', function($http, $scope) {
	$http.get('/listminutes.php').success(function(data) {
		$scope.minutes = data;
	});
  }])
  .controller('EventController', ['$http', '$scope', function($http, $scope) {
	var months = ['Jan.', 'Feb.', 'Mar.', 'Apr.', 'May', 'Jun.', 'Jul.', 'Aug.', 'Sep.', 'Oct.', 'Nov.', 'Dec.'];
	$http.get('/eventproxy.php').success(function(data) {
		var jcalData=ICAL.parse(data);
		var comp = new ICAL.Component(jcalData[1]);
		var vevents = comp.getAllSubcomponents("vevent");
		$scope.events=[];
		for (var i in vevents) {
			var event = new ICAL.Event(vevents[i]);
			var e = {};
			var date = event.startDate.toJSDate();
			e.summary = event.summary;
			e.month=months[date.getMonth()];
			e.day=date.getDate();
			e.year=date.getFullYear();
			e.hour=date.getHours();
			e.min=date.getMinutes();
			
			if (e.hour > 12) {
				e.TOD = "PM";
				e.hour -= 12;
			} else {
				e.TOD = "AM";
			}
			e.url=event._firstProp("url");
			e.location = event.location;
			
			$scope.events.push(e);
		}
	});

	$scope.execs = [
{'name': 'Navin Gidha', 'pos': 'President'},
{'name': 'Steven Xu', 'pos': 'Vice-President'},
{'name': 'Mark Lui', 'pos': 'Treasurer'},
{'name': 'Kristina Vandergulik', 'pos': 'Director of Resources'},
{'name': 'Kenneth Kwok', 'pos': 'Director of Activities'},
{'name': 'Nicholas Hoekstra', 'pos': 'Secretary'},
{'name': 'Colin Woodbury', 'pos': 'Executive at Large'},
{'name': 'Kristina Vandergulik', 'pos': 'SFSS Forum Representative'},
{'name': 'Alex Land & Max Proske', 'pos': 'First Year Representatives'}
];
  }]);
