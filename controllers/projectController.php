<?php

namespace controllers;

use models\ProjectModel;

class ProjectController {
    private $projectModel;

    public function __construct() {
        $this->projectModel = new ProjectModel();
    }

    public function handleRequest() {
        header('Content-Type: application/json');
        
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

            $action = $requestData['action'] ?? '';

            try {
                switch ($action) {
                    case 'create':
                        $this->createProject($requestData);
                        break;
                    case 'updateProject':
                        $this->updateProject($requestData);
                        break;
                    case 'deleteProject':
                        $this->deleteProject($requestData['id']);
                        break;
                    default:
                        throw new \Exception('Invalid action: ' . $action);
                }
            } catch (\Exception $e) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        } else {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Method Not Allowed'
            ]);
            exit;
        }
    }

    private function createProject($data) {
        if (!isset($data['name'], $data['state'], $data['user_id'])) {
            throw new \Exception('Project name, state, and user ID are required.');
        }

        $this->projectModel->createProject(
            $data['name'],
            $data['description'] ?? null,
            $data['user_id'],
            $data['state']
        );

        if (ob_get_length()) {
            ob_end_clean();
        }
    

        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Project updated successfully'
        ]);
       
    }

    private function updateProject($data) {
        if (!isset($data['id'], $data['state'])) {
            throw new \Exception('Project ID and state are required.');
        }

        $this->projectModel->updateProjectState(
            $data['id'],
            $data['state']
        );

        if (ob_get_length()) {
            ob_end_clean();
        }
    
        // Set response headers and return JSON
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Project updated successfully'
        ]);
    }

    private function deleteProject($id) {
        if (empty($id)) {
            throw new \Exception('Project ID is required.');
        }

        $this->projectModel->deleteProject($id);
        if (ob_get_length()) {
            ob_end_clean();
        }
    
        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Project deleted successfully'
        ]);
    }
}

// Initialize and handle the request
$controller = new ProjectController();
$controller->handleRequest();

?>
