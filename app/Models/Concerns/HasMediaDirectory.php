<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use LogicException;

trait HasMediaDirectory
{
    public static function bootHasMediaDirectory(): void
    {
        $event = in_array(SoftDeletes::class, class_uses_recursive(static::class), true) ? 'forceDeleted' : 'deleted';

        static::registerModelEvent($event, function (self $model): void {
            $model->mediaDisk()->deleteDirectory($model->mediaDirectory());
        });
    }

    public function mediaDirectory(): string
    {
        $salt = (string) config('filesystems.media_hash_salt');

        if ($salt === '') {
            throw new LogicException('MEDIA_HASH_SALT is not set; refusing to build a guessable media path.');
        }

        if (blank($this->getKey())) {
            throw new LogicException('A media directory needs a stored record with a key.');
        }

        $hash = md5($salt.$this->getKey());

        return implode('/', [
            app()->environment(),
            $this->getTable(),
            substr($hash, 0, 2),
            substr($hash, 2, 2),
            substr($hash, 4, 2),
            substr($hash, 6),
        ]);
    }

    public function mediaPath(string $filename): string
    {
        return $this->mediaDirectory().'/'.ltrim($filename, '/');
    }

    public function mediaDisk(): Filesystem
    {
        return Storage::disk();
    }
}
