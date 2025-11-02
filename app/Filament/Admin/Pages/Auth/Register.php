<?php

namespace App\Filament\Admin\Pages\Auth;

use App\Models\Role;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Database\Eloquent\Model;

class Register extends BaseRegister
{
    protected function handleRegistration(array $data): Model
    {
        $user = parent::handleRegistration($data);

        // Assign Guest role to new users
        $guestRole = Role::where('slug', 'guest')->first();
        if ($guestRole) {
            $user->roles()->attach($guestRole);
        }

        return $user;
    }
}
