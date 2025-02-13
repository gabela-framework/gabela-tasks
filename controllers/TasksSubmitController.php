<?php

namespace Gabela\Tasks\Controller;

getIncluded(TASK_MODEL);

use Gabela\Tasks\Model\Task;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class TasksSubmitController
{
    private $logger;

    /**
     * @var Task
     */
    private Task $taskCollection;

    /**
     * Task Update constructor
     *
     * @param Task $taskCollection
     */
    public function __construct(Task $taskCollection)
    {
        $this->logger = new Logger('registration-controller');
        $this->logger->pushHandler(new StreamHandler('var/System.log', Logger::DEBUG));
        $this->taskCollection = $taskCollection;
    }

    public function submit()
    {
        if (!isset($_SESSION['user_id'])) {
            // Redirect to the index page
            redirect('/index');
        }


        // Check if the form is submitted
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Use setters to update task properties
            $this->taskCollection->setId($_POST['id']);
            $this->taskCollection->setTitle($_POST['title']);
            $this->taskCollection->setDescription($_POST['description']);
            $this->taskCollection->setDueDate($_POST['due_date']);
            $this->taskCollection->setCompleted(isset($_POST['completed']) ? 1 : 0);
            $this->taskCollection->setAssignedTo($_POST['assign_to']); // Set the assigned user's ID

            // Update the task in the database
            if ($this->taskCollection->save()) {
                $this->logger->info("Task {$this->taskCollection->getTitle()} is updated succesfully ");
                // Redirect back to edit page with success message
                return redirect("/tasks");
            }
        }
    }
}
