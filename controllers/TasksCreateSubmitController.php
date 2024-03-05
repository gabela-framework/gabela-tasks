<?php

namespace Gabela\Controller\Tasks;

use Gabela\Model\Task;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class TasksCreateSubmitController
{
    private $logger;

    public function __construct($logger = null)
    {
        $this->logger = new Logger('create-task-controller');
        $this->logger->pushHandler(new StreamHandler('var/System.log', Logger::DEBUG));
    }

    public function submit()
    {
        $task = new Task();
        // Check if the user is logged in
        if (!isset($_SESSION['user_id'])) {
            redirect("/tasks");
        }

        // Check if the form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Retrieve the user ID from your authentication system or form input
            if (isset($_SESSION['user_id'])) {
                $userId = $_SESSION['user_id'];
            }

            // Create a new Task object
            $task = new Task();

            // Set task properties from form input
            $task->setTitle($_POST["title"]);
            $task->setDescription($_POST["description"]);
            $task->setDueDate($_POST["due_date"]);
            $task->setUserId($userId); // Set the user ID
            $task->setAssignedTo($_POST['assign_to']); // Set the assigned user's ID

            $task->setCompleted(isset($_POST["completed"]) ? 1 : 0);

            // Insert the task into the database
            if ($task->save()) {
                $this->logger->info('Task (' . $task->getTitle() . ') is created successfully ');
                // Redirect back to edit page with success message
                return redirect("/tasks");
            }
        }
    }
}
