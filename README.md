Task Management API
Welcome to the Task Management API! This API allows for efficient management of tasks with different roles, providing functionalities for CRUD operations, task assignments, and advanced querying. Below you'll find detailed information about the API, its features, and how to use it.

Table of Contents
Overview
Features
API Endpoints
Models
Query Scopes
Soft Deletes
Date Handling
Setup
Testing
Contributing
License
Overview
The Task Management API is designed to help manage tasks and users with different roles. It supports creating, updating, viewing, and deleting tasks and users while implementing role-based access controls to ensure proper authorization.

Features
Role-based Access Control: Admin, Manager, and User roles with specific permissions.
CRUD Operations: Create, read, update, and delete tasks and users.
Task Assignment: Managers can assign tasks to users. Assigned users can update task statuses.
Advanced Querying: Filter tasks by priority and status.
Soft Deletes: Restore deleted tasks and users.
Date Handling: Accessors and mutators for date formatting.
API Endpoints
Task Management
POST /tasks

Create a new task.
GET /tasks

Retrieve all tasks with optional filtering by priority and status.
GET /tasks/{id}

Retrieve details of a specific task.
PUT /tasks/{id}

Update a task (only assigned user or admin can update).
DELETE /tasks/{id}

Soft delete a task.
Task Assignment
POST /tasks/{id}/assign
Assign a task to a user (Manager role only).
User Management
POST /users

Create a new user.
GET /users

Retrieve all users.
PUT /users/{id}

Update user details (Admin role only).
DELETE /users/{id}

Soft delete a user.
Models
Task Model
fillable: ['title', 'description', 'priority', 'due_date', 'status', 'assigned_to']
primaryKey: task_id
table: tasks
timestamps: true
User Model
guarded: ['password', 'role']
primaryKey: user_id
table: users
timestamps: true
Query Scopes
Priority Scope
Filter tasks by priority:

php
Copy code
public function scopePriority($query, $priority)
{
    return $query->where('priority', $priority);
}
Status Scope
Filter tasks by status:

php
Copy code
public function scopeStatus($query, $status)
{
    return $query->where('status', $status);
}
Soft Deletes
Soft deletes are implemented for both tasks and users to allow restoration. Use the onlyTrashed() method to retrieve deleted records and restore() to recover them.

Date Handling
Use accessors and mutators for date formatting:

php
Copy code
// Accessor
public function getDueDateAttribute($value)
{
    return \Carbon\Carbon::parse($value)->format('d-m-Y H:i');
}

// Mutator
public function setDueDateAttribute($value)
{
    $this->attributes['due_date'] = \Carbon\Carbon::createFromFormat('d-m-Y H:i', $value)->format('Y-m-d H:i:s');
}
Setup
Clone the repository:

bash
Copy code
git clone <repository-url>
Install dependencies:

bash
Copy code
composer install
Set up the environment:

Copy the .env.example file to .env and update the database configuration.

bash
Copy code
cp .env.example .env
Run migrations and seeders:

bash
Copy code
php artisan migrate
php artisan db:seed
Start the server:

bash
Copy code
php artisan serve
Testing
Run the tests with:

bash
Copy code
php artisan test
Contributing
Contributions are welcome! Please fork the repository and submit a pull request with your changes.

License
This project is licensed under the MIT License. See the LICENSE file for details.

