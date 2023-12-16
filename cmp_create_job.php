<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

if (!isset($_SESSION["cmp_loggedin"]) || $_SESSION["cmp_loggedin"] === false) {
    header("location: cmp_login.php");
    exit;
}

try {
    require('database/config.php');
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);
    $sql = "SELECT * FROM event_timings WHERE name='Edit Job Profile'";
    $res = $pdo->query($sql);
    $res = $res->fetch(PDO::FETCH_ASSOC);
    $edit_profile_start_time = $res['start_time'];
    $edit_profile_end_time = $res['end_time'];
    $pdo = null;
} catch (PDOException $e) {
    die("Some Error Occured!");
}

function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function isValidDeadline($date)
{
    return strtotime($date) >= strtotime(date("Y-m-d")) + 24 * 60 * 60;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && strtotime($edit_profile_start_time) <= time() && strtotime($edit_profile_end_time) >= time()) {
    $title = sanitize_input($_POST['title']);
    $salary = floatval(sanitize_input($_POST['salary']));
    $num_vacancies = floatval(sanitize_input($_POST['num_vacancies']));
    $ten_perc = floatval(sanitize_input($_POST['10th_perc']));
    $twelth_perc = floatval(sanitize_input($_POST['12th_perc']));
    $ug_cpi = floatval(sanitize_input($_POST['ug_cpi']));
    $pg_cpi = floatval(sanitize_input($_POST['pg_cpi']));
    $phd_cpi = floatval(sanitize_input($_POST['phd_cpi']));
    $link = sanitize_input($_POST['link']);
    $deadline = sanitize_input($_POST['deadline']);
    $courses = isset($_POST['courses']) ? $_POST['courses'] : [];
    $cmp_id = $_SESSION['cmp_id'];
    $error = "";

    if ($title === '')
        $error = $error . "<li>Job Title is Necessary.</li>";

    if (!preg_match("/^[a-zA-Z ]*$/", $title))
        $error = $error . "<li>Only letters and spaces are allowed in title.</li>";

    if ($link != '' && !filter_var($link, FILTER_VALIDATE_URL))
        $error = $error . "<li>Invalid link.</li>";

    if ($deadline === '')
        $error = $error . "<li>Deadline is necessary.</li>";
    else if (!isValidDeadline($deadline))
        $error = $error . "<li>Deadline should be at least tomorrow.</li>";

    if ($ten_perc < 0 || $ten_perc > 100 || $twelth_perc < 0 || $twelth_perc > 100 || $ug_cpi < 0 || $ug_cpi > 10 || $pg_cpi < 0 || $pg_cpi > 10 || $phd_cpi < 0 || $phd_cpi > 10) {
        $error = $error . "<li>Invalid Value for Grade or Percetage.</li>";
    }

    if (count($courses) === 0) {
        $error = $error . "<li>No course selected.</li>";
    }


    try {
        require('database/config.php');
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);
        $sql = "SELECT * FROM job_role WHERE company_id='$cmp_id' and title='$title'";
        $res = $conn->query($sql);
        $count = $res->rowCount();

        if ($count > 0)
            $error = $error . "<li>A Job Profile with same title already exists!";
    } catch (PDOException $e) {
        $error = $error . "An Error Occurred!";
    }

    $conn = null;

    if ($error != '')
        $error = "<ol>" . $error . "</ol>";


    if ($error == '') {
        require('database/config.php');

        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql1 = "INSERT INTO job_role VALUES ('$cmp_id','$title',$salary,$num_vacancies, $ten_perc,$twelth_perc,$ug_cpi, $pg_cpi,$phd_cpi,'$link','$deadline')";
            $conn->exec($sql1);

            foreach ($courses as $id) {
                $sql2 = "INSERT INTO allowed_courses VALUES ('$cmp_id','$title','$id');";
                $conn->exec($sql2);
            }

            $success = "Job Profile Created Successfully.";
        } catch (PDOException $e) {
            $error = "Some Error Occurred!";
        }

        $conn = null;
    }
}

function courses()
{
    require('database/config.php');

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);

        $sql = 'SELECT id, name 
					FROM course';

        $statement = $conn->query($sql);
        $res = $statement->fetchAll(PDO::FETCH_ASSOC);
        $ans = array();

        foreach ($res as $item) {
            $tmp = array($item['name'], $item['id']);
            array_push($ans, $tmp);
        }
        return $ans;
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }

    $conn = null;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Job Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel='stylesheet' href='css/sidebar.css'>
</head>

<body class="d-flex flex-row flex-shrink-0" style="background-color: #eee;">
    <?php
    include_once('components/cmp_sidebar.php');
    createStdSidebar('Job Profiles');
    ?>

    <main class="flex-grow-1 d-flex flex-column" style="height:100vh; overflow:auto;">
        <?php include_once('components/header.php'); ?>
        <div class='flex-grow-1 m-2 d-flex align-items-center justify-content-center '>
            <div class='bg-white shadow overflow-auto' style='min-width: 80%'>
                <h1 class='p-4   text-center h3 border-bottom'>Create Job Profile</h1>
                <div class="border-bottom">
                    <a class="btn btn-primary m-4" href='cmp_profiles.php'>View Job Profiles</a>
                </div>

                <?php
                if (isset($error) && $error != '') {
                    echo '<div class="m-4 alert alert-danger alert-dismissible fade show" role="alert">
                      <strong>Error!</strong> ' . $error . '
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                }
                if (isset($success) && $success != '') {
                    echo '<div class="m-4 alert alert-success alert-dismissible fade show" role="alert">
                      <strong>Success!</strong> ' . $success . '
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                }
                echo "<div class='alert alert-secondary m-4'>";
                if (strtotime($edit_profile_start_time) > time()) {
                    echo "You can start to create job profiles from $edit_profile_start_time";
                } else if (strtotime($edit_profile_end_time) < time()) {
                    echo "Deadline is over. You can't create any job profile now.";
                } else {
                    echo "Deadline for Creating Profiles: $edit_profile_end_time";
                }
                echo "</div>";
                ?>

                <?php

                if (strtotime($edit_profile_start_time) <= time() && strtotime($edit_profile_end_time) >= time())
                    echo "<form method='post' class='p-4'>
                    <div class='form-floating mb-3'>
                        <input type='text' class='form-control' id='title' name='title' placeholder=' ' required>
                        <label for='title'>Job Title</label>
                    </div>
                    <div class='form-floating mb-3'>
                        <input type='number' min='0' class='form-control' id='salary' name='salary' placeholder=' '>
                        <label for='salary'>Salary</label>
                    </div>
                    <div class='form-floating mb-3'>
                        <input type='number' min='0' class='form-control' id='num_vacancies' name='num_vacancies'
                            placeholder=' '>
                        <label for='num_vacancies'>No. of Vacancies</label>
                    </div>

                    <div class='form-floating mb-3'>
                        <input type='number' step='0.1' min='0' max='100' class='form-control' id='10th_perc'
                            name='10th_perc' placeholder=' '>
                        <label for='10th_perc'>10th Percentage Required</label>
                    </div>
                    <div class='form-floating mb-3'>
                        <input type='number' step='0.1' min='0' max='100' class='form-control' id='12th_perc'
                            name='12th_perc' placeholder=' '>
                        <label for='12th_perc'>12th Percentage Required</label>
                    </div>

                    <div class='form-floating mb-3'>
                        <input type='number' step='0.1' min='0' max='10' class='form-control' id='ug_cpi' name='ug_cpi'
                            placeholder=' '>
                        <label for='ug_cpi'>UG CPI Required</label>
                    </div>
                    <div class='form-floating mb-3'>
                        <input type='number' step='0.1' min='0' max='10' class='form-control' id='pg_cpi' name='pg_cpi'
                            placeholder=' '>
                        <label for='pg_cpi'>PG CPI Required</label>
                    </div>
                    <div class='form-floating mb-3'>
                        <input type='number' step='0.1' min='0' max='10' class='form-control' id='phd_cpi'
                            name='phd_cpi' placeholder=' '>
                        <label for='phd_cpi'>PHD CPI Required</label>
                    </div>
                    <div class='form-floating mb-3 border p-3'>
                        <div class='mb-3 fw-bold'> Eligible Courses </div>
                        <div id='courses'>";

                if (strtotime($edit_profile_start_time) <= time() && strtotime($edit_profile_end_time) >= time()) {
                    $c = courses();

                    $n = count($c);

                    for ($i = 0; $i < $n; ++$i) {
                        $t = $c[$i];
                        $id = $t[1];
                        $name = $t[0];
                        echo "<div class='form-check'>
                                <input class='form-check-input' type='checkbox' name='courses[]' value='$id' id='ch$i' checked>
                                <label class='form-check-label' for='ch$i'>
                                  $name
                                </label>
                              </div>";
                    }
                }

                if (strtotime($edit_profile_start_time) <= time() && strtotime($edit_profile_end_time) >= time())
                    echo "
                    </div>
                    <button class='btn btn-success btn-sm my-3' id='select-all-btn'>Select
                        All</button>
                    <button class='btn btn-danger btn-sm m-3' id='clear-all-btn'>Clear
                        All</button>
                    </div>
                    <div class='form-floating mb-3'>
                        <input type='text' class='form-control' id='link' name='link' placeholder='name@example.com'>
                        <label for='link'>Link</label>
                    </div>
                    <div class='form-floating mb-5'>
                        <input type='date' class='form-control' id='deadline' name='deadline' placeholder='deadline' required>
                        <label for='deadline'>deadline</label>
                    </div>

                    <div class='d-grid mb-4'>
                        <button class='btn btn-primary p-2 text-uppercase fw-bold' type='submit'>Register</button>
                    </div>
                    </form>";
                ?>
            </div>
        </div>
        </div>
    </main>
    <script src='js/index.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <script>
        let tomorrow = new Date((new Date().getTime()) + 86400000);
        let dd = tomorrow.getDate();
        let mm = tomorrow.getMonth() + 1;
        let yyyy = tomorrow.getFullYear();

        if (dd < 10) {
            dd = '0' + dd;
        }

        if (mm < 10) {
            mm = '0' + mm;
        }

        tomorrow = yyyy + '-' + mm + '-' + dd;
        document.getElementById("deadline").setAttribute("min", tomorrow);

        document.getElementById('select-all-btn').onclick = (e) => {
            e.preventDefault();
            $courses = document.querySelectorAll('#courses input');

            $courses.forEach($c => {
                $c.checked = true;
            });
        }

        document.getElementById('clear-all-btn').onclick = (e) => {
            e.preventDefault();
            $courses = document.querySelectorAll('#courses input');

            $courses.forEach($c => {
                $c.checked = false;
            });
        }
    </script>
</body>

</html>