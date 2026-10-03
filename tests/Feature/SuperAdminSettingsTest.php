<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\BusinessSetting;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SuperAdminSettingsTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure roles match the database seeders exactly
        Role::updateOrCreate(['id' => 1], ['name' => 'Admin', 'slug' => 'admin']);
        Role::updateOrCreate(['id' => 2], ['name' => 'Super Admin', 'slug' => 'superadmin']);
    }

    public function test_non_super_admin_cannot_access_settings(): void
    {
        $user = User::create([
            'name' => 'Regular Admin User',
            'email' => 'regular@example.com',
            'password' => bcrypt('password'),
            'branch_id' => 2,
            'role_id' => 1,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['branch_id' => 2])
            ->get(route('super-admin.settings'));

        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_settings(): void
    {
        $user = User::create([
            'name' => 'Super Admin User',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('password'),
            'branch_id' => 1,
            'role_id' => 2,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['branch_id' => 1])
            ->get(route('super-admin.settings'));

        $response->assertStatus(200);
        $response->assertSee('Feature permissions');
    }

    public function test_super_admin_can_update_env_features(): void
    {
        $user = User::create([
            'name' => 'Super Admin User',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('password'),
            'branch_id' => 1,
            'role_id' => 2,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['branch_id' => 1])
            ->post(route('super-admin.settings.env'), [
                'APP_SC' => 'yes',
                'APP_IMEI' => 'yes',
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
    }

    public function test_super_admin_can_update_sidebar_modules(): void
    {
        $user = User::create([
            'name' => 'Super Admin User',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('password'),
            'branch_id' => 1,
            'role_id' => 2,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['branch_id' => 1])
            ->post(route('super-admin.settings.sidebar'), [
                'hidden_modules' => ['pos', 'purchase'],
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('business_settings', [
            'type' => 'hidden_sidebar_modules',
            'value' => json_encode(['pos', 'purchase']),
        ]);
    }
}
