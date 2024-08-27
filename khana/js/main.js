// Declare app module.
var app = angular.module('myApp', ["ngRoute", "mobile-angular-ui", "myApp.Services", "myApp.Controllers"]);
        
// Define routes.
app.config(function($routeProvider) {
    $routeProvider
        .when('/', {
            controller: 'IntroCtrl',
            templateUrl: 'views/intro.html'
        })
        .when('/login', {
            controller: 'LoginCtrl',
            templateUrl: 'views/login.html'
        })
        
        .when('/companies', {
            controller: 'ListCtrl',
            templateUrl: 'views/list-companies.html',
            resolve: { dataType: function() {return 'company';} }
        })
        .when('/flights', {
            controller: 'FlightsCtrl',
            templateUrl: 'views/list-flights.html'
        })
        .when('/officers', {
            controller: 'ListCtrl',
            templateUrl: 'views/list-officers.html',
            resolve: { dataType: function() {return 'officer';} }
        })
        .when('/places', {
            controller: 'ListCtrl',
            templateUrl: 'views/list-places.html',
            resolve: { dataType: function() {return 'place';} }
        })
        .when('/technicians', {
            controller: 'ListCtrl',
            templateUrl: 'views/list-technicians.html',
            resolve: { dataType: function() {return 'technician';} }
        })


        .when('/maintenances', {
            controller: 'ListCtrl',
            templateUrl: 'views/list-maintenances.html',
            resolve: { dataType: function() {return 'maintenances';} }
        })



        
        .when('/company/:id', {
            controller: 'FormCtrl',
            templateUrl: 'views/edit-simple.html',
            resolve: { 
                dataType: function() {return 'company';},
                returnUrl: function() {return '/companies';},
                defaultEntry: function() {return {id: null, name: '', disabled: 0};} 
            }
        })
        .when('/flight/:id', {
            controller: 'FlightCtrl',
            templateUrl: 'views/edit-flight.html'
        })
        .when('/officer/:id', {
            controller: 'FormCtrl',
            templateUrl: 'views/edit-officer.html',
            resolve: { 
                dataType: function() {return 'officer';},
                returnUrl: function() {return '/officers';},
                defaultEntry: function() {return {id: null, first_name: '', last_name: '', code: '', login_code: '', section: '', subsection: '', observations: ''};} 
            }
        })
        .when('/place/:id', {
            controller: 'FormCtrl',
            templateUrl: 'views/edit-simple.html',
            resolve: { 
                dataType: function() {return 'place';},
                returnUrl: function() {return '/places';},
                defaultEntry: function() {return {id: null, name: ''};} 
            }
        })
        .when('/technician/:id', {
            controller: 'FormCtrl',
            templateUrl: 'views/edit-technician.html',
            resolve: { 
                dataType: function() {return 'technician';},
                returnUrl: function() {return '/technicians';},
                defaultEntry: function() {return {id: null, first_name: '', last_name: '', code: '', login_code: '', section: '', subsection: '', observations: ''};} 
            }
        })


        .when('/maintenances/:id', {
            controller: 'FormCtrl',
            templateUrl: 'views/edit-maintenances.html',
            resolve: { 
                dataType: function() {return 'maintenances';},
                returnUrl: function() {return '/maintenances';},
                defaultEntry: function() {return {user_id: null, station_id: '', zone_id: '', screenid_type: '', fp_id: '', pp_id: '', observations: ''};} 
            }
        })



    ;
});


