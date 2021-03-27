<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ApiController extends Controller
{

	// set is_business_day to true for the given date
    public function isBusinessDay(Request $request) {
    	$rules = [
           'date'      => 'required|date'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
	        return response()->json([
	    		'status_code' => 422,
	    		'data' => ["error" => $validator->getMessageBag()->toArray()]
	    	], 422);
        }
    	return response()->json([
    		'status_code' => 200,
    		'data' => ["is_business_day" => true]
    	], 200);
    }

    // List all businness days from the given dates
    public function getBusinessDays(Request $request) {
    	$start_date = $request->start_date;
    	$end_date = $request->end_date;

    	$rules = [
           'start_date'      => 'required|date',
           'end_date'      => 'required|date|after_or_equal:start_date'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
	        return response()->json([
	    		'status_code' => 422,
	    		'data' => ["error" => $validator->getMessageBag()->toArray()]
	    	], 422);
        }
        $from = Carbon::createFromFormat('d-m-Y', $start_date);
        $to = Carbon::createFromFormat('d-m-Y', $end_date);
        $dates = $this->generateDateRange($from, $to);
    	return response()->json([
    		'status_code' => 200,
    		'data' => ['days' => $dates]
    	], 200);
    }

    // Push date range in an array
    private function generateDateRange(Carbon $start_date, Carbon $end_date) {
	    $dates = [];
	    for($date = $start_date; $date->lte($end_date); $date->addDay()) {
	    	if(!$date->isWeekend()){
	    		$dates[] = $date->format('d-m-Y');
	    	}	        
	    }

	    return $dates;

	}
}
