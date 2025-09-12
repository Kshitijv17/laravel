<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $context, $state, callable $set) {
                                        if ($context === 'create') {
                                            $set('slug', \Illuminate\Support\Str::slug($state));
                                        }
                                    }),

                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(\App\Models\Product::class, 'slug', ignoreRecord: true),
                            ]),

                        Textarea::make('short_description')
                            ->maxLength(500)
                            ->rows(2)
                            ->columnSpanFull(),

                        RichEditor::make('description')
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                                'link',
                            ]),
                    ]),

                Section::make('Product Details')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('sku')
                                    ->label('SKU')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(\App\Models\Product::class, 'sku', ignoreRecord: true)
                                    ->placeholder('Auto-generated if empty'),

                                Select::make('category_id')
                                    ->label('Category')
                                    ->options(\App\Models\Category::where('is_active', true)->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                TextInput::make('weight')
                                    ->numeric()
                                    ->suffix('kg')
                                    ->step(0.01),
                            ]),

                        TextInput::make('dimensions')
                            ->maxLength(255)
                            ->placeholder('L x W x H (cm)')
                            ->helperText('Format: Length x Width x Height in centimeters'),
                    ]),

                Section::make('Pricing & Discounts')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->step(0.01),

                                TextInput::make('compare_price')
                                    ->label('Compare at Price')
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->helperText('Original price for discount display'),

                                TextInput::make('cost_price')
                                    ->label('Cost Price')
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->helperText('Your cost for profit calculations'),
                            ]),
                    ]),

                Section::make('Inventory Management')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('stock_quantity')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0),

                                Toggle::make('track_inventory')
                                    ->default(true)
                                    ->helperText('Enable inventory tracking for this product'),
                            ]),
                    ]),

                Section::make('Media')
                    ->schema([
                        FileUpload::make('images')
                            ->image()
                            ->multiple()
                            ->directory('products')
                            ->maxFiles(10)
                            ->reorderable()
                            ->imageEditor()
                            ->columnSpanFull(),
                    ]),

                Section::make('Status & Visibility')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_active')
                                    ->default(true)
                                    ->helperText('Product visibility in store'),

                                Toggle::make('is_featured')
                                    ->default(false)
                                    ->helperText('Show in featured products'),
                            ]),
                    ]),

                Section::make('SEO & Meta')
                    ->schema([
                        TextInput::make('meta_title')
                            ->maxLength(255)
                            ->helperText('SEO title for search engines'),

                        Textarea::make('meta_description')
                            ->maxLength(500)
                            ->rows(3)
                            ->helperText('SEO description for search engines'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
