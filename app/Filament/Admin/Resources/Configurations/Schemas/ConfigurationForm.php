<?php

namespace App\Filament\Admin\Resources\Configurations\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
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
                                    'text'        => __('messages.type_text'),
                                    'image'       => __('messages.type_image'),
                                    'text-editor' => __('messages.type_text_editor'),
                                ])
                                ->default('text')
                                ->required()
                                ->native(false)
                                ->live(), // ← triggers form to re-render
    
                           
                        ])
                        ->columns(2),
    
                    // ── Dynamic value field based on type ──
                    Section::make(__('messages.value'))
                        ->schema([
                            TextInput::make('value_text')
                            ->label(__('messages.value'))
                            ->nullable()
                            ->afterStateHydrated(function ($component, $record) {
                                if ($record?->type === 'text') {
                                    $component->state($record->value);
                                }
                            })
                            ->visible(fn (Get $get): bool => $get('type') === 'text'),
                        
                        FileUpload::make('value_image')
                            ->label(__('messages.type_image'))
                            ->disk('public')
                            ->directory('cover')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatioOptions([null, '16:9', '4:3', '1:1'])
                            ->afterStateHydrated(function ($component, $record) {
                                if ($record?->type === 'image' && is_string($record->value)) {
                                    $component->state([$record->value]);
                                }
                            })
                            ->visible(fn (Get $get): bool => $get('type') === 'image'),
                        
                        RichEditor::make('value_editor')
                            ->label(__('messages.value'))
                           
                            ->nullable()
                            ->afterStateHydrated(function ($component, $record) {
                                if ($record?->type === 'text-editor') {
                                    $component->state($record->value);
                                }
                            })
                            ->columnSpanFull()
                            ->visible(fn (Get $get): bool => $get('type') === 'text-editor'),

                    Toggle::make('is_visible')
                        ->label(__('messages.is_visible'))
                        ->default(true)
                        ->columnSpanFull(),

                        ])->columns(1),

                 
            ]);
    }
    
}
