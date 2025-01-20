<?php

namespace App\Http\Controllers;

use App\Models\SensorReading;
use App\Services\DeviceIdentificationByToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SensorReadingController extends Controller
{
    public function getSensorReadings()
    {
        $sensorReadings = SensorReading::with('device:id,name,location')->get();

        if ($sensorReadings->isEmpty()) {
            return response()->json(['message' => 'No sensor readings found'], 404);
        }

        return response()->json($sensorReadings, 200);
    }

    public function getSensorReading($id)
    {
        $sensorReading = SensorReading::with('device:id,name,location')->find($id);

        if (!$sensorReading) {
            return response()->json(['message' => 'Sensor reading not found'], 404);
        }

        return response()->json($sensorReading, 200);
    }

    public function createSensorReading(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'device_token' => 'required|string',
            'temperature' => 'required|numeric',
            'humidity' => 'required|numeric',
            'smoke_level' => 'required|numeric',
            'gas_level' => 'required|numeric'
        ]);

        if ($validatedData->fails()) {
            return response()->json($validatedData->errors(), 422);
        }

        $deviceIdentification = new DeviceIdentificationByToken();
        $deviceId = $deviceIdentification->getDeviceId($request->device_token);

        SensorReading::create([
            'device_id' => $deviceId, 
            'temperature' => $request->temperature,
            'humidity' => $request->humidity,
            'smoke_level' => $request->smoke_level,
            'gas_level' => $request->gas_level
        ]);

        return response()->json(['message' => 'Sensor reading created successfully'], 201);
    }

    public function updateSensorReading(Request $request, $id)
    {
        $sensorReading = SensorReading::find($id);

        if (!$sensorReading) {
            return response()->json(['message' => 'Sensor reading not found'], 404);
        }

        $validatedData = Validator::make($request->all(), [
            'temperature' => 'required|numeric',
            'humidity' => 'required|numeric',
            'smoke_level' => 'required|numeric',
            'gas_level' => 'required|numeric'
        ]);

        if ($validatedData->fails()) {
            return response()->json($validatedData->errors(), 422);
        }

        $sensorReading->update([
            'device_id' => $id,
            'temperature' => $request->temperature,
            'humidity' => $request->humidity,
            'smoke_level' => $request->smoke_level,
            'gas_level' => $request->gas_level
        ]);

        return response()->json(['message' => 'Sensor reading updated successfully'], 200);
    }

    public function deleteSensorReading($id)
    {
        $sensorReading = SensorReading::find($id);

        if (!$sensorReading) {
            return response()->json(['message' => 'Sensor reading not found'], 404);
        }

        $sensorReading->delete();

        return response()->json(['message' => 'Sensor reading deleted successfully'], 200);
    }

}
