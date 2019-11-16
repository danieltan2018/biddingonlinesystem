<?php

class PrerequisiteDAO
{
    public function retrieve($cid)
    {
        $sql = 'select cid, prerequisite from prerequisite where cid=:cid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();


        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':cid', $cid, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Prerequisite($row['cid'], $row['prerequisite']);
        }
        return $result;
    }

    public function retrieveAll()
    {
        $sql = 'select * from prerequisite ORDER BY cid, prerequisite';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = ["course" => $row['cid'], "prerequisite" => $row['prerequisite']];
        }
        return $result;
    }

    public function add($prerequisite)
    {
        $sql = "INSERT IGNORE INTO prerequisite (cid, prerequisite) VALUES (:cid, :prerequisite)";

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);


        $stmt->bindParam(':cid', $prerequisite->cid, PDO::PARAM_STR);
        $stmt->bindParam(':prerequisite', $prerequisite->prerequisite, PDO::PARAM_STR);

        $isAddOK = False;
        if ($stmt->execute()) {
            $isAddOK = True;
        }

        return $isAddOK;
    }

    public function update($prerequisite)
    {
        $sql = 'UPDATE course SET prerequisite=:prerequisite WHERE cid=:cid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':cid', $prerequisite->cid, PDO::PARAM_STR);
        $stmt->bindParam(':school', $prerequisite->prerequisite, PDO::PARAM_STR);

        $isUpdateOk = False;
        if ($stmt->execute()) {
            $isUpdateOk = True;
        }

        return $isUpdateOk;
    }

    public function removeAll()
    {
        $sql = 'TRUNCATE TABLE prerequisite';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $stmt->execute();
        $count = $stmt->rowCount();
    }
}
