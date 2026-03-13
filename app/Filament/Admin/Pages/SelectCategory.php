<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use App\Models\Admin\MainCategory;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

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
    
                            Select::make('type')
                                ->label(__('messages.type'))
                                ->options([
                                    'wedding' => 'Wedding',
                                    'engagement' => 'Engagement',
                                    'handtied_ceremony' => 'Handtied ceremony',
                                    // 'birthday' => 'Birthday',
                                    'other' => 'Other',
                                ])
                                ->required(),
    
                            TextInput::make('slug')
                                ->label(__('messages.slug'))
                                ->unique()
                                ->readOnly()
                                ->required(),
    
                            TextInput::make('bride_name')
                            ->label(__('messages.bride_name'))
                            ->placeholder('John Smith')
                            ->required(),

                            TextInput::make('groom_name')
                            ->label(__('messages.groom_name'))
                            ->placeholder('Hun Malyly')
                            ->required(),
    
                            DateTimePicker::make('date')
                                ->label(__('messages.date'))
                                ->native(true)
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $datePart = \Carbon\Carbon::parse($state)->format('dmY');
    
                                    if ($state && \Carbon\Carbon::parse($state)->format('H:i') !== '00:00') {
                                        $timePart = \Carbon\Carbon::parse($state)->format('Hi');
                                    } else {
                                        $timePart = rand(1000, 9999);
                                    }
    
                                    $set('slug', $datePart . '-' . $timePart);
                                }),
    
                            TextInput::make('google_map')
                            ->label(__('messages.google_map')),
    
                            Textarea::make('address')
                                ->label(__('messages.address'))
                                ->columnSpanFull(), // full-width
    
                            FileUpload::make('cover_image')
                                ->label(__('messages.cover_image'))
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
                        ]),
                ]),
        ];
    }
    public function getTitle(): string
    {
        return __('messages.select_category');
    }
}