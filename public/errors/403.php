<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="refresh" content="5;url=/php_01/">
	<title>403 - Acceso Denegado</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
		  integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
		  crossorigin="anonymous"
		  referrerpolicy="no-referrer" />
</head>
<body class="bg-slate-100 h-screen flex items-center justify-center p-4">
	<div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8 text-center border border-slate-200">
		<div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
			<i class="fa-solid fa-lock"></i>
		</div>
		<h1 class="text-4xl font-extrabold text-slate-800 mb-2">403</h1>
		<h2 class="text-xl font-bold text-slate-700 mb-3">Acceso Restringido</h2>
		<p class="text-slate-500 text-sm mb-6">
			No tienes los permisos necesarios para acceder a esta sección.
		</p>

		<div class="bg-slate-50 rounded-lg p-3 border border-slate-200 mb-6">
			<p class="text-xs text-slate-600">
				Serás redirigido automáticamente al inicio en <span id="timer" class="font-bold text-indigo-600">5</span> segundos.
			</p>
		</div>

		<a href="/php_01/" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg text-sm transition-colors shadow-sm">
			<i class="fa-solid fa-house"></i>
			Volver al Inicio Ahora
		</a>
	</div>

	<script>
		let seconds = 5;
		const timerElement = document.getElementById('timer');
		setInterval(() => {
			if (seconds > 1) {
				seconds--;
				timerElement.textContent = seconds;
			}
		}, 1000);
	</script>
</body>
</html>