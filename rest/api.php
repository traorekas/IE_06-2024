<?php
// ---- Configuration ---- //

ini_set('display_errors', 1);

$CONNECTION_STRING = 'pgsql://securiport:SeCuRiPoRt@localhost/supervisor?charset=utf8';
//$CONNECTION_STRING = 'pgsql://postgres:securiport12@localhost/supervisor?charset=utf8';
//$CONNECTION_STRING = 'pgsql://postgres:SeCuRiPoRt@localhost/supervisor?charset=utf8';
$LOGIN_METHOD = 'ldap';  // Other valid values: 'database'
//$LDAP = array( "host" => "ldap://PCO11AB0011", "port" => 389, "version" => 3, "rdn" => "uid=%s,ou=People,dc=maxcrc,dc=com" );
$LDAP = array(
    "host" => "ldap://192.168.2.226",
    //"host" => "ldap://192.168.2.101",
    "port" => 389,
    "version" => 3,
    "rdn" => "%s@office.securiportci.local",
    "baseDN" => "DC=office,DC=securiportci,DC=local",
    "filter" => "(&(objectCategory=user))",
    "adminGroup" => "supervisorappadmin",
    "supervisorGroup" => "supervisorapp"
);

// ---- Initialization ---- //

require_once 'api.init.php';
require_once 'api.utils.php';

// ---- Define routes and implement views ---- //

// All responses are JSON files.
header('Content-type: text/javascript');

// Services for login and logout.
//    get  /
//    post /login
//    get  /logout
include_once 'api.svcs.login.php';

// Services for getting lists and find rows by id.
//    get /list/:types
//    get /findById/:type/:id
//    get /enrollments/foreignValues
include_once 'api.svcs.get.php';

// Services for update or delete rows.
//     put    /:type/:id
//     delete /:type/:id
include_once 'api.svcs.edit.php';

// Services submit an enrollment or get the list of enrollments submitted.
//     post /enrollments
//     post /enrollments/search
include_once 'api.svcs.enrollments.php';

//ktraore(Servicing)
// Services submit an maintenance or get the list of maintenances submitted.
//     post /maintenances
//     post /maintenances/search
include_once 'api.svcs.maintenances.php';

//ktraore(Servicing)
// Services submit an maintenance or get the list of maintenances submitted.
//     post /movement
//     post /movement/search
include_once 'api.svcs.movement.php';

//ktraore(Servicing)
// Services submit an maintenance or get the list of maintenances submitted.
//     post /device
//     post /device/search
include_once 'api.svcs.device.php';


//ktraore(Servicing)
// Services submit an maintenance or get the list of maintenances submitted.
//     post /devicesMovement
//     post /devicesMovement/search
//     post /devicesMovement/Update
include_once 'api.svcs.devicesMovement.php';

//ktraore(Servicing)
// Services submit an maintenance or get the list of maintenances submitted.
//     post /devicesCategories
//     post /devicesCategories/search
//     post /devicesCategories/Update
include_once 'api.svcs.devicesCategories.php';


//ktraore(Servicing)
// Services submit an maintenance or get the list of maintenances submitted.
//     post /stations
//     post /stations/search
//     post /stations/Update
include_once 'api.svcs.stations.php';

// Test service.
//$app->get('/test/:id', function($id) {
//    $rows = Technician::all();
//    $maps = ApiUtils::rowsToMaps($rows);
//    print_r($maps);
//});

// Run application.
$app->run();