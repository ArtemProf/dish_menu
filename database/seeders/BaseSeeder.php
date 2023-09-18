<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BaseSeeder extends Seeder
{
    public function getSeedsFromJsonOrFail(string $pathFile): array
    {
        $result = [];
        $path = pathinfo($pathFile);

        $dir = $path['dirname'];
        $files = scandir($dir);

        foreach ($files as $file) {
            if (!str_contains($file, $path['filename'])) {
                continue;
            }
            $content = $this->getJsonFileContent($dir . DIRECTORY_SEPARATOR . $file);
            $result = array_merge($result, $content);
        }

        return $result;
    }

    protected function getJsonFileContent(string $pathFile): array
    {
        if (!file_exists($pathFile)) {
            throw new \Exception(sprintf('Seeder json file %s doesnt exists', $pathFile));
        }
        $content = file_get_contents($pathFile);

        if (!$content) {
            return [];
        }

        $json = json_decode($content, true);
        if (!$json) {
            return [];
        }

        return $json;
    }
}
