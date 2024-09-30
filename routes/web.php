<?php

use Illuminate\Support\Facades\Route; 
use App\Http\Controllers\TodoController; 

Route::get('/', function () {
    return view('welcome');
})->name('home'); 

// Defining a route to show the list of tasks
Route::get('/tasks', [Todocontroller::class, 'index'])->name('tasks.index');
// When a GET request is made to '/tasks', it calls the 'index' method of TaskController to display the tasks
// This route is named 'tasks.index' for easy reference

// Defining a route to store a new task
Route::post('/tasks', [Todocontroller::class, 'store'])->name('tasks.store');
// When a POST request is made to '/tasks', it calls the 'store' method of TaskController
// This is used to add a new task to the list
// This route is named 'tasks.store' for easy reference

// Defining a route to mark a task as complete
Route::patch('/tasks/{id}/complete', [Todocontroller::class, 'markComplete'])->name('tasks.complete');
// When a PATCH request is made to '/tasks/{id}/complete', it calls the 'markComplete' method of TaskController
// The {id} is a placeholder for the task's unique identifier, allowing you to mark that specific task as complete
// This route is named 'tasks.markComplete' for easy reference

Route::patch('/tasks/{id}/incomplete', [Todocontroller::class, 'markIncomplete'])->name('tasks.incomplete');

Route::delete('/tasks/{id}', [TodoController::class, 'destroy'])->name('tasks.destroy');
