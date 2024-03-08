<?php

/**
 * @package   Task Management
 * @author    Ntabethemba Ntshoza
 * @date      11-10-2023
 * @copyright Copyright © 2023 VMP By Maneza
 */


getRequired(USER_MODULE_MODEL);
getRequired(TASKS_MODEL);
getRequired(WEATHER_API);

use Gabela\Model\Task;
use Gabela\Users\Model\User;

// require 'models/User.php';

// Check if the user is logged in

$users = new User();
$task = new Task();

// Define your OpenWeatherMap API key and city
$config = getIncluded(WEB_CONFIGS);

if (isset($config['weather']['apikey'])) {
    $apiKey = $config['weather']['apikey'][0];
}

$city = $users->getWeatherCity();

$allUsers = $users->getAllUsers();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Add Tasks - VMP Tasks management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Responsive Minimal Bootstrap Theme">
    <meta name="keywords" content="responsive,minimal,bootstrap,theme">
    <meta name="author" content="">

    <!--[if lt IE 9]>
    <script src="assets/js/html5shiv.js"></script>
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
    <script src="assets/js/jquery.prettyPhoto.js"></script>
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

    <style>
        #editUser input {
            width: 50%;
        }
    </style>
</head>

<body>
    <div id="wrapper">

        <!-- header begin -->
        <header>

            <?php getRequired(NAVBAR_PARTIAL); ?>
            <?php getRequired(WEATHER_PARTIAL); ?>

            <ul class="crumb">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="<?= BASE_URL . 'tasks' ?>">Home</a>
                    </li>
                <?php elseif (!isset($_SESSION['user_id'])): ?>
                    <li><a href="<?= BASE_URL . '' ?>">Home</a>
                    </li>
                <?php endif; ?>
                <li class="sep">/</li>
                <li>Add Tasks</li>
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
                    <h2>Add Tasks</h2>
                </div>
                <hr class="blank">

                <!-- Task edit form -->
                <form class="form-horizontal col-md-6 col-md-offset-3" method="post"
                    action="<?= EXTENTION_PATH ?>/tasks-create-submit">
                    <div class="form-group">
                        <label for="title" class="col-sm-2">Title:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description" class="col-sm-2">Description:</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="description" name="description" rows="4"
                                required></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="due_date" class="col-sm-2" id="dueDate">Due Date:</label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control" id="due_date" name="due_date" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="completed" class="col-sm-2">Completed:</label>
                        <div class="col-sm-10">
                            <input type="checkbox" id="completed" name="completed">
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
                            <button type="submit" class="btn btn-primary" value="Post">Add Task</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- content close -->

    <?php getRequired(FOOTER_PARTIAL); ?>