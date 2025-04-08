<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DateTimeZone;

class TimeZoneController extends Controller
{
    public function index()
    {
        return view('timezone.index');
    }

    public function searchCities(Request $request)
    {
        $search = strtolower($request->input('search'));
        $timezones = DateTimeZone::listIdentifiers();
        $results = [];
        
        foreach ($timezones as $timezone) {
            $parts = explode('/', $timezone);
            if (count($parts) > 1) {
                $city = end($parts);
                $city = str_replace('_', ' ', $city);
                if (str_contains(strtolower($city), $search)) {
                    $results[] = [
                        'timezone' => $timezone,
                        'city' => $city,
                        'country' => $parts[0]
                    ];
                }
            }
        }
        
        return response()->json(['results' => $results]);
    }

    public function getTime(Request $request)
    {
        try {
            $timezones = $request->input('timezones', []);
            
            if (empty($timezones)) {
                return response()->json(['error' => 'At least one timezone is required'], 400);
            }

            $times = [];
            foreach ($timezones as $timezone) {
                if (!in_array($timezone, DateTimeZone::listIdentifiers())) {
                    continue;
                }

                $time = now()->setTimezone($timezone);
                $parts = explode('/', $timezone);
                $city = end($parts);
                $city = str_replace('_', ' ', $city);
                
                $times[] = [
                    'timezone' => $timezone,
                    'time' => $time->format('H:i:s'),
                    'date' => $time->format('Y-m-d'),
                    'offset' => $time->format('P'),
                    'city' => $city,
                    'country' => $parts[0]
                ];
            }
            
            return response()->json(['times' => $times]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error processing timezones: ' . $e->getMessage()], 500);
        }
    }
} 