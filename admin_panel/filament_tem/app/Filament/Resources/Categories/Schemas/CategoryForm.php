<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category Information')
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
                                    ->unique(\App\Models\Category::class, 'slug', ignoreRecord: true),
                            ]),

                        Textarea::make('description')
                            ->columnSpanFull()
                            ->rows(3),

                        FileUpload::make('image')
                            ->image()
                            ->directory('categories')
                            ->imageEditor(),

                        Select::make('parent_id')
                            ->label('Parent Category')
                            ->options(\App\Models\Category::all()->pluck('name', 'id'))
                            ->searchable()
                            ->placeholder('Select parent category (optional)'),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('sort_order')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),

                                Toggle::make('is_active')
                                    ->default(true),
                            ]),
                    ]),
            ]);
    }
}
