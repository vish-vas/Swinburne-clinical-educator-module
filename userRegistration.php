<?php
	include ("db_config.php");

	session_start();

	$out="";
	if ($_SERVER['HTTPS'] != "on") 
	{
	    $url = "https://". $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	    header("Location: $url");
	    exit;
	}
	if($_SERVER["REQUEST_METHOD"] == "POST")
	{
		

		$firstName = "";
		$lastName = "";
	    $email = "";
	    $password = "";
	    $phoneNumber = "";
	    $educatorRole = "1"; // 1- USER, 0 - ADMIN
	    $testStatus = "N";  // Initial Status of the registered user will be N
	    $testAttempts = 0; // Initial attempts will be set as 0
	    $activeStatus = "Y"; // When the user is registered his status will be active so set it as "Y"
	    $flag=true;

    	if(isset($_POST['firstName']) && isset($_POST['firstName'])!="")
		{
			$firstName = $_POST['firstName'];
		}
		else $flag=false;

		if(isset($_POST['lastName']) && isset($_POST['lastName'])!="")
		{
			$lastName = $_POST['lastName'];
		}
		else $flag=false;

		if(isset($_POST['email']) && isset($_POST['email'])!="")
		{
			$email = $_POST['email'];
		}
		else $flag=false;

		if(isset($_POST['password']) && isset($_POST['password'])!="")
		{
			$password = $_POST['password'];
		}
		else $flag=false;

	    if(isset($_POST['phone']) && isset($_POST['phone'])!="")
	    {
	      $phoneNumber = $_POST['phone'];
	    }
	    else $flag=false;

	    if($flag)
	    {
			$procedureParams = array(array(&$email));

			$sql = "EXEC usp_CheckUserAlreadyRegistered @email = ?";

			//echo "I am here 2\n";
			$stmt = sqlsrv_query($conn, $sql, $procedureParams);
			if($stmt)
			{
				//echo "I am here 3\n";
				$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_NUMERIC);

				///echo "... $row[0]\n";

				if($row[0] > 0)
				{
						$out = "<p class='red'>Email address already registered!</p>";
						sqlsrv_free_stmt($stmt);
						//return false;
						//header("location:login.php");
				}
				else
				{
					$passHash = md5($password);
					$procedureParams = array(array(&$firstName), array(&$lastName), array(&$email),
			                              array(&$passHash), array(&$phoneNumber), array(&$educatorRole),
			                              array(&$testStatus), array(&$testAttempts), array(&$activeStatus));

					$sql = "EXEC usp_InsertUserDetails @firstName = ?, @lastName = ?, @emailId = ?,
			                                      @strPassword = ?, @strPhoneNumber = ?,@educatorRole = ?,
			                                      @testStatus = ?, @testAttempts = ?, @activeStatus = ?";

				    // prepares and executes SQL query
				    $stmt = sqlsrv_query($conn, $sql, $procedureParams);
					if($stmt)
					{
				        sqlsrv_free_stmt( $stmt);
				        sqlsrv_close($conn);
				        $_SESSION['userId'] = "";
						$_SESSION['user'] = $firstname;
						$_SESSION['email'] = $email;
						$_SESSION['admin'] = 0;
				        header("location: training.php");
					}
					else
					{
					    die(print_r(sqlsrv_errors(), true));
				        sqlsrv_free_stmt( $stmt);
				        sqlsrv_close($conn);
					}
				}
			}
			else
			{
				//echo "I am here 4\n";
				die( print_r( sqlsrv_errors(), true));
				sqlsrv_free_stmt($stmt);
				sqlsrv_close($conn);
				return false;
			}
		}
	}

?>

<HTML XMLns="http://www.w3.org/1999/xHTML">
<head>
    <title>Educator Login for Swinburne's Clinical Educator Module</title>
		<link href="css/screen.css" rel="stylesheet" />
		<link href="css/register.css" rel="stylesheet" />
		<link href="css/cmxform.css" rel="stylesheet" />
		<script src="jquery-ui-1.12.1.custom/external/jquery/jquery.js"></script>
		<script src="jquery-ui-1.12.1.custom/external/jquery/jquery.validate.js"></script>
		<script>
				$().ready(function () {
						// validate the comment form when it is submitted
						$("#commentForm").validate();

						// validate signup form on keyup and submit
						$("#signupForm").validate({
								rules: {
										firstname: "required",
										lastname: "required",

										password: {
												required: true,
												minlength: 8
										},

										email: {
												required: true,
												email: true
										},
										number:{
												required:true,
												minlength:7,
												maxlength:10
										},
										agree: "required"
								},
								messages: {
										firstname: "Please enter your First Name",
										lastname: "Please enter your Last Name",

										password: {
												required: "Please provide a Password",
												minlength: "Your password must be at least 8 characters long"
										},

										number:{
												required: "Please provide a valid mobile number",
												minlength: "Your mobile number must have 7 digits",
												maxlength: "Your mobile number must have 10 digits",
										},
										email: "Please enter a valid E-mail address",

								}
						});


				});
		</script>
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

    <form method="post" id="signupForm">

        <div class="swinheader" >

                <div class="login-page">

                    <div class="form">
                        <form class="login-form">
                            <center class="loginheader">REGISTER</center>
                            <input type="text" id="firstname" name="firstName" placeholder="FIRST NAME" required />
                            <input type="text" id="lastname" name="lastName" placeholder="LAST NAME" required/>
                            <input type="text" id="email" name="email" placeholder="EMAIL ID" required/>
                            <input type="password" id="password" name="password" placeholder="PASSWORD" required/>
                            <input type="text" id="number"name="phone" placeholder="PHONE NUMBER" required/>
                            <button name="Login">Submit</button>
                        </form>
                        <div><?php echo $out;?></div>
                    </div>
                    
            </div>
            </div>
    </form>

</body>
</HTML>
