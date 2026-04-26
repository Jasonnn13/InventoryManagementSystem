<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LevelManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_approve_request_to_admin(): void
    {
        $admin = User::factory()->create(['level' => User::LEVEL_ADMIN]);
        $request = User::factory()->create(['level' => User::LEVEL_REQUEST]);

        $response = $this->actingAs($admin)->post(route('level.approve', $request->id));

        $response->assertRedirect(route('level.index', absolute: false));
        $this->assertDatabaseHas('users', [
            'id' => $request->id,
            'level' => User::LEVEL_ADMIN,
        ]);
    }

    public function test_admin_cannot_delete_other_admins(): void
    {
        $admin = User::factory()->create(['level' => User::LEVEL_ADMIN]);
        $otherAdmin = User::factory()->create(['level' => User::LEVEL_ADMIN]);

        $response = $this->actingAs($admin)->delete(route('level.destroy', $otherAdmin->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('users', ['id' => $otherAdmin->id]);
    }

    public function test_owner_can_delete_request_and_admin_users(): void
    {
        $owner = User::factory()->create(['level' => User::LEVEL_OWNER]);
        $admin = User::factory()->create(['level' => User::LEVEL_ADMIN]);

        $response = $this->actingAs($owner)->delete(route('level.destroy', $admin->id));

        $response->assertRedirect(route('level.index', absolute: false));
        $this->assertDatabaseMissing('users', ['id' => $admin->id]);
    }

    public function test_owner_cannot_delete_other_owners(): void
    {
        $owner = User::factory()->create(['level' => User::LEVEL_OWNER]);
        $otherOwner = User::factory()->create(['level' => User::LEVEL_OWNER]);

        $response = $this->actingAs($owner)->delete(route('level.destroy', $otherOwner->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('users', ['id' => $otherOwner->id]);
    }
}