<?php

namespace App\Service;

use App\Service\GeocodingService;

class DistanceCalculator{
    public function calculateDistance($latitude1, $longitude1, $latitude2, $longitude2){

        $earthRadius = 6371; // Rayon de la Terre en km

        $lat1Rad = deg2rad($latitude1);
        $lon1Rad = deg2rad($longitude1);
        $lat2Rad = deg2rad($latitude2);
        $lon2Rad = deg2rad($longitude2);

        $deltaLat = $lat2Rad - $lat1Rad;
        $deltaLon = $lon2Rad - $lon1Rad;

        $a = sin($deltaLat / 2) ** 2 +
            cos($lat1Rad) * cos($lat2Rad) * sin($deltaLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}