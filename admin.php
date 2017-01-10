<?php
	include ("db_config.php");

	session_start();
	$err = "";
	if ($_SERVER['HTTPS'] != "on") 
    {
        $url = "https://". $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        header("Location: $url");
        exit;
    }

	if($_SESSION)
	{
		if(isset($_SESSION['user']) && isset($_SESSION['userId']) && isset($_SESSION['email']) && $_SESSION['email']!="")
		{
			if($_SESSION['admin']==0)
			{
				header("Location: login.php");
			}
		}
		if($_SESSION['admin']==1)
		{
			header("Location: export.php");
		}
	}
	else if($_POST)
	{
		if(isset($_POST['email']) && $_POST['email']!='' && isset($_POST['password']) && $_POST['password']!='')
		{
			$loginEmail = $_POST['email'];
			$password = md5($_POST['password']);
			$procedureParams = array(array(&$loginEmail), array(&$password));
			$sql = "EXEC usp_ValidateAdminLoginDetails @emailId = ?, @strPassword = ?";
			$selectResult = sqlsrv_query($conn, $sql, $procedureParams);
			if($selectResult)
            {
               if(sqlsrv_has_rows($selectResult))
                {
                	$tableRow = sqlsrv_fetch_array($selectResult);
                	if($tableRow['status']>0)
                	{
						$_SESSION['admin'] = 1;
						header("Location: export.php");
                	}
                	else
		            {
		            	$err = "<p class='red'>Email/ password combination not correct.</p>";
		            }
                }
            }
            sqlsrv_free_stmt($stmt);
			sqlsrv_close($conn);
		}
		else
		{
			$err = "<p class='red'>Please enter your email and password.</p>";
		}
	}
?>

<HTML XMLns="http://www.w3.org/1999/xHTML">
<head>
    <title>Educator Login for Swinburne's Clinical Educator Module</title>
    <link href="css/login.css" rel="stylesheet" />
    <style type="text/css">
        .label {
            display: inline-block;
            width: 150px;
            margin-right: 30px;
            text-align: left;
        }

        .input {
            size: 20px;
        }

        .div {
            margin: auto;
        }
        .red{
        	color: #F21C00;
        }
    </style>
</head>
<body>

    <form method="post">

        <div class="swinheader" >

            <div class="login-page">

                <div class="form">

                    <form class="login-form">

                        <center class="loginheader">LOGIN</center>
                        <input type="text" name="email" placeholder="email" />
                        <input type="password" name="password" placeholder="password" />
                        <button name="Login">Submit</button>
                    </form>
                    <br/>
                    <br/>
                    <div><?php echo $err;?></div>
                </div>
        	</div>
        </div>
    </form>

</body>
</HTML>
