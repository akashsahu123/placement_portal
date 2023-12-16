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
    $sql = "SELECT c.name, c.id, j.job_title, j.is_accepted FROM job_offer j,company c WHERE roll_no='$roll_no' and c.id = j.company_id";

    $q = $pdo->query($sql);
    $q->setFetchMode(PDO::FETCH_ASSOC);

    $job_offers = array();
    $accepted = false;

    while ($row = $q->fetch()) {
        $p = array();

        $p['company'] = $row['name'];
        $p['job_title'] = $row['job_title'];
        $p['cmp_id'] = $row['id'];

        switch ($row['is_accepted']) {
            case '0':
                $p['status'] = 'Rejected';
                break;

            case '1':
                $p['status'] = 'Accepted';
                $accepted = true;
                break;

            case '2':
                $p['status'] = 'Yet to Accept';
        }

        array_push($job_offers, $p);
    }

    $sql = "SELECT * FROM event_timings WHERE name='Accept Offer'";
    $res = $pdo->query($sql);
    $res = $res->fetch(PDO::FETCH_ASSOC);
    $accept_offer_start_time = $res['start_time'];
    $accept_offer_end_time = $res['end_time'];
} catch (PDOException $e) {
    die("Some Error Occured!");
}


if (isset($_GET['action']) && strtotime($accept_offer_start_time) <= time() && strtotime($accept_offer_end_time) >= time()) {
    $cmp_id = $_GET['cmp_id'];
    $role = $_GET['role'];
    $roll_no = $_SESSION['std_roll_no'];

    try {
        require('database/config.php');
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);
        $sql = "UPDATE job_offer SET is_accepted=1 WHERE company_id='$cmp_id' and job_title='$role' and roll_no='$roll_no'";
        $pdo->exec($sql);
        echo "<script>alert(`Offer Accepted Successfully`);window.location='std_job_offers.php';</script>";
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
    <title>Job Offers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel='stylesheet' href='css/sidebar.css'>
</head>

<body class="d-flex flex-row flex-shrink-0" style="background-color: #eee;">
    <?php
    include_once('components/std_sidebar.php');
    createStdSidebar('Job Offers');
    ?>
    <main class="flex-grow-1 d-flex flex-column" style="height:100vh; overflow:auto;">
        <?php include_once('components/header.php'); ?>
        <div class='flex-grow-1 m-2 d-flex align-items-center justify-content-center '>
            <div class='bg-white shadow overflow-auto container' style='min-width: 80%; width: auto'>
                <h1 class='p-4   text-center h3'>Job Offers</h1>
                <div class='row p-3 border-top'>
                    <div class='alert alert-secondary m-0'>
                        <?php
                        if (strtotime($accept_offer_start_time) > time()) {
                            echo "You can apply for jobs from $accept_offer_start_time";
                        } else if (strtotime($accept_offer_end_time) < time()) {
                            echo "Application window is closed. You can't apply for any job now.";
                        } else {
                            echo "Job Application window will close on: $accept_offer_end_time";
                        }
                        ?>
                    </div>
                </div>
                <div class='row  p-3'>
                    <div class=' alert alert-secondary m-0'>
                        <?php
                        if ($accepted)
                            echo "You have already accepted an offer, Now you can no more accept any offer";
                        else
                            echo "You can accept only one offer, then your profile will be locked.";
                        ?>
                    </div>
                </div>
                <?php

                if (count($job_offers) > 0) {
                    echo "<div class='row border-top border-bottom p-3 justify-content-between'>
                        <div class='col-auto align-self-center d-flex flex-row align-items-center justify-content-center'>
                            <input id='search' type='search' class='m-2 form-control' placeholder='Search...'
                                aria-label='Search'>
                        </div>
                    </div>";
                }
                ?>
                <?php
                if (count($job_offers) == 0) {
                    echo "<div class='row h4 px-3 py-5 border-top text-muted'><div class='text-center'>No Jobs  Offers Found.</div></div>";
                } else {
                    echo "
                    <div class='table-responsive'>
                    <table id='profiles_table' class='table text-muted table-hover my-3 align-middle text-center' style='font-size: 0.85rem'>
                        <thead class='text-dark table-light'>
                        <tr>
                            <th>ID</th>
                            <th>Company</th>
                            <th>Role</th>
                            <th>Details</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                    ";

                    $n = count($job_offers);

                    for ($i = 0; $i < $n; ++$i) {
                        $p = $job_offers[$i];
                        $id = $i + 1;
                        $company = $p['company'];
                        $cmp_id = $p['cmp_id'];
                        $role = $p['job_title'];
                        $status = $p['status'];

                        echo "
                        <tr>
                        <td>$id</td>
                        <td>$company</td>
                        <td>$role</td>
                        <td><a href='job_detail.php?cmp_id=$cmp_id&title=$role'>Details</a></td>
                        <td>$status</td>";

                        if (strtotime($accept_offer_start_time) <= time() && strtotime($accept_offer_end_time) >= time() && !$accepted && $status != 'Accepted')
                            echo "<td><a class='btn btn-primary btn-sm' href='std_job_offers.php?action=accept_job_offer&cmp_id=$cmp_id&role=$role'>Accept</a></td>";
                        else
                            echo "<td></td>";

                        echo "
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
                if (value == '' || t.children[1].innerText.toLowerCase().includes(value) || t.children[2].innerText.toLowerCase().includes(value) || t.children[4].innerText.toLowerCase().includes(value))
                    t.style.display = 'table-row';
                else {
                    t.style.display = 'none';
                }
            });
        }

    </script>
</body>

</html>