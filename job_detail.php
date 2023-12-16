<?php
include_once('database/config.php');
date_default_timezone_set('Asia/Kolkata');

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $db_password);
    $cmp_id = $_GET['cmp_id'];
    $title = $_GET['title'];
    $sql = "SELECT * FROM job_role WHERE company_id='$cmp_id' and title='$title'";
    $res = $pdo->query($sql);
    $res = $res->fetch(PDO::FETCH_ASSOC);
    $sql2 = "SELECT name FROM company WHERE id='$cmp_id'";
    $res2 = $pdo->query($sql2);
    $res2 = $res2->fetch(PDO::FETCH_ASSOC);

    if (!$res || !$res2)
        die("Wrong company id or job title in parameters");

    $company = $res2['name'];
    $salary = $res['salary'];
    $num_vacancies = $res['num_vacancies'];
    $tenth_percentage = $res['tenth_percentage'];
    $twelth_percentage = $res['twelth_percentage'];
    $ug_cpi = $res['ug_cpi'];
    $pg_cpi = $res['pg_cpi'];
    $phd_cpi = $res['phd_cpi'];
    $link = $res['link'];
    $deadline = $res['deadline'];


    $sql = "SELECT c.name 
			FROM allowed_courses a,course c
            WHERE a.course_id=c.id and a.company_id='$cmp_id' and job_title='$title'";

    $statement = $pdo->query($sql);
    $res = $statement->fetchAll(PDO::FETCH_ASSOC);
    $courses = array();

    foreach ($res as $item) {
        array_push($courses, $item['name']);
    }

} catch (PDOException $e) {
    die("An Error Occurred!");
}

$pdo = null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Profile Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="css/sidebar.css">

</head>

<body
    style='background-color: #eee; background-image: url("images/iit1.jpg"); background-size: cover; background-repeat: no-repeat; background-attachment: fixed'>
    <div style="background-color: rgba(0,0,0,0.5);">
        <main class="container">
            <div class="row justify-content-center align-items-center">
                <div class='col m-2 d-flex align-items-center justify-content-center '>
                    <div class='bg-white shadow' style='min-width: 80%'>
                        <h1 class='p-4   text-center h3 border-bottom'>Job Profile Details</h1>
                        <div class='container'>
                            <div class="row">
                                <div class="col-12 col-sm-6 p-4">
                                    <div class="fw-bold">Job Title</div>
                                    <div>
                                        <?php echo $title; ?>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 p-4">
                                    <div class="fw-bold">Company Name</div>
                                    <div>
                                        <?php echo $company; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6 p-4">
                                    <div class="fw-bold">Salary</div>
                                    <div>
                                        <?php echo $salary; ?>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 p-4">
                                    <div class="fw-bold">No. of Vacancies</div>
                                    <div>
                                        <?php echo $num_vacancies; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6 p-4">
                                    <div class="fw-bold">10th Percentage Required</div>
                                    <div>
                                        <?php echo $tenth_percentage; ?>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 p-4">
                                    <div class="fw-bold">12th Percentage Required</div>
                                    <div>
                                        <?php echo $twelth_percentage; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6 p-4">
                                    <div class="fw-bold">UG CPI Required</div>
                                    <div>
                                        <?php echo $ug_cpi; ?>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 p-4">
                                    <div class="fw-bold">PG CPI Required</div>
                                    <div>
                                        <?php echo $pg_cpi; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6 p-4">
                                    <div class="fw-bold">PHD CPI Required</div>
                                    <div>
                                        <?php echo $phd_cpi; ?>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 p-4">
                                    <div class="fw-bold">Link</div>
                                    <div>
                                        <?php echo $link; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6 p-4">
                                    <div class="fw-bold">deadline</div>
                                    <div>
                                        <?php echo $deadline; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6 p-4">
                                    <div class="fw-bold">Eligible Courses</div>
                                    <div>
                                        <?php
                                        echo "<ul>";
                                        foreach ($courses as $c) {
                                            echo "<li>$c</li>";
                                        }
                                        echo "</ul>"
                                            ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="js/sidebar.js"></script>
</body>

</html>