<?php

namespace App\Filament\Resources\ChallengeResource\Pages;

use App\Filament\Resources\ChallengeResource;
use App\Models\Challenge;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;

class ListChallenges extends ListRecords
{
    protected static string $resource = ChallengeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('importProblems')
                ->label('Import Problems')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->form([
                    Forms\Components\FileUpload::make('json_file')
                        ->label('Problems JSON File')
                        ->acceptedFileTypes(['application/json'])
                        ->required()
                        ->helperText('Upload a .json file containing an array of problem definitions.'),
                    Forms\Components\Toggle::make('skip_existing')
                        ->label('Skip duplicates (by slug)')
                        ->default(true),
                ])
                ->action(function (array $data): void {
                    $filePath = storage_path('app/public/' . $data['json_file']);

                    if (!file_exists($filePath)) {
                        // Try livewire-tmp path
                        $filePath = storage_path('app/' . $data['json_file']);
                    }

                    if (!file_exists($filePath)) {
                        Notification::make()
                            ->title('File not found')
                            ->danger()
                            ->send();
                        return;
                    }

                    $json = file_get_contents($filePath);
                    $problems = json_decode($json, true);

                    if (!$problems || !is_array($problems)) {
                        Notification::make()
                            ->title('Invalid JSON format')
                            ->body('File must contain a JSON array of problem objects.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $imported = 0;
                    $skipped = 0;

                    foreach ($problems as $index => $problem) {
                        // Generate slug if missing
                        if (empty($problem['slug'])) {
                            $problem['slug'] = Str::slug($problem['title'] ?? 'problem-' . ($index + 1));
                        }

                        // Skip duplicates
                        if ($data['skip_existing'] && Challenge::where('slug', $problem['slug'])->exists()) {
                            $skipped++;
                            continue;
                        }

                        // Set defaults
                        $problem['order_index'] = $problem['order_index'] ?? Challenge::max('order_index') + 1;
                        $problem['is_published'] = $problem['is_published'] ?? false;
                        $problem['requires_cluster'] = $problem['requires_cluster'] ?? true;
                        $problem['problem_type'] = $problem['problem_type'] ?? 'troubleshoot';
                        $problem['points'] = $problem['points'] ?? 10;
                        $problem['estimated_minutes'] = $problem['estimated_minutes'] ?? 15;

                        // Handle JSON fields that might be arrays
                        foreach (['scenario_manifests_json', 'validation_rules_json', 'initial_files_json', 'solution_files_json', 'hints_json', 'command_flows_json', 'initial_state_json', 'quiz_options_json'] as $jsonField) {
                            if (isset($problem[$jsonField]) && is_array($problem[$jsonField])) {
                                $problem[$jsonField] = json_encode($problem[$jsonField]);
                            }
                        }

                        Challenge::create($problem);
                        $imported++;
                    }

                    // Clean up the uploaded file
                    @unlink($filePath);

                    Notification::make()
                        ->title("Import Complete")
                        ->body("Imported {$imported} problems" . ($skipped > 0 ? ", skipped {$skipped} duplicates" : ""))
                        ->success()
                        ->send();
                }),
            Actions\CreateAction::make(),
        ];
    }
}
