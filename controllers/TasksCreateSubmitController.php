<?php

namespace Gabela\Tasks\Controller;

getIncluded(TASK_MODEL);

use Gabela\Core\Session;
use Gabela\Tasks\Model\Task;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class TasksCreateSubmitController
{
    private $logger;

    /**
     * @var Session
     */
    private Session $customerSession;

    /**
     * @var Task
     */
    private Task $taskCollection;

    /**
     * Create task constructor
     *
     * @param Task $taskCollection
     * @param Session $customerSession
     */
    public function __construct(
        Task $taskCollection,
        Session $customerSession
    ) {
        $this->logger = new Logger('create-task-controller');
        $this->logger->pushHandler(new StreamHandler('var/System.log', Logger::DEBUG));
        $this->taskCollection = $taskCollection;
        $this->customerSession = $customerSession;
    }

    public function submit()
    {
        // Check if the user is logged in
        if (!$this->customerSession->getCurrentUserId()) {
            redirect("/tasks");
        }

        // Check if the form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Retrieve the user ID from your authentication system or form input
            if ($this->customerSession->getCurrentUserId()) {
                $userId = $this->customerSession->getCurrentUserId();
            }

            // Create a new Task object

            // Set task properties from form input
            $this->taskCollection->setTitle($_POST["title"]);
            $this->taskCollection->setDescription($_POST["description"]);
            $this->taskCollection->setDueDate($_POST["due_date"]);
            $this->taskCollection->setUserId($userId); // Set the user ID
            $this->taskCollection->setAssignedTo($_POST['assign_to']); // Set the assigned user's ID
            $this->taskCollection->setCompleted(isset($_POST["completed"]) ? 1 : 0);

            // Insert the task into the database
            if ($this->taskCollection->save()) {
                $this->logger->info('Task (' . $this->taskCollection->getTitle() . ') is created successfully ');
                // Redirect back to edit page with success message
                return redirect("/tasks");
            }
        }
    }
}
