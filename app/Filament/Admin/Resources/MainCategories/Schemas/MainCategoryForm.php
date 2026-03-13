<?php

namespace App\Filament\Admin\Resources\MainCategories\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;


class MainCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')->default(fn () => Auth::id()),
                Select::make('type')
                    ->label(__('messages.type'))
                    ->options([
                        'wedding' => 'Wedding',
                        'engagement' => 'Engagement',
                        'handtied_ceremony' => 'Handtied ceremony',
                        'birthday' => 'Birthday',
                        'other' => 'Other',
                  ])->required(),

                TextInput::make('slug')
                ->unique()
                ->readOnly()
                ->required(),

                TextInput::make('bride_name')->placeholder('John Smith'),
                TextInput::make('groom_name')->placeholder('Hun Malyly'),
            
                 DateTimePicker::make('date')
                    ->native(true)
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set) {
                        // $state is the selected date/time as string (e.g., "2026-03-10 15:30")
                        $datePart = \Carbon\Carbon::parse($state)->format('dmY'); // e.g., 10032026

                        // Extract time part as HHMM
                        if ($state && \Carbon\Carbon::parse($state)->format('H:i') !== '00:00') {
                            $timePart = \Carbon\Carbon::parse($state)->format('Hi'); // e.g., 1530
                        } else {
                            $timePart = rand(1000, 9999); // random 4-digit number if no time selected
                        }

                        // Set slug field
                        $set('slug', $datePart . '-' . $timePart);
                }),
                TextInput::make('google_map'),
                Textarea::make('address')
                    ->columnSpanFull(),

                FileUpload::make('cover_image')
                ->disk('public')
                ->directory('cover')
                ->image()
                ->imageEditor()
                ->imageEditorAspectRatioOptions([null, '16:9', '4:3', '1:1']),

                FileUpload::make('qr_code')
                ->disk('public')  
                ->directory('qr')
                ->visibility('public')
                ->image()
                ->imageEditorAspectRatioOptions([null, '16:9', '4:3', '1:1']),
               
                Toggle::make('is_visible')
                    ->default(true)
                    ->required(),
            ]);
    }
}
