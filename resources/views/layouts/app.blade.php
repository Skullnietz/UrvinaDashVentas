<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
      
        
        <!-- Incluye DataTables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
        
       
    <style>
        .navbar {
            background-color: black;
        }
        .navbar-brand, .nav-link {
            color: white;
            transition: color 0.3s ease;
        }
        .nav-link:hover, .nav-link:focus {
        color: #f8f9fa;
        transform: scale(1.1); /* Aumenta ligeramente el tamaño al pasar el mouse */
    }
    .active {
        font-weight: bold; /* Resalta el elemento activo */
        color: #f8f9fa; /* Cambia el color del texto activo */
    }
    .navbar img {
        height: 50px; /* Ajusta el tamaño de la imagen según sea necesario */
    }
    body {
        background-color: #f0f0f0; /* Fondo gris claro */
    }
    .content-area {
        background-color: rgba(255, 255, 255, 0.9); /* Fondo blanco con opacidad */
        border-radius: 8px; /* Bordes redondeados */
        padding: 20px; /* Espaciado interno */
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Sombra */
    }
    </style>
</head>
<body>


    
    <!-- Incluye la versión completa de jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
     <!-- Incluye DataTables JS -->
     <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
        <!-- Contenido de la página -->
        @yield('content')

        <!-- Cargar los scripts de la página -->
        @yield('scripts')
</body>
</html>
