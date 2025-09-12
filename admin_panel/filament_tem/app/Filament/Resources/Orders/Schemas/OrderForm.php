<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('order_number')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(\App\Models\Order::class, 'order_number', ignoreRecord: true)
                                    ->placeholder('Auto-generated if empty'),

                                Select::make('user_id')
                                    ->label('Customer')
                                    ->options(\App\Models\User::all()->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                Select::make('status')
                                    ->required()
                                    ->options([
                                        'pending' => 'Pending',
                                        'processing' => 'Processing',
                                        'shipped' => 'Shipped',
                                        'delivered' => 'Delivered',
                                        'cancelled' => 'Cancelled',
                                        'refunded' => 'Refunded',
                                    ])
                                    ->default('pending')
                                    ->native(false),
                            ]),

                        Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Internal notes about this order'),
                    ]),

                Section::make('Pricing')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextInput::make('subtotal')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->step(0.01),

                                TextInput::make('tax_amount')
                                    ->label('Tax')
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->default(0),

                                TextInput::make('shipping_amount')
                                    ->label('Shipping')
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->default(0),

                                TextInput::make('total_amount')
                                    ->label('Total')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->step(0.01),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('discount_amount')
                                    ->label('Discount')
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->default(0),

                                TextInput::make('discount_code')
                                    ->label('Discount Code')
                                    ->maxLength(255)
                                    ->placeholder('Applied discount code'),
                            ]),
                    ]),

                Section::make('Billing Address')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('billing_first_name')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('billing_last_name')
                                    ->required()
                                    ->maxLength(255),
                            ]),

                        TextInput::make('billing_company')
                            ->maxLength(255),

                        TextInput::make('billing_address_line_1')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('billing_address_line_2')
                            ->maxLength(255),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('billing_city')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('billing_state')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('billing_postal_code')
                                    ->required()
                                    ->maxLength(20),
                            ]),

                        TextInput::make('billing_country')
                            ->required()
                            ->maxLength(255)
                            ->default('United States'),

                        TextInput::make('billing_phone')
                            ->tel()
                            ->maxLength(20),
                    ])
                    ->collapsible(),

                Section::make('Payment Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('payment_status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'paid' => 'Paid',
                                        'failed' => 'Failed',
                                        'refunded' => 'Refunded',
                                        'partially_refunded' => 'Partially Refunded',
                                    ])
                                    ->default('pending')
                                    ->native(false),

                                TextInput::make('payment_method')
                                    ->maxLength(255)
                                    ->placeholder('e.g., Credit Card, PayPal'),
                            ]),

                        TextInput::make('transaction_id')
                            ->maxLength(255)
                            ->placeholder('Payment gateway transaction ID'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
