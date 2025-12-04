$(document).ready(function () {

    // FUNCTION: Load and display tasks
    function loadTasks() {
        $.ajax({
            url: 'api/read.php',
            method: 'GET',
            dataType: 'json',
            success: function (tasks) {
                // Clear existing tasks
                $('#tasks-list').html('');

                // Check if there are tasks
                if (tasks.length === 0) {
                    $('#tasks-list').html('<p>No tasks yet. Add your first task!</p>');
                    return;
                }

                // Loop through each task and create HTML
                $.each(tasks, function (index, task) {
                    var taskHtml = '<div class="task-item">' +
                        '<h3>' + task.title + '</h3>' +
                        '<p>' + task.description + '</p>' +
                        '<p>Points: ' + task.points + '</p>' +
                        '<p>Status: ' + (task.is_completed == 1 ? 'Completed' : 'Not Completed') + '</p>' +
                        '<button class="btn-complete" data-id="' + task.id + '">Selesai</button>' +
                        '<button class="btn-delete" data-id="' + task.id + '">Delete</button>' +
                        '</div>';

                    $('#tasks-list').append(taskHtml);
                });
            },
            error: function (xhr, status, error) {
                $('#tasks-list').html('<p>Error loading tasks</p>');
                console.log('Error:', error);
            }
        });
    }

    // LOAD TASKS ON PAGE LOAD
    loadTasks();

    // FORM SUBMIT HANDLER
    $('#task-form').submit(function (e) {
        e.preventDefault();

        var title = $('#task-title').val();
        var description = $('#task-description').val();
        var points = $('#task-points').val();

        var taskData = {
            'task-title': title,
            'task-description': description,
            'task-points': points
        };

        $.ajax({
            url: 'api/create.php',
            method: 'POST',
            data: taskData,
            dataType: 'json',
            success: function (response) {
                alert('Success! Task created!');
                $('#task-form')[0].reset();
                loadTasks(); // RELOAD TASKS AFTER ADDING
            },
            error: function (xhr, status, error) {
                alert('Error: ' + error);
            }
        });
    });


    // Complete Button Handler
    $('#tasks-list').on('click', '.btn-complete', function () {
        var taskId = $(this).data('id');
        $.ajax({
            url: 'api/update.php',
            method: 'POST',
            data: {'task-id': taskId},
            dataType: 'json',
            success: function (response) {
                alert('Task Completed!');
                loadTasks();
            },
            error: function (xhr, status, error) {
                alert('Error: ' + error);
            }
        });
    });

    // Delete Button Handler
    $('#tasks-list').on('click', '.btn-delete', function () {
        var taskId = $(this).data('id');
        $.ajax({
            url: 'api/delete.php',
            method: 'POST',
            data: {'task-id': taskId},
            dataType: 'json',
            success: function (response) {
                alert('Task Deleted!');
                loadTasks();
            },
            error: function (xhr, status, error) {
                alert('Error: ' + error);
            }
        });
    });

});