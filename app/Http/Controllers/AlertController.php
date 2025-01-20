<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Services\DeviceIdentificationByToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AlertController extends Controller
{
    public function getAlerts()
    {
        $alerts = Alert::with('device:id,name,location')->get();

        if ($alerts->isEmpty()) {
            return response()->json(['message' => 'No alerts found'], 404);
        }

        return response()->json($alerts, 200);
    }

    public function getAlert($id)
    {
        $alert = Alert::with('device:id,name,location')->find($id);

        if (!$alert) {
            return response()->json(['message' => 'Alert not found'], 404);
        }

        return response()->json($alert, 200);
    }

    public function createAlert(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'device_token' => 'required|string',
            'type' => 'required|string|in:Temperatura,Humedad,Nivel de humo,Nivel de gas',
            'value' => 'required|numeric',
            'max_value' => 'required|numeric'
        ]);

        if($validatedData->fails()) {
            return response()->json($validatedData->errors(), 422);
        }

        $deviceIdentification = new DeviceIdentificationByToken();
        $deviceId = $deviceIdentification->getDeviceId($request->device_token);
    
        Alert::create([
            'device_id' => $deviceId,
            'type' => $request->type,
            'value' => $request->value,
            'max_value' => $request->max_value
        ]);

        return response()->json(['message' => 'Alert created successfully'], 201);
    }

    public function updateAlert(Request $request, $id)
    {
        $alert = Alert::find($id);

        if (!$alert) {
            return response()->json(['message' => 'Alert not found'], 404);
        }

        $validatedData = Validator::make($request->all(), [
            'type' => 'required|string|in:Temperatura,Humedad,Nivel de humo,Nivel de gas',
            'value' => 'required|numeric',
            'max_value' => 'required|numeric'
        ]);

        if($validatedData->fails()) {
            return response()->json($validatedData->errors(), 422);
        }

        $alert->update([
            'device_id' => $id,
            'type' => $request->type,
            'value' => $request->value,
            'max_value' => $request->max_value
        ]);

        return response()->json(['message' => 'Alert updated successfully'], 200);
    }

    public function deleteAlert($id)
    {
        $alert = Alert::find($id);

        if (!$alert) {
            return response()->json(['message' => 'Alert not found'], 404);
        }

        $alert->delete();

        return response()->json(['message' => 'Alert deleted successfully'], 200);
    }
}
