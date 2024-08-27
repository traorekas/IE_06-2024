<?php

// Service for submit a new maintenance.
$app->post('/movement', function () use ($app) {
    try {
        // Verify if the user is logged.
        if(isset($_SESSION['user'])) {
            // The user is logged. Get input.
            $request = $app->request();
            $input = json_decode($request->getBody()); 

            // Save  Movement.
					$attributes = ApiUtils::mapToAttributes("Movement", $input); 										
					$attributes['move_date'] = $attributes['move_date'];
					$attributes['user_id'] = $attributes['user_id'];
					$attributes['movefrom_id'] = $attributes['movefrom_id'];
					$attributes['moveto_id'] = $attributes['moveto_id'];
					$attributes['device_id'] = $attributes['device_id'];
					$attributes['movementtype_id'] = $attributes['movementtype_id'];					
					$attributes['used'] = $attributes['used'];
					$movement = new Movement($attributes);
					$movement->save(); 			
            // Print result.
            echo json_encode(array("error" => null));

        } else {
            // The user is not logged, return error message.
           echo json_encode(array("error" => "InvalidLogin", "message" => null));
        }
    }catch(Exception $e) {
        // An exception occurred. Return an error message.
        echo json_encode(array("error" => "Unexpected", "message" => $e->getMessage()));
        $GLOBALS['log']->LogError($e->getMessage());
    }
});

// Service for get the list of devices.
$app->post('/movement/search', function () use ($app) {
    try {
        // Verify if the user is logged.
        if(isset($_SESSION['user'])) {
            // The user is logged. Get input.
            $request = $app->request();
            $input = json_decode($request->getBody()); 
            if (is_object($input)) { $input = get_object_vars($input); }
            
            // Get dates.
            $start = $input['start'];
            $end = $input['end'];
            
            // Define query and parameters.
            $sqlQuery = "SELECT * FROM devices user_id = ?";
            $params = array($_SESSION['user']['id']);
            if(!empty($start)) {
                $sqlQuery = $sqlQuery . " AND date_upkeep >= ?";
                array_push($params, $start);
            }
            if(!empty($end)) {
                $sqlQuery = $sqlQuery . " AND date_upkeep <= ?";
                array_push($params, $end);
            }
            
            // Execute query.
            $conn = ActiveRecord\ConnectionManager::get_connection("development");  
            $query = $conn->query($sqlQuery, $params);
            $res = $query->execute();
            if($res) {
                $records = $query->fetchAll();
            } else {
                $records = null;
            }
            
            // Print result.
            echo json_encode(array("error" => null, "records" => $records));
        } else {
            // The user is not logged, return error message.
           echo json_encode(array("error" => "InvalidLogin", "message" => null));
        }
    }catch(Exception $e) {
        // An exception occurred. Return an error message.
        echo json_encode(array("error" => "Unexpected", "message" => $e->getMessage()));
        $GLOBALS['log']->LogError($e->getMessage());
    }
});

// Service for delete a devices from the database.
$app->post('/movement/delete/:id', function ($id) {
    try {
        // Verify that the user is logged.
        if(isset($_SESSION['user'])) {
            $error = null;
            $message = null;
            
            // Get enrolment and verify permissions.
            $row = Devices::find($id);
            if($_SESSION['user']['is_admin'] || $_SESSION['user']['id'] == $row['user_id']) {
                // Delete entry.
                $row->delete();
            } else {
                // Set error.
                $error = "CantDeleteEnrollment";
                $message = "You don't have rights to delete this devices enrollment";
            }
 
            // Return result.
            echo json_encode(array(
                "error" => $error,
                "message" => $message
            ));
        } else {
            // Return error message.
            echo json_encode(array(
                "error" => "AccessDenied",
                "message" => "You must be logged to consume execute this action"
            ));
        }
    }catch(Exception $e) {
        // An exception ocurred. Return an error message.
        echo json_encode(array("error" => "Unexpected", "message" => $e->getMessage()));
        $GLOBALS['log']->LogError($e->getMessage());
    }    
});
