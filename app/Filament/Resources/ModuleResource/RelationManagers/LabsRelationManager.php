<?php

namespace App\Filament\Resources\ModuleResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class LabsRelationManager extends RelationManager
{
    protected static string $relationship = 'labs';

    protected static ?string $title = 'Labs';

    protected static ?string $icon = 'heroicon-o-beaker';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Lab Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Lab Configuration')
                    ->schema([
                        Forms\Components\TextInput::make('estimated_minutes')
                            ->numeric()
                            ->default(30)
                            ->suffix('minutes')
                            ->helperText('Estimated time to complete'),
                        Forms\Components\TextInput::make('ttl_minutes')
                            ->numeric()
                            ->default(180)
                            ->suffix('minutes')
                            ->label('Session TTL')
                            ->helperText('Max time before auto-cleanup'),
                    ])->columns(2),

                Forms\Components\Section::make('Environment')
                    ->schema([
                        Forms\Components\TextInput::make('workbench_image')
                            ->placeholder('govkloudacr.azurecr.io/code-server:latest')
                            ->helperText('Docker image for the lab workbench'),
                        Forms\Components\TextInput::make('validator_image')
                            ->placeholder('Optional validator image')
                            ->helperText('Image to validate lab completion'),
                        Forms\Components\KeyValue::make('lab_config_json')
                            ->label('Lab Config (JSON)')
                            ->helperText('Resource limits, environment variables, etc.'),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('estimated_minutes')
                    ->label('Duration')
                    ->suffix(' min')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sessions_count')
                    ->counts('sessions')
                    ->label('Sessions'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
