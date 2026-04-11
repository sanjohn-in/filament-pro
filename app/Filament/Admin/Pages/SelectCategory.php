<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use App\Models\Admin\MainCategory;
use App\Services\ImageCompressor;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SelectCategory extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected  string $view = 'filament.admin.pages.select-category';

    protected static bool $shouldRegisterNavigation = false;

    public $categories;

    public function mount()
    {
        $this->categories = MainCategory::where('user_id', Auth::id())
        ->get();
        // dd($this->categories);
    }

    public function selectCategory($id)
    {
        session(['main_category_id' => $id]);
        return redirect('/admin');
    }
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('messages.create_category'))
                ->icon('heroicon-o-plus')
                ->model(MainCategory::class)
                ->after(function () {
                    $this->categories = MainCategory::where('user_id', Auth::id())->get();
                })
                ->schema([
                    Grid::make(2) // 2 columns
                        ->schema([
                            Hidden::make('user_id')->default(fn () => Auth::id()),
    
                            Grid::make(2)
                            ->schema([
                                Select::make('type')
                                    ->label(__('messages.type'))
                                    ->options([
                                        'wedding'           => __('messages.wedding'),
                                        'engagement'        => __('messages.engagement'),
                                        'birthday'          => __('messages.birthday'),
                                        'handtied_ceremony' => __('messages.handtied_ceremony'),
                                        'other'             => __('messages.other'),
                                    ])
                                  ->required(),
        
                                  Select::make('music_id')
                                  ->label(__('messages.music'))
                                  ->relationship('musics', 'name') // 'name' = column to display
                                  ->searchable()
                                  ->preload()
                                  ->required(),
                                    
                            ]),
    
                            TextInput::make('slug')
                                ->label(__('messages.slug'))
                                ->unique()
                                ->readOnly()
                                ->required(),

                                Grid::make(2)
                                ->schema([
                                    TextInput::make('bride_name')
                                    ->placeholder('ធូ សាន')
                                    ->label(__('messages.bride_name_kh'))
                                    ->required(),
            
                                    TextInput::make('groom_name')
                                    ->placeholder('ហ៊ុន ចាន់ម៉ាលីលី')
                                    ->label(__('messages.groom_name_kh'))
                                    ->required(),
                                    
                                ]),
            
                                Grid::make(2)
                                ->schema([
                                    TextInput::make('bride_name_en')
                                    ->placeholder('Thou San')
                                    ->label(__('messages.bride_name_en')),
            
                                    TextInput::make('groom_name_en')
                                    ->placeholder('Hun Chanmalyly')
                                    ->label(__('messages.groom_name_en')),
                                ]),
    
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

                            TextInput::make('google_map')
                            ->placeholder('https://maps.app.goo.gl/sQsL8f5PGFqqhmtB6')
                            ->label(__('messages.google_map')),
    
                            Textarea::make('address')
                                ->placeholder('Phnom Penh, St271, Cambodia')
                                ->label(__('messages.address'))
                                ->rows(1),
    
                                Repeater::make('schedules')
                                ->label(__('messages.schedules', ['default' => 'Event Schedules']))
                                ->schema([
                                    TextInput::make('label')
                                        ->label(__('messages.ceremony_label', ['default' => 'Label']))
                                        ->placeholder('e.g. កិច្ចសន្យាអាពាហ៍ពិពាហ៍, ពិធីទទួលភ្ញៀវ…')
                                        ->required(),
            
                                    Select::make('time')
                                        ->label(__('messages.ceremony_time', ['default' => 'Time']))
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
                                        ->optionsLimit(96)
                                        ->searchable()
                                        ->native(false)
                                        ->placeholder(__('messages.select_time', ['default' => 'Select time']))
                                        ->required(),
                                ])
                                ->columns(2)                    // label + time side-by-side, venue + address below
                                ->addActionLabel(__('messages.add_schedule'))
                                ->reorderable()                 // drag-to-reorder handles
                                ->collapsible()                 // collapse existing items to save space
                                ->cloneable()                   // quickly duplicate a block
                                ->defaultItems(1)               // start with one empty row ready to fill
                                ->columnSpanFull(),
            
                            // ─────────────────────────────────────────────────────────
            
                            ColorPicker::make('theme_color')
                            ->label(__('messages.theme_color'))
                            ->default('#87CEEB')
                            ->formatStateUsing(fn ($state) => $state ?? '#87CEEB')
                            ->required(),
            
                            ColorPicker::make('bg_color')
                                ->label(__('messages.background_color'))
                                ->default('#e6e6e6')
                                ->formatStateUsing(fn ($state) => $state ?? '#e6e6e6')
                                ->required(),
                                
                                
                                FileUpload::make('cover_image')
                                ->disk('public')
                                ->directory('cover')
                                ->image()
                                ->imageEditor()
                                ->imageEditorAspectRatioOptions([null, '16:9', '4:3', '1:1'])
                                ->label(__('messages.cover_image'))
                                ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                                    return ImageCompressor::compressAndSave($file, 'cover');
                                }),
    
                            FileUpload::make('qr_code')
                                ->disk('public')  
                                ->directory('qr')
                                ->visibility('public')
                                ->image()
                                ->label(__('messages.qr_code'))
                                ->imageEditorAspectRatioOptions([null, '16:9', '4:3', '1:1'])
                                ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                                    return ImageCompressor::compressAndSave($file, 'cover');
                                }),
    

                                FileUpload::make('portfolios')
                                ->disk('public')
                                ->directory(function () {
                                    $username = Auth::user()?->name ?? 'guest';
                                    $folder   = Str::of($username)->lower()->replace(' ', '');
                                    return "portfolios/{$folder}";
                                })
                                ->image()
                                ->multiple()
                                ->maxFiles(9)
                                ->helperText(__('messages.portfolios_helper'))
                                ->panelLayout('grid')
                                ->imageEditor()
                                ->imageEditorAspectRatioOptions([null, '1:1', '4:3', '16:9'])
                                ->label(__('messages.portfolios'))
                                ->columnSpanFull()
                                ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                                    return ImageCompressor::compressAndSave($file, 'cover');
                                }),

                            Toggle::make('is_visible')
                                ->default(true)
                                ->label(__('messages.is_visible'))
                                ->required(),
                        ]),
                ]),
        ];
    }
    public function getTitle(): string
    {
        return __('messages.select_category');
    }
}