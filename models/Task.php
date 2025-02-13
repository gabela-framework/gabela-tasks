<?php

/**
 * @package   Task Management
 * @author    Ntabethemba Ntshoza
 * @date      11-10-2023
 * @copyright Copyright © 2023 VMP By Maneza
 */

namespace Gabela\Tasks\Model;

getIncluded(TASK_MODEL_INFERFACE);

use PDO;
use mysqli;
use Monolog\Logger;
use Gabela\Core\Model;
use Gabela\Core\Database;
use InvalidArgumentException;
use Monolog\Handler\StreamHandler;
use Gabela\Tasks\Model\TaskInterface;

class Task extends Model
{
    private $id;
    private $title;
    private $description;
    private $dueDate;
    private $userId;
    private $completed;
    private $assign_to;


    protected $db;
    private $logger;
    protected $table = 'tasks';
    protected $primaryKey = 'task_id';

    /**
     * Task class constructor
     *
     * Initializes the Task model with a database connection and a logger.
     *
     * @param PDO|null $db Optional PDO database connection. If not provided, a new connection will be created.
     */
    public function __construct(PDO $db = null)
    {
        $this->db = Database::connect();
        $this->logger = new Logger('task-model');
        $this->logger->pushHandler(new StreamHandler('var/System.log', Logger::DEBUG));
    }

    /**
     * Set Id
     *
     * @param [type] $id
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setTitle($title)
    {
        // Validate and sanitize the title, e.g., ensure it's not empty
        $title = trim($title);
        if (!empty($title)) {
            $this->title = $title;
        } else {
            throw new InvalidArgumentException('Title cannot be empty');
        }
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setDescription($description)
    {
        //validation for the description if needed
        $this->description = trim($description); //remove empty spaces
        $this->description = strip_tags($description); //remove html tags;
        $this->description = stripslashes($description); //remove empty spaces;
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDueDate($dueDate)
    {
        if ($dueDate !== null) {
            $this->dueDate = $dueDate;
        }
    }

    public function getDueDate()
    {
        return $this->dueDate;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setCompleted($completed)
    {
        $this->completed = (bool) $completed;
    }

    public function getCompleted($completed)
    {
        return $this->completed;
    }

    public function isCompleted()
    {
        return $this->completed;
    }

    // Getter method for name
    public function getAssignedTo()
    {
        return $this->assign_to;
    }

    // Setter method for name
    public function setAssignedTo($assign_to)
    {
        $this->assign_to = $assign_to;
    }


    /**
     * Save New Task
     *
     * @return mixed
     */
    public function save()
    {
        // You should adjust this logic based on your actual application flow.
        $userId = null;

        if (isset($_POST["id"])) {
            $userId = $_POST["user_id"];
        }

        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
        }

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'user_id' => $userId,
            'assign_to' => $this->assign_to,
            'completed' => $this->completed,
            'due_date' => $this->dueDate,
            'status_id' => 1,
        ];

        if ($this->id) {

            $_SESSION['task_saved'] = "Task: ( {$this->title} ) Saved successfully";

            return $this->update($data, $this->id);
        } else {
            return $this->insert($data);
        }
    }
    
    /**
     * Retrieve all tasks from the database.
     *
     * This method fetches all task records from the database and returns them
     * as an array of task objects. It does not take any parameters and will
     * return an empty array if no tasks are found.
     *
     * @return array An array of task objects.
     */
    public function getAll()
{
    $tasks = $this->findAll();
    $taskList = [];

    if (!empty($tasks)) {
        // Loop through the tasks and create Task objects for each record
        foreach ($tasks as $row) {
            $task = new self();
            $task->getData($row);
            $taskList[] = $task;
        }
    }

    return $taskList;
}

    /**
     * Get Tasks by ID
     *
     * @param mixed $id
     *
     */
    public static function getById($id)
    {
        $task = new self();
        $taskData = $task->find($id);

        if ($taskData) {
            $task->getData($taskData);
        }

        return $task;
    }

    /**
     * Populates the Task object with data from an associative array.
     *
     * @param array $data An associative array containing task data with the following keys:
     *                    - 'task_id' (int): The ID of the task.
     *                    - 'title' (string): The title of the task.
     *                    - 'description' (string): A description of the task.
     *                    - 'due_date' (string): The due date of the task.
     *                    - 'assign_to' (int): The ID of the user to whom the task is assigned.
     *                    - 'user_id' (int): The ID of the user who created the task.
     *                    - 'completed' (bool): The completion status of the task.
     *
     * @return void
     */
    public function getData(&$data) {
        $this->setId($data['task_id']);
        $this->setTitle($data['title']);
        $this->setDescription($data['description']);
        $this->setDueDate($data['due_date']);
        $this->setAssignedTo($data['assign_to']);
        $this->setUserId($data['user_id']);
        $this->setCompleted($data['completed']);
    }
}
