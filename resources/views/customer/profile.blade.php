{{--
    Customer Profile View
    Displays user profile information with edit functionality

    @extends customer.layout
    @version 1.0.0
--}}

@extends('customer.layout')

@section('title', __('My Profile') . ' - Majalis')

@section('content')
<div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="mb-2 text-3xl font-bold text-gray-900">{{ __('My Profile') }}</h1>
        <p class="text-gray-600">{{ __('Manage your account information and settings') }}</p>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="p-4 mb-6 border border-green-200 rounded-lg bg-green-50">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-green-800">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 mb-6 border border-red-200 rounded-lg bg-red-50">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span class="text-red-800">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

        {{-- Left Column - Profile Card --}}
        <div class="lg:col-span-1">
            <div class="p-6 bg-white rounded-lg shadow-md">
                {{-- Avatar --}}
                <div class="flex flex-col items-center mb-6">
                    <div class="flex items-center justify-center w-24 h-24 mb-4 text-3xl font-bold text-white bg-indigo-600 rounded-full">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ $user->name }}</h2>
                    <p class="text-gray-600">{{ $user->email }}</p>
                </div>

                {{-- Quick Stats --}}
                <div class="pt-6 space-y-4 border-t">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">{{ __('Member Since') }}</span>
                        <span class="font-medium text-gray-900">{{ $user->created_at->format('M Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">{{ __('Total Bookings') }}</span>
                        <span class="font-medium text-gray-900">{{ $user->bookings()->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">{{ __('Email Verified') }}</span>
                        @if($user->email_verified_at)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
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
                <div class="pt-6 mt-6 space-y-3 border-t">
                    <a href="{{ route('customer.bookings') }}"
                        class="flex items-center w-full p-3 transition rounded-lg bg-gray-50 hover:bg-gray-100 group">
                        <div class="flex items-center justify-center w-10 h-10 mr-3 bg-indigo-600 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="font-medium text-gray-900 group-hover:text-gray-600">{{ __('My Bookings') }}</span>
                    </a>

                    <a href="{{ route('customer.halls.index') }}"
                        class="flex items-center w-full p-3 transition rounded-lg bg-gray-50 hover:bg-gray-100 group">
                        <div class="flex items-center justify-center w-10 h-10 mr-3 bg-green-600 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <span class="font-medium text-gray-900 group-hover:text-gray-600">{{ __('Browse Halls') }}</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Right Column - Edit Forms --}}
        <div class="space-y-8 lg:col-span-2">

            {{-- Profile Information Form --}}
            <div class="p-6 bg-white rounded-lg shadow-md">
                <h3 class="mb-6 text-lg font-semibold text-gray-900">{{ __('Profile Information') }}</h3>

                <form action="{{ route('customer.profile.update') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        {{-- Name --}}
                        <div>
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-700">
                                {{ __('Full Name') }} <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name', $user->name) }}"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror"
                            >
                            @error('name')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-700">
                                {{ __('Email Address') }} <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email', $user->email) }}"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror"
                            >
                            @error('email')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label for="phone" class="block mb-2 text-sm font-medium text-gray-700">
                                {{ __('Phone Number') }}
                            </label>
                            <input
                                type="tel"
                                id="phone"
                                name="phone"
                                value="{{ old('phone', $user->phone) }}"
                                placeholder="+968 XXXX XXXX"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('phone') border-red-500 @enderror"
                            >
                            @error('phone')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- WhatsApp (if applicable) --}}
                        @if(isset($user->whatsapp) || true)
                        <div>
                            <label for="whatsapp" class="block mb-2 text-sm font-medium text-gray-700">
                                {{ __('WhatsApp Number') }}
                            </label>
                            <input
                                type="tel"
                                id="whatsapp"
                                name="whatsapp"
                                value="{{ old('whatsapp', $user->whatsapp ?? '') }}"
                                placeholder="+968 XXXX XXXX"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('whatsapp') border-red-500 @enderror"
                            >
                            @error('whatsapp')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        @endif
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex justify-end mt-6">
                        <button
                            type="submit"
                            class="px-6 py-2 text-white transition bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            {{ __('Save Changes') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Change Password Form --}}
            <div class="p-6 bg-white rounded-lg shadow-md">
                <h3 class="mb-6 text-lg font-semibold text-gray-900">{{ __('Change Password') }}</h3>

                <form action="{{ route('customer.profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        {{-- Current Password --}}
                        <div>
                            <label for="current_password" class="block mb-2 text-sm font-medium text-gray-700">
                                {{ __('Current Password') }} <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="password"
                                id="current_password"
                                name="current_password"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('current_password') border-red-500 @enderror"
                            >
                            @error('current_password')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            {{-- New Password --}}
                            <div>
                                <label for="password" class="block mb-2 text-sm font-medium text-gray-700">
                                    {{ __('New Password') }} <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('password') border-red-500 @enderror"
                                >
                                @error('password')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Confirm New Password --}}
                            <div>
                                <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-700">
                                    {{ __('Confirm New Password') }} <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="password"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                >
                            </div>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex justify-end mt-6">
                        <button
                            type="submit"
                            class="px-6 py-2 text-white transition bg-gray-800 rounded-lg hover:bg-gray-900 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                        >
                            {{ __('Update Password') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Delete Account Section --}}
            <div class="p-6 bg-white border border-red-100 rounded-lg shadow-md">
                <h3 class="mb-2 text-lg font-semibold text-red-600">{{ __('Danger Zone') }}</h3>
                <p class="mb-4 text-sm text-gray-600">
                    {{ __('Once you delete your account, all of your data will be permanently removed. This action cannot be undone.') }}
                </p>

                <button
                    type="button"
                    onclick="confirmDelete()"
                    class="px-4 py-2 text-white transition bg-red-600 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                >
                    {{ __('Delete Account') }}
                </button>

                {{-- Delete Confirmation Modal --}}
                <div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
                    <div class="w-full max-w-md p-6 mx-4 bg-white rounded-lg shadow-xl">
                        <h4 class="mb-4 text-lg font-semibold text-gray-900">{{ __('Confirm Account Deletion') }}</h4>
                        <p class="mb-6 text-gray-600">
                            {{ __('Are you sure you want to delete your account? This action is permanent and cannot be undone.') }}
                        </p>

                        <form action="{{ route('customer.profile.destroy') }}" method="POST">
                            @csrf
                            @method('DELETE')

                            <div class="mb-4">
                                <label for="delete_password" class="block mb-2 text-sm font-medium text-gray-700">
                                    {{ __('Enter your password to confirm') }}
                                </label>
                                <input
                                    type="password"
                                    id="delete_password"
                                    name="password"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                >
                            </div>

                            <div class="flex justify-end space-x-3">
                                <button
                                    type="button"
                                    onclick="closeDeleteModal()"
                                    class="px-4 py-2 text-gray-800 transition bg-gray-200 rounded-lg hover:bg-gray-300"
                                >
                                    {{ __('Cancel') }}
                                </button>
                                <button
                                    type="submit"
                                    class="px-4 py-2 text-white transition bg-red-600 rounded-lg hover:bg-red-700"
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

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteModal();
        }
    });

    // Close modal on backdrop click
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
</script>
@endpush
@endsection
