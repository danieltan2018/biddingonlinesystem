<?php

class CourseDAO
{
    public function retrieve($cid)
    {
        $sql = 'select cid, school, title, description, examdate, examstart, examend from course where cid=:cid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();


        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':cid', $cid, PDO::PARAM_STR);
        $stmt->execute();


        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $examdate = $row['examdate'];
            $examdate = str_replace("-", "", $examdate);
            $examstart = $row['examstart'];
            $examstart = str_replace(":", "", $examstart);
            $examstart = substr($examstart, 0, 4);
            $examstart = ltrim($examstart, '0');
            $examend = $row['examend'];
            $examend = str_replace(":", "", $examend);
            $examend = substr($examend, 0, 4);
            $examend = ltrim($examend, '0');
            return new Course($row['cid'], $row['school'], $row['title'], $row['description'], $examdate, $examstart, $examend);
        }
    }

    public function retrieveAll()
    {
        $sql = 'select * from course ORDER BY cid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $examdate = $row['examdate'];
            $examdate = str_replace("-", "", $examdate);
            $examstart = $row['examstart'];
            $examstart = str_replace(":", "", $examstart);
            $examstart = substr($examstart, 0, 4);
            $examstart = ltrim($examstart, '0');
            $examend = $row['examend'];
            $examend = str_replace(":", "", $examend);
            $examend = substr($examend, 0, 4);
            $examend = ltrim($examend, '0');
            $result[] = [
                "course" => $row['cid'],
                "school" => $row['school'],
                "title" => $row['title'],
                "description" => $row['description'],
                "exam date" => $examdate,
                "exam start" => $examstart,
                "exam end" => $examend
            ];
        }
        return $result;
    }

    public function add($course)
    {
        $sql = "INSERT IGNORE INTO course (cid, school, title, description, examdate, examstart, examend) VALUES (:cid, :school, :title, :description, :examdate, :examstart, :examend)";

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);


        $stmt->bindParam(':cid', $course->cid, PDO::PARAM_STR);
        $stmt->bindParam(':school', $course->school, PDO::PARAM_STR);
        $stmt->bindParam(':title', $course->title, PDO::PARAM_STR);
        $stmt->bindParam(':description', $course->description, PDO::PARAM_STR);
        $stmt->bindParam(':examdate', $course->examdate, PDO::PARAM_STR);
        $stmt->bindParam(':examstart', $course->examstart, PDO::PARAM_STR);
        $stmt->bindParam(':examend', $course->examend, PDO::PARAM_STR);

        $isAddOK = False;
        if ($stmt->execute()) {
            $isAddOK = True;
        }

        return $isAddOK;
    }

    public function update($course)
    {
        $sql = 'UPDATE course SET school=:school, title=:title, description=:description, examdate=:examdate, examstart=:examstart, examend=:examend  WHERE cid=:cid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':cid', $course->cid, PDO::PARAM_STR);
        $stmt->bindParam(':school', $course->school, PDO::PARAM_STR);
        $stmt->bindParam(':title', $course->title, PDO::PARAM_STR);
        $stmt->bindParam(':description', $course->description, PDO::PARAM_STR);
        $stmt->bindParam(':examdate', $course->examdate, PDO::PARAM_STR);
        $stmt->bindParam(':examstart', $course->examstart, PDO::PARAM_STR);
        $stmt->bindParam(':examend', $course->examend, PDO::PARAM_STR);

        $isUpdateOk = False;
        if ($stmt->execute()) {
            $isUpdateOk = True;
        }

        return $isUpdateOk;
    }

    public function removeAll()
    {
        $sql = 'TRUNCATE TABLE course';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $stmt->execute();
        $count = $stmt->rowCount();
    }
}
