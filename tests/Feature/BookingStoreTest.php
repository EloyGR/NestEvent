<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BookingStoreTest extends TestCase
{
    use RefreshDatabase;

    private int $userSequence = 0;

    private function createUser(string $userType): User
    {
        // Genera usuarios de prueba sin depender de factories del proyecto.
        $this->userSequence++;

        return User::query()->create([
            'username' => 'testuser_' . $this->userSequence,
            'email' => 'testuser_' . $this->userSequence . '@example.com',
            'password_hash' => Hash::make('Password123!'),
            'first_name' => 'Nombre' . $this->userSequence,
            'last_name' => 'Apellido' . $this->userSequence,
            'phone' => null,
            'profile_picture' => null,
            'user_type' => $userType,
            'is_active' => true,
        ]);
    }

    private function createEvent(int $organizerId, string $start, string $end): Event
    {
        // Crea un evento valido para ejercer el flujo de reserva.
        return Event::query()->create([
            'name' => 'Evento de prueba ' . $organizerId,
            'description' => 'Evento para pruebas P0',
            'start_datetime' => $start,
            'end_datetime' => $end,
            'organizer_id' => $organizerId,
            'event_type' => 'concierto',
            'expected_attendance' => 120,
            'is_public' => true,
            'status' => 'approved',
        ]);
    }

    private function createVenue(int $managerId): Venue
    {
        // Crea un local operativo con datos minimos de negocio.
        return Venue::query()->create([
            'name' => 'Local de prueba ' . $managerId,
            'description' => 'Local para pruebas P0',
            'address' => 'Calle Falsa 123',
            'city' => 'Madrid',
            'state' => 'Centro',
            'zip_code' => '28001',
            'country' => 'Espana',
            'capacity' => 300,
            'price_per_hour' => 150,
            'manager_id' => $managerId,
            'is_active' => true,
        ]);
    }

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('The pdo_sqlite extension is not available in this environment.');
        }

        parent::setUp();
    }

    public function test_event_manager_can_create_booking_for_own_event(): void
    {
        // Caso feliz: el organizador reserva usando su propio evento.
        $organizer = $this->createUser('event_manager');
        $venueManager = $this->createUser('local_manager');

        $event = $this->createEvent(
            (int) $organizer->user_id,
            '2026-04-10 10:00:00',
            '2026-04-10 12:00:00'
        );

        $venue = $this->createVenue((int) $venueManager->user_id);

        $startDate = Carbon::parse('2030-06-10 10:00:00');
        $endDate = Carbon::parse('2030-06-10 12:00:00');

        DB::table('venue_availability')->insert([
            'venue_id' => $venue->venue_id,
            'day_of_week' => $startDate->dayOfWeek,
            'opening_time' => '08:00:00',
            'closing_time' => '23:00:00',
            'is_available' => true,
        ]);

        $response = $this->actingAs($organizer)->post(route('bookings.store'), [
            'event_id' => $event->event_id,
            'venue_id' => $venue->venue_id,
            'start_date' => $startDate->toDateString(),
            'start_time' => $startDate->format('H:i'),
            'end_date' => $endDate->toDateString(),
            'end_time' => $endDate->format('H:i'),
            'notes' => 'prueba de reserva',
        ]);

        $response->assertRedirect(route('bookings.index'));

        $this->assertDatabaseHas('bookings', [
            'event_id' => $event->event_id,
            'venue_id' => $venue->venue_id,
            'booking_status' => 'pending',
            'notes' => 'prueba de reserva',
        ]);

        $this->assertDatabaseHas('notifications', [
            'notification_type' => 'booking_created',
            'related_entity_type' => 'booking',
        ]);
    }

    public function test_event_manager_cannot_create_booking_for_foreign_event(): void
    {
        // Caso de seguridad P0: debe bloquearse la reserva sobre evento ajeno.
        $attacker = $this->createUser('event_manager');
        $realOwner = $this->createUser('event_manager');
        $venueManager = $this->createUser('local_manager');

        $foreignEvent = $this->createEvent(
            (int) $realOwner->user_id,
            '2030-06-10 10:00:00',
            '2030-06-10 12:00:00'
        );

        $venue = $this->createVenue((int) $venueManager->user_id);

        DB::table('venue_availability')->insert([
            'venue_id' => $venue->venue_id,
            'day_of_week' => 1,
            'opening_time' => '08:00:00',
            'closing_time' => '23:00:00',
            'is_available' => true,
        ]);

        $response = $this->actingAs($attacker)->from(route('bookings.create'))->post(route('bookings.store'), [
            'event_id' => $foreignEvent->event_id,
            'venue_id' => $venue->venue_id,
            'start_date' => '2030-06-10',
            'start_time' => '10:00',
            'end_date' => '2030-06-10',
            'end_time' => '12:00',
            'notes' => 'intento no autorizado',
        ]);

        $response->assertRedirect(route('bookings.create'));
        $response->assertSessionHasErrors(['event_id']);

        $this->assertDatabaseMissing('bookings', [
            'event_id' => $foreignEvent->event_id,
            'venue_id' => $venue->venue_id,
            'notes' => 'intento no autorizado',
        ]);
    }
}
