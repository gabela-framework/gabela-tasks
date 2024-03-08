<?php

namespace Gabela\Tasks\Controller;

use Gabela\Core\AbstractController;

class TasksController extends AbstractController
{
    public function tasks()
    {
        $this->getTemplate(TASKS_HOMEPAGE);
    }

    public function create()
    {
       $this->getTemplate(TASKS_CREATEPAGE);
    }

    public function edit()
    {
        $this->getTemplate(TASKS_UPDATEPAGE);
    }
}

