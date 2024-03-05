<?php

/**
 * @package   Task Management
 * @author    Ntabethemba Ntshoza
 * @date      11-10-2023
 * @copyright Copyright © 2023 VMP By Maneza
 */

namespace Gabela\Tasks\Model;

use mysqli;
use Gabela\Core\Database;
use Monolog\Logger;
use Gabela\Model\TaskInterface;
use InvalidArgumentException;
use Monolog\Handler\StreamHandler;

class Task implements TaskInterface
{
    private $id;
    private $title;
    private $description;
    private $dueDate;
    private $userId;
    private $completed;
    private $assign_to;

    private $db;
    private $logger;

    public function __construct(mysqli $db = null) {
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
        $this->completed = (bool)$completed;
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
     * @return bool
     */
    public function save()
    {
        // Prepare the SQL statement
        $sql = "INSERT INTO tasks (title, description, assign_to, due_date, user_id, completed) 
        VALUES (?, ?, ?, ?, ?, ?)";

        // You should adjust this logic based on your actual application flow.
        $userId = null; // Initialize user ID as null

        if (isset($_POST["id"])) {
            $userId = $_POST["id"];
        }

        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
        }

        // Bind parameters and execute the query
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssssii", $this->title, $this->description, $this->assign_to, $this->dueDate, $userId, $this->completed);

        if ($stmt->execute()) {

            $_SESSION['task_saved'] = "Task: ( {$this->title} ) Saved successfully";

            return true; // 
        } else {
            return false; // Task could not be saved
        }
    }

    /**
     * Get All Tasks
     *
     * @return array
     */
    public static function getAllTasks()
    {
        $db = Database::connect();
        
        // Perform a query to fetch tasks from the database
        $sql = "SELECT * FROM tasks";
        $result = $db->query($sql);

        $tasks = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $task = new Task();
                // $user = new User();
                $task->setId($row['id']);
                $task->setTitle($row['title']);
                $task->setDescription($row['description']);
                $task->setDueDate($row['due_date']);
                $task->setAssignedTo($row['assign_to']);
                $task->setUserId($row['user_id']);
                $task->setCompleted($row['completed']);
                $tasks[] = $task;
            }
        }

        return $tasks;
    }

    /**
     * Update Tasks
     *
     * @return void
     */
    public function update()
    {
        // Prepare the SQL statement
        $sql = "UPDATE tasks 
                SET title = ?, description = ?, assign_to = ?, due_date = ?, completed = ?
                WHERE id = ?";

        // Bind parameters and execute the query
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param(
            "ssssii",
            $this->title,
            $this->description,
            $this->assign_to,
            $this->dueDate,
            $this->completed,
            $this->id
        );

        if ($stmt->execute()) {
            $_SESSION['task_updated'] = "Task: ( {$this->title} ) Updated successfully";
            return true; // Task updated successfully
        } else {
            $_SESSION['task_update_error'] = "Task not update, check your function and try again";
            return false; // Task could not be updated
        }
    }

    /**
     * Delete Tasks
     *
     * @param [type] $id
     * @return boolean
     */
    public function delete($id)
    {
        // Prepare the SQL statement
        $sql = "DELETE FROM tasks WHERE id = ?";

        // Bind parameters and execute the query
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $this->logger->info('Task is deleted successfully ');
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get Tasks by ID
     *
     * @param mixed $id
     * 
     */
    public static function getTaskById($id)
    {
        $db = Database::connect();
        // Prepare the SQL statement
        $sql = "SELECT * FROM tasks WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            // Fetch the result as an associative array
            $result = $stmt->get_result();
            if ($result->num_rows === 1) {
                $taskData = $result->fetch_assoc();
                $task = new Task();
                $task->setId($taskData['id']);
                $task->setTitle($taskData['title']);
                $task->setDescription($taskData['description']);
                $task->setAssignedTo($taskData['assign_to']);
                $task->setDueDate($taskData['due_date']);
                $task->setUserId($taskData['user_id']);
                $task->setCompleted($taskData['completed']);
                return $task;
            } else {
                return null; // Task with the given ID not found
            }
        } else {
            return null; // Error in executing the query
        }
    }
}
