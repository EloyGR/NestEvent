<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // Roles permitidos en el flujo de alta pública de gestores.
    private const SIGN_IN_ROLES = ['event_manager', 'local_manager'];

    /**
     * Muestra el formulario de registro.
     *
     * @return \Illuminate\View\View
     */
    public function showSignInRoleSelection()
    {
        return view('sign-in-selector');
    }

    /**
     * Muestra el formulario de registro para un tipo de gestor.
     *
     * @param string $role
     * @return \Illuminate\View\View
     */
    public function showSignInForm(string $role)
    {
        // Blindaje: evitamos mostrar el formulario con roles no permitidos.
        if (!in_array($role, self::SIGN_IN_ROLES, true)) {
            abort(404);
        }

        return view('sign-in', ['selectedRole' => $role]);
    }

    /**
     * Maneja el registro de nuevos usuarios.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function signIn(Request $request)
    {
        // Valida los datos de alta y la imagen opcional.
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:8',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'user_type' => 'required|in:event_manager,local_manager',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $profilePicturePath = null;
        if ($request->hasFile('profile_picture')) {
            // Guarda la imagen en disco publico para su uso en vistas.
            $file = $request->file('profile_picture');
            $filename = time() . '_' . $file->getClientOriginalName();
            $profilePicturePath = $file->storeAs('profile_pictures', $filename, 'public');
        }

        // Crea el usuario con contraseña hasheada.
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password), // Hashea la contraseña antes de guardar.
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'profile_picture' => $profilePicturePath,
            'user_type' => $request->user_type,
            'is_active' => true, // Marca el usuario como activo por defecto.
        ]);

        // Inicia sesion y conserva compatibilidad con la sesion legacy.
        Auth::login($user);
        // Mitiga session fixation regenerando el identificador tras autenticar.
        $request->session()->regenerate();
        session([
            'user_id' => $user->user_id,
            'user_type' => $user->user_type,
        ]);

        // Redirige al perfil del usuario recien creado.
        return redirect()->route('users.show', $user->user_id)->with('success', '¡Usuario registrado con éxito!');
    }

    /**
     * Muestra el formulario de inicio de sesion.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function showLoginForm(Request $request)
    {
        // Guardamos destino anterior para poder volver tras autenticación.
        if ($request->headers->get('referer') && $request->headers->get('referer') !== route('login')) {
            session(['url.intended' => $request->headers->get('referer')]);
        }

        return view('login');
    }

    /**
     * Maneja el inicio de sesion.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Valida credenciales de entrada.
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Busca el usuario por correo y valida password_hash.
        $user = User::where('email', $request->email)->first();

        // Si las credenciales son correctas, inicia sesion.
        if ($user && Hash::check($request->password, $user->password_hash)) {
            // Sincroniza el guard de Laravel y la sesion custom.
            Auth::login($user);
            // Rotamos la sesion en cada login valido por seguridad.
            $request->session()->regenerate();

            // Guarda user_id y user_type en la sesion.
            session([
                'user_id' => $user->user_id,
                'user_type' => $user->user_type,
            ]);

            // Redirige al perfil tras iniciar sesion.
            return redirect()->route('users.show', $user->user_id)->with('success', '¡Inicio de sesión exitoso!');

        }

        // Devuelve error si la autenticacion falla.
        return back()->withErrors(['email' => 'Correo electrónico o contraseña inválidos.']);
    }

    /**
     * Muestra el dashboard solo si el usuario está autenticado.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function dashboard()
    {
        // Fallback explícito por sesión para vistas que dependen de user_id.
        if (!session()->has('user_id')) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder a esta página.');
        }

        // Carga el usuario autenticado desde la sesion.
        $user = User::find(session('user_id'));

        return view('dashboard', compact('user'));
    }

    /**
     * Maneja el cierre de sesión.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        // Cerramos ambos contextos de autenticación: guard + sesión custom.
        Auth::logout();
        session()->forget(['user_id', 'user_type']);

        // Invalidamos sesion y token CSRF para cerrar completamente el contexto anterior.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', '¡Cierre de sesión exitoso!');
    }

    /**
     * Maneja el cambio de contraseña del usuario.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(Request $request)
    {
        // Valida los campos requeridos para cambiar la contraseña.
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8',
        ]);

        // Recupera el usuario desde la sesion legacy.
        $user = User::find(session('user_id'));

        // Verifica la contraseña actual antes de guardar la nueva.
        if (!Hash::check($request->current_password, $user->password_hash)) {
            return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
        }

        // Actualiza la contraseña del usuario.
        $user->password_hash = Hash::make($request->new_password);
        $user->save();

        // Redirige con mensaje de exito.
        return back()->with('success', '¡Contraseña cambiada con éxito!');
    }

    /**
     * Depuracion de sesion y autenticacion.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function debugSession(Request $request)
    {
        // Muestra estado de sesion y autenticacion para depuracion.
        dd([
            'session_user_id' => session('user_id'),
            'auth_check' => Auth::check(),
            'auth_user' => Auth::user(),
        ]);
    }
}