<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendancePoint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MapController extends Controller
{
    /**
     * Get all active attendance points with GPS coordinates.
     */
    public function getAttendancePoints(Request $request): JsonResponse
    {
        $query = AttendancePoint::with('projek:id,name,code')
            ->where('is_active', true);

        if ($request->filled('projek_id')) {
            $query->where('projek_id', $request->projek_id);
        }

        $points = $query->get()->map(function ($point) {
            return [
                'id' => $point->id,
                'name' => $point->name,
                'projek_id' => $point->projek_id,
                'projek_name' => $point->projek?->name ?? 'Lokasi Umum',
                'projek_code' => $point->projek?->code,
                'latitude' => (float) $point->latitude,
                'longitude' => (float) $point->longitude,
                'radius_meters' => (int) $point->radius_meters,
                'address' => $point->address,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $points,
        ]);
    }

    /**
     * Get details of a single attendance point.
     */
    public function showPoint(AttendancePoint $attendancePoint): JsonResponse
    {
        $attendancePoint->load('projek:id,name,code');

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $attendancePoint->id,
                'name' => $attendancePoint->name,
                'projek_id' => $attendancePoint->projek_id,
                'projek_name' => $attendancePoint->projek?->name,
                'latitude' => (float) $attendancePoint->latitude,
                'longitude' => (float) $attendancePoint->longitude,
                'radius_meters' => (int) $attendancePoint->radius_meters,
                'address' => $attendancePoint->address,
            ],
        ]);
    }

    /**
     * Validate whether current user GPS location is inside the geofence radius using Haversine formula.
     */
    public function validateLocation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'attendance_point_id' => ['required', 'exists:attendance_points,id'],
        ]);

        $point = AttendancePoint::with('projek:id,name')->findOrFail($validated['attendance_point_id']);

        $distance = $this->calculateHaversineDistance(
            (float) $validated['latitude'],
            (float) $validated['longitude'],
            (float) $point->latitude,
            (float) $point->longitude
        );

        $isWithinRadius = $distance <= $point->radius_meters;

        return response()->json([
            'status' => 'success',
            'is_within_radius' => $isWithinRadius,
            'distance_meters' => round($distance, 2),
            'radius_meters' => (int) $point->radius_meters,
            'message' => $isWithinRadius
                ? 'Posisi Anda berada di dalam zona radius presensi ('.round($distance, 1).'m dari titik pusat).'
                : 'Posisi Anda di luar radius presensi (Jarak: '.round($distance, 1).'m, Radius maksimum: '.$point->radius_meters.'m).',
            'point' => [
                'id' => $point->id,
                'name' => $point->name,
                'latitude' => (float) $point->latitude,
                'longitude' => (float) $point->longitude,
                'radius_meters' => (int) $point->radius_meters,
            ],
        ]);
    }

    /**
     * Calculate distance between two GPS coordinates in meters using the Haversine formula.
     */
    protected function calculateHaversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // in meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}

