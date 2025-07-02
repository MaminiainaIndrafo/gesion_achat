<?php

namespace App\Services;

use Shopify\Clients\Rest;
use Illuminate\Support\Facades\Cache;

class ShopifyService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Rest(
            config('shopify.domain'),
            config('shopify.access_token'),
            ['version' => config('shopify.api_version')]
        );
    }

    public function getProductBySku(string $sku): ?array
    {
        return Cache::remember("shopify:product:{$sku}", now()->addHours(2), function() use ($sku) {
            $response = $this->client->get('products', [
                'sku' => $sku,
                'limit' => 1,
                'fields' => 'id,title,variants'
            ]);

            return $response->getDecodedBody()['products'][0]['variants'][0] ?? null;
        });
    }

    public function getAllProducts(): array
    {
        return Cache::remember('shopify:products:all', now()->addHours(6), function() {
            $products = [];
            $pageInfo = null;

            do {
                $params = ['limit' => 250, 'fields' => 'id,title,variants'];
                if ($pageInfo) {
                    $params['page_info'] = $pageInfo;
                }

                $response = $this->client->get('products', $params);
                $products = array_merge($products, $response->getDecodedBody()['products']);

                $linkHeader = $response->getHeader('link')[0] ?? null;
                $pageInfo = $this->extractNextPageInfo($linkHeader);
            } while ($pageInfo);

            return $products;
        });
    }

    protected function extractNextPageInfo(?string $linkHeader): ?string
    {
        if (!$linkHeader) return null;

        if (preg_match('/<([^>]+)>; rel="next"/', $linkHeader, $matches)) {
            $url = parse_url($matches[1]);
            parse_str($url['query'], $query);
            return $query['page_info'] ?? null;
        }

        return null;
    }
}