<?php

namespace models;

use entities\ProjectEntity;
use config\DB; // Assuming you have a core\DB class for database interaction

class ProjectModel {

    // Create a new project
    public function createProject($name, $description, $user_id,$state) {
        $query = "INSERT INTO projects (name, description, user_id, state) VALUES (:name, :description, :user_id, :state)";
        $params = [
            'name' => $name,
            'description' => $description,
            'user_id' => $user_id,
            'state' => $state
        ];
        return DB::query($query, $params);
    }

    // Delete a project by ID
    public function deleteProject($id) {
        $query = "DELETE FROM projects WHERE id = :id";
        $params = ['id' => $id];
        return DB::query($query, $params);
    }

    // Update a project by ID
    public function updateProject($id, $name, $description, $state) {
        $query = "UPDATE projects SET name = :name, description = :description, state = :state WHERE id = :id";
        $params = [
            'id' => $id,
            'name' => $name,
            'description' => $description,
            'state' => $state
        ];
        return DB::query($query, $params);
    }

    // Get all projects for a specific user
    public function getProjects($user_id) {
        $query = "SELECT * FROM projects WHERE user_id = :user_id";
        $params = ['user_id' => $user_id];
        $result = DB::query($query, $params);
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Get all public projects (state = public)
    // public function getPublicProjects() {
    //     $query = "SELECT * FROM projects WHERE state = 'public'";
    //     $result = DB::query($query);
    //     return $result->fetchAll(\PDO::FETCH_ASSOC);
    // }

    public function getPublicProjects() {
        $query = "
           SELECT 
    p.id,
    p.name,
    p.description,
    p.user_id,
    p.state,
    p.type,
    COUNT(DISTINCT t.id) AS task_count,
    COUNT(DISTINCT at.user_id) AS member_count
FROM 
    projects p
LEFT JOIN 
    tasks t ON t.project_id = p.id
LEFT JOIN 
    assigntasks at ON at.task_id = t.id
WHERE 
    p.state = 'public'
GROUP BY 
    p.id, p.name, p.description, p.user_id, p.state, p.type;

        ";
        $result = DB::query($query);
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    // Get all private projects (state = private)
    public function getPrivateProjects($user_id) {
        $query = "SELECT * FROM projects WHERE state = 'private' AND user_id = :user_id";
        $params = ['user_id' => $user_id];
        $result = DB::query($query, $params);
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getProjectsByUserId($user_id) {
        $query = "
            SELECT 
                p.id,
                p.name,
                p.description,
                p.user_id,
                p.state,
                p.type,
                COUNT(DISTINCT t.id) AS task_count,
                COUNT(DISTINCT at.user_id) AS member_count
            FROM 
                projects p
            LEFT JOIN 
                tasks t ON t.project_id = p.id
            LEFT JOIN 
                assigntasks at ON at.task_id = t.id
            WHERE 
                p.user_id = :user_id
            GROUP BY 
                p.id, p.name, p.description, p.user_id, p.state, p.type
        ";
        $params = ['user_id' => $user_id];
        $result = DB::query($query, $params);
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }
}

?>
