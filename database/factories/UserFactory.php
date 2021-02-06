<?php

namespace Database\Factories;

use App\Models\User;
use Domain\Tenant\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->userName,
            'email' => $this->faker->unique()->safeEmail,
            'role' => 'admin',
            'email_verified_at' => now(),
            'password' => bcrypt('secret'),
            'remember_token' => Str::random(10),
        ];
    }

    public function withTenant()
    {
        return $this->state(function () {
            return [
                'tenant_id' => Tenant::factory()->create(),
            ];
        });
    }

    public function memberTenant()
    {
        return $this->state(function () {
            $tenants = Tenant::all()->pluck('id')->toArray();

            return [
                'tenant_id' => $tenants[array_rand($tenants)],
                'role' => 'member',
            ];
        });
    }
}
