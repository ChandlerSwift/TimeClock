<?php
	include_once('../config.php');
	$conn = mysqli_connect($servername, $username, $password, $database);
		session_start();
	
	if($_SESSION['ADMIN'] !== 1){
	  header('location: login.php');
	}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php echo $title; ?></title>
    
  	<!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">-->
  	<link rel="stylesheet" href="../css/bootstrap.css" type="text/css" />

    <link href="../css/dashboard.css" rel="stylesheet">
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function(){
        $("#pending").click(function(){
            $("#pending").hide(200, function(){
              $("#approved").show(200);
              $("#denied").show(200);
            });
        });
        
        $("#generate").click(function(){
            $("#generate").addClass("active");
            $("#gen_div").show(200);
        });
    });
    </script>
    <style>
      #add_user{
        display: none;
      }
      #gen_div{
        display:none;
      }
    </style>
  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#"><?php echo $title; ?></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li id="generate"><a>Generate Report</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="../">Home</a></li>
            <li class="active"><a href="index.php">Dashboard</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="login.php?logout=1">Log Out</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
            <li><a href="index.php">Overview <span class="sr-only">(current)</span></a></li>
            <li><a href="users.php">Users</a></li>
            <li class="active"><a href="logs.php">Reports</a></li>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <div id="gen_div">
            <h2 class="sub-header">Generate Report</h2>
            <h3>Name</h3>
            <form class="form-inline" action="logs.php" method="get"><input type="hidden" name="n" value="name"><input class="form-control" type="text" name="v" placeholder="Name"></input> <input type="submit" class="btn btn-default"></form>
            <h3>Date</h3>
            <form class="form-inline" action="logs.php" method="get"><input type="hidden" name="n" value="date"><input class="form-control" type="text" name="v" placeholder="Date"></input> <input type="submit" class="btn btn-default"> <a href="logs.php?n=date&v=<?php echo date('Y-m-d'); ?>" class="btn btn-warning">Veiw Today's Report</a> </form>
            <h3>Status</h3>
            <a class="btn btn-warning" href="logs.php?n=status&v=0">Pending</a> <a class="btn btn-danger" href="logs.php?n=status&v=2">Denied</a> <a class="btn btn-success" href="logs.php?n=status&v=1">Approved</a>
          </div>
          <h2 class="sub-header">Logs</h2>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Date</th>
                  <th>Time In</th>
                  <th>Time Out</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                    switch ($_GET['n']) {
                        case 'date':
                            $value = $_GET['v'];
                            $result = mysqli_query($conn,"SELECT * FROM `$data_table` WHERE DATE(Time_In) = '$value' AND Time_Out IS NOT NULL ORDER BY ID DESC");
                            break;
                        case 'name':
                            $value = $_GET['v'];
                            $generate = mysqli_query($conn,"SELECT * FROM `$user_table` WHERE Name='$value'");
                            while($row2 = mysqli_fetch_array($generate)) {
                              $ID = $row2['ID'];
                            }
                          
                            
                            $result = mysqli_query($conn,"SELECT * FROM `$data_table` WHERE User='$ID' AND Time_Out IS NOT NULL ORDER BY ID DESC");
                            break;
                        case 'status':
                            $value = $_GET['v'];
                            $result = mysqli_query($conn,"SELECT * FROM `$data_table` WHERE Status='$value' AND Time_Out IS NOT NULL ORDER BY ID DESC");
                            break;
                        case 'id':
                            $value = $_GET['v'];
                            $result = mysqli_query($conn,"SELECT * FROM `$data_table` WHERE ID='$value' AND Time_Out IS NOT NULL ORDER BY ID DESC");
                            break;
                        default:
                            $result = mysqli_query($conn,"SELECT * FROM `$data_table` WHERE Time_Out IS NOT NULL ORDER BY ID DESC");
                            break;
                    }
                    while($row = mysqli_fetch_array($result)) {
                      $time_in = $row['Time_In'];
                      $time_out = $row['Time_Out'];
                      
                      $userID = $row['User'];
                      $name = mysqli_query($conn,"SELECT * FROM `$user_table` WHERE ID=$userID");
                      while($namesrow = mysqli_fetch_array($name)) {
                        $user = $namesrow['Name'];
                      }
                      
                      switch ($row['Status']) {
                          case 0:
                              $status = '<a id="pending" class="label label-warning">Pending</a><a style="display:none;" href="action.php?s=2&id=' . $row['ID'] . '&n=' . $_GET['n'] . '&v=' . $_GET['v'] . '" id="denied" class="label label-danger">Denied</a> <a style="display:none;" href="action.php?s=1&id=' . $row['ID'] . '&n=' . $_GET['n'] . '&v=' . $_GET['v'] . '" id="approved" class="label label-success">Approved</a>';
                              break;
                          case 1:
                              $status = '<span class="label label-success">Approved</span>';
                              break;
                          case 2:
                              $status = '<span class="label label-danger">Denied</span>';
                              break;
                      }
                      
                      echo "<tr>";
                      echo "<td>" . $row['ID'] . "</td>";
                      echo "<td>" . $user . "</td>";
                  		echo "<td>" . date('l, F j, Y', strtotime($time_in)) . "</td>";
                      echo "<td>" . date("g:i A", strtotime($time_in)) . "</td>";
                      echo "<td>" . date("g:i A", strtotime($time_out)) . "</td>";
                      echo "<td>" . $status . "</td>";
                      $delete = '<td><a href="action.php?dh=1&id=' . $row['ID'] . '&n=' . $_GET['n'] . '&v=' . $_GET['v'] . '" id="denied" class="label label-danger">Delete</a></td>';
                      echo $delete;
                      echo "</tr>";
                    }
                  
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
