@extends('layouts.govkloud')

@section('title', $lesson->title . ' - GovKloud Labs')

@push('styles')
    <style>
        .lesson-content {
            max-width: 800px;
        }

        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: var(--radius);
            background: var(--bg-card);
            margin-bottom: 2rem;
        }

        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .reading-content {
            background: var(--bg-card);
            border-radius: var(--radius);
            padding: 2rem;
            line-height: 1.8;
        }

        .reading-content h1,
        .reading-content h2,
        .reading-content h3 {
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .reading-content p {
            margin-bottom: 1rem;
        }

        .reading-content code {
            background: rgba(99, 102, 241, 0.2);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-family: 'Fira Code', monospace;
        }

        .reading-content pre {
            background: #0d1117;
            padding: 1rem;
            border-radius: 8px;
            overflow-x: auto;
            margin: 1rem 0;
        }

        .lesson-nav {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
        }
    </style>
@endpush

@section('content')
    <div class="breadcrumb">
        <a href="{{ route('courses.index') }}">Modules</a>
        <span>/</span>
        <a href="{{ route('courses.show', $module->slug) }}">{{ $module->title }}</a>
        <span>/</span>
        <span>{{ $lesson->title }}</span>
    </div>

    <div class="lesson-content">
        <h1 class="mb-3">{{ $lesson->title }}</h1>

        @if($lesson->video_url)
            <div class="video-container">
                <iframe src="{{ $lesson->video_url }}" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>
            </div>
        @endif

        @if($lesson->reading_md)
            <div class="reading-content">
                {!! Str::markdown($lesson->reading_md) !!}
            </div>
        @endif

        <div class="lesson-nav">
            @if($prevLesson)
                <a href="{{ route('lessons.show', [$module->slug, $prevLesson->slug]) }}" class="btn btn-secondary">
                    ← {{ $prevLesson->title }}
                </a>
            @else
                <div></div>
            @endif

            @if($nextLesson)
                <a href="{{ route('lessons.show', [$module->slug, $nextLesson->slug]) }}" class="btn btn-primary">
                    {{ $nextLesson->title }} →
                </a>
            @else
                <a href="{{ route('courses.show', $module->slug) }}" class="btn btn-success">
                    ✓ Complete Module
                </a>
            @endif
        </div>
    </div>
@endsection