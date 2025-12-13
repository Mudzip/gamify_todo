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

    // LOAD TASKS AND STATS ON PAGE LOAD
    loadTasks();
    loadStats();
    loadRewards();

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
                loadStats(); // UPDATE STATS AFTER COMPLETING TASK
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

    // Claim Button Handler
    $('#rewards-list').on('click', '.btn-claim', function () {
        var rewardLevel = $(this).data('level');
        $.ajax({
            url: 'api/claim.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({'reward_level': rewardLevel}),
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    alert('Hadiah Diklaim Selamat Menikmati!');
                    loadRewards(); // Refresh rewards list
                } else {
                    alert('Error: ' + response.error);
                }
            },
            error: function (xhr, status, error) {
                alert('Error: ' + error);
            }
        });
    });

    // Display Points And Level
    function loadStats() {
        $.ajax({
            url: 'api/stats.php',
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                $('#display-level').text(response['level']);
                $('#display-points').text(response['total_points']);
                var progress = response['total_points'] % 100;
                $('#display-progress').css('width', progress + '%');
            },
            error: function (xhr, status, error) {
                console.log('Error loading stats:', error);
            }
        });
    }


    function loadRewards() {
        $.ajax({
            url: 'api/rewards.php',
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                var currentLevel = response.current_level;
                var rewards = response.rewards;
                var claimHistory = response.claim_history;

                // Clear existing
                $('#rewards-list').html('');
                $('#claim-history').html('');

                // Loop rewards
                $.each(rewards, function (index, reward) {
                    var isUnlocked = currentLevel >= reward.level;
                    var rewardHtml;

                    if (isUnlocked) {
                        rewardHtml = '<div class="reward-item bg-success p-2 mb-2 rounded">' +
                            'Level ' + reward.level + ' - ' + reward.name + ' (' + reward.duration + ')' +
                            ' <button class="btn btn-sm btn-warning btn-claim" data-level="' + reward.level + '">Claim</button>' +
                            '</div>';
                    } else {
                        rewardHtml = '<div class="reward-item bg-dark p-2 mb-2 rounded text-muted">' +
                            'Level ' + reward.level + ' - ' + reward.name + ' (' + reward.duration + ')' +
                            '</div>';
                    }

                    $('#rewards-list').append(rewardHtml);
                });

                // Loop claim history
                if (claimHistory.length === 0) {
                    $('#claim-history').html('<p>Belum ada klaim.</p>');
                } else {
                    $.each(claimHistory, function (index, claim) {
                        var historyHtml = '<p>• Level ' + claim.reward_level + ' - ' + claim.claimed_at + '</p>';
                        $('#claim-history').append(historyHtml);
                    });
                }
            },
            error: function (xhr, status, error) {
                console.log('Error loading rewards:', error);
            }
        });
    }

});