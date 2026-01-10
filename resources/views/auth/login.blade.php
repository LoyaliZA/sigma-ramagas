<x-guest-layout title="Login">
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="text-center mb-6">
            <h3 class="text-gray-600 font-bold text-xl">Bienvenido</h3>
            <p class="text-gray-400 text-sm">Ingresa tus credenciales para continuar</p>
        </div>

        <div class="relative">
            <label for="email" class="block font-semibold text-xs text-gray-600 uppercase mb-1 ml-1">Correo Electrónico</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="bi bi-envelope text-gray-400"></i>
                </div>
                <input id="email" class="pl-10 block mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:border-sigma focus:ring focus:ring-sigma focus:ring-opacity-20 transition duration-200 py-2.5" 
                    type="email" name="email" :value="old('email')" required autofocus placeholder="correo@bellaroma.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-xs" />
        </div>

        <div class="mt-5 relative">
            <label for="password" class="block font-semibold text-xs text-gray-600 uppercase mb-1 ml-1">Contraseña</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="bi bi-lock text-gray-400"></i>
                </div>
                <input id="password" class="pl-10 block mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:border-sigma focus:ring focus:ring-sigma focus:ring-opacity-20 transition duration-200 py-2.5" 
                    type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-xs" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-sigma shadow-sm focus:ring-sigma" name="remember">
                <span class="ms-2 text-sm text-gray-500 select-none">Recordarme</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-gray-400 hover:text-sigma font-medium transition-colors" href="{{ route('password.request') }}">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>

        <div class="mt-8">
            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-bold text-white bg-sigma hover:bg-sigma-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sigma transition ease-in-out duration-150 transform hover:-translate-y-0.5">
                INGRESAR AL SISTEMA
            </button>
        </div>

        <div class="relative mt-8 mb-4">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center">
                <span class="px-2 bg-white text-xs text-gray-400 uppercase tracking-wider">Acceso Corporativo</span>
            </div>
        </div>
    </form>
</x-guest-layout>