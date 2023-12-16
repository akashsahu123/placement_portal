<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

if (isset($_SESSION["admin_loggedin"]) && $_SESSION["admin_loggedin"] === true) {
    header("location: admin_home.php");
    exit;
}

function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_id = sanitize_input($_POST['admin_id']);
    $password = sanitize_input($_POST['password']);
    $error = "";

    if ($admin_id === '')
        $error = "Admin ID is Necessary";
    else if ($password === '')
        $error = "Password can't be empty.";
    else {
        include_once('database/config.php');

        try {
            $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);
            $sql = "SELECT count(*) FROM admin c WHERE id='$admin_id'";
            $res = $pdo->query($sql);
            $count = (int) $res->fetchColumn();

            if ($count == 0)
                $error = "User doesn't exist.";
            else {
                $sql = "SELECT c.password FROM admin c WHERE id='$admin_id'";
                $res = $pdo->query($sql);
                $res = $res->fetch(PDO::FETCH_ASSOC);

                $pswd2 = $res['password'];

                if (password_verify($password, $pswd2)) {
                    $_SESSION["admin_loggedin"] = true;
                    $_SESSION["admin_id"] = $admin_id;
                    header("location: admin_home.php");
                } else {
                    $error = "Wrong Passsword.";
                }
            }
        } catch (PDOException $e) {
            die("Some Error Occured!");
        }

        $pdo = null;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
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
                    <div class="h5 p-4 card-header text-center">ADMIN LOGIN</div>
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
                                <input type="number" min='0' class="form-control" id="admin_id" placeholder=" "
                                    name="admin_id" required>
                                <label for="admin_id">Admin Id</label>
                            </div>
                            <div class="form-floating mb-5">
                                <input type="password" class="form-control" id="password" placeholder="password"
                                    name="password" required>
                                <label for="password">Password</label>
                            </div>

                            <div class="d-grid mb-4">
                                <button class="btn btn-primary p-2 text-uppercase fw-bold" type="submit">Login</button>
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