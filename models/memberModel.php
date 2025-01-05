<?php
namespace models;

use Config\DB;

class MemberModel {
    public function addMember($projectId, $userId) {
        $query = "INSERT INTO members (project_id, user_id) VALUES (:project_id, :user_id)";
        $params = [
            'project_id' => $projectId,
            'user_id' => $userId
        ];
        
        try {
            DB::query($query, $params);
            return true;
        } catch (\PDOException $e) {
            // If duplicate entry, just return true as the member is already added
            if ($e->getCode() == '23000') {
                return true;
            }
            return false;
        }
    }

    public function removeMember($projectId, $userId) {
        $query = "DELETE FROM members WHERE project_id = :project_id AND user_id = :user_id";
        $params = [
            'project_id' => $projectId,
            'user_id' => $userId
        ];
        
        return DB::query($query, $params)->rowCount() > 0;
    }

    public function getProjectMembers($projectId) {
        $query = "
            SELECT u.id, u.username
            FROM members m
            INNER JOIN users u ON m.user_id = u.id
            WHERE m.project_id = :project_id
        ";
        $params = ['project_id' => $projectId];
        
        $result = DB::query($query, $params);
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function searchUsers($term) {
        $query = "
            SELECT id, username
            FROM users
            WHERE username LIKE :term
            LIMIT 10
        ";
        $params = ['term' => "%$term%"];
        
        $result = DB::query($query, $params);
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }
}
