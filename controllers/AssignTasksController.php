<?php

namespace controllers;

use models\AssignTasksModel;


class AssignTasksController {
    private $assignTasksModel;

    public function __construct() {
        $this->assignTasksModel = new AssignTasksModel();
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rawInput = file_get_contents('php://input');
            $requestData = json_decode($rawInput, true);

            if (!$requestData) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid JSON in request body'
                ]);
                exit;
            }

            $action = $requestData['action'] ?? null;

            try {
                switch ($action) {
                    case 'assignTask':
                        $this->assignTask($requestData);
                        break;
                    case 'unassignTask':
                        $this->unassignTask($requestData);
                        break;
                    case 'getAssignedTasksByUser':
                        $this->getAssignedTasksByUser($requestData);
                        break;
                    case 'getUsersAssignedToTask':
                        $this->getUsersAssignedToTask($requestData);
                        break;
                    case 'getAllTaskAssignments':
                        $this->getAllTaskAssignments();
                        break;
                    default:
                        throw new \Exception('Invalid action specified');
                }
            } catch (\Exception $e) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
        } else {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Method Not Allowed'
            ]);
        }
    }

    private function assignTask($data) {
        $user_id = $data['user_id'] ?? null;
        $task_id = $data['task_id'] ?? null;

        if (empty($user_id) || empty($task_id)) {
            throw new \Exception('Both user_id and task_id are required.');
        }

        $this->assignTasksModel->assignTask($user_id, $task_id);

        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Task successfully assigned to user.'
        ]);
    }

    private function unassignTask($data) {
        $id = $data['id'] ?? null;

        if (empty($id)) {
            throw new \Exception('Assignment ID is required.');
        }

        $this->assignTasksModel->unassignTask($id);

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Task assignment successfully removed.'
        ]);
    }

    private function getAssignedTasksByUser($data) {
        $user_id = $data['user_id'] ?? null;

        if (empty($user_id)) {
            throw new \Exception('User ID is required.');
        }

        $tasks = $this->assignTasksModel->getAssignedTasksByUser($user_id);

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $tasks
        ]);
    }

    private function getUsersAssignedToTask($data) {
        $task_id = $data['task_id'] ?? null;

        if (empty($task_id)) {
            throw new \Exception('Task ID is required.');
        }

        $users = $this->assignTasksModel->getUsersAssignedToTask($task_id);

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $users
        ]);
    }

    private function getAllTaskAssignments() {
        $assignments = $this->assignTasksModel->getAllTaskAssignments();

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $assignments
        ]);
    }
}

?>
