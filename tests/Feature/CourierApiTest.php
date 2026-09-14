<?php

namespace Tests\Feature;

use App\Models\Courier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourierApiTest extends TestCase
{
    use RefreshDatabase;

    private const SAMPLE_COURIERS = [
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

    private function seedAll(): void
    {
        foreach (self::SAMPLE_COURIERS as $person) {
            Courier::create($person);
        }
    }

    public function test_index_paginated_and_sorted_by_name_by_default(): void
    {
        $this->seedAll();

        $res = $this->getJson('/api/couriers?per_page=2');

        $res->assertOk()->assertJsonStructure(['current_page', 'data', 'total']);
        $this->assertSame(['Agus Santoso', 'Budiono Hadi Agung'], $res->json('data.*.name'));
        $this->assertSame(5, $res->json('total'));
    }

    public function test_index_sorted_by_name_return_all_five_couriers(): void
    {
        $this->seedAll();

        $res = $this->getJson('/api/couriers?per_page=20');

        $res->assertOk();
        $this->assertSame(5, $res->json('total'));
        $this->assertEqualsCanonicalizing(
            array_column(self::SAMPLE_COURIERS, 'name'),
            $res->json('data.*.name'),
        );
    }

    public function test_index_can_sort_by_registered_at(): void
    {
        Courier::create(self::SAMPLE_COURIERS[1]);
        Courier::create(self::SAMPLE_COURIERS[4]);

        $res = $this->getJson('/api/couriers?sort=registered_at&direction=desc');

        $res->assertOk();
        $this->assertSame(['Rina Marlina', 'Yoga Pratama'], $res->json('data.*.name'));
    }

    public function test_index_search_matches_every_word_on_budiono(): void
    {
        $this->seedAll();

        $res = $this->getJson('/api/couriers?search=budi+agung');

        $res->assertOk();
        $this->assertSame(['Budiono Hadi Agung'], $res->json('data.*.name'));
    }

    public function test_index_filters_by_levels(): void
    {
        $this->seedAll();

        $res = $this->getJson('/api/couriers?level=2,3');

        $res->assertOk();
        $this->assertEqualsCanonicalizing([2, 3], $res->json('data.*.level'));
        $this->assertEqualsCanonicalizing(['Rina Marlina', 'Agus Santoso'], $res->json('data.*.name'));
    }

    public function test_show_returns_budiono_hadi_agung(): void
    {
        $budiono = Courier::create(self::SAMPLE_COURIERS[0]);

        $this->getJson("/api/couriers/{$budiono->id}")
            ->assertOk()
            ->assertJsonPath('name', 'Budiono Hadi Agung')
            ->assertJsonPath('email', 'budiono.hadiagung@gmail.com')
            ->assertJsonPath('level', 5);
    }

    public function test_store_validates_and_persists(): void
    {
        $res = $this->postJson('/api/couriers', self::SAMPLE_COURIERS[0]);

        $res->assertCreated()->assertJsonPath('name', 'Budiono Hadi Agung');
        $this->assertDatabaseHas('couriers', ['phone' => '081298745321', 'level' => 5]);
    }

    public function test_store_rejects_invalid_input(): void
    {
        $payload = array_replace(self::SAMPLE_COURIERS[0], [
            'level' => 6,
            'name' => '',
        ]);

        $this->postJson('/api/couriers', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'level']);

        $this->assertDatabaseCount('couriers', 0);
    }

    public function test_update_validates_and_persists_within_five(): void
    {
        $courier = Courier::create(self::SAMPLE_COURIERS[0]);

        $res = $this->putJson("/api/couriers/{$courier->id}", self::SAMPLE_COURIERS[3]);

        $res->assertOk()->assertJsonPath('name', 'Dewi Lestari');
        $this->assertDatabaseHas('couriers', ['id' => $courier->id, 'name' => 'Dewi Lestari', 'level' => 1]);
    }

    public function test_update_rejects_duplicate_phone(): void
    {
        $budiono = Courier::create(self::SAMPLE_COURIERS[0]);
        $dewi = Courier::create(self::SAMPLE_COURIERS[3]);

        $this->putJson("/api/couriers/{$dewi->id}", ['phone' => $budiono->phone])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['phone']);
    }

    public function test_destroy_removes_record(): void
    {
        $courier = Courier::create(self::SAMPLE_COURIERS[4]);

        $this->deleteJson("/api/couriers/{$courier->id}")->assertNoContent();

        $this->assertDatabaseMissing('couriers', ['id' => $courier->id]);
    }
}
