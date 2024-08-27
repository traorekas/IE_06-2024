<?php

// Ping service.
$app->get('/', function () {
    $user = isset($_SESSION["user"])? $_SESSION["user"] : null;
    echo json_encode(array("error" => null, "user" => $user));
});

// Login service.
$app->post('/login', function () use ($app) {
    try {
        // Get parameters.
        $request = $app->request();
        $input = json_decode($request->getBody()); 
        if (is_object($input)) { $input = get_object_vars($input); }

        // Verify which login method use.
        if($GLOBALS['LOGIN_METHOD'] === 'database') {
            // Verify if an user with that name and password exists.
            $users = ApiUtils::rowsToMaps( Technician::find('all',array('conditions' => array('te_login_usr = ? AND te_login_psw = ?'))) );
            if(count($users) > 0) {
                // The user exists, save his data in the session and return a success message.
                $_SESSION["user"] = $users[0];
                echo json_encode(array("error" => null, "user" => $users[0] ));
            } else {
                // The user do not exists, return error message.
                echo json_encode(array("error" => "InvalidLogin", "message" => "Invalid username or password"));
            }
        } else {
            $invalid = true;
            
            try {
                // Use LDAP authentication.
                $ad = ldap_connect($GLOBALS['LDAP']['host'], $GLOBALS['LDAP']['port']);
                if($ad) {
                    // Set version number
                    ldap_set_option($ad, LDAP_OPT_PROTOCOL_VERSION, $GLOBALS['LDAP']['version']);
                    ldap_set_option($ad, LDAP_OPT_REFERRALS, 0);

                    // Binding to ldap server
                    $bd = false;
                    try {
                        $bd = ldap_bind($ad, sprintf($GLOBALS['LDAP']['rdn'], $input['username']), $input['password']);
                    }catch(Exception $ldapEx) { $GLOBALS['log']->LogError($ldapEx->getMessage()); }
                        
                    if($bd) {
                        // Username and password are good for LDAP. 
                        $invalid = false;
                        
                        // Verify if the user has permission to this application.
                        $userData = ApiUtils::GetLDAPUserData($ad, $input['username']);
                        if($userData['supervisor']) {
                            // Search if the user exists in the database.
                            $users = ApiUtils::rowsToMaps( Technician::find('all', array('conditions' => array('te_login_usr = ?', $input['username']))) );
                            if(count($users) > 0) {
                                // The user exists, save his data in the session and return a success message.
                                $_SESSION["user"] = $users[0];
                                echo json_encode(array("error" => null, "user" => $users[0] ));
                            } else {
                                // The user do not exists, create it.
                                $technician = new Technician( array(
                                    "te_login_usr" => $userData['username'], 
                                    "te_first_name" => $userData['firstname'], 
                                    "te_last_name" => $userData['lastname'], 
                                    "te_admin" => $userData['admin'] )
                                );
                                $technician->save();

                                // Return the user.
                                $user = ApiUtils::rowToMap($technician);
                                $_SESSION["user"] = $user;
                                echo json_encode(array("error" => null, "user" => $user ));
                            }
                        } else {
                            // The user do not belongs to the supervisor group. Return access denied error.
                            echo json_encode(array("error" => "AccessDenied", "message" => "The user is not authorized to use this application"));
                        }
                    }

                    // Close connection.
                    ldap_close($ad);
                }
            }catch(Exception $ex) {
                // An exception ocurred. Return an error message.
                echo json_encode(array("error" => "Unexpected", "message" => $ex->getMessage()));
                $GLOBALS['log']->LogError($ex->getMessage());
                $invalid = false;
            }

            // Default return value.
            if($invalid) { echo json_encode(array("error" => "InvalidLogin", "message" => "Invalid username or password")); }
        }
    }catch(Exception $e) {
        // An exception ocurred. Return an error message and undo logging.
        echo json_encode(array("error" => "Unexpected", "message" => $e->getMessage()));
        $GLOBALS['log']->LogError($e->getMessage());
        $_SESSION["user"] = null;
    }
});

// Logout service.
$app->get('/logout', function () {
    // Clear session and return success message.
    $_SESSION["user"] = null;
    echo json_encode(array("error" => null));
});
