<?php

class CourseCompletedDAO
{

    public function retrieve($userid)
    {
        $sql = 'select userid, cid from course_completed where userid=:userid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();


        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new CourseCompleted($row['userid'], $row['cid']);
        }
        return $result;
    }

    public function retrieveAll()
    {
        $sql = 'select * from course_completed ORDER BY cid, userid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = ["userid" => $row['userid'], "course" => $row['cid']];
        }
        return $result;
    }

    public function add($CourseCompleted)
    {
        $sql = "INSERT IGNORE INTO course_completed (userid, cid) VALUES (:userid, :cid)";

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);


        $stmt->bindParam(':userid', $CourseCompleted->userid, PDO::PARAM_STR);
        $stmt->bindParam(':cid', $CourseCompleted->cid, PDO::PARAM_STR);

        $isAddOK = False;
        if ($stmt->execute()) {
            $isAddOK = True;
        }

        return $isAddOK;
    }

    public function update($CourseCompleted)
    {
        $sql = 'UPDATE course_completed SET cid=:cid  WHERE userid=:userid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':userid', $CourseCompleted->userid, PDO::PARAM_STR);
        $stmt->bindParam(':cid', $CourseCompleted->cid, PDO::PARAM_STR);

        $isUpdateOk = False;
        if ($stmt->execute()) {
            $isUpdateOk = True;
        }

        return $isUpdateOk;
    }

    public function removeAll()
    {
        $sql = 'TRUNCATE TABLE course_completed';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $stmt->execute();
        $count = $stmt->rowCount();
    }
}
