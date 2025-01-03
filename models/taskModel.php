<?php

namespace models;

use config\DB;

class TaskModel {

    // Helper function to convert display state to database state
    private function convertToDbState($state) {
        switch($state) {
            case 'To Do':
                return 'todo';
            case 'In Progress':
                return 'inprogress';
            case 'Review':
                return 'review';
            case 'Done':
                return 'done';
            default:
                return $state;
        }
    }

    // Create a new task
    public function createTask($name, $description, $project_id, $state, $tag, $deadline) {
        error_log("Creating task with state: " . $state);
        $dbState = $this->convertToDbState($state);
        error_log("Converted state to: " . $dbState);

        $query = "INSERT INTO tasks (name, description, project_id, state, tag, deadline) 
                  VALUES (:name, :description, :project_id, :state, :tag, :deadline)";
        $params = [
            'name' => $name,
            'description' => $description,
            'project_id' => $project_id,
            'state' => $dbState,
            'tag' => $tag,
            'deadline' => $deadline
        ];
        return DB::query($query, $params);
    }

    // Delete a task by ID
    public function deleteTask($id) {
        $query = "DELETE FROM tasks WHERE id = :id";
        $params = ['id' => $id];
        return DB::query($query, $params);
    }

    // Update a task by ID
    public function updateTask($id, $name, $description, $state, $tag, $deadline) {
        $dbState = $this->convertToDbState($state);
        
        $query = "UPDATE tasks 
                  SET name = :name, description = :description, state = :state, tag = :tag, deadline = :deadline 
                  WHERE id = :id";
        $params = [
            'id' => $id,
            'name' => $name,
            'description' => $description,
            'state' => $dbState,
            'tag' => $tag,
            'deadline' => $deadline
        ];
        return DB::query($query, $params);
    }

    // Get all tasks for a specific project
    public function getTasksByProject($projectId) {
        try {
            $query = "SELECT t.*, p.name as project_name 
                     FROM tasks t 
                     LEFT JOIN projects p ON t.project_id = p.id 
                     WHERE t.project_id = :project_id 
                     ORDER BY t.id DESC";
            $stmt = DB::query($query, ['project_id' => $projectId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Database error in getTasksByProject: " . $e->getMessage());
            throw new \Exception("Failed to fetch tasks for project");
        }
    }

    // Get all tasks
    public function getAllTasks() {
        try {
            $query = "SELECT t.*, p.name as project_name 
                     FROM tasks t 
                     LEFT JOIN projects p ON t.project_id = p.id 
                     ORDER BY t.id DESC";
            $stmt = DB::query($query);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Database error in getAllTasks: " . $e->getMessage());
            throw new \Exception("Failed to fetch tasks");
        }
    }

    // Get all tasks with a specific state
    public function getTasksByState($state) {
        $query = "SELECT * FROM tasks WHERE state = :state";
        $params = ['state' => $state];
        $result = DB::query($query, $params);
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Get all tasks with a specific tag
    public function getTasksByTag($tag) {
        $query = "SELECT * FROM tasks WHERE tag = :tag";
        $params = ['tag' => $tag];
        $result = DB::query($query, $params);
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Get all tasks by deadline
    public function getTasksByDeadline($deadline) {
        $query = "SELECT * FROM tasks WHERE deadline = :deadline";
        $params = ['deadline' => $deadline];
        $result = DB::query($query, $params);
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Get assigned users for a task
    public function getAssignedUsers($taskId) {
        try {
            $query = "SELECT u.id, u.username 
                     FROM users u 
                     INNER JOIN assigntasks ta ON u.id = ta.user_id 
                     WHERE ta.task_id = :task_id";
            $stmt = DB::query($query, ['task_id' => $taskId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Database error in getAssignedUsers: " . $e->getMessage());
            throw new \Exception("Failed to fetch assigned users");
        }
    }

    // Remove assignment
    public function removeAssignment($taskId, $userId) {
        try {
            $query = "DELETE FROM assigntasks 
                     WHERE task_id = :task_id AND user_id = :user_id";
            $params = [
                'task_id' => $taskId,
                'user_id' => $userId
            ];
            DB::query($query, $params);
            return true;
        } catch (\PDOException $e) {
            error_log("Database error in removeAssignment: " . $e->getMessage());
            throw new \Exception("Failed to remove assignment");
        }
    }

    // Assign a task to a user
    public function assignTask($taskId, $assignee) {
        try {
            // First check if the user exists
            $userQuery = "SELECT id FROM users WHERE username = :username";
            $userStmt = DB::query($userQuery, ['username' => $assignee]);
            $user = $userStmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user) {
                throw new \Exception('User not found');
            }

            // Check if assignment already exists
            $checkQuery = "SELECT COUNT(*) as count FROM assigntasks 
                         WHERE task_id = :task_id AND user_id = :user_id";
            $checkStmt = DB::query($checkQuery, [
                'task_id' => $taskId,
                'user_id' => $user['id']
            ]);
            $exists = $checkStmt->fetch(\PDO::FETCH_ASSOC)['count'] > 0;

            if ($exists) {
                throw new \Exception('User is already assigned to this task');
            }

            // Then assign the task
            $query = "INSERT INTO assigntasks (task_id, user_id) 
                     VALUES (:task_id, :user_id)";
            $params = [
                'task_id' => $taskId,
                'user_id' => $user['id']
            ];
            DB::query($query, $params);
            return true;
        } catch (\PDOException $e) {
            error_log("Database error in assignTask: " . $e->getMessage());
            throw new \Exception("Failed to assign task");
        }
    }
}

?>
