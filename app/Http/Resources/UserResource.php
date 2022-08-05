<?php

namespace App\Http\Resources;

use App\Helpers\Util;
use App\Http\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use App\Rules\InIpRange;
use App\Validators\ServerValidator;
use App\Validators\UserValidator;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'Main';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('gravatar')
                    ->rounded()
                    ->label('')
                    ->getStateUsing(static fn (User $record) => $record->gravatar()),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('server.address')
                    ->label(__('Server')),
                Tables\Columns\TextColumn::make('server.port')
                    ->label(__('Port')),
                Tables\Columns\BooleanColumn::make('is_admin')
                    ->sortable()
                    ->label(__('Administrator')),
                Tables\Columns\BooleanColumn::make('is_active')
                    ->sortable()
                    ->label(__('Activity')),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_active')
                ->form([
                    Forms\Components\Toggle::make('is_active_toggle')
                        ->label(__('Active users only')),
                ])
                ->query(static function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['is_active_toggle'],
                            fn (Builder $query, $toggle): Builder => $query->where('is_active', (bool) $toggle),
                        );
                }),
                Tables\Filters\Filter::make('is_admin')
                    ->form([
                        Forms\Components\Toggle::make('is_admin_toggle')
                            ->label(__('Administrators only')),
                    ])
                    ->query(static function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['is_admin_toggle'],
                                fn (Builder $query, $toggle): Builder => $query->where('is_admin', (bool) $toggle),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make('delete')->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function form(Form $form): Form
    {
        $ports = Util::getAllowedPorts();
        $addresses = Util::getAllowedAddresses();

        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema(static::getInfoCardScheme($addresses, $ports))
                    ->columnSpan(1),
                Forms\Components\Card::make()
                    ->schema(static::getFormCardScheme($addresses, $ports))
                    ->columnSpan(2),
            ])
            ->columns(['sm' => 3, 'lg' => null]);
    }

    private static function getInfoCardScheme(array $addresses, array $ports): array
    {
        return [
            Forms\Components\Placeholder::make('server_subnet')
                ->label(__('Allowed server subnet:'))
                ->content($addresses['subnet']),
            Forms\Components\Placeholder::make('min_port')
                ->label(__('Minimal port:'))
                ->content($ports['min']),
            Forms\Components\Placeholder::make('max_port')
                ->label(__('Maximum port:'))
                ->content($ports['max']),
        ];
    }

    private static function getFormCardScheme(array $addresses, array $ports): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->label(__('Username'))
                ->required()
                ->rules(UserValidator::rules()['name']),
            Forms\Components\TextInput::make('email')
                ->label(__('Email'))
                ->required()
                ->rules(UserValidator::rules()['email']),
            Forms\Components\TextInput::make('password')
                ->label(__('Password'))
                ->password()
                ->required()
                ->rules(UserValidator::rules()['password']),
            Forms\Components\TextInput::make('password_confirmation')
                ->required()
                ->password()
                ->label(__('Confirm password')),
            Forms\Components\TextInput::make('server.address')
                ->label(__('Server address'))
                ->required()
                ->rules(ServerValidator::rules($addresses, $ports)['address']),
            Forms\Components\TextInput::make('server.port')
                ->label(__('Server port'))
                ->required()
                ->rules(ServerValidator::rules($addresses, $ports)['port']),
            Forms\Components\Toggle::make('is_admin')
                ->label(__('Administrator')),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
        ];
    }

    protected static function getNavigationBadge(): ?string
    {
        return static::$model::where('is_active', true)->count();
    }
}
