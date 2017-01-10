<?php
/**
    Author: Vishvas Handa
    Version: 1.0

*/
    include("db_config.php");
    session_start();
    $resultStr = '';

    if ($_SERVER['HTTPS'] != "on") 
    {
        $url = "https://". $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        header("Location: $url");
        exit;
    }
    if(isset($_SESSION['user']) && isset($_SESSION['userId']) && isset($_SESSION['email']) && $_SESSION['email']!="")
    {
        $userID = $_SESSION['userId'];
        $user = $_SESSION['user'];
        $email = $_SESSION['email'];
        if($_POST)
        {
            $ansArr = array();
            $selectAnsQuery = "EXEC usp_SelectAnswers";
            $ansRS = sqlsrv_query($conn, $selectAnsQuery);
            if($ansRS===false)
            {
                die(print_r(sqlsrv_errors(), true));
            }
            if(sqlsrv_has_rows($ansRS))
            {
                while($tableRow = sqlsrv_fetch_array($ansRS))
                {
                    array_push($ansArr, array(
                        'q_id'=>$tableRow['QUESTION_ID'],
                        'o_id'=>$tableRow['OPTION_ID']
                        ));
                }
            }
            sqlsrv_free_stmt($ansRS);

            $count=0;
            if(count($_POST)==count($ansArr))
            {
                foreach ($ansArr as $row)
                {
                    if($_POST[$row['q_id']]==$row['o_id'])
                    {
                        $count++;
                    }
                }
                if($count==count($ansArr))
                {
                    $procedureParams = array(array(&$userID));
                    $updateQuery = "EXEC usp_UpdateUserDetails @userId = ?";
                    $update = sqlsrv_query($conn, $updateQuery, $procedureParams);
                    if($update)
                    {
                        sqlsrv_free_stmt( $update);
                    }

                    $insertStmt = "EXEC usp_InsertTestDetails '".$email."', 'Y'";
                    $insert = sqlsrv_query($conn, $insertStmt);
                    if($insert)
                    {
                        sqlsrv_free_stmt( $insert);
                    }

                    $selectUserStmt = "EXEC usp_SelectUserDetails @userId = ?";
                    $selectResult = sqlsrv_query($conn, $selectUserStmt, $procedureParams);
                    if($selectResult)
                    {
                       if(sqlsrv_has_rows($selectResult))
                        {
                            date_default_timezone_set('Australia/Melbourne');
                            while($tableRow = sqlsrv_fetch_array($selectResult))
                            {
                                $resultStr= "<h2>Congratulations, ".$tableRow['FIRST_NAME']
                                ."! You have passed the test.</h2><br><br><h3>Following are the details of your test attempt</h3>"
                                ."<br><h5>(You may print this page as evidence of your test result if you wish to do so.)</h5><br><br>Name: "
                                .$tableRow['FIRST_NAME']." ".$tableRow['LAST_NAME']."<br>ID: ".$userID."<br>Email: ".$tableRow['EMAIL']
                                ."<br>Test Status: ".$tableRow['TEST_STATUS']."ass<br>Test Attempts: ".$tableRow['TEST_ATTEMPTS']
                                ."<br>Attempt Date/Time: ".date("D M j Y G:i:s T")."<br>";
                            }
                        }
                    }
                    sqlsrv_free_stmt($selectResult);
                }
                else
                {
                    $userUpdateStmt = "EXEC usp_FUpdateUserDetails ".$userID;
                    $update = sqlsrv_query($conn, $userUpdateStmt);
                    if($update)
                    {
                        sqlsrv_free_stmt($update);
                        $resultStr= "Attempt failed, correct answers = ".$count."<br> Retake test: <a href='test.php'>click here</a>";
                    }

                    $insertStmt = "EXEC usp_InsertTestDetails '".$email."', 'N'";
                    $insert = sqlsrv_query($conn, $insertStmt);
                    if($insert)
                    {
                        sqlsrv_free_stmt( $insert);
                    }
                }
            }
            else
            {
                $resultStr=  "<br><b>All questions are compulsary to answer!</b><br> retake test: <a href='test.php'>click here</a>";
            }
        }
        else
        {
            header("Location: test.php");
        }
    }
    else
    {
        header("Location: login.php");
    }
    sqlsrv_close($conn);
?>
<html>
<head>
    <title>Test Outcome</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="jquery-ui-1.12.1.custom/external/jquery/jquery.js"></script>
    <script src="jquery-ui-1.12.1.custom/jquery-ui.js"></script>
    <link type="text/css" href="jquery-ui-1.12.1.custom/jquery-ui.min.css" rel="stylesheet" />
    <link type="text/css" href="jquery-ui-1.12.1.custom/jquery-ui.structure.css" rel="stylesheet" />
    <link type="text/css" href="jquery-ui-1.12.1.custom/jquery-ui.theme.css" rel="stylesheet" />
    <link href="css/w3.css" rel="stylesheet" />
    <link type="text/css" href="css/training.css" rel="stylesheet" />
</head>
<body>
  <div class="cssdivheader">
  </div>
  <div class="cssdivMainContent">

      <div class="cssdivResultContent">
        <br/>
    <?php echo $resultStr;?>
</div>
<div class="display-topright">
<a href="logout.php" class="w3-btn btntraining">Logout</a>
</div>
</div>
</body>
</html>
