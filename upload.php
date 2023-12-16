<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

if (!isset($_SESSION["std_loggedin"]) || $_SESSION["std_loggedin"] === false) {
	header("location: std_home.php");
	exit;
}

try {
	require('database/config.php');
	$pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);
	$sql = "SELECT * FROM event_timings WHERE name='Update Resume'";
	$res = $pdo->query($sql);
	$res = $res->fetch(PDO::FETCH_ASSOC);
	$update_resume_start_time = $res['start_time'];
	$update_resume_end_time = $res['end_time'];

	if (strtotime($update_resume_start_time) > time() || strtotime($update_resume_end_time) < time())
		echo "<script>alert(`Can't update resume now.`);window.location='std_resume.php';</script>";

} catch (PDOException $e) {
	die("Some Error Occured!");
}

$resume = '';

if (isset($_FILES['resume1']))
	$resume = 'resume1';
else if (isset($_FILES['resume2']))
	$resume = 'resume2';
else if (isset($_FILES['resume3']))
	$resume = 'resume3';
else
	header("location: std_resume.php");


$roll_no = $_SESSION["std_roll_no"];
$msg = "";

if ($_FILES[$resume]['type'] == "application/pdf") {
	$source_file = $_FILES[$resume]['tmp_name'];
	$dest_file = "uploads/" . guidv4() . '.pdf';
	move_uploaded_file($source_file, $dest_file)
		or die("Error!!");
	if ($_FILES[$resume]['error'] == 0) {
		updateDatabase($dest_file);
	} else {
		$msg = "Some error occured. Please try again later.";
	}
} else if ($_FILES[$resume]['type'] != "application/pdf") {
	$msg = "File extension is not pdf. Only pdfs are allowed.";
}

function updateDatabase($link)
{
	$roll_no = $GLOBALS['roll_no'];
	$resume = $GLOBALS['resume'];
	$msg = '';
	try {
		require('database/config.php');

		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);

		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$resume_no = substr($resume, -1);
		$sql = "SELECT link FROM resume WHERE roll_no='$roll_no' and resume_no=$resume_no";

		$res = $conn->query($sql);
		$count = $res->rowCount();

		if ($count === 0) {
			$sql = "INSERT INTO resume VALUES('$roll_no',$resume_no,0,'$link')";
			$conn->exec($sql);
		} else {
			$res = $res->fetch(PDO::FETCH_ASSOC);
			unlink($res['link']);
			$sql = "UPDATE resume SET link='$link' WHERE roll_no='$roll_no' and resume_no=$resume_no";
			$conn->exec($sql);
		}

		$msg = "$resume uploaded successfully";
	} catch (PDOException $e) {
		$msg = "Some error occured. Please try again later.";
	}

	$conn = null;
	echo "<script>alert('$msg');window.location='std_resume.php';</script>";
}

function guidv4($data = null)
{
	$data = $data ?? random_bytes(16);
	assert(strlen($data) == 16);
	$data[6] = chr(ord($data[6]) & 0x0f | 0x40);
	$data[8] = chr(ord($data[8]) & 0x3f | 0x80);
	return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

echo "<script>alert('$msg');window.location='std_resume.php';</script>";
?>