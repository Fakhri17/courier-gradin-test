<?php

namespace Database\Seeders;

use App\Models\Courier;
use Illuminate\Database\Seeder;

class CourierSeeder extends Seeder
{
    public function run(): void
    {
        $couriers = [
            [
                'name' => 'Budiono Hadi Agung',
                'phone' => '081298745321',
                'email' => 'budiono.hadiagung@gmail.com',
                'level' => 5,
                'vehicle_type' => 'motor',
                'vehicle_plate_number' => 'B 4821 XYZ',
                'address' => 'Jl. Jend. Sudirman No. 12, Jakarta',
                'status' => 'active',
                'registered_at' => '2024-05-12',
            ],
            [
                'name' => 'Rina Marlina',
                'phone' => '081326548710',
                'email' => 'rina.marlina@gmail.com',
                'level' => 2,
                'vehicle_type' => 'motor',
                'vehicle_plate_number' => 'D 3102 KAL',
                'address' => 'Jl. Melati No. 7, Bandung',
                'status' => 'active',
                'registered_at' => '2025-08-03',
            ],
            [
                'name' => 'Agus Santoso',
                'phone' => '082145987302',
                'email' => 'agus.santoso77@yahoo.co.id',
                'level' => 3,
                'vehicle_type' => 'mobil',
                'vehicle_plate_number' => 'B 9821 ANB',
                'address' => 'Jl. Pahlawan No. 45, Surabaya',
                'status' => 'active',
                'registered_at' => '2024-11-19',
            ],
            [
                'name' => 'Dewi Lestari',
                'phone' => '085691238745',
                'email' => 'dewi.lestari88@gmail.com',
                'level' => 1,
                'vehicle_type' => 'motor',
                'vehicle_plate_number' => 'L 7710 JKN',
                'address' => 'Jl. Merdeka No. 21, Semarang',
                'status' => 'active',
                'registered_at' => '2025-03-27',
            ],
            [
                'name' => 'Yoga Pratama',
                'phone' => '089612345870',
                'email' => 'yoga.pratama21@gmail.com',
                'level' => 4,
                'vehicle_type' => 'pickup',
                'vehicle_plate_number' => 'AD 5512 RQT',
                'address' => 'Jl. Gatot Subroto No. 9, Medan',
                'status' => 'active',
                'registered_at' => '2024-06-08',
            ],
        ];

        foreach ($couriers as $courier) {
            Courier::firstOrCreate(['phone' => $courier['phone']], $courier);
        }
    }
}
