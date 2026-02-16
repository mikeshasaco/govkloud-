@extends('layouts.govkloud')

@section('title', $lab->title . ' - GovKloud Labs')

@push('styles')
    <style>
        .lab-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .lab-info {
            flex: 1;
            min-width: 300px;
        }

        .lab-meta {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .lab-steps {
            margin-top: 2rem;
        }

        .step-list {
            list-style: none;
        }

        .step-item {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            background: var(--bg-card);
            border-radius: var(--radius);
            margin-bottom: 0.75rem;
            border: 1px solid var(--border);
        }

        .step-number {
            width: 32px;
            height: 32px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            flex-shrink: 0;
        }

        .step-content {
            flex: 1;
        }

        .step-type {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
        }

        .start-card {
            background: linear-gradient(135deg, var(--bg-card), rgba(99, 102, 241, 0.1));
            border: 1px solid var(--primary);
            padding: 2rem;
            border-radius: var(--radius);
            text-align: center;
            margin-top: 2rem;
        }
    </style>
@endpush

@section('content')
    <div class="breadcrumb">
        <a href="{{ route('courses.index') }}">Modules</a>
        <span>/</span>
        <a href="{{ route('courses.show', $lab->module->slug) }}">{{ $lab->module->title }}</a>
        <span>/</span>
        <span>{{ $lab->title }}</span>
    </div>

    <div class="lab-header">
        <div class="lab-info">
            <h1>{{ $lab->title }}</h1>
            @if($lab->description)
                <p class="text-muted mt-2">{{ $lab->description }}</p>
            @endif
            <div class="lab-meta">
                <span class="badge badge-primary">~{{ $lab->estimated_minutes }} minutes</span>
                <span class="badge badge-secondary">{{ $lab->steps->count() }} steps</span>
            </div>
        </div>
    </div>

    @if($lab->steps->count() > 0)
        <div class="lab-steps">
            <h2 class="mb-2">What You'll Do</h2>
            <ul class="step-list">
                @foreach($lab->steps as $step)
                    <li class="step-item">
                        <div class="step-number">{{ $loop->iteration }}</div>
                        <div class="step-content">
                            <div class="step-type">{{ ucfirst($step->type) }}</div>
                            <div>{{ $step->payload_json['title'] ?? 'Step ' . $loop->iteration }}</div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="start-card">
        <h2 class="mb-2">Ready to Begin?</h2>
        <p class="text-muted mb-3">This lab will provision a dedicated environment for {{ $lab->ttl_minutes }} minutes.</p>

        @auth
            <button id="start-lab-btn" class="btn btn-success" data-lab-id="{{ $lab->id }}">
                🚀 Start Lab
            </button>
        @else
            <a href="/login" class="btn btn-primary">Sign in to Start</a>
        @endauth
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('start-lab-btn')?.addEventListener('click', async function () {
            const btn = this;
            const labId = btn.dataset.labId;

            btn.disabled = true;
            btn.textContent = 'Starting...';

            try {
                const response = await fetch(`/api/labs/${labId}/start`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });

                const data = await response.json();

                if (response.ok) {
                    window.location.href = `/lab-sessions/${data.session_id}`;
                } else {
                    alert(data.error || 'Failed to start lab');
                    btn.disabled = false;
                    btn.textContent = '🚀 Start Lab';
                }
            } catch (error) {
                alert('Network error. Please try again.');
                btn.disabled = false;
                btn.textContent = '🚀 Start Lab';
            }
        });
    </script>
@endpush