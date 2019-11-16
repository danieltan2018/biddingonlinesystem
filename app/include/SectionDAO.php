<?php

class SectionDAO
{
    public function retrieve($cid, $sid)
    {
        $sql = 'select cid, sid, dayweek, starttime, endtime, instructor, venue, size from section where cid=:cid and sid=:sid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();


        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':cid', $cid, PDO::PARAM_STR);
        $stmt->bindParam(':sid', $sid, PDO::PARAM_STR);
        $stmt->execute();


        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $starttime = $row['starttime'];
            $starttime = str_replace(":", "", $starttime);
            $starttime = substr($starttime, 0, 4);
            $starttime = ltrim($starttime, '0');
            $endtime = $row['endtime'];
            $endtime = str_replace(":", "", $endtime);
            $endtime = substr($endtime, 0, 4);
            $endtime = ltrim($endtime, '0');
            $size = $row['size'];
            $size = intval($size);
            if ($row['dayweek'] == 1) {
                $day = "Monday";
            }
            elseif ($row['dayweek'] == 2) {
                $day = "Tuesday";
            }
            elseif ($row['dayweek'] == 3) {
                $day = "Wednesday";
            }
            elseif ($row['dayweek'] == 4) {
                $day = "Thursday";
            }
            elseif ($row['dayweek'] == 5) {
                $day = "Friday";
            }
            elseif ($row['dayweek'] == 6) {
                $day = "Saturday";
            }
            elseif ($row['dayweek'] == 7) {
                $day = "Sunday";
            }
            return new Section($row['cid'], $row['sid'], $day, $starttime, $endtime, $row['instructor'], $row['venue'], $size);
        }
    }

    public function retrieveAll()
    {
        $sql = 'select * from section ORDER BY cid, sid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $starttime = $row['starttime'];
            $starttime = str_replace(":", "", $starttime);
            $starttime = substr($starttime, 0, 4);
            $starttime = ltrim($starttime, '0');
            $endtime = $row['endtime'];
            $endtime = str_replace(":", "", $endtime);
            $endtime = substr($endtime, 0, 4);
            $starttime = ltrim($starttime, '0');
            $size = $row['size'];
            $size = intval($size);
            if ($row['dayweek'] == 1) {
                $day = "Monday";
            }
            elseif ($row['dayweek'] == 2) {
                $day = "Tuesday";
            }
            elseif ($row['dayweek'] == 3) {
                $day = "Wednesday";
            }
            elseif ($row['dayweek'] == 4) {
                $day = "Thursday";
            }
            elseif ($row['dayweek'] == 5) {
                $day = "Friday";
            }
            elseif ($row['dayweek'] == 6) {
                $day = "Saturday";
            }
            elseif ($row['dayweek'] == 7) {
                $day = "Sunday";
            }
            $result[] = [
                "course" => $row['cid'],
                "section" => $row['sid'],
                "day" => $day,
                "start" => $starttime,
                "end" => $endtime,
                "instructor" => $row['instructor'],
                "venue" => $row['venue'],
                "size" => $size
            ];
        }
        return $result;
    }

    public function add($section)
    {
        $sql = "INSERT IGNORE INTO section (cid, sid, dayweek, starttime, endtime, instructor, venue, size) VALUES (:cid, :sid, :dayweek, :starttime, :endtime, :instructor, :venue, :size)";

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);


        $stmt->bindParam(':cid', $section->cid, PDO::PARAM_STR);
        $stmt->bindParam(':sid', $section->sid, PDO::PARAM_STR);
        $stmt->bindParam(':dayweek', $section->dayweek, PDO::PARAM_STR);
        $stmt->bindParam(':starttime', $section->starttime, PDO::PARAM_STR);
        $stmt->bindParam(':endtime', $section->endtime, PDO::PARAM_STR);
        $stmt->bindParam(':instructor', $section->instructor, PDO::PARAM_STR);
        $stmt->bindParam(':venue', $section->venue, PDO::PARAM_STR);
        $stmt->bindParam(':size', $section->size, PDO::PARAM_STR);

        $isAddOK = False;
        if ($stmt->execute()) {
            $isAddOK = True;
        }

        return $isAddOK;
    }

    public function update($section)
    {
        $sql = 'UPDATE section SET cid=:cid, sid=:sid, dayweek=:dayweek, starttime=:starttime, endtime=:endtime, instructor=:instructor, venue=:venue, size=:size  WHERE cid=:cid and sid=:sid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':cid', $section->cid, PDO::PARAM_STR);
        $stmt->bindParam(':sid', $section->sid, PDO::PARAM_STR);
        $stmt->bindParam(':dayweek', $section->dayweek, PDO::PARAM_STR);
        $stmt->bindParam(':starttime', $section->starttime, PDO::PARAM_STR);
        $stmt->bindParam(':endtime', $section->endtime, PDO::PARAM_STR);
        $stmt->bindParam(':instructor', $section->instructor, PDO::PARAM_STR);
        $stmt->bindParam(':venue', $section->venue, PDO::PARAM_STR);
        $stmt->bindParam(':size', $section->size, PDO::PARAM_STR);

        $isUpdateOk = False;
        if ($stmt->execute()) {
            $isUpdateOk = True;
        }

        return $isUpdateOk;
    }

    public function removeAll()
    {
        $sql = 'TRUNCATE TABLE section';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $stmt->execute();
        $count = $stmt->rowCount();
    }
}
