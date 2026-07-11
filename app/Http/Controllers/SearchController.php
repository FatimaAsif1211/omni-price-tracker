<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class SearchController extends Controller
{
    private function client(): Client
    {
        return new Client([
            'verify' => false, 
            'timeout' => 15, // Reduced timeout to prevent frontend lag
            'http_errors' => false,
            'headers' => [
                'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
                'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.9',
            ],
        ]);
    }

    public function search(Request $request)
    {
        $query = trim($request->query('query', ''));
        if (empty($query)) {
            return response()->json(['status' => 'error', 'message' => 'Query is required.'], 400);
        }

        // Safe Fallback Aggregation Arrays
        $priceOyeResults = $this->searchPriceOye($query);
        $darazResults    = $this->searchDaraz($query);
        $naheedResults   = $this->searchNaheed($query);

        // Merge all safely parsed outputs
        $allResults = array_merge($priceOyeResults, $darazResults, $naheedResults);

        if (empty($allResults)) {
            return response()->json([
                'status'  => 'unavailable',
                'message' => "No results found for \"$query\" across PriceOye, Daraz, or Naheed.",
            ]);
        }

        // Build a clean keyword list for strict dynamic title filtering
        $searchQueryClean = strtolower($query);
        $stopWords = ['the','and','for','with','in','of','a','an','to','by','is','it'];
        $rawKeywords = array_filter(explode(' ', $searchQueryClean), fn($w) => strlen($w) > 1 && !in_array($w, $stopWords));
        
        // Remove plural trailing "s" for flexible matching execution (e.g., "perfumes" matches "perfume")
        $searchKeywords = array_map(function($word) {
            return rtrim($word, 's'); 
        }, $rawKeywords);

        // Deduplicate records via landing links with targeted validation filtering
        // Deduplicate records via landing links with targeted validation filtering
        $keyed = [];
        foreach ($allResults as $r) { 
            
            // TARGETED FILTER: Only filter PriceOye products
            if ($r['store'] === 'PriceOye') {
                $productNameLower = strtolower($r['name']);
                $isRelevant = false;

                foreach ($searchKeywords as $keyword) {
                    // Core dynamic text match
                    if (str_contains($productNameLower, $keyword) || str_contains($keyword, $productNameLower)) {
                        $isRelevant = true;
                        break;
                    }
                    
                    // FIXED: Handle generic category synonyms for "tablet"
                    if ($keyword === 'tablet') {
                        if (str_contains($productNameLower, 'pad') || 
                            str_contains($productNameLower, 'tab') || 
                            str_contains($productNameLower, 'ipad')) {
                            $isRelevant = true;
                            break;
                        }
                    }
                }

                // If a PriceOye product doesn't pass the keyword or synonym check, skip it
                if (!$isRelevant) {
                    continue;
                }
            }

            // Daraz and Naheed products skip the filter check and pass directly
            $keyed[$r['link']] = $r; 
        }
        
        $final = array_values($keyed);

        if (empty($final)) {
            return response()->json([
                'status'  => 'unavailable',
                'message' => "No matching results found for \"$query\".",
            ]);
        }
        
        // Global Low to High Sorting
        usort($final, fn($a, $b) => $a['price'] <=> $b['price']);

        // Save safely into your shared MySQL tables
        try {
            foreach ($final as $item) {
                Product::updateOrCreate(
                    ['product_link' => $item['link']],
                    [
                        'product_name' => $item['name'], 
                        'store_name'   => $item['store'],
                        'price'        => $item['price'], 
                        'image_url'    => $item['image'], 
                        'fetched_at'   => now()
                    ]
                );
            }
        } catch (\Exception $dbEx) {
            \Log::error('Database Sync Error: ' . $dbEx->getMessage());
        }

        return response()->json([
            'status'  => 'success', 
            'count'   => count($final), 
            'results' => $final
        ]);
    }

    // ── 1. PriceOye Scraper Engine ───────────────────────────────────────────
    private function searchPriceOye(string $query): array
    {
        try {
            $url = 'https://api.priceoye.pk/api/search_list?' . http_build_query([
                'category' => '1', 'widget' => '0', 'page' => '',
                'query' => $query, '__amp_source_origin' => 'https://priceoye.pk',
            ]);
            
            $response = $this->client()->get($url, [
                'headers' => ['Referer' => 'https://priceoye.pk/', 'Origin' => 'https://priceoye.pk', 'AMP-Same-Origin' => 'true'],
            ]);

            if ($response->getStatusCode() !== 200) return [];
            
            $body  = (string) $response->getBody();
            $items = json_decode($body, true)['items'] ?? [];

            $stopWords = ['the','and','for','with','in','of','a','an','to','by','is','it'];
            $keywords  = array_values(array_filter(explode(' ', strtolower($query)), fn($w) => strlen($w) > 1 && !in_array($w, $stopWords)));
            $phoneBrands = ['iphone','samsung','xiaomi','oppo','vivo','realme','nokia','infinix','tecno','oneplus','huawei','motorola','itel','honor'];
            $searchingPhone = (bool) array_filter($phoneBrands, fn($pk) => str_contains(strtolower($query), $pk));

            $results = [];
            foreach ($items as $item) {
                $name  = trim($item['title'] ?? ''); if (empty($name)) continue;
                if (isset($item['stock_status']) && $item['stock_status'] == 0) continue;
                $price = (int) str_replace(',', '', $item['lowest_price'] ?? '0'); if ($price <= 0) continue;
                $lower = strtolower($name);
                
                // If searching for specialized products like perfumes, bypass phone filters gracefully
                if ($searchingPhone) {
                    $firstKw = $keywords[0] ?? '';
                    if ($firstKw && !str_contains($lower, $firstKw)) continue;
                    $tw = $mw = 0;
                    foreach ($keywords as $kw) { $w = strlen($kw); $tw += $w; if (str_contains($lower, $kw)) $mw += $w; }
                    if ($tw > 0 && ($mw / $tw) < 0.4) continue;
                    if (!(bool) array_filter($phoneBrands, fn($pk) => str_contains(strtolower($query), $pk) && str_contains($lower, $pk))) continue;
                }

                $link  = $item['prodcutUrl'] ?? 'https://priceoye.pk/' . ($item['slug'] ?? '');
                $image = $item['image'] ?? ''; if (str_starts_with($image, '//')) $image = 'https:' . $image;
                $rp    = (int) str_replace(',', '', $item['retail_price'] ?? '0');
                
                $results[] = [
                    'name'         => ucwords(strtolower($name)), 
                    'price'        => $price,
                    'retail_price' => $rp > $price ? $rp : null,
                    'image'        => $image ?: 'https://static.priceoye.pk/images/product-placeholder.gif',
                    'link'         => $link, 
                    'store'        => 'PriceOye'
                ];
            }
            return $results;
        } catch (\Exception $e) { 
            \Log::error('PriceOye Scraper Error: ' . $e->getMessage()); 
            return []; 
        }
    }

    // ── 2. Daraz Scraper Engine ──────────────────────────────────────────────
    private function searchDaraz(string $query): array
    {
        try {
            $scriptPath = base_path("scraper/daraz_scraper.mjs");
            
            if (!file_exists($scriptPath)) {
                \Log::error("Daraz node script missing at path: " . $scriptPath);
                return [];
            }

            $output = shell_exec("node " . escapeshellarg($scriptPath) . " " . escapeshellarg($query));
            if (empty($output)) return [];

            $data = json_decode($output, true) ?? [];
            
            $results = [];
            foreach ($data as $item) {
                $priceText = $item['priceShow'] ?? '0';
                $cleanPrice = intval(preg_replace('/[^0-9]/', '', $priceText));
                
                if ($cleanPrice <= 0) continue;

                $results[] = [
                    'name'         => trim($item['name'] ?? 'No Name'),
                    'price'        => $cleanPrice,
                    'retail_price' => null,
                    'image'        => $item['image'] ?? 'https://static.priceoye.pk/images/product-placeholder.gif',
                    'link'         => $item['link'] ?? '#',
                    'store'        => 'Daraz'
                ];
            }
            return $results;
        } catch (\Exception $e) {
            \Log::error('Daraz Scraper Error: ' . $e->getMessage());
            return [];
        }
    }

    // ── 3. Naheed Scraper Engine (Enhanced and Isolated) ─────────────────────
    private function searchNaheed(string $query): array
    {
        try {
            $url = "https://www.naheed.pk/catalogsearch/result/?q=" . urlencode($query);

            // Using explicit timeout boundaries to keep external blocks from locking your app
            $response = Http::timeout(10)->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            ])->get($url);

            if (!$response->successful()) {
                \Log::warning('Naheed server returned status code: ' . $response->status());
                return [];
            }

            $crawler = new Crawler($response->body());
            $results = [];

            // Targeted mapping check over item blocks
            $productItems = $crawler->filter('.product-item, .item.product-item');
            if ($productItems->count() === 0) return [];

            $productItems->each(function (Crawler $node) use (&$results) {
                $nameNode  = $node->filter('.product-item-link, a.product-item-photo');
                $priceNode = $node->filter('.price, [data-price-type="finalPrice"] .price');
                $imgNode   = $node->filter('img.product-image-photo, .product-image-container img');

                if ($nameNode->count() > 0 && $priceNode->count() > 0) {
                    $rawPrice = $priceNode->first()->text();
                    $cleanPrice = intval(preg_replace('/[^0-9]/', '', $rawPrice));

                    if ($cleanPrice > 0) {
                        $title = trim($nameNode->first()->text());
                        // Fallback processing for title extraction if textual node yields blanks
                        if (empty($title) && $imgNode->count() > 0) {
                            $title = trim($imgNode->attr('alt') ?? '');
                        }

                        $link  = $nameNode->first()->attr('href') ?? '#';
                        $image = $imgNode->count() > 0 ? ($imgNode->attr('data-src') ?? $imgNode->attr('src')) : 'https://static.priceoye.pk/images/product-placeholder.gif';

                        if (!empty($title)) {
                            $results[] = [
                                'name'         => ucwords(strtolower($title)),
                                'price'        => $cleanPrice,
                                'retail_price' => null,
                                'image'        => $image,
                                'link'         => $link,
                                'store'        => 'Naheed'
                            ];
                        }
                    }
                }
            });

            return $results;
        } catch (\Exception $e) {
            \Log::error('Naheed Scraper Bypass Exception: ' . $e->getMessage());
            return [];
        }
    }
}