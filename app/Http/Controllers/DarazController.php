<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class DarazController extends Controller {
    public function index() { return view('daraz'); }

    public function debug(Request $request) {
        $query = $request->input('query', 'frock');
        $scriptPath = base_path("scraper/daraz_scraper.mjs");

        $output = shell_exec("node " . escapeshellarg($scriptPath) . " " . escapeshellarg($query));
        $data = json_decode($output, true) ?? [];

        if (!empty($data)) {
            usort($data, function($a, $b) {
                // Price ko clean karke numeric compare karna
                $pA = (float) preg_replace('/[^\d.]/', '', $a['priceShow']);
                $pB = (float) preg_replace('/[^\d.]/', '', $b['priceShow']);
                return $pA <=> $pB;
            });
        }
        return response()->json($data);
    }
}