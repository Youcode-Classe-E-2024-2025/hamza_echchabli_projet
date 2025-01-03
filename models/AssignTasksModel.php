<?php

namespace models;

use config\DB;

class AssignTasksModel {

    // Assign a task to a user
    public function assignTask($user_id, $task_id) {
        $query = "INSERT INTO assigntasks (user_id, task_id) VALUES (:user_id, :task_id)";
        $params = [
            'user_id' => $user_id,
            'task_id' => $task_id
        ];
        return DB::query($query, $params);
    }

    // Unassign a task by ID
    public function unassignTask($id) {
        $query = "DELETE FROM assigntasks WHERE id = :id";
        $params = ['id' => $id];
        return DB::query($query, $params);
    }

    // Get all assigned tasks for a specific user
    public function getAssignedTasksByUser($user_id) {
        $query = "SELECT * FROM assigntasks WHERE user_id = :user_id";
        $params = ['user_id' => $user_id];
        $result = DB::query($query, $params);
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Get all users assigned to a specific task
    public function getUsersAssignedToTask($task_id) {
        $query = "SELECT * FROM assigntasks WHERE task_id = :task_id";
        $params = ['task_id' => $task_id];
        $result = DB::query($query, $params);
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Check if a task is already assigned to a user
    public function isTaskAssignedToUser($user_id, $task_id) {
        $query = "SELECT COUNT(*) AS count FROM assigntasks WHERE user_id = :user_id AND task_id = :task_id";
        $params = [
            'user_id' => $user_id,
            'task_id' => $task_id
        ];
        $result = DB::query($query, $params);
        return $result->fetch(\PDO::FETCH_ASSOC)['count'] > 0;
    }

    // Get all task assignments
    public function getAllTaskAssignments() {
        $query = "SELECT * FROM assigntasks";
        $result = DB::query($query);
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }
}

?>

