<?php

namespace App\Http\Controllers;

use App\Services\DeviceApiService;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    protected $deviceApiService;

    public function __construct(DeviceApiService $deviceApiService)
    {
        $this->deviceApiService = $deviceApiService;
    }

    public function index()
    {
        return view('devices.index');
    }

    public function getDeviceData(Request $request)
    {
        $request->validate([
            'imei' => 'required|string'
        ]);

        try {
            $deviceData = $this->deviceApiService->getDeviceUpdate($request->imei);
            return response()->json($deviceData);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getMultipleDevices(Request $request)
    {
        $request->validate([
            'imeis' => 'required|string'
        ]);

        // Aceita IMEIs separados por ; ou por linha
        $imeis = preg_split('/[;\n\r]+/', $request->imeis);
        $imeis = array_filter(array_map('trim', $imeis));

        $results = [];
        foreach ($imeis as $imei) {
            try {
                $data = $this->deviceApiService->getDeviceUpdate($imei);
                // Quebra os campos 'log' e 'selfCheckParam' em subcampos
                if (isset($data['log']) && is_string($data['log'])) {
                    $data['log'] = json_decode($data['log'], true) ?? $data['log'];
                }
                if (isset($data['selfCheckParam']) && is_string($data['selfCheckParam'])) {
                    $data['selfCheckParam'] = json_decode($data['selfCheckParam'], true) ?? $data['selfCheckParam'];
                }
                $results[] = [
                    'imei' => $imei,
                    'data' => $data
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'imei' => $imei,
                    'data' => null
                ];
            }
        }
        return response()->json($results);
    }
} 