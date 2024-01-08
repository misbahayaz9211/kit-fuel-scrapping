<?php

namespace App\Services;

use Goutte\Client;
use App\Models\Country;
use App\Models\Electricity;
use App\Models\Fuel;
use App\Models\Gas;
use Illuminate\Support\Facades\DB;

use Weidner\Goutte\GoutteFacade as Goutte;
class ScrapFuelService
{
    public function scrapeAndSave($country)
    {
        // $client = new Client();
        $crawler = Goutte::request('GET', "https://www.globalpetrolprices.com/$country/");

        $data = $this->getData($crawler, 'Fuels, price per liter');
        $electricityPrices = $this->getData($crawler, 'Electricity prices per kWh');
        $naturalGasPrices = $this->getData($crawler, 'Natural gas prices per kWh');
        
        $getCountry = Country::where('name',$country)->first();
      
        if($getCountry != []){
            $oldCountryId = $getCountry->id;
            $duplicateFuel = DB::table('fuel')->select('country_id', 'date', 'gasoline', 'methane', 'diesel', 'kerosene','LPG','heating_oil','ethanol', DB::raw('COUNT(*) as c'))->groupBy('country_id','date','gasoline', 'methane', 'diesel', 'kerosene','LPG','heating_oil','ethanol')->having('c', '>=', 1)->first();
            if($duplicateFuel->c < 1){
                if($data != []){
                    $add_fuel = new Fuel;
                    $add_fuel->country_id = $oldCountryId;
                    $add_fuel->gasoline = $data['Gasoline prices']['USD'];
                    $add_fuel->diesel = $data['Diesel prices']['USD'];
                    if (isset($data['Methane prices']['USD'])) {
                        $add_fuel->methane = $data['Methane prices']['USD'];
                    }
                    if (isset($data['Kerosene prices']['USD'])) {
                        $add_fuel->kerosene = $data['Kerosene prices']['USD'];
                    }
                    if (isset($data['LPG prices']['USD'])) {
                        $add_fuel->LPG = $data['LPG prices']['USD'];
                    }
                    if (isset($data['Heating Oil prices']['USD'])) {
                        $add_fuel->heating_oil = $data['Heating Oil prices']['USD'];
                    }
                    if (isset($data['Ethanol prices']['USD'])) {
                        $add_fuel->ethanol = $data['Ethanol prices']['USD'];
                    }
                    $add_fuel->date = $data['Gasoline prices']['Date'];
                    $add_fuel->save();
                }
            }
            $duplicateElectricity = DB::table('electricity')->select('country_id', 'date', 'household', 'business', DB::raw('COUNT(*) as c'))->groupBy('country_id','date', 'household', 'business')->having('c', '>=', 1)->first();
            if($duplicateElectricity->c < 1){

                if($electricityPrices != []){
                    $add_electricity = new Electricity;
                    $add_electricity->country_id = $oldCountryId;
                    $add_electricity->household = $electricityPrices['Households']['USD'];
                    $add_electricity->business = $electricityPrices['Business']['USD'];
                    $add_electricity->date = $electricityPrices['Households']['Date'];
                    $add_electricity->save();
                }
            }

            $duplicateGas = DB::table('gas')->select('country_id', 'date', 'household', 'business', DB::raw('COUNT(*) as c'))->groupBy('country_id','date', 'household', 'business')->having('c', '>=', 1)->first();
            if($duplicateGas->c < 1){
                
                if($naturalGasPrices != []){
                    $add_gas = new Gas;
                    $add_gas->country_id = $oldCountryId;
                    $add_gas->household = $naturalGasPrices['Households']['USD'];
                    $add_gas->business = $naturalGasPrices['Business']['USD'];
                    $add_gas->date = $naturalGasPrices['Households']['Date'];
                    $add_gas->save();
                }
            }
            
           
        }
        else{
            $add_country = new Country;
            $add_country->name = $country;
            $add_country->save();
            $lastCountryId = $add_country->id;

            if($data != [] ){
                $add_fuel = new Fuel;
                $add_fuel->country_id = $lastCountryId;
                $add_fuel->gasoline = $data['Gasoline prices']['USD'];
                $add_fuel->diesel = $data['Diesel prices']['USD'];
                if (isset($data['Methane prices']['USD'])) {
                    $add_fuel->methane = $data['Methane prices']['USD'];
                }
                if (isset($data['Kerosene prices']['USD'])) {
                    $add_fuel->kerosene = $data['Kerosene prices']['USD'];
                }
                if (isset($data['LPG prices']['USD'])) {
                    $add_fuel->LPG = $data['LPG prices']['USD'];
                }
                if (isset($data['Heating Oil prices']['USD'])) {
                    $add_fuel->heating_oil = $data['Heating Oil prices']['USD'];
                }
                if (isset($data['Ethanol prices']['USD'])) {
                    $add_fuel->ethanol = $data['Ethanol prices']['USD'];
                }
                $add_fuel->date = $data['Gasoline prices']['Date'];
                $add_fuel->save();
            }
            // Save electricity prices
            if($electricityPrices != []){
                $add_electricity = new Electricity;
                $add_electricity->country_id = $lastCountryId;
                $add_electricity->household = $electricityPrices['Households']['USD'];
                $add_electricity->business = $electricityPrices['Business']['USD'];
                $add_electricity->date = $electricityPrices['Households']['Date'];
                $add_electricity->save();
            }
            
            if($naturalGasPrices != []){
                $add_gas = new Gas;
                $add_gas->country_id = $lastCountryId;
                $add_gas->household = $naturalGasPrices['Households']['USD'];
                $add_gas->business = $naturalGasPrices['Business']['USD'];
                $add_gas->date = $naturalGasPrices['Households']['Date'];
                $add_gas->save();
            }
           
        }

        // $result = [
        //     'country' => $country,
        //     'data' => $data,
        //     'electricityPrices' => $electricityPrices,
        //     'naturalGasPrices' => $naturalGasPrices,
        // ];
        $result = [
    'country' => $country,
    'data' => [
        'Diesel prices' => isset($data['Diesel prices']) ? $data['Diesel prices'] : [
            'Date' => '-',
            'Currency' => '-',
            'USD' => '-'
        ],
        'Gasoline prices' => isset($data['Gasoline prices']) ? $data['Gasoline prices'] : [
            'Date' => '-',
            'Currency' => '-',
            'USD' => '-'
        ],
        'Methane prices' => isset($data['Methane prices']) ? $data['Methane prices'] : [
            'Date' => '-',
            'Currency' => '-',
            'USD' => '-'
        ],
        'Ethanol prices' => isset($data['Ethanol prices']) ? $data['Ethanol prices'] : [
            'Date' => '-',
            'Currency' => '-',
            'USD' => '-'
        ],
        'Kerosene prices' => isset($data['Kerosene prices']) ? $data['Kerosene prices'] : [
            'Date' => '-',
            'Currency' => '-',
            'USD' => '-'
        ],
        'Heating Oil prices' => isset($data['Heating Oil prices']) ? $data['Heating Oil prices'] : [
            'Date' => '-',
            'Currency' => '-',
            'USD' => '-'
        ],
        'LPG prices' => isset($data['LPG prices']) ? $data['LPG prices'] : [
            'Date' => '-',
            'Currency' => '-',
            'USD' => '-'
        ],
    ],
    'electricityPrices' =>[ 
        'Households' => isset($electricityPrices['Households']) ? $electricityPrices['Households'] :[
            'Date' => '-',
            'Currency' => '-',
            'USD' => '-'
        ],
        'Business' => isset($electricityPrices['Business']) ? $electricityPrices['Business'] :[
            'Date' => '-',
            'Currency' => '-',
            'USD' => '-'
        ],
    ],
    'naturalGasPrices' => [ 
        'Households' => isset($naturalGasPrices['Households']) ? $naturalGasPrices['Households'] :[
            'Date' => '-',
            'Currency' => '-',
            'USD' => '-'
        ],
        'Business' => isset($naturalGasPrices['Business']) ? $naturalGasPrices['Business'] :[
            'Date' => '-',
            'Currency' => '-',
            'USD' => '-'
        ],
    ]
];



        $filename = strtolower(str_replace(' ', '_', $country)) . '_data.json';
        $filePath = public_path('data/' . $filename);

        file_put_contents($filePath, json_encode($result, JSON_PRETTY_PRINT));

        return $result;
    }

    private function getData($crawler, $tableTitle)
    {
        $data = [];

        $crawler->filter('td.tableTitleBar')->each(function ($tableTitleBar) use (&$data, $tableTitle) {
            if (trim($tableTitleBar->text()) === $tableTitle) {
                $tableRows = $tableTitleBar->closest('table')->filter('tbody tr');

                $tableRows->each(function ($row) use (&$data) {
                    $titleLink = $row->filter('th a');
                    if ($titleLink->count() > 0) {
                        $title = trim($titleLink->text());
                        $date = trim($row->filter('td.value:nth-child(2)')->text());
                        $currency = trim($row->filter('td.value:nth-child(3)')->text());
                        $usd = trim($row->filter('td.value:nth-child(4)')->text());

                        $data[$title] = [
                            'Date' => $date,
                            'Currency' => $currency,
                            'USD' => $usd,
                        ];
                    }
                });
            }
        });

        return $data;
    }
}
