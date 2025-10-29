<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class MetricsController extends Controller
{
    public function monthlySales(): JsonResponse
    {
        return response()->json([
            'label' => 'Monthly Sales',
            'value' => 125000, // Example static value; replace with real query as needed
        ]);
    }

    public function openDeals(): JsonResponse
    {
        return response()->json([
            'label' => 'Open Deals',
            'value' => 42, // Example static value
        ]);
    }

    public function activeAccounts(): JsonResponse
    {
        return response()->json([
            'label' => 'Active Accounts',
            'value' => 318, // Example static value
        ]);
    }

    public function dailySales(): JsonResponse
    {
        $data = [];
        $sub30Days = now()->subDays(30);
        for ($pastDate = $sub30Days->clone(); $pastDate->lte(now()); $pastDate->addDay()) {
            $data[$pastDate->toDateString()] = rand(4000, 15000);
        }
        return response()->json([
            'label' => 'Daily Sales (last 30 days)',
            'values' => $data,
        ]);
    }
}
