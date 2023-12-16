<?php

function sanitize_input($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $cmp_id = sanitize_input($_POST['cmp_id']);
  $name = sanitize_input($_POST['name']);
  $email = sanitize_input($_POST['email']);
  $password = sanitize_input($_POST['password']);
  $error = "";


  if (!isset($name) || $name == '')
    $error = $error . "<li>Name is Necessary.</li>";

  if (!isset($cmp_id) || $cmp_id == '')
    $error = $error . "<li>Name is Necessary.</li>";

  if (!preg_match("/^[a-zA-Z ]*$/", $name))
    $error = $error . "<li>Only letters and spaces are allowed in name.</li>";

  if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    $error = $error . "<li>Invalid email format.</li>";

  if (!preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password) || !preg_match("/[0-9]/", $password) || !preg_match("/[^\w]/", $password) || strlen($password) < 8) {
    $error = $error . "<li>Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.</li>";
  }

  if ($error != '')
    $error = "<ol>" . $error . "</ol>";


  if ($error == '') {
    include_once('database/config.php');

    try {
      $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $sql = "SELECT * FROM company WHERE id='$cmp_id'";
      $res = $conn->query($sql);
      $count = $res->rowCount();

      if ($count > 0)
        $error = "Company ID Already Exists!";
      else {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO company
		  VALUES ('$cmp_id','$name','$email','$password',0)";
        $conn->exec($sql);
        $success = "Your Request Submitted Successfully. Your Account will be created soon. Please check later.";
      }
    } catch (PDOException $e) {
      $error = 'Some Error Occured!';
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
  <title>Company Registeration</title>
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
      <div class="col-sm-9 col-md-7 col-lg-5 mx-auto ">
        <div class="card border-0 shadow rounded-3 my-3 align-self-center">
          <div class="h5 p-4 card-header text-center">COMPANY REGISTERATION
          </div>
          <div class="card-body p-4 p-sm-5">
            <form method='post'>
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
                <input type="text" class="form-control" id="cmp_id" name="cmp_id" placeholder=" " required>
                <label for="name">Company ID</label>
              </div>

              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="name" name="name" placeholder=" " required>
                <label for="name">Name</label>
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

              <div class="text-center text-muted fs-6">
                Already have an account ? <a href="cmp_login.php">sign in</a>
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
</body>

</html>