<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login' }} | BPJS Keliling</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#033e87',
                        secondary: '#01a850',
                        lightBg: '#f8fafc',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        /* Smooth fade-in */
        body {
            opacity: 0;
            transition: opacity 0.3s;
        }

        body.loaded {
            opacity: 1;
        }
    </style>
</head>

<body class="bg-lightBg font-sans text-slate-700" onload="document.body.classList.add('loaded')">
    @yield('content')
</body>

</html>