
// Declare module.
var appCtrl = angular.module('myApp.Controllers', ['toastr']);

// Controller for the navigation bar.
appCtrl.controller('NavbarCtrl', function ($scope, $rootScope, $location, RestSvcs, MySession) {
    // Update name displayed at header when user changes.
    $scope.user = null;
    $rootScope.$on('userUpdated', function () {
        $scope.user = MySession.user !== null? MySession.user.username : null;
    });
    
    /**
     * Fuction invoked the first time that the page is loaded.
     */
    $scope.init = function() {
        // Call the ping service to verify if the user is logged and if is an administrator.
        RestSvcs.ping(function(userLogged) {
            // If the user is not logged or if is not an administrator, redirect to the login page.
            if(userLogged !== true || !MySession.user.is_admin) {
                MySession.setUser(null);
                $location.path("/login");
            }
            
            // Update the UI.
            if(!$scope.$$phase) $scope.$apply();
        });
    };
});

// Controller for the home page.
appCtrl.controller('IntroCtrl', function ($scope, $location) {
    // Go method.
    $scope.go = function(path) {
        $location.path(path);
    };
});

// Controller for the login view.
appCtrl.controller('LoginCtrl', function ($scope, $rootScope, $route, $location, toastr, RestSvcs, MySession) {
    // Define variables.
    $scope.user = {name: MySession.user !== null? MySession.user.username : null, pass: null};
    $scope.logged = MySession.isLogged();
    $rootScope.$on('userUpdated', function () {
        $route.reload();
    });

    // Define login method.
    $scope.login = function() {
        // Verify parameters.
        if($scope.user.name === null || $scope.user.name.length <= 0) { 
            toastr.error("The username can't be empty", null);
            return;
        }
        if($scope.user.pass === null || $scope.user.pass.length <= 0) {
            toastr.error("The password can't be empty", null); 
            return;
        }
        
        // Invoke login service.
        RestSvcs.login($scope.user.name, $scope.user.pass, function(logged) {
            // Clear the password.
            $scope.user.pass = '';

            // If logged, redirect to the main page.
            if(logged) {
                $location.path("/");
            }
            
            // Update the UI.
            if(!$scope.$$phase) $scope.$apply();
        });
    };
    
    // Define logout method.
    $scope.logout = function() {
        // Invoke logout service.
        RestSvcs.logout(function(success) {
            // Update the UI.
            if(!$scope.$$phase) $scope.$apply();
        });
    };
});



// Controller for display all the register of a table.
appCtrl.controller('ListCtrl', function ($scope, $location, $routeParams, RestSvcs, dataType) {
    // Initialize variables.
    $scope.rows = [];

    // Get list of register.
    RestSvcs.list(dataType, function(lists) {
        // Verify if the register were found.
        if(lists != null && lists[dataType] != null) {
            // Update the list.
            $scope.rows = lists[dataType];
      
            // Update the UI.
            if(!$scope.$$phase) $scope.$apply();
        }
    });
    
    // Function to edit or create a row.
    $scope.edit = function(id) {
        $location.path("/"+dataType+"/" + id);
    };
});

// Controller for an edit form.
appCtrl.controller('FormCtrl', function ($scope, $routeParams, $location, RestSvcs, toastr, dataType, returnUrl, defaultEntry) {
    // Initialize variables.
    $scope.type = dataType;
    $scope.entry = defaultEntry;
    
    // Get the entry to edit.
    if(!isNaN($routeParams.id)) {
        RestSvcs.findById(dataType, $routeParams.id, function(row) {
            // Verify if the row was read.
            if(row !== false) {
                $scope.entry = row;
            }

            // Update UI.
            if(!$scope.$$phase) $scope.$apply();
        });
    }
    
    // Behaviour for the 'cancel' button.
    $scope.goBack = function() {
        $location.path(returnUrl);
        return false;
    };
    
    // Behaviour for submitting the form.
    $scope.save = function(form) {
        // Verify if the form is valid.
        if(form.$valid) {
            // Call service to save changes.
            RestSvcs.save(dataType, $scope.entry, function(res) {
                if(res) {
                    toastr.success('The changes were saved', 'Success');
                    if(isNaN($routeParams.id)) $scope.goBack();
                }
            });
        } else {
            // Show error message.
            toastr.error('They are some errors in the form', 'Error');
        }
    };
    
    // Behaviour for the 'delete' button.
    $scope.delete = function() {
        RestSvcs.delete(dataType, $routeParams.id, function(res) {
            // If the register was deleted, go back to the list view.
            if(res) $scope.goBack();
        });
        return false;
    };
});



// Controller for the flights page.
appCtrl.controller('FlightsCtrl', function ($scope, $location, RestSvcs) {
    // Initialize variables.
    $scope.flights = [];
    $scope.companies = [];
    
    // Get list of campanies.
    RestSvcs.list("company-flight", function(lists) {
        if(lists != null) {
            // Update the lists.
            $scope.flights = lists['flight'];
            $scope.companies = lists['company'];
            
            // Update the UI.
            if(!$scope.$$phase) $scope.$apply();
        }
    });
    
    // Function to edit or create a row.
    $scope.edit = function(id) {
        $location.path("/flight/" + id);
    };
    
    // Function to get the company of a flight.
    $scope.getCompany = function(flight) {
        var res = null;
        try { res = _.findWhere($scope.companies, {id: flight.company_id}); }catch(e) {}
        return res !== null? res.name : '';
    };
});

// Controller for the flight form.
appCtrl.controller('FlightCtrl', function ($scope, $routeParams, $location, RestSvcs, toastr) {
    // Initialize variables.
    $scope.entry = {id: null, name: '', company_id: null};
    $scope.companies = [];
    
    // Get the list of companies.
    RestSvcs.list("companies", function(lists) {
        if(lists != null && lists['companies'] != null) {
            // Update the list.
            $scope.companies = lists['companies'];
            
            // Update the UI.
            if(!$scope.$$phase) $scope.$apply();
        }
    });
    
    // Get the entry to edit.
    if(!isNaN($routeParams.id)) {
        RestSvcs.findById("flight", $routeParams.id, function(row) {
            // Verify if the row was read.
            if(row !== false) {
                $scope.entry = row;
            }
            
            // Update UI.
            if(!$scope.$$phase) $scope.$apply();
        });
    }
    
    // Behaviour for the 'cancel' button.
    $scope.goBack = function() {
        $location.path("/flights");
        return false;
    };
    
    // Behaviour for submitting the form.
    $scope.save = function(form) {
        // Verify if the form is valid.
        if(form.$valid) {
            // Call service to save changes.        
            RestSvcs.save("flight", $scope.entry, function(res) {
                if(res) {
                    toastr.success('The changes were saved', 'Success');
                    if(isNaN($routeParams.id)) $scope.goBack();
                }
            });
        } else {
            // Show error message.
            toastr.error('They are some errors in the form', 'Error');            
        }
    };
    
    // Behaviour for the 'delete' button.
    $scope.delete = function() {
        RestSvcs.delete("flight", $routeParams.id, function(res) {
            // If the register was deleted, go back to the list view.
            if(res) $scope.goBack();
        });
        return false;
    };
});
