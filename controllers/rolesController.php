<?php

namespace controllers;

use models\RolesModel;

class RolesController {
    private $roleModel;

    public function __construct() {
        $this->roleModel = new RolesModel();
    }

    /**
     * Handles incoming requests.
     */
    public function handleRequest() {
        header('Content-Type: application/json');
        
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
                case 'createRole':
                    $this->createRole($requestData);
                    break;

                case 'assignPermissions':
                    $this->assignPermissions($requestData);
                    break;

                case 'getProjectRoles':
                    $this->getProjectRoles($requestData['project_id']);
                    break;

                case 'deleteRole':
                    $this->deleteRole($requestData);
                    break;

                default:
                    throw new \Exception('Invalid action');
            }
        } catch (\Exception $e) {
            error_log("Error in handleRequest: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Maps permission names to their database IDs.
     *
     * @return array Mapping of permission names to their IDs
     */
    private function getPermissionMap() {
        return [
            'addMembers' => 1,
            'Edit' => 2,
            'assign' => 3,
            'create' => 4,
            'delete' => 5,
            'removeMember' => 6
        ];
    }

    /**
     * Creates a new role.
     *
     * @param array $data Role data.
     */
    private function createRole($data) {
        if (empty($data['role_name'])) {
            throw new \Exception('Role name is required.');
        }

        $roleId = $this->roleModel->createRole($data['role_name'],$data['project_id']);
        
        if (!empty($data['permissions'])) {
            $permissionMap = $this->getPermissionMap();
            $permissionIds = [];
            
            foreach ($data['permissions'] as $permissionName) {
                if (isset($permissionMap[$permissionName])) {
                    $permissionIds[] = $permissionMap[$permissionName];
                }
            }

            if (!empty($permissionIds)) {
                $this->roleModel->assignPermissionsToRole($roleId, $permissionIds);
            }
        }

        echo json_encode([
            'success' => true,
            'message' => 'Role created successfully',
            'role_id' => $roleId
        ]);
    }

    /**
     * Assigns permissions to a role.
     *
     * @param array $data Role and permissions data.
     */
    private function assignPermissions($data) {
        if (empty($data['role_name']) || !isset($data['permissions'])) {
            throw new \Exception('Role name and permissions are required.');
        }

        $permissionMap = $this->getPermissionMap();
        $permissionIds = [];
        
        foreach ($data['permissions'] as $permissionName) {
            if (isset($permissionMap[$permissionName])) {
                $permissionIds[] = $permissionMap[$permissionName];
            }
        }

        $this->roleModel->assignPermissionsToRole($data['role_name'], $permissionIds);

        echo json_encode([
            'success' => true,
            'message' => 'Permissions assigned successfully'
        ]);
    }

    /**
     * Retrieves roles for a specific project.
     *
     * @param int $projectId Project ID.
     */
    private function getProjectRoles($projectId) {
        if (empty($projectId)) {
            throw new \Exception('Project ID is required.');
        }

        $roles = $this->roleModel->getProjectRoles($projectId);

        echo json_encode([
            'success' => true,
            'data' => $roles
        ]);
    }

    /**
     * Deletes a role.
     *
     * @param array $data Role data.
     */
    private function deleteRole($data) {
        if (empty($data['role_name'])) {
            throw new \Exception('Role name is required.');
        }

        $this->roleModel->deleteRole($data['role_name']);

        echo json_encode([
            'success' => true,
            'message' => 'Role deleted successfully'
        ]);
    }
}
