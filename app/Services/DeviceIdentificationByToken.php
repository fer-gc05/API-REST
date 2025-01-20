<?php

namespace App\Services;

use App\Models\Device;


class DeviceIdentificationByToken
{
    public function getDeviceId($deviceToken)
    {
        $device = Device::where('token', $deviceToken)->first();

        if (!$device) {
            return null;
        }

        return $device->id;
    }
}