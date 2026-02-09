//[calendar Javascript]

//Project:	Master Admin - Responsive Admin Template
//Primary use:   Used only for the event calendar


!function($) {
    "use strict";

    var CalendarApp = function() {
        this.$body = $("body")
        this.$calendar = $('#calendar'),
        this.$event = ('#external-events div.external-event'),
        this.$categoryForm = $('#add-new-events form'),
        this.$extEvents = $('#external-events'),
        this.$modal = $('#my-event'),
        this.$saveCategoryBtn = $('.save-category'),
        this.$calendarObj = null
    };


    /* on drop */
    CalendarApp.prototype.onDrop = function (eventObj, date) { 
        var $this = this;
        // retrieve the dropped element's stored Event Object
        var originalEventObject = eventObj.data('eventObject');
        var $categoryClass = eventObj.attr('data-class');
        // we need to copy it, so that multiple events don't have a reference to the same object
        var copiedEventObject = $.extend({}, originalEventObject);
        // assign it the date that was reported
        copiedEventObject.start = date;
        if ($categoryClass)
            copiedEventObject['className'] = [$categoryClass];
        
        // Save to DB via AJAX
        $.post('ajax_calendar.php', {
            action: 'add',
            title: copiedEventObject.title,
            start: date.format(),
            end: date.format(),
            class_name: $categoryClass,
            all_day: true
        }, function(response) {
            if (response.status === 'success') {
                copiedEventObject.id = response.id; // Assign ID from DB
                // render the event on the calendar
                $this.$calendar.fullCalendar('renderEvent', copiedEventObject, true);
                
                // is the "remove after drop" checkbox checked?
                if ($('#drop-remove').is(':checked')) {
                    // if so, remove the element from the "Draggable Events" list
                    eventObj.remove();
                }
            } else {
                alert('Failed to save event: ' + response.message);
            }
        }, 'json');
    },
    /* on click on event */
    CalendarApp.prototype.onEventClick =  function (calEvent, jsEvent, view) {
        var $this = this;
            var form = $("<form></form>");
            form.append("<label>Change event name</label>");
            form.append("<div class='input-group'><input class='form-control' type=text value='" + calEvent.title + "' /><span class='input-group-btn'><button type='submit' class='btn btn-success waves-effect waves-light'><i class='fa fa-check'></i> Save</button></span></div>");
            $this.$modal.modal({
                backdrop: 'static'
            });
            $this.$modal.find('.delete-event').show().end().find('.save-event').hide().end().find('.modal-body').empty().prepend(form).end().find('.delete-event').unbind('click').click(function () {
                // Delete from DB
                $.post('ajax_calendar.php', {
                    action: 'delete',
                    id: calEvent.id
                }, function(response) {
                    if (response.status === 'success') {
                        $this.$calendarObj.fullCalendar('removeEvents', function (ev) {
                            return (ev._id == calEvent._id);
                        });
                        $this.$modal.modal('hide');
                    } else {
                        alert('Failed to delete: ' + response.message);
                    }
                }, 'json');
            });
            $this.$modal.find('form').on('submit', function () {
                var newTitle = form.find("input[type=text]").val();
                
                // Update in DB
                $.post('ajax_calendar.php', {
                    action: 'update',
                    id: calEvent.id,
                    title: newTitle
                }, function(response) {
                    if (response.status === 'success') {
                        calEvent.title = newTitle;
                        $this.$calendarObj.fullCalendar('updateEvent', calEvent);
                        $this.$modal.modal('hide');
                    } else {
                        alert('Failed to update: ' + response.message);
                    }
                }, 'json');
                
                return false;
            });
    },
    /* on select */
    CalendarApp.prototype.onSelect = function (start, end, allDay) {
        var $this = this;
            $this.$modal.modal({
                backdrop: 'static'
            });
            var form = $("<form></form>");
            form.append("<div class='row'></div>");
            form.find(".row")
                .append("<div class='col-md-6'><div class='form-group'><label class='form-label'>Event Name</label><input class='form-control' placeholder='Insert Event Name' type='text' name='title'/></div></div>")
                .append("<div class='col-md-6'><div class='form-group'><label class='form-label'>Category</label><select class='form-control' name='category'></select></div></div>")
                .find("select[name='category']")
                .append("<option value='bg-danger'>Danger</option>")
                .append("<option value='bg-success'>Success</option>")
                .append("<option value='bg-purple'>Purple</option>")
                .append("<option value='bg-primary'>Primary</option>")
                .append("<option value='bg-pink'>Pink</option>")
                .append("<option value='bg-info'>Info</option>")
                .append("<option value='bg-warning'>Warning</option></div></div>");
            $this.$modal.find('.delete-event').hide().end().find('.save-event').show().end().find('.modal-body').empty().prepend(form).end().find('.save-event').unbind('click').click(function () {
                form.submit();
            });
            $this.$modal.find('form').on('submit', function () {
                var title = form.find("input[name='title']").val();
                var categoryClass = form.find("select[name='category'] option:checked").val();
                if (title !== null && title.length != 0) {
                    
                    // Add to DB
                    $.post('ajax_calendar.php', {
                        action: 'add',
                        title: title,
                        start: start.format(),
                        end: end.format(),
                        class_name: categoryClass,
                        all_day: allDay
                    }, function(response) {
                        if (response.status === 'success') {
                            $this.$calendarObj.fullCalendar('renderEvent', {
                                id: response.id,
                                title: title,
                                start:start,
                                end: end,
                                allDay: allDay,
                                className: categoryClass
                            }, true);  
                            $this.$modal.modal('hide');
                        } else {
                            alert('Failed to save event: ' + response.message);
                        }
                    }, 'json');
                }
                else{
                    alert('You have to give a title to your event');
                }
                return false;
                
            });
            $this.$calendarObj.fullCalendar('unselect');
    },
    CalendarApp.prototype.enableDrag = function() {
        //init events
        $(this.$event).each(function () {
            // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
            // it doesn't need to have a start or end
            var eventObject = {
                title: $.trim($(this).text()) // use the element's text as the event title
            };
            // store the Event Object in the DOM element so we can get to it later
            $(this).data('eventObject', eventObject);
            // make the event draggable using jQuery UI
            $(this).draggable({
                zIndex: 999999,
                revert: true,      // will cause the event to go back to its
                revertDuration: 0  //  original position after the drag
            });
        });
    }
    /* Initializing */
    CalendarApp.prototype.init = function() {
        this.enableDrag();
        /*  Initialize the calendar  */
        var $this = this;
        
        // Fetch events from DB
        $.getJSON('ajax_calendar.php?action=fetch', function(events) {
            $this.$calendarObj = $this.$calendar.fullCalendar({
                slotDuration: '00:15:00', /* If we want to split day time each 15minutes */
                minTime: '08:00:00',
                maxTime: '19:00:00',  
                defaultView: 'month',  
                handleWindowResize: true,   
                 
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                events: events,
                editable: true,
                droppable: true, // this allows things to be dropped onto the calendar !!!
                eventLimit: true, // allow "more" link when too many events
                selectable: true,
                drop: function(date) { $this.onDrop($(this), date); },
                select: function (start, end, allDay) { $this.onSelect(start, end, allDay); },
                eventClick: function(calEvent, jsEvent, view) { $this.onEventClick(calEvent, jsEvent, view); },
                eventDrop: function(event, delta, revertFunc) {
                    $.post('ajax_calendar.php', {
                        action: 'drop',
                        id: event.id,
                        start: event.start.format(),
                        end: event.end ? event.end.format() : event.start.format()
                    }, function(response) {
                        if (response.status !== 'success') {
                            revertFunc();
                            alert('Update failed: ' + response.message);
                        }
                    }, 'json');
                },
                eventResize: function(event, delta, revertFunc) {
                    $.post('ajax_calendar.php', {
                        action: 'drop', // Use same action as it handles start/end update
                        id: event.id,
                        start: event.start.format(),
                        end: event.end.format()
                    }, function(response) {
                        if (response.status !== 'success') {
                            revertFunc();
                            alert('Update failed: ' + response.message);
                        }
                    }, 'json');
                },
                eventDragStop: function(event, jsEvent) {
                    var trashEl = $('#calendar-trash');
                    var ofs = trashEl.offset();
                    var x1 = ofs.left;
                    var x2 = ofs.left + trashEl.outerWidth(true);
                    var y1 = ofs.top;
                    var y2 = ofs.top + trashEl.outerHeight(true);
                    
                    if (jsEvent.pageX >= x1 && jsEvent.pageX <= x2 &&
                        jsEvent.pageY >= y1 && jsEvent.pageY <= y2) {
                        
                        if(confirm('Are you sure you want to delete this event?')) {
                            $.post('ajax_calendar.php', {
                                action: 'delete',
                                id: event.id
                            }, function(response) {
                                if (response.status === 'success') {
                                    $this.$calendarObj.fullCalendar('removeEvents', event._id);
                                } else {
                                    alert('Failed to delete: ' + response.message);
                                }
                            }, 'json');
                        }
                    }
                }
            });
        });

        //on new event
        this.$saveCategoryBtn.on('click', function(){
            var categoryName = $this.$categoryForm.find("input[name='category-name']").val();
            var categoryColor = $this.$categoryForm.find("select[name='category-color']").val();
            if (categoryName !== null && categoryName.length != 0) {
                // Save to DB
                $.post('ajax_calendar.php', {
                    action: 'add_template',
                    title: categoryName,
                    class_name: 'bg-' + categoryColor
                }, function(response) {
                    if (response.status === 'success') {
                        $this.$extEvents.append('<div class="m-15 external-event bg-' + categoryColor + '" data-class="bg-' + categoryColor + '" style="position: relative;"><i class="fa fa-hand-o-right"></i>' + categoryName + '</div>')
                        $this.enableDrag();
                    } else {
                        alert('Failed to save template: ' + response.message);
                    }
                }, 'json');
            }

        });
    },

   //init CalendarApp
    $.CalendarApp = new CalendarApp, $.CalendarApp.Constructor = CalendarApp
    
}(window.jQuery),

//initializing CalendarApp
function($) {
    "use strict";
    $.CalendarApp.init()
}(window.jQuery);