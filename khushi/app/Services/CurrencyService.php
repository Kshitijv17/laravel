<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class CurrencyService
{
    protected $baseCurrency;
    protected $currentCurrency;
    protected $rates;

    public function __construct()
    {
        $this->baseCurrency = config('currencies.base', 'USD');
        $this->currentCurrency = $this->getCurrentCurrency();
        $this->rates = $this->getExchangeRates();
    }

    public function getCurrentCurrency()
    {
        // 1. Check session
        if (Session::has('currency')) {
            return Session::get('currency');
        }
        
        // 2. Check user preference
        if (auth()->check() && auth()->user()->currency) {
            return auth()->user()->currency;
        }
        
        // 3. Auto-detect from IP/country
        if (config('currencies.auto_detect')) {
            $detected = $this->detectCurrencyFromLocation();
            if ($detected) {
                return $detected;
            }
        }
        
        // 4. Default currency
        return config('currencies.default', 'INR');
    }

    public function convert($amount, $from = null, $to = null)
    {
        $from = $from ?: $this->baseCurrency;
        $to = $to ?: $this->currentCurrency;
        
        if ($from === $to) {
            return $amount;
        }
        
        // Convert to base currency first
        if ($from !== $this->baseCurrency) {
            $amount = $amount / $this->getRate($from);
        }
        
        // Convert from base to target currency
        if ($to !== $this->baseCurrency) {
            $amount = $amount * $this->getRate($to);
        }
        
        return round($amount, $this->getDecimalPlaces($to));
    }

    public function format($amount, $currency = null)
    {
        $currency = $currency ?: $this->currentCurrency;
        $currencyData = config("currencies.supported.{$currency}");
        
        if (!$currencyData) {
            return $amount;
        }
        
        $formatted = number_format(
            $amount, 
            $currencyData['decimal_places'], 
            '.', 
            ','
        );
        
        return sprintf($currencyData['format'], $formatted);
    }

    public function getRate($currency)
    {
        return $this->rates[$currency] ?? 1;
    }

    public function getDecimalPlaces($currency)
    {
        return config("currencies.supported.{$currency}.decimal_places", 2);
    }

    public function getSupportedCurrencies()
    {
        return config('currencies.supported', []);
    }

    public function setCurrency($currency)
    {
        $supported = array_keys($this->getSupportedCurrencies());
        
        if (!in_array($currency, $supported)) {
            return false;
        }
        
        Session::put('currency', $currency);
        $this->currentCurrency = $currency;
        
        // Update user preference if logged in
        if (auth()->check()) {
            auth()->user()->update(['currency' => $currency]);
        }
        
        return true;
    }

    protected function getExchangeRates()
    {
        $cacheKey = 'exchange_rates';
        $cacheDuration = config('currencies.exchange_api.cache_duration', 3600);
        
        return Cache::remember($cacheKey, $cacheDuration, function () {
            if (config('currencies.exchange_api.enabled')) {
                return $this->fetchLiveRates();
            }
            
            return $this->getStaticRates();
        });
    }

    protected function fetchLiveRates()
    {
        try {
            $apiKey = config('currencies.exchange_api.api_key');
            $provider = config('currencies.exchange_api.provider');
            
            if (!$apiKey) {
                return $this->getStaticRates();
            }
            
            $response = match($provider) {
                'fixer' => Http::get("https://api.fixer.io/latest", [
                    'access_key' => $apiKey,
                    'base' => $this->baseCurrency
                ]),
                'exchangerate-api' => Http::get("https://api.exchangerate-api.com/v4/latest/{$this->baseCurrency}"),
                default => null
            };
            
            if ($response && $response->successful()) {
                $data = $response->json();
                return $data['rates'] ?? $this->getStaticRates();
            }
        } catch (\Exception $e) {
            \Log::error('Currency API error: ' . $e->getMessage());
        }
        
        return $this->getStaticRates();
    }

    protected function getStaticRates()
    {
        $currencies = config('currencies.supported', []);
        $rates = [];
        
        foreach ($currencies as $code => $data) {
            $rates[$code] = $data['rate'];
        }
        
        return $rates;
    }

    protected function detectCurrencyFromLocation()
    {
        try {
            // Simple IP-based detection (you can use more sophisticated services)
            $ip = request()->ip();
            
            // Skip local IPs
            if (in_array($ip, ['127.0.0.1', '::1']) || str_starts_with($ip, '192.168.')) {
                return null;
            }
            
            $response = Http::timeout(5)->get("http://ip-api.com/json/{$ip}");
            
            if ($response->successful()) {
                $data = $response->json();
                $countryCode = $data['countryCode'] ?? null;
                
                if ($countryCode) {
                    return config("currencies.country_currency.{$countryCode}");
                }
            }
        } catch (\Exception $e) {
            // Fail silently
        }
        
        return null;
    }
}
