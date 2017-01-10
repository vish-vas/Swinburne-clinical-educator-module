<?php
/**
	Author: Vishvas Handa
	Version: 1.0

*/
	include("db_config.php");
	session_start();
	$err = '';
	if ($_SERVER['HTTPS'] != "on") 
	{
	    $url = "https://". $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	    header("Location: $url");
	    exit;
	}
	if(isset($_SESSION['user']) && isset($_SESSION['userId']) && isset($_SESSION['email']) && $_SESSION['email']!="")
    {
		$contentArr = array();
		$selectContentQuery = 'EXEC usp_SelectTrainingMaterial';
		$result = sqlsrv_query($conn, $selectContentQuery);
		if($result===false)
		{
			die(print_r(sqlsrv_errors(), true));
		}
		if(sqlsrv_has_rows($result))
		{
			while($tableRow = sqlsrv_fetch_array($result))
			{
				array_push($contentArr, array(
					"id"=>$tableRow['ID'],
					"heading"=>$tableRow['HEADING'],
					"description"=>$tableRow['DESCRIPTION'],
					"body"=>$tableRow['BODY']
					));
			}

			$contentStr = "";
			$buttonStr = "";
			foreach ($contentArr as $content)
			{
				$buttonStr.='<button class="w3-btn demo" onclick="currentDiv('
					.$content['id'].')">'.$content['id'].'</button>';
				$contentStr .= '<div class="mySlides" style="width:100%">
			    <div class="heading">'.$content['heading'].'</div>
			    <div class="description">'.$content['description'].'</div>
			    <div class="cbody">
			    <ul>';
			    $list = explode('\n', $content['body']);
			    foreach ($list as $value) 
			    {
			      	$contentStr.= '<li>'.$value.'</li>';
			    }
			    $contentStr.='</ul></div></div>';
			}
		}
	}
	else
	{
		header("Location: login.php");
	}

?>

<!DOCTYPE html>
<HTML XMLns="http://www.w3.org/1999/xHTML">
<head>
    <title>Educator Training</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="jquery-ui-1.12.1.custom/external/jquery/jquery.js"></script>

    <link href="css/training.css" rel="stylesheet" />
		<link href="css/w3.css" rel="stylesheet" />
</head>
<body>
    <div class="cssdivheader">
    </div>
    <div class="cssdivMainContent">

        <div class="cssdivContent">
            <div class="w3-container" >
                <h2 class="cssheaderText">Welcome to Educator Training Module </h2>
            </div>

            <div class="w3-content" style="max-width:80%; border:1px grey solid;">
                <?php echo $contentStr;?>
            </div>

            <div class="w3-center">
                <div class="w3-section">
                    <button class="w3-btn btntraining" onclick="plusDivs(-1)">Prev</button>
                    <button class="w3-btn btntraining" onclick="plusDivs(1)">Next</button>
                </div>
                <?php echo $buttonStr;?>
            </div>
            <div class="display-topright">
                <a href="test.php" class="w3-btn btntraining">Take Test!</a>
                <a href="logout.php" class="w3-btn btntraining">Logout</a>
            </div>

        </div>
    </div>

</body>

<script language="javascript">
var slideIndex = 1;
showDivs(slideIndex);

function plusDivs(n) {
		showDivs(slideIndex += n);
}

function currentDiv(n) {
		showDivs(slideIndex = n);
}

function showDivs(n) {
		var i;
		var x = document.getElementsByClassName("mySlides");
		var dots = document.getElementsByClassName("demo");
		if (n > x.length) { slideIndex = 1 }
		if (n < 1) { slideIndex = x.length }
		for (i = 0; i < x.length; i++) {
				x[i].style.display = "none";
		}
		for (i = 0; i < dots.length; i++) {
				dots[i].className = dots[i].className.replace(" w3-red", "");
		}
		x[slideIndex - 1].style.display = "block";
		dots[slideIndex - 1].className += " w3-red";
}
</script>
</HTML>
