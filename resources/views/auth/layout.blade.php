<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Auth') - Book.com</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}?v={{ time() }}">
</head>
<body>
    @yield('content')
    
    <script>
    setInterval(function() {
        fetch('{{ route("home") }}', {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).then(response => response.text()).then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newToken = doc.querySelector('meta[name="csrf-token"]');
            if (newToken) {
                document.querySelector('meta[name="csrf-token"]').setAttribute('content', newToken.content);
                document.querySelectorAll('input[name="_token"]').forEach(input => {
                    input.value = newToken.content;
                });
            }
        }).catch(error => console.log('CSRF refresh failed:', error));
    }, 60000);
    </script>
</body>
</html>
