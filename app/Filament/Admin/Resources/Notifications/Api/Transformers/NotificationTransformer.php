<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Notifications\Api\Transformers;

use App\Filament\Api\Transformers\Transformer;
use Illuminate\Notifications\DatabaseNotification;

/**
 * @property DatabaseNotification $resource
 */
class NotificationTransformer extends Transformer {}
