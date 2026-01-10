<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // [LOG] 1. Capturamos los datos actuales antes de modificarlos
        // Usamos toArray() para guardar una copia exacta de cómo estaba
        $valoresAnteriores = $user->toArray();

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // [LOG] 2. Registramos la actualización
        // Usamos $user->toArray() que ya tiene los nuevos valores
        $this->logAction(
            'Actualización de Perfil', 
            'users', 
            $user->id, 
            $valoresAnteriores, 
            $user->toArray()
        );

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        
        // [LOG] 1. Capturamos los datos antes de borrar
        $valoresAnteriores = $user->toArray();

        // [LOG] 2. IMPORTANTE: Registramos el log ANTES de hacer logout
        // Si hiciéramos logout primero, Auth::id() sería null en la función logAction
        $this->logAction(
            'Eliminación de Cuenta Propia', 
            'users', 
            $user->id, 
            $valoresAnteriores, 
            null
        );

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}