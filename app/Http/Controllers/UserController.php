<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * CRUD de usuarios (referencia rapida):
     * - Read: show()
     * - Update: edit(), update(), updatePassword(), uploadProfilePicture()
     * - Delete: destroy()
     *
     * Nota: el alta de usuarios se gestiona en el flujo de autenticacion/registro.
     */
    /**
     * Muestra los detalles de un usuario específico.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Carga el perfil objetivo y sus relaciones paginadas.
        $user = User::findOrFail($id);

        // Carga los eventos que organiza el usuario.
        $events = $user->organizedEvents()->paginate(5, ['*'], 'events_page');

        // Carga los locales que gestiona el usuario.
        $venues = $user->managedVenues()->paginate(5, ['*'], 'venues_page');

        // Retorna la vista con perfil, eventos y locales.
        return view('users.show', compact('user', 'events', 'venues'));
    }

    /**
     * Muestra el formulario para editar un usuario.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        // Solo el propietario de la cuenta puede acceder al formulario de edición.
        if (!auth()->check() || (int) auth()->user()->user_id !== (int) $id) {
            return redirect()->route('users.show', $id)->with('error', 'Solo puedes editar tu propia cuenta.');
        }

        $user = User::findOrFail($id);

        return view('users.edit', compact('user'));
    }

    /**
     * Actualiza los datos de un usuario.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Bloquea acceso si no hay sesion o si el perfil no pertenece al usuario autenticado.
        if (!auth()->check() || (int) auth()->user()->user_id !== (int) $id) {
            return redirect()->route('users.show', $id)->with('error', 'Solo puedes editar tu propia cuenta.');
        }

        $user = User::findOrFail($id);

        // Usa un bag de errores independiente para el formulario de perfil.
        $validatedData = $request->validateWithBag('profileUpdate', [
            'username' => 'required|string|max:50|unique:users,username,' . $user->user_id . ',user_id',
            'email' => 'required|email|max:100|unique:users,email,' . $user->user_id . ',user_id',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone' => 'nullable|string|max:20',
        ], [
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.max' => 'El nombre de usuario no puede superar los 50 caracteres.',
            'username.unique' => 'El nombre de usuario ya está en uso.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'Introduce un email válido.',
            'email.max' => 'El email no puede superar los 100 caracteres.',
            'email.unique' => 'El email ya está en uso.',
            'first_name.required' => 'El nombre es obligatorio.',
            'first_name.max' => 'El nombre no puede superar los 50 caracteres.',
            'last_name.required' => 'El apellido es obligatorio.',
            'last_name.max' => 'El apellido no puede superar los 50 caracteres.',
            'phone.max' => 'El teléfono no puede superar los 20 caracteres.',
        ]);

        $user->username = $validatedData['username'];
        $user->email = $validatedData['email'];
        $user->first_name = $validatedData['first_name'];
        $user->last_name = $validatedData['last_name'];
        $user->phone = $validatedData['phone'] ?? null;

        $user->save();

        return redirect()->route('users.show', $user->user_id)->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Actualiza la contraseña del usuario autenticado.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request, $id)
    {
        // Aplica la misma regla de autorizacion: cada usuario gestiona su propia contraseña.
        if (!auth()->check() || (int) auth()->user()->user_id !== (int) $id) {
            return redirect()->route('users.show', $id)->with('error', 'Solo puedes editar tu propia cuenta.');
        }

        $user = User::findOrFail($id);

        // Usa un bag de errores independiente para el formulario de contraseña.
        $validatedData = $request->validateWithBag('passwordUpdate', [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed|different:current_password',
        ], [
            'current_password.required' => 'La contraseña actual es obligatoria.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.different' => 'La nueva contraseña debe ser diferente de la actual.',
        ]);

        if (!Hash::check($validatedData['current_password'], $user->password_hash)) {
            return back()->withErrors([
                'current_password' => 'La contraseña actual no es correcta.',
            ], 'passwordUpdate');
        }

        $user->password_hash = Hash::make($validatedData['password']);
        $user->save();

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }

    /**
     * Elimina una cuenta de usuario si no tiene dependencias activas.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // Permite eliminar la cuenta solo al propio usuario autenticado.
        if (!auth()->check() || (int) auth()->user()->user_id !== (int) $id) {
            return redirect()->route('users.show', $id)->with('error', 'Solo puedes eliminar tu propia cuenta.');
        }

        $user = User::findOrFail($id);

        // Evita borrar usuarios con datos dependientes para mantener integridad funcional.
        if (
            $user->organizedEvents()->exists() ||
            $user->managedVenues()->exists() ||
            DB::table('bookings')->where('approved_by', $user->user_id)->exists() ||
            DB::table('notifications')->where('user_id', $user->user_id)->exists()
        ) {
            return redirect()->route('users.show', $user->user_id)
                ->with('error', 'No se puede eliminar la cuenta porque tiene datos relacionados (eventos, locales, reservas o notificaciones).');
        }

        if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        // Ejecuta eliminacion y cierre de sesion en una sola transaccion.
        DB::transaction(function () use ($user) {
            $user->delete();
            Auth::logout();
            session()->forget(['user_id', 'user_type']);
            session()->invalidate();
            session()->regenerateToken();
        });

        return redirect()->route('home')->with('success', 'Tu cuenta ha sido eliminada correctamente.');
    }

    /**
     * Gestiona la subida de foto de perfil para un usuario.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadProfilePicture(Request $request, $id)
    {
        // Permite actualizar foto solo al propietario de la cuenta.
        if (!auth()->check() || (int) auth()->user()->user_id !== (int) $id) {
            return redirect()->back()->with('error', 'No tienes permisos para actualizar esta foto de perfil.');
        }

        // Valida la solicitud de subida de imagen.
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Busca el usuario por ID.
        $user = User::findOrFail($id);

        // Procesa la nueva imagen y reemplaza la anterior de forma segura.
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');

            // Genera un nombre de archivo unico.
            $filename = time() . '_' . $file->getClientOriginalName();

            // Guarda el archivo en almacenamiento publico.
            $filePath = $file->storeAs('profile_pictures', $filename, 'public');

            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            // Actualiza el campo profile_picture del usuario.
            $user->profile_picture = $filePath;
            $user->save();

            return redirect()->back()->with('success', 'Foto de perfil actualizada correctamente.');
        }

        return redirect()->back()->with('error', 'No se ha subido ningún archivo.');
    }
}