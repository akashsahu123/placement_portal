<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

if (!isset($_SESSION["cmp_loggedin"]) || $_SESSION["cmp_loggedin"] === false) {
  header("location: cmp_login.php");
  exit;
}

require('database/config.php');

try {
  $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);
  $cmp_id = $_SESSION['cmp_id'];
  $sql = "SELECT * FROM company WHERE id='$cmp_id'";
  $res = $pdo->query($sql);
  $res = $res->fetch(PDO::FETCH_ASSOC);
  $name = $res['name'];
  $email = $res['email'];
  $cmp_id = $res['id'];
} catch (PDOException $e) {
  die("Some Error Occured! ");
}

$pdo = null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Company Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel='stylesheet' href='css/sidebar.css'>
</head>

<body class="d-flex flex-row flex-shrink-0" style="background-color: #eee;">
  <?php
  include_once('components/cmp_sidebar.php');
  createStdSidebar('Home');
  ?>
  <main class="flex-grow-1 d-flex flex-column" style="height:100vh; overflow:auto;">
    <?php include_once('components/header.php'); ?>
    <div class='flex-grow-1 m-2 d-flex align-items-center justify-content-center '>
      <div class='bg-white shadow overflow-auto container p' style='min-width: 80%; width: auto'>
        <h1 class='p-4   text-center h3 border-bottom'>Your Profile</h1>
        <div class="row">
          <div class="col-sm-6 p-4">
            <div class="fw-bold">Name</div>
            <div>
              <?php echo $name; ?>
            </div>
          </div>
          <div class="col-sm-6 p-4">
            <div class="fw-bold">Company Id</div>
            <div>
              <?php echo $cmp_id; ?>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-6 p-4">
            <div class="fw-bold">Email</div>
            <div>
              <?php echo $email; ?>
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