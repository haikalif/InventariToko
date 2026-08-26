<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Visus\Cuid2\Cuid2;

/**
 * Pasang trait ini di setiap Model yang primary key-nya CUID.
 * Butuh package: composer require visus/cuid2
 */
trait HasCuid
{
    protected static function bootHasCuid(): void
    {
        static::creating(function (Model $model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) new Cuid2();
            }
        });
    }

    public function initializeHasCuid(): void
    {
        $this->keyType = 'string';
        $this->incrementing = false;
    }
}
