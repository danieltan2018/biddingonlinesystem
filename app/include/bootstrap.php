<?php
require_once 'common.php';
require_once 'bid-process.php';

function doBootstrap()
{
	$zip_file = $_FILES["bootstrap-file"]["tmp_name"];
	$temp_dir = sys_get_temp_dir();

	if (isEmpty($zip_file)) {
		return ["status" => "error"];
	}

	$student_processed = 0;
	$course_processed = 0;
	$section_processed = 0;
	$prerequisite_processed = 0;
	$course_completed_processed = 0;
	$bid_processed = 0;

	$zip = new ZipArchive;
	if ($zip->open($zip_file)) {
		$zip->extractTo($temp_dir);
		$zip->close();

		$student_path = "$temp_dir/student.csv";
		$course_path = "$temp_dir/course.csv";
		$section_path = "$temp_dir/section.csv";
		$prerequisite_path = "$temp_dir/prerequisite.csv";
		$course_completed_path = "$temp_dir/course_completed.csv";
		$bid_path = "$temp_dir/bid.csv";

		$student = @fopen($student_path, "r");
		$course = @fopen($course_path, "r");
		$section = @fopen($section_path, "r");
		$prerequisite = @fopen($prerequisite_path, "r");
		$course_completed = @fopen($course_completed_path, "r");
		$bid = @fopen($bid_path, "r");

		$StudentDAO = new StudentDAO();
		$StudentDAO->removeAll();
		$CourseDAO = new CourseDAO();
		$CourseDAO->removeAll();
		$SectionDAO = new SectionDAO();
		$SectionDAO->removeAll();
		$PrerequisiteDAO = new PrerequisiteDAO();
		$PrerequisiteDAO->removeAll();
		$CourseCompletedDAO = new CourseCompletedDAO();
		$CourseCompletedDAO->removeAll();
		$BidDAO = new BidDAO();
		$BidDAO->removeAll();
		$BidResultDAO = new BidResultDAO();
		$BidResultDAO->removeAll();
		$MinBidDAO = new MinBidDAO();
		$MinBidDAO->removeAll();
		$ConfigDAO = new ConfigDAO();
		$ConfigDAO->setRound(1);

		$errors = [];

		$data = fgetcsv($student);
		$file = "student.csv";
		$line = 2;
		while (($data = fgetcsv($student)) !== false) {
			$data[0] = trim($data[0]); //userid
			$data[1] = trim($data[1]); //password
			$data[2] = trim($data[2]); //name
			$data[3] = trim($data[3]); //school
			$data[4] = trim($data[4]); //edollar
			$messages = [];
			if ($data[0] == null || $data[0] == "" || $data[1] == null || $data[1] == "" || $data[2] == null || $data[2] == "" || $data[3] == null || $data[3] == "" || $data[4] == null || $data[4] == "") {
				if ($data[0] == null || $data[0] == "") {
					$messages[] = "blank userid";
				}
				if ($data[1] == null || $data[1] == "") {
					$messages[] = "blank password";
				}
				if ($data[2] == null || $data[2] == "") {
					$messages[] = "blank name";
				}
				if ($data[3] == null || $data[3] == "") {
					$messages[] = "blank school";
				}
				if ($data[4] == null || $data[4] == "") {
					$messages[] = "blank edollar";
				}
			} else {
				if (strlen($data[0]) > 128) {
					$messages[] = "invalid userid";
				}
				if ($StudentDAO->retrieve($data[0]) !== null) {
					$messages[] = "duplicate userid";
				}
				if (!preg_match('/^\d+(\.(\d){1,2})?$/', $data[4])) {
					$messages[] = "invalid e-dollar";
				}
				if (strlen($data[1]) > 128) {
					$messages[] = "invalid password";
				}
				if (strlen($data[2]) > 100) {
					$messages[] = "invalid name";
				}
			}

			if (isEmpty($messages)) {
				if ($StudentDAO->add(new Student($data[0], $data[1], $data[2], $data[3], $data[4]))) {
					$student_processed++;
				}
			} else {
				$errors[] = ["file" => $file, "line" => $line, "message" => $messages];
			}
			$line++;
		}
		fclose($student);
		@unlink($student_path);

		$data = fgetcsv($course);
		$file = "course.csv";
		$line = 2;
		while (($data = fgetcsv($course)) !== false) {
			$data[0] = trim($data[0]); //course
			$data[1] = trim($data[1]); //school
			$data[2] = trim($data[2]); //title
			$data[3] = trim($data[3]); //description
			$data[4] = trim($data[4]); //exam date
			$data[5] = trim($data[5]); //exam start
			$data[6] = trim($data[6]); //exam end
			$messages = [];
			$date = date_parse_from_format("Ymd", $data[4]);
			$valid_date = (checkdate($date["month"], $date["day"], $date["year"]));
			if ($data[0] == null || $data[0] == "" || $data[1] == null || $data[1] == "" || $data[2] == null || $data[2] == "" || $data[3] == null || $data[3] == "" || $data[4] == null || $data[4] == "" || $data[5] == null || $data[5] == "" || $data[6] == null || $data[6] == "") {
				if ($data[0] == null || $data[0] == "") {
					$messages[] = "blank course";
				}
				if ($data[1] == null || $data[1] == "") {
					$messages[] = "blank school";
				}
				if ($data[2] == null || $data[2] == "") {
					$messages[] = "blank title";
				}
				if ($data[3] == null || $data[3] == "") {
					$messages[] = "blank description";
				}
				if ($data[4] == null || $data[4] == "") {
					$messages[] = "blank exam date";
				}
				if ($data[5] == null || $data[5] == "") {
					$messages[] = "blank exam start";
				}
				if ($data[6] == null || $data[6] == "") {
					$messages[] = "blank exam end";
				}
			} else {
				if (!preg_match('/^\d{8}$/', $data[4]) || !$valid_date) {
					$messages[] = "invalid exam date";
				}
				if (!preg_match('/^(2[0-3]|[01]?[0-9]):([0-5][0-9])$/', $data[5]) || !strtotime($data[5])) {
					$messages[] = "invalid exam start";
				}
				if (!preg_match('/^(2[0-3]|[01]?[0-9]):([0-5][0-9])$/', $data[6]) || !strtotime($data[6]) || strtotime($data[6]) <= strtotime($data[5])) {
					$messages[] = "invalid exam end";
				}
				if (strlen($data[2]) > 100) {
					$messages[] = "invalid title";
				}
				if (strlen($data[3]) > 1000) {
					$messages[] = "invalid description";
				}
			}

			if (isEmpty($messages)) {
				if ($CourseDAO->add(new Course($data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6]))) {
					$course_processed++;
				}
			} else {
				$errors[] = ["file" => $file, "line" => $line, "message" => $messages];
			}
			$line++;
		}
		fclose($course);
		@unlink($course_path);

		$data = fgetcsv($section);
		$file = "section.csv";
		$line = 2;
		while (($data = fgetcsv($section)) !== false) {
			$data[0] = trim($data[0]); //course
			$data[1] = trim($data[1]); //section
			$data[2] = trim($data[2]); //day
			$data[3] = trim($data[3]); //start
			$data[4] = trim($data[4]); //end
			$data[5] = trim($data[5]); //instructor
			$data[6] = trim($data[6]); //venue
			$data[7] = trim($data[7]); //size
			$messages = [];
			if ($data[0] == null || $data[0] == "" || $data[1] == null || $data[1] == "" || $data[2] == null || $data[2] == "" || $data[3] == null || $data[3] == "" || $data[4] == null || $data[4] == "" || $data[5] == null || $data[5] == "" || $data[6] == null || $data[6] == "" || $data[7] == null || $data[7] == "") {
				if ($data[0] == null || $data[0] == "") {
					$messages[] = "blank course";
				}
				if ($data[1] == null || $data[1] == "") {
					$messages[] = "blank section";
				}
				if ($data[2] == null || $data[2] == "") {
					$messages[] = "blank day";
				}
				if ($data[3] == null || $data[3] == "") {
					$messages[] = "blank start";
				}
				if ($data[4] == null || $data[4] == "") {
					$messages[] = "blank end";
				}
				if ($data[5] == null || $data[5] == "") {
					$messages[] = "blank instructor";
				}
				if ($data[6] == null || $data[6] == "") {
					$messages[] = "blank venue";
				}
				if ($data[7] == null || $data[7] == "") {
					$messages[] = "blank size";
				}
			} else {
				if ($CourseDAO->retrieve($data[0]) == null) {
					$messages[] = "invalid course";
				} elseif (!preg_match('/^S[1-9][0-9]?$/', $data[1])) {
					$messages[] = "invalid section";
				}
				if (!preg_match('/^[1-7]$/', $data[2])) {
					$messages[] = "invalid day";
				}
				if (!preg_match('/^(2[0-3]|[01]?[0-9]):([0-5][0-9])$/', $data[3]) || !strtotime($data[3])) {
					$messages[] = "invalid start";
				}
				if (!preg_match('/^(2[0-3]|[01]?[0-9]):([0-5][0-9])$/', $data[4]) || !strtotime($data[4]) || strtotime($data[4]) <= strtotime($data[3])) {
					$messages[] = "invalid end";
				}
				if (strlen($data[5]) > 100) {
					$messages[] = "invalid instructor";
				}
				if (strlen($data[6]) > 100) {
					$messages[] = "invalid venue";
				}
				if ((int) $data[7] < 1) {
					$messages[] = "invalid size";
				}
			}

			if (isEmpty($messages)) {
				if ($SectionDAO->add(new Section($data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7]))) {
					$section_processed++;
				}
			} else {
				$errors[] = ["file" => $file, "line" => $line, "message" => $messages];
			}
			$line++;
		}
		fclose($section);
		@unlink($section_path);

		$data = fgetcsv($prerequisite);
		$file = "prerequisite.csv";
		$line = 2;
		while (($data = fgetcsv($prerequisite)) !== false) {
			$data[0] = trim($data[0]); //cid
			$data[1] = trim($data[1]); //prerequisite
			$messages = [];
			if ($data[0] == null || $data[0] == "" || $data[1] == null || $data[1] == "") {
				if ($data[0] == null || $data[0] == "") {
					$messages[] = "blank course";
				}
				if ($data[1] == null || $data[1] == "") {
					$messages[] = "blank prerequisite";
				}
			} else {
				if ($CourseDAO->retrieve($data[0]) == null) {
					$messages[] = "invalid course";
				}
				if ($CourseDAO->retrieve($data[1]) == null) {
					$messages[] = "invalid prerequisite";
				}
			}

			if (isEmpty($messages)) {
				if ($PrerequisiteDAO->add(new Prerequisite($data[0], $data[1]))) {
					$prerequisite_processed++;
				}
			} else {
				$errors[] = ["file" => $file, "line" => $line, "message" => $messages];
			}
			$line++;
		}
		fclose($prerequisite);
		@unlink($prerequisite_path);

		$data = fgetcsv($course_completed);
		$file = "course_completed.csv";
		$line = 2;
		while (($data = fgetcsv($course_completed)) !== false) {
			$data[0] = trim($data[0]); //userid
			$data[1] = trim($data[1]); //cid
			$messages = [];
			if ($data[0] == null || $data[0] == "" || $data[1] == null || $data[1] == "") {
				if ($data[0] == null || $data[0] == "") {
					$messages[] = "blank userid";
				}
				if ($data[1] == null || $data[1] == "") {
					$messages[] = "blank code";
				}
			} else {
				if ($StudentDAO->retrieve($data[0]) == null) {
					$messages[] = "invalid userid";
				}
				if ($CourseDAO->retrieve($data[1]) == null) {
					$messages[] = "invalid course";
				}
				if (!empty($PrerequisiteDAO->retrieve($data[1]))) {
					$prerequired_arr = $PrerequisiteDAO->retrieve($data[1]);
					$prerequired = array_column($prerequired_arr, 'prerequisite');
					$completed_arr = $CourseCompletedDAO->retrieve($data[0]);
					$completed = array_column($completed_arr, 'cid');
					foreach ($prerequired as $cname) {
						if (!in_array($cname, $completed)) {
							$messages[] = "invalid course completed";
							break;
						}
					}
				}
			}
			if (isEmpty($messages)) {
				if ($CourseCompletedDAO->add(new CourseCompleted($data[0], $data[1]))) {
					$course_completed_processed++;
				}
			} else {
				$errors[] = ["file" => $file, "line" => $line, "message" => $messages];
			}
			$line++;
		}
		fclose($course_completed);
		@unlink($course_completed_path);

		$data = fgetcsv($bid);
		$file = "bid.csv";
		$line = 2;
		while (($data = fgetcsv($bid)) !== false) {
			$data[0] = trim($data[0]); //userid
			$data[1] = trim($data[1]); //amount
			$data[2] = trim($data[2]); //cid
			$data[3] = trim($data[3]); //sid
			$messages = [];
			if ($data[0] == null || $data[0] == "" || $data[1] == null || $data[1] == "" || $data[2] == null || $data[2] == "" || $data[3] == null || $data[3] == "") {
				if ($data[0] == null || $data[0] == "") {
					$messages[] = "blank userid";
				}
				if ($data[1] == null || $data[1] == "") {
					$messages[] = "blank amount";
				}
				if ($data[2] == null || $data[2] == "") {
					$messages[] = "blank code";
				}
				if ($data[3] == null || $data[3] == "") {
					$messages[] = "blank section";
				}
			} else {
				if ($StudentDAO->retrieve($data[0]) == null) {
					$messages[] = "invalid userid";
				}
				if (floatval($data[1]) < 10 || !preg_match('/^\d+(\.(\d){1,2})?$/', $data[1])) {
					$messages[] = "invalid amount";
				}
				if ($CourseDAO->retrieve($data[2]) == null) {
					$messages[] = "invalid course";
				} elseif ($SectionDAO->retrieve($data[2], $data[3]) == null) {
					$messages[] = "invalid section";
				}
			}

			// Logical Validation - from json/update-bid.php
			if (isEmpty($messages)) {
				$previous_bid = $BidDAO->retrieveBid($data[0], $data[2]);
				$stu = $StudentDAO->retrieve($data[0]);
				if ($previous_bid != null) {
					$previous_amount = $previous_bid->amount;
					$BidDAO->remove($data[0],$data[2]);
					$stu->edollar += $previous_amount;
					$StudentDAO->update($stu);
					$invalid_bid = checkbid($data[0], $data[1], $data[2], $data[3]);
					if (!isEmpty($invalid_bid)) {
						foreach ($invalid_bid as $bid_error) {
							$messages[] = $bid_error;
						}
					}
					//sufficient, remove e-dollar error
					if ($stu->edollar - $data[1] >= 0) {
						$messages = array_diff($messages, ["not enough e-dollar"]);
					}
					if (isEmpty($messages)) {
						$BidDAO->replace(new Bid($data[0], $data[1], $data[2], $data[3]));
						$stu->edollar -= $data[1]; // Deduct current bid
						$StudentDAO->update($stu);
						$bid_processed++;
					}
					else {
						$BidDAO->add($previous_bid);
						$stu->edollar -= $previous_amount;
						$StudentDAO->update($stu);
					}
				}
				else {
					$invalid_bid = checkbid($data[0], $data[1], $data[2], $data[3]);
					if (!isEmpty($invalid_bid)) {
						foreach ($invalid_bid as $bid_error) {
							$messages[] = $bid_error;
						}
					}
					if (isEmpty($messages)) {
						$BidDAO->replace(new Bid($data[0], $data[1], $data[2], $data[3]));
						$stu->edollar -= $data[1]; // Deduct current bid
						$StudentDAO->update($stu);
						$bid_processed++;
					}

				}
			}

			// Perform Update - from json/update-bid.php

			if (!isEmpty($messages)) {
				$errors[] = ["file" => $file, "line" => $line, "message" => $messages];
			}
			$line++;
		}
		fclose($bid);
		@unlink($bid_path);
	}

	$num_record_loaded = [
		["bid.csv" => $bid_processed],
		["course.csv" => $course_processed],
		["course_completed.csv" => $course_completed_processed],
		["prerequisite.csv" => $prerequisite_processed],
		["section.csv" => $section_processed],
		["student.csv" => $student_processed]
	];

	sort($errors);

	if (empty($errors)) {
		$result = [
			"status" => "success",
			"num-record-loaded" => $num_record_loaded
		];
	} else {
		$result = [
			"status" => "error",
			"num-record-loaded" => $num_record_loaded,
			"error" => $errors
		];
	}
	return $result;
}
