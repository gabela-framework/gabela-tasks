<?php

getRequired(USER_MODEL);
getRequired(WEATHER_API);

use Gabela\Model\Task;
use Gabela\Model\User;
use Gabela\Core\ClassManager;

/**
 * @package   Task Management
 * @author    Ntabethemba Ntshoza
 * @date      11-10-2023
 * @copyright Copyright © 2023 VMP By Maneza
 */

$classManager = new ClassManager();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    redirect('/');
    // Terminate script execution
}

/** @var User $users */
// $users = $classManager->createInstance(User::class);

// $config = getIncluded(WEB_CONFIGS);

// if (isset($config['weather']['apikey']) && is_array($config['weather']['apikey'])) {
//     $apiKey = $config['weather']['apikey'][0];
// } else {
//     $apiKey = $config['weather']['apikey'];
// }

// $city = $users->getWeatherCity();

/** @var Task $taskClass */
$taskClass = $classManager->createInstance(Task::class);
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
                            <strong>Phone:</strong> (111) 333 7777 <span class="separator"></span><strong>Email:</strong> <a href="#">contact@example.com</a>
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

                <?php
                if (isset($_SESSION['registration_error'])) {
                    // Use SweetAlert to display the error message
                    echo '<script>
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: "' . $_SESSION['registration_error'] . '"
                            });
                        </script>';
                    // Clear the session variable
                    unset($_SESSION['registration_error']);
                }
                ?>

                <?php if (isset($_SESSION['registration_success'])) : ?>
                    <div class="alert alert-success">
                        <?php echo $_SESSION['registration_success']; ?>
                    </div>
                    <?php unset($_SESSION['registration_success']); // Clear the message after displaying
                    ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['login_success'])) : ?>
                    <div class="alert alert-success">
                        <?php echo $_SESSION['login_success']; ?>
                    </div>
                    <?php unset($_SESSION['login_success']); // Clear the message after displaying 
                    ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['task_saved'])) : ?>
                    <div class="alert alert-success">
                        <?php echo $_SESSION['task_saved']; ?>
                    </div>
                    <?php unset($_SESSION['task_saved']); // Clear the message after displaying 
                    ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['task_updated'])) : ?>
                    <div class="alert alert-success">
                        <?php echo $_SESSION['task_updated']; ?>
                    </div>
                    <?php unset($_SESSION['task_updated']); // 
                    ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['task_update_error'])) : ?>
                    <div class="alert alert-error">
                        <?php echo $_SESSION['task_update_error']; ?>
                    </div>
                    <?php unset($_SESSION['task_update_error']);
                    ?>
                <?php endif; ?>


                <?php
                // Check if the delete_success query parameter is set
                if (isset($_GET['delete_success']) && $_GET['delete_success'] == 1) {
                    echo '<div class="alert alert-success">Task ' . $taskClass->getId() . ' deleted successfully!</div>';
                }
                ?>

                <?php
                $alltasks = $taskClass->getAllTasks();
                // Check if there are no tasks, and display the "Create Task" button if true
                if (empty($alltasks)) {
                    // echo '<a  href="createTask.php" class="btn btn-primary">Create a Task</a>';
                } else {
                ?>

                    <table id="taskTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th data-orderable="false">Description</th>
                                <th>Assigned to</th>
                                <th>Due Date</th>
                                <th>Completed</th>
                                <th>Log by</th>
                                <th data-orderable="false">Edit</th>
                                <th data-orderable="false">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // var_dump($_SESSION);
                            $tasks = $taskClass->getAllTasks();
                            ?>
                            <!-- Loop through your tasks and display them as table rows -->
                            <?php foreach ($tasks as $task) : ?>
                                <tr>
                                    <td>
                                        <?php printValue($task->getId()); ?>
                                    </td>
                                    <td>
                                        <?php printValue($task->getTitle()); ?>
                                    </td>
                                    <td>
                                        <?php printValue($task->getDescription()); ?>
                                    </td>
                                    <td>
                                        <?php printValue($task->getAssignedTo()); ?>
                                    </td>
                                    <td>
                                        <?php printValue($task->getDueDate()); ?>
                                    </td>
                                    <td>
                                        <?php printValue($task->isCompleted() ? 'Yes' : 'No'); ?>
                                    </td>

                                    <td>
                                        <!-- Display user ID as a clickable link -->
                                        <a href="<?= EXTENTION_PATH ?>/users-profile?user_id=<?php printValue($task->getUserId()); ?>">
                                            <?php printValue($task->getUserId()); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <!-- Edit button -->
                                        <button onclick="editTask(<?php printValue($task->getId()); ?>)" class="btn btn-primary btn-sm">Edit</button>
                                    </td>
                                    <td>
                                        <!-- Delete button -->
                                        <button onclick="deleteTask(<?php printValue($task->getId()); ?>)" class="btn btn-danger btn-sm">Delete</button>
                                    </td>

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
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php
                } // End of else block
                printValue('<a  href=" ' . BASE_URL . 'tasks-create" class="btn btn-primary">Add Task</a>');
                ?>

                <div class="map">
                </div>
            </div>
        </div>
    </section>
    <!-- content close -->

    <?php getIncluded(FOOTER_PARTIAL); ?>