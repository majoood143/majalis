{{-- resources/views/welcome.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Majalis - Hall Booking System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="flex items-center justify-center min-h-screen">
        <div class="text-center">
            <h1 class="mb-4 text-5xl font-bold text-gray-800">Majalis</h1>
            <p class="mb-8 text-xl text-gray-600">Hall Booking Management System</p>

            <div class="space-x-4">
                <a href="{{ route('customer.halls.index') }}"
                   class="inline-block px-6 py-3 text-white transition bg-blue-600 rounded-lg hover:bg-blue-700">
                    Browse Halls
                </a>

                <a href="{{ route('filament.admin.auth.login') }}"
                   class="inline-block px-6 py-3 text-white transition bg-gray-600 rounded-lg hover:bg-gray-700">
                    Admin Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>
