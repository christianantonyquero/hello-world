<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\User;
use App\Store;
use App\Services\StoreLineService;

class StoreLineControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = factory(Store::class)->create();
        $this->service = new StoreLineService();
    }

    public function test_line_status_page()
    {
        $user = factory(User::class)->create();

        // user not in line
        $response = $this->actingAs($user)
                         ->get('/store/'.$this->store->id.'/line');
        $response->assertStatus(200);
        $response->assertSee('Get In Line');

        // user already in line
        $this->service->joinLine($this->store, $user);
        $response = $this->actingAs($user)
                         ->get('/store/'.$this->store->id.'/line');
        $response->assertStatus(200);
        $response->assertSee('You\'re at spot 1 in line', false);
        $response->assertSee($this->service->getShareUrl($this->store, $user));
        $response->assertDontSee('You\'re in line with', false);
    }

    public function test_line_status_page_shows_other_users()
    {
        $users = factory(User::class, 3)->create();
        $this->service->joinLine($this->store, $users[0]);
        $this->service->joinLine($this->store, $users[1], $this->service->getShareCode($this->store, $users[0]));
        $this->service->joinLine($this->store, $users[2],  $this->service->getShareCode($this->store, $users[0]));

        $response = $this->actingAs($users[0])
                         ->get('/store/'.$this->store->id.'/line');

        $response->assertSee('You\'re in line with', false);
        $response->assertSee($users[1]->display_name);
        $response->assertSee($users[2]->display_name);
    }

    public function test_join_line_page()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
                         ->get('/store/'.$this->store->id.'/line/join');
        $response->assertStatus(302);
        $this->assertEquals(1, $this->service->getUserCurrentPosition($this->store, $user));
    }

    public function test_join_line_page_with_code()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $storeLinePosition = $this->service->joinLine($this->store, $user);

        $response = $this->actingAs($user2)
                         ->get('/store/'.$this->store->id.'/line/join/'.$storeLinePosition->share_code);
        $response->assertStatus(302);
        $this->assertEquals($storeLinePosition->position, $this->service->getUserCurrentPosition($this->store, $user2));
    }
}
