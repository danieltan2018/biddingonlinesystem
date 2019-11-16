<?php

class ConfigDAO
{
    public function retrieve($item)
    {
        $sql = 'select value from config where item=:item';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':item', $item, PDO::PARAM_STR);
        $stmt->execute();

        if ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $result['value'];
        }
    }

    public function getRound()
    {
        $sql = "select value from config where item='round'";

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        if ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return (int) $result['value'];
        }
    }

    public function setRound($value)
    {
        $sql = "REPLACE INTO config (item, value) VALUES ('round', :value)";

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':value', $value, PDO::PARAM_STR);

        $isReplaceOK = False;
        if ($stmt->execute()) {
            $isReplaceOK = True;
        }

        return $isReplaceOK;
    }

    public function resetRound()
    {
        $sql = "DELETE from config WHERE item = 'round'";

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $isDelOK = False;
        if ($stmt->execute()) {
            $isDelOK = True;
        }

        return $isDelOK;
    }
}
