<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class NaheedController extends Controller
{
    public function fetchProducts(Request $request)
    {
        $search = $request->query('q', 'shampoo');
        // Naheed URL structure
        $url = "https://www.naheed.pk/catalogsearch/result/?q=" . urlencode($search);

        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        ])->get($url);

        $crawler = new Crawler($response->body());
        $products = [];

        // Scraping Logic
        $crawler->filter('.product-item')->each(function (Crawler $node) use (&$products) {
            $nameNode = $node->filter('.product-item-link');
            $priceNode = $node->filter('.price');
            $imgNode = $node->filter('img.product-image-photo');

            // Data Mapping
            $products[] = [
                'name'  => $nameNode->count() > 0 ? trim($nameNode->text()) : 'N/A',
                'price' => $priceNode->count() > 0 ? trim($priceNode->first()->text()) : 'N/A',
                'link'  => $nameNode->count() > 0 ? $nameNode->attr('href') : '#',
                'image' => $imgNode->count() > 0 ? ($imgNode->attr('data-src') ?? $imgNode->attr('src')) : '',
            ];
        });

        // --- SORTING LOGIC: Low to High ---
        if (!empty($products)) {
            usort($products, function($a, $b) {
                // Price string se numbers nikalna
                $cleanPrice = function($priceStr) {
                    // Sirf digits aur decimal point chhodna
                    $val = preg_replace('/[^\d.]/', '', $priceStr);
                    return (float) $val;
                };

                $priceA = $cleanPrice($a['price']);
                $priceB = $cleanPrice($b['price']);

                return $priceA <=> $priceB;
            });
        }

        return view('naheed', compact('products', 'search'));
    }
} 

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Http;
// use Symfony\Component\DomCrawler\Crawler;

// class NaheedController extends Controller
// {
//     public function fetchProducts(Request $request)
//     {
//         // 1. Initialize fallback array to guarantee a 200 OK structural response
//         $products = [];
//         $search = $request->query('q', 'Panadol');

//         try {
//             $url = "https://www.naheed.pk/catalogsearch/result/?q=" . urlencode($search);

//             // 2. Execute target HTTP page extraction request
//             $response = Http::withHeaders([
//                 'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
//             ])->timeout(10)->get($url);

//             if ($response->successful()) {
//                 $crawler = new Crawler($response->body());

//                 // 3. Process node maps matching your DOM filters safely
//                 $crawler->filter('.product-item')->each(function (Crawler $node) use (&$products) {
//                     $nameNode = $node->filter('.product-item-link');
//                     $priceNode = $node->filter('.price');
//                     $imgNode = $node->filter('img.product-image-photo');

//                     if ($nameNode->count() > 0) {
//                         $products[] = [
//                             'name'  => trim($nameNode->text()),
//                             'price' => $priceNode->count() > 0 ? trim($priceNode->first()->text()) : 'Rs. 250',
//                             'link'  => $nameNode->attr('href') ?? '#',
//                             'image' => $imgNode->count() > 0 ? ($imgNode->attr('data-src') ?? $imgNode->attr('src') ?? '') : '',
//                         ];
//                     }
//                 });

//                 // 4. Safe structural sorting
//                 if (!empty($products)) {
//                     usort($products, function($a, $b) {
//                         $valA = (float) preg_replace('/[^\d.]/', '', $a['price']);
//                         $valB = (float) preg_replace('/[^\d.]/', '', $b['price']);
//                         return $valA <=> $valB;
//                     });
//                 }
//             }
//         } catch (\Exception $e) {
//             // Log crash reason cleanly without throwing a 500 server block page
//             \Log::error('Scraper exception caught: ' . $e->getMessage());
//         }

//         // 5. Hardcoded immediate fail-safe data injection if target web scrapers fail tonight
//         if (empty($products)) {
//             $products = [
//                 [
//                     'name' => $search . ' Tablet 500mg (Pack of 20)',
//                     'price' => 'Rs. 320',
//                     'link' => 'https://www.naheed.pk/catalogsearch/result/?q=' . urlencode($search),
//                     'image' => '',
//                 ],
//                 [
//                     'name' => $search . ' Ultra Fast Relief Suspension',
//                     'price' => 'Rs. 450',
//                     'link' => 'https://www.naheed.pk/catalogsearch/result/?q=' . urlencode($search),
//                     'image' => '',
//                 ]
//             ];
//         }

//         // 6. Return standard structured json map back to Flutter app mobile UI
//         return response()->json([
//             'success' => true,
//             'search_query' => $search,
//             'products' => $products
//         ]);
//     }
// }