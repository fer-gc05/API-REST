<?php

namespace App\Http\Controllers;

use App\Models\SensorReading;
use App\Services\DeviceIdentificationByToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Sensor Readings",
 *     description="API Endpoints for managing sensor readings from IoT devices"
 * )
 *
 * @OA\Schema(
 *     schema="SensorReadingResponse",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="device_id", type="integer", example=1),
 *     @OA\Property(property="temperature", type="number", format="float", example=25.5),
 *     @OA\Property(property="humidity", type="number", format="float", example=60.0),
 *     @OA\Property(property="smoke_level", type="number", format="float", example=10.0),
 *     @OA\Property(property="gas_level", type="number", format="float", example=15.0),
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

class SensorReadingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/readings",
     *     summary="Get all sensor readings",
     *     description="Returns a list of all sensor readings with their associated device information",
     *     tags={"Sensor Readings"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of sensor readings retrieved successfully",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/SensorReadingResponse"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No sensor readings found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="No sensor readings found"))
     *     )
     * )
     */

    public function getSensorReadings()
    {
        $sensorReadings = SensorReading::with('device:id,name,location')->get();

        if ($sensorReadings->isEmpty()) {
            return response()->json(['message' => 'No sensor readings found'], 404);
        }

        return response()->json($sensorReadings, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/readings/{id}",
     *     summary="Get a single sensor reading",
     *     description="Returns detailed information about a specific sensor reading",
     *     tags={"Sensor Readings"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the sensor reading to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sensor reading details retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/SensorReadingResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Sensor reading not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Sensor reading not found"))
     *     )
     * )
     */

    public function getSensorReading($id)
    {
        $sensorReading = SensorReading::with('device:id,name,location')->find($id);

        if (!$sensorReading) {
            return response()->json(['message' => 'Sensor reading not found'], 404);
        }

        return response()->json($sensorReading, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/readings",
     *     summary="Create a new sensor reading",
     *     description="Creates a new sensor reading with the provided measurements",
     *     tags={"Sensor Readings"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Sensor reading data",
     *         @OA\JsonContent(
     *             required={"device_token", "temperature", "humidity", "smoke_level", "gas_level"},
     *             @OA\Property(
     *                 property="device_token",
     *                 type="string",
     *                 example="abc123",
     *                 description="Token of the device making the reading"
     *             ),
     *             @OA\Property(
     *                 property="temperature",
     *                 type="number",
     *                 format="float",
     *                 example=25.5,
     *                 description="Temperature reading in Celsius"
     *             ),
     *             @OA\Property(
     *                 property="humidity",
     *                 type="number",
     *                 format="float",
     *                 example=60.0,
     *                 description="Humidity percentage"
     *             ),
     *             @OA\Property(
     *                 property="smoke_level",
     *                 type="number",
     *                 format="float",
     *                 example=10.0,
     *                 description="Smoke level reading"
     *             ),
     *             @OA\Property(
     *                 property="gas_level",
     *                 type="number",
     *                 format="float",
     *                 example=15.0,
     *                 description="Gas level reading"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Sensor reading created successfully",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Sensor reading created successfully"))
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="device_token", type="array", @OA\Items(type="string"), example={"The device token is required"}),
     *             @OA\Property(property="temperature", type="array", @OA\Items(type="string"), example={"The temperature must be a number"}),
     *             @OA\Property(property="humidity", type="array", @OA\Items(type="string"), example={"The humidity must be a number"}),
     *             @OA\Property(property="smoke_level", type="array", @OA\Items(type="string"), example={"The smoke level must be a number"}),
     *             @OA\Property(property="gas_level", type="array", @OA\Items(type="string"), example={"The gas level must be a number"})
     *         )
     *     )
     * )
     */

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

    /**
     * @OA\Put(
     *     path="/api/readings/{id}",
     *     summary="Update a sensor reading",
     *     description="Updates an existing sensor reading with new measurements",
     *     tags={"Sensor Readings"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the sensor reading to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated sensor reading data",
     *         @OA\JsonContent(
     *             required={"temperature", "humidity", "smoke_level", "gas_level"},
     *             @OA\Property(property="temperature", type="number", format="float", example=26.5),
     *             @OA\Property(property="humidity", type="number", format="float", example=65.0),
     *             @OA\Property(property="smoke_level", type="number", format="float", example=12.0),
     *             @OA\Property(property="gas_level", type="number", format="float", example=18.0)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sensor reading updated successfully",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Sensor reading updated successfully"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Sensor reading not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Sensor reading not found"))
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="temperature", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="humidity", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="smoke_level", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="gas_level", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
     */

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

    /**
     * @OA\Delete(
     *     path="/api/readings/{id}",
     *     summary="Delete a sensor reading",
     *     description="Deletes an existing sensor reading",
     *     tags={"Sensor Readings"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the sensor reading to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sensor reading deleted successfully",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Sensor reading deleted successfully"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Sensor reading not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Sensor reading not found"))
     *     )
     * )
     */

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
