<?php

namespace App\Filament\Admin\Resources\Themes\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ThemeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Theme Information')
                ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                RichEditor::make('description')
                    ->label('Description')
                    ->columnSpanFull(),

                TextInput::make('price')
                    ->label('Price')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->prefix('$')
                    ->default(0),

                Toggle::make('is_free')
                    ->label('Is Free')
                    ->default(false)
                    ->reactive()
                    ->afterStateUpdated(function (Set $set, $state) {
                        if ($state) {
                            $set('price', 0);
                        }
                    }),

                Toggle::make('is_active')
                    ->label('Is Active')
                    ->default(true),

                TextInput::make('display_order')
                    ->label('Display Order')
                    ->numeric()
                    ->default(0),
            ])
            ->columns(2),

            Section::make('Images')
                ->schema([
                    FileUpload::make('image_url')
                        ->label('Theme Image')
                        ->directory('themes')
                        ->disk('public')
                        ->image()
                        ->imageEditor()
                        ->imageEditorAspectRatioOptions([null, '16:9', '4:3', '1:1']),

                    FileUpload::make('preview_image_url')
                        ->label('Preview Image')
                        ->image()
                        ->directory('themes')
                        ->columnSpanFull(),
                ]),

            Section::make('Assign to Categories')
                ->schema([
                    CheckboxList::make('mainCategories')
                        ->label('Main Categories')
                        ->relationship('mainCategories', 'type')
                        ->columnSpanFull(),
                ]),
            ]);
    }
}
