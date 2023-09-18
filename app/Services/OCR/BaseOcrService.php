<?php

namespace App\Services\OCR;

use Illuminate\Http\UploadedFile;

abstract class BaseOcrService
{
    protected array $config;
    protected string $baseUrl;
    protected string $token;
    protected string $image;
    protected string $text = '';

    public function __construct()
    {
        $this->config = config('services.ocr');
        $this->baseUrl = $this->config['base_url'];
        $this->token = $this->config['token'];
    }

    public function processImage(UploadedFile $file) {}
}
