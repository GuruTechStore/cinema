<?php
// app/Http/Controllers/Auth/AuthController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm(Request $request)
    {
        // Guardar la URL de redirección si viene como parámetro
        if ($request->has('redirect')) {
            session(['url.intended' => $request->get('redirect')]);
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // Redirigir según el rol del usuario
            if (Auth::user()->esAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            // Si hay una URL de intención (como comprar entradas), redirigir ahí
            $redirectTo = session('url.intended', '/');
            session()->forget('url.intended');
            
            return redirect()->to($redirectTo)->with('success', '¡Bienvenido de vuelta!');
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function showRegistrationForm(Request $request)
    {
        // Guardar la URL de redirección si viene como parámetro
        if ($request->has('redirect')) {
            session(['url.intended' => $request->get('redirect')]);
        }
        
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => 'usuario',
        ]);

        Auth::login($user);

        // Si hay una URL de intención (como comprar entradas), redirigir ahí
        $redirectTo = session('url.intended', '/');
        session()->forget('url.intended');
        
        return redirect()->to($redirectTo)->with('success', '¡Cuenta creada exitosamente! Ahora puedes comprar tus entradas.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('info', 'Sesión cerrada correctamente');
    }
}

?>