<?php

namespace Gabela\Controller\Tasks;

use Gabela\Core\AbstractController;

class TasksCreateController extends AbstractController
{
    public function create()
    {
       $this->getTemplate(TASKS_CREATEPAGE);
    }
}