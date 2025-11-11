{{-- 
    Ticket Message View Template
    
    This template is used to display individual ticket messages in the admin panel.
    Place this file in: resources/views/filament/ticket-message-view.blade.php
    
    @var $record App\Models\TicketMessage
--}}

<div class="space-y-4">
    {{-- Message Header --}}
    <div class="flex items-start justify-between border-b pb-3">
        <div class="flex items-center space-x-3">
            {{-- User Avatar --}}
            <div class="flex-shrink-0">
                <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                        {{ strtoupper(substr($record->user->name, 0, 2)) }}
                    </span>
                </div>
            </div>
            
            {{-- User Info --}}
            <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ $record->user->name }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $record->user->email }}
                </p>
            </div>
        </div>

        {{-- Message Type Badge --}}
        <div>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                @if($record->type === App\Models\TicketMessageType::STAFF_REPLY) bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                @elseif($record->type === App\Models\TicketMessageType::INTERNAL_NOTE) bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100
                @else bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100
                @endif">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                {{ $record->type->getLabel() }}
            </span>
        </div>
    </div>

    {{-- Message Content --}}
    <div class="prose prose-sm dark:prose-invert max-w-none">
        <div class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
            {{ $record->message }}
        </div>
    </div>

    {{-- Attachments --}}
    @if($record->has_attachments)
        <div class="border-t pt-3">
            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                Attachments ({{ $record->attachments_count }})
            </h4>
            
            <div class="space-y-2">
                @foreach($record->attachments as $index => $attachment)
                    <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="flex items-center space-x-2">
                            {{-- File Icon --}}
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            
                            {{-- File Info --}}
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $attachment['original_name'] ?? 'Attachment ' . ($index + 1) }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ App\Models\TicketMessage::formatFileSize($attachment['size'] ?? 0) }}
                                </p>
                            </div>
                        </div>

                        {{-- Download Button --}}
                        <a href="{{ $record->getAttachmentDownloadUrl($index) }}" 
                           class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                           download>
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Message Metadata --}}
    <div class="border-t pt-3">
        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
            <div class="flex items-center space-x-4">
                {{-- Posted Time --}}
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Posted {{ $record->created_at->diffForHumans() }}
                </span>

                {{-- Read Status --}}
                @if($record->is_read)
                    <span class="flex items-center text-green-600 dark:text-green-400">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Read {{ $record->read_at?->diffForHumans() }}
                    </span>
                @else
                    <span class="flex items-center text-gray-400">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Unread
                    </span>
                @endif

                {{-- Internal Note Indicator --}}
                @if($record->is_internal)
                    <span class="flex items-center text-yellow-600 dark:text-yellow-400">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                        Internal Only
                    </span>
                @endif
            </div>

            {{-- IP Address (for audit) --}}
            @if($record->ip_address)
                <span class="text-gray-400" title="IP Address">
                    {{ $record->ip_address }}
                </span>
            @endif
        </div>
    </div>
</div>
