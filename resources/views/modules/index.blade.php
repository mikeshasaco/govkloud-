@extends('layouts.govkloud')

@section('title', 'Modules - GovKloud Labs')

@section('content')
    <h1 class="mb-3">Learning Modules</h1>
    <p class="text-muted mb-3">Master cloud infrastructure with hands-on labs and guided lessons.</p>

    <div class="card-grid">
        @forelse($modules as $module)
            <a href="{{ route('modules.show', $module->slug) }}" class="card" style="text-decoration: none; color: inherit;">
                <div class="flex items-center gap-2 mb-2">
                    <span class="badge badge-primary">{{ $module->lessons_count }} Lessons</span>
                    <span class="badge badge-secondary">{{ $module->labs_count }} Labs</span>
                </div>
                <h3>{{ $module->title }}</h3>
                @if($module->description)
                    <p class="text-muted mt-1">{{ Str::limit($module->description, 120) }}</p>
                @endif
            </a>
        @empty
            <div class="card">
                <p class="text-muted">No modules available yet. Check back soon!</p>
            </div>
        @endforelse
    </div>
@endsection