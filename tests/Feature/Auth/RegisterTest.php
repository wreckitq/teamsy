<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\TestCase;
use Livewire\Livewire;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public $authRegister = 'auth.register';

    public $email = 'tallstack@example.com';

    /** @test */
    function registration_page_contains_livewire_component()
    {
        $this->get(route('register'))
            ->assertSuccessful()
            ->assertSeeLivewire($this->authRegister);
    }

    /** @test */
    public function is_redirected_if_already_logged_in()
    {
        $user = User::factory()->create();

        $this->be($user);

        $this->get(route('register'))
            ->assertRedirect(route('home'));
    }

    /** @test */
    function a_user_can_register()
    {
        Event::fake();

        Livewire::test($this->authRegister)
            ->set('name', 'Tall Stack')
            ->set('email', $this->email)
            ->set('password', 'password')
            ->set('passwordConfirmation', 'password')
            ->set('role', 'admin')
            ->call('register')
            ->assertRedirect(route('home'));

        $this->assertTrue(User::whereEmail($this->email)->exists());
        $this->assertEquals($this->email, Auth::user()->email);

        Event::assertDispatched(Registered::class);
    }

    /** @test */
    function name_is_required()
    {
        Livewire::test($this->authRegister)
            ->set('name', '')
            ->call('register')
            ->assertHasErrors(['name' => 'required']);
    }

    /** @test */
    function email_is_required()
    {
        Livewire::test($this->authRegister)
            ->set('email', '')
            ->call('register')
            ->assertHasErrors(['email' => 'required']);
    }

    /** @test */
    function email_is_valid_email()
    {
        Livewire::test($this->authRegister)
            ->set('email', 'tallstack')
            ->call('register')
            ->assertHasErrors(['email' => 'email']);
    }

    /** @test */
    function email_hasnt_been_taken_already()
    {
        User::factory()->create(['email' => $this->email]);

        Livewire::test($this->authRegister)
            ->set('email', $this->email)
            ->call('register')
            ->assertHasErrors(['email' => 'unique']);
    }

    /** @test */
    function see_email_hasnt_already_been_taken_validation_message_as_user_types()
    {
        User::factory()->create(['email' => $this->email]);

        Livewire::test($this->authRegister)
            ->set('email', 'smallstack@gmail.com')
            ->assertHasNoErrors()
            ->set('email', $this->email)
            ->call('register')
            ->assertHasErrors(['email' => 'unique']);
    }

    /** @test */
    function password_is_required()
    {
        Livewire::test($this->authRegister)
            ->set('password', '')
            ->set('passwordConfirmation', 'password')
            ->call('register')
            ->assertHasErrors(['password' => 'required']);
    }

    /** @test */
    function password_is_minimum_of_eight_characters()
    {
        Livewire::test($this->authRegister)
            ->set('password', 'secret')
            ->set('passwordConfirmation', 'secret')
            ->call('register')
            ->assertHasErrors(['password' => 'min']);
    }

    /** @test */
    function password_matches_password_confirmation()
    {
        Livewire::test($this->authRegister)
            ->set('email', $this->email)
            ->set('password', 'password')
            ->set('passwordConfirmation', 'not-password')
            ->call('register')
            ->assertHasErrors(['password' => 'same']);
    }
}
