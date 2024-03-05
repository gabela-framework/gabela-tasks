<?php

namespace Gabela\Tasks\Controller;

use Gabela\Model\Task;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;


class TasksSubmitController
{
    private $logger;

    public function __construct($logger = null)
    {
        $this->logger = new Logger('registration-controller');
        $this->logger->pushHandler(new StreamHandler('var/System.log', Logger::DEBUG));
    }

    public function submit()
    {
        if (!isset($_SESSION['user_id'])) {
            // Redirect to the index page
            redirect('/index');
        }


        // Check if the form is submitted
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Create an instance of the Task class
            $task = new Task();

            // Use setters to update task properties
            $task->setId($_POST['id']);
            $task->setTitle($_POST['title']);
            $task->setDescription($_POST['description']);
            $task->setDueDate($_POST['due_date']);
            $task->setCompleted(isset($_POST['completed']) ? 1 : 0);
            $task->setAssignedTo($_POST['assign_to']); // Set the assigned user's ID

            // Update the task in the database
            if ($task->update()) {
                $this->logger->info("Task {$task->getTitle()} is updated succesfully ");
                // Redirect back to edit page with success message
                return redirect("/tasks");
            }
        }
    }
}
