@extends('layouts.govkloud')

@section('title', $module->title . ' - GovKloud Labs')

@section('content')
    <div class="breadcrumb">
        <a href="{{ route('modules.index') }}">Modules</a>
        <span>/</span>
        <span>{{ $module->title }}</span>
    </div>

    <h1 class="mb-2">{{ $module->title }}</h1>
    @if($module->description)
        <p class="text-muted mb-3">{{ $module->description }}</p>
    @endif

    @if($module->lessons->count() > 0)
        <h2 class="mt-4 mb-2">Lessons</h2>
        <div class="card-grid">
            @foreach($module->lessons as $lesson)
                <a href="{{ route('lessons.show', [$module->slug, $lesson->slug]) }}" class="card"
                    style="text-decoration: none; color: inherit;">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="badge badge-primary">Lesson {{ $loop->iteration }}</span>
                        @if($lesson->video_url)
                            <span class="badge badge-secondary">📹 Video</span>
                        @endif
                    </div>
                    <h3>{{ $lesson->title }}</h3>
                </a>
            @endforeach
        </div>
    @endif

    @if($module->labs->count() > 0)
        <h2 class="mt-4 mb-2">Hands-on Labs</h2>
        <div class="card-grid">
            @foreach($module->labs as $lab)
                <a href="{{ route('labs.show', $lab->slug) }}" class="card" style="text-decoration: none; color: inherit;">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="badge badge-secondary">🧪 Lab</span>
                        <span class="badge badge-primary">~{{ $lab->estimated_minutes }} min</span>
                    </div>
                    <h3>{{ $lab->title }}</h3>
                    @if($lab->description)
                        <p class="text-muted mt-1">{{ Str::limit($lab->description, 100) }}</p>
                    @endif
                </a>
            @endforeach
        </div>
    @endif
@endsection