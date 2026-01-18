<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LabResource\Pages;
use App\Models\Lab;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LabResource extends Resource
{
    protected static ?string $model = Lab::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Lab Information')
                    ->schema([
                        Forms\Components\Select::make('module_id')
                            ->relationship('module', 'title')
                            ->required()
                            ->searchable()
                            ->label('Module'),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('URL-friendly identifier'),
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Lab Configuration')
                    ->schema([
                        Forms\Components\TextInput::make('estimated_minutes')
                            ->numeric()
                            ->default(30)
                            ->suffix('minutes')
                            ->helperText('Estimated time to complete'),
                        Forms\Components\TextInput::make('order_index')
                            ->numeric()
                            ->default(0)
                            ->helperText('Order within module'),
                        Forms\Components\Toggle::make('is_published')
                            ->default(true),
                    ])->columns(3),

                Forms\Components\Section::make('Environment')
                    ->schema([
                        Forms\Components\TextInput::make('helm_chart')
                            ->placeholder('govkloud-workbench')
                            ->helperText('Helm chart name for lab environment'),
                        Forms\Components\KeyValue::make('helm_values')
                            ->label('Helm Values (YAML)')
                            ->helperText('Override values for the Helm chart'),
                    ]),

                Forms\Components\Section::make('Instructions')
                    ->schema([
                        Forms\Components\MarkdownEditor::make('instructions_md')
                            ->label('Lab Instructions (Markdown)')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('module.title')
                    ->label('Module')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('estimated_minutes')
                    ->label('Duration')
                    ->suffix(' min')
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_index')
                    ->label('Order')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('order_index')
            ->filters([
                Tables\Filters\SelectFilter::make('module')
                    ->relationship('module', 'title'),
                Tables\Filters\TernaryFilter::make('is_published'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListLabs::route('/'),
            'create' => Pages\CreateLab::route('/create'),
            'edit' => Pages\EditLab::route('/{record}/edit'),
        ];
    }
}
