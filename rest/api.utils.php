<?php

/**
 * Static class that provides several utilities methods.
 */
class ApiUtils {
    /**
     * Get the values of some properties of an array, rename them and store them in a new array.
     * 
     * @param Mixed $map An associative array or an object to copy.
     * @param Array $names An array of strings indicating the properties that must be copied.
     * @param Array $newNames An array of strings with the new names of the properties.
     * @return Array 
     */
    private static function copyArrayValuesAndChangeKeyNames($map, $names, $newNames) {
        // Validate parameters
        if(!is_null($names)) {
            // Verify that $map is an associative array.
            if (is_object($map)) { $map = get_object_vars($map); }

            // Make the changes.
            $res = array();
            for($k=0; $k<count($names); $k++) {
                $newName = (is_null($newNames) || $k >= count($newNames) || empty($newNames[$k]))? $names[$k] : $newNames[$k];
                $res[$newName] = $map[$names[$k]];
            }
            return $res;
        } else {
            // Change can't be done, return original object.
            return $map;
        }
    }
    
    /**
     * Gets an associative array from a database row.
     * 
     * @param Object $row A database row.
     */
    public static function rowToMap($row) {
        $map = null;

        // Parse row according to his type.
                 if(get_class($row) == "Company") {
            $map = ApiUtils::copyArrayValuesAndChangeKeyNames($row->attributes(), 
	    array("id", "ar_name", "ar_disabled"), 
	    array("id", "name", "disabled"));
        }
                 if(get_class($row) == "Flight") {
            $map = ApiUtils::copyArrayValuesAndChangeKeyNames($row->attributes(), 
	    array("id", "ar_flight_number", "ar_aerial_company_id", "ar_disabled"), 
	    array("id", "name", "company_id", "disabled"));
        }
                 if(get_class($row) == "Officer") {
            $map = ApiUtils::copyArrayValuesAndChangeKeyNames($row->attributes(),
            array("id", "of_first_name", "of_last_name", "of_code", "of_section", "of_subsection", "of_login_code", "of_observations", "of_disabled"), 
            array("id", "first_name", "last_name", "code", "section", "subsection", "login_code", "observations", "disabled"));
        }
                 if(get_class($row) == "Place") {
            $map = ApiUtils::copyArrayValuesAndChangeKeyNames($row->attributes(), 
	    array("id", "pl_description", "pl_disabled"), 
	    array("id", "name", "disabled"));
        }
                 if(get_class($row) == "Technician") {
            $map = ApiUtils::copyArrayValuesAndChangeKeyNames($row->attributes(), 
            array("id", "te_login_usr", "te_first_name", "te_last_name", "te_email", "te_observations", "te_admin"), 
            array("id", "username", "first_name", "last_name", "email", "observations", "is_admin"));
        }
        	 if(get_class($row) == "Enrollment") {
            $map = ApiUtils::copyArrayValuesAndChangeKeyNames($row->attributes(), 
            array("enr_time_seconds", "enr_place_id", "enr_officer_id", "enr_flight_id", "enr_skipped_person", "enr_skipped_fingerprints", "enr_with_other_errors", "enr_wrong_airline", "enr_machine_readable", "enr_observations", "enr_fingerprints "),
            array("time_seconds", "place_id", "officer_id", "flight_id", "skipped_person", "skipped_fingerprints", "other_errors", "wrong_airline", "machine_readable", "observations", "fingerprints_scanned"));
        }
        
 		//(Maintenance)
		 if(get_class($row) == "MovementType") {
            $map = ApiUtils::copyArrayValuesAndChangeKeyNames($row->attributes(), 
	    array("id", "mt_designation"), 
	    array("Id", "MTDesignation"));
        }

		 if(get_class($row) == "DevicesCategorie") {
            $map = ApiUtils::copyArrayValuesAndChangeKeyNames($row->attributes(), 
	    array("id","c_code","c_designation","c_classe", "active"), 
	    array("Id","CCode","CDesignation","CClasse","Active"));
        }
		
                 if(get_class($row) == "Position") {
            $map = ApiUtils::copyArrayValuesAndChangeKeyNames($row->attributes(), 
	    array("id", "p_designation"), 
	    array("Id", "PDesignation"));
        }	
		
                 if(get_class($row) == "Location") {
            $map = ApiUtils::copyArrayValuesAndChangeKeyNames($row->attributes(), 
	    array("id", "position_id", "l_designation"), 
	    array("Id", "PositionId", "LDesignation"));
        }
		
                 if(get_class($row) == "Zone") {
            $map = ApiUtils::copyArrayValuesAndChangeKeyNames($row->attributes(), 
	    array("id", "location_id", "z_designation"), 
	    array("Id", "LocationId", "ZDesignation"));
        }	
		
		 if(get_class($row) == "Device") {
            $map = ApiUtils::copyArrayValuesAndChangeKeyNames($row->attributes(),
            array("add_date", "categorie_id", "device", "observations", "used"),
	    array("AddDate", "CategorieId", "Device", "Observations", "IsUsed"));
        }		
		
		 if(get_class($row) == "Maintenance") {
            $map = ApiUtils::copyArrayValuesAndChangeKeyNames($row->attributes(), 
            array("date_upkeep", "user_id", "station_id", "fp", "pp", "kbd", "mos", "ups", "suw", "sus","time1", "time2", "time3", "timeavg", "rcg", "av", "cln", "ss", "snd", "net", "swi", "usb", "validated", "observations"),
            array("DateUpkeep", "UserId", "StationId", "Fp", "Pp", "Kbd", "Mos", "Ups", "Suw", "Sus","Time1", "Time2", "Time3", "TimeAvg", "Rcg", "Av", "Cln", "Ss", "Snd", "Net", "Swi", "Usb", "Validated",  "Observations"));
        }

		 if(get_class($row) == "Station") {
            $map = ApiUtils::copyArrayValuesAndChangeKeyNames($row->attributes(), 
            array("id","create_date", "zone_id", "screenid_type", "desktop_id", "fp_id", "pp_id", "kbd_id", "mos_id", "ups_id" , "cam_id", "active"),
            array("Id","CreateDate", "ZoneId", "ScreenId_Type", "DesktopId", "FpId", "PpId", "KbdId", "MosId", "UpsId", "CamId", "IsActive"));
        }
									
		if(get_class($row) == "Movement") {
            $map = ApiUtils::copyArrayValuesAndChangeKeyNames($row->attributes(), 
            array("id","move_date", "user_id", "movefrom_id", "moveto_id", "device_id", "movementtype_id", "used"),
            array("Id","MoveDate", "UserId", "MoveFrom_Id", "MoveTo_Id", "DeviceId", "MovementType_Id", "IsUsed"));
        }
		//
		
	 		
        return $map;
    }
    
    /**
     * Get an array of maps from an array of database's rows.
     * 
     * @param Array $rows An array of database rows.
     */
    public static function rowsToMaps($rows) {
        $res = array();
        
        // Validate parameters.
        if(!is_array($rows)) { 
            $rows = array($rows); 
        }

        // Iterate over each row.
        foreach($rows as $entry) {
            $map = ApiUtils::rowToMap($entry);
            if($map != null) {
                array_push($res, $map); 
            }
        }

        return $res;
    }
    
    /**
     * Creates a copy of an associative array and changes the names of theirs keys to match the attribute's name of a table.
     * 
     * @param string $type A string indicating the table of the data.
     * @param Array $map An associative array.
     */
    public static function mapToAttributes($type, $map) {
        $res = null;

        // Verify that $map is not an object.
        if (is_object($map)) { $map = get_object_vars($map); }
        
        // Parse row according to his type.
        if(strcasecmp($type, "Company") == 0) {
            $res = ApiUtils::copyArrayValuesAndChangeKeyNames($map, 
	    array("id", "name", "disabled"), 
	    array("id", "ar_name", "ar_disabled"));
        }
        if(strcasecmp($type, "Flight") == 0) {
            $res = ApiUtils::copyArrayValuesAndChangeKeyNames($map, 
	    array("id", "name", "company_id", "disabled"), 
	    array("id", "ar_flight_number", "ar_aerial_company_id", "ar_disabled"));
        }
        if(strcasecmp($type, "Officer") == 0) {
            $res = ApiUtils::copyArrayValuesAndChangeKeyNames($map, 
            array("id", "first_name", "last_name", "code", "section", "subsection", "login_code", "observations", "disabled"), 
            array("id", "of_first_name", "of_last_name", "of_code", "of_section", "of_subsection", "of_login_code", "of_observations", "of_disabled"));
        }
        if(strcasecmp($type, "Place") == 0) {
            $res = ApiUtils::copyArrayValuesAndChangeKeyNames($map, 
	    array("id", "name", "disabled"), 
	    array("id", "pl_description", "pl_disabled") );
        }
        if(strcasecmp($type, "Technician") == 0) {
            $res = ApiUtils::copyArrayValuesAndChangeKeyNames($map, 
            array("id", "username", "first_name", "last_name", "email", "observations", "is_admin"), 
            array("id", "te_login_usr", "te_first_name", "te_last_name", "te_email", "te_observations", "te_admin"));
        }
        if(strcasecmp($type, "Enrollment") == 0) {
            $res = ApiUtils::copyArrayValuesAndChangeKeyNames($map, 
            array("time_seconds", "place_id", "officer_id", "flight_id", "skipped_person", "skipped_fingerprints", "other_errors", "wrong_airline", "machine_readable", "observations", "fingerprints_scanned"),
            array("enr_time_seconds", "enr_place_id", "enr_officer_id", "enr_flight_id", "enr_skipped_person", "enr_skipped_fingerprints", "enr_with_other_errors", "enr_wrong_airline", "enr_machine_readable", "enr_observations", "enr_fingerprints"));
        }
           
		//(Maintenance)  
	
		if(strcasecmp($type, "MovementType") == 0) {
            $res = ApiUtils::copyArrayValuesAndChangeKeyNames($map,
	    array("Id", "MTDesignation"), 
	    array("id", "mt_designation"));
        }	
			
		if(strcasecmp($type, "DevicesCategorie") == 0) {
            $res = ApiUtils::copyArrayValuesAndChangeKeyNames($map, 
	    array("Id","CCode", "CDesignation", "CClasse", "Active"), 
	    array("id","c_code", "c_designation", "c_classe", "active"));
        }
		
		if(strcasecmp($type, "Position") == 0) {
            $res = ApiUtils::copyArrayValuesAndChangeKeyNames($map,
	    array("Id", "PDesignation"), 
	    array("id", "p_designation"));
        }
		
		if(strcasecmp($type, "Location") == 0) {
            $res = ApiUtils::copyArrayValuesAndChangeKeyNames($map,
	    array("Id", "PositionId", "LDesignation"), 
	    array("id", "position_id", "l_designation"));
        }	
		
		if(strcasecmp($type, "Zone") == 0) {
            $res = ApiUtils::copyArrayValuesAndChangeKeyNames($map,
	    array("Id", "LocationId", "ZDesignation"), 
	    array("id", "location_id", "z_designation"));
        }	
		
		 if(strcasecmp($type, "Device") == 0) {
            $res = ApiUtils::copyArrayValuesAndChangeKeyNames($map,
            array("AddDate", "CategorieId", "Device", "Observations","IsUsed"),
            array("add_date", "categorie_id", "device", "observations","used"));
        }		

		 if(strcasecmp($type, "Maintenance") == 0) {
            $res = ApiUtils::copyArrayValuesAndChangeKeyNames($map, 
	    array("DateUpKeep", "StationId","Fp", "Pp", "Kbd", "Mos", "Ups", "Suw", "Sus","Time1", "Time2", "Time3", "TimeAvg", "Rcg", "Av", "Cln", "Ss", "Snd", "Net", "Swi", "Usb", "Validated", "Observations"),
            array("date_upkeep", "station_id","fp", "pp", "kbd", "mos", "ups", "suw", "sus","time1", "time2", "time3", "timeavg", "rcg", "av", "cln", "ss", "snd", "net", "swi", "usb", "validated", "observations"));
        }
		//
		 if(strcasecmp($type, "Station") == 0) {
            $res = ApiUtils::copyArrayValuesAndChangeKeyNames($map,
            array("CreateDate", "ZoneId", "ScreenId_Type", "DesktopId", "FpId", "PpId", "KbdId", "MosId", "UpsId", "CamId", "IsActive"),
            array("create_date", "zone_id", "screenid_type", "desktop_id", "fp_id", "pp_id", "kbd_id", "mos_id", "ups_id", "cam_id", "active"));
        }					
		
		 if(strcasecmp($type, "Movement") == 0) {
            $res = ApiUtils::copyArrayValuesAndChangeKeyNames($map, 
            array("Id","MoveDate", "UserId", "MoveFrom_Id", "MoveTo_Id", "DeviceId", "MovementType_Id", "IsUsed"),
            array("id","move_date", "user_id", "movefrom_id", "moveto_id", "device_id", "movementtype_id", "used"));
        }		
		
		//  		  
        return $res;
    }
    
    /**
     * Gets the data of a LDAP user.
     * 
     * @param Object $ldapConn The LDAP connection's object.
     * @param String $username The user's name.
     * @return Array An associative array with the values 'username', 'firstname', 'lastname', 'admin' and 'supervisor'.
     */
    public static function GetLDAPUserData($ldapConn, $username) {
        // Define default values.
        $firstname = null; $lastname = null; $isAdmin = 0; $isSupervisor = false;

        try{
            // Get LDAP registers.
            if ( ($search = ldap_search($ldapConn, $GLOBALS['LDAP']['baseDN'], $GLOBALS['LDAP']['filter'])) ) {
                $entries = ldap_get_entries($ldapConn, $search);

                // Search for the register of the user.
                $userPrincipalName = sprintf($GLOBALS['LDAP']['rdn'], $username);
                for ($i=0; $i<$entries["count"]; $i++) {
                    $entry = $entries[$i];
                    if(isset($entry["userprincipalname"]) && $entry["userprincipalname"][0] == $userPrincipalName) {
                        if(isset($entry["givenname"])) { $firstname = $entry["givenname"][0]; }
                        if(isset($entry["sn"])) { $lastname = $entry["sn"][0]; }
                        if(isset($entry["memberof"])) {
                            foreach ($entry["memberof"] as $group) {
                                if( strrpos($group.'', $GLOBALS['LDAP']['adminGroup']) ) { $isAdmin = 1; }
                                if( strrpos($group.'', $GLOBALS['LDAP']['supervisorGroup']) ) { $isSupervisor = true; }
                            }
                        }
                    }
                }
            }
        } catch (Exception $ex) { $GLOBALS['log']->LogError($ex->getMessage()); }

        // Return result.
        return array(
            "username" => $username,
            "firstname" => $firstname,
            "lastname" => $lastname,
            "admin" => $isAdmin,
            "supervisor" => $isSupervisor);
    }
    
    /**
     * Gets the technician object corresponding to a LDAP user.
     * 
     * @param Object $ldapConn The LDAP connection's object.
     * @param String $username The user's name.
     * @return Technician The corresponding technician.
     */
    public static function LDAPUserToTechnician($ldapConn, $username) {
        // Get user data.
        $userData = ApiUtils::GetLDAPUserData($ldapConn, $username);

        // Return result.
        if($userData['supervisor']) {
            return new Technician( array(
                "te_login_usr" => $userData['username'], 
                "te_first_name" => $userData['firstname'], 
                "te_last_name" => $userData['lastname'], 
                "te_admin" => $userData['isAdmin'] )
            );
        } else {
            return null;
        }
    }
}
