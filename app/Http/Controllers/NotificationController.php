<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;

class NotificationController extends Controller
{
    /**
     * Obtiene el usuario autenticado por guard o por sesion legacy.
     */
    private function resolveAuthenticatedUser(): ?User
    {
        // Resuelve compatibilidad: primero guard de Laravel y luego sesion legacy.
        $user = auth()->user();

        if (! $user && session()->has('user_id')) {
            $user = User::find(session('user_id'));
        }

        return $user;
    }

    /**
     * Muestra las notificaciones del usuario autenticado.
     */
    public function index()
    {
        $user = $this->resolveAuthenticatedUser();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para ver tus notificaciones.');
        }

        // Ordena en descendente para priorizar notificaciones recientes.
        $notifications = DB::table('notifications')
            ->where('user_id', $user->user_id)
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Muestra una notificacion concreta y la marca como leida.
     */
    public function show(Request $request, int $id)
    {
        $user = $this->resolveAuthenticatedUser();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para ver tus notificaciones.');
        }

        // Carga defensiva: solo permite abrir notificaciones propias.
        $notification = DB::table('notifications')
            ->where('notification_id', $id)
            ->where('user_id', $user->user_id)
            ->first();

        if (! $notification) {
            abort(404);
        }

        $booking = null;
        if ((string) $notification->related_entity_type === 'booking' && ! empty($notification->related_entity_id)) {
            // Enriquece el detalle cuando la notificacion esta ligada a una booking.
            $booking = Booking::with(['event', 'venue'])
                ->where('booking_id', $notification->related_entity_id)
                ->first();
        }

        if (! $notification->is_read) {
            DB::table('notifications')
                ->where('notification_id', $notification->notification_id)
                ->update(['is_read' => true]);
            $notification->is_read = true;
        }

        $from = $request->query('from');
        // Solo acepta vuelta a rutas internas de notificaciones para evitar open redirect.
        if (! is_string($from) || ! str_starts_with($from, route('notifications.index'))) {
            $from = route('notifications.index');
        }

        return view('notifications.show', [
            'notification' => $notification,
            'backUrl' => $from,
            'booking' => $booking,
        ]);
    }
}
