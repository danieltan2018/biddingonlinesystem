<?php
require_once 'include/protect.php';
$userid = $_SESSION['user'];
if ($_SESSION['user'] == 'admin') {
    require_once 'admin.php';
} else {
    require_once 'bidding-results.php';
    // Get edollar and round information
    $StudentDAO = new StudentDAO();
    $stu = $StudentDAO->retrieve($userid);
    $balance = $stu->edollar;
    $ConfigDAO = new ConfigDAO();
    $round = $ConfigDAO->getRound();
    $rounded = round($round / 2, 0, PHP_ROUND_HALF_UP);
    if ($round % 2 == 0) {
        $rounded = $rounded . " ended";
    } else {
        $rounded = $rounded . " in progress";
    }
    ?>

    <html>

    <head>
        <title>BIOS Homepage</title>
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
                    <img src="images/user.png" class="img-responsive">
                </div>
                <div class="profile-usertitle">
                    <div class="profile-usertitle-name"><?= $userid ?></div>
                    <div class="profile-usertitle-status">Online</div>
                </div>
                <div class="clear"></div>
            </div>
            <ul class="nav menu">
                <li class="active"><a href="admin.php"><em class="fa fa-user-circle">&nbsp;</em> Dashboard</a></li>
                <li><a href="bidding.php"><em class="fa fa-pencil">&nbsp;</em> Bidding</a></li>
                <li><a href="logout.php"><em class="fa fa-power-off">&nbsp;</em> Logout</a></li>
            </ul>
        </div>

        <div class="col-sm-10 col-sm-offset-2 main">
            <div class="row">
                <ol class="breadcrumb">
                    <li><a href="#">
                            <em class="fa fa-user-circle"></em>
                        </a></li>
                    <li class="active">Dashboard</li>
                </ol>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h2><u>Student Bidding (Round <?= $rounded ?>)</h2></u>
                            <h3>Welcome <b><?= $userid ?></b>, your e$ balance is: <b>e$<?= $balance ?></b></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                    <?php
                        // Drop bid function
                        if (isset($_POST['drop']) && ($round == 1 || $round == 3)) {
                            echo "<h1 style='color:green;'>Dropping bid...</h1>";
                            $BidDAO = new BidDAO();
                            $BidDAO->remove($userid, $_POST['drop_cid']);
                            // Refund e-dollars
                            $student = $StudentDAO->retrieve($userid);
                            $student->edollar += $_POST['drop_amount'];
                            $StudentDAO->update($student);
                            echo "<script>window.location.replace('bidding.php')</script>";
                        }

                        // Drop section function
                        if (isset($_POST['drop_s']) && $round == 3) {
                            echo "<h1 style='color:green;'>Dropping section...</h1>";
                            $BidResultDAO = new BidResultDAO();
                            $BidResultDAO->remove($userid, $_POST['drop_s_cid']);
                            // Refund e-dollars
                            $student = $StudentDAO->retrieve($userid);
                            $student->edollar += $_POST['drop_s_amount'];
                            $StudentDAO->update($student);
                            echo "<script>window.location.replace('bidding.php')</script>";
                        }

                        BidResultsTable();
                    }
                    ?>
                    </div>
                </div>
            </div>
        </div>
    </body>

    </html>
