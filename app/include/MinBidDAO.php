<?php

class MinBidDAO
{
    public function getMinBid($cid, $sid)
    {
        $sql = 'select minbid from minbid where cid=:cid and sid=:sid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();


        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':cid', $cid, PDO::PARAM_STR);
        $stmt->bindParam(':sid', $sid, PDO::PARAM_STR);
        $stmt->execute();

        if ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return (float) $result['minbid'];
        } else {
            return 10.00;
        }
    }

    public function updateMinBid($cid, $sid, $minbid)
    {
        $sql = "REPLACE INTO minbid (cid, sid, minbid) VALUES (:cid, :sid, :minbid)";

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':cid', $cid, PDO::PARAM_STR);
        $stmt->bindParam(':sid', $sid, PDO::PARAM_STR);
        $stmt->bindParam(':minbid', $minbid, PDO::PARAM_STR);

        $isReplaceOK = False;
        if ($stmt->execute()) {
            $isReplaceOK = True;
        }

        return $isReplaceOK;
    }

    public function removeAll()
    {
        $sql = 'TRUNCATE TABLE minbid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $stmt->execute();
        $count = $stmt->rowCount();
    }
}
