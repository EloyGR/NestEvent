<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    private int $userSequence = 0;

    private function createUser(array $overrides = []): User
    {
        // Helper local para crear usuarios de test sin usar factories.
        $this->userSequence++;

        $defaults = [
            'username' => 'security_user_' . $this->userSequence,
            'email' => 'security_user_' . $this->userSequence . '@example.com',
            'password_hash' => Hash::make('Password123!'),
            'first_name' => 'Security',
            'last_name' => 'Tester' . $this->userSequence,
            'phone' => null,
            'profile_picture' => null,
            'user_type' => 'event_manager',
            'is_active' => true,
        ];

        return User::query()->create(array_merge($defaults, $overrides));
    }

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('The pdo_sqlite extension is not available in this environment.');
        }

        parent::setUp();
    }

    public function test_debug_session_route_is_not_available_outside_local_environment(): void
    {
        // Endurecimiento P0: la ruta de debug no debe exponerse en testing/produccion.
        $response = $this->get('/debug-session');

        $response->assertNotFound();
    }

    public function test_login_regenerates_session_and_sets_legacy_session_keys(): void
    {
        // Verifica rotacion de sesion tras login y continuidad de claves legacy.
        $password = 'Secret1234!';

        $user = $this->createUser([
            'email' => 'security-login@example.com',
            'password_hash' => Hash::make($password),
            'user_type' => 'event_manager',
        ]);

        $this->get(route('login'));
        $initialSessionId = session()->getId();

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect(route('users.show', $user->user_id));
        $response->assertSessionHas('user_id', $user->user_id);
        $response->assertSessionHas('user_type', $user->user_type);
        $this->assertAuthenticatedAs($user);

        $rotatedSessionId = session()->getId();
        $this->assertNotSame($initialSessionId, $rotatedSessionId);
    }

    public function test_logout_invalidates_auth_and_legacy_session_state(): void
    {
        // Verifica cierre completo de contexto: auth, sesion custom y token CSRF.
        $user = $this->createUser([
            'user_type' => 'event_manager',
        ]);

        $this->actingAs($user);
        $this->withSession([
            'user_id' => $user->user_id,
            'user_type' => $user->user_type,
        ]);

        $oldToken = session()->token();

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
        $response->assertSessionMissing('user_id');
        $response->assertSessionMissing('user_type');
        $this->assertGuest();
        $this->assertNotSame($oldToken, session()->token());
    }
}
