<?php
include('config.php');
include('session.php');

$result=mysqli_query($conn, "select * from users where id='$session_id'")or die('Error In Session');
$row=mysqli_fetch_array($result);
 ?>
<html>
<head>
<link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
</head>
<body>
<div class="form-wrapper">
    <p><center><a href="dashboard/index.html">Launch Dashboard</a></center></p>
<!--	 <div class="reminder">
    <p><a href="logout.php">Log out</a></p>
  </div>-->
</div>

</body>
</html>
