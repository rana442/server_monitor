<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected array $deviceGroups = [
        'AC-Power',
        'Camera',
        'Core Device',
        'Corporate',
        'Fiber Path',
        'Mikrotik',
        'OLT',
        'Switch',
        'Server',
        'Upstream',
        'Vendor',
    ];
    
    protected array $deviceGroupColors = [
        'AC-Power'    => 'secondary',
        'Camera'      => 'warning',
        'Core Device' => 'primary',
        'Corporate'   => 'info',
        'Fiber Path'   => 'danger',
        'Mikrotik'    => 'success',
        'OLT'         => 'secondary',
        'Switch'      => 'warning',
        'Server'      => 'primary',
        'Upstream'    => 'info',
        'Vendor'    => 'danger',
    ];
}
