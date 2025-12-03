<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Muestra el formulario de registro (Sign-In).
     *
     * @return \Illuminate\View\View
     */
    public function showSignInForm()
    {
        return view('sign-in');
    }

    /**
     * Maneja el registro de nuevos usuarios (Sign-In).
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function signIn(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:8',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
        ]);

        // Crear el usuario en la base de datos
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password), // Hashear la contraseña
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'user_type' => 'user', // Tipo de usuario por defecto
            'is_active' => true, // Activar el usuario por defecto
        ]);

        // Iniciar sesión automáticamente
        session(['user_id' => $user->user_id]);

        // Redirigir al dashboard
        return redirect()->route('dashboard')->with('success', '¡Usuario registrado con éxito!');
    }

    /**
     * Muestra el formulario de inicio de sesión (Login).
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function showLoginForm(Request $request)
    {
        // Guarda la URL anterior en la sesión si no es la ruta de login
        if ($request->headers->get('referer') && $request->headers->get('referer') !== route('login')) {
            session(['url.intended' => $request->headers->get('referer')]);
        }

        return view('login');
    }

    /**
     * Maneja el inicio de sesión (Login).
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Buscar al usuario por email
        $user = User::where('email', $request->email)->first();

        // Verificar si el usuario existe y la contraseña es correcta
        if ($user && Hash::check($request->password, $user->password_hash)) {
            // Iniciar sesión utilizando Auth
            Auth::login($user);

            // Almacenar explícitamente el user_id y user_type en la sesión
            session([
                'user_id' => $user->user_id,
                'user_type' => $user->user_type,
            ]);

            // Redirigir al perfil del usuario después del inicio de sesión exitoso
            return redirect()->route('users.show', $user->user_id)->with('success', '¡Inicio de sesión exitoso!');

        }

        // Si la autenticación falla, redirigir con un error
        return back()->withErrors(['email' => 'Correo electrónico o contraseña inválidos.']);
    }

    /**
     * Muestra el dashboard solo si el usuario está autenticado.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function dashboard()
    {
        // Verificar si el usuario está autenticado
        if (!session()->has('user_id')) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder a esta página.');
        }

        // Obtener el usuario autenticado
        $user = User::find(session('user_id'));

        return view('dashboard', compact('user'));
    }

    /**
     * Maneja el cierre de sesión.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        // Eliminar la sesión del usuario
        session()->forget('user_id');

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
        // Validar el input
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8',
        ]);

        // Encontrar el usuario autenticado
        $user = User::find(session('user_id'));

        // Comprobar si la contraseña actual es correcta
        if (!Hash::check($request->current_password, $user->password_hash)) {
            return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
        }

        // Actualizar la contraseña del usuario
        $user->password_hash = Hash::make($request->new_password);
        $user->save();

        // Redirigir de vuelta con un mensaje de éxito
        return back()->with('success', '¡Contraseña cambiada con éxito!');
    }

    /**
     * Debugging session and authentication
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function debugSession(Request $request)
    {
        // Debugging session and authentication
        dd([
            'session_user_id' => session('user_id'),
            'auth_check' => Auth::check(),
            'auth_user' => Auth::user(),
        ]);
    }
}