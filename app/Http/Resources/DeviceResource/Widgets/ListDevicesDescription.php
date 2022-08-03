<?php

namespace App\Http\Resources\DeviceResource\Widgets;

use Filament\Widgets\Widget;

class ListDevicesDescription extends Widget
{
    protected static string $view = 'widgets.device-page-description';

    protected int | string | array $columnSpan = 'full';
}
