<?php

namespace App\Integrations;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SupplierApiClient
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 15,
            'connect_timeout' => 5,
            'verify' => config('app.env') === 'production',
        ]);
    }

    public function getProductData(string $supplierCode, string $productRef): ?array
    {
        return Cache::remember(
            "supplier:{$supplierCode}:product:{$productRef}",
            now()->addHours(1),
            fn() => $this->fetchFromApi($supplierCode, $productRef)
        );
    }

    private function fetchFromApi(string $supplierCode, string $productRef): ?array
    {
        $config = config("suppliers.{$supplierCode}");

        if (!$config) {
            Log::error("Configuration manquante pour le fournisseur: {$supplierCode}");
            return null;
        }

        try {
            $response = $this->client->get($config['endpoint'], [
                'query' => [
                    'reference' => $productRef,
                    'api_key' => $config['api_key']
                ],
                'headers' => $config['headers'] ?? []
            ]);

            return $this->parseResponse(
                $response->getBody()->getContents(),
                $config['format'] ?? 'json'
            );

        } catch (\Exception $e) {
            Log::error("API Error - {$supplierCode}: " . $e->getMessage());
            return null;
        }
    }

    private function parseResponse(string $content, string $format): array
    {
        return match ($format) {
            'json' => json_decode($content, true) ?? [],
            'xml' => $this->parseXml($content),
            default => throw new \RuntimeException("Format {$format} non supporté")
        };
    }

    private function parseXml(string $xml): array
    {
        // Implémentez le parsing XML si nécessaire
        return [];
    }
}