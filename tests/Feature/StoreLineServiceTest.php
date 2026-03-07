<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

use App\User;
use App\Store;
use App\StoreLineOpening;
use App\StoreLinePosition;
use App\Services\StoreLineService;

class StoreLineServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new StoreLineService();
    }

    public function test_user_can_join_store_line()
    {
        $user = factory(User::class)->create();
        $store = factory(Store::class)->create();

        $response = $this->service->joinLine($store, $user);

        $this->assertInstanceOf(StoreLinePosition::class, $response);
        $this->assertDatabaseHas('store_line_positions', [
            'user_id' => $user->id,
            'store_id' => $store->id,
            'position' => 1, // new store
        ]);

        $this->assertEquals(1, $this->service->getUserCurrentPosition($store, $user));

        // add another user
        $user2 = factory(User::class)->create();

        $response2 = $this->service->joinLine($store, $user2);

        $this->assertInstanceOf(StoreLinePosition::class, $response2);
        $this->assertDatabaseHas('store_line_positions', [
            'user_id' => $user2->id,
            'store_id' => $store->id,
            'position' => 2, // 2nd in line
        ]);

        $this->assertEquals(2, $this->service->getUserCurrentPosition($store, $user2));
    }

    public function test_user_can_join_same_position_with_code()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $store = factory(Store::class)->create();

        $response = $this->service->joinLine($store, $user);
        $this->assertInstanceOf(StoreLinePosition::class, $response);

        $response2 = $this->service->joinLine($store, $user2, $response->share_code);
        $this->assertInstanceOf(StoreLinePosition::class, $response2);

        // should have joined at same position
        $this->assertEquals(
            $this->service->getUserCurrentPosition($store, $user),
            $this->service->getUserCurrentPosition($store, $user2)
        );

        $this->assertEquals($user->id, $response2->referred_by_id);
    }

    public function test_max_position_calculation()
    {
        $store = factory(Store::class)->create();

        $this->assertEquals(0, $this->service->getCurrentMaxPosition($store));

        $this->add_users($store, 2);
        $this->assertEquals(2, $this->service->getCurrentMaxPosition($store));

        $this->add_users($store, 3);
        $this->assertEquals(5, $this->service->getCurrentMaxPosition($store));
    }

    public function test_user_current_position()
    {
        $store = factory(Store::class)->create();
        $store2 = factory(Store::class)->create();

        $user = factory(User::class)->create();
        $this->service->joinLine($store, $user);
        $this->assertEquals(1, $this->service->getUserCurrentPosition($store, $user));

        $user2 = factory(User::class)->create();
        $this->service->joinLine($store, $user2);
        $this->assertEquals(2, $this->service->getUserCurrentPosition($store, $user2));

        // should not include data from other stores
        $this->add_users($store2, 4);
        $this->service->joinLine($store2, $user);
        $this->assertEquals(1, $this->service->getUserCurrentPosition($store, $user));

        // should be false if user has not joined line yet
        $this->assertFalse($this->service->getUserCurrentPosition($store2, $user2));
    }

    public function test_position_share_code()
    {
        $user = factory(User::class)->create();
        $store = factory(Store::class)->create();
        $store2 = factory(Store::class)->create();

        $response = $this->service->joinLine($store, $user);
        $this->assertGreaterThan(39, strlen($response->share_code));

        $response2 = $this->service->joinLine($store2, $user);
        $this->assertGreaterThan(39, strlen($response->share_code));

        $this->assertNotEquals($response->share_code, $response2->share_code); // should be unique
    }

    public function test_position_share_url()
    {
        $user = factory(User::class)->create();
        $store = factory(Store::class)->create();

        $response = $this->service->joinLine($store, $user);

        $this->assertStringContainsString(
            '/store/'.$store->id.'/line/join/'.$response->share_code,
            $this->service->getShareUrl($store, $user)
        );
    }

    public function test_store_user_access()
    {
        $user = factory(User::class)->create();
        $store = factory(Store::class)->create();
        $position = factory(StoreLinePosition::class)->create([
            'store_id' => $store->id,
            'user_id' => $user->id,
            'position' => 9,
        ]);

        factory(StoreLineOpening::class)->create([
            'store_id' => $store->id,
            'users' => 5,
            'time' => Carbon::now(),
        ]);

        // not yet
        $this->assertFalse($this->service->userHasAccess($store, $user));

        // add one in future
        factory(StoreLineOpening::class)->create([
            'store_id' => $store->id,
            'users' => 5,
            'time' => Carbon::now()->addDay(),
        ]);

        // still not yet
        $this->assertFalse($this->service->userHasAccess($store, $user));

        // add one now
        factory(StoreLineOpening::class)->create([
            'store_id' => $store->id,
            'users' => 5,
            'time' => Carbon::now(),
        ]);

        // should be in now
        $this->assertTrue($this->service->userHasAccess($store, $user));
    }

    public function test_store_line_user_groups()
    {
        $users = factory(User::class, 4)->create();
        $store = factory(Store::class)->create();
        $position = factory(StoreLinePosition::class)->create([
            'store_id' => $store->id,
            'user_id' => $users[0]->id,
            'position' => 2,
        ]);
        $position = factory(StoreLinePosition::class)->create([
            'store_id' => $store->id,
            'user_id' => $users[1]->id,
            'position' => 2,
        ]);
        $position = factory(StoreLinePosition::class)->create([
            'store_id' => $store->id,
            'user_id' => $users[2]->id,
            'position' => 2,
        ]);
        $position = factory(StoreLinePosition::class)->create([
            'store_id' => $store->id,
            'user_id' => $users[3]->id,
            'position' => 4,
        ]);

        $group = $this->service->getUsersInGroup($store, $users[0]);

        $this->assertTrue($group->contains($users[1]->fresh()));
        $this->assertTrue($group->contains($users[2]->fresh()));
        $this->assertFalse($group->contains($users[0]->fresh())); // shouldn't contain self
        $this->assertFalse($group->contains($users[3]->fresh())); // shouldn't contain user at another position
    }

    private function add_users($store, $count = 3)
    {
        $users = factory(User::class, $count)->create();

        foreach($users as $user){
            $this->service->joinLine($store, $user);
        }
    }
}
