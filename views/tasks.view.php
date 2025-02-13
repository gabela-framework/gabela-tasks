<?php

/**
 * @package   Task Management
 * @author    Ntabethemba Ntshoza
 * @date      11-10-2023
 * @copyright Copyright © 2023 VMP By Maneza
 *  
 */
getIncluded(WEATHER_API);

use Gabela\Core\Session;
use Gabela\Tasks\Model\Task;
use Gabela\Users\Model\User;

/**
 * Controller variables
 * 
 * @var Task $taskModel
 * @var User $userModel
 * @var array $sessionMessages
 * @var string $tableHtml
 * @var string $formHtml
 */
?>

<!DOCTYPE html>
<html lang="en">

<?php getRequired(PAGE_HEAD); ?>

<body>
    <div id="wrapper">

        <!-- header begin -->
        <header>
            <div class="info">
                <div class="container">
                    <div class="row">
                        <div class="span6 info-text">
                            <strong>Phone:</strong> (111) 333 7777 <span
                                class="separator"></span><strong>Email:</strong> <a href="#">
                                <?php printValue(Session::getCurrentUserEmail()) ?>
                            </a>
                        </div>
                        <div class="span6 text-right">
                            <div class="social-icons">
                                <a class="social-icon sb-icon-facebook" href="#"></a>
                                <a class="social-icon sb-icon-twitter" href="#"></a>
                                <a class="social-icon sb-icon-rss" href="#"></a>
                                <a class="social-icon sb-icon-dribbble" href="#"></a>
                                <a class="social-icon sb-icon-linkedin" href="#"></a>
                                <a class="social-icon sb-icon-flickr" href="#"></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php getIncluded(NAVBAR_PARTIAL); ?>
            <?php getIncluded(WEATHER_PARTIAL); ?>

            <ul class="crumb">
                <li><a href="<?= BASE_URL . 'tasks' ?>">Home</a></li>
                <li class="sep">/</li>
                <li>Tasks</li>
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
                    <h2>Tasks</h2>
                </div>
                <hr class="blank">


                <?php foreach ($sessionMessages as $type => $message): ?>
                    <div class="alert alert-<?= strpos($type, 'error') !== false ? 'danger' : 'success'; ?>">
                        <?= $message; ?>
                    </div>
                <?php endforeach; ?>


                <?php
                // Check if the delete_success query parameter is set
                if (isset($_GET['delete_success']) && $_GET['delete_success'] == 1) {
                    echo '<div class="alert alert-success">Task ' . $taskModel->getId() . ' deleted successfully!</div>';
                }
                ?>

                <?php

                // Check if there are no tasks, and display the "Create Task" button if true
                if (empty($tableHtml)): 
                ?>
                           <div class="modal fade" id="taskModal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="taskModalLabel">Add Task2</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Rendered Form -->
                                        <?= $formHtml; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>

                    <div class="container">

                        <div class="text-start" style="float: right;">
                            <?php printValue('<a href="#" class="btn btn-primary" data-toggle="modal" data-target="#taskModal">Add Task</a>'); ?>
                        </div>
                        <h1>Tasks List</h1>

                        <!-- Render the table -->
                        <?php if (!empty($tableHtml)): ?>
                            <?= $tableHtml; ?>
                        <?php else: ?>
                            <p>No tasks available.</p>
                        <?php endif; ?>

                        <!-- Task form -->
                        <div class="modal fade" id="taskModal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="taskModalLabel">Add Task2</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Rendered Form -->
                                        <?= $formHtml; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- JavaScript function to confirm and delete the task -->
                    <script>
                        function deleteTask(userId) {
                            Swal.fire({
                                title: 'Are you sure?',
                                text: 'You won\'t be able to revert this!',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, delete it!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // If user clicks "Yes," redirect to delete URL
                                    window.location.href = "<?= EXTENTION_PATH ?>/task-delete?id=" + userId;
                                }
                            });
                        }

                        function logoutNow() {
                            Swal.fire({
                                title: 'Are you sure?',
                                text: 'You want to logout?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, logout!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // If user clicks "Yes," redirect to delete URL
                                    window.location.href = "<?= EXTENTION_PATH ?>/logout";
                                }
                            });
                        }

                        function editTask(userId) {
                            Swal.fire({
                                title: 'Are you sure?',
                                text: 'You are about to edit this task.',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, edit it!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // If user clicks "Yes," redirect to edit URL
                                    window.location.href = "<?= EXTENTION_PATH ?>/task-edit?id=" + userId;
                                }
                            });
                        }
                    </script>
                <?php
                endif // End of else block
                ?>

                <div class="map">
                </div>
            </div>
        </div>
    </section>
    <!-- content close -->

    <?php getIncluded(FOOTER_PARTIAL); ?>