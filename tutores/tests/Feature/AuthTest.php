<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class AuthTest extends RoleTestCase
{
    #[Test]
    public function guest_is_redirected_to_login(): void
    {
        $this->get(route('catalog.index'))->assertRedirect(route('login'));
    }

    #[Test]
    public function login_page_renders(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Continuar con Google')
            ->assertSee('catolica.edu.sv');
    }

    #[Test]
    public function google_callback_rejects_external_domains(): void
    {
        $this->mockGoogleUser('intruso@gmail.com', 'Intruso');

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('auth');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'intruso@gmail.com']);
    }

    #[Test]
    public function google_callback_creates_student_for_institutional_account(): void
    {
        $this->mockGoogleUser('alumno@catolica.edu.sv', 'Alumno Nuevo');

        $this->get(route('auth.google.callback'))->assertRedirect(route('catalog.index'));

        $this->assertAuthenticated();
        $user = User::where('email', 'alumno@catolica.edu.sv')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('estudiante'));
    }

    #[Test]
    public function google_callback_keeps_existing_role_when_linking_account(): void
    {
        $teacher = User::factory()->create(['email' => 'profe@catolica.edu.sv']);
        $teacher->assignRole('docente');

        $this->mockGoogleUser('profe@catolica.edu.sv', 'Profe');

        $this->get(route('auth.google.callback'));

        $this->assertTrue($teacher->fresh()->hasRole('docente'));
        $this->assertFalse($teacher->fresh()->hasRole('estudiante'));
    }

    #[Test]
    public function logout_invalidates_the_session(): void
    {
        $this->actingAs($this->student())
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    private function mockGoogleUser(string $email, string $name): void
    {
        $googleUser = Mockery::mock(\Laravel\Socialite\Two\User::class);
        $googleUser->shouldReceive('getEmail')->andReturn($email);
        $googleUser->shouldReceive('getId')->andReturn('google-'.md5($email));
        $googleUser->shouldReceive('getName')->andReturn($name);
        $googleUser->shouldReceive('getAvatar')->andReturn(null);

        Socialite::shouldReceive('driver')->with('google')->andReturnSelf();
        Socialite::shouldReceive('user')->andReturn($googleUser);
    }
}
