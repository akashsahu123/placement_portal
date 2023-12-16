<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

if (!isset($_SESSION["cmp_loggedin"]) || $_SESSION["cmp_loggedin"] === false) {
    header("location: cmp_login.php");
    exit;
}

$cmp_id = $_SESSION['cmp_id'];

include('database/config.php');
$pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);
$sql = "SELECT s.name, s.email, s.roll_no, j.job_title FROM job_offer j, student s WHERE j.roll_no = s.roll_no && j.is_accepted=1";
$offers = array();

try {
    $q = $pdo->query($sql);
    $q->setFetchMode(PDO::FETCH_ASSOC);

    while ($row = $q->fetch()) {
        $tmp = array();
        $tmp['roll_no'] = $row['roll_no'];
        $tmp['job_title'] = $row['job_title'];
        $tmp['name'] = $row['name'];
        $tmp['email'] = $row['email'];
        array_push($offers, $tmp);
    }
} catch (PDOException $e) {
    die("An Error Occured");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accepted Offers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="css/sidebar.css">
</head>

<body class="d-flex" style="background-color: #eee;">
    <?php
    include_once('components/cmp_sidebar.php');
    createStdSidebar('Accepted Offers');
    ?>
    <main class="flex-grow-1 d-flex flex-column" style="height:100vh; overflow:auto;">
        <?php include_once('components/header.php'); ?>
        <div class='flex-grow-1 m-2 d-flex align-items-center justify-content-center overflow-auto'>
            <div class='bg-white shadow' style='min-width: 80%'>
                <h1 class='p-4   text-center h3 border-bottom'>Accepted Offers</h1>

                <?php

                if (count($offers) > 0) {
                    echo "<div class='row border-bottom p-3 justify-content-between'>
                            <div class='col-auto align-self-center d-flex flex-row align-items-center justify-content-center'>
                                <input id='search' type='search' class='m-2 form-control' placeholder='Search...'
                                    aria-label='Search'>
                            </div>
                          </div>";
                }

                if (count($offers) == 0)
                    echo "<div class='m-3  text-center text-muted'>No students have accepted any of your offers yet.</div>";
                else {
                    echo "<div class='table-responsive'>
                    <table id='offers_table' class='table text-muted table-hover my-3 align-middle text-center' style='font-size: 0.85rem'>
                    <thead class='text-dark table-light'>
                    <tr>
                        <th>Roll No.</th>
                        <th>name</th>
                        <th>Job Role</th>
                        <th>Email</th>
                    </tr>
                    </thead>
                    <tbody>";

                    foreach ($offers as $s) {
                        echo "<tr>
                        <td>" . $s['roll_no'] . "</td>
                        <td>" . $s['name'] . "</td>
                        <td>" . $s['job_title'] . "</td>
                        <td>" . $s['email'] . "</td>";
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

            let elems = document.querySelectorAll('#offers_table tbody tr');

            elems.forEach((t) => {
                if (value == '' || t.children[0].innerText.toLowerCase().includes(value) || t.children[1].innerText.toLowerCase().includes(value) || t.children[2].innerText.toLowerCase().includes(value) || t.children[3].innerText.toLowerCase().includes(value))
                    t.style.display = 'table-row';
                else {
                    t.style.display = 'none';
                }
            });
        }

    </script>
</body>

</html>