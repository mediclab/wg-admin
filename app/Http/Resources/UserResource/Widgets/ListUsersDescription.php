<?php

namespace App\Http\Resources\UserResource\Widgets;

use Filament\Widgets\Widget;

class ListUsersDescription extends Widget
{
    protected static string $view = 'widgets.user-page-description';

    protected int | string | array $columnSpan = 'full';
}
