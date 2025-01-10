# 🛠️ Project Management System

A robust **Project Management System** designed for effective team collaboration and task tracking. This platform offers features such as **user authentication**, **role-based access control**, and **project/task management**. Ideal for teams working on projects that require structured workflows and permissions.

---

## 📖 Features

### 🔒 **Authentication**
- **Login**: Securely log in to access the platform.
- **Register**: Create a new user account.

### 📂 **Project Management**
- **Create Projects**: Add new projects to the system.
- **Update Projects**: Modify existing project details.
- **Delete Projects**: Remove projects permanently.
  - **Note**: Users must log in to perform these actions.

### 🗂️ **Task Management**
- **Create Tasks**: Add tasks to projects.
- **Update Tasks**: Edit task details (e.g., deadlines, status).
- **Delete Tasks**: Remove tasks when no longer needed.
  - **Permission Required**: Users must have the necessary permissions to manage tasks.

### 🛡️ **Role & Permissions**
- **Create Roles**: Define roles such as `Admin`, `Project Manager`, or `Team Member`.
- **Assign Permissions**: Set permissions for roles (e.g., CRUD operations on tasks or projects).
- **Manage Roles**: Modify or delete existing roles.
  - **Access**: Only project owners or admins can manage roles.

---

## 🛠️ Technologies Used
- **Backend**: PHP 8 (OOP, PDO for database interaction)
- **Frontend**: HTML, CSS, JavaScript
- **Database**: PostgreSQL
- **Version Control**: Git and GitHub

---

## 🛠️ How to Set Up the Project

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/your-username/project-management-system.git
