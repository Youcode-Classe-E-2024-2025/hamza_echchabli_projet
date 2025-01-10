<?php
namespace models;

use Config\DB;
use \PDO;

class RolesModel {
    // Get all roles for a project
    public function getProjectRoles($project_id) {
        try {
            $query = "
             SELECT 
    r.name AS role_name,
    string_agg(p.name, ', ') AS permission_name
FROM 
    public.roles r
LEFT JOIN 
    public.rolper rp ON r.id = rp.roles_id
LEFT JOIN 
    public.permissions p ON rp.permission_id = p.id
WHERE 
    r.project_id = :project_id
GROUP BY 
    r.id
ORDER BY 
    r.name;



            ";
        
            $params = [
                'project_id' => $project_id
            ];
        
            $res = DB::query($query, $params);
            $data = $res->fetchAll(PDO::FETCH_ASSOC);
        
            // Group by role name
            $grouped = [];
            foreach ($data as $row) {
                if ($row['permission_name'] !== null) {
                    $grouped[$row['role_name']][] = $row['permission_name'];
                } else {
                    $grouped[$row['role_name']] = [];
                }
            }
        
            return $grouped;
        } catch (\PDOException $e) {
            error_log("Database error in getProjectRoles: " . $e->getMessage());
            throw new \Exception("Failed to get project roles");
        }
    }
    

    // Create a new role (no link to member_id)
    public function createRole($name , $project_id) {
        try {
            $query = "INSERT INTO public.roles (name,project_id) VALUES (:name, :project_id) RETURNING id";
            $params = ['name' => $name, 'project_id' => $project_id];
            
            $result = DB::query($query, $params);
            return $result->fetch(PDO::FETCH_ASSOC)['id'];
        } catch (\PDOException $e) {
            error_log("Database error in createRole: " . $e->getMessage());
            throw new \Exception("Failed to create role");
        }
    }

    // Assign permissions to a role
    public function assignPermissionsToRole($roleId, $permissions) {
        try {
            // First, remove all existing permissions for the role
            $deleteQuery = "DELETE FROM public.rolper WHERE roles_id = :role_id";
            DB::query($deleteQuery, ['role_id' => $roleId]);

            if (!empty($permissions)) {
                // Now, assign new permissions to the role
                $insertQuery = "INSERT INTO public.rolper (roles_id, permission_id) VALUES (:role_id, :permission_id)";
                foreach ($permissions as $permissionId) {
                    $params = [
                        'role_id' => $roleId,
                        'permission_id' => $permissionId
                    ];
                    DB::query($insertQuery, $params);
                }
            }
            return true;
        } catch (\PDOException $e) {
            error_log("Database error in assignPermissionsToRole: " . $e->getMessage());
            throw new \Exception("Failed to assign permissions");
        }
    }

    // Update an existing role
    public function updateRole($id, $name) {
        try {
            $query = "UPDATE public.roles SET name = :name WHERE id = :id";
            $params = [
                'id' => $id,
                'name' => $name
            ];
            
            $result = DB::query($query, $params);
            return $result->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log("Database error in updateRole: " . $e->getMessage());
            throw new \Exception("Failed to update role");
        }
    }

    // Delete a role
    public function deleteRole($id) {
        try {
            // First delete role permissions
            $deletePermQuery = "DELETE FROM public.rolper WHERE roles_id = :id";
            DB::query($deletePermQuery, ['id' => $id]);

            // Then delete the role
            $query = "DELETE FROM public.roles WHERE id = :id";
            DB::query($query, ['id' => $id]);
            return true;
        } catch (\PDOException $e) {
            error_log("Database error in deleteRole: " . $e->getMessage());
            throw new \Exception("Failed to delete role");
        }
    }
}
?>
