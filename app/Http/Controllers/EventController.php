<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'date.startDate' => 'required|date|date_format:Y-m-d',
            'date.endDate' => 'required|date|after_or_equal:date.startDate|date_format:Y-m-d',
            'description' => 'required|min:1',
            'days' => 'required|array'
        ]);

        $start = Carbon::parse($request->date['startDate']);
        $end = Carbon::parse($request->date['endDate']);

        $result = [];

        foreach ($request->days as $key => $day) {
            $tmp = $start->copy();
        
            while($tmp->lessThanOrEqualTo($end)) {
    
                if($tmp->is(ucwords($day))) {

                    $event = Event::updateOrCreate(
                        ['date' => $tmp->copy()->format('Y-m-d')],
                        ['description' => $request->description]
                    );

                    array_push($result, $event);

                }
    
                $tmp->addDay();
            }

        }

        $events = Event::whereMonth('date', $start->copy()->format('m'))
                ->whereYear('date', $start->copy()->format('Y'))
                ->whereNotIn('id', array_column($result, 'id'))
                ->delete();

        return response()->json($result);
    }

    public function getEvents(Request $request) {
        $request->validate([
            'date' => 'required|date|date_format:Y-m-d'
        ]);

        $date = Carbon::parse($request->date);

        $events = Event::whereMonth('date', $date->format('m'))->whereYear('date', $date->format('Y'))->get();

        return response()->json([
            'status' => 'success',
            'code' => '200',
            'data' => $events
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
