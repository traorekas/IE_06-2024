<?php

// Print current time.
$now = new DateTime();
echo "Starting at " . $now->format('Y-m-d H:i:s') . "<br/>";

// Connect to LDAP server.
$ad = ldap_connect('ldap://192.168.2.100', 389) or die("Could not connect!");
//$ad = ldap_connect('ldap://PCO11AB0011', 389) or die("Could not connect!");
//$ad = ldap_connect('ldap://localhost', 389) or die("Could not connect!");

// Set version number
ldap_set_option($ad, LDAP_OPT_PROTOCOL_VERSION, 3) or die("Could not set ldap protocol");
ldap_set_option($ad, LDAP_OPT_REFERRALS, 0) or die("Could not set ldap protocol");

// Binding to ldap server.
$bd = ldap_bind($ad, 'redmine@office.securiportci.local', 'MineRed142536');
//$bd = ldap_bind($ad, 'testin@office.securiportci.local', 'Pass147258');
//$bd = ldap_bind($ad, 'testout@office.securiportci.local', 'Pass147258');
//$bd = ldap_bind($ad, 'uid=jdoe,ou=People,dc=maxcrc,dc=com', 'sunflower') or die("Couldn't Connect");

// Verify result.
if($bd) {
    // Print success message.
    echo '<div style="color:green">The user has logged.</div><br/>';

    // Search for users in the domain.
    echo "Ask for the list of all users in the domain...<br/>";
    $base_dn = "DC=office,DC=securiportci,DC=local";
    $filter = "(&(objectCategory=user))";
    $search = ldap_search($ad, $base_dn, $filter);

    // Get data searched.
    $number_returned = ldap_count_entries($ad,$search);
    $info = ldap_get_entries($ad, $search);
    echo "The number of entries returned was: ". $number_returned."<br/><br/>";

    // Iterate data searched.
    foreach($info as $entry) {
//        // Print all attributes.
//        echo '<div style="font-family:Verdana; font-size:10pt;">';
//        if(isset($entry["name"])) { echo '<h3>' . $entry["name"][0] . '</h3>'; }
//        
//        if(!is_integer($entry)) {
//            foreach($entry as $key => $value) {
//                if(!is_integer($key)) {
//                    echo '<strong>' . $key . ':</strong> ';
//                    print_r($value);
//                    echo '<br/>';
//                }
//            }
//            echo '<br/><br/>';
//        }
//        echo '</div>';
        
        // Print attributes of interest.
        echo '<div style="font-family:Verdana; font-size:10pt;">';
        if(isset($entry["userprincipalname"])) {
            $htmlInfo = '';
            if(isset($entry["name"])) { $htmlInfo .= "<strong>Name</strong>: ". $entry["name"][0] . "<br/>"; }
            if(isset($entry["userprincipalname"])) { $htmlInfo .= "<strong>Principal name</strong>: ". $entry["userprincipalname"][0] . "<br/>"; }
            if(isset($entry["givenname"])) { $htmlInfo .= "<strong>Given name</strong>: ". $entry["givenname"][0] . "<br/>"; }
            if(isset($entry["sn"])) { $htmlInfo .= "<strong>SN</strong>: ". $entry["sn"][0] . "<br/>"; }
            if(isset($entry["email"])) { $htmlInfo .= "<strong>Email</strong>: ". $entry["email"][0] . "<br/>"; }
            $isAdmin = false;
            $isSupervisor = false;
            if(isset($entry["memberof"])) {
                $htmlInfo .= '<strong>Groups:</strong><br/><div style="font-size:8pt;">';
                foreach ($entry["memberof"] as $key => $group) {
                    if($key != "count") { $htmlInfo .= $group . '<br/>'; }

                    if( strrpos($group.'', "Administrators") ) { $isAdmin = true; }
                    if( strrpos($group.'', "supervisorapp") ) { $isSupervisor = true; }
                }
                $htmlInfo .= '</div>';
            }
            
            if($isAdmin) { $htmlInfo .= '<span style="color:darkblue">[Admin]</span><br/>'; }
            if($isSupervisor) { $htmlInfo .= '<span style="color:darkgreen">[Supervisor]</span><br/>'; }
            echo $htmlInfo . '<br/>';
        }
        echo '</div>';
    }        
} else {
    echo '<div style="color:red">Invalid combination of username and password.</div>';
}

// Close connection.
ldap_close($ad);
