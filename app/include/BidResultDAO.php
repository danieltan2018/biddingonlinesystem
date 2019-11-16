<?php

class BidResultDAO
{
    public function countSuccess($cid, $sid)
    {
        $sql = 'select COUNT(userid) as count from bid_result where cid=:cid and sid=:sid and state="Success"';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':cid', $cid, PDO::PARAM_STR);
        $stmt->bindParam(':sid', $sid, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['count'];
    }

    public function lowestSuccess($cid, $sid)
    {
        $sql = 'select MIN(amount) as lowest from bid_result where cid=:cid and sid=:sid and state="Success"';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':cid', $cid, PDO::PARAM_STR);
        $stmt->bindParam(':sid', $sid, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['lowest'];
    }

    public function retrieve($userid)
    {
        $sql = 'select round, cid, sid, ranking, userid, amount, state from bid_result where userid=:userid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new BidResult($row['round'], $row['cid'], $row['sid'], $row['ranking'], $row['userid'], $row['amount'], $row['state']);
        }
        return $result;
    }

    public function retrieveSuccess($userid)
    {
        $sql = 'select round, cid, sid, ranking, userid, amount, state from bid_result where userid=:userid and state="Success"';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new BidResult($row['round'], $row['cid'], $row['sid'], $row['ranking'], $row['userid'], $row['amount'], $row['state']);
        }
        return $result;
    }

    public function retrieveEnrollment($userid, $cid)
    {
        $sql = 'select round, cid, sid, ranking, userid, amount, state from bid_result WHERE userid=:userid and cid=:cid and state="Success"';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->bindParam(':cid', $cid, PDO::PARAM_STR);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return new BidResult($row['round'], $row['cid'], $row['sid'], $row['ranking'], $row['userid'], $row['amount'], $row['state']);
        }
    }

    public function retrieveAll($round)
    {
        $sql = 'select * from bid_result WHERE round=:round ORDER BY cid, sid, amount DESC, userid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':round', $round, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = [
                "userid" => $row['userid'],
                "amount" => $row['amount'],
                "course" => $row['cid'],
                "section" => $row['sid']
            ];
        }
        return $result;
    }

    public function dumpBid($cid, $sid, $round)
    {
        $sql = 'select * from bid_result WHERE cid=:cid and sid=:sid and round=:round ORDER BY amount DESC, userid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':cid', $cid, PDO::PARAM_STR);
        $stmt->bindParam(':sid', $sid, PDO::PARAM_STR);
        $stmt->bindParam(':round', $round, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        $count = 1;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($row['state'] == 'Success') {
                $state = 'in';
            } else {
                $state = 'out';
            }
            $result[] = [
                "row" => $count,
                "userid" => $row['userid'],
                "amount" => $row['amount'],
                "result" => $state
            ];
            $count++;
        }
        return $result;
    }

    public function bidStatus($cid, $sid)
    {
        $sql = 'select * from bid_result WHERE cid=:cid and sid=:sid ORDER BY amount DESC, userid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':cid', $cid, PDO::PARAM_STR);
        $stmt->bindParam(':sid', $sid, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($row['state'] == 'Success') {
                $status = 'success';
            } else {
                $status = 'fail';
            }
            $result[] = [
                "userid" => $row['userid'],
                "amount" => $row['amount'],
                "balance" => null, // to keep output order
                "status" => $status
            ];
        }
        return $result;
    }

    public function retrieveSectionStudent()
    {
        $sql = 'select * from bid_result WHERE state="Success" ORDER BY cid, userid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = [
                "userid" => $row['userid'],
                "course" => $row['cid'],
                "section" => $row['sid'],
                "amount" => $row['amount']
            ];
        }
        return $result;
    }

    public function dumpSection($cid, $sid)
    {
        $sql = 'select * from bid_result WHERE state="Success" and cid=:cid and sid=:sid ORDER BY userid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':cid', $cid, PDO::PARAM_STR);
        $stmt->bindParam(':sid', $sid, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = [
                "userid" => $row['userid'],
                "amount" => $row['amount']
            ];
        }
        return $result;
    }

    public function add($bid_result)
    {
        $sql = "INSERT IGNORE INTO bid_result (round, cid, sid, ranking, userid, amount, state) VALUES (:round, :cid, :sid, :ranking, :userid, :amount, :state)";

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':round', $bid_result->round, PDO::PARAM_STR);
        $stmt->bindParam(':cid', $bid_result->cid, PDO::PARAM_STR);
        $stmt->bindParam(':sid', $bid_result->sid, PDO::PARAM_STR);
        $stmt->bindParam(':ranking', $bid_result->ranking, PDO::PARAM_STR);
        $stmt->bindParam(':userid', $bid_result->userid, PDO::PARAM_STR);
        $stmt->bindParam(':amount', $bid_result->amount, PDO::PARAM_STR);
        $stmt->bindParam(':state', $bid_result->state, PDO::PARAM_STR);

        $isAddOK = False;
        if ($stmt->execute()) {
            $isAddOK = True;
        }

        return $isAddOK;
    }

    public function remove($userid, $cid)
    {
        $sql = 'DELETE from bid_result WHERE userid=:userid and cid=:cid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->bindParam(':cid', $cid, PDO::PARAM_STR);

        $isRemoveOK = False;
        if ($stmt->execute()) {
            $isRemoveOK = True;
        }

        return $isRemoveOK;
    }

    public function removeAll()
    {
        $sql = 'TRUNCATE TABLE bid_result';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $stmt->execute();
        $count = $stmt->rowCount();
    }
}
