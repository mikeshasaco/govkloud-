@extends('layouts.govkloud')

@section('title', 'Profile - GovKloud')

@section('content')
    <div style="max-width: 800px; margin: 2rem auto;">
        <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 1.5rem;">Profile Settings</h1>

        <div class="card" style="margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem;">Profile Information</h2>
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="card" style="margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem;">Update Password</h2>
            @include('profile.partials.update-password-form')
        </div>

        <div class="card">
            <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem;">Delete Account</h2>
            @include('profile.partials.delete-user-form')
        </div>
    </div>
@endsection