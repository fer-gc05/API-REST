<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeviceController extends Controller
{
    public function getDevices()
    {
        $devices = Device::all();

        if ($devices->isEmpty()) {
            return response()->json(['message' => 'No devices found'], 404);
        }

        return response()->json($devices, 200);
    }

    public function getDevice($id)
    {
        $device = Device::find($id);

        if (!$device) {
            return response()->json(['message' => 'Device not found'], 404);
        }

        return response()->json($device, 200);
    }

    public function createDevice(Request $request)
    {
        $validatedData = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|min:10|max:100',
                'location' => 'required|string|min:10|max:100'
            ]
        );

        if ($validatedData->fails()) {
            return response()->json($validatedData->errors(), 422);
        }

        Device::create([
            'name' => $request->name,
            'location' => $request->location,
            'token' => bin2hex(random_bytes(16)),
        ]);

        return response()->json(['message' => 'Device created successfully'], 201);
    }

    public function updateDevice(Request $request, $id)
    {
        $device = Device::find($id);

        if (!$device) {
            return response()->json(['message' => 'Device not found'], 404);
        }

        $validatedData = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|min:10|max:100',
                'location' => 'required|string|min:10|max:100'
            ]
        );

        if ($validatedData->fails()) {
            return response()->json($validatedData->errors(), 422);
        }

        $device->update([
            'name' => $request->name,
            'location' => $request->location
        ]);

        return response()->json(['message' => 'Device updated successfully'], 200);
    }

    public function deleteDevice($id)
    {
        $device = Device::find($id);

        if (!$device) {
            return response()->json(['message' => 'Device not found'], 404);
        }

        $device->delete();

        return response()->json(['message' => 'Device deleted successfully'], 200);
    }

    public function activateDevice($token)
    {
        $device = Device::where('token', $token)->first();

        if (!$device) {
            return response()->json(['message' => 'Device not found'], 404);
        }

        $device->update(['status' => 'Activo']);

        return response()->json(['message' => 'Device activated successfully'], 200);
    }

    public function deactivateDevice($token)
    {
        $device = Device::where('token', $token)->first();

        if (!$device) {
            return response()->json(['message' => 'Device not found'], 404);
        }

        $device->update(['status' => 'Inactivo']);

        return response()->json(['message' => 'Device deactivated successfully'], 200);
    }

    public function getDeviceByToken($token)
    {
        $device = Device::where('token', $token)->first();

        if (!$device) {
            return response()->json(['message' => 'Device not found'], 404);
        }

        return response()->json(['status' => $device->status], 200);
    }
}
