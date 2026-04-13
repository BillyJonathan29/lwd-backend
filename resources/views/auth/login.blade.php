<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dashboard Alope</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-b from-gray-200 to-gray-300 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-sm px-4">

        <!-- Logo -->
        <div class="flex flex-col items-center mb-6">
            <div class="bg-blue-600 p-4 rounded-xl shadow-md">
                <!-- Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M12 3l9 4.5-9 4.5L3 7.5 12 3z" />
                    <path d="M3 7.5v9l9 4.5 9-4.5v-9" />
                </svg>
            </div>
            <h1 class="text-blue-700 font-bold text-xl mt-3">LWD</h1>
            <p class="text-xs tracking-widest text-gray-500">LEARNING WITH DOING</p>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-t-4 border-blue-600">

            <h2 class="text-center text-lg font-semibold text-gray-800">Login Anggota</h2>
            <p class="text-center text-sm text-gray-500 mt-1 mb-6">
                Silakan masuk untuk melanjutkan aktivitas belajar Anda.
            </p>

            <form class="space-y-4" action="{{ route('login') }}" method="post">
                @csrf

                @if(session('error'))
                <div class="p-3 text-sm text-red-600 bg-red-100 rounded-lg text-center">
                    {{ session('error') }}
                </div>
                @endif

                <div>
                    <label class="text-xs text-blue-600 font-semibold">NIM</label>
                    <div class="relative mt-1">
                        <input
                            type="text"
                            name="nim"
                            value="{{ old('nim') }}"
                            placeholder="Masukkan Nomor Induk Mahasiswa"
                            class="w-full pl-10 pr-3 py-2 rounded-lg bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <span class="absolute left-3 top-2.5 text-gray-400">👤</span>
                    </div>
                    @error('nim')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-xs text-blue-600 font-semibold">Password</label>
                    <div class="relative mt-1">
                        <input
                            type="password"
                            name="password"
                            placeholder="********"
                            class="w-full pl-10 pr-10 py-2 rounded-lg bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <span class="absolute left-3 top-2.5 text-gray-400">🔒</span>
                    </div>
                    @error('password')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold shadow-md transition">
                    MASUK
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center text-xs text-gray-500 mt-6 italic">
            "Pendidikan bukan persiapan untuk hidup; pendidikan adalah hidup itu sendiri."
        </p>

    </div>

</body>

</html>