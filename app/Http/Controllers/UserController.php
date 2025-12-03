<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Muestra los detalles de un usuario específico.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Buscar el usuario por su ID o lanzar un error 404
        $user = User::findOrFail($id);

        // Cargar los eventos que el usuario está organizando
        $events = $user->organizedEvents()->paginate(5, ['*'], 'events_page');

        // Cargar los lugares que el usuario gestiona
        $venues = $user->managedVenues()->paginate(5, ['*'], 'venues_page');

        // Vista con el usuario, sus eventos y lugares
        return view('users.show', compact('user', 'events', 'venues'));
    }

    /**
     * Handles profile picture upload for a user.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadProfilePicture(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Find the user by ID
        $user = User::findOrFail($id);

        // Handle the uploaded file
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');

            // Generate a unique filename
            $filename = time() . '_' . $file->getClientOriginalName();

            // Store the file in the public storage directory
            $filePath = $file->storeAs('profile_pictures', $filename, 'public');

            // Update the user's profile_picture field
            $user->profile_picture = $filePath;
            $user->save();

            return redirect()->back()->with('success', 'Profile picture uploaded successfully.');
        }

        return redirect()->back()->with('error', 'No file uploaded.');
    }
}