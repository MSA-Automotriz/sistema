<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Concesionario MSA: Autos, camiones y motos de alta calidad. Encuentra el vehículo perfecto con las mejores ofertas.">
    <meta name="keywords" content="concesionario, autos, camiones, motos, MSA, vehículos, comprar auto, ofertas vehículos">
    <meta name="author" content="MSA Concesionario">
    <title>MSA Concesionario - Autos, Camiones y Motos</title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Google Fonts: Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Swiper.js para el carrusel -->
    <link href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
        /* Estilo del carrusel */
        .swiper-container {
            width: 100%;
            overflow: hidden;
        }
        .swiper-slide {
            background-size: cover;
            background-position: center;
            height: 600px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            position: relative;
        }
        @media (max-width: 768px) {
            .swiper-slide {
                height: 400px;
            }
        }
        .swiper-slide::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }
        .swiper-slide-content {
            position: relative;
            z-index: 2;
            max-width: 90%;
        }
        .swiper-button-prev, .swiper-button-next {
            color: #FF0000;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .swiper-button-prev::after, .swiper-button-next::after {
            font-size: 20px;
        }
        .swiper-pagination-bullet {
            background: white;
            opacity: 0.5;
        }
        .swiper-pagination-bullet-active {
            opacity: 1;
            background: #FF0000;
        }
        .vehicle-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .vehicle-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .whatsapp-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        .filter-btn {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .filter-btn.active {
            background-color: #FF0000;
            color: white;
        }
    </style>
</head>
<body class="bg-white text-black">
    <!-- Encabezado -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center">
                <img src="https://via.placeholder.com/150x50?text=MSA+Logo" alt="MSA Logo" class="h-10">
            </div>
            <!-- Navegación -->
            <nav class="hidden md:flex space-x-6">
                <a href="#" class="text-black hover:text-red-600 font-medium">Inicio</a>
                <a href="#vehicles" class="text-black hover:text-red-600 font-medium">Vehículos</a>
                <a href="#contact" class="text-black hover:text-red-600 font-medium">Contacto</a>
                <a href="#about" class="text-black hover:text-red-600 font-medium">Nosotros</a>
            </nav>
            <!-- Autenticación -->
            <div class="flex items-center space-x-4">
                @auth
                    <a href="{{ url('/admin/dashboard') }}" class="text-black hover:text-red-600 font-medium">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-black hover:text-red-600 font-medium">Cerrar Sesión</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-black hover:text-red-600 font-medium">Iniciar Sesión</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 font-medium">Registrarse</a>
                    @endif
                @endauth
            </div>
        </div>
    </header>

    <!-- Sección Hero con Carrusel -->
    <section class="swiper-container">
        <div class="swiper-wrapper">
            <!-- Slide 1: Auto -->
            <div class="swiper-slide" style="background-image: url('https://via.placeholder.com/1920x600?text=Auto+Destacado')">
                <div class="swiper-slide-content">
                    <h1 class="text-4xl md:text-6xl font-bold mb-4">Descubre el Auto Perfecto</h1>
                    <p class="text-lg md:text-2xl mb-6">Elegancia y potencia en cada viaje.</p>
                    <a href="#vehicles" class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 font-medium">Explorar Autos</a>
                </div>
            </div>
            <!-- Slide 2: Camión -->
            <div class="swiper-slide" style="background-image: url('https://via.placeholder.com/1920x600?text=Camión+Destacado')">
                <div class="swiper-slide-content">
                    <h1 class="text-4xl md:text-6xl font-bold mb-4">Camiones de Alta Capacidad</h1>
                    <p class="text-lg md:text-2xl mb-6">Potencia para tu negocio.</p>
                    <a href="#vehicles" class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 font-medium">Explorar Camiones</a>
                </div>
            </div>
            <!-- Slide 3: Moto -->
            <div class="swiper-slide" style="background-image: url('https://via.placeholder.com/1920x600?text=Moto+Destacada')">
                <div class="swiper-slide-content">
                    <h1 class="text-4xl md:text-6xl font-bold mb-4">Vive la Aventura en Moto</h1>
                    <p class="text-lg md:text-2xl mb-6">Libertad sobre dos ruedas.</p>
                    <a href="#vehicles" class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 font-medium">Explorar Motos</a>
                </div>
            </div>
        </div>
        <!-- Navegación del Carrusel -->
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </section>

    <!-- Sección de Vehículos con Filtros -->
    <section id="vehicles" class="container mx-auto px-4 py-12">
        <h2 class="text-4xl font-bold text-center mb-8">Explora Nuestros Vehículos</h2>
        <!-- Filtros -->
        <div class="flex justify-center space-x-4 mb-8">
            <button class="filter-btn px-4 py-2 rounded-lg border border-gray-300 hover:bg-red-600 hover:text-white active" data-filter="all">Todos</button>
            <button class="filter-btn px-4 py-2 rounded-lg border border-gray-300 hover:bg-red-600 hover:text-white" data-filter="auto">Autos</button>
            <button class="filter-btn px-4 py-2 rounded-lg border border-gray-300 hover:bg-red-600 hover:text-white" data-filter="camion">Camiones</button>
            <button class="filter-btn px-4 py-2 rounded-lg border border-gray-300 hover:bg-red-600 hover:text-white" data-filter="moto">Motos</button>
        </div>
        <!-- Lista de Vehículos -->
        <div id="vehicle-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Vehículo 1: Auto -->
            <div class="vehicle-card bg-white rounded-lg shadow-lg overflow-hidden" data-category="auto">
                <img src="https://via.placeholder.com/400x250?text=Auto+1" alt="Auto 1" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="text-xl font-semibold">Auto Modelo 1</h3>
                    <p class="text-gray-600 mb-2">$20,990</p>
                    <p class="text-sm text-gray-500">Motor 1.5L Turbo</p>
                    <a href="#" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 mt-4 inline-block">Ver Detalles</a>
                </div>
            </div>
            <!-- Vehículo 2: Camión -->
            <div class="vehicle-card bg-white rounded-lg shadow-lg overflow-hidden" data-category="camion">
                <img src="https://via.placeholder.com/400x250?text=Camión+1" alt="Camión 1" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="text-xl font-semibold">Camión Modelo 1</h3>
                    <p class="text-gray-600 mb-2">$45,000</p>
                    <p class="text-sm text-gray-500">Capacidad 10 Toneladas</p>
                    <a href="#" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 mt-4 inline-block">Ver Detalles</a>
                </div>
            </div>
            <!-- Vehículo 3: Moto -->
            <div class="vehicle-card bg-white rounded-lg shadow-lg overflow-hidden" data-category="moto">
                <img src="https://via.placeholder.com/400x250?text=Moto+1" alt="Moto 1" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="text-xl font-semibold">Moto Modelo 1</h3>
                    <p class="text-gray-600 mb-2">$5,500</p>
                    <p class="text-sm text-gray-500">Motor 250cc</p>
                    <a href="#" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 mt-4 inline-block">Ver Detalles</a>
                </div>
            </div>
            <!-- Vehículo 4: Auto -->
            <div class="vehicle-card bg-white rounded-lg shadow-lg overflow-hidden" data-category="auto">
                <img src="https://via.placeholder.com/400x250?text=Auto+2" alt="Auto 2" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="text-xl font-semibold">Auto Modelo 2</h3>
                    <p class="text-gray-600 mb-2">$22,000</p>
                    <p class="text-sm text-gray-500">Motor 2.0L</p>
                    <a href="#" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 mt-4 inline-block">Ver Detalles</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección: Adquiere tu Auto Soñado -->
    <section id="contact" class="bg-gray-100 py-12">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold mb-4">Adquiere el Vehículo de tus Sueños</h2>
            <p class="text-lg mb-6">Contáctanos hoy y descubre las mejores opciones de financiamiento y personalización.</p>
            <a href="https://wa.me/51987654321" target="_blank" class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 font-medium">Contáctanos Ahora</a>
        </div>
    </section>

    <!-- Información de la Empresa (SEO) -->
    <section id="about" class="container mx-auto px-4 py-12">
        <h2 class="text-4xl font-bold text-center mb-8">Sobre MSA Concesionario</h2>
        <div class="prose max-w-none text-center">
            <p>En <strong>MSA Concesionario</strong>, somos líderes en la venta de autos, camiones y motos en el Perú. Con más de 20 años de experiencia, ofrecemos vehículos de alta calidad, servicio excepcional y opciones de financiamiento adaptadas a tus necesidades. Nuestra misión es ayudarte a encontrar el vehículo perfecto, ya sea para tu negocio, aventuras personales o movilidad diaria.</p>
            <p>Nuestros concesionarios están ubicados en las principales ciudades del país, y contamos con un equipo de expertos listos para asesorarte. Visítanos y descubre por qué miles de clientes confían en MSA para hacer realidad sus sueños de movilidad.</p>
            <p><strong>Dirección:</strong> Av. Principal 123, Lima, Perú<br>
               <strong>Teléfono:</strong> +51 987 654 321<br>
               <strong>Email:</strong> contacto@msa.com</p>
        </div>
    </section>

    <!-- Sección Llamativa: Testimonios -->
    <section class="bg-black text-white py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-8">Lo que Dicen Nuestros Clientes</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white text-black rounded-lg p-6 shadow-lg">
                    <p class="italic mb-4">"Compré mi camión en MSA y el servicio fue impecable. ¡Recomendado!"</p>
                    <p class="font-semibold">Juan Pérez</p>
                </div>
                <div class="bg-white text-black rounded-lg p-6 shadow-lg">
                    <p class="italic mb-4">"La moto que adquirí superó mis expectativas. ¡Gran experiencia!"</p>
                    <p class="font-semibold">María Gómez</p>
                </div>
                <div class="bg-white text-black rounded-lg p-6 shadow-lg">
                    <p class="italic mb-4">"El proceso de compra fue rápido y confiable. ¡Volveré por otro auto!"</p>
                    <p class="font-semibold">Carlos López</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pie de Página -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-bold mb-4">MSA Concesionario</h3>
                    <p>Soluciones confiables para transporte y movilidad.</p>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Enlaces Rápidos</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-red-400">Inicio</a></li>
                        <li><a href="#vehicles" class="hover:text-red-400">Vehículos</a></li>
                        <li><a href="#contact" class="hover:text-red-400">Contacto</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Contacto</h3>
                    <p>Email: contacto@msa.com</p>
                    <p>Teléfono: +51 987 654 321</p>
                </div>
            </div>
            <div class="mt-8 text-center">
                <p>© {{ date('Y') }} MSA Concesionario. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Botón Flotante de WhatsApp -->
    <a href="https://wa.me/51987654321" target="_blank" class="whatsapp-btn bg-red-600 text-white p-4 rounded-full shadow-lg hover:bg-red-700">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.117.548 4.114 1.504 5.867L0 24l6.305-1.653A11.95 11.95 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm6.706 18.294c-.308.867-1.814 1.667-2.51 1.794-.695.127-1.59.063-2.857-.508-2.413-1.086-3.948-3.556-4.066-3.74-.118-.185-.986-1.496-1.004-2.84 0-.706.39-1.058.508-1.204.118-.145.295-.363.472-.363.177 0 .354.06.472.12.118.06 1.416 1.667 1.416 1.667s-.295.363-.413.544c-.118.182-.236.363-.236.544 0 .182.708 1.03 1.535 1.998.827.968 1.535 1.151 1.653 1.272.118.121.413.302.767-.06.354-.363 1.416-1.575 1.416-1.575s.295.121.531.302c.236.182.354.363.413.484.06.121.177.665-.1 1.385z"/>
        </svg>
    </a>

    <!-- Scripts -->
    <script>
        // Inicializar Swiper
        const swiper = new Swiper('.swiper-container', {
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            slidesPerView: 1,
            spaceBetween: 0,
            preloadImages: false,
            lazy: true,
        });

        // Filtros de Vehículos
        document.querySelectorAll('.filter-btn').forEach(button => {
            button.addEventListener('click', () => {
                document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');

                const filter = button.getAttribute('data-filter');
                const vehicles = document.querySelectorAll('.vehicle-card');

                vehicles.forEach(vehicle => {
                    if (filter === 'all' || vehicle.getAttribute('data-category') === filter) {
                        vehicle.style.display = 'block';
                    } else {
                        vehicle.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>