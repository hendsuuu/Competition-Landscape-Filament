<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
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
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
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
                Forms\Components\TextInput::make('RBP')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('EUP')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('yield')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('flag_zona')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('kuota_nasional')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('kuota_lokal')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('validity')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('flag_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('product_type')
                    ->required(),
                Forms\Components\TextInput::make('denom')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                Tables\Columns\TextColumn::make('RBP')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('EUP')
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
