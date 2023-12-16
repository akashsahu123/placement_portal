<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

if (!isset($_SESSION["std_loggedin"]) || $_SESSION["std_loggedin"] === false) {
  header("location: std_login.php");
  exit;
}


require('database/config.php');

try {
  $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);
  $roll_no = $_SESSION['std_roll_no'];
  $sql = "SELECT * FROM student WHERE roll_no='$roll_no'";
  $res = $pdo->query($sql);
  $res = $res->fetch(PDO::FETCH_ASSOC);
  $tenth_percentage = $res['tenth_percentage'];
  $twelth_percentage = $res['twelth_percentage'];
  $ug_cpi = $res['ug_cpi'];
  $pg_cpi = ($res['pg_cpi'] == '' ? 0 : $res['pg_cpi']);
  $phd_cpi = ($res['phd_cpi'] == '' ? 0 : $res['phd_cpi']);
  $course = $res['course'];

  $sql = "SELECT * 
  FROM job_role j, allowed_courses c, company cmp
  WHERE j.company_id = c.company_id and j.title = c.job_title and j.company_id = cmp.id
  and $ug_cpi>=j.ug_cpi and (j.pg_cpi=NULL or $pg_cpi>=j.pg_cpi) and (j.phd_cpi=NULL or $phd_cpi>=j.phd_cpi) and $tenth_percentage>=j.tenth_percentage
  and $twelth_percentage>=j.twelth_percentage and '$course'=c.course_id";

  $q = $pdo->query($sql);
  $q->setFetchMode(PDO::FETCH_ASSOC);

  $profiles = array();

  while ($row = $q->fetch()) {
    $p = array();

    $p['company'] = $row['name'];
    $p['role'] = $row['title'];
    $p['deadline'] = $row['deadline'];
    $p['status'] = getStatus($roll_no, $row['id'], $row['title']);
    $p['details'] = $row['link'];
    $p['cmp_id'] = $row['id'];
    array_push($profiles, $p);
  }

  $sql = "SELECT * FROM event_timings WHERE name='Apply for Jobs'";
  $res = $pdo->query($sql);
  $res = $res->fetch(PDO::FETCH_ASSOC);
  $apply_job_start_time = $res['start_time'];
  $apply_job_end_time = $res['end_time'];
} catch (PDOException $e) {
  die("Some Error Occured!");
}

function getStatus($roll_no, $company_id, $title)
{
  try {
    require('database/config.php');
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);
    $sql = "SELECT count(*) FROM job_apply WHERE roll_no='$roll_no' and company_id='$company_id' and job_title='$title'";
    $res = $pdo->query($sql);
    $count = (int) $res->fetchColumn();

    if ($count > 0)
      return 'applied';
    else
      return 'not applied';
  } catch (PDOException $e) {
    die("Some Error Occured");
  }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  require('database/config.php');

  if (isset($_POST['apply'])) {
    try {
      $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $password = password_hash($password, PASSWORD_DEFAULT);
      $sql = "INSERT INTO  VALUES ('$roll_no','$fname $lname', $ten_perc,$twelth_perc,$ug_cpi, $pg_cpi,$phd_cpi,'$program','$email','$password')";
      $conn->exec($sql);
      $success = "Registeration Successfull. <a href='std_login.php'>login</a>";
    } catch (PDOException $e) {
      echo ("Some Error Occured!");
    }

    $conn = null;
  }
}

if (isset($_GET['action']) && strtotime($apply_job_start_time) <= time() && strtotime($apply_job_end_time) >= time()) {

  $action = $_GET['action'];
  $cmp_id = $_GET['cmp_id'];
  $title = $_GET['title'];
  $roll_no = $_SESSION['std_roll_no'];

  if (isset($_GET['resume']))
    $resume_no = (int) $_GET['resume'];

  require('database/config.php');

  try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if ($action == 'apply') {

      $flag = false;

      foreach ($profiles as $p) {
        if ($p['cmp_id'] === $cmp_id && $p['role'] === $title) {
          $flag = true;
        }
      }

      $sql = "SELECT * FROM resume   WHERE roll_no='$roll_no' and resume_no=$resume_no";
      $res = $conn->query($sql);
      $count = $res->rowCount();

      if (!$flag || $count == 0)
        die("Invalid Request");

      $sql = "INSERT INTO job_apply VALUES ('$cmp_id','$title','$roll_no','$resume_no')";
      $msg = "Applied Successfully.";
    } else if ($action == 'withdraw') {
      $sql = "DELETE FROM job_apply WHERE roll_no='$roll_no' and company_id = '$cmp_id' and job_title = '$title';";
      $msg = "Withdrawed Successfully.";
    } else
      die("No action specified.");

    $conn->exec($sql);
    echo "<script>alert('$msg');window.location='std_job_apply.php'</script>";
    $conn = null;
    exit();
  } catch (PDOException $e) {
    $error = $e->getMessage();
    echo "<script>alert(`$error`);window.location='std_job_apply.php'</script>";
    $conn = null;
    exit();
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Apply for Jobs</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel='stylesheet' href='css/sidebar.css'>
</head>

<body class="d-flex flex-row flex-shrink-0" style="background-color: #eee;">
  <?php
  include_once('components/std_sidebar.php');
  createStdSidebar('Apply for Jobs');
  ?>
  <main class="flex-grow-1 d-flex flex-column" style="height:100vh; overflow:auto;">
    <?php include_once('components/header.php'); ?>
    <div class='flex-grow-1 m-2 d-flex align-items-center justify-content-center '>
      <div class='bg-white shadow overflow-auto container' style='min-width: 80%; width: auto'>
        <h1 class='p-4 text-center h3 m-0'>Job Applications</h1>
        <?php

        echo "<div class='row border-top p-4'><div class=' alert alert-secondary m-0'>";

        if (strtotime($apply_job_start_time) > time()) {
          echo "You can apply for jobs from $apply_job_start_time";
        } else if (strtotime($apply_job_end_time) < time()) {
          echo "Application window is closed. You can't apply for any job now.";
        } else {
          echo "Job Application window will close on: $apply_job_end_time";
        }

        echo "</div></div>";

        if (count($profiles) > 0) {
          echo "<div class='row border-top border-bottom p-3 justify-content-between'><div class='col-auto align-self-center d-flex flex-row align-items-center justify-content-center'>
                    <input id='search' type='search' class='m-2 form-control' placeholder='Search...' aria-label='Search'>
                  </div></div>";
        }

        if (count($profiles) == 0) {
          echo "<div class='row h4 px-3 py-5 justify-content-center text-muted border-top'><div class='col text-center'>No Jobs Found.</div></div>";
        } else {

          echo "
          <div class='table-responsive'>
          <table id='applications_table' class='table text-muted table-hover my-3 align-middle text-center' style='font-size: 0.85rem'>
            <thead class='text-dark table-light'>
              <tr>
                <th>ID</th>
                <th>Company</th>
                <th>Role</th>
                <th>Application Deadline</th>
                <th>Status</th>
                <th>Action</th>
                <th>View Details</th>
              </tr>
            </thead>
            <tbody>
          ";

          usort($profiles, function ($a, $b) {
            return strcmp($b['deadline'], $a['deadline']);
          });

          $n = count($profiles);

          for ($i = 0; $i < $n; ++$i) {
            $p = $profiles[$i];
            $id = $i + 1;
            $company = $p['company'];
            $role = $p['role'];
            $deadline = $p['deadline'];
            $status = $p['status'];
            $cmp_id = $p['cmp_id'];

            echo "
            <tr>
              <td>$id</td>
              <td>$company</td>
              <td>$role</td>
              <td>$deadline</td>";

            if ($status == 'applied')
              echo "<td>Applied</td>";
            else
              echo "<td>Not Applied</td>";



            if (
              $deadline >= date("Y-m-d") &&
              strtotime($apply_job_start_time) <= time() && strtotime($apply_job_end_time) >= time()
            ) {
              if ($status == "applied") {
                echo "<td><a href='std_job_apply.php?action=withdraw&cmp_id=$cmp_id&title=$role' class='btn btn-dark btn-sm'>Withdraw</a></td>";
              } else {
                echo "<td><button type='button' class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#myModal' onclick='applyJobHelper(`$cmp_id`,`$company`, `$role`)'>Apply</button></td>";
              }
            } else {
              echo "<td></td>";
            }

            echo "
              <td><a href='job_detail.php?cmp_id=$cmp_id&title=$role'>Details</a></td>
            </tr>
            ";
          }

          echo "
            </tbody>
            </table>
          </div>
          ";
        }
        ?>
      </div>
    </div>

    <!--modal-->
    <div class="modal" id="myModal">
      <div class="modal-dialog">
        <div class="modal-content">

          <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title">Apply</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <?php

            require('database/config.php');

            try {
              $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);
              $roll_no = $_SESSION['std_roll_no'];
              $sql = "SELECT * FROM resume WHERE roll_no='$roll_no'";
              $q = $pdo->query($sql);
              $q->setFetchMode(PDO::FETCH_ASSOC);
              $resumes = array();

              while ($row = $q->fetch()) {
                array_push($resumes, $row['resume_no']);
              }

              if (count($resumes) == 0) {
                echo "<span class='text-muted'>You don't have submitted any resume. Can't apply.</span>";
              } else {
                echo "<div class='text-muted'>Choose resume to apply to <span id='modal-company-name' class=''></span> for <span
                          id='modal-company-role' class=''></span>:
                      </div>";
                echo "<select id='select-resume' class='form-select my-3'>";

                foreach ($resumes as $p) {
                  echo "<option value='$p'>Resume $p</option>";
                }

                echo "</select>";

                echo "<button id='apply_link' href='' class='btn btn-primary my-3'>Apply</button>";
              }
            } catch (PDOException $e) {
              die("Some Error Occured!");
            }
            ?>
          </div>
        </div>
      </div>
    </div>

  </main>
  <script src='js/index.js'></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"></script>
  <script>
    let t = document.getElementById('apply_link');

    document.getElementById('select-resume').onchange = (e) => {
      t.dataset.resume = e.currentTarget.value;
    }

    function applyJobHelper(cmp_id, company, title) {

      t.dataset.cmp_id = cmp_id;
      t.dataset.title = title;
      t.dataset.resume = document.getElementById('select-resume').value;
      document.getElementById('modal-company-name').innerText = company;
      document.getElementById('modal-company-role').innerText = title;
    }

    t.onclick = () => {
      window.location = `std_job_apply.php?action=apply&cmp_id=${t.dataset.cmp_id}&title=${t.dataset.title}&resume=${t.dataset.resume}`;
    }


    document.getElementById('search').oninput = (e) => {
      let value = e.currentTarget.value.trim().toLowerCase();

      let elems = document.querySelectorAll('#applications_table tbody tr');

      elems.forEach(t => {
        if (value == '' || t.children[1].innerText.toLowerCase().includes(value) || t.children[2].innerText.toLowerCase().includes(value) || t.children[3].innerText.toLowerCase().includes(value) || t.children[4].innerText.toLowerCase().includes(value) || t.children[5].innerText.toLowerCase().includes(value))
          t.style.display = 'table-row';
        else {
          t.style.display = 'none';
        }
      })
    }
  </script>
</body>

</html>