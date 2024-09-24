<?php

namespace App\Filament\Sales\Resources;

use App\Filament\Sales\Resources\ProductResource\Pages;
use App\Filament\Sales\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
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
                    ->visible(true)
                    ->readOnly()
                    ->required(),
                Forms\Components\Select::make('brand_id')
                    ->relationship('brand', 'name')
                    ->required(),
                Forms\Components\Select::make('location_id')
                    ->relationship('location', 'name')
                    ->required(),
                Forms\Components\TextInput::make('product_name')
                    ->required()
                    ->maxLength(255),
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
                        $set('yield', $get('total_kuota') != 0 ? $get('eup') / $get('total_kuota') : 0)
                    ),
                Forms\Components\TextInput::make('yield')
                    ->label('ٌYield (Keuntungan Bersih)')
                    ->required()
                    ->readOnly()
                    ->reactive()
                    ->numeric(),
                Forms\Components\TextInput::make('kuota_nasional')
                    ->reactive()
                    ->live()
                    ->numeric()
                    ->afterStateUpdated(
                        function (callable $set, $state, $get) {
                            $set('total_kuota', $get('kuota_nasional') + $get('kuota_lokal'));
                            $set('yield', ($get('total_kuota') != 0) ? $get('eup') / $get('total_kuota') : 0);
                        }
                    ),
                Forms\Components\TextInput::make('kuota_lokal')
                    ->reactive()
                    ->live()
                    ->numeric()
                    ->afterStateUpdated(
                        function (callable $set, $state, $get) {
                            $set('total_kuota', $get('kuota_nasional') + $get('kuota_lokal'));
                            $set('yield', ($get('total_kuota') != 0) ? $get('eup') / $get('total_kuota') : 0);
                        }
                    ),
                Forms\Components\TextInput::make('total_kuota')
                    ->numeric()
                    ->readOnly()
                    ->afterStateUpdated(
                        fn(callable $set, $state, $get) =>
                        $set('yield', $get('total_kuota') != 0 ? $get('eup') / $get('total_kuota') : 0)
                    )
                    ->reactive(),
                Forms\Components\TextInput::make('validity')
                    ->label('validity (Jumlah Hari Paket Aktif)')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('product_type')
                    ->options([
                        'SIM' => 'SIM',
                        'VOUCHER' => 'VOUCHER',
                    ])
                    ->required(),
                Forms\Components\Select::make('denom')
                    ->options([
                        '5-10 K' => '5-10 K',
                        '10-15 K' => '10-15 K',
                        '<30 K' => '<30 K',
                        '20 K' => '20 K',
                        '25 K' => '25 K',
                        '30 K' => '30 K',
                        '40 K' => '40 K',
                        '50 K' => '50 K',
                        '60-70 K' => '60-70 K',
                        '80-90 K' => '80-90 K',
                        '100 K' => '100 K',
                        '120 K' => '120 K',
                        '150 K' => '150 K',
                    ])
                    ->required(),
            ]);
    }
    // protected function getTableQuery()
    // {
    //     // Mengembalikan query builder yang hanya mengambil data milik user yang sedang login
    //     return \App\Models\Product::where('user_id', Auth::id());
    // }
    public static function table(Table $table): Table
    {
        return $table
            // ->query(function (Builder $query) {
            //     return $query->where('user_id', Auth::id()); // Hanya tampilkan data milik user yang sedang login
            // })
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
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
                    ->sortable(),
                Tables\Columns\TextColumn::make('eup')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('yield')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('flag_zona')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kuota_nasional')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kuota_lokal')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_kuota')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('validity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('flag_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('product_type'),
                Tables\Columns\TextColumn::make('denom'),
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
