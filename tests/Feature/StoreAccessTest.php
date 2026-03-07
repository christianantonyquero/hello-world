<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

use App\Store;
use App\User;
use App\StoreLineOpening;
use App\Services\StoreLineService;

class StoreAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = factory(Store::class)->create();
        $this->service = new StoreLineService();
    }

    public function test_user_cannot_access_store_before_access_is_granted()
    {
        $user = factory(User::class)->create();
        $this->service->joinLine($this->store, $user);

        $response = $this->actingAs($user)
             ->get('/store/'.$this->store->id);

        $response->assertRedirect('/store/'.$this->store->id.'/line');
    }

    public function test_user_can_can_access_store_once_line_opening_occurs()
    {
        $user = factory(User::class)->create();
        $this->service->joinLine($this->store, $user);
        factory(StoreLineOpening::class)->create([
            'store_id' => $this->store->id,
            'users' => 5,
            'time' => Carbon::now(),
        ]);

        $response = $this->actingAs($user)
             ->get('/store/'.$this->store->id);

        $response->assertStatus(200);
    }

    public function test_admin_user_always_has_access_to_store()
    {
        $user = factory(User::class)->state('admin')->create();

        $response = $this->actingAs($user)
             ->get('/store/'.$this->store->id);

        $response->assertStatus(200);
    }
}
