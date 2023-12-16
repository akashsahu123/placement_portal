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
    $sql = "SELECT * FROM student WHERE is_verified=0";
    $q = $pdo->query($sql);
    $q->setFetchMode(PDO::FETCH_ASSOC);

    $students = array();

    while ($row = $q->fetch()) {
        $p = array();
        $p['roll_no'] = $row['roll_no'];
        $p['name'] = $row['name'];
        $p['email'] = $row['email'];
        array_push($students, $p);
    }
    $pdo = null;
} catch (PDOException $e) {
    $pdo = null;
    die("Some Error Occured!");
}

if (isset($_GET['action'])) {
    $roll_no = $_GET['roll_no'];
    $action = $_GET['action'];

    try {
        require('database/config.php');
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);

        if ($action === 'verify') {
            $sql = "UPDATE student SET is_verified=1 WHERE roll_no='$roll_no'";
            $pdo->exec($sql);
            echo "<script>alert(`Verified Successfully`);window.location='admin_verify_students.php';</script>";
        } else {
            $sql = "DELETE FROM student WHERE roll_no='$roll_no'";
            $pdo->exec($sql);
            echo "<script>alert(`Rejected Successfully`);window.location='admin_verify_students.php';</script>";
        }

        $pdo = null;
        exit();
    } catch (PDOException $e) {
        die("Some Error Occured!");
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel='stylesheet' href='css/sidebar.css'>
</head>

<body class="d-flex flex-row flex-shrink-0" style="background-color: #eee;">
    <?php
    include_once('components/admin_sidebar.php');
    createStdSidebar('Verify Students');
    ?>
    <main class="flex-grow-1 d-flex flex-column" style="height:100vh; overflow:auto;">
        <?php include_once('components/header.php'); ?>
        <div class='flex-grow-1 m-2 d-flex align-items-center justify-content-center '>
            <div class='bg-white shadow overflow-auto container' style='min-width: 80%; width: auto'>
                <h1 class='p-4 text-center h3 m-0 border-bottom'>Verify Students</h1>
                <?php

                if (count($students) == 0) {
                    echo "<div class='h4 px-3 py-5 text-center text-muted border-top'>No Students Found for Verification.</div>";
                } else {
                    echo "
                    <div class='table-responsive'>
                    <table id='applications_table' class='table text-muted table-hover my-3 align-middle text-center' style='font-size: 0.85rem'>
                        <thead class='text-dark table-light'>
                        <tr>
                            <th>Roll No.</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                    ";

                    $n = count($students);

                    for ($i = 0; $i < $n; ++$i) {
                        $p = $students[$i];
                        $roll_no = $p['roll_no'];
                        $name = $p['name'];
                        $email = $p['email'];

                        echo "
                            <tr>
                            <td>$roll_no</td>
                            <td>$name</td>
                            <td>$email</td>
                            <td><button class='btn btn-success btn-sm me-2' onclick='window.location=`admin_verify_students.php?action=verify&roll_no=$roll_no`'>Verify</button><button class='btn btn-danger btn-sm' onclick='window.location=`admin_verify_students.php?action=reject&roll_no=$roll_no`'>Reject</button></td>";
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
    </main>
    <script src='js/index.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>

</html>