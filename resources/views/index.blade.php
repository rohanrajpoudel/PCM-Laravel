<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks List</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <!-- Include jQuery for simplicity in handling the AJAX request -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
            <textarea name="description" rows="4" placeholder="Do not forget to take a bath at 7AM in the morning everyday."></textarea>

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

    <script>
        $(document).ready(function() {
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
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
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
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
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
