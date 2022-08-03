<?php

declare(strict_types=1);

namespace App\Providers\AvatarProviders;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Illuminate\Database\Eloquent\Model;

class GravatarProvider implements AvatarProvider
{

    public function get(Model $user): string
    {
        return sprintf(
            'https://www.gravatar.com/avatar/%s?s=%d&d=%s&r=%s',
            md5(strtolower(trim($user->email))),
            80,
            'mp',
            'g'
        );
    }
}
