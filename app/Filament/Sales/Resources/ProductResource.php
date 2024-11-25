<?php

namespace App\Filament\Sales\Resources;

use App\Filament\Sales\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section as ComponentsSection;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Table;
use Filament\Tables\Actions\Modal\ModalAction;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\ImageEntry;
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
                Forms\Components\Hidden::make('user_id')
                    ->default(Auth::id())
                    ->required(),
                ComponentsSection::make('Product Information')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('brand_id')
                            ->native(false)
                            ->relationship('brand', 'name')
                            ->required(),
                        // Forms\Components\Select::make('location_id')
                        //     ->native(false)
                        //     ->relationship('location', 'name')
                        //     ->required(),
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
                            ->required(),
                        Forms\Components\Select::make('top_product')
                            ->label('ٌTop Product')
                            ->required()
                            ->options([
                                'Ya' => 'Ya',
                                'Tidak' => 'Tidak',
                            ]),
                        Forms\Components\TextInput::make('product_rank')
                            ->label('ٌRanking *jika top product')
                            ->maxLength(255)
                            ->numeric(),
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
                            ->live(debounce: 1500)
                            ->afterStateUpdated(
                                fn(callable $set, $state, $get) =>
                                $set('yield', ($get('total_kuota') != 0) ? $get('eup') / $get('total_kuota') : 0)
                            ),
                        Forms\Components\TextInput::make('yield')
                            ->label('ٌYield (Keuntungan Bersih)')
                            // ->required()
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
                            ->live(debounce: 1500)
                            ->afterStateUpdated(
                                function (callable $set, $state, $get) {
                                    $set('total_kuota', ($get('kuota_nasional') != null) ? $get('kuota_nasional') + $get('kuota_lokal') : $get('kuota_lokal'));
                                    $set('yield', ($get('total_kuota') != null) ? $get('eup') / $get('total_kuota') : 0);
                                }
                            ),
                        Forms\Components\TextInput::make('kuota_lokal')
                            ->reactive()
                            ->numeric()
                            ->live(debounce: 1500)
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
                            ->live(debounce: 1500)
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
                FileUpload::make('image')
                    ->image()
                    ->optimize('webp')
                    ->resize(50)
                    ->columnSpan(2),

            ]);
    }
    protected static function getProvinces(): array
    {
        // Panggil API untuk mendapatkan data provinsi
        $response = Http::get('https://wilayah.id/api/provinces.json');

        // Cek apakah response sukses dan memiliki data
        if ($response->successful() && isset($response['data'])) {
            $provinces = $response['data'];

            // Daftar kode provinsi untuk Jawa, Bali, dan Nusa Tenggara
            $validProvinceCodes = [
                // '32', // Jawa Barat
                '33', // Jawa Tengah
                '34', // Yogyakarta
                '35', // Jawa Timur
                '51', // Bali
                '52', // Nusa Tenggara Barat
                '53', // Nusa Tenggara Timur
            ];

            // Filter provinces to only include those in the valid codes
            $filteredProvinces = collect($provinces)
                ->filter(function ($province) use ($validProvinceCodes) {
                    return in_array($province['code'], $validProvinceCodes);
                });

            // Pluck 'name' dan 'code' untuk dijadikan opsi select
            return $filteredProvinces->pluck('name', 'code')->toArray();
        }

        // Jika gagal atau tidak ada data, kembalikan array kosong
        return [];
    }
    // protected static function getRegencies($province_code): array
    // {
    //     if (!$province_code) {
    //         return [];
    //     }

    //     // Panggil API untuk mendapatkan kabupaten/kota berdasarkan kode provinsi
    //     $response = Http::get("https://wilayah.id/api/regencies/{$province_code}.json");

    //     // Cek apakah response sukses dan memiliki data
    //     if ($response->successful() && isset($response['data'])) {
    //         $regencies = $response['data'];

    //         // Daftar kabupaten dan kota yang ingin ditampilkan
    //         $validRegencies = [
    //             'Kab. Kudus',
    //             'Kab. Pati',
    //             'Kab. Pekalongan',
    //             'Kab. Pemalang',
    //             'Kab. Salatiga',
    //             'Kab. Semarang',
    //             'Kab. Tegal',
    //             'Kab. Karanganyar',
    //             'Kab. Kebumen',
    //             'Kab. Klaten',
    //             'Kab. Magelang',
    //             'Kab. Purwokerto',
    //             'Kab. Sleman',
    //             'Kab. Solo',
    //             'Kab. Yogyakarta',
    //             'Kab. Jember',
    //             'Kab. Kediri',
    //             'Kab. Malang',
    //             'Kab. Probolinggo',
    //             'Kab. Sidoarjo',
    //             'Kab. Tulungagung',
    //             'Kab. Gresik Mojokerto',
    //             'Kab. Jombang',
    //             'Kab. Madiun',
    //             'Kab. Madura',
    //             'Kab. Surabaya',
    //             'Kab. Tuban Lamongan',
    //             'Kab. Bali Barat',
    //             'Kota Semarang',
    //             'Kab. Bali Timur',
    //             'Kab. Florest Barat',
    //             'Kab. Florest Timur',
    //             'Kab. Lombok',
    //             'Kab. Sumba',
    //             'Kab. Sumbawa',
    //             'Kab. Timor',
    //         ];

    //         // Filter regencies to only include those in the valid list
    //         $filteredRegencies = collect($regencies)
    //             ->filter(function ($regency) use ($validRegencies) {
    //                 return in_array($regency['name'], $validRegencies);
    //             });

    //         // Pluck 'name' dan 'code' untuk dijadikan opsi select
    //         return $filteredRegencies->pluck('name', 'code')->toArray();
    //     }

    //     // Jika gagal atau tidak ada data, kembalikan array kosong
    //     return [];
    // }


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
            // ->query(function (Builder $query) {
            //     // Jika user bukan admin, maka filter berdasarkan user_id
            //     if (Auth::id() !== 1) {
            //         $query->where('user_id', Auth::id());
            //     }
            // })
            ->columns([
                Tables\Columns\TextColumn::make('brand.name')
                    ->numeric()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('location.name')
                //     ->numeric()
                //     ->sortable(),
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
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('flag_zona')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kuota_nasional')
                    ->numeric()
                    ->suffix(' GB')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('kuota_lokal')
                    ->numeric()
                    ->suffix(' GB')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_kuota')
                    ->numeric()
                    ->suffix(' GB')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('validity')
                    ->numeric()
                    ->suffix(' Hari')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('flag_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('product_type'),
                Tables\Columns\TextColumn::make('denom'),
                Tables\Columns\TextColumn::make('top_product')
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_rank'),
                // Tables\Columns\TextColumn::make('user.name')
                //     ->label('Author')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                // ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('image')
                    ->label('Image')
                    ->width('100px')
                    ->openUrlInNewTab()
                    ->height('100px'),

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
                Section::make('Image')
                    ->columns(2)
                    ->schema([
                        ImageEntry::make('image')
                            ->label('Product Image')
                            ->columnSpanFull()
                    ])

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
            // 'create' => Pages\CreateProduct::route('/create'),
            // 'view' => Pages\ViewProduct::route('/{record}'),
            // 'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
