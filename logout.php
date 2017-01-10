<?php
/**
    Author: Vishvas Handa (100044749)
    Version: 1.0
    
    logout.php is used to remove user session credentials from the database and to redirect the user to login page.
*/
	session_start();
	if ($_SERVER['HTTPS'] != "on") 
	{
	    $url = "https://". $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	    header("Location: $url");
	    exit;
	}
	if(isset($_SESSION['admin']))
	{
		$admin = $_SESSION['admin'];
	}
	if(session_destroy())
	{
		if($admin==0)
			header("Location: login.php");
		else if($admin==1)
			header("Location: admin.php");
		else
			header("Location: login.php");
	}
?>
