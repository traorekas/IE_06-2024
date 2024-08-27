<?php

// Service for create or update a row in the database.
$app->put('/:type/:id', function ($type, $id) use ($app) {
    try {
        // Verify that the user is logged.
        if(isset($_SESSION['user']) && $_SESSION['user']['is_admin']) {
            // Get input.
            $request = $app->request();
            $input = json_decode($request->getBody()); 
            
            // Transform input to attributes.
            $attributes = ApiUtils::mapToAttributes($type, $input);

            // Verify if entry must be created or updated.
            $entry = null;
            if(empty($id) || !is_numeric($id)) {
                // Create entry.
                if($type == "company") { $entry = new Company($attributes); }
                if($type == "flight") { $entry = new Flight($attributes); }
                if($type == "officer") { $entry = new Officer($attributes); }
                if($type == "place") { $entry = new Place($attributes); }
                if($type == "technician") { $entry = new Technician($attributes); }
 				
				//(Maintenance)
					if($type == "movementType") { $entry = new MovementType($attributes); }
					if($type == "position") { $entry = new Position($attributes); }
					if($type == "location") { $entry = new Location($attributes); }
					if($type == "zone") { $entry = new Zone($attributes); }
					if($type == "movement") { $entry = new Movement($attributes); }
					if($type == "station") { $entry = new Station($attributes); }
					if($type == "device") { $entry = new Device($attributes); }
					if($type == "devicesCategorie") { $entry = new DevicesCategorie($attributes); }
					if($type == "maintenance") { $entry = new Maintenance($attributes); }
				//
 				
                // Save it.
                if($entry != null) { $entry->save(); }
            } else {
                // Find entry.
                if($type == "company") { $entry = Company::find($id); }
                if($type == "flight") { $entry = Flight::find($id); }
                if($type == "officer") { $entry = Officer::find($id); }
                if($type == "place") { $entry = Place::find($id); }
                if($type == "technician") { $entry = Technician::find($id); }
								
                //(Maintenance)
					if($type == "movementType") { $entry = new MovementType($id); }
					if($type == "position") { $entry = new Position($id); }
					if($type == "location") { $entry = new Location($id); }
					if($type == "zone") { $entry = new Zone($id); }
					if($type == "movement") { $entry = new Movement($id); }
					if($type == "station") { $entry = new Station($id); }
					if($type == "device") { $entry = new Device($id); }
					if($type == "devicesCategorie") { $entry = new DevicesCategorie($id); }
					if($type == "maintenance") { $entry = new Maintenance($id); }
				//
 				
				
                // Update it.
                if($entry != null) { $entry->update_attributes($attributes); }
            }
            
            // Return result.
            echo json_encode(array(
                "error" => $entry != null? null : "EntryNotSaved",
                "message" => $entry != null? null : "The entry could not be saved/updated",
            ));            
        } else {
            // Return error message.
            echo json_encode(array(
                "error" => "AccessDenied",
                "message" => "You must be logged as administrator to consume this service"
            ));
        }
    }catch(Exception $e) {
        // An exception ocurred. Return an error message.
        echo json_encode(array("error" => "Unexpected", "message" => $e->getMessage()));
        $GLOBALS['log']->LogError($e->getMessage());
    }    
});

// Service for delete a row from the database.
$app->delete('/:type/:id', function ($type, $id) {
    try {
        // Verify that the user is logged.
        if(isset($_SESSION['user']) && $_SESSION['user']['is_admin']) {
            $error = null;
            $message = null;
            
            // Companies.
            if($type == "company") {
                // Verify if the company has flight's.
                $count = Flight::count(array('conditions' => 'ar_aerial_company_id = ' . $id));
                if($count <= 0) {
                    // Delete company.
                    $row = Company::find($id);
                    $row->delete();
                } else {
                    // Set error.
                    $error = "CantDeleteCompany";
                    $message = "The company could not be deleted since it has flights linked";
                }
            }
            
            // Flights.
            if($type == "flight") {
                // Verify if the flight has enrollments.
                $count = Enrollment::count(array('conditions' => 'enr_flight_id = ' . $id));
                if($count <= 0) {
                    // Delete flight.
                    $row = Flight::find($id);
                    $row->delete();
                } else {
                    // Set error.
                    $error = "CantDeleteFlight";
                    $message = "The flight could not be deleted since it has enrollments linked";
                }                
            }
            
            // Officers.
            if($type == "officer") {
                // Verify if the officer has enrollments.
                $count = Enrollment::count(array('conditions' => 'enr_officer_id = ' . $id));
                if($count <= 0) {
                    // Delete officer.
                    $row = Officer::find($id);
                    $row->delete();
                } else {
                    // Set error.
                    $error = "CantDeleteOfficer";
                    $message = "The officer could not be deleted since it has enrollments linked";
                }                
            }
            
            // Places.
            if($type == "place") {
                // Verify if the place has enrollments.
                $count = Enrollment::count(array('conditions' => 'enr_place_id = ' . $id));
                if($count <= 0) {
                    // Delete place.
                    $row = Place::find($id);
                    $row->delete();
                } else {
                    // Set error.
                    $error = "CantDeletePlace";
                    $message = "The place could not be deleted since it has enrollments linked";
                }                
            }
            
            // Technicians.
            if($type == "technician") {
                // Verify if the technician has enrollments.
                $count = Enrollment::count(array('conditions' => 'enr_technician_id = ' . $id));
                if($count <= 0) {
                    // Delete flight.
                    $row = Technician::find($id);
                    $row->delete();
                } else {
                    // Set error.
                    $error = "CantDeleteTechnician";
                    $message = "The technician could not be deleted since it has enrollments linked";
                }                                
            }
            
		//(Maintenance)

			    // MovementType.
					if($type == "movementType") {
						// Verify if the position has locations.
						$count = Movements::count(array('conditions' => 'movementtype_id = ' . $id));
						if($count <= 0) {
							// Delete company.
							$row = MovementType::find($id);
							$row->delete();
						} else {
							// Set error.
							$error = "CantDeletePosition";
							$message = "The position could not be deleted since it has movement linked";
						}
					}
							
			    // Position.
					if($type == "position") {
						// Verify if the position has locations.
						$count = Location::count(array('conditions' => 'position_id = ' . $id));
						if($count <= 0) {
							// Delete company.
							$row = Position::find($id);
							$row->delete();
						} else {
							// Set error.
							$error = "CantDeletePosition";
							$message = "The position could not be deleted since it has location linked";
						}
					}					
			    
				// Location.
					if($type == "location") {
						// Verify if the position has locations.
						$count = Positions::count(array('conditions' => 'position_id = ' . $id));
						if($count <= 0) {
							// Delete company.
							$row = Location::find($id);
							$row->delete();
						} else {
							// Set error.
							$error = "CantDeleteLocation";
							$message = "The location could not be deleted since it has zone linked";
						}
					}
					
			    // Zone.
					if($type == "zone") {
						// Verify if the position has locations.
						$count = Locations::count(array('conditions' => 'location_id = ' . $id));
						if($count <= 0) {
							// Delete company.
							$row = Zone::find($id);
							$row->delete();
						} else {
							// Set error.
							$error = "CantDeleteZone";
							$message = "The zone could not be deleted since it has station linked";
						}
					}

			    // Maintenance.
					if($type == "maintenance") {
						// Verify if the position has station.
						$count = Stations::count(array('conditions' => 'station_id = ' . $id));
						if($count <= 0) {
							// Delete company.
							$row = Maintenance::find($id);
							$row->delete();
						} else {
							// Set error.
							$error = "CantDeleteMaintenance";
							$message = "The maintenance could not be deleted since it has station linked";
						}
					}

			    // Station.
					if($type == "station") {
						// Verify if the position has zone.
						$count = Zones::count(array('conditions' => 'zone_id = ' . $id));
						if($count <= 0) {
							// Delete company.
							$row = Station::find($id);
							$row->delete();
						} else {
							// Set error.
							$error = "CantDeleteStation";
							$message = "The station could not be deleted since it has zone linked";
						}
					}

			    // DevicesCategorie.
					if($type == "devicesCategorie") {
						// Verify if the position has zone.
						$count = Device::count(array('conditions' => 'categorie_id = ' . $id));
						if($count <= 0) {
							// Delete company.
							$row = DevicesCategorie::find($id);
							$row->delete();
						} else {
							// Set error.
							$error = "CantDeleteDevicesCategorie";
							$message = "The Devices Categorie could not be deleted since it has zone linked";
						}
					}
					
					
					// Devices.
					if($type == "device") {
						// Verify if the device is used.
						$count = Device::count(array('conditions' => 'use_id = ' . $id));
						if($count <= 0) {
							// Delete company.
							$row = Device::find($id);
							$row->delete();
						} else {
							// Set error.
							$error = "CantDeleteDevices";
							$message = "The device could not be deleted since it's in use by Station";
						}
					}
					
		//(Maintenance)
 		
		
            // Return result.
            echo json_encode(array(
                "error" => $error,
                "message" => $message
            ));
        } else {
            // Return error message.
            echo json_encode(array(
                "error" => "AccessDenied",
                "message" => "You must be logged as administrator to consume this service"
            ));
        }
    }catch(Exception $e) {
        // An exception ocurred. Return an error message.
        echo json_encode(array("error" => "Unexpected", "message" => $e->getMessage()));
        $GLOBALS['log']->LogError($e->getMessage());
    }    
});

