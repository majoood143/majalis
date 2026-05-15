<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use WallaceMartinss\FilamentEvolution\Enums\StatusConnectionEnum;
use WallaceMartinss\FilamentEvolution\Facades\Whatsapp;
use WallaceMartinss\FilamentEvolution\Models\WhatsappInstance;
use Illuminate\Validation\ValidationException;

class WhatsAppTester extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected string $view = 'filament.pages.whatsapp-tester';
    protected static string | \UnitEnum | null $navigationGroup = 'WhatsApp';
    protected static ?int $navigationSort = 1;

    public ?array $data = [];
    public ?string $messageType = 'text';

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Message Configuration')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('instance_id')
                                    ->label('WhatsApp Instance')
                                    ->options(fn() => WhatsappInstance::where('status', StatusConnectionEnum::OPEN)
                                        ->pluck('name', 'id'))
                                    ->required()
                                    ->placeholder('Select a connected instance')
                                    ->helperText('Only connected instances are shown')
                                    ->live(),

                                TextInput::make('number')
                                    ->label('Phone Number')
                                    ->required()
                                    ->placeholder('5511999999999')
                                    ->helperText('Format: Country code + area code + number (without + or spaces)')
                                    ->maxLength(20)
                                    ->live(),

                                Select::make('message_type')
                                    ->label('Message Type')
                                    ->options([
                                        'text' => 'Text Message',
                                        'image' => 'Image',
                                        'video' => 'Video',
                                        'audio' => 'Audio',
                                        'document' => 'Document',
                                        'location' => 'Location',
                                        'contact' => 'Contact',
                                    ])
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state) {
                                        $this->messageType = $state;
                                    }),
                            ]),
                    ]),

                // Text Message Fields
                Group::make()
                    ->schema([
                        Section::make('Text Message')
                            ->schema([
                                Textarea::make('message')
                                    ->label('Message Content')
                                    ->required()
                                    ->rows(5)
                                    ->placeholder('Enter your message here...'),
                            ]),
                    ])
                    ->visible(fn(callable $get) => $get('message_type') === 'text'),

                // Media Fields
                Group::make()
                    ->schema([
                        Section::make('Media Message')
                            ->schema([
                                FileUpload::make('media')
                                    ->label('Media File')
                                    ->required()
                                    ->disk('public')
                                    ->directory('whatsapp-test')
                                    ->maxSize(16384)
                                    ->visibility('public'),

                                Textarea::make('caption')
                                    ->label('Caption')
                                    ->rows(2)
                                    ->placeholder('Optional caption for your media'),
                            ]),
                    ])
                    ->visible(fn(callable $get) => in_array($get('message_type'), ['image', 'video', 'audio', 'document'])),

                // Location Fields
                Group::make()
                    ->schema([
                        Section::make('Location')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('latitude')
                                            ->label('Latitude')
                                            ->required()
                                            ->numeric()
                                            ->placeholder('-23.5505'),

                                        TextInput::make('longitude')
                                            ->label('Longitude')
                                            ->required()
                                            ->numeric()
                                            ->placeholder('-46.6333'),
                                    ]),

                                TextInput::make('location_name')
                                    ->label('Location Name')
                                    ->placeholder('My Office'),

                                TextInput::make('address')
                                    ->label('Address')
                                    ->placeholder('São Paulo, SP'),
                            ]),
                    ])
                    ->visible(fn(callable $get) => $get('message_type') === 'location'),

                // Contact Fields
                Group::make()
                    ->schema([
                        Section::make('Contact')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('contact_name')
                                            ->label('Contact Name')
                                            ->required()
                                            ->placeholder('John Doe'),

                                        TextInput::make('contact_number')
                                            ->label('Phone Number')
                                            ->required()
                                            ->placeholder('+5511888888888'),
                                    ]),
                            ]),
                    ])
                    ->visible(fn(callable $get) => $get('message_type') === 'contact'),

            ])
            ->statePath('data');
    }

    public function send(): void
    {
        $data = $this->form->getState();

        try {
            // Validate required fields
            if (empty($data['instance_id'])) {
                throw ValidationException::withMessages(['instance_id' => 'Please select a WhatsApp instance']);
            }

            if (empty($data['number'])) {
                throw ValidationException::withMessages(['number' => 'Please enter a phone number']);
            }

            $instanceId = $data['instance_id'];
            $number = $data['number'];
            $type = $data['message_type'];

            // Send message based on type
            $result = match ($type) {
                'text' => Whatsapp::sendText($instanceId, $number, $data['message']),
                'image' => Whatsapp::sendImage($instanceId, $number, $data['media'], $data['caption'] ?? null),
                'video' => Whatsapp::sendVideo($instanceId, $number, $data['media'], $data['caption'] ?? null),
                'audio' => Whatsapp::sendAudio($instanceId, $number, $data['media']),
                'document' => Whatsapp::sendDocument($instanceId, $number, $data['media'], null, $data['caption'] ?? null),
                'location' => Whatsapp::sendLocation(
                    $instanceId,
                    $number,
                    (float) $data['latitude'],
                    (float) $data['longitude'],
                    $data['location_name'] ?? null,
                    $data['address'] ?? null
                ),
                'contact' => Whatsapp::sendContact($instanceId, $number, $data['contact_name'], $data['contact_number']),
                default => throw new \Exception('Unsupported message type'),
            };

            // Success notification
            Notification::make()
                ->title('Message sent successfully!')
                ->success()
                ->body("Message sent to {$number} via WhatsApp")
                ->send();

            // Reset form but keep instance and message type
            $this->form->fill([
                'instance_id' => $data['instance_id'],
                'message_type' => $type,
                'number' => '', // Clear number for next test
            ]);
        } catch (ValidationException $e) {
            Notification::make()
                ->title('Validation Error')
                ->danger()
                ->body($e->getMessage())
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error sending message')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('send')
                ->label('Send Message')
                ->action('send')
                ->color('primary')
                ->icon('heroicon-o-paper-airplane'),
        ];
    }

    public static function canAccess(): bool
    {
        return true; // Modify based on your authorization logic
    }
}
