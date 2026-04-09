<?php

namespace App\Filament\Admin\Resources\Configurations\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ConfigurationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('messages.configuration_information'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('messages.name'))
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, callable $set) =>
                                $operation === 'create'
                                    ? $set('slug', Str::slug($state))
                                    : null
                            )
                            ->columnSpanFull(),

                        TextInput::make('link')
                            ->label(__('messages.link'))
                            ->url()
                            ->nullable()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('slug')
                            ->label(__('messages.slug'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Select::make('type')
                            ->label(__('messages.type'))
                            ->options([
                                'text'  => __('messages.type_text'),
                                'image' => __('messages.type_image'),
                                'music' => __('messages.music'),
                            ])
                            ->default('text')
                            ->required()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(fn ($set) => $set('value', null)),
                    ])
                    ->columns(2),

                Section::make(__('messages.value'))
                    ->schema([

                        // ── TEXT ──
                        TextInput::make('text_value')
                            ->label(__('messages.value'))
                            ->nullable()
                            ->afterStateHydrated(fn ($component, $record) =>
                                $component->state($record?->type === 'text' ? $record->value : null)
                            )
                           
                            ->visible(fn (Get $get): bool => $get('type') === 'text'),

                        // ── IMAGE ──
                        FileUpload::make('value')
                            ->label(__('messages.type_image'))
                            ->disk('public')
                            ->directory('cover')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatioOptions([null, '16:9', '4:3', '1:1'])
                            ->afterStateHydrated(function ($component, $record) {
                                $component->state(
                                    ($record?->type === 'image' && filled($record->value))
                                        ? (is_array($record->value) ? $record->value : [$record->value])
                                        : []
                                );
                            })
                            ->dehydrated(fn (Get $get): bool => $get('type') === 'image') // ✅
                            ->visible(fn (Get $get): bool => $get('type') === 'image'),

                        // ── MUSIC ──
                        FileUpload::make('value')
                            ->label(__('messages.music'))
                            ->disk('public')
                            ->directory('music')
                            ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/aac'])
                            ->rules(['max:51200'])
                            ->afterStateHydrated(function ($component, $record) {
                                $component->state(
                                    ($record?->type === 'music' && filled($record->value))
                                        ? (is_array($record->value) ? $record->value : [$record->value])
                                        : []
                                );
                            })
                            ->dehydrated(fn (Get $get): bool => $get('type') === 'music') // ✅
                            ->visible(fn (Get $get): bool => $get('type') === 'music'),

                        Toggle::make('is_visible')
                            ->label(__('messages.is_visible'))
                            ->default(true)
                            ->columnSpanFull(),

                    ])->columns(1),
            ]);
    }
}