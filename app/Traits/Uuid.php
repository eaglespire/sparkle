<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait Uuid
{
    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            if (! $model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }
}