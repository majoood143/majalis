<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use App\Filament\Admin\Resources\PageResource\Pages\ListPages;
use App\Filament\Admin\Resources\PageResource\Pages\CreatePage;
use App\Filament\Admin\Resources\PageResource\Pages\EditPage;
use App\Filament\Admin\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Support\Str;


/**
 * Page Resource - Admin Panel
 *
 * Manages static content pages in the Filament admin panel
 * Provides bilingual content management for English and Arabic
 */
class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected static string | \UnitEnum | null $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('admin.pages.navigation_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Main Content Section
                Section::make(__('admin.pages.section_main_content'))
                    ->description(__('admin.pages.section_main_content_desc'))
                    ->schema([
                        // Slug field
                        TextInput::make('slug')
                            ->label(__('admin.pages.slug'))
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->alphaDash()
                            ->helperText(__('admin.pages.slug_helper'))
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                // Auto-generate slug from English title if empty
                                if (empty($state) && filled($get('title_en'))) {
                                    $set('slug', Str::slug($get('title_en')));
                                }
                            }),

                        // English Title
                        TextInput::make('title_en')
                            ->label(__('admin.pages.title_en'))
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                // Auto-generate slug if empty
                                if (empty($get('slug')) && filled($state)) {
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        // Arabic Title
                        TextInput::make('title_ar')
                            ->label(__('admin.pages.title_ar'))
                            ->required()
                            ->maxLength(255)
                            ->extraAttributes(['dir' => 'rtl']),
                    ])
                    ->columns(2),

                // English Content Section
                Section::make(__('admin.pages.section_content_en'))
                    ->schema([
                        RichEditor::make('content_en')
                            ->label(__('admin.pages.content_en'))
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                                'blockquote',
                                'codeBlock',
                            ]),
                    ])
                    ->collapsible(),

                // Arabic Content Section
                Section::make(__('admin.pages.section_content_ar'))
                    ->schema([
                        RichEditor::make('content_ar')
                            ->label(__('admin.pages.content_ar'))
                            ->required()
                            ->columnSpanFull()
                            ->extraAttributes(['dir' => 'rtl'])
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                                'blockquote',
                                'codeBlock',
                            ]),
                    ])
                    ->collapsible(),

                // SEO Section
                Section::make(__('admin.pages.section_seo'))
                    ->description(__('admin.pages.section_seo_desc'))
                    ->schema([
                        // English SEO
                        TextInput::make('meta_title_en')
                            ->label(__('admin.pages.meta_title_en'))
                            ->maxLength(60)
                            ->helperText(__('admin.pages.meta_title_helper')),

                        Textarea::make('meta_description_en')
                            ->label(__('admin.pages.meta_description_en'))
                            ->maxLength(160)
                            ->rows(3)
                            ->helperText(__('admin.pages.meta_description_helper')),

                        // Arabic SEO
                        TextInput::make('meta_title_ar')
                            ->label(__('admin.pages.meta_title_ar'))
                            ->maxLength(60)
                            ->extraAttributes(['dir' => 'rtl'])
                            ->helperText(__('admin.pages.meta_title_helper')),

                        Textarea::make('meta_description_ar')
                            ->label(__('admin.pages.meta_description_ar'))
                            ->maxLength(160)
                            ->rows(3)
                            ->extraAttributes(['dir' => 'rtl'])
                            ->helperText(__('admin.pages.meta_description_helper')),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),

                // Settings Section
                Section::make(__('admin.pages.section_display_settings'))
                    ->schema([
                        Toggle::make('is_active')
                            ->label(__('admin.pages.is_active'))
                            ->default(true)
                            ->helperText(__('admin.pages.is_active_helper')),

                        TextInput::make('order')
                            ->label(__('admin.pages.order'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText(__('admin.pages.order_helper')),

                        Toggle::make('show_in_header')
                            ->label(__('admin.pages.show_in_header'))
                            ->default(false)
                            ->helperText(__('admin.pages.show_in_header_helper')),

                        Toggle::make('show_in_footer')
                            ->label(__('admin.pages.show_in_footer'))
                            ->default(true)
                            ->helperText(__('admin.pages.show_in_footer_helper')),
                    ])
                    ->columns(4),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title_en')
                    ->label(__('admin.pages.title_en'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('title_ar')
                    ->label(__('admin.pages.title_ar'))
                    ->searchable()
                    ->sortable()
                    ->extraAttributes(['dir' => 'rtl']),

                TextColumn::make('slug')
                    ->label(__('admin.pages.slug'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->badge()
                    ->color('gray'),

                IconColumn::make('is_active')
                    ->label(__('admin.pages.is_active'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('order')
                    ->label(__('admin.pages.order'))
                    ->numeric()
                    ->sortable(),

                IconColumn::make('show_in_header')
                    ->label(__('admin.pages.header'))
                    ->boolean(),

                IconColumn::make('show_in_footer')
                    ->label(__('admin.pages.footer'))
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label(__('admin.pages.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('admin.pages.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('admin.pages.filter_status'))
                    ->placeholder(__('admin.pages.filter_all'))
                    ->trueLabel(__('admin.pages.filter_active'))
                    ->falseLabel(__('admin.pages.filter_inactive')),

                TernaryFilter::make('show_in_footer')
                    ->label(__('admin.pages.show_in_footer')),

                TernaryFilter::make('show_in_header')
                    ->label(__('admin.pages.show_in_header')),

                TrashedFilter::make(),
            ])
            ->recordActions([
                \Filament\Actions\ActionGroup::make([

                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
                // TODO: ActivityLogTimelineTableAction removed (rmsramos v3-only) - replace with v4 equivalent,
                ])

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}
