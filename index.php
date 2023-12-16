<?php
function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = sanitize_input($_POST['user']);

    if ($user == 'student')
        header("location: cmp_login.php");
    else if ($user == 'company')
        header("location: std_login.php");
    else
        header("location: admin_login.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Placement Portal</title>
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
                    <div class="h5 p-4 card-header text-center">PLACEMENT PORTAL</div>

                    <form method='post' class="card-body p-4 p-sm-5">
                        <div class="form-check mx-3 my-4">
                            <input type="radio" class="form-check-input" id="company" name="user" value="student"
                                checked>
                            <label class="form-check-label" for="company">Company</label>
                        </div>
                        <div class="form-check mx-3 my-4">
                            <input type="radio" class="form-check-input" id="student" name="user" value="company">
                            <label class="form-check-label" for="student">Student</label>
                        </div>
                        <div class="form-check mx-3 mt-4 mb-5">
                            <input type="radio" class="form-check-input" id="admin" name="user" value="admin">
                            <label class="form-check-label" for="student">Admin</label>
                        </div>
                        <div class="d-grid mb-4">
                            <button class="btn btn-primary p-2 text-uppercase fw-bold" type="submit">Proceed</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>

</html>