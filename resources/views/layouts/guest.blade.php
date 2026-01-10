<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SIGMA') }}</title>

        <link rel="icon" href="{{ asset('img/ramagas_mini.ico') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=nunito:400,500,600,700&display=swap" rel="stylesheet" />
        
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

        <script src="https://cdn.tailwindcss.com"></script>
        
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            sigma: {
                                DEFAULT: '#3c5b9b', // Tu azul principal
                                hover: '#2c4a85',   // Un tono más oscuro para hover
                                light: '#f1f3f9'    // El fondo grisáceo suave
                            }
                        }
                    }
                }
            }
        </script>
        
        <style>
            /* Pequeños ajustes para fuentes */
            body { font-family: 'Nunito', sans-serif; }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased bg-sigma-light">
        <div class="min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0">
            
            <div class="w-full sm:max-w-md mt-6 px-0 bg-white shadow-xl overflow-hidden rounded-xl border border-gray-100">
                
                <div class="bg-sigma py-8 text-center relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-full bg-white opacity-5">
                        <i class="bi bi-grid-3x3 text-9xl -ml-10 -mt-10 absolute"></i>
                    </div>

                    <div class="relative z-10">
                        <div class="flex justify-center mb-3">
                            <div class="p-3 bg-white/10 rounded-full backdrop-blur-sm">
                                <i class="bi bi-box-seam text-4xl text-white"></i>
                            </div>
                        </div>
                        <h2 class="text-3xl font-bold text-white tracking-wide">SIGMA</h2>
                        <p class="text-blue-100 text-sm font-light tracking-widest uppercase mt-1">Gestión de Activos</p>
                    </div>
                </div>

                <div class="px-8 py-8">
                    {{ $slot }}
                </div>
            </div>

            <div class="mt-6 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} Neobash - Ramagas - Departamento de Sistemas
            </div>
        </div>
    </body>
</html>