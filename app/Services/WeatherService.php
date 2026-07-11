<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class WeatherService
{
    protected Client $client;
    protected string $apiKey;
    protected string $baseUrl = 'https://api.openweathermap.org/data/2.5/';

    public function __construct()
    {
        $this->client = new Client(['timeout' => 5]);
        $this->apiKey = config('services.openweather.key');
    }

    public function getForecast(string $city, string $date): ?array
    {
        try {
            $response = $this->client->get($this->baseUrl . 'forecast', [
                'query' => [
                    'q'     => $city . ',BD',
                    'appid' => $this->apiKey,
                    'units' => 'metric',
                    'cnt'   => 40,
                ]
            ]);

            $data      = json_decode($response->getBody(), true);
            $eventDate = strtotime($date);
            $closest   = null;
            $minDiff   = PHP_INT_MAX;

            foreach ($data['list'] as $forecast) {
                $diff = abs($forecast['dt'] - $eventDate);
                if ($diff < $minDiff) {
                    $minDiff = $diff;
                    $closest = $forecast;
                }
            }

            if (!$closest) return null;

            return [
                'temp'        => round($closest['main']['temp']),
                'feels_like'  => round($closest['main']['feels_like']),
                'humidity'    => $closest['main']['humidity'],
                'description' => ucfirst($closest['weather'][0]['description']),
                'icon'        => $closest['weather'][0]['icon'],
                'icon_url'    => 'https://openweathermap.org/img/wn/' . $closest['weather'][0]['icon'] . '@2x.png',
                'wind_speed'  => round($closest['wind']['speed'] * 3.6),
                'city'        => $data['city']['name'],
            ];

        } catch (RequestException $e) {
            return null;
        }
    }

    public function getCurrentWeather(string $city): ?array
    {
        try {
            $response = $this->client->get($this->baseUrl . 'weather', [
                'query' => [
                    'q'     => $city . ',BD',
                    'appid' => $this->apiKey,
                    'units' => 'metric',
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            return [
                'temp'        => round($data['main']['temp']),
                'feels_like'  => round($data['main']['feels_like']),
                'humidity'    => $data['main']['humidity'],
                'description' => ucfirst($data['weather'][0]['description']),
                'icon_url'    => 'https://openweathermap.org/img/wn/' . $data['weather'][0]['icon'] . '@2x.png',
                'wind_speed'  => round($data['wind']['speed'] * 3.6),
                'city'        => $data['name'],
            ];

        } catch (RequestException $e) {
            return null;
        }
    }
}