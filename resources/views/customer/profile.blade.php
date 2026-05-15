{{--
    Customer Profile View
    Displays user profile information with edit functionality

    @extends customer.layout
    @version 1.1.0
--}}

@extends('customer.layout')

@section('title', __('My Profile') . ' - Majalis')

@section('content')
<style>
    .brand-btn {
        background-color: #B9916D;
        color: #fff;
        transition: background-color 0.2s, box-shadow 0.2s;
    }
    .brand-btn:hover { background-color: #a47a59; }
    .brand-btn:focus { outline: none; box-shadow: 0 0 0 3px rgba(185,145,109,0.35); }

    .brand-input:focus {
        border-color: #B9916D;
        box-shadow: 0 0 0 3px rgba(185,145,109,0.2);
        outline: none;
    }

    .brand-quick-link { transition: background-color 0.2s, box-shadow 0.2s; }
    .brand-quick-link:hover { background-color: #E8D5C4; }

    .profile-avatar-ring {
        box-shadow: 0 0 0 4px #E8D5C4, 0 0 0 6px #B9916D;
    }

    .card-accent { border-top: 3px solid #B9916D; }

    .stat-value { color: #B9916D; }

    .section-icon { background-color: #B9916D; }
    .section-icon-alt { background-color: #a47a59; }
</style>

<div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-1">
            <span class="inline-block w-1 h-8 rounded-full" style="background-color:#B9916D;"></span>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('My Profile') }}</h1>
        </div>
        <p class="mt-1 text-gray-500 ms-4">{{ __('Manage your account information and settings') }}</p>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="p-4 mb-6 rounded-lg border" style="background-color:#f0fdf4; border-color:#bbf7d0;">
            <div class="flex items-center">
                <svg class="w-5 h-5 me-2 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-green-800">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 mb-6 border border-red-200 rounded-lg bg-red-50">
            <div class="flex items-center">
                <svg class="w-5 h-5 me-2 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span class="text-red-800">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

        {{-- Left Column - Profile Card --}}
        <div class="lg:col-span-1">
            <div class="overflow-hidden bg-white rounded-xl shadow-md">

                {{-- Card gradient header --}}
                <div class="h-24" style="background: linear-gradient(135deg, #B9916D 0%, #c9a07c 60%, #E8D5C4 100%);"></div>

                {{-- Avatar (overlapping header) --}}
                <div class="flex flex-col items-center px-6 pb-6 -mt-12">
                    <div class="flex items-center justify-center w-24 h-24 mb-3 text-3xl font-bold text-white rounded-full profile-avatar-ring"
                         style="background-color:#B9916D;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>

                    {{-- Quick Stats --}}
                    <div class="w-full pt-5 mt-5 space-y-3 border-t border-gray-100">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">{{ __('Member Since') }}</span>
                            <span class="text-sm font-semibold stat-value">{{ $user->created_at->format('M Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">{{ __('Total Bookings') }}</span>
                            <span class="text-sm font-semibold stat-value">{{ $user->bookings()->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">{{ __('Email Verified') }}</span>
                            @if($user->email_verified_at)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white"
                                      style="background-color:#B9916D;">
                                    <svg class="w-3 h-3 me-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('Verified') }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    {{ __('Not Verified') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Quick Actions --}}
                    <div class="w-full pt-5 mt-5 space-y-2 border-t border-gray-100">
                        <a href="{{ route('customer.bookings') }}"
                            class="brand-quick-link flex items-center w-full p-3 rounded-lg bg-gray-50 group">
                            <div class="flex items-center justify-center w-10 h-10 me-3 rounded-lg section-icon shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-800">{{ __('My Bookings') }}</span>
                        </a>

                        <a href="{{ route('customer.halls.index') }}"
                            class="brand-quick-link flex items-center w-full p-3 rounded-lg bg-gray-50 group">
                            <div class="flex items-center justify-center w-10 h-10 me-3 rounded-lg section-icon-alt shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-800">{{ __('Browse Halls') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column - Edit Forms --}}
        <div class="space-y-8 lg:col-span-2">

            {{-- Profile Information Form --}}
            <div class="bg-white rounded-xl shadow-md card-accent overflow-hidden">
                <div class="px-6 pt-6 pb-1 flex items-center gap-2 mb-5">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg section-icon">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Profile Information') }}</h3>
                </div>

                <form action="{{ route('customer.profile.update') }}" method="POST" class="px-6 pb-6">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        {{-- Name --}}
                        <div>
                            <label for="name" class="block mb-1.5 text-sm font-medium text-gray-700">
                                {{ __('Full Name') }} <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name', $user->name) }}"
                                required
                                class="brand-input w-full px-4 py-2.5 border rounded-lg transition {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }}"
                            >
                            @error('name')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block mb-1.5 text-sm font-medium text-gray-700">
                                {{ __('Email Address') }} <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email', $user->email) }}"
                                required
                                class="brand-input w-full px-4 py-2.5 border rounded-lg transition {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }}"
                            >
                            @error('email')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label for="phone" class="block mb-1.5 text-sm font-medium text-gray-700">
                                {{ __('Phone Number') }}
                            </label>
                            <input
                                type="tel"
                                id="phone"
                                name="phone"
                                value="{{ old('phone', $user->phone) }}"
                                placeholder="+968 XXXX XXXX"
                                class="brand-input w-full px-4 py-2.5 border rounded-lg transition {{ $errors->has('phone') ? 'border-red-500' : 'border-gray-300' }}"
                            >
                            @error('phone')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- WhatsApp --}}
                        @if(isset($user->whatsapp) || true)
                        <div>
                            <label for="whatsapp" class="block mb-1.5 text-sm font-medium text-gray-700">
                                {{ __('WhatsApp Number') }}
                            </label>
                            <input
                                type="tel"
                                id="whatsapp"
                                name="whatsapp"
                                value="{{ old('whatsapp', $user->whatsapp ?? '') }}"
                                placeholder="+968 XXXX XXXX"
                                class="brand-input w-full px-4 py-2.5 border rounded-lg transition {{ $errors->has('whatsapp') ? 'border-red-500' : 'border-gray-300' }}"
                            >
                            @error('whatsapp')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        @endif
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex justify-end mt-6">
                        <button type="submit" class="brand-btn px-6 py-2.5 rounded-lg font-medium">
                            {{ __('Save Changes') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Change Password Form --}}
            <div class="bg-white rounded-xl shadow-md card-accent overflow-hidden">
                <div class="px-6 pt-6 pb-1 flex items-center gap-2 mb-5">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg section-icon-alt">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Change Password') }}</h3>
                </div>

                <form action="{{ route('customer.profile.password') }}" method="POST" class="px-6 pb-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-5">
                        {{-- Current Password --}}
                        <div>
                            <label for="current_password" class="block mb-1.5 text-sm font-medium text-gray-700">
                                {{ __('Current Password') }} <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="password"
                                id="current_password"
                                name="current_password"
                                required
                                class="brand-input w-full px-4 py-2.5 border rounded-lg transition {{ $errors->has('current_password') ? 'border-red-500' : 'border-gray-300' }}"
                            >
                            @error('current_password')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            {{-- New Password --}}
                            <div>
                                <label for="password" class="block mb-1.5 text-sm font-medium text-gray-700">
                                    {{ __('New Password') }} <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    required
                                    class="brand-input w-full px-4 py-2.5 border rounded-lg transition {{ $errors->has('password') ? 'border-red-500' : 'border-gray-300' }}"
                                >
                                @error('password')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Confirm New Password --}}
                            <div>
                                <label for="password_confirmation" class="block mb-1.5 text-sm font-medium text-gray-700">
                                    {{ __('Confirm New Password') }} <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="password"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    required
                                    class="brand-input w-full px-4 py-2.5 border border-gray-300 rounded-lg transition"
                                >
                            </div>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex justify-end mt-6">
                        <button type="submit" class="brand-btn px-6 py-2.5 rounded-lg font-medium">
                            {{ __('Update Password') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Danger Zone --}}
            <div class="p-6 bg-white border border-red-100 rounded-xl shadow-md">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-red-600">{{ __('Danger Zone') }}</h3>
                </div>
                <p class="mb-4 text-sm text-gray-500">
                    {{ __('Once you delete your account, all of your data will be permanently removed. This action cannot be undone.') }}
                </p>

                <button
                    type="button"
                    onclick="confirmDelete()"
                    class="px-4 py-2 text-white transition bg-red-600 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 text-sm font-medium"
                >
                    {{ __('Delete Account') }}
                </button>

                {{-- Delete Confirmation Modal --}}
                <div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
                    <div class="w-full max-w-md p-6 mx-4 bg-white rounded-xl shadow-2xl">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex items-center justify-center w-10 h-10 bg-red-100 rounded-full">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900">{{ __('Confirm Account Deletion') }}</h4>
                        </div>
                        <p class="mb-5 text-sm text-gray-500">
                            {{ __('Are you sure you want to delete your account? This action is permanent and cannot be undone.') }}
                        </p>

                        <form action="{{ route('customer.profile.destroy') }}" method="POST">
                            @csrf
                            @method('DELETE')

                            <div class="mb-5">
                                <label for="delete_password" class="block mb-1.5 text-sm font-medium text-gray-700">
                                    {{ __('Enter your password to confirm') }}
                                </label>
                                <input
                                    type="password"
                                    id="delete_password"
                                    name="password"
                                    required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition"
                                >
                            </div>

                            <div class="flex justify-end gap-3">
                                <button
                                    type="button"
                                    onclick="closeDeleteModal()"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 transition bg-gray-100 rounded-lg hover:bg-gray-200"
                                >
                                    {{ __('Cancel') }}
                                </button>
                                <button
                                    type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white transition bg-red-600 rounded-lg hover:bg-red-700"
                                >
                                    {{ __('Delete My Account') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete() {
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDeleteModal();
    });

    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });
</script>
@endpush
@endsection
