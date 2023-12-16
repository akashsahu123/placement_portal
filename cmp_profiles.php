<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

if (!isset($_SESSION["cmp_loggedin"]) || $_SESSION["cmp_loggedin"] === false) {
  header("location: cmp_login.php");
  exit;
}

$cmp_id = $_SESSION['cmp_id'];

include_once('database/config.php');
$pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);
$sql = "SELECT * FROM job_role WHERE company_id='$cmp_id'";

$profiles = array();

try {
  $q = $pdo->query($sql);
  $q->setFetchMode(PDO::FETCH_ASSOC);

  while ($row = $q->fetch()) {
    $title = $row['title'];
    $deadline = $row['deadline'];
    $link = $row['link'];
    $tmp = array('title' => $title, 'deadline' => $deadline, 'link' => $link);
    array_push($profiles, $tmp);
  }

  $sql = "SELECT * FROM event_timings WHERE name='Edit Job Profile'";
  $res = $pdo->query($sql);
  $res = $res->fetch(PDO::FETCH_ASSOC);
  $edit_profile_start_time = $res['start_time'];
  $edit_profile_end_time = $res['end_time'];
} catch (PDOException $e) {
  die("Some Error Occured!");
}

$pdo = null;

if (isset($_GET['action']) && strtotime($edit_profile_start_time) <= time() && strtotime($edit_profile_end_time) >= time()) {
  if ($_GET['action'] == 'job_delete') {
    $title = $_GET['title'];

    try {

      include_once('database/config.php');
      $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $sql = "DELETE FROM job_role WHERE company_id='$cmp_id' and title='$title'";
      $conn->exec($sql);
      $_SESSION['success'] = "Job Deleted Successfully!";
      $conn = null;
      header("location: cmp_profiles.php");
      exit();
    } catch (PDOException $e) {
      $_SESSION['error'] = "An Error Occurred";
      $conn = null;
      header("location: cmp_profiles.php");
      exit();
    }

  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Company Job Profiles</title>
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
        <h1 class='p-4   text-center h3 border-bottom'>Job Profiles</h1>
        <?php

        if (isset($_SESSION['error']) && $_SESSION['error'] != '') {
          echo '<div class="m-4 alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> ' . $_SESSION['error'] . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';

          $_SESSION['error'] = '';
        }

        if (isset($_SESSION['success']) && $_SESSION['success'] != '') {
          echo '<div class="m-4 alert alert-success alert-dismissible fade show" role="alert">
          <strong>Success!</strong> ' . $_SESSION['success'] . '
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';

          $_SESSION['success'] = '';
        }
        ?>

        <?php
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

        <div class="border-bottom container">
          <div class=' row justify-content-between align-items-center'>
            <button onclick='window.location=`cmp_create_job.php`;' class="col-auto btn btn-primary m-4" type='button' 
            <?php if (
							strtotime($edit_profile_start_time) > time() || strtotime($edit_profile_end_time) < time()
							) echo "disabled"; ?>>Create Job Profile</button>
            <input id='search' type="search" class="col-auto form-control m-4" style='width: 11rem'
              placeholder="Search..." aria-label="Search">
          </div>
        </div>
        <?php

        if (count($profiles) == 0) {
          echo "<div class='h4 px-3 py-5 text-center text-muted'>No Profiles Found.</div>";
        } else {
          echo "<div class='table-responsive'>
            <table id='profiles_table' class='table text-muted table-hover my-3 align-middle' style='font-size: 0.85rem'>
              <thead class='text-dark table-light'>
                <tr class='border-top border-dark text-center'>
                  <th>ID</th>
                  <th>Role</th>
                  <th>Deadline</th>
                  <th>Action</th>
                  <th>Links</th>
                  <th>Details</th>
                </tr>
              </thead>
              <tbody>";

          $i = 1;

          foreach ($profiles as $p) {
            $title = $p['title'];
            $deadline = $p['deadline'];
            $link = $p['link'];

            echo "
                    <tr class='text-center'>
                      <td>$i </td>
                      <td>$title</td>
                      <td>$deadline</td>
                      <td><a href='cmp_profiles.php?action=job_delete&title=$title' class='btn btn-sm btn-primary'>Delete</a></td>
                      <td>$link</td>
                      <td><a href='job_detail.php?cmp_id=$cmp_id&title=$title'>details</a></td>
                    </tr>";
            ++$i;
          }

          echo "</tbody>
            </table>
          </div>";
        }
        ?>

      </div>
    </div>
  </main>
  <script src='js/index.js'></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"></script>
  <script>
    document.getElementById('search').oninput = (e) => {
      let value = e.currentTarget.value.trim().toLowerCase();

      let elems = document.querySelectorAll('#profiles_table tbody tr');

      elems.forEach((t) => {
        if (value == '' || t.children[1].innerText.toLowerCase().includes(value) || t.children[2].innerText.toLowerCase().includes(value))
          t.style.display = 'table-row';
        else {
          t.style.display = 'none';
        }
      });
    }

  </script>
</body>

</html>