<?php

namespace App\Http\Controllers;

use App\Services\ScrapFuelService;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\Electricity;
use App\Models\Fuel;
use App\Models\Gas;
use Illuminate\Support\Str;

class ScrapController extends Controller
{
    public function getPrices(Request $request, ScrapFuelService $scrapingService)
    {
        // $country = $request->query('country');
        $country = ucwords($request->query('country'));
        $country = str_replace(' ', '-', $country);
        if($country === "Usa" || $country === "Uk"){
            $country = Str::upper($country);
        }

        if (!$country) {
            return response()->json(['error' => 'Country name is required as a query parameter.'], 400);
        }

        $result = $scrapingService->scrapeAndSave($country);

        return response()->json($result);
    }
    public function historyData(Request $request){
        $countryName = ucwords($request->country);
        $countryName = str_replace(' ', '-', $countryName);
        if($countryName === "Usa" || $countryName === "Uk"){
            $countryName = Str::upper($countryName);
        }
        if (!$countryName) {
            return response()->json(['error' => 'Country name is required as a query parameter.'], 400);
        }
        $getCountry = Country::where('name',$countryName)->get();
            $data = array();
            foreach($getCountry as $country){
                $countryId = $country->id;
                $getFuel = Fuel::where('country_id',$countryId)->get();
                $country['fuel prices'] = $getFuel;
                $getElectricity = Electricity::where('country_id',$countryId)->get();
                $country['electricity prices'] = $getElectricity;
                $getGas = Gas::where('country_id',$countryId)->get();
                $country['natural gas prices'] = $getGas;
            }

        if($getCountry){
            return response()->json(['Country' => $getCountry], 200);
        }
        else{
            return response()->json(['error' => 'No Data Found.'], 400);
        }
        
    }
}
