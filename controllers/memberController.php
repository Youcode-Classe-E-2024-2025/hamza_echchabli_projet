<?php
namespace controllers;

use models\MemberModel;

use models\RolePerModel;
use models\ProjectModel;

class MemberController {
    private $memberModel;
    private $RP;

    private $PA;

    public function __construct() {
        $this->memberModel = new MemberModel();
        $this->RP = new RolePerModel();
        $this->PA = new ProjectModel();
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
                    case 'searchUsers':
                        $this->searchUsers($requestData);
                        break;
                    case 'addMember':
                        
                        $userId = $_SESSION['user']['id'];
                        $projectId = $requestData['project_id'];
                    
                        // Vérifie si l'utilisateur est administrateur.
                        $isAdmin = $this->PA->checkAdmin($userId, $projectId);
                    
                        // Vérifie si l'utilisateur a la permission de créer une tâche.
                        $hasCreatePermission = $this->RP->getUserPermissions($userId, $projectId, 'addMembers');
                    
                        // Si l'utilisateur n'est ni administrateur ni autorisé à créer des tâches.
                        if (!$isAdmin && !$hasCreatePermission) {
                            http_response_code(403);
                            echo json_encode([
                                'success' => false,
                                
                            ]);
                            return;
                        }
                        
                        $this->addMember($requestData);
                        break;
                    case 'removeMember':
                        $userId = $_SESSION['user']['id'];
                        $projectId = $requestData['project_id'];
                    
                        // Vérifie si l'utilisateur est administrateur.
                        $isAdmin = $this->PA->checkAdmin($userId, $projectId);
                    
                        // Vérifie si l'utilisateur a la permission de créer une tâche.
                        $hasCreatePermission = $this->RP->getUserPermissions($userId, $projectId, 'removeMember');
                    
                        // Si l'utilisateur n'est ni administrateur ni autorisé à créer des tâches.
                        if (!$isAdmin && !$hasCreatePermission) {
                            http_response_code(403);
                            echo json_encode([
                                'success' => false,
                                
                            ]);
                            return;
                        }
                        $this->removeMember($requestData);
                        break;
                    case 'updateMemberRole':
                            $this->EditMemberRole($requestData);
                            break;
                    case 'getProjectMembers':
                        $this->getProjectMembers($requestData['project_id']);
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

    private function searchUsers($requestData) {
        $term = $requestData['term'] ?? '';

        if (empty($term)) {
            throw new \Exception('Search term is required');
        }

        $users = $this->memberModel->searchUsers($term);
        if (ob_get_length()) {
            ob_end_clean();
        }
    
        echo json_encode([
            'success' => true,
            'data' => $users
        ]);
    }

    private function addMember($requestData) {
        $projectId = $requestData['project_id'] ?? null;
        $userId = $requestData['user_id'] ?? null;

        if (!$projectId || !$userId) {
            throw new \Exception('Missing required fields');
        }
        
        $success = $this->memberModel->addMember($projectId, $userId);
        if (ob_get_length()) {
            ob_end_clean();
        }
    
        echo json_encode(['success' => true]);
    }





    private function EditMemberRole($requestData) {
        $projectId = $requestData['project_id'] ?? null;
        $userId = $requestData['user_id'] ?? null;
        $roleName = $requestData['role_id'] ?? null;


        $res = $this->memberModel->getRole($roleName);
        if (!$res) {
            throw new \Exception('Role not found');
        }
       
        
        $success = $this->memberModel->editRole($projectId, $userId , $res['id'] );
        if (ob_get_length()) {
            ob_end_clean();
        }
    
        echo json_encode(['success' => $success]);
    }


    private function removeMember($requestData) {
        $projectId = $requestData['project_id'] ?? null;
        $userId = $requestData['user_id'] ?? null;

        if (!$projectId || !$userId) {
            throw new \Exception('Missing required fields');
        }

        $success = $this->memberModel->removeMember($projectId, $userId);
        if (ob_get_length()) {
            ob_end_clean();
        }
    
        echo json_encode(['success' => $success]);
    }

    private function getProjectMembers($projectId) {
        if (!$projectId) {
            throw new \Exception('Project ID is required');
        }

        $members = $this->memberModel->getProjectMembers($projectId);
        if (ob_get_length()) {
            ob_end_clean();
        }
    
        echo json_encode([
            'success' => true,
            'data' => $members
        ]);
    }
}
