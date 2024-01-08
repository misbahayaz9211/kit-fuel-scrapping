<?php

namespace App\Console\Commands;

use App\Services\ScrapService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ScrapCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrap:prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command scraps the prices of fuel from the web and stores it in the file.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ScrapService $scrapService)
    {
        try {
            $scrapService->scrapNew();
            $scrapService->scrapOld();

            return Command::SUCCESS;
        } catch (\Exception $e) {
            Log::info(__CLASS__.' '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
