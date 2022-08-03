<?php

namespace App\Http\Resources;

use App\Helpers\Util;
use App\Http\Resources\DeviceResource\Pages\ListDevices;
use App\Models\Device;
use App\Rules\InIpRange;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class DeviceResource extends Resource
{
    protected static ?string $model = Device::class;

    protected static ?string $navigationGroup = 'Main';

    protected static ?string $navigationIcon = 'heroicon-o-device-mobile';

    public static function table(Table $table): Table
    {
        return $table
            ->columns(static::getTableColumns())
            ->filters([
                Tables\Filters\Filter::make('is_active')
                    ->form([
                        Forms\Components\Toggle::make('is_active_toggle')
                            ->label(__('Active devices only')),
                    ])
                    ->query(static function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['is_active_toggle'],
                                fn (Builder $query, $toggle): Builder => $query->where('is_active', (bool) $toggle),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('qr-code')
                    ->iconButton()
                    ->icon('heroicon-o-qrcode')
                    ->action('showQrCode')
                    ->visible(static fn (Device $record): bool => (bool) $record->is_active),
                Tables\Actions\Action::make('config-download')
                    ->iconButton()
                    ->icon('heroicon-o-download')
                    ->action('downloadConfig')
                    ->visible(static fn (Device $record): bool => (bool) $record->is_active),
                Tables\Actions\DeleteAction::make('delete')->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make('bulk-delete'),
            ]);
    }

    public static function getTableColumns(): array
    {
        return [
            Tables\Columns\BooleanColumn::make('isOnline')
                ->trueIcon('heroicon-o-status-online')
                ->falseIcon('heroicon-o-status-offline')
                ->trueColor('success')
                ->falseColor('danger')
                ->label(''),
            Tables\Columns\TextColumn::make('name')
                ->sortable()
                ->searchable()
                ->label(__('Device')),
            Tables\Columns\ViewColumn::make('server.user')
                ->view('tables.columns.user-column')
                ->label(__('User')),
            Tables\Columns\TextColumn::make('address')
                ->searchable()
                ->sortable()
                ->label(__('IP Address')),
            Tables\Columns\TextColumn::make('latestHandshakeAt')
                ->dateTime()
                ->formatStateUsing(static fn (?Carbon $state): string => null !== $state ? $state->diffForHumans() : __('never'))
                ->label(__('Last activity')),
            Tables\Columns\BooleanColumn::make('is_active')
                ->sortable()
                ->label(__('Status')),
            Tables\Columns\TextColumn::make('transferTx')
                ->formatStateUsing(static fn (?int $state): string => Util::bytesToHuman($state ?? 0))
                ->label(__('Upload')),
            Tables\Columns\TextColumn::make('transferRx')
                ->formatStateUsing(static fn (?int $state): string => Util::bytesToHuman($state ?? 0))
                ->label(__('Download')),
        ];
    }

    public static function form(Form $form): Form
    {
        $server = \Auth::user()->server;
        $minAddress = long2ip(ip2long($server->address) + 1);
        $maxAddress = long2ip(ip2long($server->address) + 249);

        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema(static::getInfoCardScheme($minAddress, $maxAddress))
                    ->columnSpan(1),
                Forms\Components\Card::make()
                    ->schema(static::getFormCardScheme($minAddress, $maxAddress))
                    ->columnSpan(2),
            ])
            ->columns(['sm' => 3, 'lg' => null]);
    }

    private static function getInfoCardScheme(string $minAddress, string $maxAddress): array
    {
        return [
            Forms\Components\Placeholder::make('min_address')
                ->label(__('Minimal IP Address:'))
                ->content($minAddress),
            Forms\Components\Placeholder::make('max_address')
                ->label(__('Maximum IP Address:'))
                ->content($maxAddress),
        ];
    }

    private static function getFormCardScheme(string $minAddress, string $maxAddress): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->label(__('Device name'))
                ->required()
                ->rules('required|string|min:6|max:255'),
            Forms\Components\TextInput::make('address')
                ->label(__('IP Address'))
                ->required()
                ->rules([
                    'required',
                    'ipv4',
                    'max:15',
                    'unique:devices,address',
                    new InIpRange($maxAddress, $minAddress),
                ]),
            Forms\Components\TextInput::make('keep_alive')
                ->label(__('Keep-Alive'))
                ->required()
                ->default(0)
                ->rules('required|int|max:300|min:0'),
            Forms\Components\TextInput::make('mtu')
                ->label(__('MTU'))
                ->required()
                ->default(1420)
                ->rules('required|int|min:0'),
            Forms\Components\TextInput::make('dns_1')
                ->label(__('DNS 1'))
                ->default(config('wg.default_dns_1', '8.8.8.8'))
                ->rules('required_without:dns2|ipv4'),
            Forms\Components\TextInput::make('dns_2')
                ->label(__('DNS 2'))
                ->default(config('wg.default_dns_2', '8.8.4.4'))
                ->rules('required_without:dns1|ipv4'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = \Auth::user();
        $query = parent::getEloquentQuery();

        if ($user && !$user->isAdmin()) {
            $query->where('server_id', $user->server->getKey());
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDevices::route('/'),
        ];
    }

    protected static function getNavigationBadge(): ?string
    {
        return static::$model::where('is_active', true)->count();
    }
}
