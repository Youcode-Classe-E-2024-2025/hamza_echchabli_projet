

// namespace entities;

// class ProjectEntity {
//     private $id;
//     private $name;
//     private $type;
//     private $description;
//     private $userId;
//     private $createdAt;

//     // Getters
//     public function getId() {
//         return $this->id;
//     }

//     public function getName() {
//         return $this->name;
//     }

//     public function getType() {
//         return $this->type;
//     }

//     public function getDescription() {
//         return $this->description;
//     }

//     public function getUserId() {
//         return $this->userId;
//     }

//     public function getCreatedAt() {
//         return $this->createdAt;
//     }

//     // Setters
//     public function setId($id) {
//         $this->id = $id;
//         return $this;
//     }

//     public function setName($name) {
//         $this->name = $name;
//         return $this;
//     }

//     public function setType($type) {
//         $this->type = $type;
//         return $this;
//     }

//     public function setDescription($description) {
//         $this->description = $description;
//         return $this;
//     }

//     public function setUserId($userId) {
//         $this->userId = $userId;
//         return $this;
//     }

//     public function setCreatedAt($createdAt) {
//         $this->createdAt = $createdAt;
//         return $this;
//     }

//     // Validation method
//     public function validate() {
//         $errors = [];

//         if (empty($this->name)) {
//             $errors[] = 'Project name is required';
//         }

//         if (empty($this->type)) {
//             $errors[] = 'Project type is required';
//         }

//         return $errors;
//     }

//     // Convert to array for database insertion
//     public function toArray() {
//         return [
//             'name' => $this->name,
//             'type' => $this->type,
//             'description' => $this->description,
//             'user_id' => $this->userId
//         ];
//     }
// }

?> 