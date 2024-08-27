<?php

// Service for submit a new maintenance.
$app->post('/maintenances', function () use ($app) {
    try {
        // Verify if the user is logged.
        if(isset($_SESSION['user'])) {
            // The user is logged. Get input.
            $request = $app->request();
            $input = json_decode($request->getBody()); 

            // Save maintenance.

					$attributes = ApiUtils::mapToAttributes("Maintenance", $input); 										
					$attributes['fp'] = $attributes['fp']? 1 : 0;
					$attributes['pp'] = $attributes['pp']? 1 : 0;
					$attributes['kbd'] = $attributes['kbd']? 1 : 0;
					$attributes['mos'] = $attributes['mos']? 1 : 0;
					$attributes['ups'] = $attributes['ups']? 1 : 0;
					$attributes['suw'] = $attributes['suw'];	
					$attributes['sus'] = $attributes['sus'];
					$attributes['time1'] = $attributes['time1'];
					$attributes['time2'] = $attributes['time2'];
					$attributes['time3'] = $attributes['time3'];
					$attributes['timeavg'] = $attributes['timeavg'];			
					$attributes['rcg'] = $attributes['rcg']? 1 : 0;
					$attributes['av'] = $attributes['av']? 1 : 0;
					$attributes['cln'] = $attributes['cln']? 1 : 0;
					$attributes['ss'] = $attributes['ss']? 1 : 0;
					$attributes['snd'] = $attributes['snd']? 1 : 0;
					$attributes['net'] = $attributes['net']? 1 : 0;
					$attributes['swi'] = $attributes['swi']? 1 : 0;
					$attributes['usb'] = $attributes['usb']? 1 : 0;
					$attributes['station_id'] = $attributes['station_id'];	
					$attributes['date_upkeep'] = $attributes['date_upkeep'];
					$attributes['user_id'] = $_SESSION['user']['id'];						
					$attributes['validated'] = $attributes['validated']? 1 : 0;
					$attributes['observations'] = $attributes['observations'];		
					$maintenance = new Maintenance($attributes);
					$maintenance->save();
					
/*						
					$KstationId = $attributes['station_id'];
					$Datekeep = $attributes['date_upkeep'];
					$Date_Keep = date('Y-m-d H:i:s');
					    //$sqlQuery = "SELECT * FROM stations WHERE id = ". $KstationId;   date('Y-m-d',$time);
						$sqlQuery = "INSERT INTO stations (id, create_date ,zone_id ,screenid_type ,desktop_id ,fp_id ,pp_id ,kbd_id, mos_id ,ups_id ,active ,cam_id) VALUES (9,'2019-05-15', 2, 'AllInOne1','Desk11Abb','fp10Abb','pp10Abb', 'kbd10Abb','mos10Abb','ups10Abb',0,'cam10Abb')";
					$conn = ActiveRecord\ConnectionManager::get_connection("development");  
					$query = $conn->query($sqlQuery);
					//$res = $query->execute();
*/	
					 			
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

//---------------------------------------------------------------------------------------------------------------------------------------------//

// Service for get the list of maintenances.
$app->post('/maintenances/RefreshStations', function () use ($app) {
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
			$sqlQuery = "SELECT  ";            
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
        // An exception ocurred. Return an error message.
        echo json_encode(array("error" => "Unexpected", "message" => $e->getMessage()));
        $GLOBALS['log']->LogError($e->getMessage());
    }
});


//--------------------------------------------------------------------------------------------------------------------------------------------//


// Service for get the list of maintenances.
$app->post('/maintenances/search', function () use ($app) {
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
			$sqlQuery = "SELECT date_upkeep ,user_id ,station_id,zone_id ,screenid_type ,desktop_id ,fp_id ,pp_id ,kbd_id ,mos_id ,ups_id ,cam_id ,active ";
            $sqlQuery .= "FROM maintenances RIGHT JOIN stations ON (maintenances.station_id = stations.id) user_id = ?";
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
        // An exception ocurred. Return an error message.
        echo json_encode(array("error" => "Unexpected", "message" => $e->getMessage()));
        $GLOBALS['log']->LogError($e->getMessage());
    }
});

// Service for delete a maintenance from the database.
$app->post('/maintenances/delete/:id', function ($id) {
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
