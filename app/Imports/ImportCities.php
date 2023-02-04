<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class ImportCities implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $item) {
            DB::table('cities')->insert([
                [
                    'province_id' => $item[0],
                    'name' => $item[1],
                    'latitude' => $item[2],
                    'longitude' => $item[3]
                ]
            ]);
        }
    }
}
