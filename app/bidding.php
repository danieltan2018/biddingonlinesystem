<?php
require_once 'include/protect.php';
require_once 'include/bid-process.php';
require_once 'bidding-results.php';

// Get user information
$userid = $_SESSION['user'];
// Prevent admin from causing errors
if ($userid == 'admin') {
    echo "Not Allowed";
    exit;
} else {
    $StudentDAO = new StudentDAO();
    $stu = $StudentDAO->retrieve($userid);
    $balance = $stu->edollar;
}

// Get round information
$ConfigDAO = new ConfigDAO();
$round = $ConfigDAO->getRound();
$rounded = round($round / 2, 0, PHP_ROUND_HALF_UP);
if ($round % 2 == 0) {
    $rounded = $rounded . " ended";
} else {
    $rounded = $rounded . " in progress";
}

// Populate list of classes student can bid for
$CourseDAO = new CourseDAO;
$SectionDAO = new SectionDAO;
$MinBidDAO = new MinBidDAO;
$class_list = $SectionDAO->retrieveAll();
$valid_courses = [];
$valid_sections = [];
foreach ($class_list as $class) {
    $thisminbid = $MinBidDAO->getMinBid($class['course'], $class['section']);
    if (isEmpty(checkbid($userid, $thisminbid, $class['course'], $class['section']))) {
        $course = $CourseDAO->retrieve($class['course']);
        $valid_courses[$class['course']] = $course->title;
        $valid_sections[$class['course']][] = $class['section'];
    }
}

// Get form fields
$cid = '';
$sid = '';
if (isset($_POST['cid'])) {
    $cid = $_POST['cid'];
}
if (isset($_POST['sid'])) {
    $sid = $_POST['sid'];
}
if (isset($_POST['amount'])) {
    $amount = $_POST['amount'];
}
$minbid = $MinBidDAO->getMinBid($cid, $sid);
?>

<html>

<head>
    <title>Bidding</title>
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
            <li><a href="index.php"><em class="fa fa-user-circle">&nbsp;</em> Dashboard</a></li>
            <li class="active"><a href="bidding.php"><em class="fa fa-pencil">&nbsp;</em> Bidding</a></li>
            <li><a href="logout.php"><em class="fa fa-power-off">&nbsp;</em> Logout</a></li>
        </ul>
    </div>

    <div class="col-sm-10 col-sm-offset-2 main">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="#">
                        <em class="fa fa-pencil"></em>
                    </a></li>
                <li class="active">Bidding</li>
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
                    <h2>Place Bid</h2>
                    <form action='bidding.php' method='POST'>
                        <h6>Course Code: <select name='cid' onchange='this.form.submit()' required></h6>
                        <option value=''></option>
                        <?php
                        foreach ($valid_courses as $course => $coursename) {
                            if ($course == $cid) {
                                echo "<option value='$course' selected>$course - $coursename</option>";
                            } else {
                                echo "<option value='$course'>$course - $coursename</option>";
                            }
                        }
                        ?>
                        </select>
                        <br><br>
                        <h6> Section Number: <select name='sid' onchange='this.form.submit()' required></h6>
                        <option value=''></option>
                        <?php
                        if (!isEmpty($cid)) {
                            $sections_for_course = $valid_sections[$cid];
                            foreach ($sections_for_course as $section) {
                                if ($section == $sid) {
                                    echo "<option value='$section' selected>$section</option>";
                                } else {
                                    echo "<option value='$section'>$section</option>";
                                }
                            }
                        }
                        ?>
                        </select>
                        <br><br>
                        <h6> e$ Amount: <input type='number' name='amount' step='0.01' min=<?= $minbid ?> max=<?= $balance ?> required></h6>
                        <br>
                        <?php
                        echo "The minimum bid is <b>e$$minbid</b>";
                        if ($rounded == 2 && !isEmpty($cid) && !isEmpty($sid)) {
                            // Find number of vacancies
                            $section = $SectionDAO->retrieve($cid, $sid);
                            $numVacancies = $section->size;
                            $BidResultDAO = new BidResultDAO;
                            $numSuccess = $BidResultDAO->countSuccess($cid, $sid);
                            if ($numSuccess > 0) {
                                $numVacancies -= $numSuccess;
                            }
                            echo " and there are <b>$numVacancies vacancies</b> left.";
                        }
                        ?>
                        <br><br>
                        <?php
                        if ($round % 2 != 0) {
                            echo "<input type='submit' name='placebid' value='Place Bid'>";
                        } else {
                            echo "<h3 style='color:red;'>Bidding is closed.</h3>";
                        }
                        ?>
                    </form>

                    <?php
                    // Place bid function
                    if (isset($_POST['placebid']) && isset($cid) && isset($sid) && isset($amount)) {
                        $messages = checkbid($userid, $amount, $cid, $sid);
                        // If errors present, show user error
                        if (!isEmpty($messages)) {
                            echo "<h3 style='color:red;'>The following errors have occurred:</h3>";
                            echo "<ol>";
                            foreach ($messages as $message) {
                                echo "<li>$message</li>";
                            }
                            echo "</ol>";
                        } else {
                            echo "<h1 style='color:green;'>Placing bid...</h1>";
                            // Add bid & deduct e-dollar
                            $bid = new Bid($userid, $amount, $cid, $sid);
                            $BidDAO = new BidDAO();
                            $BidDAO->add($bid);
                            $stu->edollar -= $amount;
                            $StudentDAO->update($stu);
                            // Change MinBid if round 2
                            if ($rounded == 2) {
                                // Get sorted array of bid amounts
                                $bids = $BidDAO->retrieveClassBids($cid, $sid);
                                $AmountArr = array_column($bids, 'amount');
                                // Update MinBid if more bids than vacancies
                                if ($numVacancies <= count($AmountArr)) {
                                    $newMinBid = $AmountArr[$numVacancies - 1] + 1;
                                    if ($newMinBid > $minbid) { // MinBid cannot go down
                                        $MinBidDAO->updateMinBid($cid, $sid, $newMinBid);
                                    }
                                }
                            }
                            echo "<script>window.location.replace('index.php')</script>";
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>