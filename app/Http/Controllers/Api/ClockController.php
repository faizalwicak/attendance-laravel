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
        $latitudeFrom,
        $longitudeFrom,
        $latitudeTo,
        $longitudeTo,
        $earthRadius = 6371000
    ) {
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

    public function clockIn(Request $request)
    {
        $data = $request->validate([
            'lat' => 'required|numeric|min:-90|max:90',
            'lng' => 'required|numeric|min:-180|max:180',
        ], [
            'lat.required' => 'Latitude tidak boleh kosong.',
            'lat.numeric' => 'Latitude tidak valid.',
            'lat.min' => 'Latitude tidak valid.',
            'lat.max' => 'Latitude tidak valid.',
            'lng.required' => 'Longitude tidak boleh kosong.',
            'lng.numeric' => 'Longitude tidak valid.',
            'lng.min' => 'Longitude tidak valid.',
            'lng.max' => 'Longitude tidak valid.',
        ]);
        $nowDate = new DateTime();

        $record = Record::where('date', $nowDate->format('Y-m-d'))
            ->with('attend')
            ->where('user_id', auth()->user()->id)
            ->first();

        if ($record && $record->attend && $record->attend->clock_in_time != null) {
            return response()->json(['message' => 'Anda sudah absen masuk.'], 422);
        }

        $school = School::find(auth()->user()->school_id)->first();

        $distance = $this->haversineGreatCircleDistance($school->lat, $school->lng, $data['lat'], $data['lng']);
        if ($distance > $school->distance) {
            return response()->json(['message' => 'Anda berada di luar area sekolah.'], 422);
        }

        $clockInTime = strtotime($school->clock_in);
        $limitTime = '05:00:00';
        $nowTime = strtotime($nowDate->format('H:i:s'));

        if ($nowTime < strtotime($limitTime)) {
            return response()->json(['message' => 'Anda bisa absensi setelah jam ' . $limitTime . '.'], 422);
        }

        $status = null;
        $message = "";

        if ($nowTime <= $clockInTime) {
            $status = 'ON_TIME';
            $message = "Presensi berhasil. Anda masuk tepat waktu.";
        } else {
            $status = 'LATE';
            $message = "Presensi berhasil. Anda terlambat masuk.";
        }

        $updateData = [
            'clock_in_time' => $nowDate->format('H:i:s'),
            'clock_in_lat' => $data['lat'],
            'clock_in_lng' => $data['lng'],
            'clock_in_status' => $status,
        ];

        if ($record) {
            if ($record->attend == null) {
                $record = $record->attend()->create($updateData);
            } else {
                $record = $record->attend()->update($updateData);
            }
        } else {
            $createdData = [
                'user_id' => auth()->user()->id,
                'date' => $nowDate->format('Y-m-d'),
                'is_leave' => 0,
            ];
            $record = Record::create($createdData);
            $record->attend()->create($updateData);
        }

        return response()->json([
            'message' => $message,
            'clock' => $nowDate->format('Y-m-d H:i:s'),
        ]);
    }

    public function clockOut(Request $request)
    {
        $data = $request->validate([
            'lat' => 'nullable|numeric|min:-90|max:90',
            'lng' => 'nullable|numeric|min:-180|max:180',
        ], [
            'lat.numeric' => 'Latitude tidak valid.',
            'lat.min' => 'Latitude tidak valid.',
            'lat.max' => 'Latitude tidak valid.',
            'lng.numeric' => 'Longitude tidak valid.',
            'lng.min' => 'Longitude tidak valid.',
            'lng.max' => 'Longitude tidak valid.',
        ]);
        $nowDate = new DateTime();

        $record = Record::where('date', $nowDate->format('Y-m-d'))
            ->where('user_id', auth()->user()->id)
            ->with('attend')
            ->first();

        $school = School::find(auth()->user()->school_id)->first();
        $status = null;
        $message = "Presensi berhasil.";

        $distance = $this->haversineGreatCircleDistance($school->lat, $school->lng, $data['lat'], $data['lng']);
        if ($distance > $school->distance) {
            return response()->json(['message' => 'Anda berada di luar area sekolah.'], 422);
        }

        $limitTime = '22:00:00';
        $nowTime = strtotime($nowDate->format('H:i:s'));

        if ($nowTime > strtotime($limitTime)) {
            return response()->json(['message' => 'Anda bisa absensi sebelum jam ' . $limitTime . '.'], 422);
        }

        if ($record) {
            $record = $record->attend()->update([
                'clock_out_time' => $nowDate->format('H:i:s'),
                'clock_out_lat' => $data['lat'],
                'clock_out_lng' => $data['lng'],
            ]);
        } else {
            $createdData = [
                'user_id' => auth()->user()->id,
                'date' => $nowDate->format('Y-m-d'),
                'is_leave' => 0,
            ];
            $record = Record::create($createdData);
            $record->attend()->create([
                'clock_out_time' => $nowDate->format('H:i:s'),
                'clock_out_lat' => $data['lat'],
                'clock_out_lng' => $data['lng'],
            ]);
        }

        return response()->json([
            'message' => $message,
            'clock' => $nowDate->format('Y-m-d H:i:s'),
        ]);
    }

    public function history(Request $request)
    {
        $now = new DateTime('now');
        $month = $now->format('m');
        $year = $now->format('Y');

        if ($request->has('month')) {
            $month = $request->input('month');
        }
        if ($request->has('year')) {
            $year = $request->input('year');
        }

        $now->setDate($year, $month, 1);
        $record_array = [];
        for ($i = 0; $i < $now->format('t'); $i++) {
            array_push($record_array, null);
        }

        $records = Record::where('user_id', auth()->user()->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->with('attend')
            ->with('leave')
            ->get();

        foreach ($records as $value) {
            $value_date = strtotime($value['date']);
            $record_array[(int)date('d', $value_date) - 1] = $value;
        }

        return response()->json($record_array);
    }

    public function clockStatus()
    {
        $now = new DateTime('now');

        $records = Record::where('user_id', auth()->user()->id)
            ->whereDate('date', '=', $now->format('Y-m-d'))
            ->with('attend')
            ->with('leave')
            ->first();

        return response()->json($records);
    }
}
