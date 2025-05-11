<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DeviceApiService
{
    protected $baseUrl;
    protected $appKey;
    protected $secret;

    public function __construct()
    {
        $this->baseUrl = config('services.device_api.url');
        $this->appKey = config('services.device_api.app_key');
        $this->secret = config('services.device_api.secret');
    }

    protected function getToken()
    {
        return Cache::remember('device_api_token', 25 * 60, function () {
            $response = Http::post($this->baseUrl . '/token', [
                'appKey' => $this->appKey,
                'secret' => $this->secret
            ]);

            if ($response->successful()) {
                $token = $response->json()['data']['token'];
                Log::info('Novo token gerado para API externa', ['token' => $token]);
                return $token;
            }

            Log::error('Falha ao obter token da API externa', ['response' => $response->body()]);
            throw new \Exception('Failed to get API token');
        });
    }

    public function getDeviceUpdate($imei)
    {
        $token = $this->getToken();
        Log::info('Enviando requisição para /queryDeviceStatus com token', ['token' => $token]);

        $response = Http::withHeaders([
            'Authorization' => $token
        ])->post($this->baseUrl . '/queryDeviceStatus', [
            'imeiList' => [$imei]
        ]);

        if ($response->successful()) {
            return $response->json()['data'][0] ?? null;
        }

        Log::error('Falha ao obter dados do dispositivo', ['imei' => $imei, 'response' => $response->body()]);
        throw new \Exception('Failed to get device update');
    }
} 