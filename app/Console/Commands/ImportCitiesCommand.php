<?php

namespace App\Console\Commands;

use App\Imports\ImportCities;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ImportCitiesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import_cities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'import cities from excel file to database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        Excel::import(new ImportCities, storage_path('app\public\cities.xlsx'));

        return 0;
    }
}
