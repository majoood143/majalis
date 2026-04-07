<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;


/**
 * Page Resource - Admin Panel
 *
 * Manages static content pages in the Filament admin panel
 * Provides bilingual content management for English and Arabic
 */
class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('admin.pages.navigation_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Main Content Section
                Forms\Components\Section::make(__('admin.pages.section_main_content'))
                    ->description(__('admin.pages.section_main_content_desc'))
                    ->schema([
                        // Slug field
                        Forms\Components\TextInput::make('slug')
                            ->label(__('admin.pages.slug'))
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->alphaDash()
                            ->helperText(__('admin.pages.slug_helper'))
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, ?string $state) {
                                // Auto-generate slug from English title if empty
                                if (empty($state) && filled($get('title_en'))) {
                                    $set('slug', Str::slug($get('title_en')));
                                }
                            }),

                        // English Title
                        Forms\Components\TextInput::make('title_en')
                            ->label(__('admin.pages.title_en'))
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, ?string $state) {
                                // Auto-generate slug if empty
                                if (empty($get('slug')) && filled($state)) {
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        // Arabic Title
                        Forms\Components\TextInput::make('title_ar')
                            ->label(__('admin.pages.title_ar'))
                            ->required()
                            ->maxLength(255)
                            ->extraAttributes(['dir' => 'rtl']),
                    ])
                    ->columns(2),

                // English Content Section
                Forms\Components\Section::make(__('admin.pages.section_content_en'))
                    ->schema([
                        Forms\Components\RichEditor::make('content_en')
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
                Forms\Components\Section::make(__('admin.pages.section_content_ar'))
                    ->schema([
                        Forms\Components\RichEditor::make('content_ar')
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
                Forms\Components\Section::make(__('admin.pages.section_seo'))
                    ->description(__('admin.pages.section_seo_desc'))
                    ->schema([
                        // English SEO
                        Forms\Components\TextInput::make('meta_title_en')
                            ->label(__('admin.pages.meta_title_en'))
                            ->maxLength(60)
                            ->helperText(__('admin.pages.meta_title_helper')),

                        Forms\Components\Textarea::make('meta_description_en')
                            ->label(__('admin.pages.meta_description_en'))
                            ->maxLength(160)
                            ->rows(3)
                            ->helperText(__('admin.pages.meta_description_helper')),

                        // Arabic SEO
                        Forms\Components\TextInput::make('meta_title_ar')
                            ->label(__('admin.pages.meta_title_ar'))
                            ->maxLength(60)
                            ->extraAttributes(['dir' => 'rtl'])
                            ->helperText(__('admin.pages.meta_title_helper')),

                        Forms\Components\Textarea::make('meta_description_ar')
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
                Forms\Components\Section::make(__('admin.pages.section_display_settings'))
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('admin.pages.is_active'))
                            ->default(true)
                            ->helperText(__('admin.pages.is_active_helper')),

                        Forms\Components\TextInput::make('order')
                            ->label(__('admin.pages.order'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText(__('admin.pages.order_helper')),

                        Forms\Components\Toggle::make('show_in_header')
                            ->label(__('admin.pages.show_in_header'))
                            ->default(false)
                            ->helperText(__('admin.pages.show_in_header_helper')),

                        Forms\Components\Toggle::make('show_in_footer')
                            ->label(__('admin.pages.show_in_footer'))
                            ->default(true)
                            ->helperText(__('admin.pages.show_in_footer_helper')),
                    ])
                    ->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title_en')
                    ->label(__('admin.pages.title_en'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('title_ar')
                    ->label(__('admin.pages.title_ar'))
                    ->searchable()
                    ->sortable()
                    ->extraAttributes(['dir' => 'rtl']),

                Tables\Columns\TextColumn::make('slug')
                    ->label(__('admin.pages.slug'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('admin.pages.is_active'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('order')
                    ->label(__('admin.pages.order'))
                    ->numeric()
                    ->sortable(),

                Tables\Columns\IconColumn::make('show_in_header')
                    ->label(__('admin.pages.header'))
                    ->boolean(),

                Tables\Columns\IconColumn::make('show_in_footer')
                    ->label(__('admin.pages.footer'))
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('admin.pages.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('admin.pages.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('admin.pages.filter_status'))
                    ->placeholder(__('admin.pages.filter_all'))
                    ->trueLabel(__('admin.pages.filter_active'))
                    ->falseLabel(__('admin.pages.filter_inactive')),

                Tables\Filters\TernaryFilter::make('show_in_footer')
                    ->label(__('admin.pages.show_in_footer')),

                Tables\Filters\TernaryFilter::make('show_in_header')
                    ->label(__('admin.pages.show_in_header')),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                ActionGroup::make([

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                ActivityLogTimelineTableAction::make('Activities'),
                ])

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
