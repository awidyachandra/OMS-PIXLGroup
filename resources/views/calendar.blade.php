<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        .fc-daygrid-event {
            text-align: center;
            height: 30px;
            display: flex !important;
                justify-content: center;
                align-items: center;

        }

        .fc-event-title {
            width: 100%;
            text-align: center;
            font-weight: bold;
        }
        .fc-daygrid-day-number {
    color: black !important;
    font-weight: 600;
    text-decoration: none !important; /* hilangkan underline */
}
.fc-col-header-cell-cushion {
    color: black !important;
    font-weight: 600;
    text-decoration: none !important;
}
.fc-col-header-cell {
    background-color: #f1f1f1;
}
.fc-header-toolbar {
    background: #ffffff;
    padding: 12px 16px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    margin-bottom: 15px;
    color: #3b2a6f;
}
.fc-button {
    background-color: #3b2a6f !important;
    border: none !important;
    color: white !important;
    border-radius: 8px !important;
    padding: 5px 12px !important;
}

/* hover */
.fc-button:hover {
    background-color: #2c1f54 !important;
}

/* active */
.fc-button:active {
    background-color: #241a45 !important;
}

/* disabled */
.fc-button:disabled {
    background-color: #ccc !important;
    color: #666 !important;
}
    </style>
</head>

<body>
    @extends('layouts.app')

    @section('content')
        <div class="container">
            <h4 class="fw-bold mb-4">Booking Calendar</h4>

            <div id="calendar"></div>
        </div>
    @endsection

    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            let calendarEl = document.getElementById('calendar');

            let calendar = new FullCalendar.Calendar(calendarEl, {

                initialView: 'dayGridMonth',

                height: 650,
                headerToolbar: {
        left: 'prev',
        center: 'title',
        right: 'next today'
    },

                events: '/calendar/events',

                eventClick: function(info) {

                    let orderId = info.event.id;

                    window.location.href = `/marketing/orders/detail/${orderId}`;
                },

                eventDidMount: function(info) {

                    // 🔥 warna berdasarkan status
                    let status = info.event.extendedProps.status;

                    if (status === 'pending approval') {
                        info.el.style.backgroundColor = '#6c757d';
                    } else if (status === 'processed') {
                        info.el.style.backgroundColor = '#0d6efd';
                    } else if (status === 'assigned') {
                        info.el.style.backgroundColor = '#ffc107';
                    } else if (status === 'on rent') {
                        info.el.style.backgroundColor = '#198754';
                    } else if (status === 'completed') {
                        info.el.style.backgroundColor = '#212529';
                    } else if (status === 'cancelled') {
                        info.el.style.backgroundColor = '#dc3545';
                    }
                }

            });

            calendar.render();
        });
    </script>

    {{-- MODAL DETAIL --}}
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content p-4">

                <h5>Detail Order</h5>

                <div id="detailContent">
                    Loading...
                </div>

            </div>
        </div>
    </div>
</body>

</html>
