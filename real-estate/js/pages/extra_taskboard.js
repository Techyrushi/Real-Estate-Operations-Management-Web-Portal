//[ Javascript]

$(function () {
    "use strict";
    
    // Make the dashboard widgets sortable Using jquery UI
    $('.connectedSortable').sortable({
        placeholder         : 'sort-highlight',
        connectWith         : '.connectedSortable',
        handle              : '.handle',
        forcePlaceholderSize: true,
        zIndex              : 999999,
        receive: function(event, ui) {
            // Item dropped into a new list
            var newStatus = $(this).data('status');
            var taskId = ui.item.data('id');
            
            $.post('ajax_taskboard.php', {
                action: 'update_status',
                id: taskId,
                status: newStatus
            }, function(response) {
                if(response.status !== 'success') {
                    alert('Failed to update task status');
                    // Revert if needed, but UI is already updated
                }
            }, 'json');
        }
    });
    $('.connectedSortable .box-header, .connectedSortable .nav-tabs-custom').css('cursor', 'move');

    // Add Task
    $('#save-task-btn').on('click', function(e) {
        e.preventDefault();
        var formData = $('#add-task-form').serialize();
        // Check required fields manually since we are using AJAX
        if (!$('#task-title').val()) {
            alert('Title is required');
            return;
        }

        $.post('ajax_taskboard.php', {
            action: 'add',
            title: $('#task-title').val(),
            description: $('#task-desc').val(),
            status: $('#task-status').val(),
            priority: $('#task-priority').val()
        }, function(response) {
            if(response.status === 'success') {
                alert('Task added successfully!');
                location.reload(); // Simple reload to show new task
            } else {
                alert('Failed to add task: ' + response.message);
            }
        }, 'json');
    });

    // Delete Task
    $(document).on('click', '.delete-task', function(e) {
        e.preventDefault();
        if(!confirm('Are you sure you want to delete this task?')) return;
        
        var $li = $(this).closest('li');
        var taskId = $(this).data('id');
        
        $.post('ajax_taskboard.php', {
            action: 'delete',
            id: taskId
        }, function(response) {
            if(response.status === 'success') {
                $li.fadeOut(function() { $(this).remove(); });
            } else {
                alert('Failed to delete task: ' + response.message);
            }
        }, 'json');
    });

}); // End of use strict
