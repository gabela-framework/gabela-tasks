<?php

namespace Gabela\Tasks\Controller;

getIncluded(TASK_MODEL);
getIncluded(USER_MODULE_MODEL);

use Gabela\Core\AbstractController;
use Gabela\Tasks\Model\Task;
use Gabela\Users\Model\User;
use Gabela\Core\Session;

class TasksController extends AbstractController
{
    protected Task $taskModel;
    protected User $userModel;

    public function __construct(
        User $userModel,
        Task $taskModel
    ) {

        $this->userModel = $userModel;
        $this->taskModel = $taskModel;
    }

    public function tasks()
    {

        $data = [];

        if (!Session::isLoggedIn()) {
            redirect(EXTENTION_PATH . '/');
            return;
        }

        // Define the table headers
        //$headers = ['ID', 'Title', 'Description', 'AssignedTo', 'Due Date', 'Completed', 'Log by', 'Edit', 'Delete'];

        $sessionMessages = $this->getSessionMessages();

        $headers = [
            ['label' => 'ID', 'attributes' => ['style' => 'width: 1%;', 'class' => 'header-id']],
            ['label' => 'Title', 'attributes' => ['style' => 'width: 15em;', 'class' => 'header-title']],
            ['label' => 'Description', 'attributes' => ['style' => 'width: 20em;', 'class' => 'header-Description']],
            ['label' => 'AssignedTo', 'attributes' => ['style' => 'width: 1%;', 'class' => 'header-AssignedTo']],
            ['label' => 'Due Date', 'attributes' => ['style' => 'width: 5em;', 'class' => 'header-Date']],
            ['label' => 'Completed', 'attributes' => ['style' => 'width: 1%;', 'class' => 'header-Completed']],
            ['label' => 'Log by', 'attributes' => ['style' => 'width: 5em;', 'class' => 'header-logBy']],
            ['label' => 'Edit', 'attributes' => ['style' => 'width: 1%;', 'class' => 'header-edit']],
            ['label' => 'Delete', 'attributes' => ['style' => 'width: 1%;', 'class' => 'header-delete']],
        ];

        // Define the table options, like adding custom classes
        $options = [
            'class' => 'table table-striped table-bordered',
            'id' => 'taskTable',
        ];

        $tasks = $this->taskModel->getAll();
        foreach ($tasks as $task) {

            $userLogged = $this->userModel->getUserById($task->getUserId());
            $user = $this->userModel->findById($task->getUserId());
            $dueDate = date('d-m-Y', strtotime($task->getDueDate()));

            $linkUrl = EXTENTION_PATH . "/users-profile?user_id={$user['user_id']}";

            $data[] = [
                'ID' => $task->getId(),
                'Title' => $task->getTitle(),
                'Description' => $task->getDescription(),
                'AssignedTo' => $this->renderLink( $linkUrl, $task->getAssignedTo(), ['style' => 'test-align: center']),
                'Due Date' => $dueDate,
                'Completed' => $task->isCompleted() ? 'Yes' : 'No',
                'Log by' => "<a href=\"" . EXTENTION_PATH . "/users-profile?user_id={$task->getUserId()}\">{$userLogged['name']}</a>",
                'Edit' => $this->renderButton('Edit', "editTask({$task->getId()})", ['class' => 'btn btn-primary btn-sm']),
                'Delete' => $this->renderButton('Delete', "deleteTask({$task->getId()})", ['class' => 'btn btn-danger btn-sm']),
             ];
        }

        
        $tableHtml = $this->renderTable($data, $headers, $options);

        // Define form fields
        $fields = [
            ['type' => 'text', 'name' => 'title', 'label' => 'Title', 'value' => '', 'attributes' => ['class' => 'form-control', 'required' => 'required']],
            ['type' => 'textarea', 'name' => 'description', 'label' => 'Description', 'value' => '', 'attributes' => ['class' => 'form-control', 'rows' => 4, 'required' => 'required']],
            ['type' => 'date', 'name' => 'due_date', 'label' => 'Due Date', 'value' => '', 'attributes' => ['class' => 'form-control', 'required' => 'required']],
            ['type' => 'checkbox', 'name' => 'completed', 'label' => 'Completed', 'value' => '', 'attributes' => ['id' => 'completed']],
            ['type' => 'select', 'name' => 'assign_to', 'label' => 'Assigned To', 'options' => $this->getUserOptions(), 'attributes' => ['class' => 'form-control']],
        ];

        // Create the form with action and method
        $formHtml = $this->renderForm($fields, EXTENTION_PATH . '/tasks-create-submit', 'POST', true, ['class' => 'form-horizontal col-md-6 col-md-offset-3']);


        // Pass the generated HTML table to the view
        $this->renderTemplate(TASKS_HOMEPAGE, [
            'tableHtml' => $tableHtml,
            'formHtml' => $formHtml,
            'taskModel' => $this->taskModel,
            'userModel' => $this->userModel,
            'sessionMessages' => $sessionMessages,
        ]);
    }

    /**
     * Summary of user options for a select
     * 
     * @return array[]
     */
    private function getUserOptions()
    {
        $userNames = $this->userModel->getUsersFromDatabase();

        $options = [];
        foreach ($userNames as $userName) {
            $options[] = [
                'value' => $userName['user_id'],
                'label' => $userName['name']
            ];
        }

        return $options;
    }

    /**
     * Get all the session messages
     * 
     * @return array
     */
    protected function getSessionMessages()
    {
        $messages = [];
        $types = [
            'registration_error', 
            'registration_success', 
            'login_success', 
            'task_saved', 
            'task_updated', 
            'task_update_error'
        ];

        foreach ($types as $type) {
            if (isset($_SESSION[$type])) {
                $messages[$type] = $_SESSION[$type];
                unset($_SESSION[$type]);
            }
        }
        return $messages;
    }

    /**
     * Edit tasks
     * 
     * @return void
     */
    public function edit()
    {
        $this->getTemplate(TASKS_UPDATEPAGE);
    }
}
