<?php
require_once 'include/admin_protect.php';
require_once 'include/round-process.php';

if (isset($_POST['start'])) {
	startRound();
}

if (isset($_POST['stop'])) {
	stopRound();
}

if (isset($_POST['reset'])) {
	$ConfigDAO = new ConfigDAO();
	$ConfigDAO->resetRound();
}

$BidResultDAO = new BidResultDAO();
$ConfigDAO = new ConfigDAO();
$round = $ConfigDAO->getRound();
$rounded = round($round / 2, 0, PHP_ROUND_HALF_UP);
?>

<html>

<head>
	<title>Admin Controls</title>
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/font-awesome.min.css" rel="stylesheet">
	<link href="css/styles3.css" rel="stylesheet">
</head>

<body>
	<nav class="navbar navbar-custom navbar-fixed-top">
		<div class="container-fluid">
			<div class="navbar-header">
				<div class="site-title">
					<img src="images/lion.png" style="width:50px;height:50px;">
					<h4>BIOS</h4>
				</div>
			</div>
		</div>
	</nav>
	<div id="sidebar-collapse" class="col-sm-2 sidebar">
		<div class="profile-sidebar">
			<div class="profile-userpic">
				<img src="images/face1.jpg" class="img-responsive">
			</div>
			<div class="profile-usertitle">
				<div class="profile-usertitle-name">ADMIN</div>
				<div class="profile-usertitle-status">Online</div>
			</div>
			<div class="clear"></div>
		</div>
		<ul class="nav menu">
			<li class="active"><a href="admin.php"><em class="fa fa-dashboard">&nbsp;</em> Admin Controls</a></li>
			<li><a href="logout.php"><em class="fa fa-power-off">&nbsp;</em> Logout</a></li>
		</ul>
	</div>


	<div class="col-sm-10 col-sm-offset-2 main">
		<div class="row">
			<ol class="breadcrumb">
				<li><a href="#">
						<em class="fa fa-dashboard"></em>
					</a></li>
				<li class="active">Admin Controls</li>
			</ol>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-body">
						<?php
						if (isEmpty($round)) {
							if (!isset($_POST['bootstrap'])) {
								echo "<h3>Upload bootstrap file to start Round 1</h3>";
								echo "<form id='bootstrap-form' action='admin.php' method='POST' enctype='multipart/form-data'>";
								echo "<input id='bootstrap-file' type='file' name='bootstrap-file'><br><br>";
								echo "<input type='submit' name='bootstrap' value='BOOTSTRAP'>";
								echo "</form>";
							} else {
								echo "<h3>Bootstrap Results:</h3>";
								require_once 'bootstrap-display.php';
								$ConfigDAO = new ConfigDAO();
								$round = $ConfigDAO->getRound();
								if ($round == 1) {
									echo "<h3>Round 1 started.</h3>";
									echo "<form action='admin.php' method='POST'><input type='submit' name='stop' value='STOP ROUND'></form>";
								}
							}
						} elseif ($round >= 4) {
							echo "<h3>Round 2 successfully cleared and ended.</h3><br>";
							echo "<h3>Click here to reset system.</h3>";
							echo "<form action='admin.php' method='POST'><input type='submit' name='reset' value='RESET'></form>";
						} elseif ($round % 2 != 0) {
							echo "<h3>Round $rounded started.</h3>";
							echo "<form action='admin.php' method='POST'><input type='submit' name='stop' value='STOP ROUND'></form>";
						} else {
							echo "<h3>Round $rounded successfully cleared and stopped.</h3>";
							echo "<form action='admin.php' method='POST'><input type='submit' name='start' value='START NEXT ROUND'></form>";
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>

</html>