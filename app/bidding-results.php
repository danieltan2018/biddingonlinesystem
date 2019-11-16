<?php
function BidResultsTable()
{
    require_once 'include/round-process.php';
    $BidDAO = new BidDAO();
    $BidResultDAO = new BidResultDAO();
    $ConfigDAO = new ConfigDAO();

    $userid = $_SESSION['user'];
    $bid_results = $BidResultDAO->retrieve($userid);
    $bids = $BidDAO->retrieve($userid);
    $round = $ConfigDAO->getRound();

    if (!isEmpty($bids)) {
        echo "<h2>Current Bids</h2>";
        echo "<table border='1'>";
        echo "
            <th>Course</th>
            <th>Section</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Drop Bid</th>";
        if ($round == 3) {
            echo "
                <th>Min Bid</th>
                <th>Vacancies</th>";
        }

        foreach ($bids as $bid) {
            $course = $bid->cid;
            $section = $bid->sid;
            $amount = $bid->amount;
            if ($round == 1) {
                $status = "Pending";
            } else {
                $clearingPrice = getClearingPrice($course, $section);
                if ($amount >= $clearingPrice) {
                    $status = "Success";
                } else {
                    $status = "Fail";
                }
            }
            echo "
                <tr>
                    <td>$course</td>
                    <td>$section</td>
                    <td>$amount</td>
                    <td>$status</td>
                    <td>
                    <form action='' method='POST'>
                    <input type='submit' name='drop' value='Drop Bid'>
                    <input type='hidden' name='drop_cid' value='$course'>
                    <input type='hidden' name='drop_amount' value='$amount'>
                    </form>
                    </td>";
            if ($round == 3) {
                // Find minimum bid
                $MinBidDAO = new MinBidDAO;
                $minbid = $MinBidDAO->getMinBid($course, $section);
                // Find number of vacancies
                $SectionDAO = new SectionDAO;
                $s = $SectionDAO->retrieve($course, $section);
                $numVacancies = $s->size;
                $BidResultDAO = new BidResultDAO;
                $numSuccess = $BidResultDAO->countSuccess($course, $section);
                if ($numSuccess > 0) {
                    $numVacancies -= $numSuccess;
                }
                echo "
                <td>$minbid</td>
                <td>$numVacancies</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }


    if (!isEmpty($bid_results)) {
        echo "<h2>Bidding Results</h2>";
        echo "<table border='1'>";
        echo "
            <th>Round</th>
            <th>Course</th>
            <th>Section</th>
            <th>Amount</th>
            <th>Status</th>";
        if ($round == 3) {
            echo "<th>Drop Section</th>";
        }

        foreach ($bid_results as $bid_result) {
            $bidround = $bid_result->round;
            $course = $bid_result->cid;
            $section = $bid_result->sid;
            $amount = $bid_result->amount;
            $state = $bid_result->state;

            echo "
                <tr>
                    <td>$bidround</td>
                    <td>$course</td>
                    <td>$section</td>
                    <td>$amount</td>
                    <td>$state</td>
                    ";
            if ($state == "Success" && $round == 3) {
                echo "
                    <td>
                    <form action='' method='POST'>
                    <input type='submit' name='drop_s' value='Drop Section'>
                    <input type='hidden' name='drop_s_cid' value='$course'>
                    <input type='hidden' name='drop_s_amount' value='$amount'>
                    </form>
                    </td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
}
