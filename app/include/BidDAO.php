<?php

class BidDAO
{
    public function retrieve($userid)
    {
        $sql = 'select userid, amount, cid, sid from bid where userid=:userid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();


        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Bid($row['userid'],  $row['amount'], $row['cid'], $row['sid']);
        }
        return $result;
    }

    public function retrieveBid($userid, $cid)
    {
        $sql = 'select userid, amount, cid, sid from bid where userid=:userid and cid=:cid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();


        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->bindParam(':cid', $cid, PDO::PARAM_STR);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return new Bid($row['userid'],  $row['amount'], $row['cid'], $row['sid']);
        }
    }

    public function countBids($cid, $sid)
    {
        $sql = 'select COUNT(userid) as count from bid where cid=:cid and sid=:sid';

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

    public function lowestBid($cid, $sid)
    {
        $sql = 'select MIN(amount) as lowest from bid where cid=:cid and sid=:sid';

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

    public function retrieveClassBids($cid, $sid)
    {
        $sql = 'select userid, amount, cid, sid from bid where cid=:cid and sid=:sid ORDER BY amount DESC';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();


        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':cid', $cid, PDO::PARAM_STR);
        $stmt->bindParam(':sid', $sid, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Bid($row['userid'],  $row['amount'], $row['cid'], $row['sid']);
        }
        return $result;
    }

    public function retrieveClasses()
    {
        $sql = 'select DISTINCT cid, sid from bid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = ['cid' => $row['cid'],  'sid' => $row['sid']];
        }
        return $result;
    }

    public function retrieveAll()
    {
        $sql = 'select * from bid ORDER BY cid, sid, amount DESC, userid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
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

    public function dumpBid($cid, $sid)
    {
        $sql = 'select * from bid WHERE cid=:cid and sid=:sid ORDER BY amount DESC, userid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':cid', $cid, PDO::PARAM_STR);
        $stmt->bindParam(':sid', $sid, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        $count = 1;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = [
                "row" => $count,
                "userid" => $row['userid'],
                "amount" => $row['amount'],
                "result" => "-"
            ];
            $count++;
        }
        return $result;
    }

    public function bidStatus($cid, $sid)
    {
        $sql = 'select * from bid WHERE cid=:cid and sid=:sid ORDER BY amount DESC, userid';

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
                "amount" => $row['amount'],
            ];
        }
        return $result;
    }

    public function add($bid)
    {
        $sql = "INSERT IGNORE INTO bid (userid, amount, cid, sid) VALUES (:userid, :amount, :cid, :sid)";

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);


        $stmt->bindParam(':userid', $bid->userid, PDO::PARAM_STR);
        $stmt->bindParam(':amount', $bid->amount, PDO::PARAM_STR);
        $stmt->bindParam(':cid', $bid->cid, PDO::PARAM_STR);
        $stmt->bindParam(':sid', $bid->sid, PDO::PARAM_STR);

        $isAddOK = False;
        if ($stmt->execute()) {
            $isAddOK = True;
        }

        return $isAddOK;
    }

    public function replace($bid)
    {
        $sql = "REPLACE INTO bid (userid, amount, cid, sid) VALUES (:userid, :amount, :cid, :sid)";

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':userid', $bid->userid, PDO::PARAM_STR);
        $stmt->bindParam(':amount', $bid->amount, PDO::PARAM_STR);
        $stmt->bindParam(':cid', $bid->cid, PDO::PARAM_STR);
        $stmt->bindParam(':sid', $bid->sid, PDO::PARAM_STR);

        $isReplaceOK = False;
        if ($stmt->execute()) {
            $isReplaceOK = True;
        }

        return $isReplaceOK;
    }

    public function remove($userid, $cid)
    {
        $sql = 'DELETE from bid WHERE userid=:userid and cid=:cid';

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
        $sql = 'TRUNCATE TABLE bid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $stmt->execute();
        $count = $stmt->rowCount();
    }
}
