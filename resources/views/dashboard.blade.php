@extends('layouts.govkloud')

@section('title', 'Dashboard - GovKloud')

@section('content')
    <div style="max-width: 1200px; margin: 2rem auto;">
        <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 1.5rem;">Dashboard</h1>

        <div class="card">
            <p style="font-size: 1.1rem;">You're logged in!</p>
            <p style="color: var(--text-muted); margin-top: 1rem;">
                Welcome to GovKloud. Explore our Kubernetes courses and hands-on labs.
            </p>
        </div>
    </div>
@endsection