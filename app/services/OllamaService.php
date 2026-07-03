<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Throwable;

class OllamaService
{
    public function generate(string $prompt): string
    {
        try {
            $response = Http::timeout(120)
                ->post('http://127.0.0.1:11434/api/generate', [
                    'model' => 'gemma3:4b',
                    'prompt' => $prompt,
                    'stream' => false,
                ]);

            if (! $response->successful()) {
                return 'Ollama sedang tidak aktif. Jalankan \'ollama serve\'.';
            }

            $hasil = trim((string) data_get($response->json(), 'response', ''));

            return $hasil !== '' ? $hasil : 'Tidak ada analisis.';
        } catch (Throwable $th) {
            return 'Ollama sedang tidak aktif. Jalankan \'ollama serve\'.';
        }
    }
}
