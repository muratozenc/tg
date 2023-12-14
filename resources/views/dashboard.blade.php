@php
$user = auth()->user();
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            My Appointments
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div id="calendar"></div>
                    {{ $user->user_type }}
                    <script>
                        $(document).ready(function () {
                            var SITEURL = "{{ url('/') }}";

                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });

                            $.ajax({
                                url: SITEURL + "/update-user-timezone",
                                data: { timezone: userTimeZone() },
                                type: "POST"
                            });

                            function userTimeZone() {
                                return Intl.DateTimeFormat().resolvedOptions().timeZone;
                            }

                            var calendarConfig = {
                                editable: true,
                                header: {
                                    left: 'prev,next today',
                                    center: 'title',
                                    right: 'agendaDay,agendaWeek,month'
                                },
                                defaultView: 'agendaDay',
                                events: SITEURL + "/appointments",
                                displayEventTime: true,
                                editable: false,
                                eventRender: function (event, element, view) {
                                    event.allDay = event.allDay === 'true';
                                },
                                selectable: true,
                                selectHelper: true
                            };

                            $('#calendar').fullCalendar(calendarConfig);

                        });

                        function displayMessage(message) {
                            alert(message);
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
