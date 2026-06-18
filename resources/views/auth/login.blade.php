<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hasil Panen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Favicon aman -->
    <link rel="icon" type="image/png" href="{{ secure_asset('PG.ico') }}">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-green-600"> Hasil Panen</h1>
                <p class="text-gray-600 mt-2">Silakan login untuk melanjutkan</p>
            </div>

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Form login dengan secure URL (pakai route helper) -->
            <form method="POST" action="{{ secure_url('login') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Username</label>
                    <input type="text" name="username" required
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2">Password</label>
                    <input type="password" name="password" required
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <button type="submit"
                        class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition duration-200">
                    Login
                </button>
            </form>
        </div>
    </div>
</body>
</html>