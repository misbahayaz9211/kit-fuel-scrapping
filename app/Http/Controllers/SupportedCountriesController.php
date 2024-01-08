<?php

namespace App\Http\Controllers;

use App\Services\ScrapService;
use Illuminate\Http\Request;

class SupportedCountriesController extends Controller
{
    public function __invoke(Request $request, ScrapService $scrapService)
    {
        try {
            return $this->response(data: $scrapService->getSupportedCountries(), message: 'success');
        } catch (\Exception $e) {
            return $this->response(message: __CLASS__.' '.$e->getMessage(), status: 500);
        }
    }
}
