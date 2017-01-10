<?php
include ("db_config.php");
define('AUS_TIMEZONE', 'Australia/Melbourne');

session_start();

if(isset($_SESSION['admin']) && $_SESSION['admin']==1)
{
  if($_SERVER["REQUEST_METHOD"] == "POST")
  {
    if(isset($_POST['fromdatepicker']) && isset($_POST['todatepicker']) && $_POST['fromdatepicker']!="" & $_POST['todatepicker']!="")
    {
      $fromDate = $_POST['fromdatepicker'];
      $toDate = $_POST['todatepicker'];
      
      $procedureParams = array(array(&$fromDate), array(&$toDate));

      $sql = "EXEC usp_GetEducatorAndTestDetailsForReport @fromDate = ?, @toDate = ?";

      $stmt = sqlsrv_query($conn, $sql, $procedureParams);

      if($stmt === false)
      {
          sqlsrv_free_stmt( $stmt);
          sqlsrv_close($conn);
          die( print_r( sqlsrv_errors(), true));
      }

      $output = '
                  <table class="table2excel csstable" border="1">
                  <thead class="cssthead">
                    <tr>
                      <td><b>First Name</b></td>
                      <td><b>Last Name</b></td>
                      <td><b>Phone Number</b></td>
                      <td><b>E-mail Address</b></td>
                      <td><b>Active Status</b></td>
                      <td><b>Test Result</b></td>
                      <td><b>No of Test Attempts</b></td>
                      <td><b>Test Date</b></td>
                      </tr>
                  </thead> <tbody>';

        while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))
        {
          $firstName = $row["FIRST_NAME"];
          $lastName = $row["LAST_NAME"];
          $emailAddress = $row["EMAIL"];
          $phoneNo = $row["PHONE_NUMBER"];
          $activeInd = $row["ACTIVE_YN"];
          $testResult = $row["TEST_STATUS"];
          $testAttempts = $row["TEST_ATTEMPTS"];
          date_default_timezone_set(AUS_TIMEZONE);
          $testDateFromDB = $row["INSERT_DATETIME"]->format('Y-m-d H:i:s');

          $output .= "<tr>
                      <td>$firstName</td>
                      <td>$lastName</td>
                      <td>$phoneNo</td>
                      <td>$emailAddress</td>
                      <td>$activeInd</td>
                      <td>$testResult</td>
                      <td>$testAttempts</td>
                      <td>$testDateFromDB</td>
                      </tr>";
        }

        $output .= "</tbody> </table>";

        $output .= '
                    <form method="post">
                    <p>
                      <button class="btnExportToExcel" id="btnExport">Export to Excel</button>
                    </p>
                    </form>
                    ';
        sqlsrv_free_stmt( $stmt);
        sqlsrv_close($conn);
    }
    else
    {
      $output = "Please select the dates for report generation.";
    }
  }
}
else
{
  header("Location: admin.php");
}
?>

<HTML XMLns="http://www.w3.org/1999/xHTML">
<head>
    <title>Test Report Generator</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <script src="jquery-ui-1.12.1.custom/external/jquery/jquery.js"></script>
    <script src="jquery-ui-1.12.1.custom/jquery-ui.js"></script>
    <script src="jquery-ui-1.12.1.custom/external/jquery/jquery.table2excel.js"></script>
    <link href="jquery-ui-1.12.1.custom/jquery-ui.min.css" rel="stylesheet" />
    <link href="jquery-ui-1.12.1.custom/jquery-ui.structure.css" rel="stylesheet" />
    <link href="jquery-ui-1.12.1.custom/jquery-ui.theme.css" rel="stylesheet" />
    <link type="text/css" href="css/exportexcel.css" rel="stylesheet" />



    <script type="text/javascript">
        $(function ()
        {
            $("#fromdatepicker").datepicker();
            $("#todatepicker").datepicker();
        });

        $(function () {
            $("#btnExport").click(function () {
                $(".table2excel").table2excel({
                    exclude: ".noExl",
                    name: "Excel Document Name",
                    filename: "download",
                    fileext: ".xls",
                    exclude_img: true,
                    exclude_links: true,
                    exclude_inputs: true
                });
            });
        });
    </script>
    <style type="text/css">

    .display-topright {
    position: absolute;
    right: 0;
    top: 0;
    margin: 16px 58px;
}
.btntraining {
    text-transform: uppercase;
    border-radius: 5px;
    text-align: center;
    outline: 0;
    height: 30px;
    border: 0;
    color: #FFFFFF;
    font-size: 14px;
    -webkit-transition: all 0.3 ease;
    transition: all 0.3 ease;
    cursor: pointer;
}
.wide{
  width: 200px;
  text-align: right;
}
    </style>

    <link href="css/w3.css" rel="stylesheet" />
</head>
<body>
  <div class="cssdivheader">
  </div>
  <div class="cssdivMainContent">

      <div class="cssdivContent">
        <div>
          <h2>Swinburne Clinical Educator Report Generator</h2>
        </div>


          <form method="POST">
            <p>Select the From Date and To Date to display the Educator test details</p>
                <table>
                    <tr>
                        <td>  FROM DATE:  </td>
                        <td><input type="text" id="fromdatepicker" name="fromdatepicker"></td>
                        <td>  TO DATE:  </td>
                        <td><input type="text" id="todatepicker" name="todatepicker"></td>
                        <td><button id="btnListUserDetails" name='listEducators' value='Show Educator Details'class="btnExportToExcel">Submit</button></td>
                        <td class='wide'><a href="<?php echo $invite; ?>"> Send Invite </a></td>
                    </tr>
                </table>
          </form>
          <div class="display-topright">
              <a href="logout.php" class="w3-btn btntraining">Logout</a>
          </div>
          <?php echo $output; ?>
 
    </div>
  </body>
</HTML>
