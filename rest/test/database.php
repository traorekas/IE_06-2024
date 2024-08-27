<?php
/*
$CONNECTION_STRING = 'pgsql://postgres:Securiport12@localhost/supervisor?charset=utf8';

require_once 'libs/ActiveRecord/ActiveRecord.php';
 
ActiveRecord\Config::initialize(function($cfg) {
    $cfg->set_model_directory('libs/models');
    $cfg->set_connections(array('development' => $GLOBALS['CONNECTION_STRING']));
});


function changeAttributeNames($map, $names, $newNames) {
    // Validate parameters
    if(!is_null($names) && !is_null($newNames)) {
        // Verify that $map is an associative array.
        if (is_object($map)) { $map = get_object_vars($map); }
        
        // Make the changes.
        $res = array();
        for($k=0; $k<count($names); $k++) {
            $res[$newNames[$k]] = $map[$names[$k]];
        }
        return $res;
    } else {
        // Change can't be done, return original object.
        return $map;
    }
}

function getAttributes($records, $names=null, $newNames=null) {
    $res = array();
    for($i=0; $i<count($records); $i++) {
        $attr = $records[$i]->attributes();
        
        if(is_null($names) || is_null($newNames)) {
            $res[$i] = $attr;
        } else {
            $res[$i] = changeAttributeNames($attr, $names, $newNames);
        }
    }
    return $res;
}

// ---------- ----------- //

$conn = ActiveRecord\ConnectionManager::get_connection("development");  
$values = array(1);
$res = $conn->query("SELECT * FROM enrollments_view WHERE enr_technician_id = ?", $values);
echo "<br/>---<br/>";
print_r($res);
$algo = $res->execute();
if($algo) {
    $data = $res->fetchAll();
    print_r($data);
    echo "<br/>---<br/>";
}
*/