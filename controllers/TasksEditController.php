<?php

namespace Gabela\Tasks\Controller;

use Gabela\Core\AbstractController;

class TasksEditController extends AbstractController
{
    public function edit()
    {
        $this->getTemplate(TASKS_UPDATEPAGE);
    }
}

