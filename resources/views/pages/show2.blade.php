{{--
    Static Page View Template
    Displays static content pages with RTL support for Arabic

    @param Page $page The page model instance
    @param string $title Localized page title
    @param string $content Localized page content
    @param string|null $metaTitle SEO meta title
    @param string|null $metaDescription SEO meta description
--}}

@extends('layouts.app')

@section('title', $metaTitle ?? $title)

@push('meta')
    {{-- SEO Meta Tags --}}
    <meta name="description" content="{{ $metaDescription ?? $title }}">
    <meta property="og:title" content="{{ $metaTitle ?? $title }}">
    <meta property="og:description" content="{{ $metaDescription ?? $title }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
@endpush

@section('content')
    <div class="min-h-screen bg-gray-50 py-8 {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- Breadcrumb Navigation --}}
            <nav class="mb-8" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'flex-row-reverse space-x-reverse' : '' }}">
                    <li>
                        {{-- <a href="{{ route('/') }}" class="text-gray-500 hover:text-gray-700"> --}}
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                            </svg>
                        </a>
                    </li>
                    <li>
                        <span class="text-gray-400">/</span>
                    </li>
                    <li>
                        <span class="font-medium text-gray-900">{{ $title }}</span>
                    </li>
                </ol>
            </nav>

            {{-- Page Content Card --}}
            <div class="overflow-hidden bg-white rounded-lg shadow-sm">

                {{-- Page Header --}}
                <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-12 sm:px-12 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                    <h1 class="text-3xl font-bold text-white sm:text-4xl">
                        {{ $title }}
                    </h1>
                    <p class="mt-2 text-sm text-blue-100">
                        {{ __('pages.last_updated') }}: {{ $page->updated_at->translatedFormat('d F Y') }}
                    </p>
                </div>

                {{-- Page Content --}}
                <div class="px-6 py-8 sm:px-12 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                    <div class="prose max-w-none {{ app()->getLocale() === 'ar' ? 'prose-rtl' : '' }}">
                        {!! $content !!}
                    </div>
                </div>

                {{-- Page Footer with Contact CTA --}}
                @if($page->slug !== 'contact-us')
                    <div class="bg-gray-50 px-6 py-8 sm:px-12 border-t border-gray-200 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                        <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ __('pages.need_help') }}
                                </h3>
                                <p class="mt-1 text-gray-600">
                                    {{ __('pages.contact_description') }}
                                </p>
                            </div>
                            <a href="{{ route('pages.contact-us') }}"
                               class="inline-flex items-center px-6 py-3 font-medium text-white transition-colors duration-200 bg-blue-600 rounded-lg hover:bg-blue-700">
                                {{ __('pages.contact_us') }}
                                <svg class="w-5 h-5 {{ app()->getLocale() === 'ar' ? 'mr-2 rotate-180' : 'ml-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Related Pages Section --}}
            @php
                $relatedPages = \App\Models\Page::active()
                    ->where('id', '!=', $page->id)
                    ->ordered()
                    ->limit(3)
                    ->get();
            @endphp

            @if($relatedPages->count() > 0)
                <div class="mt-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                        {{ __('pages.related_pages') }}
                    </h2>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        @foreach($relatedPages as $relatedPage)
                            <a href="{{ route('pages.show', $relatedPage->slug) }}"
                               class="block bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden group {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 transition-colors group-hover:text-blue-600">
                                        {{ $relatedPage->title }}
                                    </h3>
                                    <p class="mt-2 text-sm text-gray-600 line-clamp-2">
                                        {{ Str::limit(strip_tags($relatedPage->content), 100) }}
                                    </p>
                                    <span class="inline-flex items-center mt-4 text-sm font-medium text-blue-600">
                                        {{ __('pages.read_more') }}
                                        <svg class="w-4 h-4 {{ app()->getLocale() === 'ar' ? 'mr-1 rotate-180' : 'ml-1' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Custom Prose Styles for RTL */
        .prose-rtl {
            direction: rtl;
            text-align: right;
        }

        .prose-rtl ul,
        .prose-rtl ol {
            padding-right: 1.5rem;
            padding-left: 0;
        }

        .prose-rtl li {
            text-align: right;
        }

        /* Responsive Typography */
        .prose h2 {
            font-size: 1.875rem;
            font-weight: 700;
            margin-top: 2rem;
            margin-bottom: 1rem;
            color: #1f2937;
        }

        .prose h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            color: #374151;
        }

        .prose p {
            margin-bottom: 1rem;
            line-height: 1.75;
            color: #4b5563;
        }

        .prose ul,
        .prose ol {
            margin-bottom: 1rem;
        }

        .prose li {
            margin-bottom: 0.5rem;
        }

        .prose a {
            color: #2563eb;
            text-decoration: underline;
        }

        .prose a:hover {
            color: #1d4ed8;
        }

        /* Line Clamp Utility */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endpush
