<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Product;

class PriceoyeController extends Controller
{
    private const API_URL = 'https://api.priceoye.pk/api/search_list';

    public function search(Request $request)
    {
        $query = trim($request->query('query', ''));

        if (empty($query)) {
            return response()->json(['status' => 'error', 'message' => 'Query parameter is required.'], 400);
        }

        // ── Call the real PriceOye API ─────────────────────────────────────
        $url = self::API_URL . '?' . http_build_query([
            'category'            => '1',
            'widget'              => '0',
            'page'                => '',
            'query'               => $query,
            '__amp_source_origin' => 'https://priceoye.pk',
        ]);

        try {
            $client = new Client([
                'verify'  => false,
                'timeout' => 20,
                'headers' => [
                    'User-Agent'      => 'Mozilla/5.0 (Linux; Android 11; Pixel 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Mobile Safari/537.36',
                    'Accept'          => 'application/json',
                    'Accept-Language' => 'en-US,en;q=0.9',
                    'Referer'         => 'https://priceoye.pk/',
                    'Origin'          => 'https://priceoye.pk',
                    'AMP-Same-Origin' => 'true',
                ],
            ]);

            $body = (string) $client->get($url)->getBody();
            $data = json_decode($body, true);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'API request failed: ' . $e->getMessage()], 500);
        }

        $items = $data['items'] ?? [];

        // ── If API returned nothing, this product doesn't exist on PriceOye ─
        if (empty($items)) {
            return response()->json([
                'status'  => 'unavailable',
                'message' => "\"$query\" is not available on PriceOye.",
            ]);
        }

        // ── Build keyword list from the query ─────────────────────────────
        // Split into individual words, ignore short filler words
        $stopWords = ['the','and','for','with','in','of','a','an','to','by','on','at','is','it'];
        $keywords  = array_values(array_filter(
            explode(' ', strtolower($query)),
            fn($w) => strlen($w) > 1 && !in_array($w, $stopWords)
        ));

        // ── Score and filter each product ─────────────────────────────────
        $results = [];

        foreach ($items as $item) {
            $name  = trim($item['title'] ?? '');
            if (empty($name)) continue;

            // Stock check
            if (isset($item['stock_status']) && $item['stock_status'] == 0) continue;

            // Price
            $price = (int) str_replace(',', '', $item['lowest_price'] ?? '0');
            if ($price <= 0) continue;

            // ── RELEVANCE SCORING ─────────────────────────────────────────
            // Count how many of the user's keywords appear in the product name
            $lowerName   = strtolower($name);
            $matchCount  = 0;
            $totalWeight = 0;

            foreach ($keywords as $kw) {
                $weight = strlen($kw); // longer words carry more weight (e.g. "samsung" > "s")
                $totalWeight += $weight;
                if (str_contains($lowerName, $kw)) {
                    $matchCount += $weight;
                }
            }

            // Calculate match percentage
            $matchPercent = $totalWeight > 0 ? ($matchCount / $totalWeight) * 100 : 0;

            // ── STRICT RULE: The FIRST keyword must always match ──────────
            // If user types "samsung galaxy s25", the word "samsung" MUST be in the title.
            // This blocks "Sportsman SM-720" matching "sam" in "samsung".
            $firstKeyword = $keywords[0] ?? '';
            if ($firstKeyword && !str_contains($lowerName, $firstKeyword)) {
                continue; // hard reject — first word missing entirely
            }

            // ── THRESHOLD: Require at least 40% keyword weight match ──────
            // For single-word queries (e.g. "iphone"), requires exact substring match.
            // For multi-word (e.g. "iphone 15 pro"), allows partial but meaningful match.
            if ($matchPercent < 40) {
                continue;
            }

            // ── CATEGORY MISMATCH: Block clearly unrelated categories ─────
            // If user searches for a phone brand, block non-device categories
            $phoneKeywords   = ['iphone','samsung','xiaomi','oppo','vivo','realme','nokia',
                                'infinix','tecno','oneplus','huawei','motorola','itel','honor'];
            $searchingPhone  = false;
            foreach ($phoneKeywords as $pk) {
                if (str_contains(strtolower($query), $pk)) {
                    $searchingPhone = true;
                    break;
                }
            }

            // If searching for a phone brand, skip accessories/unrelated categories
            // unless the product title also contains the brand name
            if ($searchingPhone) {
                $brandInTitle = false;
                foreach ($phoneKeywords as $pk) {
                    if (str_contains(strtolower($query), $pk) && str_contains($lowerName, $pk)) {
                        $brandInTitle = true;
                        break;
                    }
                }
                if (!$brandInTitle) continue;
            }

            // ── Build and collect the result ──────────────────────────────
            $link  = $item['prodcutUrl'] ?? 'https://priceoye.pk/' . ($item['slug'] ?? 'store');
            $image = $item['image'] ?? '';
            if (str_starts_with($image, '//')) $image = 'https:' . $image;
            if (empty($image)) $image = 'https://static.priceoye.pk/images/product-placeholder.gif';

            $retailPrice = (int) str_replace(',', '', $item['retail_price'] ?? '0');

            $results[$link] = [
                'name'         => ucwords(strtolower($name)),
                'price'        => $price,
                'retail_price' => ($retailPrice > $price) ? $retailPrice : null,
                'image'        => $image,
                'link'         => $link,
                'category'     => $item['category_name'] ?? '',
                'match'        => round($matchPercent), // useful for debugging, can remove later
            ];
        }

        // ── Nothing passed the filter ─────────────────────────────────────
        // This correctly handles searches like "perfume" — API returns
        // unrelated items, all get filtered out, we return unavailable.
        if (empty($results)) {
            return response()->json([
                'status'  => 'unavailable',
                'message' => "No results found for \"$query\" on PriceOye. This product may not be available.",
            ]);
        }

        $final = array_values($results);

        // ── Save to DB ─────────────────────────────────────────────────────
        foreach ($final as $item) {
            Product::updateOrCreate(
                ['product_link' => $item['link']],
                [
                    'product_name' => $item['name'],
                    'store_name'   => 'PriceOye',
                    'price'        => $item['price'],
                    'image_url'    => $item['image'],
                    'fetched_at'   => now(),
                ]
            );
        }

        // ── Sort lowest → highest price ────────────────────────────────────
        usort($final, fn($a, $b) => $a['price'] <=> $b['price']);

        return response()->json([
            'status'  => 'success',
            'count'   => count($final),
            'results' => $final,
        ]);
    }
}