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

                Section::make(__('messages.value')),

                FileUpload::make('value')
                ->label(fn (Get $get) => $get('type') === 'music' ? __('messages.music') : __('messages.type_image'))
                ->disk('public')
                ->directory(fn (Get $get) => $get('type') === 'music' ? 'music' : 'cover')
                ->image(fn (Get $get) => $get('type') === 'image')
                ->imageEditor(fn (Get $get) => $get('type') === 'image')
                ->imageEditorAspectRatioOptions([null, '16:9', '4:3', '1:1'])
                ->acceptedFileTypes(fn (Get $get) => $get('type') === 'music' 
                    ? [
                        'audio/mpeg', 
                        'audio/mp3', 
                        'audio/wav', 
                        'audio/x-wav', 
                        'audio/wave', 
                        'audio/ogg', 
                        'audio/aac',
                        'application/octet-stream', // Required for some MP3 encoders
                    ] 
                    : ['image/jpeg', 'image/png', 'image/webp', 'image/gif']
                )
                ->maxSize(51200) // 50MB
                ->visible(fn (Get $get): bool => in_array($get('type'), ['image', 'music']))
                ->dehydrated(fn (Get $get): bool => in_array($get('type'), ['image', 'music']))
                // Ignore validation on existing string paths during edit
                ->rules([
                    fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                        // If it's already an existing string saved in DB, skip validation
                        if (is_string($value)) {
                            return;
                        }
                    },
                ]),
            ]);
    }
}