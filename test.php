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
		$questArr = array();
		$selectQuestQuery = "EXEC usp_SelectQuestions;";
		$questRS = sqlsrv_query($conn, $selectQuestQuery);
		if($questRS===false)
		{
			die(print_r(sqlsrv_errors(), true));
		}
		if(sqlsrv_has_rows($questRS))
		{
			while($tableRow = sqlsrv_fetch_array($questRS))
			{
				array_push($questArr, array(
					'q_id'=>$tableRow['QUESTION_ID'],
					'question'=>$tableRow['QUESTION_STRING']
					));
			}
		}
		$optionArr = array();
		$selectOptionsQuery = "EXEC usp_SelectOptions;";
		$optionRS = sqlsrv_query($conn, $selectOptionsQuery);
		if($optionRS===false)
		{
			die(print_r(sqlsrv_errors(), true));
		}
		if(sqlsrv_has_rows($optionRS))
		{
			while($tableRow = sqlsrv_fetch_array($optionRS))
			{
				array_push($optionArr, array(
					'o_id'=>$tableRow['OPTION_ID'],
					'q_id'=>$tableRow['QUESTION_ID'],
					'option'=>$tableRow['OPTION_STRING']
					));
			}
		}
		$bodyStr="<form id='testForm' method='post' action='result.php'>";
		$buttonStr="";
		
		foreach ($questArr as $question) 
		{
			$buttonStr.='<button class="w3-btn demo" onclick="currentDiv('.$question['q_id'].')">'.$question['q_id'].'</button>';
			$bodyStr.='<div class="mySlides" style="width:100%">
  <div class="heading">Q'.$question['q_id'].'. '.$question['question'].'</div><div class="description">';
  			foreach ($optionArr as $option) 
  			{
  				if($option['q_id']==$question['q_id'])
  				{
  					$bodyStr.='<input type="radio" name="'.$option['q_id'].'" value="'.$option['o_id'].'"/> '.$option['option'].'<br><br>';
  				}
  			}
  			$bodyStr.='</div></div>';
		}
		$bodyStr.='</form>';
	}
	else
	{
		header("Location: login.php");
	}

?>

<!DOCTYPE html>
<html>
<head>
<title>Educator Exam</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="css/w3.css" rel="stylesheet" />
<link type="text/css" href="css/training.css" rel="stylesheet" />
</head>
<body>
	<div class="cssdivheader">
	</div>
	<div class="cssdivMainContent">

	<div class="cssdivContent">
<div class="w3-container" style="text-align:center;">
  <h2 class="cssheaderText">Educator Knowledge Test</h2>
  <p class="csswarningText">All the questions must be answered correctly to pass the test.</p>
</div>

<div class="w3-content" style="max-width:80%; min-height: 470px; border:1px grey solid;">
  <?php echo $bodyStr;?>
</div>

<div class="w3-center">
  <div class="w3-section">
    <button class="w3-btn btntraining" onclick="plusDivs(-1)">Prev</button>
    <button class="w3-btn btntraining" onclick="plusDivs(1)">Next</button>
  </div>
  <?php echo $buttonStr;?>
</div>
<div class="display-topright">
<button type="submit" form="testForm" class="w3-btn btntraining" >Submit Test!</button>
<a href="logout.php" class="w3-btn btntraining">Logout</a>
</div>
<script>
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
  if (n > x.length) {slideIndex = 1}
  if (n < 1) {slideIndex = x.length}
  for (i = 0; i < x.length; i++) {
     x[i].style.display = "none";
  }
  for (i = 0; i < dots.length; i++) {
     dots[i].className = dots[i].className.replace(" w3-red", "");
  }
  x[slideIndex-1].style.display = "block";
  dots[slideIndex-1].className += " w3-red";
}
</script>
</div>
</body>
</html>
