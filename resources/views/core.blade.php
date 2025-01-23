<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba Campuslands</title>
    <!-- Import Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <header class="p-3 bg-dark text-white">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
                    Campuslands
                </a>

                <ul class="nav col-12 col-lg-auto ml-lg-auto mb-2 justify-content-center mb-md-0">
                    <li><a href="/products" class="nav-link px-2 text-white">Productos</a></li>
                    <li><a href="/warehouse" class="nav-link px-2 text-white">Bodegas</a></li>
                    <li><a href="/inventories" class="nav-link px-2 text-white">Inventario</a></li>
                </ul>
            </div>
        </div>
    </header>

    <div class="container pt-2">
        @yield('content')
    </div>

    <!-- Import Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Import Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    @yield('scripts')
</body>

</html>
