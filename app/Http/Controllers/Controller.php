<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected array $deviceGroups = [
        'AC-Power',
        'Camera',
        'Core Device',
        'Corporate',
        'Mikrotik',
        'OLT',
        'Switch',
        'Server',
        'Upstream',
    ];
    
    protected array $deviceGroupColors = [
        'AC-Power'    => 'secondary',
        'Camera'      => 'warning',
        'Core Device' => 'primary',
        'Corporate'   => 'info',
        'Mikrotik'    => 'danger',
        'OLT'         => 'success',
        'Switch'      => 'info',
        'Server'      => 'light',
        'Upstream'    => 'secondary',
    ];
}
