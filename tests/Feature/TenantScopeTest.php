<?php

namespace Tests\Feature;

use App\Models\User;
use Domain\Tenant\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class TenantScopeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function a_model_has_a_tenant_id_on_the_migration()
    {
        $now = now();

        $this->artisan('make:model Test -m');

        $filename = $now->year . '_' . $now->format('m') . '_' . $now->format('d') . '_' . $now->format('H')
            . $now->format('i') . $now->format('s') . '_create_tests_table.php';
        $pathMigration = database_path('migrations/' . $filename);

        $this->assertTrue(File::exists($pathMigration));
        $this->assertStringContainsString(
            '$table->unsignedBigInteger(\'tenant_id\')',
            File::get($pathMigration)
        );

        File::delete($pathMigration);
        File::delete(app_path('Models/Test.php'));
    }

    /** @test */
    public function a_user_can_only_see_users_in_the_same_tenant()
    {
        $user = User::factory()->withTenant()->create();

        User::factory()->count(9)->create([
            'tenant_id' => $user->tenant_id,
        ]);

        User::factory()->count(10)->withTenant()->create();

        auth()->login($user);

        $this->assertEquals(10, User::count());
    }

    /** @test */
    public function a_user_can_only_create_a_user_in_his_tenant()
    {
        $tenant1 = Tenant::factory()->create();

        $user1 = User::factory()->create([
            'tenant_id' => $tenant1,
        ]);

        auth()->login($user1);

        $createdUser = User::factory()->create();

        $this->assertTrue($createdUser->tenant_id === $user1->tenant_id);
    }

    /** @test */
    public function a_user_can_only_create_a_user_in_his_tenant_even_if_other_tenant_is_provided()
    {
        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create();

        $user1 = User::factory()->create([
            'tenant_id' => $tenant1,
        ]);

        auth()->login($user1);

        $createdUser = User::factory()->make();
        $createdUser->tenant_id = $tenant2->id;
        $createdUser->save();

        $this->assertTrue($createdUser->tenant_id === $user1->tenant_id);
    }
}
