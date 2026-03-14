<?php

namespace Tests\Unit;

use App\Support\Files\PublicImageDataUrl;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicImageDataUrlTest extends TestCase
{
    public function test_it_rejects_path_traversal(): void
    {
        $dataUrl = PublicImageDataUrl::fromPath('../.env');

        $this->assertNull($dataUrl);
    }

    public function test_it_returns_data_url_for_valid_public_image(): void
    {
        $relativePath = 'tests/tiny-hardening.png';
        $tinyPng = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO2B9hsAAAAASUVORK5CYII=');

        Storage::disk('public')->put($relativePath, $tinyPng);

        $dataUrl = PublicImageDataUrl::fromPath($relativePath);

        $this->assertIsString($dataUrl);
        $this->assertStringStartsWith('data:image/png;base64,', $dataUrl);

        Storage::disk('public')->delete($relativePath);
    }
}
