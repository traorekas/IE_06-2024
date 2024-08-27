<?php

// Service for submit a new station.
$app->post('/devicesCategories', function () use ($app) {
    try {
        // Verify if the user is logged.
        if(isset($_SESSION['user'])) {
            // The user is logged. Get input.
            $request = $app->request();
            $input = json_decode($request->getBody()); 

            // Save DevicesCategorie.

					$attributes = ApiUtils::mapToAttributes("DevicesCategorie", $input); 					
					$attributes['c_code'] = $attributes['c_code'];
					$attributes['c_designation'] = $attributes['c_designation'];
					$attributes['c_classe'] = $attributes['c_classe'];
					$attributes['active'] = $attributes['active'];				
					$devicesCategorie = new DevicesCategorie($attributes);
					$devicesCategorie->save();
							
            // Print result.
            echo json_encode(array("error" => null));

        } else {
            // The user is not logged, return error message.
           echo json_encode(array("error" => "InvalidLogin", "message" => null));
        }
    }catch(Exception $e) {
        // An exception ocurred. Return an error message.
        echo json_encode(array("error" => "Unexpected", "message" => $e->getMessage()));
        $GLOBALS['log']->LogError($e->getMessage());
    }
});


// Service for Update Stations.
$app->post('/devicesCategories/Update', function () use ($app) {
    try {
        // Verify if the user is logged.
        if(isset($_SESSION['user'])) {
            // The user is logged. Get input.
            $request = $app->request();
            $input = json_decode($request->getBody()); 
            if (is_object($input)) { $input = get_object_vars($input); }
            			
			////$station_id = $input['id'];
            // Get Station Id.
			$sqlQuery = "UPDATE stations SET active = 0 WHERE id = 14";
			
			            
            // Execute query.k
            $conn = ActiveRecord\ConnectionManager::get_connection("development");  
            $query = $conn->query($sqlQuery);
            $res = $query->execute();
			
            if($res) {
                $records = $query->fetchAll();
/* 				
					$attributes = ApiUtils::mapToAttributes("Station", $input);
					$attributes['id'] = $records['id'];
					$attributes['active'] = false;				
					$station = new Station($attributes);
					$station->save();
 */				
            } else {
                $records = null;
            }
            return $records;
            // Print result.
            echo json_encode(array("error" => null, "records" => $records));
        } else {
            // The user is not logged, return error message.
           echo json_encode(array("error" => "InvalidLogin", "message" => null));
        }
    }catch(Exception $e) {
        // An exception ocurred. Return an error message.
        echo json_encode(array("error" => "Unexpected", "message" => $e->getMessage()));
        $GLOBALS['log']->LogError($e->getMessage());
    }
});

// Service for get the list of stations.
$app->post('/devicesCategories/search', function () use ($app) {
    try {
        // Verify if the user is logged.
        if(isset($_SESSION['user'])) {
            // The user is logged. Get input.
            $request = $app->request();
            $input = json_decode($request->getBody()); 
            if (is_object($input)) { $input = get_object_vars($input); }
            
            // Get Station Id.
            $screenid_type = $input['ScreenId_Type'];
            $desktop_id = $input['DesktopId'];
            $dateServer =  date('Y-m-d H:i:s');
            // Define query and parameters.
            $sqlQuery = "SELECT * FROM stations user_id = ?";
            $params = array($_SESSION['user']['id']);
            if(!empty($screenid_type)) {
                $sqlQuery = $sqlQuery . " AND screenid_type = ?";
                array_push($params, $ScreenId_Type);
            }
            if(!empty($desktop_id)) {
                $sqlQuery = $sqlQuery . " AND desktop_id = ?";
                array_push($params, $desktop_id);
            }			
			if(!empty($dateServer)) {
                $sqlQuery = $sqlQuery . " AND create_date > ?";
                array_push($params, $dateServer);
            }
            
            // Execute query.
            $conn = ActiveRecord\ConnectionManager::get_connection("development");  
            $query = $conn->query($sqlQuery, $params);
            $res = $query->execute();
            if($res) {
                $records = $query->fetchAll();
/* 				
					$attributes = ApiUtils::mapToAttributes("Station", $input);
					$attributes['id'] = $records['id'];
					$attributes['active'] = false;				
					$station = new Station($attributes);
					$station->save();
 */				
            } else {
                $records = null;
            }
            return $records;
            // Print result.
            echo json_encode(array("error" => null, "records" => $records));
        } else {
            // The user is not logged, return error message.
           echo json_encode(array("error" => "InvalidLogin", "message" => null));
        }
    }catch(Exception $e) {
        // An exception ocurred. Return an error message.
        echo json_encode(array("error" => "Unexpected", "message" => $e->getMessage()));
        $GLOBALS['log']->LogError($e->getMessage());
    }
});

// Service for delete a maintenance from the database.
$app->post('/devicesCategories/delete/:id', function ($id) {
    try {
        // Verify that the user is logged.
        if(isset($_SESSION['user'])) {
            $error = null;
            $message = null;
            
            // Get enrolment and verify permissions.
            $row = Maintenance::find($id);
            if($_SESSION['user']['is_admin'] || $_SESSION['user']['id'] == $row['user_id']) {
                // Delete entry.
                $row->delete();
            } else {
                // Set error.
                $error = "CantDeleteEnrollment";
                $message = "You don't have rights to delete this maintenance enrollment";
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
