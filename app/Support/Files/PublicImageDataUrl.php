<?php

namespace App\Support\Files;

use Illuminate\Support\Facades\Storage;
use Throwable;

class PublicImageDataUrl
{
    public static function fromPath(?string $path): ?string
    {
        if (! is_string($path)) {
            return null;
        }

        $normalizedPath = trim(str_replace('\\', '/', $path));
        $normalizedPath = ltrim($normalizedPath, '/');

        if ($normalizedPath === '') {
            return null;
        }

        if (str_contains($normalizedPath, '../') || str_contains($normalizedPath, "\0")) {
            return null;
        }

        if (preg_match('/^[A-Za-z]:\//', $normalizedPath) === 1) {
            return null;
        }

        try {
            $disk = Storage::disk('public');

            if (! $disk->exists($normalizedPath)) {
                return null;
            }

            $mimeType = (string) ($disk->mimeType($normalizedPath) ?: '');
            if (! str_starts_with($mimeType, 'image/')) {
                return null;
            }

            $contents = $disk->get($normalizedPath);
            if ($contents === '') {
                return null;
            }

            return 'data:'.$mimeType.';base64,'.base64_encode($contents);
        } catch (Throwable) {
            return null;
        }
    }
}
