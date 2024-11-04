<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">

        <!-- Styles -->
        <style>
             /* Estilos de los enlaces */
        a {
            text-decoration: none; /* Elimina el subrayado del enlace */
            color: #3498db; /* Color inicial del enlace */
            transition: background-color 0.3s ease, transform 0.3s ease; /* Transición suave */
        }

        a:hover {
            background-color: #ecf0f1; /* Color de fondo al pasar el mouse */
            transform: scale(1.05); /* Aumentar el tamaño ligeramente */
            color: #2980b9; /* Color al pasar el mouse */
        }
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
                background-image: url('https://urvina.com.mx/es/img/slider_Urvina_1_Mesa%20de%20trabajo%201.jpg'); /* Fondo con imagen */
                background-size: cover; /* Ajustar la imagen de fondo */
                background-position: center; /* Centrar la imagen de fondo */
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
                padding: 20px;
                border: 5px solid #636b6f; /* Borde del marco */
                border-radius: 15px; /* Esquinas redondeadas */
                background-color: white; /* Fondo blanco para el contenido */
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); /* Sombra para el efecto de profundidad */
            }

            .title {
                font-size: 84px;
            }

            .subtitle {
                font-size: 36px; /* Tamaño del subtítulo */
                margin-top: 10px; /* Espaciado arriba del subtítulo */
                color: #636b6f; /* Color del subtítulo */
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="content">
                <!-- Imagen en lugar del texto -->
                <img src="data:image/png;base64,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" alt="Grupo Urvina" class="title" style="width: 200px; height: auto;"> <!-- Cambia la ruta a la imagen -->
                
                <h2 class="subtitle">DASHBOARD DE VENTAS</h2> <!-- Subtítulo agregado -->

                <div class="links">
                    <a href="/rentautil">Utilidad y Rentabilidad</a>
                    <a href="/topsupinf">TOP SUPERIOR E INFERIOR</a>
                    <a href="/ventaspermes">VENTAS EN PORCENTAJE POR MES</a>
                </div>
            </div>
        </div>
    </body>
</html>
