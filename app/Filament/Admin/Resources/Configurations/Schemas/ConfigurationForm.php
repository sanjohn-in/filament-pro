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
                        TextInput::make('value')
                            ->label(__('messages.value'))
                            ->nullable()
                            ->visible(fn (Get $get): bool => $get('type') === 'text'),

                        // ── FILE (IMAGE or MUSIC) ──
                        FileUpload::make('value')
                            ->label(fn (Get $get) => $get('type') === 'music' ? __('messages.music') : __('messages.type_image'))
                            ->disk('public')
                            ->directory(fn (Get $get) => $get('type') === 'music' ? 'music' : 'cover')
                            ->image(fn (Get $get) => $get('type') === 'image')
                            ->imageEditor(fn (Get $get) => $get('type') === 'image')
                            ->imageEditorAspectRatioOptions([null, '16:9', '4:3', '1:1'])
                            ->acceptedFileTypes(fn (Get $get) => $get('type') === 'music' 
                                ? ['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/aac'] 
                                : ['image/*']
                            )
                            ->maxSize(51200) // 50MB (value in KB)
                            ->afterStateHydrated(function ($component, $record) {
                                if ($record && in_array($record->type, ['image', 'music']) && filled($record->value)) {
                                    $component->state(is_array($record->value) ? $record->value : [$record->value]);
                                }
                            })
                            ->visible(fn (Get $get): bool => in_array($get('type'), ['image', 'music'])),

                        Toggle::make('is_visible')
                            ->label(__('messages.is_visible'))
                            ->default(true)
                            ->columnSpanFull(),

                    ])->columns(1),
            ]);
    }
}