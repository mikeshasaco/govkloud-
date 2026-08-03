<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChallengeResource\Pages;
use App\Models\Challenge;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ChallengeResource extends Resource
{
    protected static ?string $model = Challenge::class;

    protected static ?string $navigationIcon = 'heroicon-o-code-bracket-square';

    protected static ?string $navigationLabel = 'Problems';

    protected static ?string $modelLabel = 'Challenge';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Challenge Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn(Forms\Set $set, ?string $state) =>
                                $set('slug', Str::slug($state))
                            ),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Auto-generated from title'),
                        Forms\Components\Select::make('category')
                            ->options([
                                'kubernetes' => '☸️ Kubernetes',
                                'terraform' => '🏗️ Terraform',
                                'docker' => '🐳 Docker',
                            ])
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('difficulty')
                            ->options([
                                'beginner' => '🟢 Beginner',
                                'medium' => '🟠 Medium',
                                'hard' => '🔴 Hard',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('estimated_minutes')
                            ->numeric()
                            ->default(15)
                            ->suffix('minutes'),
                        Forms\Components\TextInput::make('order_index')
                            ->numeric()
                            ->default(0)
                            ->helperText('Auto-assigned if left empty'),
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->rows(6)
                            ->columnSpanFull()
                            ->helperText('Markdown supported. Describe what the user needs to accomplish.'),
                        Forms\Components\Toggle::make('is_published')
                            ->default(false),
                    ])->columns(2),

                Forms\Components\Section::make('Code Editor Setup')
                    ->description('Define the files pre-loaded in the code editor when the user starts this challenge.')
                    ->schema([
                        Forms\Components\KeyValue::make('initial_files_json')
                            ->label('Initial Files')
                            ->keyLabel('Filename')
                            ->valueLabel('Content')
                            ->addActionLabel('Add File')
                            ->helperText('e.g., Key: pod.yaml, Value: # Write your manifest here'),
                        Forms\Components\KeyValue::make('file_language_map')
                            ->label('Language Map')
                            ->keyLabel('Filename')
                            ->valueLabel('Language')
                            ->addActionLabel('Add Mapping')
                            ->helperText('e.g., Key: pod.yaml, Value: yaml'),
                    ]),

                Forms\Components\Section::make('Terminal Configuration')
                    ->description('Configure the terminal simulator behavior for this challenge.')
                    ->schema([
                        Forms\Components\Textarea::make('command_flows_json')
                            ->label('Command Flows (JSON)')
                            ->rows(10)
                            ->helperText('JSON config: required_commands, validations, custom_outputs')
                            ->formatStateUsing(fn($state) => $state ? json_encode($state, JSON_PRETTY_PRINT) : '')
                            ->dehydrateStateUsing(fn($state) => $state ? json_decode($state, true) : null),
                        Forms\Components\Textarea::make('initial_state_json')
                            ->label('Initial Cluster State (JSON)')
                            ->rows(6)
                            ->helperText('Pre-existing resources in the simulated cluster')
                            ->formatStateUsing(fn($state) => $state ? json_encode($state, JSON_PRETTY_PRINT) : '')
                            ->dehydrateStateUsing(fn($state) => $state ? json_decode($state, true) : null),
                    ]),

                Forms\Components\Section::make('Solution')
                    ->description('The correct answer revealed after the user completes or gives up.')
                    ->schema([
                        Forms\Components\KeyValue::make('solution_files_json')
                            ->label('Solution Files')
                            ->keyLabel('Filename')
                            ->valueLabel('Correct Content')
                            ->addActionLabel('Add Solution File'),
                        Forms\Components\Textarea::make('solution_explanation')
                            ->label('Solution Explanation')
                            ->rows(4)
                            ->helperText('Markdown explanation of why this solution works.'),
                    ]),

                Forms\Components\Section::make('Hints')
                    ->description('Progressive hints revealed one at a time.')
                    ->schema([
                        Forms\Components\Textarea::make('hints_json')
                            ->label('Hints (JSON array)')
                            ->rows(4)
                            ->helperText('JSON array of strings, e.g., ["Hint 1", "Hint 2", "Hint 3"]')
                            ->formatStateUsing(fn($state) => $state ? json_encode($state, JSON_PRETTY_PRINT) : '')
                            ->dehydrateStateUsing(fn($state) => $state ? json_decode($state, true) : null),
                    ]),

                Forms\Components\Section::make('Tutorial Video')
                    ->description('Optional animated tutorial users can watch before starting.')
                    ->schema([
                        Forms\Components\TextInput::make('video_url')
                            ->label('Video URL (YouTube, Vimeo, etc.)')
                            ->url()
                            ->placeholder('https://youtube.com/watch?v=...'),
                        Forms\Components\FileUpload::make('video_file')
                            ->label('Or Upload Video')
                            ->disk('azure')
                            ->directory('challenge-videos')
                            ->visibility('public')
                            ->acceptedFileTypes(['video/mp4', 'video/webm'])
                            ->maxSize(512000), // 500MB
                    ]),

                Forms\Components\Section::make('Tags')
                    ->schema([
                        Forms\Components\TagsInput::make('tags')
                            ->placeholder('Add tags...')
                            ->helperText('e.g., pods, secrets, probes, networking'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'kubernetes' => 'info',
                        'terraform' => 'warning',
                        'docker' => 'primary',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('difficulty')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'beginner' => 'success',
                        'medium' => 'warning',
                        'hard' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('estimated_minutes')
                    ->label('Est. Time')
                    ->suffix(' min')
                    ->sortable(),
                Tables\Columns\TextColumn::make('attempts_count')
                    ->counts('attempts')
                    ->label('Attempts')
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_index')
                    ->numeric()
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
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'kubernetes' => 'Kubernetes',
                        'terraform' => 'Terraform',
                        'docker' => 'Docker',
                    ]),
                Tables\Filters\SelectFilter::make('difficulty')
                    ->options([
                        'beginner' => 'Beginner',
                        'medium' => 'Medium',
                        'hard' => 'Hard',
                    ]),
                Tables\Filters\TernaryFilter::make('is_published'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChallenges::route('/'),
            'create' => Pages\CreateChallenge::route('/create'),
            'edit' => Pages\EditChallenge::route('/{record}/edit'),
        ];
    }
}
