<?php

namespace controllers;

use models\TaskModel;
use models\AssignTasksModel;

/**
 * TaskController handles task-related requests.
 */
class TaskController {
    private $taskModel;

    public function __construct() {
        $this->taskModel = new TaskModel();
    }

    /**
     * Handles incoming requests.
     */
    public function handleRequest() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Method Not Allowed');
            }

            $rawInput = file_get_contents('php://input');
            $requestData = json_decode($rawInput, true);

            if (!$requestData) {
                throw new \Exception('Invalid JSON in request body');
            }

            $action = $requestData['action'] ?? '';

            switch ($action) {
                case 'create':
                    $this->createTask($requestData);
                    break;
                case 'getTasksByProject':
                    $projectId = $requestData['project_id'] ?? null;
                    $this->getTasksByProject($projectId);
                    break;
                case 'getTasksByState':
                    $this->getTasksByState($requestData['state']);
                    break;
                case 'updateTask':
                    $this->updateTask($requestData);
                    break;
                case 'deleteTask':
                    $this->deleteTask($requestData['id']);
                    break;
                case 'assignTask':
                    $this->assignTask($requestData);
                    break;
                case 'getAssignedUsers':
                    $this->getAssignedUsers($requestData['task_id']);
                    break;
                case 'removeAssignment':
                    $this->removeAssignment($requestData);
                    break;
                default:
                    throw new \Exception('Invalid action');
            }
        } catch (\Exception $e) {
            error_log("Error in handleRequest: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Creates a new task.
     *
     * @param array $data Task data.
     */
    private function createTask($data) {
        error_log("Creating task with data: " . print_r($data, true));
        
        if (!isset($data['name'], $data['project_id'], $data['state'])) {
            throw new \Exception('Task name, project ID, and state are required.');
        }

        $result = $this->taskModel->createTask(
            $data['name'],
            $data['description'] ?? null,
            $data['project_id'],
            $data['state'],
            $data['tag'] ?? null,
            $data['deadline'] ?? null
        );

        if (!$result) {
            throw new \Exception('Failed to create task in database');
        }

        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Task created successfully'
        ]);
    }

    /**
     * Retrieves tasks by project ID.
     *
     * @param int $projectId Project ID.
     */
    private function getTasksByProject($projectId = null) {
        try {
            if ($projectId === null) {
                $tasks = $this->taskModel->getAllTasks();
            } else {
                $tasks = $this->taskModel->getTasksByProject($projectId);
            }

            // Get assigned users for each task
            foreach ($tasks as &$task) {
                $task['assignedUsers'] = $this->taskModel->getAssignedUsers($task['id']);
            }

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $tasks
            ]);
        } catch (\Exception $e) {
            error_log("Error in getTasksByProject: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Retrieves tasks by state.
     *
     * @param string $state Task state.
     */
    private function getTasksByState($state) {
        if (empty($state)) {
            throw new \Exception('State is required.');
        }

        $tasks = $this->taskModel->getTasksByState($state);

        http_response_code(200);
        echo json_encode([
            'success' => 'here',
            'data' => $tasks
        ]);
    }

    /**
     * Updates a task.
     *
     * @param array $data Task data.
     */
    private function updateTask($data) {
        try {
            if (!isset($data['id'])) {
                throw new \Exception('Task ID is required.');
            }

            // Prepare task data with default values
            $taskData = [
                'id' => $data['id'],
                'name' => $data['name'] ?? '',
                'description' => $data['description'] ?? '',
                'state' => $data['state'] ?? 'todo',
                'tag' => $data['tag'] ?? '',
                'deadline' => $data['deadline'] ?? null
            ];

            // Update the task
            $success = $this->taskModel->updateTask(
                $taskData['id'],
                $taskData['name'],
                $taskData['description'],
                $taskData['state'],
                $taskData['tag'],
                $taskData['deadline']
            );

            if (!$success) {
                throw new \Exception('Failed to update task.');
            }

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Task updated successfully'
            ]);
        } catch (\Exception $e) {
            error_log("Error in updateTask: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Deletes a task.
     *
     * @param int $taskId Task ID.
     */
    private function deleteTask($taskId) {
        if (empty($taskId)) {
            throw new \Exception('Task ID is required.');
        }

        $result = $this->taskModel->deleteTask($taskId);
        if (!$result) {
            throw new \Exception('Failed to delete task.');
        }

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Task deleted successfully'
        ]);
    }

    /**
     * Assigns a task to an assignee.
     *
     * @param array $data Task data.
     */
    private function assignTask($data) {
        try {
            if (empty($data['task_id']) || empty($data['assignee'])) {
                throw new \Exception('Task ID and assignee are required');
            }

            $this->taskModel->assignTask($data['task_id'], $data['assignee']);
            
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Task assigned successfully'
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Retrieves assigned users for a task.
     *
     * @param int $taskId Task ID.
     */
    private function getAssignedUsers($taskId) {
        try {
            if (empty($taskId)) {
                throw new \Exception('Task ID is required');
            }

            $users = $this->taskModel->getAssignedUsers($taskId);
            
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Removes an assignment from a task.
     *
     * @param array $data Task data.
     */
    private function removeAssignment($data) {
        try {
            if (!isset($data['task_id']) || !isset($data['user_id'])) {
                throw new \Exception('Task ID and user ID are required');
            }

            $this->taskModel->removeAssignment($data['task_id'], $data['user_id']);
            
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Assignment removed successfully'
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}

?>
