<?php

// Service for get the rows of a table.
$app->get('/list/:types', function ($types) {
    try {
        // Verify that the user is logged.
        if(isset($_SESSION['user'])) {
            // Get list.
            $lists = array();  
            if(strpos($types, 'companies') !== false) { $lists['companies'] = ApiUtils::rowsToMaps( Company::find('all', array('order' => 'ar_name asc')) ); }
            if(strpos($types, 'flights') !== false) { $lists['flights'] = ApiUtils::rowsToMaps( Flight::find('all', array('order' => 'ar_flight_number asc')) ); }
            if(strpos($types, 'officers') !== false) { $lists['officers'] = ApiUtils::rowsToMaps( Officer::find('all', array('order' => 'of_code asc')) ); }
            if(strpos($types, 'places') !== false) { $lists['places'] = ApiUtils::rowsToMaps( Place::find('all', array('order' => 'pl_description asc')) ); }
            if(strpos($types, 'technicians') !== false) { $lists['technicians'] = ApiUtils::rowsToMaps( Technician::find('all', array('order' => 'te_login_usr asc')) ); }
			
 			//(Maintenance)
			if(strpos($types, 'movementTypes') !== false) { $lists['movementTypes'] = ApiUtils::rowsToMaps( MovementType::find('all', array('order' => 'mt_designation asc')) ); }				
			if(strpos($types, 'positions') !== false) { $lists['positions'] = ApiUtils::rowsToMaps( Position::find('all', array('order' => 'p_designation asc')) ); }
            if(strpos($types, 'locations') !== false) { $lists['locations'] = ApiUtils::rowsToMaps( Location::find('all', array('order' => 'l_designation asc')) ); }
            if(strpos($types, 'zones') !== false) { $lists['zones'] = ApiUtils::rowsToMaps( Zone::find('all', array('order' => 'z_designation asc')) ); }
			if(strpos($types, 'stations') !== false) { $lists['stations'] = ApiUtils::rowsToMaps( Station::find('all', array('order' => 'desktop_id asc')) ); }
	/**/	if(strpos($types, 'devicesCategories') !== false) { $lists['devicesCategories'] = ApiUtils::rowsToMaps( DevicesCategorie::find('all', array('order' => 'id asc')) ); }
			if(strpos($types, 'devices') !== false) { $lists['devices'] = ApiUtils::rowsToMaps( Device::find('all', array('order' => 'id asc')) ); }
			if(strpos($types, 'movements') !== false) { $lists['movements'] = ApiUtils::rowsToMaps( Movement::find('all', array('order' => 'device_id asc')) ); }
            if(strpos($types, 'maintenances') !== false) { $lists['maintenances'] = ApiUtils::rowsToMaps( Maintenances::find('all', array('order' => 'date_upkeep asc')) ); }
			//
 			
            if(strpos($types, 'company') !== false) { $lists['company'] = ApiUtils::rowsToMaps( Company::find('all', array('order' => 'ar_name asc')) ); }
            if(strpos($types, 'flight') !== false) { $lists['flight'] = ApiUtils::rowsToMaps( Flight::find('all', array('order' => 'ar_flight_number asc')) ); }
            if(strpos($types, 'officer') !== false) { $lists['officer'] = ApiUtils::rowsToMaps( Officer::find('all', array('order' => 'of_code asc')) ); }
            if(strpos($types, 'place') !== false) { $lists['place'] = ApiUtils::rowsToMaps( Place::find('all', array('order' => 'pl_description asc')) ); }
            if(strpos($types, 'technician') !== false) { $lists['technician'] = ApiUtils::rowsToMaps( Technician::find('all', array('order' => 'te_login_usr asc')) ); }
						
 			//(Maintenance)
			if(strpos($types, 'movementType') !== false) { $lists['movementType'] = ApiUtils::rowsToMaps( MovementType::find('all', array('order' => 'mt_designation asc')) ); }	
			if(strpos($types, 'position') !== false) { $lists['position'] = ApiUtils::rowsToMaps( Position::find('all', array('order' => 'p_designation asc')) ); }
            if(strpos($types, 'location') !== false) { $lists['location'] = ApiUtils::rowsToMaps( Location::find('all', array('order' => 'l_designation asc')) ); }
            if(strpos($types, 'zone') !== false) { $lists['zone'] = ApiUtils::rowsToMaps( Zone::find('all', array('order' => 'z_designation asc')) ); }
            if(strpos($types, 'station') !== false) { $lists['station'] = ApiUtils::rowsToMaps( Station::find('all', array('order' => 'desktop_id asc')) ); }
	/**/	if(strpos($types, 'devicesCategorie') !== false) { $lists['devicesCategorie'] = ApiUtils::rowsToMaps( DevicesCategorie::find('all', array('order' => 'id asc')) ); }
			if(strpos($types, 'device') !== false) { $lists['device'] = ApiUtils::rowsToMaps( Device::find('all', array('order' => 'id asc')) ); }
			if(strpos($types, 'movement') !== false) { $lists['movement'] = ApiUtils::rowsToMaps( Movement::find('all', array('order' => 'device_id asc')) ); }
            if(strpos($types, 'maintenance') !== false) { $lists['maintenance'] = ApiUtils::rowsToMaps( Maintenance::find('all', array('order' => 'date_upkeep asc')) ); }
			//  
  
	 
            // Return result.
            echo json_encode(array(
                "lists" => $lists,
                "error" => null
            ));
        } else {
            // Return error message.
            echo json_encode(array(
                "error" => "AccessDenied",
                "message" => "You must be logged to consume this service"
            ));
        }
    }catch(Exception $e) {
        // An exception occurred. Return an error message.
        echo json_encode(array("error" => "Unexpected", "message" => $e->getMessage()));
        $GLOBALS['log']->LogError($e->getMessage());
    }    
});

// Service for get a row from the database.
$app->get('/findById/:type/:id', function ($type, $id) {
    try {
        // Verify that the user is logged.
        if(isset($_SESSION['user'])) {
            // Get list.
            $row = array();
            if($type == "company") { $row = ApiUtils::rowToMap( Company::find($id) ); }
            if($type == "flight") { $row = ApiUtils::rowToMap( Flight::find($id) ); }
            if($type == "officer") { $row = ApiUtils::rowToMap( Officer::find($id) ); }
            if($type == "place") { $row = ApiUtils::rowToMap( Place::find($id) ); }
            if($type == "technician") { $row = ApiUtils::rowToMap( Technician::find($id) ); }
			
 			
			//(Maintenance)
			if($type == "movementType") { $row = ApiUtils::rowToMap( MovementType::find($id) ); }
			if($type == "position") { $row = ApiUtils::rowToMap( Position::find($id) ); }
            if($type == "location") { $row = ApiUtils::rowToMap( Location::find($id) ); }
            if($type == "zone") { $row = ApiUtils::rowToMap( Zone::find($id) ); }
            if($type == "movement") { $row = ApiUtils::rowToMap( Movement::find($id) ); }
			if($type == "station") { $row = ApiUtils::rowToMap( Station::find($id) ); }			
			if($type == "devicesCategorie") { $row = ApiUtils::rowToMap( DevicesCategorie::find($id) ); }
			if($type == "device") { $row = ApiUtils::rowToMap( Device::find($id) ); }			
            if($type == "maintenance") { $row = ApiUtils::rowToMap( Maintenance::find($id) ); }
			//
       
            // Return result.
            echo json_encode(array(
                "row" => $row,
                "error" => null
            ));
        } else {
            // Return error message.
            echo json_encode(array(
                "error" => "AccessDenied",
                "message" => "You must be logged to consume this service"
            ));
        }
    }catch(Exception $e) {
        // An exception ocurred. Return an error message.
        echo json_encode(array("error" => "Unexpected", "message" => $e->getMessage()));
        $GLOBALS['log']->LogError($e->getMessage());
    }    
});
