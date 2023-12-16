<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

if (!isset($_SESSION["std_loggedin"]) || $_SESSION["std_loggedin"] === false) {
	header("location: std_login.php");
	exit;
}

include_once('database/config.php');

try {
	$pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);
	$roll_no = $_SESSION['std_roll_no'];
	$sql = "SELECT s.*, c.name as course_name FROM student s,course c WHERE s.roll_no='$roll_no' and s.course = c.id";
	$res = $pdo->query($sql);
	$res = $res->fetch(PDO::FETCH_ASSOC);
	$name = $res['name'];
	$email = $res['email'];
	$course_name = $res['course_name'];
	$roll_no = $res['roll_no'];
	$tenth_percentage = $res['tenth_percentage'];
	$twelth_percentage = $res['twelth_percentage'];
	$ug_cpi = $res['ug_cpi'];
	$pg_cpi = $res['pg_cpi'] == '' ? 'Not Applicable' : $res['pg_cpi'];
	$phd_cpi = $res['phd_cpi'] == '' ? 'Not Applicable' : $res['phd_cpi'];
} catch (PDOException $e) {
	die("An Error Occurred!");
}

$pdo = null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Student Home</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
		integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<link rel='stylesheet' href='css/sidebar.css'>
</head>

<body class="d-flex flex-row flex-shrink-0" style="background-color: #eee;">
	<?php
    include_once('components/std_sidebar.php');
    createStdSidebar('Home');
    ?>
	<main class="flex-grow-1 d-flex flex-column" style="height:100vh; overflow:auto;">
		<?php include_once('components/header.php'); ?>
		<div class='flex-grow-1 m-2 d-flex align-items-center justify-content-center '>
			<div class='bg-white shadow overflow-auto container' style='width: auto; min-width: 80%'>
				<h1 class='p-4   text-center h3 border-bottom'>Your Profile</h1>
				<div class="row">
					<div class="col p-4">
						<div class="fw-bold">Name</div>
						<div>
							<?php echo $name; ?>
						</div>
					</div>
					<div class="col p-4">
						<div class="fw-bold">Roll No</div>
						<div>
							<?php echo $roll_no; ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col p-4">
						<div class="fw-bold">10th Percentage</div>
						<div>
							<?php echo $tenth_percentage; ?>
						</div>
					</div>
					<div class="col p-4">
						<div class="fw-bold">12 th Percentage</div>
						<div>
							<?php echo $twelth_percentage; ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col p-4">
						<div class="fw-bold">UG CPI</div>
						<div>
							<?php echo $ug_cpi; ?>
						</div>
					</div>
					<div class="col p-4">
						<div class="fw-bold">PG CPI</div>
						<div>
							<?php echo $pg_cpi; ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col p-4">
						<div class="fw-bold">PHD CPI</div>
						<div>
							<?php echo $phd_cpi; ?>
						</div>
					</div>
					<div class="col p-4">
						<div class="fw-bold">Email</div>
						<div>
							<?php echo $email; ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col p-4">
						<div class="fw-bold">Course</div>
						<div>
							<?php echo $course_name; ?>
						</div>
					</div>
				</div>

			</div>
		</div>
	</main>
	<script src='js/index.js'></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
		crossorigin="anonymous"></script>
</body>

</html>