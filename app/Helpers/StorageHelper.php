<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class StorageHelper
{
    /**
     * Return Storage::url for a stored path, but normalize any leading 'storage/' or leading slashes
     * so we never produce '/storage/storage/...'.
     *
     * @param string|null $path
     * @return string|null
     */
    public static function url(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $p = ltrim($path, '/');
        if (str_starts_with($p, 'storage/')) {
            $p = substr($p, strlen('storage/'));
        }

        return Storage::url($p);
    }
}
