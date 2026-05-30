<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    /**
     * Muestra el formulario de contacto.
     */
    public function index()
    {
        // Precarga datos del usuario para completar el formulario.
        $user = auth()->user();

        if (! $user && session()->has('user_id')) {
            $user = User::find(session('user_id'));
        }

        return view('contact', compact('user'));
    }

    /**
     * Envia un mensaje de contacto a los administradores como notificacion interna.
     */
    public function store(Request $request)
    {
        // Valida datos del formulario de contacto.
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:2000',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede superar los 100 caracteres.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'Introduce un email valido.',
            'email.max' => 'El email no puede superar los 150 caracteres.',
            'subject.required' => 'El asunto es obligatorio.',
            'subject.max' => 'El asunto no puede superar los 150 caracteres.',
            'message.required' => 'El mensaje es obligatorio.',
            'message.max' => 'El mensaje no puede superar los 2000 caracteres.',
        ]);

        // Obtiene destinatarios: administradores activos del sistema.
        $adminIds = User::query()
            ->where('user_type', 'admin')
            ->where('is_active', true)
            ->pluck('user_id')
            ->all();

        if (empty($adminIds)) {
            return back()
                ->withInput()
                ->withErrors(['message' => 'No hay administradores disponibles para recibir tu mensaje en este momento.']);
        }

        $subject = trim($validated['subject']);
        $title = "Contacto: {$subject}";

        $senderName = $validated['name'];
        $senderEmail = $validated['email'];
        $body = trim($validated['message']);

        $notificationMessage = "Mensaje de contacto de {$senderName} ({$senderEmail}).";
        $notificationMessage .= " Contenido: {$body}";

        $now = now();
        // Inserta notificaciones en bloque para reducir consultas.
        $rows = array_map(function (int $adminId) use ($title, $notificationMessage, $now) {
            return [
                'user_id' => $adminId,
                'title' => $title,
                'message' => $notificationMessage,
                'is_read' => false,
                'notification_type' => 'contact',
                'related_entity_type' => null,
                'related_entity_id' => null,
                'created_at' => $now,
            ];
        }, $adminIds);

        DB::table('notifications')->insert($rows);

        return redirect()->route('home')->with('success', 'Tu mensaje se ha enviado correctamente.');
    }
}
