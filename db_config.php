<?php
/**
	Author: Vishvas Handa
	Version: 1.0 
	Project: Swinburne Clinical Educator Module
	File: db_config.php stores the application database credentials and other application data.
*/
$server = "tcp:swinburneclinicaleducator.database.windows.net,1433"; 
//database server address goes here. example: "tcp:swinburneclinicaleducator.database.windows.net,1433"
$user = "clinicaleducatormodule"; 
//database username goes here. example: "clinicalEducatorModule"
$pass = "Clinicaleducator123"; 
//database password goes here. example: "Clinicaleducator123"
$database = "swinburneClinicalEducator"; 
//database name goes here. example: "swinburneClinicalEducator"

$applicationWebAddress = "https://swinburneclinicaleducator.azurewebsites.net";
// The web address of the application (required for send email invite functionality). example: "https://swinburneclinicaleducator.azurewebsites.net"
$subj="CSHC Placement Test Web Link";
$body="Hello,%0D%0A%0D%0AFollowing is the link for Online Training and Testing application:%0D%0A%0D%0A".$applicationWebAddress."%0D%0A%0D%0AKindly take the test for completion of the placement procedure.%0D%0A%0D%0ACSHC Placements%0D%0A%0D%0APh. 92101161";
$invite = "mailto:?subject=".$subj."&body=".$body;
// This is the invite message that will be sent thru email and can be modified according to user's requirement from subject and body variables.

$connectionoptions = array("Database" => $database, 
                           "UID" => $user, 
                           "PWD" => $pass,						   
                           "Encrypt"=>false, 
                           "TrustServerCertificate"=>true);
 
$conn = sqlsrv_connect($server, $connectionoptions);
if( !$conn ) 
     die( print_r( sqlsrv_errors(), true));
?>