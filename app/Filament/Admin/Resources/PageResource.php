<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
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

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Main Content Section
                Forms\Components\Section::make('Main Content')
                    ->description('Page title, slug, and basic information')
                    ->schema([
                        // Slug field
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->alphaDash()
                            ->helperText('URL-friendly identifier (e.g., about-us)')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, ?string $state) {
                                // Auto-generate slug from English title if empty
                                if (empty($state) && filled($get('title_en'))) {
                                    $set('slug', Str::slug($get('title_en')));
                                }
                            }),

                        // English Title
                        Forms\Components\TextInput::make('title_en')
                            ->label('Title (English)')
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
                            ->label('Title (Arabic)')
                            ->required()
                            ->maxLength(255)
                            ->extraAttributes(['dir' => 'rtl']),
                    ])
                    ->columns(2),

                // English Content Section
                Forms\Components\Section::make('English Content')
                    ->schema([
                        Forms\Components\RichEditor::make('content_en')
                            ->label('Content (English)')
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
                Forms\Components\Section::make('Arabic Content')
                    ->schema([
                        Forms\Components\RichEditor::make('content_ar')
                            ->label('Content (Arabic)')
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
                Forms\Components\Section::make('SEO Settings')
                    ->description('Meta tags for search engines')
                    ->schema([
                        // English SEO
                        Forms\Components\TextInput::make('meta_title_en')
                            ->label('Meta Title (English)')
                            ->maxLength(60)
                            ->helperText('Recommended: 50-60 characters'),

                        Forms\Components\Textarea::make('meta_description_en')
                            ->label('Meta Description (English)')
                            ->maxLength(160)
                            ->rows(3)
                            ->helperText('Recommended: 150-160 characters'),

                        // Arabic SEO
                        Forms\Components\TextInput::make('meta_title_ar')
                            ->label('Meta Title (Arabic)')
                            ->maxLength(60)
                            ->extraAttributes(['dir' => 'rtl'])
                            ->helperText('Recommended: 50-60 characters'),

                        Forms\Components\Textarea::make('meta_description_ar')
                            ->label('Meta Description (Arabic)')
                            ->maxLength(160)
                            ->rows(3)
                            ->extraAttributes(['dir' => 'rtl'])
                            ->helperText('Recommended: 150-160 characters'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),

                // Settings Section
                Forms\Components\Section::make('Display Settings')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Page is visible to users'),

                        Forms\Components\TextInput::make('order')
                            ->label('Display Order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Lower numbers appear first'),

                        Forms\Components\Toggle::make('show_in_header')
                            ->label('Show in Header')
                            ->default(false)
                            ->helperText('Display link in header navigation'),

                        Forms\Components\Toggle::make('show_in_footer')
                            ->label('Show in Footer')
                            ->default(true)
                            ->helperText('Display link in footer navigation'),
                    ])
                    ->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title_en')
                    ->label('Title (English)')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('title_ar')
                    ->label('Title (Arabic)')
                    ->searchable()
                    ->sortable()
                    ->extraAttributes(['dir' => 'rtl']),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('order')
                    ->label('Order')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\IconColumn::make('show_in_header')
                    ->label('Header')
                    ->boolean(),

                Tables\Columns\IconColumn::make('show_in_footer')
                    ->label('Footer')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All Pages')
                    ->trueLabel('Active Only')
                    ->falseLabel('Inactive Only'),

                Tables\Filters\TernaryFilter::make('show_in_footer')
                    ->label('Show in Footer'),

                Tables\Filters\TernaryFilter::make('show_in_header')
                    ->label('Show in Header'),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
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
