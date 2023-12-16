<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

if (!isset($_SESSION["cmp_loggedin"]) || $_SESSION["cmp_loggedin"] === false) {
  header("location: cmp_login.php");
  exit;
}

$cmp_id = $_SESSION['cmp_id'];



try {
  require('database/config.php');
  $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);
  $sql = "SELECT title FROM job_role WHERE company_id='$cmp_id'";
  $profiles = array();
  $q = $pdo->query($sql);
  $q->setFetchMode(PDO::FETCH_ASSOC);

  while ($row = $q->fetch()) {
    $title = $row['title'];
    array_push($profiles, $title);
  }

  $sql = "SELECT * FROM event_timings WHERE name='Give Offer'";
  $res = $pdo->query($sql);
  $res = $res->fetch(PDO::FETCH_ASSOC);
  $give_offer_start_time = $res['start_time'];
  $give_offer_end_time = $res['end_time'];
} catch (PDOException $e) {
  die("An Error Occured");
}

$std_applications = array();

try {
  $sql = "SELECT s.name, s.roll_no, j.resume_no,j.job_title, s.email, c.name  as course, r.link FROM job_apply j, student s, course c, resume r 
  WHERE company_id='$cmp_id' and j.roll_no = s.roll_no and s.course=c.id and j.roll_no=r.roll_no and j.resume_no=r.resume_no";

  $q = $pdo->query($sql);
  $q->setFetchMode(PDO::FETCH_ASSOC);

  while ($row = $q->fetch()) {
    $tmp = array();
    $tmp['name'] = $row['name'];
    $tmp['roll_no'] = $row['roll_no'];
    $tmp['email'] = $row['email'];
    $tmp['course'] = $row['course'];
    $tmp['resume'] = $row['link'];
    $tmp['job_title'] = $row['job_title'];
    $tmp['job_offer'] = jobOffer($row['roll_no'], $cmp_id, $row['job_title']);
    array_push($std_applications, $tmp);
  }
} catch (PDOException $e) {
  die("An Error Occured");
}

function jobOffer($roll_no, $cmp_id, $job_title)
{
  try {
    include('database/config.php');
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);
    $sql = "SELECT * FROM job_offer WHERE roll_no='$roll_no' and company_id='$cmp_id' and job_title='$job_title'";
    $res = $pdo->query($sql);
    $count = $res->rowCount();

    if ($count > 0)
      return true;
    else
      return false;
  } catch (PDOException $e) {
    die("An Error Occured!");
  }
}

if (isset($_GET['action']) && strtotime($give_offer_start_time) <= time() && strtotime($give_offer_end_time) >= time()) {
  $action = $_GET['action'];
  $cmp_id = $_SESSION['cmp_id'];
  $title = $_GET['job_title'];
  $roll_no = $_GET['roll_no'];

  require('database/config.php');

  try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if ($action == 'give_offer') {
      $sql = "SELECT * FROM job_apply WHERE roll_no='$roll_no' and company_id='$cmp_id' and job_title='$title'";
      $res = $conn->query($sql);
      $count = $res->rowCount();

      if ($count === 0) {
        die("Invalid Request");
      }

      $sql = "INSERT INTO job_offer VALUES ('$cmp_id','$title','$roll_no',2)";
      $conn->exec($sql);
      $_SESSION['success'] = "Offer Given Successfully to '$roll_no' for '$title'.";
      $conn = null;
      header("location: cmp_std_applications.php");
      exit();
    } else if ($action == 'withdraw_offer') {
      $sql = "DELETE FROM job_offer WHERE roll_no='$roll_no' and company_id = '$cmp_id' and job_title = '$title';";
      $conn->exec($sql);
      $_SESSION['success'] = "Offer Withdrawed Successfully from '$roll_no' for '$title'.";
      $conn = null;
      header("location: cmp_std_applications.php");
      exit();
    } else
      die("No action specified.");
  } catch (PDOException $e) {
    die("An Error Occured.");
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Applications</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="css/sidebar.css">
</head>

<body class="d-flex" style="background-color: #eee;">
  <?php
  include_once('components/cmp_sidebar.php');
  createStdSidebar('Student Applications');
  ?>
  <main class="flex-grow-1 d-flex flex-column" style="height:100vh; overflow:auto;">
    <?php include_once('components/header.php'); ?>
    <div class='flex-grow-1 m-2 d-flex align-items-center justify-content-center overflow-auto'>
      <div class='bg-white shadow' style='min-width: 80%'>
        <h1 class='p-4   text-center h3 border-bottom'>Student Applications</h1>


        <?php
        if (isset($_SESSION['success']) && $_SESSION['success'] != '') {
          echo '<div class="m-4 alert alert-success alert-dismissible fade show" role="alert">
								  <strong>Success!</strong> ' . $_SESSION['success'] . '
								  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
								</div>';

          $_SESSION['success'] = '';
        }


        echo "<div class='alert alert-secondary m-4'>";
        if (strtotime($give_offer_start_time) > time()) {
          echo "You can give offers to students from $give_offer_start_time";
        } else if (strtotime($give_offer_end_time) < time()) {
          echo "Deadline is over. You can't give offers to any student now.";
        } else {
          echo "Deadline for Giving Offers to Students: $give_offer_end_time";
        }
        echo "</div>";

        if (count($std_applications) > 0) {
          echo "<div class='row p-3 justify-content-between'>
                  <div class='col-auto align-self-center d-flex flex-row align-items-center justify-content-center'>
                      <input id='search' type='search' class='m-2 form-control' placeholder='Search...'
                          aria-label='Search'>
                  </div>
                </div>";
        }

        if (count($profiles) == 0)
          echo "<div class='p-3 border-top text-muted text-center'>You don't have opened any job profiles yet. Create profiles to see student applications here.</div>";
        else if (count($std_applications) == 0)
          echo "<div class='p-3 border-top text-muted text-center'>No students have applied yet.</div>";
        else {
          echo "<div class='m-4 table-responsive'>
            <table id='applications_table' class='table text-muted table-hover align-middle text-center' style='font-size: 0.85rem'>
            <thead class='text-dark table-light'>
              <tr>
                <th>Job Role</th>
                <th>name</th>
                <th>Roll No.</th>
                <th>Email</th>
                <th>Course</th>
                <th>Resume</th>
                <th>Job Offer</th>
              </tr>
            </thead>
            <tbody>";

          foreach ($std_applications as $s) {
            echo "<tr>
              <td>" . $s['job_title'] . "</td>
              <td>" . $s['name'] . "</td>
              <td>" . $s['roll_no'] . "</td>
              <td>" . $s['email'] . "</td>
              <td>" . $s['course'] . "</td>
              <td><a href='" . $s['resume'] . "'>view</a></td>";

            if (strtotime($give_offer_start_time) <= time() && strtotime($give_offer_end_time) >= time()) {
              if ($s['job_offer']) {
                echo "<td><button onclick='window.location=`cmp_std_applications.php?action=withdraw_offer&roll_no=" . $s['roll_no'] . "&job_title=" . $s['job_title'] . "`'class='btn btn-dark btn-sm'>Withdraw</button></td>";
              } else {
                echo "<td><button onclick='window.location=`cmp_std_applications.php?action=give_offer&roll_no=" . $s['roll_no'] . "&job_title=" . $s['job_title'] . "`'class='btn btn-primary btn-sm'>Give Offer</button></td>";
              }
            } else {
              echo "<td></td>";
            }
            echo "</tr>";
          }

          echo "
              </tbody>
              </table>
            </div>";
        }
        ?>
      </div>
    </div>
  </main>
  <script src="js/index.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"></script>
  <script>
    document.getElementById('search').oninput = (e) => {
      let value = e.currentTarget.value.trim().toLowerCase();

      let elems = document.querySelectorAll('#applications_table tbody tr');

      elems.forEach((t) => {
        if (value == '' || t.children[0].innerText.toLowerCase().includes(value) || t.children[1].innerText.toLowerCase().includes(value) || t.children[2].innerText.toLowerCase().includes(value) || t.children[3].innerText.toLowerCase().includes(value) || t.children[4].innerText.toLowerCase().includes(value))
          t.style.display = 'table-row';
        else {
          t.style.display = 'none';
        }
      });
    }

  </script>
</body>

</html>