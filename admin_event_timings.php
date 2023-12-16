<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] === false) {
  header("location: admin_login.php");
  exit;
}

try {
  require('database/config.php');
  $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);
  $sql = "SELECT * FROM event_timings";
  $q = $pdo->query($sql);
  $q->setFetchMode(PDO::FETCH_ASSOC);

  $events = array();

  while ($row = $q->fetch()) {
    $p = array();
    $p['name'] = $row['name'];
    $p['start_time'] = $row['start_time'];
    $p['end_time'] = $row['end_time'];
    array_push($events, $p);
  }
  $pdo = null;
} catch (PDOException $e) {
  $pdo = null;
  die("Some Error Occured!");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST['event-name'];
  $start_time = $_POST['start-time'];
  $end_time = $_POST['end-time'];

  if ($start_time === '')
    $msg = 'Start Time is necessary';
  else if ($end_time === '')
    $msg = 'End Time is necessary';
  else if (strtotime($start_time) >= strtotime($end_time))
    $msg = 'End Time should be greater than Start Time.';
  else if (strtotime($start_time) < time())
    $msg = 'Start Time Should be greater than current time.';
  else {
    require('database/config.php');

    try {
      $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $sql = "UPDATE event_timings SET start_time='$start_time', end_time='$end_time' WHERE name='$name'";
      $conn->exec($sql);
      $msg = "Event '$name' updated successfully.";
    } catch (PDOException $e) {
      die("Some Error Occured!");
    }
  }

  echo "<script>alert(`$msg`);window.location='admin_event_timings.php'</script>";
  $conn = null;
  exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Event Timings</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel='stylesheet' href='css/sidebar.css'>
</head>

<body class="d-flex flex-row flex-shrink-0" style="background-color: #eee;">
  <?php
  include_once('components/admin_sidebar.php');
  createStdSidebar('Event Timings');
  ?>
  <main class="flex-grow-1 d-flex flex-column" style="height:100vh; overflow:auto;">
    <?php include_once('components/header.php'); ?>
    <div class='flex-grow-1 m-2 d-flex align-items-center justify-content-center '>
      <div class='bg-white shadow overflow-auto container' style='min-width: 80%; width: auto'>
        <h1 class='p-4 text-center h3 m-0 border-bottom'>Event Timings</h1>
        <?php

        if (count($events) == 0) {
          echo "<div class='h4 px-3 py-5 text-center text-muted border-top'>No Events Found.</div>";
        } else {
          echo "
                <div class='table-responsive'>
                <table id='applications_table' class='table text-muted table-hover my-3 align-middle text-center' style='font-size: 0.85rem'>
                    <thead class='text-dark table-light'>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                ";

          $n = count($events);

          for ($i = 0; $i < $n; ++$i) {
            $p = $events[$i];
            $id = $i + 1;
            $name = $p['name'];
            $start_time = $p['start_time'];
            $end_time = $p['end_time'];

            echo "
            <tr>
              <td>$id</td>
              <td>$name</td>
              <td>$start_time</td>
              <td>$end_time</td>";

            echo "<td><button type='button' class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#myModal' onclick='editEvent(`$name`)'>Edit</button></td>
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
            <h4 class="modal-title" id='event-name'>Edit Event</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <form method='post'>
              <input name='event-name' id='event-name2' value='' hidden>
              <div class="form-floating mb-3">
                <input type="datetime-local" class="form-control" id="start-time" placeholder="Start Time"
                  name="start-time" required>
                <label for="start-time">Start Time</label>
              </div>
              <div class="form-floating mb-3">
                <input type="datetime-local" class="form-control" id="end-time" placeholder="End Time" name="end-time"
                  required>
                <label for="end-time">End Time</label>
              </div>
              <div class="d-grid mb-4">
                <button class="btn btn-primary p-2 text-uppercase fw-bold" type="submit">Update</button>
              </div>
            </form>
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

    function editEvent(name) {
      document.getElementById('event-name').innerText = name;
      document.getElementById('event-name2').value = name;
    }  </script>
</body>

</html>