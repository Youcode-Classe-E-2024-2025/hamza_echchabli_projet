<?php

namespace controllers;

use models\ProjectModel;

class ProjectController {
    private $projectModel;

    public function __construct() {
        $this->projectModel = new ProjectModel();
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

            $action = $requestData['action'];

            try {
                switch ($action) {
                    case 'create':
                        $this->createProject($requestData);
                        break;
                    case 'getProjectsByUser':
                        $this->getProjectsByUser($requestData['user_id']);
                        break;
                    case 'getPublicProjects':
                        $this->getPublicProjects();
                        break;
                    case 'getPrivateProjects':
                        $this->getPrivateProjects($requestData['user_id']);
                        break;
                    case 'updateProject':
                        $this->updateProject($requestData);
                        break;
                    case 'deleteProject':
                        $this->deleteProject($requestData['id']);
                        break;
                    default:
                        throw new \Exception('Invalid action');
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

        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Project created successfully'
        ]);
    }

    private function getProjectsByUser($user_id) {
        if (empty($user_id)) {
            throw new \Exception('User ID is required.');
        }

        $projects = $this->projectModel->getProjects($user_id);

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $projects
        ]);
    }

    private function getPublicProjects() {
        $projects = $this->projectModel->getPublicProjects();

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $projects
        ]);
    }

    private function getPrivateProjects($user_id) {
        if (empty($user_id)) {
            throw new \Exception('User ID is required.');
        }

        $projects = $this->projectModel->getPrivateProjects($user_id);

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $projects
        ]);
    }

    private function updateProject($data) {
        if (!isset($data['id'], $data['name'], $data['state'])) {
            throw new \Exception('Project ID, name, and state are required.');
        }

        $this->projectModel->updateProject(
            $data['id'],
            $data['name'],
            $data['description'] ?? null,
            $data['state']
        );

        http_response_code(200);
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

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Project deleted successfully'
        ]);
    }
}

?>
