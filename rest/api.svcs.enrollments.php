<?php

// Service for submit a new enrollment.
$app->post('/enrollments', function () use ($app) {
    try {
        // Verify if the user is logged.
        if(isset($_SESSION['user'])) {
            // The user is logged. Get input.
            $request = $app->request();
            $input = json_decode($request->getBody()); 

            // Save enrollment.
            $attributes = ApiUtils::mapToAttributes("Enrollment", $input);    
            $attributes['enr_skipped_person'] = $attributes['enr_skipped_person']? 1 : 0;
            $attributes['enr_skipped_fingerprints'] = $attributes['enr_skipped_fingerprints']? 1 : 0;
            $attributes['enr_with_other_errors'] = $attributes['enr_with_other_errors']? 1 : 0;
            $attributes['enr_machine_readable'] = $attributes['enr_machine_readable']? 1 : 0;
            $attributes['enr_fingerprints'] = $attributes['enr_fingerprints'];
            $attributes['enr_technician_id'] = $_SESSION['user']['id'];
            $attributes['enr_date'] = date('Y-m-d H:i:s');
            $enrollment = new Enrollment($attributes);
            $enrollment->save();

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

// Service for get the list of enrollments.
$app->post('/enrollments/search', function () use ($app) {
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
            $sqlQuery = "SELECT * FROM enrollments_view WHERE enr_technician_id = ?";
            $params = array($_SESSION['user']['id']);
            if(!empty($start)) {
                $sqlQuery = $sqlQuery . " AND enr_date >= ?";
                array_push($params, $start);
            }
            if(!empty($end)) {
                $sqlQuery = $sqlQuery . " AND enr_date <= ?";
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

// Service for delete an enrollment from the database.
$app->post('/enrollments/delete/:id', function ($id) {
    try {
        // Verify that the user is logged.
        if(isset($_SESSION['user'])) {
            $error = null;
            $message = null;
            
            // Get enrolment and verify permissions.
            $row = Enrollment::find($id);
            if($_SESSION['user']['is_admin'] || $_SESSION['user']['id'] == $row['enr_technician_id']) {
                // Delete entry.
                $row->delete();
            } else {
                // Set error.
                $error = "CantDeleteEnrollment";
                $message = "You don't have rights to delete this enrollment";
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
