<?php

/**
 * @package   Task Management
 * @author    Ntabethemba Ntshoza
 * @date      11-10-2023
 * @copyright Copyright © 2023 VMP By Maneza
 */

require __DIR__ . '/../../models/User.php';
require __DIR__ . '/../../models/Task.php';
require __DIR__ . '/../../helper/weather.php';

use Gabela\Model\Task;
use Gabela\Model\User;

// require 'models/User.php';

// Check if the user is logged in
$users = new User();
$task = new Task();



// Fetch the task details for the given ID
if (isset($_GET['id'])) {
    $taskId = $_GET['id'];

    // Create an instance of the Task class and fetch the task by ID
    $task = Task::getTaskById($taskId);

    $dueDate = date('Y-m-d', strtotime($task->getDueDate()));

    if (!$task) {
        // Handle the case where the task with the provided ID does not exist
        redirect("/tasks");
    }
} else {
    // Handle the case where ID is not provided, perhaps show an error message or redirect
    redirect("/tasks");
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Edit Task - VMP Tasks management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Responsive Minimal Bootstrap Theme">
    <meta name="keywords" content="responsive,minimal,bootstrap,theme">
    <meta name="author" content="">

    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <link rel="stylesheet" href="css/ie.css" type="text/css">
    <![endif]-->

    <!-- Include DataTables CSS and JavaScript
     =================================================-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

    <!-- CSS Files
    ================================================== -->
    <link rel="stylesheet" href="assets/css/main.css" type="text/css" id="main-css">
    <link rel="stylesheet" href="assets/includes/styles.css" type="text/css">

    <!-- Include SweetAlert2 CSS and JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <!-- Javascript Files
    ================================================== -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.isotope.min.js"></script>
    <!-- <script src="assets/js/jquery.prettyPhoto.js"></script> -->
    <script src="assets/js/easing.js"></script>
    <script src="assets/js/jquery.ui.totop.js"></script>
    <script src="assets/js/selectnav.js"></script>
    <script src="assets/js/ender.js"></script>
    <script src="assets/js/jquery.lazyload.js"></script>
    <script src="assets/js/jquery.flexslider-min.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="assets/js/contact.js"></script>
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div id="wrapper">

        <!-- header begin -->
        <header>
            <?php getRequired(NAVBAR_PARTIAL);?>
            <?php getRequired(WEATHER_PARTIAL);?>

            <ul class="crumb">
                <li><a href="<?=BASE_URL . 'tasks'?>">Home</a></li>
                <li class="sep">/</li>
                <li>Edit Tasks</li>
            </ul>
    </div>
    </div>
    </div>
    </div>
    <!-- subheader close -->

    <!-- services section begin -->
    <section id="services" data-speed="10" data-type="background">
        <div class="container">
            <div class="row">
                <div class="text-center">
                    <h2>Edit Tasks</h2>
                </div>
                <hr class="blank">

                <!-- Task edit form -->
                <form style="width: 75%;" id="editUser loginForm" method="post" action="<?=EXTENTION_PATH?>/task-edit-submit" class="form-horizontal">
                    <input type="hidden" name="id" value="<?php echo $task->getId(); ?>">

                    <div class="form-group">
                        <label for="title" class="col-sm-2">Title:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="title" name="title" value="<?php echo $task->getTitle(); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description" class="col-sm-2">Description:</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="description" name="description" rows="4" required><?php echo $task->getDescription(); ?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="due_date" class="col-sm-2 ">Due Date:</label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control" id="due_date" name="due_date" value="<?php echo $dueDate; ?>" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="completed" class="col-sm-2">Completed:</label>
                        <div class="col-sm-10">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="completed" name="completed" <?php echo $task->isCompleted() ? 'checked' : ''; ?>> Completed
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="assign_to" class="col-sm-2">Assigned To:</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="assign_to" name="assign_to">
                                <?php
// Assume you have a function to fetch user names from the database
$userNames = $users->getUsersFromDatabase();

foreach ($userNames as $userName) {
    echo '<option value="' . $userName['name'] . '">' . $userName['name'] . '</option>';
}
?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- JavaScript function to confirm and delete the task -->
    <script>
        function logoutNow() {
            if (confirm("Are you sure you want to logout?")) {
                // Redirect to logout
                window.location.href = "<?=EXTENTION_PATH?>/logout";
            }
        }
    </script>

    <!-- content close -->

    <?php require __DIR__ . '/../../views/partials/footer.php';?>