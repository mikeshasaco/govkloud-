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
                // ── Section 1: The Basics ──
                Forms\Components\Section::make('Problem Basics')
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
                                'kubernetes' => 'Kubernetes',
                                'terraform' => 'Terraform',
                                'docker' => 'Docker',
                            ])
                            ->required(),
                        Forms\Components\Select::make('difficulty')
                            ->options([
                                'beginner' => 'Beginner',
                                'medium' => 'Medium',
                                'hard' => 'Hard',
                            ])
                            ->required(),
                        Forms\Components\Select::make('problem_type')
                            ->options([
                                'troubleshoot' => 'Troubleshoot - something is broken, fix it',
                                'build' => 'Build - create resources from scratch',
                                'scenario' => 'Scenario - multi-step investigation',
                                'debug' => 'Debug - fix broken YAML',
                                'quiz' => 'Quiz - multiple choice (no cluster)',
                            ])
                            ->default('troubleshoot')
                            ->required(),
                        Forms\Components\TextInput::make('points')
                            ->numeric()
                            ->default(10)
                            ->suffix('pts'),
                        Forms\Components\TextInput::make('estimated_minutes')
                            ->numeric()
                            ->default(15)
                            ->suffix('min'),
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->rows(6)
                            ->columnSpanFull()
                            ->helperText('Describe the problem. What is broken? What should the user do? Markdown supported.'),
                        Forms\Components\Toggle::make('is_published')
                            ->default(false)
                            ->helperText('Show this problem to users'),
                        Forms\Components\Toggle::make('requires_cluster')
                            ->default(true)
                            ->helperText('Provisions a real vcluster (turn OFF for quiz-only problems)'),
                    ])->columns(2),

                // ── Section 2: Problem Setup ──
                Forms\Components\Section::make('Problem Setup')
                    ->description('What gets deployed when the user clicks Start Environment, and how do we grade it.')
                    ->schema([
                        Forms\Components\Textarea::make('scenario_manifests_json')
                            ->label('Scenario YAML - What to deploy')
                            ->rows(14)
                            ->columnSpanFull()
                            ->helperText('Paste Kubernetes YAML that creates the broken or initial state. Separate multiple manifests with ---')
                            ->placeholder("apiVersion: v1\nkind: Pod\nmetadata:\n  name: web-app\nspec:\n  containers:\n  - name: nginx\n    image: nginx:99.99\n    ports:\n    - containerPort: 80")
                            ->formatStateUsing(fn($state) => $state ? (is_array($state) ? implode("\n---\n", $state) : $state) : '')
                            ->dehydrateStateUsing(fn($state) => $state ? array_filter(array_map('trim', explode('---', $state))) : null),
                        Forms\Components\Textarea::make('validation_rules_json')
                            ->label('Grading Rules - How to check the answer')
                            ->rows(10)
                            ->columnSpanFull()
                            ->helperText('JSON array of checks. Types: pod_status, resource_exists, field_equals, container_image, replica_count, endpoints_populated')
                            ->placeholder('[{"type": "pod_status", "name": "web-app", "namespace": "default", "expected_status": "Running", "description": "Pod should be Running"}]')
                            ->formatStateUsing(fn($state) => $state ? json_encode($state, JSON_PRETTY_PRINT) : '')
                            ->dehydrateStateUsing(fn($state) => $state ? json_decode($state, true) : null),
                        Forms\Components\KeyValue::make('initial_files_json')
                            ->label('Starter Files - Pre-loaded in the editor')
                            ->keyLabel('Filename')
                            ->valueLabel('Content')
                            ->addActionLabel('Add File')
                            ->helperText('Files shown in the code editor. e.g. fix.yaml with starter YAML'),
                    ]),

                // ── Section 3: Solution & Hints ──
                Forms\Components\Section::make('Solution & Hints')
                    ->schema([
                        Forms\Components\KeyValue::make('solution_files_json')
                            ->label('Solution Files')
                            ->keyLabel('Filename')
                            ->valueLabel('Correct Content')
                            ->addActionLabel('Add Solution File')
                            ->helperText('Shown after the user solves it or clicks Show Solution'),
                        Forms\Components\Textarea::make('solution_explanation')
                            ->label('Explanation')
                            ->rows(4)
                            ->helperText('Explain WHY the solution works and the diagnostic steps.'),
                        Forms\Components\Textarea::make('hints_json')
                            ->label('Hints (progressive)')
                            ->rows(4)
                            ->helperText('JSON array of hint strings revealed one at a time')
                            ->placeholder('["First hint", "Second hint", "Third hint"]')
                            ->formatStateUsing(fn($state) => $state ? json_encode($state, JSON_PRETTY_PRINT) : '')
                            ->dehydrateStateUsing(fn($state) => $state ? json_decode($state, true) : null),
                    ]),

                // ── Section 4: Optional ──
                Forms\Components\Section::make('Optional')
                    ->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('video_url')
                            ->label('Tutorial Video URL')
                            ->url()
                            ->placeholder('https://youtube.com/watch?v=...'),
                        Forms\Components\TagsInput::make('tags')
                            ->placeholder('pods, services, debugging...'),
                        Forms\Components\TextInput::make('time_limit_minutes')
                            ->numeric()
                            ->nullable()
                            ->suffix('minutes')
                            ->helperText('Enforced time limit (leave empty for unlimited)'),
                        Forms\Components\TextInput::make('order_index')
                            ->numeric()
                            ->default(0),
                        // Legacy fields kept for backward compatibility
                        Forms\Components\Hidden::make('file_language_map'),
                        Forms\Components\Hidden::make('command_flows_json'),
                        Forms\Components\Hidden::make('initial_state_json'),
                        Forms\Components\Hidden::make('quiz_options_json'),
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
                Tables\Columns\TextColumn::make('problem_type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'troubleshoot' => 'danger',
                        'build' => 'success',
                        'debug' => 'warning',
                        'scenario' => 'info',
                        'quiz' => 'gray',
                        default => 'gray',
                    }),
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
                Tables\Columns\TextColumn::make('points')
                    ->label('Pts')
                    ->sortable(),
                Tables\Columns\TextColumn::make('acceptance_rate')
                    ->label('Accept %')
                    ->suffix('%')
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('estimated_minutes')
                    ->label('Est. Time')
                    ->suffix(' min')
                    ->sortable(),
                Tables\Columns\TextColumn::make('attempts_count')
                    ->counts('attempts')
                    ->label('Attempts')
                    ->sortable(),
                Tables\Columns\IconColumn::make('requires_cluster')
                    ->label('Cluster')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean(),
                Tables\Columns\TextColumn::make('order_index')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
