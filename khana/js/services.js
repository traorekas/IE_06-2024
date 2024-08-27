
// Declare module.
var appSvc = angular.module('myApp.Services', ['toastr', 'blockUI']);

// Service for share the data of the current session.
appSvc.factory('MySession', function($rootScope) {
    var session = {
        /**
         * User's data.
         */
        user: null,
        
        /**
         * Update the logged user.
         * 
         * @param {Object} user The user's data.
         */
        setUser: function(user) {
            session.user = user;
            $rootScope.$broadcast('userUpdated');
        },
        
        /**
         * Verifies if the user is logged.
         * 
         * @returns {Boolean} A boolean indicating if the user is logged.
         */
        isLogged: function() {
            return session.user !== null;
        }
    };
    
    return session;
});

// Service for getting a wrapper of the $http object.
appSvc.factory('MyHttp', function($http, toastr, blockUI) {
    /**
     * Class to be used as wrapper for the object '$http'.
     */
    function myHttp() {
        this.dialog = true;
        this.toast = true;
        this.onSuccess = null;
        this.onError = null;
        
        var that = this;
        this.successHandler = function(data, status, headers, config) {
            // Close dialog.
            if(that.dialog) blockUI.stop();
            
            // Verify if the operation was a success.
            if(data !== null && data.error === null) {
                // Invoke success callback.
                if(that.onSuccess) that.onSuccess(data);  
            } else {
                // Verify if the error must be show.
                if(that.toast) {
                    if(data.error === "InvalidLogin") {
                        toastr.error("You could not be logged, the username and/or the password are incorrect", null);
                    } else if(data.error === "AccessDenied") {
                        toastr.error("You don't have enough privileges to access to this functionality", null);
                    } else {
                        toastr.error((data.error === "Unexpected" || data.message === undefined || data.message === null)? "An unexpected error ocurred at the server. Please contact your system administrator." : data.message, null);
                        console.log(data);
                    }
                }

                // Invoke error callback.
                if(that.onError) that.onError(data);  
            }
        };
        this.errorHandler = function(data, status, headers, config) {
            // Close dialog.
            if(that.dialog) blockUI.stop();

            // Show error message.
            if(that.toast) toastr.error("The application could not connect with the server. Please verify you network connection or contact your network administrator.", null);

            // Invoke callback.
            if(that.onError) that.onError(data);     
        };            
    };

    /**
     * Defines if a waiting dialog must be show while doing the request.
     * 
     * @param {Boolean} show A boolean indicating if the dialog must be show.
     * @returns {myHttp} A reference to the 'this' pointer.
     */
    myHttp.prototype.showDialog = function(show) { 
        this.dialog = show; 
        return this; 
    };

    /**
     * Defines if a toast message must be show when encountering an error.
     * 
     * @param {Boolean} show A boolean indicating if the toasts must be show.
     * @returns {myHttp} A reference to the 'this' pointer.
     */
    myHttp.prototype.showToast = function(show) { 
        this.toast = show; 
        return this; 
    };

    /**
     * Allows to define the function to invoke if a request is a success.
     * 
     * @param {function} callback The function to invoke
     * @returns {myHttp} A reference to the 'this' pointer.
     */
    myHttp.prototype.success = function(callback) { 
        this.onSuccess = callback; 
        return this; 
    };

    /**
     * Allows to define the function to invoke if a request fails due to an error.
     * 
     * @param {function} callback The function to invoke
     * @returns {myHttp} A reference to the 'this' pointer.
     */
    myHttp.prototype.error = function(callback) { 
        this.onError = callback; 
        return this; 
    };
    
    /**
     * Consumes a 'get' REST service.
     * 
     * @param {String} url The url of the service.
     */
    myHttp.prototype.get = function(url) {
        // Show loading dialog.
        if(this.dialog) blockUI.start();

        // Invoke service.
        $http.get(url).success(this.successHandler).error(this.errorHandler);               
    };
    
    /**
     * Consumes a 'post' REST service.
     * 
     * @param {String} url The url of the service.
     * @param {Object} params The params to send to the service.
     */
    myHttp.prototype.post = function(url, params) {
        // Show loading dialog.
        if(this.dialog) blockUI.start();

        // Invoke service.
        $http.post(url, params).success(this.successHandler).error(this.errorHandler);               
    };
    
    /**
     * Consumes a 'delete' REST service.
     * 
     * @param {String} url The url of the service.
     */
    myHttp.prototype.delete = function(url) {
        // Show loading dialog.
        if(this.dialog) blockUI.start();

        // Invoke service.
        $http.delete(url).success(this.successHandler).error(this.errorHandler);               
    };
    
    /**
     * Consumes a 'put' REST service.
     * 
     * @param {String} url The url of the service.
     * @param {Object} params The params to send to the service.
     */
    myHttp.prototype.put = function(url, params) {
        // Show loading dialog.
        if(this.dialog) blockUI.start();

        // Invoke service.
        $http.put(url, params).success(this.successHandler).error(this.errorHandler);               
    };
    
    // Return reference to the class constructor.
    return myHttp;
});

// Service for simplify the interaction with the REST services.
appSvc.factory('RestSvcs', function(MyHttp, MySession, toastr) {    
    // List of services.
    var svcs = {        
        /**
         * A ping function that verifies if the server is online and if the user is logged
         * 
         * @param {Function} callback A callback function which receives a boolean indicating if the user is logged or 'null' is the server is not reachable.
         */
        ping: function(callback) {
            new MyHttp().showToast(false)
                .success(function(data) {
                    // Save user data, if available.
                    if(data !== null && data.user !== null) {
                        MySession.setUser(data.user);
                    }
        
                    // Return result.
                    callback(MySession.user !== null);  
                })
                .error(function(data) { 
                    // User's information coulb not been obtained.
                    callback(null); 
                })
                .get("../rest/api.php");  
        },
        
        /**
         * Login service.
         * 
         * @param {String} username The user's name.
         * @param {String} password The user's password.
         * @param {Function} callback A callback function that receives a boolean indicating if the user was logged.
         */
        login: function(username, password, callback) {
            new MyHttp()
                .success(function(data) {
                    // Verify if the user is an administrator.            
                    if(data.user.is_admin) {
                        // Save user's data.
                        MySession.setUser(data.user);
                    } else {
                        // Show error message and logout.
                        toastr.error("The user has not enough privileges to login in this application.", null);
                        svcs.logout();
                    }

                    // Return result.
                    callback(data.user.is_admin);                    
                })
                .error(function(data) { 
                    // User's could not be logged.
                    callback(false); 
                })
                .post("../rest/api.php/login", {'username': username, 'password': password});      
        },
        
        /**
         * Login service.
         * 
         * @param {Function} callback A callback function that receives a boolean indicating if the user was logged out.
         */
        logout: function(callback) {
            new MyHttp()
                .success(function(data) {
                    // Remove login data.
                    MySession.setUser(null);
        
                    // Return result.
                    if(callback) callback(true);  
                })
                .error(function(data) { 
                    // Logout failed.
                    if(callback) callback(false); 
                })
                .get("../rest/api.php/logout");  
        },
        
        /**
         * List service.
         * 
         * @param {String} types An string containing the name of all the types whose lists are requested.
         * @param {Function} callback A callback function that receives a object with the requested lists.
         */
        list: function(types, callback) {
            new MyHttp()
                .success(function(data) {
                    // Return result.
                    callback(data.lists);
                })
                .error(function(data) { 
                    // Data could not be obtained.
                    callback(false); 
                })
                .get("../rest/api.php/list/" + types);
        },

        /**
         * Service for get a row from the database.
         * 
         * @param {String} type An string indicating the row's type.
         * @param {Number} id An integer indicating the row's id.
         * @param {Function} callback A callback function that receives the row (or 'null' if the row not exists).
         */
        findById: function(type, id, callback) {
            new MyHttp()
                .success(function(data) {
                    // Return result.
                    callback(data.row);
                })
                .error(function(data) { 
                    // Data could not be obtained.
                    callback(false); 
                })
                .get("../rest/api.php/findById/" + type + "/" + id);
        },

        /**
         * Service for save a row in the database.
         * 
         * @param {String} type An string indicating the row's type.
         * @param {Object} id An object with the row's data.
         * @param {Function} callback A callback function that receives a boolean indicating if the row was deleted.
         */
        save: function(type, row, callback) {
            new MyHttp()
                .success(function(data) {
                    // Return result. 
                    if(callback) callback(true);
                })
                .error(function(data) { 
                    // Data could not be deleted.
                    if(callback) callback(false); 
                })
                .put("../rest/api.php/" + type + "/" + row.id, row);
        },

        /**
         * Service for delete a row from the database.
         * 
         * @param {String} type An string indicating the row's type.
         * @param {Number} id An integer indicating the row's id.
         * @param {Function} callback A callback function that receives a boolean indicating if the row was deleted.
         */
        delete: function(type, id, callback) {
            new MyHttp()
                .success(function(data) {
                    // Return result. 
                    if(callback) callback(true);
                })
                .error(function(data) { 
                    // Data could not be deleted.
                    if(callback) callback(false); 
                })
                .delete("../rest/api.php/" + type + "/" + id);
        }         
    };
    
    return svcs;
});



