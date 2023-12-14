@php
$user = auth()->user();
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $user->user_type == 'therapist' ? 'Set Available Times' : 'See Available Times of Therapist' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div id="calendar"></div>

                    <script>
                        $(document).ready(function () {
                            var SITEURL = "{{ url('/') }}";
                            var userType = "{{ $user->user_type }}";

                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });

                            updateServerWithUserTimeZone();

                            var calendar = initializeCalendar(userType);

                            function updateServerWithUserTimeZone() {
                                $.ajax({
                                    url: SITEURL + "/update-user-timezone",
                                    data: { timezone: userTimeZone() },
                                    type: "POST"
                                });
                            }

                            function userTimeZone() {
                                return Intl.DateTimeFormat().resolvedOptions().timeZone;
                            }

                            function initializeCalendar(userType) {
                                var config = {
                                    header: {
                                        left: 'prev,next today',
                                        center: 'title',
                                        right: 'agendaDay,agendaWeek,month'
                                    },
                                    defaultView: 'agendaDay',
                                    events: SITEURL + "/availabilities",
                                    displayEventTime: true,
                                    editable: userType === 'therapist',
                                    eventRender: standardEventRender
                                };

                                if (userType === 'therapist') {
                                    addTherapistSpecificConfig(config);
                                } else if (userType === 'client') {
                                    addClientSpecificConfig(config);
                                }

                                return $('#calendar').fullCalendar(config);
                            }

                            function standardEventRender(event, element, view) {
                                event.allDay = event.allDay === 'true';
                            }

                            function addTherapistSpecificConfig(config) {
                                config.selectable = true;
                                config.selectHelper = true;
                                config.select = therapistSelectHandler;
                                config.eventDrop = therapistEventDropHandler;
                                config.eventClick = therapistEventClickHandler;
                            }

                            function addClientSpecificConfig(config) {
                                config.selectable = false;
                                config.eventClick = clientEventClickHandler;
                            }

                            function therapistSelectHandler(start, end, allDay) {
                                var title = confirm('Would you like to mark this time as available?');
                                if (title) {
                                    var startFormatted = $.fullCalendar.formatDate(start, "Y-MM-DD HH:mm:ss");
                                    var endFormatted = $.fullCalendar.formatDate(end, "Y-MM-DD HH:mm:ss");

                                    $.ajax({
                                        url: SITEURL + "/availabilitiesAjax",
                                        data: {
                                            title: 'Available',
                                            start: startFormatted,
                                            end: endFormatted,
                                            timezone: userTimeZone(),
                                            type: 'add'
                                        },
                                        type: "POST",
                                        success: function (response) {
                                            displayMessage("Time slot marked as available");
                                            $('#calendar').fullCalendar('refetchEvents');
                                        }
                                    });
                                }
                            }

                            function therapistEventDropHandler(event, delta) {
                                var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
                                var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");

                                $.ajax({
                                    url: SITEURL + '/availabilitiesAjax',
                                    data: {
                                        id: event.id,
                                        title:event.title,
                                        start: start,
                                        end: end,
                                        timezone: userTimeZone(),
                                        type: 'update'
                                    },
                                    type: "POST",
                                    success: function (response) {
                                        displayMessage("Appointment updated successfully");
                                    }
                                });
                            }

                            function therapistEventClickHandler(event) {
                                var deleteMsg = confirm("Are you sure you want to delete this availability?");
                                if (deleteMsg) {
                                    $.ajax({
                                        url: SITEURL + '/availabilitiesAjax',
                                        data: {
                                            id: event.id,
                                            type: 'delete'
                                        },
                                        type: "POST",
                                        success: function (response) {
                                            $('#calendar').fullCalendar('removeEvents', event.id);
                                            displayMessage("Availability removed successfully");
                                        }
                                    });
                                }
                            }

                            function clientEventClickHandler(event) {
                                var confirmBooking = confirm("Do you want to book this appointment?");
                                if (confirmBooking) {
                                    $.ajax({
                                        url: SITEURL + '/availabilitiesAjax', // This should be the URL to book the appointment
                                        data: {
                                            id: event.id
                                        },
                                        type: "POST",
                                        success: function (response) {
                                            displayMessage("Appointment booked successfully");
                                            $('#calendar').fullCalendar('refetchEvents');
                                        }
                                    });
                                }
                            }

                            function displayMessage(message) {
                                alert(message);
                            }

                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
