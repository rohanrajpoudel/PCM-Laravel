# Laravel Workshop: ToDo App Development Guide  
**Trainers:** Ishan Subedi, Rohan Raj Poudel  
**Location:** Pokhara College of Management

---

## Introduction

Hello everyone!

Due to time constraints during the workshop, we couldn't fully code the entire app. However, we've created this easy-to-follow guide to help you complete the project at your own pace.

---

## Prerequisites

Before proceeding, please make sure you have completed the following steps.

### 1. Install XAMPP

If you haven't installed XAMPP, download and install it from [XAMPP Apache Friends](https://www.apachefriends.org/index.html).

### 2. Install Composer

Composer is essential for managing PHP dependencies. If you haven't installed it yet, download it from [Composer](https://getcomposer.org).

### 3. Create a Laravel Project

Run the following command to create a new Laravel project if you haven't done so already:

```bash
composer create-project laravel/laravel projectName
```

### 4. Create a Controller

Create the `Todocontroller` by running the following Artisan command:

```bash
php artisan make:controller Todocontroller
```

### 5. Create a Model

Create the `Task` model by running the following command:

```bash
php artisan make:model Task
```

### 6. Create a Migration for the Tasks Table

Generate a migration file for creating the tasks table:

```bash
php artisan make:migration create_tasks_table
```

Once the migration is created, update the migration file (`database/migrations/YYYY_MM_DD_create_tasks_table.php`) with the following code to define the structure of the `tasks` table:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamps();  // Automatically adds created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
```

### 7. Update the `.env` File

In the `.env` file, set up the correct database connection by configuring the following lines:

- Uncomment or ensure the following line is present:

  ```
  DB_CONNECTION=mysql
  ```

- Update the database credentials accordingly:

  ```plaintext
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=your_database_name
  DB_USERNAME=your_username
  DB_PASSWORD=your_password
  ```

Replace `your_database_name`, `your_username`, and `your_password` with your actual database details.

### 8. Database Migration

Run the following command to create the `tasks` table in the database:

```bash
php artisan migrate
```

### 9. Model Setup

Update the `Task.php` file located in `app/Models` to make the attributes fillable. This ensures mass assignment is allowed when creating or updating tasks:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    // Define the attributes that are mass assignable
    protected $fillable = [
        'name',          // The name of the task
        'description',   // A brief description of the task
        'is_completed',  // A boolean indicating whether the task is completed (true/false)
    ];
}
```

---

## After completing the above steps, follow along:


## Controller Setup

Modify the `Todocontroller.php` file located in `app/Http/Controllers` by adding the necessary functions:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class Todocontroller extends Controller
{
    // Display a list of all tasks
    public function index()
    {
        // Retrieve all tasks from the database
        $tasks = Task::all();
        // Pass the tasks to the 'index' view for rendering
        return view('index', ['tasks' => $tasks]);
    }

    // Add a new task to the database
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            // 'name' is required and must be a string with a maximum length of 255 characters
            'name' => 'required|string|max:255',
            // 'description' is optional and can be a string
            'description' => 'nullable|string',
        ]);

        // Create a new task using the validated data
        Task::create($validatedData);

        // Redirect to the tasks index page after creating the task
        return redirect()->route('tasks.index');
    }

    // Mark a specific task as completed
    public function markComplete($id)
    {
        $task = Task::findOrFail($id);
        $task->is_completed = true;
        $task->save();

        return response()->json(['message' => 'Task marked as completed']);
    }

    // Mark a specific task as incomplete
    public function markIncomplete($id)
    {
        $task = Task::findOrFail($id);
        $task->is_completed = false;
        $task->save();

        return response()->json(['message' => 'Task marked as incomplete']);
    }

    // Delete a specific task
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }
}
```

---

## Routes Setup

Update the `web.php` file inside the `routes` folder with the following lines of code:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Todocontroller;

// Defining a route to show the list of tasks
Route::get('/tasks', [Todocontroller::class, 'index'])->name('tasks.index');

// Defining a route to store a new task
Route::post('/tasks', [Todocontroller::class, 'store'])->name('tasks.store');

// Defining a route to mark a task as complete
Route::patch('/tasks/{id}/complete', [Todocontroller::class, 'markComplete'])->name('tasks.complete');

// Defining a route to mark a task as incomplete
Route::patch('/tasks/{id}/incomplete', [Todocontroller::class, 'markIncomplete'])->name('tasks.incomplete');

// Defining a route to delete a task
Route::delete('/tasks/{id}', [Todocontroller::class, 'destroy'])->name('tasks.destroy');
```

---

## View Setup

Inside the `resources/views` folder, create a file named `index.blade.php` and add the following lines of code:

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks List</title>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- Include jQuery for AJAX handling -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- CSRF Token for AJAX requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container task-container">
        <h1>To-do App</h1>
        <h3>Laravel Workshop (PCM)</h3>

        <!-- Add Task Form -->
        <form action="{{ route('tasks.store') }}" method="POST" class="task-form">
            @csrf
            <!-- Task name input with helper text -->
            <label for="name">Task Name</label>
            <input type="text" name="name" placeholder="Take a Bath" required>

            <!-- Task description textarea with helper text -->
            <label for="description">Task Description</label>
            <textarea name="description" rows="4" placeholder="Do not forget to take a bath at 7AM in the morning every day."></textarea>

            <button type="submit" class="btn">Add Task</button>
        </form>

        <!-- Task List Section -->
        <section class="task-list-section">
            <ul class="task-list">
                @foreach ($tasks as $task)
                    <li class="task-item" data-task-id="{{ $task->id }}" data-task-name="{{ $task->name }}"
                        data-task-description="{{ $task->description }}" data-task-created="{{ $task->created_at }}"
                        data-task-updated="{{ $task->updated_at }}">

                        <!-- Checkbox to mark task as complete/incomplete -->
                        <input type="checkbox" class="task-checkbox" {{ $task->is_completed ? 'checked' : '' }}>

                        <!-- Task name with a hover effect to display the description -->
                        <span class="task-name" style="{{ $task->is_completed ? 'text-decoration: line-through;' : '' }}"
                              title="{{ $task->description }}">
                            {{ $task->name }}
                        </span>

                        <!-- Trash Bin Icon for Deleting Task -->
                        <button class="delete-task-btn" data-task-id="{{ $task->id }}">
                            🗑️
                        </button>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>

    <!-- Task Detail Popup -->
    <div id="taskDetailPopup" class="popup hidden">
        <div class="popup-content">
            <h2 id="popupTaskName"></h2>
            <p id="popupTaskDescription"></p>
            <p><strong>Created at:</strong> <span id="popupCreatedAt"></span></p>
            <p><strong>Updated at:</strong> <span id="popupUpdatedAt"></span></p>
            <button id="closePopupBtn" class="btn">Close</button>
        </div>
    </div>

    <!-- Delete Confirmation Popup -->
    <div id="deleteConfirmationPopup" class="popup hidden">
        <div class="popup-content">
            <p>Are you sure you want to delete this task?</p>
            <button id="confirmDeleteBtn" class="btn red-btn">Yes</button>
            <button id="cancelDeleteBtn" class="btn green-btn">No</button>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        $(document).ready(function() {
            // CSRF Token setup for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Handle checkbox click event
            $('.task-checkbox').on('change', function() {
                var taskItem = $(this).closest('.task-item');
                var taskId = taskItem.data('task-id');
                var isCompleted = $(this).is(':checked');

                if (isCompleted) {
                    taskItem.find('.task-name').css('text-decoration', 'line-through');
                } else {
                    taskItem.find('.task-name').css('text-decoration', 'none');
                }

                $.ajax({
                    url: isCompleted ? '/tasks/' + taskId + '/complete' : '/tasks/' + taskId + '/incomplete',
                    type: 'PATCH',
                    success: function(response) {
                        console.log(response.message);
                    },
                    error: function(error) {
                        console.log('Error:', error);
                    }
                });
            });

            // Show task details popup when task name is clicked
            $('.task-name').on('click', function() {
                var taskItem = $(this).closest('.task-item');
                $('#popupTaskName').text(taskItem.data('task-name'));
                $('#popupTaskDescription').text(taskItem.data('task-description'));
                $('#popupCreatedAt').text(taskItem.data('task-created'));
                $('#popupUpdatedAt').text(taskItem.data('task-updated'));

                $('#taskDetailPopup').removeClass('hidden');
            });

            // Close task details popup
            $('#closePopupBtn').on('click', function() {
                $('#taskDetailPopup').addClass('hidden');
            });

            // Show delete confirmation popup
            var taskToDelete = null;
            $('.delete-task-btn').on('click', function() {
                taskToDelete = $(this).data('task-id');
                $('#deleteConfirmationPopup').removeClass('hidden');
            });

            // Cancel delete confirmation popup
            $('#cancelDeleteBtn').on('click', function() {
                $('#deleteConfirmationPopup').addClass('hidden');
                taskToDelete = null;
            });

            // Confirm delete and remove task
            $('#confirmDeleteBtn').on('click', function() {
                if (taskToDelete) {
                    $.ajax({
                        url: '/tasks/' + taskToDelete,
                        type: 'DELETE',
                        success: function(response) {
                            // Remove the task from the list
                            $('li[data-task-id="' + taskToDelete + '"]').remove();
                            $('#deleteConfirmationPopup').addClass('hidden');
                        },
                        error: function(error) {
                            console.log('Error:', error);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
```

---

## Front-End Styling

Create a folder inside the `public` directory named `css` and create a file named `style.css` within it. Add the following CSS code:

```css
/* Global styling */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.container {
    width: 100%;
    max-width: 600px;
    background-color: #fff;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}

/* Heading styles */
h1 {
    text-align: center;
    color: #333;
    margin-bottom: 10px;
    font-size: 2rem;
}

h3 {
    text-align: center;
    margin-bottom: 20px;
}

/* Form styles */
.task-form {
    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
}

.task-form label {
    margin-bottom: 5px;
}

.task-form input,
.task-form textarea {
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
}

.task-form button {
    padding: 10px;
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
}

.task-form button:hover {
    background-color: #218838;
}

/* Task list styles */
.task-list {
    list-style-type: none;
    padding: 0;
}

.task-item {
    display: flex;
    align-items: center;
    padding: 10px;
    margin-bottom: 10px;
    background-color: #f9f9f9;
    border-left: 5px solid #28a745;
    border-bottom: 1px solid #ddd;
}

.task-checkbox {
    margin-right: 10px;
    cursor: pointer;
}

/* Task name styling */
.task-name {
    flex-grow: 1;
    font-size: 1.1rem;
    transition: color 0.3s ease;
    cursor: pointer;
}

.task-name:hover {
    color: #007bff;
    font-weight: bold;
}

/* Strikethrough effect for completed tasks */
.task-checkbox:checked + .task-name {
    text-decoration: line-through;
    color: grey;
    transition: color 0.3s ease;
}

/* Delete (bin) button styling */
.delete-task-btn {
    background-color: white;
    color: red;
    border: none;
    border-radius: 5px;
    padding: 5px 10px;
    cursor: pointer;
    font-size: 1rem;
}

.delete-task-btn:hover {
    background-color: red;
    color: white;
}

/* Popup styling */
.popup {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: rgb(255, 255, 255);
    padding: 30px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    border-radius: 10px;
}

.popup.hidden {
    display: none;
}

.popup-content {
    text-align: center;
}

.popup .btn {
    padding: 10px 15px;
    margin: 10px;
    cursor: pointer;
    border-radius: 5px;
    font-size: 1rem;
}

.red-btn {
    background-color: red;
    color: white;
}

.green-btn {
    background-color: green;
    color: white;
}
```

---

## Final Steps

- **Run the Application**: Start your Laravel development server:

  ```bash
  php artisan serve
  ```

- **Access the App**: Open your browser and navigate to `http://localhost:8000/tasks` to see your ToDo app in action.

---

## Conclusion

You have now successfully set up a basic ToDo application using Laravel. This app allows you to:

- Add new tasks.
- View all tasks.
- Mark tasks as complete or incomplete.
- View task details in a popup.
- Delete tasks with confirmation.

Feel free to customize and expand upon this foundation to add more features or improve the design.

---

**Best regards,**  
*Ishan Subedi & Rohan Raj Poudel*  
Laravel Workshop Trainers

---

If you encounter any issues or need further guidance, don't hesitate to reach out. Good luck with your project, and happy coding!

