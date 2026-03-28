<?php

namespace App\Filament\Admin\Resources\MainCategories\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
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
                ->required()
                ->label(__('messages.slug')),

                TextInput::make('bride_name')->placeholder('ធូ សាន')->label(__('messages.bride_name')),
                TextInput::make('groom_name')->placeholder('ហ៊ុន ចាន់ម៉ាលីលី')->label(__('messages.groom_name')),
            
                DatePicker::make('date')
                    ->native(false)
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set, $record) {
                
                        // ✅ Only run when creating (no record yet)
                        if ($record) {
                            return;
                        }
                
                        $datePart = \Carbon\Carbon::parse($state)->format('dmY');
                
                        if ($state && \Carbon\Carbon::parse($state)->format('H:i') !== '00:00') {
                            $timePart = \Carbon\Carbon::parse($state)->format('Hi');
                        } else {
                            $timePart = rand(1000, 9999);
                        }
                
                        $set('slug', $datePart . '-' . $timePart);
                    })
                    ->label(__('messages.date')),


                    Select::make('time')
                        ->label(__('messages.time'))
                        ->options(function () {
                            $times = [];
                            foreach (['AM', 'PM'] as $period) {
                                $startHour = $period === 'AM' ? 0 : 12;
                                $endHour   = $period === 'AM' ? 12 : 24;

                                for ($h = $startHour; $h < $endHour; $h++) {
                                    foreach ([0, 15, 30, 45] as $m) {
                                        $value   = sprintf('%02d:%02d', $h, $m);
                                        $display = sprintf(
                                            '%d:%02d %s',
                                            $h === 0 ? 12 : ($h > 12 ? $h - 12 : $h),
                                            $m,
                                            $period
                                        );
                                        $times[$value] = $display;
                                    }
                                }
                            }
                            return $times;
                        })
                        ->optionsLimit(96)  // ← 24 hours × 4 intervals = 96 total options show all
                        ->searchable()
                        ->native(false)
                        ->placeholder(__('messages.select_time'))
                        ->required(),

                TextInput::make('google_map')->label(__('messages.google_map')),
                
                Textarea::make('address')->label(__('messages.address'))->rows(1),

                FileUpload::make('cover_image')
                ->disk('public')
                ->directory('cover')
                ->image()
                ->imageEditor()
                ->imageEditorAspectRatioOptions([null, '16:9', '4:3', '1:1'])->label(__('messages.cover_image')),

                FileUpload::make('qr_code')
                ->disk('public')  
                ->directory('qr')
                ->visibility('public')
                ->image()
                ->imageEditorAspectRatioOptions([null, '16:9', '4:3', '1:1'])->label(__('messages.qr_code')),
               
                Toggle::make('is_visible')
                    ->default(true)
                    ->required()
                    ->label(__('messages.is_visible')),
            ]);
    }
}
