<?php

namespace App\Filament\Resources\ModuleResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class LessonsRelationManager extends RelationManager
{
    protected static string $relationship = 'lessons';

    protected static ?string $title = 'Lessons';

    protected static ?string $icon = 'heroicon-o-academic-cap';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('subcategory')
                            ->options([
                                'Kubernetes' => 'Kubernetes',
                                'Terraform' => 'Terraform',
                                'Docker' => 'Docker',
                                'AWS' => 'AWS',
                                'Azure' => 'Azure',
                                'GCP' => 'Google Cloud',
                                'Linux' => 'Linux',
                                'Git' => 'Git & Version Control',
                                'CI/CD' => 'CI/CD Pipelines',
                                'Ansible' => 'Ansible',
                                'Helm' => 'Helm',
                                'ArgoCD' => 'ArgoCD',
                                'Security' => 'Security & Compliance',
                            ])
                            ->searchable()
                            ->placeholder('Select technology'),
                        Forms\Components\TextInput::make('order_index')
                            ->numeric()
                            ->default(0)
                            ->helperText('Auto-assigned if left empty'),
                        Forms\Components\Select::make('lab_id')
                            ->relationship('lab', 'title')
                            ->placeholder('No lab attached'),
                    ])->columns(2),

                Forms\Components\Section::make('Video Content')
                    ->schema([
                        Forms\Components\TextInput::make('video_url')
                            ->label('Video URL (YouTube/Vimeo embed)')
                            ->placeholder('https://www.youtube.com/embed/xxxxx')
                            ->helperText('Paste YouTube or Vimeo embed URL'),
                        Forms\Components\FileUpload::make('video_file')
                            ->label('Or Upload Video File')
                            ->disk('public')
                            ->directory('lesson-videos')
                            ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                            ->maxSize(512000)
                            ->helperText('Max 500MB. MP4, WebM, or OGG format.'),
                    ]),

                Forms\Components\Section::make('Reading Material')
                    ->schema([
                        Forms\Components\MarkdownEditor::make('reading_md')
                            ->label('Lesson Content (Markdown)')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Quiz Questions')
                    ->description('Add multiple choice questions to test knowledge')
                    ->schema([
                        Forms\Components\Repeater::make('quiz_json')
                            ->label('Questions')
                            ->schema([
                                Forms\Components\TextInput::make('question')
                                    ->required()
                                    ->label('Question Text')
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('type')
                                    ->options([
                                        'multiple_choice' => 'Multiple Choice',
                                        'text' => 'Text Answer',
                                    ])
                                    ->default('multiple_choice')
                                    ->required(),
                                Forms\Components\Repeater::make('options')
                                    ->label('Answer Options')
                                    ->schema([
                                        Forms\Components\TextInput::make('text')
                                            ->label('Option')
                                            ->required(),
                                    ])
                                    ->minItems(2)
                                    ->maxItems(6)
                                    ->defaultItems(4)
                                    ->visible(fn(Forms\Get $get) => $get('type') === 'multiple_choice')
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('correct_answer')
                                    ->required()
                                    ->label('Correct Answer')
                                    ->helperText('For multiple choice, enter the exact text of the correct option'),
                                Forms\Components\Textarea::make('explanation')
                                    ->label('Explanation (shown after answering)')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->itemLabel(fn(array $state): ?string => $state['question'] ?? 'New Question')
                            ->collapsible()
                            ->cloneable()
                            ->defaultItems(0)
                            ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('subcategory')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('order_index')
                    ->label('Order')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('video_url')
                    ->label('Video')
                    ->boolean()
                    ->trueIcon('heroicon-o-video-camera')
                    ->falseIcon('heroicon-o-x-mark'),
                Tables\Columns\TextColumn::make('quiz_json')
                    ->label('Quiz')
                    ->formatStateUsing(fn($state) => $state ? count($state) . ' Q' : '-')
                    ->badge(),
                Tables\Columns\TextColumn::make('lab.title')
                    ->label('Lab')
                    ->limit(20)
                    ->placeholder('None'),
            ])
            ->defaultSort('order_index')
            ->reorderable('order_index')
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
