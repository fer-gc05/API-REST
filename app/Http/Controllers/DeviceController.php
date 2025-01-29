<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Devices",
 *     description="API Endpoints for managing IoT devices"
 * )
 *
 * @OA\Schema(
 *     schema="DeviceResponse",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Sensor Temperatura Cocina"),
 *     @OA\Property(property="location", type="string", example="Cocina Principal"),
 *     @OA\Property(property="token", type="string", example="a1b2c3d4e5f6g7h8i9j0"),
 *     @OA\Property(property="status", type="string", enum={"Activo","Inactivo"}, example="Activo"),
 *     @OA\Property(property="created_at", type="string", format="datetime", example="2024-01-29T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="datetime", example="2024-01-29T12:00:00Z")
 * )
 */

class DeviceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/devices",
     *     summary="Get all devices",
     *     description="Returns a list of all registered devices",
     *     tags={"Devices"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of devices retrieved successfully",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/DeviceResponse"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No devices found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="No devices found"))
     *     )
     * )
     */

    public function getDevices()
    {
        $devices = Device::all();

        if ($devices->isEmpty()) {
            return response()->json(['message' => 'No devices found'], 404);
        }

        return response()->json($devices, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/devices/{id}",
     *     summary="Get a single device",
     *     description="Returns detailed information about a specific device",
     *     tags={"Devices"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the device to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Device details retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/DeviceResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Device not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Device not found"))
     *     )
     * )
     */

    public function getDevice($id)
    {
        $device = Device::find($id);

        if (!$device) {
            return response()->json(['message' => 'Device not found'], 404);
        }

        return response()->json($device, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/devices",
     *     summary="Create a new device",
     *     description="Creates a new device with the provided information and generates a unique token",
     *     tags={"Devices"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Device creation data",
     *         @OA\JsonContent(
     *             required={"name", "location"},
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="Sensor Temperatura Cocina",
     *                 description="Name of the device (min: 10, max: 100 characters)"
     *             ),
     *             @OA\Property(
     *                 property="location",
     *                 type="string",
     *                 example="Cocina Principal",
     *                 description="Location of the device (min: 10, max: 100 characters)"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Device created successfully",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Device created successfully"))
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="array", @OA\Items(type="string"), example={"The name must be at least 10 characters"}),
     *             @OA\Property(property="location", type="array", @OA\Items(type="string"), example={"The location must be at least 10 characters"})
     *         )
     *     )
     * )
     */

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

    /**
     * @OA\Put(
     *     path="/api/devices/{id}",
     *     summary="Update a device",
     *     description="Updates an existing device with the provided information",
     *     tags={"Devices"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the device to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Device update data",
     *         @OA\JsonContent(
     *             required={"name", "location"},
     *             @OA\Property(property="name", type="string", example="Sensor Temperatura Sala"),
     *             @OA\Property(property="location", type="string", example="Sala Principal")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Device updated successfully",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Device updated successfully"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Device not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Device not found"))
     *     ),
     *     @OA\Response(response=422, description="Validation errors")
     * )
     */

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

    /**
     * @OA\Delete(
     *     path="/api/devices/{id}",
     *     summary="Delete a device",
     *     description="Deletes an existing device",
     *     tags={"Devices"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the device to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Device deleted successfully",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Device deleted successfully"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Device not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Device not found"))
     *     )
     * )
     */

    public function deleteDevice($id)
    {
        $device = Device::find($id);

        if (!$device) {
            return response()->json(['message' => 'Device not found'], 404);
        }

        $device->delete();

        return response()->json(['message' => 'Device deleted successfully'], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/devices/activate/{token}",
     *     summary="Activate a device",
     *     description="Activates a device using its unique token",
     *     tags={"Devices"},
     *     @OA\Parameter(
     *         name="token",
     *         in="path",
     *         required=true,
     *         description="Token of the device to activate",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Device activated successfully",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Device activated successfully"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Device not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Device not found"))
     *     )
     * )
     */
    public function activateDevice($token)
    {
        $device = Device::where('token', $token)->first();

        if (!$device) {
            return response()->json(['message' => 'Device not found'], 404);
        }

        $device->update(['status' => 'Activo']);

        return response()->json(['message' => 'Device activated successfully'], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/devices/deactivate/{token}",
     *     summary="Deactivate a device",
     *     description="Deactivates a device using its unique token",
     *     tags={"Devices"},
     *     @OA\Parameter(
     *         name="token",
     *         in="path",
     *         required=true,
     *         description="Token of the device to deactivate",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Device deactivated successfully",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Device deactivated successfully"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Device not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Device not found"))
     *     )
     * )
     */

    public function deactivateDevice($token)
    {
        $device = Device::where('token', $token)->first();

        if (!$device) {
            return response()->json(['message' => 'Device not found'], 404);
        }

        $device->update(['status' => 'Inactivo']);

        return response()->json(['message' => 'Device deactivated successfully'], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/devices/status/{token}",
     *     summary="Get device status by token",
     *     description="Returns the status of a device using its unique token",
     *     tags={"Devices"},
     *     @OA\Parameter(
     *         name="token",
     *         in="path",
     *         required=true,
     *         description="Token of the device to check",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Device status retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", enum={"Activo","Inactivo"}, example="Activo")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Device not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Device not found"))
     *     )
     * )
     */

    public function getDeviceByToken($token)
    {
        $device = Device::where('token', $token)->first();

        if (!$device) {
            return response()->json(['message' => 'Device not found'], 404);
        }

        return response()->json(['status' => $device->status], 200);
    }
}
