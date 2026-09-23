<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar Sesión - {{ config('app.name', 'Laravel') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Scripts/Styles -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <!-- Fallback Tailwind CSS if Vite acts up for user temporarily -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                fontFamily: { sans: ['Inter', 'sans-serif'] }
            }
        }
    </script>
</head>
<body class="bg-slate-50 min-h-screen flex font-sans text-slate-900 selection:bg-blue-500 selection:text-white">

    <!-- Panel Izquierdo (Diseño Corporativo) -->
    <div class="hidden lg:flex lg:w-1/2 relative bg-slate-900 border-r border-slate-800 items-center justify-center p-12 overflow-hidden">
        
        <!-- Grid decorativo -->
        <div class="absolute inset-0 bg-[url('https://play.tailwindcss.com/img/grid.svg')] bg-center [mask-image:linear-gradient(180deg,white,rgba(255,255,255,0))] mix-blend-overlay opacity-30 z-0 pointer-events-none"></div>
        
        <!-- Glows radiales -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-blue-600 rounded-full blur-[100px] opacity-20 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 bg-cyan-500 rounded-full blur-[100px] opacity-20 pointer-events-none"></div>

        <div class="relative z-10 max-w-xl text-white">
            <h1 class="text-4xl xl:text-5xl font-bold tracking-tight mb-6 leading-[1.15]">
                Potencia tu gestión <br>con <span class="text-blue-400">excelencia</span>.
            </h1>
            <p class="text-lg xl:text-xl text-slate-300 font-medium mb-10 leading-relaxed max-w-lg">
                Accede a las herramientas administrativas más avanzadas para optimizar el rendimiento y la operación diaria de tu institución.
            </p>
            
            <div class="flex items-center space-x-5 border-t border-slate-800 pt-8 mt-4">
                <div class="flex -space-x-3">
                    <div class="w-10 h-10 rounded-full border-2 border-slate-900 bg-slate-800 flex justify-center items-center font-bold text-xs">V2</div>
                    <div class="w-10 h-10 rounded-full border-2 border-slate-900 bg-slate-700 flex justify-center items-center font-bold text-xs"><svg class="w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg></div>
                </div>
                <div class="text-sm text-slate-400 font-medium leading-tight">
                    Plataforma inteligente y<br><span class="text-white">alta disponibilidad</span>
                </div>
            </div>
        </div>
        
        <!-- Desarrollado por Adaptika -->
        <div class="absolute bottom-8 left-12 z-20 text-sm font-medium text-slate-400">
            Desarrollado por <a href="https://adapptika.com" target="_blank" rel="noopener noreferrer" class="text-slate-300 hover:text-white transition-colors underline decoration-slate-600 hover:decoration-white underline-offset-4">Adaptika</a>
        </div>
    </div>

    <!-- Panel Derecho (Formulario) -->
    <div class="w-full lg:w-1/2 flex flex-col justify-center items-center relative z-10 p-6 sm:p-12 lg:p-20 relative bg-white">
        
        <!-- Contenedor del Formulario -->
        <div class="w-full max-w-md">
            
            <div class="mb-10">
                <div class="inline-flex lg:hidden items-center justify-center p-3 bg-blue-600 rounded-xl shadow-lg shadow-blue-500/30 mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <h2 class="text-3xl font-bold tracking-tight text-slate-900 mb-2">Iniciar Sesión</h2>
                <p class="text-slate-500 font-medium text-sm">Bienvenido de nuevo, ingresa tus credenciales.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Correo Electrónico</label>
                    <input id="email" type="email" class="w-full px-4 py-3 rounded-xl border border-slate-300 bg-white focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all duration-200 @error('email') border-red-500 focus:ring-red-500/20 focus:border-red-500 @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="nombre@empresa.com">
                    
                    @error('email')
                        <p class="mt-2 text-sm font-medium text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-sm font-semibold text-slate-700">Contraseña</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors">¿Olvidaste tu contraseña?</a>
                        @endif
                    </div>
                    <div class="relative">
                        <input id="password" type="password" class="w-full px-4 py-3 rounded-xl border border-slate-300 bg-white focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all duration-200 @error('password') border-red-500 focus:ring-red-500/20 focus:border-red-500 @enderror pr-12" name="password" required autocomplete="current-password" placeholder="••••••••">
                        
                        <!-- Botón Mostrar/Ocultar -->
                        <button type="button" onclick="const p = document.getElementById('password'); const type = p.getAttribute('type') === 'password' ? 'text' : 'password'; p.setAttribute('type', type); this.children[0].classList.toggle('hidden'); this.children[1].classList.toggle('hidden');" class="absolute inset-y-0 right-0 px-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors focus:outline-none">
                            <!-- Icono Ojo (Mostrar) -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <!-- Icono Ojo Tachado (Ocultar) -->
                            <svg class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                        </button>
                    </div>
                    
                    @error('password')
                        <p class="mt-2 text-sm font-medium text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me & Submit -->
                <div class="pt-2">
                    <div class="flex items-center mb-6">
                        <input id="remember" type="checkbox" name="remember" class="h-4 w-4 bg-white border-slate-300 text-blue-600 focus:ring-blue-500 focus:ring-2 focus:ring-offset-1 rounded transition" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember" class="ml-2 text-sm font-medium text-slate-600 select-none cursor-pointer">Mantener sesión iniciada</label>
                    </div>

                    <button type="submit" class="w-full bg-slate-900 text-white py-3.5 rounded-xl font-bold text-sm hover:bg-slate-800 focus:ring-4 focus:ring-slate-900/10 active:bg-slate-950 transition-all duration-200 shadow-md shadow-slate-900/5">
                        Acceder a mi cuenta
                    </button>
                </div>
            </form>
            
            <!-- Mobile Footer / Copyright -->
            <div class="text-center mt-12 lg:mt-20">
                <p class="text-xs font-medium text-slate-400">&copy; {{ date('Y') }} {{ config('app.name', 'MSA') }}. Acceso protegido.</p>
            </div>
            
        </div>
    </div>
</body>
</html>