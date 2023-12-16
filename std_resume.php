<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

if (!isset($_SESSION["std_loggedin"]) || $_SESSION["std_loggedin"] === false) {
	header("location: std_login.php");
	exit;
}


$cv1 = false;
$cv2 = false;
$cv3 = false;

try {
	require('database/config.php');
	$pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);
	$roll_no = $_SESSION['std_roll_no'];
	$sql1 = "SELECT link FROM resume WHERE roll_no='$roll_no' and resume_no=1";
	$sql2 = "SELECT link FROM resume WHERE roll_no='$roll_no' and resume_no=2";
	$sql3 = "SELECT link FROM resume WHERE roll_no='$roll_no' and resume_no=3";
	$res1 = $pdo->query($sql1);
	$res2 = $pdo->query($sql2);
	$res3 = $pdo->query($sql3);
	$count1 = $res1->rowCount();
	$count2 = $res2->rowCount();
	$count3 = $res3->rowCount();
	$res1 = $res1->fetch(PDO::FETCH_ASSOC);
	if ($res1)
		$link1 = $res1['link'];
	$res2 = $res2->fetch(PDO::FETCH_ASSOC);
	if ($res2)
		$link2 = $res2['link'];
	$res3 = $res3->fetch(PDO::FETCH_ASSOC);
	if ($res3)
		$link3 = $res3['link'];


	if ($count1 > 0)
		$cv1 = true;

	if ($count2 > 0)
		$cv2 = true;

	if ($count3 > 0)
		$cv3 = true;

	$sql = "SELECT * FROM event_timings WHERE name='Update Resume'";
	$res = $pdo->query($sql);
	$res = $res->fetch(PDO::FETCH_ASSOC);
	$update_resume_start_time = $res['start_time'];
	$update_resume_end_time = $res['end_time'];
} catch (PDOException $e) {
	die("Some Error Occured!");
}

$pdo = null;
$roll_no = $_SESSION['std_roll_no'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Student Resume</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
		integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<link rel='stylesheet' href='css/sidebar.css'>
</head>

<body class="d-flex flex-row flex-shrink-0" style="background-color: #eee;">
	<?php
    include_once('components/std_sidebar.php');
    createStdSidebar('Resume');
    ?>
	<main class="flex-grow-1 d-flex flex-column" style="height:100vh; overflow:auto;">
		<?php include_once('components/header.php'); ?>
		<div class='flex-grow-1 m-2 d-flex align-items-center justify-content-center '>
			<div class='bg-white shadow overflow-auto' style='min-width: 80%' action='upload.php'>
				<h1 class='p-4 mb-4 text-center h3 border-bottom'>Your Resume</h1>
				<div class=' alert alert-secondary m-4'>
					<?php
                    if (strtotime($update_resume_start_time) > time()) {
	                    echo "You can update your resumes from $update_resume_start_time";
                    } else if (strtotime($update_resume_end_time) < time()) {
	                    echo "Deadline is over. You can't update your resumes now.";
                    } else {
	                    echo "Deadline for updating resumes: $update_resume_end_time";
                    }
                    ?>
				</div>
				<form method='post' enctype="multipart/form-data" action='upload.php'
					class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 mx-auto my-5 justify-content-center">
					<div class="col-auto col-md-2 py-1 px-3 fs-2  text-muted align-self-center text-center">
						CV1
					</div>
					<div class="col-auto col-md-2 py-1 px-3 align-self-center text-center">
						<div>
							<?php
                            if ($cv1) {
	                            echo "<svg class='text-success' xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-check-circle' viewBox='0 0 16 16'>
								<path d='M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z'/>
								<path d='M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z'/>
							  </svg> 
							  <a class='btn btn-outline-success btn-sm m-2' href='$link1'>view</a>";
                            } else {
	                            echo '<svg class="text-danger m-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
								<path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
								<path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
							  </svg><span class="text-danger"> Not Uploaded.</span>';
                            }
                            ?>
						</div>
					</div>
					<div class='col-auto col-md-6 py-1 px-3 align-self-center text-center'><input type="file"
							id='resume1' name="resume1" class="form-control text-muted my-2" required <?php if (
                            	strtotime($update_resume_start_time) > time() || strtotime($update_resume_end_time) < time()
                            )
	                            echo "disabled"; ?>></div>


					<div class="col-auto col-md-2 py-1 px-3 align-self-center text-center"><input type="submit"
							value='<?php echo ($cv1 ? 'update' : 'submit'); ?>'
							class="btn btn-outline-primary p-2 mx-auto" style='width:6rem' <?php if (
                            	strtotime($update_resume_start_time) > time() || strtotime($update_resume_end_time) < time()
                            )
	                            echo "disabled"; ?>
						></div>
				</form>
				<form method='post' enctype="multipart/form-data" action='upload.php'
					class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 mx-auto my-5 justify-content-center">
					<div class="col-auto col-md-2 py-1 px-3 fs-2  text-muted align-self-center text-center">
						CV2
					</div>
					<div class="col-auto col-md-2 py-1 px-3 align-self-center text-center">
						<div>
							<?php
                            if ($cv2) {
	                            echo "<svg class='text-success' xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-check-circle' viewBox='0 0 16 16'>
								<path d='M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z'/>
								<path d='M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z'/>
							  </svg> 
							  <a class='btn btn-outline-success btn-sm m-2' href='$link2'>view</a>";
                            } else {
	                            echo '<svg class="text-danger m-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
								<path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
								<path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
							  </svg><span class="text-danger">Not Uploaded.</span>';
                            }
                            ?>
						</div>
					</div>
					<div class='col-auto col-md-6 py-1 px-3 align-self-center text-center'><input type="file"
							id='resume2' name="resume2" class="form-control text-muted my-2" required <?php if (
                            	strtotime($update_resume_start_time) > time() || strtotime($update_resume_end_time) < time()
                            )
	                            echo "disabled"; ?>></div>


					<div class="col-auto col-md-2 py-1 px-3 align-self-center text-center"><input type="submit"
							value='<?php echo ($cv1 ? 'update' : 'submit'); ?>'
							class="btn btn-outline-primary p-2 mx-auto" style='width:6rem' <?php if (
                            	strtotime($update_resume_start_time) > time() || strtotime($update_resume_end_time) < time()
                            )
	                            echo "disabled"; ?>
						></div>
				</form>
				<form method='post' enctype="multipart/form-data" action='upload.php'
					class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 mx-auto my-5 justify-content-center">
					<div class="col-auto col-md-2 py-1 px-3 fs-2  text-muted align-self-center text-center">
						CV3
					</div>
					<div class="col-auto col-md-2 py-1 px-3 align-self-center text-center">
						<div>
							<?php
                            if ($cv3) {
	                            echo "<svg class='text-success' xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-check-circle' viewBox='0 0 16 16'>
								<path d='M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z'/>
								<path d='M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z'/>
							  </svg> 
							  <a class='btn btn-outline-success btn-sm m-2' href='$link3'>view</a>";
                            } else {
	                            echo '<svg class="text-danger m-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
								<path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
								<path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
							  </svg><span class="text-danger"> Not Uploaded.</span>';
                            }
                            ?>
						</div>
					</div>
					<div class='col-auto col-md-6 py-1 px-3 align-self-center text-center'><input type="file"
							id='resume3' name="resume3" class="form-control text-muted my-2" required <?php if (
							strtotime($update_resume_start_time) > time() || strtotime($update_resume_end_time) < time()
							) echo "disabled"; ?>></div>


					<div class="col-auto col-md-2 py-1 px-3 align-self-center text-center"><input type="submit"
							value='<?php echo ($cv1 ? 'update' : 'submit'); ?>'
							class="btn btn-outline-primary p-2 mx-auto" style='width:6rem' <?php if (
							strtotime($update_resume_start_time) > time() || strtotime($update_resume_end_time) < time()
							) echo "disabled"; ?>
						></div>
				</form>
			</div>
		</div>
	</main>
	<script src='js/index.js'></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
		crossorigin="anonymous"></script>
</body>

</html>