<?php

namespace Gabela\Tasks\Controller;

use Gabela\Model\Task;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class TasksDeleteController
{
    private $logger;

    public function __construct($logger = null)
    {
        $this->logger = new Logger('delete-task-controller');
        $this->logger->pushHandler(new StreamHandler('var/System.log', Logger::DEBUG));
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            $taskId = $_GET['id'];

            $task = new Task();

            if ($task->delete($taskId)) {
                return redirect('/tasks?delete_success=1');
            } else {
                printValue("Failed to delete the task.");
            }
        } else {
            printValue("Invalid request.");
        }
    }
}
