<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Weidner\Goutte\GoutteFacade as Goutte;

class ScrapService
{
    protected array $singleRecord = [];

    protected array $data = [];

    protected array $fileNames = [
        'new_site' => 'new_site.json',
        'old_site' => 'old_site.json',
    ];

    public function scrapNew()
    {
        $newUrl = 'https://www.iru.org/intelligence/fuel-prices';

        $crawler = Goutte::request('GET', $newUrl);

        $crawler->filter('.fuel-prices-index-table .item-list')
            ->each(function ($tr) {
                try {
                    $this->data[] = [
                        'country' => $tr->filter('.locked-title')->text(),
                        'price' => $tr->filter('.diesel-value')->text(),
                    ];
                } catch (\Exception $e) {
                    //
                }
            });

        Storage::disk('public')->put($this->fileNames['new_site'], json_encode($this->data));
    }

    public function scrapOld()
    {
        $oldUrl = 'https://oilpricez.com/pk/pakistan-gasoline-worldwide-comparison';

        $crawler = Goutte::request('GET', $oldUrl);

        $crawler->filter('div:nth-child(2) > table tr')
            ->each(function ($row) {
                $this->singleRecord = [];

                $row->filter('td')->each(fn ($cell) => $this->singleRecord[] = $cell->text());

                if (count($this->singleRecord) >= 3) {
                    $this->data[] = [
                        'country' => $this->singleRecord[1],
                        'price' => $this->singleRecord[2],
                    ];
                }
            });

        Storage::disk('public')->put($this->fileNames['old_site'], json_encode($this->data));
    }

    public function getSitesData(): array
    {
        $data['new_site'] = collect(File::json(Storage::disk('public')->path($this->fileNames['new_site'])));
        $data['old_site'] = collect(File::json(Storage::disk('public')->path($this->fileNames['old_site'])));

        return $data;
    }

    public function getSupportedCountries(array $data = []): array
    {
        if (empty($data)) {
            $data = $this->getSitesData();
        }

        return [
            'new_site' => $data['new_site']->pluck('country'),
            'old_site' => $data['old_site']->pluck('country'),
        ];
    }

    public function getCountryData(string $country, array $data = []): array
    {
        if (empty($data)) {
            $data = $this->getSitesData();
        }

        $data['new_site'] = $data['new_site']->where('country', $country)
            ->mapWithKeys(fn ($item) => [
                'country' => $item['country'],
                'price' => $item['price'],
            ]);

        $data['old_site'] = $data['old_site']->where('country', $country)
            ->mapWithKeys(fn ($item) => [
                'country' => $item['country'],
                'price' => $item['price'],
            ]);

        return $data;
    }
}
