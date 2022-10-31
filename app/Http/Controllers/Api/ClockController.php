<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Record;
use App\Models\School;
use DateTime;
use Illuminate\Http\Request;

class ClockController extends Controller
{
    
    /**
     * Calculates the great-circle distance between two points, with
     * the Haversine formula.
     * @param float $latitudeFrom Latitude of start point in [deg decimal]
     * @param float $longitudeFrom Longitude of start point in [deg decimal]
     * @param float $latitudeTo Latitude of target point in [deg decimal]
     * @param float $longitudeTo Longitude of target point in [deg decimal]
     * @param float $earthRadius Mean earth radius in [m]
     * @return float Distance between points in [m] (same as earthRadius)
     */
    public function haversineGreatCircleDistance(
        $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
    {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);
    
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
    
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    public function clock(Request $request) {
        $data = $request->validate([
            'lat' => 'required|numeric|min:-90|max:90',
            'lng' => 'required|numeric|min:-180|max:180',
            'type' => 'required|in:CLOCK_IN,CLOCK_OUT'
        ], [
            'lat.required' => 'Latitude tidak boleh kosong.',
            'lat.numeric' => 'Latitude tidak valid.',
            'lat.min' => 'Latitude tidak valid.',
            'lat.max' => 'Latitude tidak valid.',
            'lng.required' => 'Longitude tidak boleh kosong.',
            'lng.numeric' => 'Longitude tidak valid.',
            'lng.min' => 'Longitude tidak valid.',
            'lng.max' => 'Longitude tidak valid.',
            'type.required' => 'Tipe tidak boleh kosong.',
            'type.in' => 'Tipe tidak valid.'
        ]);
        $school = School::find(auth()->user()->school_id)->first();
        $nowDate = new DateTime();
        $status = null;
        $message = "";
        $distance = $this->haversineGreatCircleDistance($school->lat, $school->lng, $data['lat'], $data['lng']);
        if ($distance > $school->distance) {
            return response()->json(['message' => 'Anda berada di luar area sekolah.'], 422);
        }

        if ($data['type'] == 'CLOCK_IN') {
            $clockInTime = strtotime($school->clock_in);
            $nowTime = strtotime($nowDate->format('H:i:s'));
            if ($nowTime <= $clockInTime) {
                $status = 'ON_TIME';
                $message = "Anda masuk tepat waktu.";
            } else {
                $status = 'LATE';
                $message = "Anda terlambat masuk.";
            }
        } 
        else {
            $message = "Anda berhasil presensi pulang.";
            // $clockOutTime = strtotime($school->clock_out);
        }

        $createdData = [
            'user_id' => auth()->user()->id,
            'lat' => $data['lat'],
            'lng' => $data['lng'],
            'status' => $status,
            'clock_time' => $nowDate,
            'clock_type' => $data['type'], 
        ];
        $record = Record::create($createdData);

        return response()->json([
            'message' => $message,
            'clock' => $nowDate->format('Y-m-d H:i:s'),
            'distance' => number_format((float)$distance, 2, '.', ''),
            'in_distance' => $distance <= $school->distance 
        ]);
    }
}
