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
    public function markComplete($id)
    {
        $task = Task::findOrFail($id);
        $task->is_completed = true;
        $task->save();
    
        return response()->json(['message' => 'Task marked as completed']);
    }
    
    public function markIncomplete($id)
    {
        $task = Task::findOrFail($id);
        $task->is_completed = false;
        $task->save();
    
        return response()->json(['message' => 'Task marked as incomplete']);
    }
    
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();
    
        return response()->json(['message' => 'Task deleted successfully']);
    }
    
}


