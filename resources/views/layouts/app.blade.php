<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fraud Analysis</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<nav class="bg-blue-600 text-white p-4 shadow flex justify-between items-center">
    <div class="font-bold text-lg">
        Fraud Analysis
    </div>
    <div class="space-x-4">
        <a href="{{ route('scans.index') }}" class="hover:underline">All Scans</a>
        <a href="{{ route('scans.latest') }}" class="hover:underline">Latest Scan</a>
    </div>
</nav>

<main class="container mx-auto p-6">
    @yield('content')
</main>

</body>
</html>
