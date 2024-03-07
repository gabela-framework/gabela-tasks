<?php

namespace Gabela\Tasks\Controller;

getIncluded(TASK_MODEL);

use Gabela\Tasks\Model\Task;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class TasksDeleteController
{
    private $logger;

    /**
     * @var Task
     */
    private Task $taskCollection;

    public function __construct(Task $taskCollection)
    {
        $this->logger = new Logger('delete-task-controller');
        $this->logger->pushHandler(new StreamHandler('var/System.log', Logger::DEBUG));
        $this->taskCollection = $taskCollection;
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            $taskId = $_GET['id'];

            if ($this->taskCollection->delete($taskId)) {
                return redirect('/tasks?delete_success=1');
            } else {
                printValue("Failed to delete the task.");
            }
        } else {
            printValue("Invalid request.");
        }
    }
}
