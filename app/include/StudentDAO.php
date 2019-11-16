<?php

class StudentDAO
{

    public function retrieve($userid)
    {
        $sql = 'select userid, password, name, school, edollar from student where userid=:userid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();


        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->execute();


        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return new Student($row['userid'],  $row['password'], $row['name'], $row['school'], $row['edollar']);
        }
    }

    public function retrieveAll()
    {
        $sql = 'select * from student ORDER BY userid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = (array) new Student($row['userid'], $row['password'], $row['name'], $row['school'], $row['edollar']);
        }
        return $result;
    }

    public function add($user)
    {
        $sql = "INSERT IGNORE INTO student (userid, password, name, school, edollar) VALUES (:userid, :password, :name, :school, :edollar)";

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':userid', $user->userid, PDO::PARAM_STR);
        $stmt->bindParam(':password', $user->password, PDO::PARAM_STR);
        $stmt->bindParam(':name', $user->name, PDO::PARAM_STR);
        $stmt->bindParam(':school', $user->school, PDO::PARAM_STR);
        $stmt->bindParam(':edollar', $user->edollar, PDO::PARAM_STR);

        $isAddOK = False;
        if ($stmt->execute()) {
            $isAddOK = True;
        }

        return $isAddOK;
    }

    public function update($user)
    {
        $sql = 'UPDATE student SET name=:name, school=:school, edollar=:edollar WHERE userid=:userid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);


        $stmt->bindParam(':userid', $user->userid, PDO::PARAM_STR);
        $stmt->bindParam(':name', $user->name, PDO::PARAM_STR);
        $stmt->bindParam(':school', $user->school, PDO::PARAM_STR);
        $stmt->bindParam(':edollar', $user->edollar, PDO::PARAM_STR);

        $isUpdateOk = False;
        if ($stmt->execute()) {
            $isUpdateOk = True;
        }

        return $isUpdateOk;
    }

    public function removeAll()
    {
        $sql = 'TRUNCATE TABLE student';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $stmt->execute();
        $count = $stmt->rowCount();
    }
}
