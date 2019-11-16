<?php
require_once 'include/common.php';
$error = '';
if (isset($_POST['username']) && isset($_POST['password'])) {
	$username = $_POST['username'];
	$password = $_POST['password'];
	if ($username == 'admin' && $password == 'admin@G8T8') {
		$_SESSION['user'] = 'admin';
		header("Location: index.php");
	}
	$StudentDAO = new StudentDAO();
	$student = $StudentDAO->retrieve($username);
	if ($student != null && $student->authenticate($password)) {
		$_SESSION['user'] = $username;
		header("Location: index.php");
	} else {
		$error = 'Invalid username or password';
	}
}
?>

<html>

<head>
	<link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
	<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" media="all" />
	<link href="//fonts.googleapis.com/css?family=Hind:300,400,500,600,700" rel="stylesheet">
</head>

<body>
	<div class="w3layouts-main">
		<div class="bg-layer"><br>
			<h1>Merlion University</h1>
			<h3>Bidding Online System</h3><br>
			<div class="header-main">
				<div class="main-icon">
					<img src="images/lion.png" style="width:250px; height:250px;">
				</div>
				<br>
				
				<div class="header-left-bottom">
					<form action="login.php" method="post">
						<div class="icon1">
							<span class="fa fa-user"></span>
							<input type="text" placeholder="Username" name="username" id="username" required="" />
						</div>
						<div class="icon1">
							<span class="fa fa-lock"></span>
							<input type="password" placeholder="Password" name="password" id="password" required="" />
						</div>
						<div class="bottom">
							<button class="btn">Log In</button>
						</div>
					</form>
				</div>
				<div class="error" align="center">
					<h5><?= $error ?></h5>
				</div>
			</div>
		</div>
	</div>
</body>

</html>