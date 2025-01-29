<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Services\DeviceIdentificationByToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Alerts",
 *     description="API Endpoints for managing alerts from devices"
 * )
 *
 * @OA\Schema(
 *     schema="AlertResponse",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="device_id", type="integer", example=1),
 *     @OA\Property(property="type", type="string", enum={"Temperatura","Humedad","Nivel de humo","Nivel de gas"}, example="Temperatura"),
 *     @OA\Property(property="value", type="number", format="float", example=45.5),
 *     @OA\Property(property="max_value", type="number", format="float", example=50.0),
 *     @OA\Property(property="created_at", type="string", format="datetime", example="2024-01-29T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="datetime", example="2024-01-29T12:00:00Z"),
 *     @OA\Property(
 *         property="device",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Sensor Cocina"),
 *         @OA\Property(property="location", type="string", example="Cocina Principal")
 *     )
 * )
 */

class AlertController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/alerts",
     *     summary="Get all alerts",
     *     description="Returns a list of all alerts with their associated device information",
     *     tags={"Alerts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of alerts retrieved successfully",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/AlertResponse"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No alerts found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="No alerts found"))
     *     )
     * )
     */

    public function getAlerts()
    {
        $alerts = Alert::with('device:id,name,location')->get();

        if ($alerts->isEmpty()) {
            return response()->json(['message' => 'No alerts found'], 404);
        }

        return response()->json($alerts, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/alerts/{id}",
     *     summary="Get a single alert",
     *     description="Returns detailed information about a specific alert",
     *     tags={"Alerts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the alert to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Alert details retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/AlertResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Alert not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Alert not found"))
     *     )
     * )
     */

    public function getAlert($id)
    {
        $alert = Alert::with('device:id,name,location')->find($id);

        if (!$alert) {
            return response()->json(['message' => 'Alert not found'], 404);
        }

        return response()->json($alert, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/alerts",
     *     summary="Create a new alert",
     *     description="Creates a new alert with the provided information",
     *     tags={"Alerts"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Alert creation data",
     *         @OA\JsonContent(
     *             required={"device_token", "type", "value", "max_value"},
     *             @OA\Property(property="device_token", type="string", example="abc123", description="Token of the device"),
     *             @OA\Property(
     *                 property="type",
     *                 type="string",
     *                 enum={"Temperatura","Humedad","Nivel de humo","Nivel de gas"},
     *                 example="Temperatura",
     *                 description="Type of the alert"
     *             ),
     *             @OA\Property(property="value", type="number", format="float", example=45.5, description="Current value"),
     *             @OA\Property(property="max_value", type="number", format="float", example=50.0, description="Maximum allowed value")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Alert created successfully",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Alert created successfully"))
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="device_token", type="array", @OA\Items(type="string"), example={"The device token is required"}),
     *             @OA\Property(property="type", type="array", @OA\Items(type="string"), example={"The type must be one of: Temperatura, Humedad, Nivel de humo, Nivel de gas"}),
     *             @OA\Property(property="value", type="array", @OA\Items(type="string"), example={"The value must be a number"}),
     *             @OA\Property(property="max_value", type="array", @OA\Items(type="string"), example={"The max value must be a number"})
     *         )
     *     )
     * )
     */

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

    /**
     * @OA\Put(
     *     path="/api/alerts/{id}",
     *     summary="Update an alert",
     *     description="Updates an existing alert with the provided information",
     *     tags={"Alerts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the alert to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Alert update data",
     *         @OA\JsonContent(
     *             required={"type", "value", "max_value"},
     *             @OA\Property(
     *                 property="type",
     *                 type="string",
     *                 enum={"Temperatura","Humedad","Nivel de humo","Nivel de gas"},
     *                 example="Humedad",
     *                 description="Type of the alert"
     *             ),
     *             @OA\Property(property="value", type="number", format="float", example=60.0, description="Current value"),
     *             @OA\Property(property="max_value", type="number", format="float", example=70.0, description="Maximum allowed value")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Alert updated successfully",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Alert updated successfully"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Alert not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Alert not found"))
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="type", type="array", @OA\Items(type="string"), example={"The type must be one of: Temperatura, Humedad, Nivel de humo, Nivel de gas"}),
     *             @OA\Property(property="value", type="array", @OA\Items(type="string"), example={"The value must be a number"}),
     *             @OA\Property(property="max_value", type="array", @OA\Items(type="string"), example={"The max value must be a number"})
     *         )
     *     )
     * )
     */

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

    /**
     * @OA\Delete(
     *     path="/api/alerts/{id}",
     *     summary="Delete an alert",
     *     description="Deletes an existing alert",
     *     tags={"Alerts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the alert to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Alert deleted successfully",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Alert deleted successfully"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Alert not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Alert not found"))
     *     )
     * )
     */

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
