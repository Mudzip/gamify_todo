$(document).ready(function () {

    // Load tasks
    function loadTasks() {
        $.ajax({
            url: 'api/read.php',
            method: 'GET',
            dataType: 'json',
            success: function (tasks) {
                $('#tasks-list').html('');

                if (tasks.length === 0) {
                    $('#tasks-list').html('<p>No tasks yet. Add your first task!</p>');
                    return;
                }

                $.each(tasks, function (index, task) {
                    var statusClass = task.is_completed == 1 ? 'bg-success' : 'bg-dark';
                    var safeTitle = $('<div>').text(task.title).html();
                    var safeDescription = $('<div>').text(task.description || '').html();
                    var taskHtml = '<div class="task-item ' + statusClass + ' p-2 mb-2 rounded">' +
                        '<h3>' + safeTitle + '</h3>' +
                        '<p>' + safeDescription + '</p>' +
                        '<p>Status: ' + (task.is_completed == 1 ? '✅ Completed (+1 token)' : '⏳ Not Completed') + '</p>';

                    if (task.is_completed == 0) {
                        taskHtml += '<button class="btn btn-success btn-sm btn-complete" data-id="' + task.id + '">Selesai</button> ';
                    }
                    taskHtml += '<button class="btn btn-danger btn-sm btn-delete" data-id="' + task.id + '">Delete</button>' +
                        '</div>';

                    $('#tasks-list').append(taskHtml);
                });
            },
            error: function () {
                $('#tasks-list').html('<p>Error loading tasks</p>');
            }
        });
    }

    // Load stats
    function loadStats() {
        $.ajax({
            url: 'api/stats.php',
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                $('#display-level').text(response.level);
                $('#display-tasks').text(response.total_tasks);
                $('#display-tokens').text(response.available_tokens);

                var tasksInLevel = response.total_tasks % 5;
                var nextLevel = 5 - tasksInLevel;
                if (tasksInLevel === 0 && response.total_tasks > 0) {
                    nextLevel = 5;
                }
                $('#display-next').text(nextLevel);

                var progress = (tasksInLevel / 5) * 100;
                $('#display-progress').css('width', progress + '%');
            },
            error: function () {
                console.log('Error loading stats');
            }
        });
    }

    // Load rewards
    function loadRewards() {
        $.ajax({
            url: 'api/rewards.php',
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                var currentLevel = response.current_level;
                var availableTokens = response.available_tokens;
                var rewards = response.rewards;
                var claimHistory = response.claim_history;

                $('#rewards-list').html('');

                $.each(rewards, function (index, reward) {
                    var isUnlocked = currentLevel >= reward.level;
                    var canAfford = availableTokens >= reward.cost;
                    var rewardHtml;

                    if (isUnlocked) {
                        rewardHtml = '<div class="reward-item bg-success p-2 mb-2 rounded d-flex justify-content-between align-items-center">' +
                            '<span>🎉 Level ' + reward.level + ' - ' + reward.name + ' (' + reward.duration + ')</span>';

                        if (canAfford) {
                            rewardHtml += '<button class="btn btn-warning btn-sm btn-claim" data-name="' + reward.name + ' (' + reward.duration + ')" data-cost="' + reward.cost + '">Buy (5 🎟️)</button>';
                        } else {
                            rewardHtml += '<button class="btn btn-secondary btn-sm" disabled>Need 5 🎟️</button>';
                        }
                        rewardHtml += '</div>';
                    } else {
                        rewardHtml = '<div class="reward-item bg-dark p-2 mb-2 rounded text-muted">' +
                            '🔒 Level ' + reward.level + ' - ' + reward.name + ' (' + reward.duration + ')' +
                            '</div>';
                    }

                    $('#rewards-list').append(rewardHtml);
                });

                // Claim history
                $('#claim-history').html('');
                if (claimHistory.length === 0) {
                    $('#claim-history').html('<p class="text-muted">No claims yet.</p>');
                } else {
                    $.each(claimHistory, function (index, claim) {
                        var historyHtml = '<p>• ' + claim.reward_name + ' (-' + claim.tokens_spent + ' 🎟️) - ' + claim.claimed_at + '</p>';
                        $('#claim-history').append(historyHtml);
                    });
                }
            },
            error: function () {
                console.log('Error loading rewards');
            }
        });
    }

    // Load on page start
    loadTasks();
    loadStats();
    loadRewards();

    // Form submit
    $('#task-form').submit(function (e) {
        e.preventDefault();

        var taskData = {
            'task-title': $('#task-title').val(),
            'task-description': $('#task-description').val()
        };

        $.ajax({
            url: 'api/create.php',
            method: 'POST',
            data: taskData,
            dataType: 'json',
            success: function () {
                alert('Task created!');
                $('#task-form')[0].reset();
                loadTasks();
            },
            error: function () {
                alert('Error creating task');
            }
        });
    });

    // Complete button
    $('#tasks-list').on('click', '.btn-complete', function () {
        var taskId = $(this).data('id');
        $.ajax({
            url: 'api/update.php',
            method: 'POST',
            data: {'task-id': taskId},
            dataType: 'json',
            success: function () {
                alert('Task Completed! +1 Token 🎟️');
                loadTasks();
                loadStats();
                loadRewards();
            },
            error: function () {
                alert('Error completing task');
            }
        });
    });

    // Delete button
    $('#tasks-list').on('click', '.btn-delete', function () {
        var taskId = $(this).data('id');
        $.ajax({
            url: 'api/delete.php',
            method: 'POST',
            data: {'task-id': taskId},
            dataType: 'json',
            success: function () {
                alert('Task Deleted!');
                loadTasks();
                loadStats();
                loadRewards();
            },
            error: function () {
                alert('Error deleting task');
            }
        });
    });

    // Claim button
    $('#rewards-list').on('click', '.btn-claim', function () {
        var rewardName = $(this).data('name');
        var rewardCost = $(this).data('cost');

        if (confirm('Claim "' + rewardName + '" for ' + rewardCost + ' tokens?')) {
            $.ajax({
                url: 'api/claim.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    'reward_name': rewardName,
                    'reward_cost': rewardCost
                }),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert('🎉 Reward claimed! Enjoy your treat!');
                        loadStats();
                        loadRewards();
                    } else {
                        alert('Error: ' + response.error);
                    }
                },
                error: function () {
                    alert('Error claiming reward');
                }
            });
        }
    });

});