<?php
include_once("Scripts/config.php");
include_once("Scripts/demo.php");
$allschools = "SELECT name FROM schools;";
$allschools = $database_connection->prepare($allschools, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$allschools->execute();

if(isset($_COOKIE['id']) && $_COOKIE['id'] != "")
{
	header("location: home");
}

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$user_query = "SELECT id FROM users WHERE email = :entered_name AND password = :entered_password";
	$user_query = $database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
	$user_query->execute(array(":entered_name" => $_POST['name'], ":entered_password" => $_POST['password']));
	$user = $user_query->fetch(PDO::FETCH_ASSOC);
	$count = $user_query->rowCount();

	if($count == 1)
	{
		setcookie("id", base64_encode($user['id']), time() + 3600000);
		setcookie("showchat", 1, time() + 3600000);
		header("location: home");
	}
	else 
	{
		echo '<p style="background-color:red;">Your Email or Password is invalid</p>';
	}
}
?>

<html>
<head>
	<script src="http://code.jquery.com/jquery-latest.min.js"></script>
	<link rel="stylesheet" type="text/css" href="CSS/login.css">
	<title>Login</title>
	
	<link rel="stylesheet" type="text/css" href="CSS/style.css">
</head>
<body class="login">
	<div class="login_container" style='background-image:url(Images/fresh_snow.png);background-repeat:repeat-x;'>
		<h1 class="loginheader">bla</h1>
		<form action="" method="post">	
			<div class="loginbox">	
				<table border="0">
					<tr>
						<td><input type="text" placeholder="Email" autocomplete="off" tabindex="1" name="name"/></td>
						<td><input type="password" tabindex="2" placeholder="Password" autocomplete="off" name="password"/></td>
						<td><input type="submit" value=" Login "></input></td>
					</tr>
				</table>
			</div>
		</form>
	</div>
	<div>
		<img style="height:50%;margin-top:19%;margin-left:20%;opacity:0.3;"src="Images/social_network.gif"></img>
		<span class="about">This site is in its development. You are welcome to sign-up, register schools and store your files here. However, I cannot guarantee the safety/availability of any of your data yet. Release date: 2014, March 8th. You can watch the site develop everyday :)</span>
	</div>
	<div class="signup">
		<?php
		if(!isset($_GET['action']))
		{
			echo '

			<h1 class="signupheader">Sign Up</h1>
			<form action="Scripts/verifysignup.php" method="POST">
			<div class="signupbox">
			<table border="0">
			<tr>
			<td><input type="text" placeholder="First Name"autocomplete="off" name="firstname"/></td><td><input type="text" placeholder="Last Name"autocomplete="off" name="lastname"/></td>
			</tr>
			<tr>
			<td colspan="2"><input type="text" style="width:100%;" placeholder="Password" autocomplete="off" name="newpassword"/></td>
			</tr>
			<tr>
			<td colspan="2"><input type="text" style="width:100%;" autocomplete="off" placeholder="Email" name="email"/></td>
			</tr>
			<tr>
			<td colspan="2"><div class="styled-select"><select id="schoolselect" style="width:100%;" name="school">'; while ($schools = $allschools->fetch(PDO::FETCH_ASSOC)) { echo "<option>".$schools."</option>";}
			echo '<option selected>Select your School</option><option>*Register a new School</option>
			<tr>
			<td><label>Select Year:</label></td><td><div class="styled-select"><select style="width:100%;" name="year"> <option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option><option>7</option><option>8</option><option>9</option><option>10</option><option>11</option><option>12</option><option>13</option><option>14</option></select></div></td>
			</tr>
			<tr>
			<td><div class="styled-select"><select style="width:100%;" name="gender"><option>Male</option><option>Female</option></select></div></td><td><center><a href="#" class"email">Why do I need to <br>provide my school?</a></center></td>
			</tr>
			<tr>
			<td colspan="2"><input type="submit" value=" Sign up! "></input></td>
			</tr>
			</table>
			</div>
			</form>

			';
		}
		else
		{
			echo '
			<h1 class="signupheader">Register a School</h1>
			<form id="school" action="Scripts/verifysignup.php" method="POST">
			<div class="signupbox">
			<table border="0">
			<tr>
			<td colspan="2"><input type="text" style="width:100%;" autocomplete="off" placeholder="School Name (e.g. Clifford School)" name="school"/></td>
			</tr>
			<tr>
			<td><input type="text" placeholder="First Name"autocomplete="off" name="firstname"/></td><td><input type="text" placeholder="Last Name"autocomplete="off" name="lastname"/></td>
			</tr>
			<tr>
			<td colspan="2"><input type="text" style="width:100%;" placeholder="Password" autocomplete="off" name="newpassword"/></td>
			</tr>
			<tr>
			<td colspan="2"><input type="text" style="width:100%;" autocomplete="off" placeholder="Email" name="email"/></td>
			</tr>
			<tr>
			<td><label>Select Year:</label></td><td><div class="styled-select"><select style="width:100%;" name="year"> <option>1</option><option>2</option><option>3</option>
			<option>4</option><option>5</option><option>6</option><option>7</option><option>10</option><option>8</option><option>9</option><option>10</option>
			<option>11</option><option>12</option><option>13</option><option>14</option></select></div></td>
			</tr>
			<tr>
			<td><div class="styled-select"><select style="width:100%;" name="gender"><option>Male</option><option>Female</option></select></div></td><td><center><a href="#" class"email">Why do I need to <br>provide my school?</a></center></td>
			</tr>
			<tr>
			<td colspan="2"><input type="submit" value="Register User and School"></input></td>
			</tr>
			<tr>
			<td colspan="2"><label>*When you register a school <br>you will automatically be<br> appointed admin.</label></td>
			</tr>
			</table>
			</div>
			</form>';
		}?>
	</div>
</div>
<div class="links">
	<a id="schoollink" href="login" style="text-decoration:none; font-size:0.8em;">Register a User</a>/
	<a id="schoollink" href="login?action=school" style="text-decoration:none; font-size:0.8em;">Register a School + User</a>/
	<a id="schoollink" href="about" style="text-decoration:none; font-size:0.8em;">About</a><br/>
	<span>Suggestions/bugs? Email me at patrick.geyer1@gmail.com</span>
	</div>
	<script>
	$('#schoolselect').change( function() {    
		if($(this).val() == "*Register a new School")
		{
			window.location.replace("login?action=school");
		}
	});
	</script>
</body>
</html>

