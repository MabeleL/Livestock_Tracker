<?php session_start(); ?>
<?php include('config.php'); ?>

<!DOCTYPE html>
<html>
<head>
<title>Malisho Login</title>
<!-- For-Mobile-Apps -->
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="User Icon Login Form Widget Responsive, Login Form Web Template, Flat Pricing Tables, Flat Drop-Downs, Sign-Up Web Templates, Flat Web Templates, Login Sign-up Responsive Web Template, Smartphone Compatible Web Template, Free Web Designs for Nokia, Samsung, LG, Sony Ericsson, Motorola Web Design" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- //For-Mobile-Apps -->
<!-- Style --> <link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
</head>
<body>
<div class="container">
<h1>Login to Malisho</h1>
     <div class="contact-form">
	 <div class="profile-pic">
	 <img src="images/cow_2.png" alt="User Icon"/>
	 </div>
   <form action="#" method="post">
	 <div class="signin">
	      <input type="text" class="user"  name="user" required="required" value="Username" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'Username';}" />
		   <input type="password" class="pass"  name="pass" required="required" value="Password" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'Password';}" />
		 <!-- <p><a href="#">Forgot Password?</a></p> --->
	 </div>
	      <input type="submit"  name="login"  value="Login" />
	 </div>
     </form>

   <?php
 	if (isset($_POST['login']))
 		{
 			$username = mysqli_real_escape_string($conn, $_POST['user']);
 			$password = mysqli_real_escape_string($conn, $_POST['pass']);

 			$query 		= mysqli_query($conn, "SELECT * FROM users WHERE  password='$password' and username='$username'");
 			$row		= mysqli_fetch_array($query);
 			$num_row 	= mysqli_num_rows($query);

 			if ($num_row > 0)
 				{
 					$_SESSION['id']=$row['id'];
 					header('location:home.php');

 				}
 			else
 				{
 					echo 'Invalid Username and Password Combination';
 				}
 		}
   ?>
</div>
<div class="footer">
     <p>Copyright &copy; 2021. | Designed by Tungana</a></p>
</div>


</body>
</html>
