<?php

namespace App\Filament\Pages\Auth;

use App\Models\Location;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;



class Register extends BaseRegister
{
    // protected static ?string $model = User::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->unique()
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->same('passwordConfirmation'),
                TextInput::make('passwordConfirmation')
                    ->label('Confirm Password')
                    ->password()
                    ->required()
                    ->minLength(8),
            ]);
    }
    // protected function getUser(array $data): \Illuminate\Contracts\Auth\Authenticatable
    // {
    //     // $user = static::getUserModel()::create([
    //     //     'name' => $data['name'],
    //     //     'email' => $data['email'],
    //     //     'password' => Hash::make($data['password'])
    //     // ]);

    //     // $role = config('filament-shield.sales.name', 'sales');

    //     // $role =

    //     // $user->assignRole($role);

    //     return $user;
    // }
}
