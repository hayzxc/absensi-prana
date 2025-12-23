<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisions = [
            'Teknis',
            'Keuangan',
            'Sertifikasi',
            'CS/Admin',
            'Verifikasi',
        ];

        foreach ($divisions as $division) {
            Division::firstOrCreate(['name' => $division]);
        }
    }
}
