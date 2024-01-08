<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetCountryPriceRequest;
use App\Services\ScrapService;
use Illuminate\Http\JsonResponse;

class ScrappingController extends Controller
{
    public function __invoke(GetCountryPriceRequest $request, ScrapService $scrapService): JsonResponse
    {
        try {
            $data = $scrapService->getSitesData();

            if (! filled($request->country) || $request->country == 'all') {
                return $this->response(data: $data, message: 'success');
            }

            return $this->response(data: $scrapService->getCountryData($request->country), message: 'success');
        } catch (\Exception $e) {
            return $this->response(message: __CLASS__.' '.$e->getMessage(), status: 500);
        }
    }
}
