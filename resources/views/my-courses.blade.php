@extends('layouts.govkloud')

@section('title', 'My Courses - GovKloud')

@section('content')
    <div style="margin-bottom: 2rem;">
        <h1>My Courses</h1>
        <p class="text-muted" style="margin-top: 0.5rem;">Your saved courses and learning progress</p>
    </div>

    @if($savedModules->isEmpty())
        <div
            style="text-align: center; padding: 4rem 2rem; background: var(--gk-slate); border-radius: 16px; border: 1px solid var(--border);">
            <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;">📚</div>
            <h2 style="margin-bottom: 0.5rem;">No saved courses yet</h2>
            <p class="text-muted" style="margin-bottom: 1.5rem;">Save courses to access them quickly from here</p>
            <a href="{{ route('courses.index') }}" class="btn btn-primary">Browse Courses</a>
        </div>
    @else
        <div class="card-grid">
            @foreach($savedModules as $module)
                <div class="card" style="position: relative;">
                    <!-- Saved Badge -->
                    <div style="position: absolute; top: 1rem; right: 1rem;">
                        <span
                            style="background: rgba(210, 180, 140, 0.2); color: var(--gk-cyan); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.7rem; font-weight: 600;">
                            ✓ SAVED
                        </span>
                    </div>

                    <!-- Module Info -->
                    <div style="margin-bottom: 1rem;">
                        @if($module->category)
                            <span class="badge badge-primary"
                                style="margin-bottom: 0.5rem; display: inline-block;">{{ $module->category }}</span>
                        @endif
                        <h3 style="margin-top: 0.5rem;">{{ $module->title }}</h3>
                        @if($module->description)
                            <p class="text-muted" style="margin-top: 0.5rem; font-size: 0.9rem;">
                                {{ Str::limit($module->description, 100) }}</p>
                        @endif
                    </div>

                    <!-- Stats -->
                    <div style="display: flex; gap: 1rem; margin-bottom: 1rem; font-size: 0.85rem; color: var(--text-muted);">
                        <span>📖 {{ $module->lessons->count() }} Lessons</span>
                        <span>🧪 {{ $module->labs()->count() }} Labs</span>
                    </div>

                    <!-- Actions -->
                    <div style="display: flex; gap: 0.75rem;">
                        <a href="{{ route('courses.show', $module->slug) }}" class="btn btn-primary"
                            style="flex: 1; text-align: center;">
                            ⚡ Continue Learning
                        </a>
                        <button onclick="unsaveModule({{ $module->id }}, this)" class="btn"
                            style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3);">
                            ✕
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <script>
        async function unsaveModule(moduleId, btn) {
            const card = btn.closest('.card');
            card.style.opacity = '0.5';

            try {
                const response = await fetch(`/modules/${moduleId}/save`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    card.style.transition = 'all 0.3s ease';
                    card.style.transform = 'scale(0.95)';
                    card.style.opacity = '0';
                    setTimeout(() => {
                        card.remove();
                        // Check if no more cards
                        if (document.querySelectorAll('.card-grid .card').length === 0) {
                            location.reload();
                        }
                    }, 300);
                }
            } catch (error) {
                card.style.opacity = '1';
                console.error('Error:', error);
            }
        }
    </script>
@endsection