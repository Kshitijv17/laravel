<?php

namespace App\Http\Controllers\Web;

use App\Services\CurrencyService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CurrencyController extends Controller
{
    protected $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function switch(Request $request)
    {
        $currency = $request->input('currency');
        
        if ($this->currencyService->setCurrency($currency)) {
            return redirect()->back()->with('success', 'Currency changed successfully');
        }
        
        return redirect()->back()->with('error', 'Unsupported currency');
    }

    public function getAvailable()
    {
        return response()->json([
            'current' => $this->currencyService->getCurrentCurrency(),
            'supported' => $this->currencyService->getSupportedCurrencies()
        ]);
    }

    public function convert(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'from' => 'required|string|size:3',
            'to' => 'required|string|size:3'
        ]);

        $converted = $this->currencyService->convert(
            $validated['amount'],
            $validated['from'],
            $validated['to']
        );

        return response()->json([
            'original' => $validated['amount'],
            'converted' => $converted,
            'from' => $validated['from'],
            'to' => $validated['to'],
            'formatted' => $this->currencyService->format($converted, $validated['to'])
        ]);
    }
}
