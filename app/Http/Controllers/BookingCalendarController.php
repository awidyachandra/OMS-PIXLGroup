<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Carbon;

class BookingCalendarController extends Controller
{
    public function calendar()
{
    return view('calendar');
}
public function calendarEvents()
{
    $orders = Order::with('customer')
        ->whereNotIn('status', ['pending approval', 'completed','processed'])
        ->get();

    $events = [];

    foreach ($orders as $order) {

        $start = Carbon::parse($order->date);
        $end   = $order->return_date 
                    ? Carbon::parse($order->return_date)
                    : $start;

        $events[] = [
            'title' => '#' . $order->id . ' ' . $order->event,
            'start' => $start->toDateString(),
            'end'   => $end->toDateString(),
            'allDay'=> true,
            'id'    => $order->id,
            'color' => '#d51500'
        ];
        $events[] = [
            'title' => 'Pickup #' . $order->id,
            'start' => $start->copy()->subDay()->toDateString(),
            'allDay'=> true,
            'id'    => $order->id,
            'color' => '#E69F00'
        ];
        $events[] = [
            'title' => 'Return #' . $order->id,
            'start' => $end->toDateString(), 
            'allDay'=> true,
            'id'    => $order->id,
            'color' => '#0072B2'
        ];
    }

    return response()->json($events);
}
}
