<?php
date_default_timezone_set('Asia/Kolkata');
function sanitize_input($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function courses()
{
  require('database/config.php');

  try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);

    $sql = 'SELECT * FROM course';

    $statement = $pdo->query($sql);

    $res = $statement->fetchAll(PDO::FETCH_ASSOC);

    $ans = array();

    foreach ($res as $item) {
      $tmp = array($item['name'], $item['id'], $item['graduation_level']);
      array_push($ans, $tmp);
    }
    return $ans;
  } catch (PDOException $e) {
    $error = $e->getMessage();
  }

  $pdo = null;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $roll_no = sanitize_input($_POST['roll_no']);
  $fname = sanitize_input($_POST['fname']);
  $lname = sanitize_input($_POST['lname']);
  $program = sanitize_input($_POST['program']);


  try {
    require('database/config.php');
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);
    $sql = "SELECT graduation_level FROM course WHERE id='$program'";
    $res = $pdo->query($sql);
    $res = $res->fetch(PDO::FETCH_ASSOC);
    $grad = $res['graduation_level'];
  } catch (PDOException $e) {
    die("Error! " . $e->getMessage());
  }


  $ten_perc = floatval(sanitize_input($_POST['10th_perc']));
  $twelth_perc = floatval(sanitize_input($_POST['12th_perc']));
  $ug_cpi = sanitize_input($_POST['ug_cpi']);
  $pg_cpi = sanitize_input($_POST['pg_cpi']);
  $phd_cpi = sanitize_input($_POST['phd_cpi']);
  $email = sanitize_input($_POST['email']);
  $password = sanitize_input($_POST['password']);
  $error = "";


  if ($fname == '')
    $error = $error . "<li>First Name is Necessary.</li>";

  if ($program == '')
    $error = $error . "<li>Program can't be empty.</li>";

  if (!preg_match("/^[a-zA-Z ]*$/", $fname) || !preg_match("/^[a-zA-Z ]*$/", $lname))
    $error = $error . "<li>Only letters and spaces are allowed in name.</li>";

  if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    $error = $error . "<li>Invalid email format.</li>";

  if (($grad == 'ug' || $grad == 'pg' || $grad == 'phd') && $ug_cpi == '')
    $error = $error . "<li>UG CPI is Necessary.</li>";

  if (($grad == 'pg' || $grad == 'phd') && $pg_cpi == '')
    $error = $error . "<li>UG CPI is Necessary.</li>";

  if ($grad == 'phd' && $phd_cpi == '')
    $error = $error . "<li>PHD CPI is Necessary.</li>";

  if ($ten_perc < 0 || $ten_perc > 100 || $twelth_perc < 0 || $twelth_perc > 100 || $ug_cpi < 0 || $ug_cpi > 10 || $pg_cpi < 0 || $pg_cpi > 10 || $phd_cpi < 0 || $phd_cpi > 10) {
    $error = $error . "<li>Invalid Value for Grade or Percetage.</li>";
  }

  if (!preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password) || !preg_match("/[0-9]/", $password) || !preg_match("/[^\w]/", $password) || strlen($password) < 8) {
    $error = $error . "<li>Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.</li>";
  }

  if ($error != '')
    $error = "<ol>" . $error . "</ol>";


  if ($error == '') {
    require('database/config.php');

    try {
      $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $sql = "SELECT * FROM student WHERE roll_no='$roll_no'";
      $res = $conn->query($sql);
      $count = $res->rowCount();

      if ($count > 0)
        $error = "Roll No. Already Exists!";
      else {
        $password = password_hash($password, PASSWORD_DEFAULT);

        if ($pg_cpi == '')
          $pg_cpi = 'NULL';

        if ($phd_cpi == '')
          $phd_cpi = 'NULL';

        $sql = "INSERT INTO student VALUES ('$roll_no','$fname $lname', $ten_perc,$twelth_perc,$ug_cpi, $pg_cpi,$phd_cpi,'$program','$email','$password',0)";
        $conn->exec($sql);
        $success = "Your Request Submitted Successfully. Your Account will be created soon. Please check later.";
      }
    } catch (PDOException $e) {
      $error = $e->getMessage();
    }

    $conn = null;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Registeration</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <style>
    body::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
    }
  </style>
</head>

<body style="background: #eee url('images/iit3.jpg') no-repeat fixed; background-size: cover">
  <div class='container'>
    <div class="row align-items-center overflow-auto" style='height: 100vh;'>
      <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
        <div class="card border-0 shadow rounded-3 my-3">
          <div class="h5 p-4 card-header text-center">STUDENT REGISTERATION</div>
          <div class="card-body p-4 p-sm-5">
            <form method="post">
              <?php
              if (isset($error) && $error != '') {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                      <strong>Error!</strong> ' . $error . '
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
              }
              ?>
              <?php
              if (isset($success) && $success != '') {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                      <strong>Success!</strong> ' . $success . '
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
              }
              ?>
              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="fname" name="fname" placeholder=" " required>
                <label for="fname">First Name</label>
              </div>
              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="lname" name="lname" placeholder=" ">
                <label for="lname">Last Name</label>
              </div>

              <div class="form-floating mb-3">
                <select class="form-select" id="program" name='program' required onchange="changeProgram()">
                  <?php
                  $c = courses();

                  $arrlength = count($c);

                  for ($x = 0; $x < $arrlength; $x++) {
                    $id = $c[$x][1];
                    $name = $c[$x][0];
                    $graduation = $c[$x][2];
                    echo "<option value='$id' data-graduation='$graduation'>$name</option>";
                  }
                  ?>
                </select>
                <label for="program">Program</label>
              </div>

              <div class="form-floating mb-3">
                <input type="number" step="0.1" min="0" max="100" class="form-control" id="10th_perc" name="10th_perc"
                  placeholder=" " required>
                <label for="10th_perc">10th Percentage</label>
              </div>
              <div class="form-floating mb-3">
                <input type="number" step="0.1" min="0" max="100" class="form-control" id="12th_perc" name="12th_perc"
                  placeholder=" " required>
                <label for="12th_perc">12th Percentage</label>
              </div>

              <div class="form-floating mb-3">
                <input type="number" step="0.1" min="0" max="10" class="form-control" id="ug_cpi" name="ug_cpi"
                  placeholder=" " required>
                <label for="ug_cpi">UG CPI</label>
              </div>
              <div class="form-floating mb-3">
                <input type="number" step="0.1" min="0" max="10" class="form-control" id="pg_cpi" name="pg_cpi"
                  placeholder=" ">
                <label for="pg_cpi">PG CPI</label>
              </div>
              <div class="form-floating mb-3">
                <input type="number" step="0.1" min="0" max="10" class="form-control" id="phd_cpi" name="phd_cpi"
                  placeholder=" ">
                <label for="phd_cpi">PHD CPI</label>
              </div>
              <div class="form-floating mb-3">
                <input type="number" step="1" min="0" class="form-control" id="roll_no" name="roll_no" placeholder=" "
                  required>
                <label for="roll_no">Roll Number</label>
              </div>
              <div class="form-floating mb-3">
                <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com"
                  required>
                <label for="email">Email address</label>
              </div>
              <div class="form-floating mb-5">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password"
                  required>
                <label for="password">Password</label>
              </div>

              <div class="d-grid mb-4">
                <button class="btn btn-primary p-2 text-uppercase fw-bold" type="submit">Register</button>
              </div>

              <div class="text-center fs-6">
                Already have an account ? <a href="std_login.php">sign in</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"></script>
  <script>
    function changeProgram() {
      let ug = document.getElementById('ug_cpi').parentElement;
      let pg = document.getElementById('pg_cpi').parentElement;
      let phd = document.getElementById('phd_cpi').parentElement;

      let grad = document.querySelector('#program :checked').dataset.graduation;

      if (grad === 'ug') {
        ug.style.display = 'block';
        pg.style.display = 'none';
        phd.style.display = 'none';
      }
      else if (grad === 'pg') {
        ug.style.display = 'block';
        pg.style.display = 'block';
        phd.style.display = 'none';
      }
      else {
        ug.style.display = 'block';
        pg.style.display = 'block';
        phd.style.display = 'block';
      }
    }

    changeProgram();
  </script>
</body>

</html>