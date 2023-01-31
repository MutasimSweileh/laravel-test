<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Workshop;
use Carbon\Carbon;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Date;

class EventsController extends BaseController
{
    public function getWarmupEvents()
    {
        return Event::all();
    }

    public function getEventsWithWorkshops()
    {
        $events = Event::all();
        $workshops = Workshop::all();
        $eventsWithWorkshops = [];

        foreach ($events as $event) {
            $workshopsByEvent = $workshops->where('event_id', $event->id)->all();
            $eventWithWorkshops = [
                'id' => $event->id,
                'name' => $event->name,
                'created_at' => $event->created_at,
                'updated_at' => $event->updated_at,
                'workshops' => array_values($workshopsByEvent)
            ];
            array_push($eventsWithWorkshops, $eventWithWorkshops);
        }
        return $eventsWithWorkshops;
    }
    public function getFutureEventsWithWorkshops()
    {
        $currentTime = now();
        $futureWorkshops = Workshop::where('start', '>=', $currentTime)
            ->selectRaw('min(start) as start, event_id')
            ->groupBy('event_id')
            ->pluck('start', 'event_id');
        $FutureEvent = [];
        $futureWorkshops =  Event::whereIn('id', $futureWorkshops->keys())->get();
        foreach ($futureWorkshops as $key => $event) {
            $workshops = Workshop::where('start', '>=', $currentTime);
            $workshopsByEvent = $workshops->where('event_id', $event->id)->get();
            $event->workshops = $workshopsByEvent;
            $FutureEvent[] = $event;
        }
        return $FutureEvent;
    }
}
