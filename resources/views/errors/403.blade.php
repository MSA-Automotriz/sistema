<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Acceso Denegado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .error-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 3rem;
            text-align: center;
            max-width: 500px;
            width: 100%;
            margin: 20px;
        }

        .error-icon {
            font-size: 5rem;
            color: #dc3545;
            margin-bottom: 1.5rem;
            animation: shake 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        h1 {
            font-size: 3rem;
            color: #333;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        p {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .button-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-outline {
            border: 2px solid #667eea;
            color: #667eea;
            background: transparent;
        }

        .btn-outline:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        .error-details {
            background: #f8f9fa;
            border-left: 4px solid #dc3545;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 2rem;
            text-align: left;
            font-size: 0.9rem;
            color: #555;
        }

        .error-details strong {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-lock"></i>
        </div>

        <h1>403</h1>
        <p><strong>Acceso Denegado</strong></p>

        <div class="error-details">
            <p><strong>Lo sentimos,</strong> no tiene permisos para acceder a esta sección del sistema.</p>

            @if ($exception->getMessage())
                <p style="margin-top: 0.5rem; font-style: italic;">
                    {{ $exception->getMessage() }}
                </p>
            @endif
        </div>

        <p style="font-size: 0.95rem;">
            Si cree que esto es un error, contacte con el administrador del sistema.
        </p>

        <div class="button-group">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>Ir al Dashboard
            </a>
            <a href="{{ route('logout') }}" class="btn btn-outline" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
            </a>
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
