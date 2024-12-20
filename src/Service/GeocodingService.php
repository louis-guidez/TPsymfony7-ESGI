<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeocodingService
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getCoordinates(string $city): ?array
    {
        $url = sprintf(
            'https://nominatim.openstreetmap.org/search?city=%s&format=json&limit=1',
            urlencode($city)
        );

        $response = $this->httpClient->request('GET', $url);

        if ($response->getStatusCode() === 200) {
            $data = $response->toArray();
            if (!empty($data)) {
                return [
                    'latitude' => (float) $data[0]['lat'],
                    'longitude' => (float) $data[0]['lon'],
                ];
            }
        }

        return null;
    }
}
