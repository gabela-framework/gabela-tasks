<?php

namespace Gabela\Tasks\Model;

interface TaskInterface
{
    public function setId($id);

    public function getId();

    public function setTitle($title);

    public function getTitle();

    public function setDescription($description);

    public function getDescription();

    public function setDueDate($dueDate);

    public function getDueDate();

    public function setUserId($userId);

    public function getUserId();

    public function setCompleted($completed);

    public function getCompleted($completed);

    public function isCompleted();

    public function getAssignedTo();

    public function setAssignedTo($assign_to);

    public function save();

    public static function getAllTasks();

    public function update();

    public function delete($id);

    public static function getTaskById($id);
}
