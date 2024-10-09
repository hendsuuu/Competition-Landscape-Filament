<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Section as ComponentsSection;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->default(Auth::id())
                    ->extraAttributes(['style' => 'display: none;'])
                    ->readOnly()
                    ->required(),
                ComponentsSection::make('Product Information')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('brand_id')
                            ->native(false)
                            ->relationship('brand', 'name')
                            ->required(),
                        Forms\Components\Select::make('location_id')
                            ->native(false)
                            ->relationship('location', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('product_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('product_type')
                            ->options([
                                'SIM' => 'SIM',
                                'VOUCHER' => 'VOUCHER',
                            ])
                            ->native(false)
                            ->required(),
                        Forms\Components\Select::make('denom')
                            ->options([
                                '5 - 10 K' => '5 - 10 K',
                                '10 - 15 K' => '10 - 15 K',
                                '15 - 20 K' => '15 - 20 K',
                                '<30 K' => '<30 K',
                                '~20 K' => '~20 K',
                                '~25 K' => '~25 K',
                                '~30 K' => '~30 K',
                                '~40 K' => '~40 K',
                                '~50 K' => '~50 K',
                                '~60 - 70 K' => '~60 - 70 K',
                                '~80 - 90 K' => '~80 - 90 K',
                                '~100 K' => '~100 K',
                                '~120 K' => '~120 K',
                                '~150 K' => '~150 K',
                            ])
                            ->native(false)
                            ->columnSpan(2)
                            ->required(),
                        Forms\Components\Select::make('provinsi')
                            ->label('Provinsi')
                            ->native(false)
                            ->preload()
                            ->searchable()
                            ->options(fn() => self::getProvinces()) // Menggunakan self untuk memanggil fungsi statis
                            ->reactive() // Membuat form responsif terhadap perubahan
                            ->afterStateUpdated(fn(callable $set) => $set('kabupaten_kota', null)) // Reset kabupaten ketika provinsi berubah
                            ->required(),

                        // Dropdown untuk kabupaten/kota, akan di-update setelah provinsi dipilih
                        Forms\Components\Select::make('kabupaten_kota')
                            ->label('Kabupaten/Kota')
                            ->native(false)
                            ->preload()
                            ->searchable()
                            ->options(fn(callable $get) => self::getRegencies($get('provinsi'))) // Menggunakan self untuk memanggil fungsi statis
                            ->required(),
                    ]),
                ComponentsSection::make('Product Price')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('rbp')
                            ->label('ٌRBP (Harga yang dibeli Outlet)')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('eup')
                            ->label('EUP (Harga yg di beli Pelanggan)')
                            ->required()
                            ->numeric()
                            ->afterStateUpdated(
                                fn(callable $set, $state, $get) =>
                                $set('yield', ($get('total_kuota') != 0) ? $get('eup') / $get('total_kuota') : 0)
                            ),
                        Forms\Components\TextInput::make('yield')
                            ->label('ٌYield (Keuntungan Bersih)')
                            ->required()
                            ->columnSpan(2)
                            ->readOnly()
                            ->reactive()
                            ->numeric(),
                    ]),
                ComponentsSection::make('Kuota Information')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('kuota_nasional')
                            ->reactive()
                            ->numeric()
                            ->afterStateUpdated(
                                function (callable $set, $state, $get) {
                                    $set('total_kuota', ($get('kuota_nasional') != null) ? $get('kuota_nasional') + $get('kuota_lokal') : $get('kuota_lokal'));
                                    $set('yield', ($get('total_kuota') != null) ? $get('eup') / $get('total_kuota') : 0);
                                }
                            ),
                        Forms\Components\TextInput::make('kuota_lokal')
                            ->reactive()
                            ->numeric()
                            ->extraAttributes([
                                'x-data' => '{}',
                                'x-init' => 'this.addEventListener("focus", () => { $el.select() })',
                                'step' => 'any',
                                'inputmode' => 'numeric', // for mobile number pad
                                'onfocus' => 'this.setSelectionRange(0, this.value.length)', // select the whole number on focus
                            ])
                            ->afterStateUpdated(
                                function (callable $set, $state, $get) {
                                    $set('total_kuota', ($get('kuota_lokal') != null) ? $get('kuota_nasional') + $get('kuota_lokal') : $get('kuota_nasional'));
                                    $set('yield', ($get('total_kuota') != null) ? $get('eup') / $get('total_kuota') : 0);
                                }
                            ),
                        Forms\Components\TextInput::make('total_kuota')
                            ->numeric()
                            ->readOnly()
                            ->afterStateUpdated(
                                fn(callable $set, $state, $get) =>
                                $set('yield', ($get('total_kuota') != null) ? $get('eup') / $get('total_kuota') : 0)
                            )

                            ->reactive(),
                        Forms\Components\TextInput::make('validity')
                            ->label('validity (Jumlah Hari Paket Aktif)')
                            ->required()
                            ->numeric(),
                    ]),

            ]);
    }
    protected static function getProvinces(): array
    {
        // Panggil API untuk mendapatkan data provinsi
        $response = Http::get('https://wilayah.id/api/provinces.json');

        // Cek apakah response sukses dan memiliki data
        if ($response->successful() && isset($response['data'])) {
            $provinces = $response['data'];

            // Pluck 'name' dan 'code' untuk dijadikan opsi select
            return collect($provinces)->pluck('name', 'code')->toArray();
        }

        // Jika gagal atau tidak ada data, kembalikan array kosong
        return [];
    }

    // Fungsi untuk mendapatkan daftar kabupaten/kota berdasarkan provinsi terpilih
    protected static function getRegencies($province_code): array
    {
        if (!$province_code) {
            return [];
        }

        // Panggil API untuk mendapatkan kabupaten/kota berdasarkan kode provinsi
        $response = Http::get("https://wilayah.id/api/regencies/{$province_code}.json");

        // Cek apakah response sukses dan memiliki data
        if ($response->successful() && isset($response['data'])) {
            $regencies = $response['data'];

            // Pluck 'name' dan 'code' untuk dijadikan opsi select
            return collect($regencies)->pluck('name', 'code')->toArray();
        }

        // Jika gagal atau tidak ada data, kembalikan array kosong
        return [];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('brand.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rbp')
                    ->numeric()
                    ->prefix('Rp. ')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('eup')
                    ->numeric()
                    ->prefix('Rp. ')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('yield')
                    ->numeric()
                    ->prefix('Rp. ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('flag_zona')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kuota_nasional')
                    ->numeric()
                    ->suffix(' GB')
                    ->sortable(),
                Tables\Columns\TextColumn::make('kuota_lokal')
                    ->numeric()
                    ->suffix(' GB')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_kuota')
                    ->numeric()
                    ->suffix(' GB')
                    ->sortable(),
                Tables\Columns\TextColumn::make('validity')
                    ->numeric()
                    ->suffix(' Hari')
                    ->sortable(),
                Tables\Columns\TextColumn::make('flag_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('product_type'),
                Tables\Columns\TextColumn::make('denom'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Author')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Section for Product Information
                Section::make('Product Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('brand.name')
                            ->label('Brand')
                            ->columnSpan(1),
                        TextEntry::make('location.name')
                            ->label('Location')
                            ->columnSpan(1),
                        TextEntry::make('product_name')
                            ->label('Product Name')
                            ->columnSpan(1),
                        TextEntry::make('product_type')
                            ->label('Product Type')
                            ->columnSpan(1),
                        TextEntry::make('denom')
                            ->label('Denom')
                            ->columnSpan(1),
                    ]),

                // Section for Pricing Information
                Section::make('Pricing Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('rbp')
                            ->label('RBP (Harga yang dibeli Outlet)')
                            ->prefix('Rp. ')
                            ->columnSpan(1),
                        TextEntry::make('eup')
                            ->label('EUP (Harga yg di beli Pelanggan)')
                            ->prefix('Rp. ')
                            ->columnSpan(1),
                        TextEntry::make('yield')
                            ->label('Yield (Keuntungan Bersih)')
                            ->prefix('Rp. ')
                            ->columnSpan(2),
                    ]),

                // Section for Quota Information
                Section::make('Kuota Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('kuota_nasional')
                            ->label('Kuota Naisonal')
                            ->suffix(' GB')
                            ->columnSpan(1),
                        TextEntry::make('kuota_lokal')
                            ->label('Kuota Nasional')
                            ->suffix(' GB')
                            ->columnSpan(1),
                        TextEntry::make('total_kuota')
                            ->label('Total Kuota')
                            ->suffix(' GB')
                            ->columnSpan(2),
                        TextEntry::make('validity')
                            ->label('Validity (Days)')
                            ->suffix(' Days')
                            ->columnSpan(1),
                    ]),

                // Section for Additional Information
                Section::make('Additional Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('flag_zona')
                            ->label('Flag Zona'),
                        TextEntry::make('flag_type')
                            ->label('Flag Type'),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime(),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
