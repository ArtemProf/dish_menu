<?php

namespace App\Services\OCR;

use App\Enums\OcrSpaceLanguage;
use App\Exceptions\OcrException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Mockery\Exception;

class OcrService extends BaseOcrService
{
    const Engine1 = 1;
    const Engine2 = 2;
    const Engine3 = 3;

    public function processImage(UploadedFile $file, string $language = OcrSpaceLanguage::RUSSIAN->value): string
    {
        $payload  = [
            'language'          => $language,
            'OCREngine'         => self::Engine1
        ];
        $response = Http::withHeader('apiKey', $this->token)
                        ->timeout(120)
                        ->attach('file', $file->getContent(), 'image.jpg')
                        ->post($this->baseUrl, $payload)
                        ->throw(function ($response) {
                            $this->validateError($response);
                        });

        $response = $this->validateError($response);

        if (!isset($response->ParsedResults, $response->ParsedResults[0], $response->ParsedResults[0]->ParsedText)) {
            throw new OcrException(__('ocr.error_during_image_recognition_upload_another'));
        }

        return $response->ParsedResults[0]->ParsedText;
    }

    protected function validateError(Response $response): ?object
    {
        $responseObject = $response->object();

        if (!empty($responseObject->ErrorMessage)) {
            throw new OcrException(__('ocr.error_during_image_recognition', [
                'error' => implode('; ', $responseObject->ErrorMessage)
            ]));
        }

        if (!empty($responseObject->IsErroredOnProcessing)) {
            throw new OcrException(__('ocr.error_during_image_recognition_upload_another'));
        }

        return $responseObject;
    }
}
