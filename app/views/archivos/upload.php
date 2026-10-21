<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once(appRoot . '/views/includes/enca.php'); ?>
	<title><?php echo siteName; ?> - Subir archivo</title>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 flex flex-col justify-between">
<div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6">
	<header>
		<?php require_once(appRoot . '/views/includes/menu.php'); ?>
	</header>

	<main class="flex justify-center my-8">
		<div class="w-full max-w-lg bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
			<h1 class="text-xl font-bold text-slate-800 mb-1">Agregar archivo</h1>
			<p class="text-sm text-slate-500 mb-6">Tamaño máximo: 20 MB</p>

			<?php if (!empty($data['error'])): ?>
				<div class="mb-4 px-4 py-3 rounded-lg bg-rose-50 text-rose-700 text-sm border border-rose-100">
					<?php echo htmlspecialchars($data['error']); ?>
				</div>
			<?php endif; ?>

			<form action="<?php echo urlRoot; ?>/archivos/upload/<?php echo $data['carpeta_id']; ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
				<div>
					<label class="block text-sm font-semibold text-slate-700 mb-1">Archivo</label>
					<input type="file" name="txtArchi" id="txtArchi" required
						   class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
					<span id="avisoPeso" class="text-xs text-rose-500"></span>
				</div>

				<div>
					<label class="block text-sm font-semibold text-slate-700 mb-1">Comentario (máx. 50 caracteres)</label>
					<input type="text" name="txtComentario" maxlength="50"
						   value="<?php echo htmlspecialchars($data['comentario']); ?>"
						   placeholder="¿De qué trata el archivo?"
						   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
				</div>

				<div class="flex justify-end gap-3 pt-4 border-t">
					<a href="<?php echo urlRoot; ?>/carpetas/index/<?php echo $data['carpeta_id']; ?>" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg font-medium hover:bg-slate-300 transition">Cancelar</a>
					<button type="submit" id="btnSubir" class="px-6 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition">Cargar</button>
				</div>
			</form>
		</div>
	</main>

	<script>
		// Avisa antes de enviar si el archivo pasa de 20 MB
		document.getElementById('txtArchi').addEventListener('change', function () {
			const max = 20 * 1024 * 1024;
			const archivo = this.files[0];
			const aviso = document.getElementById('avisoPeso');
			const boton = document.getElementById('btnSubir');
			if (archivo && archivo.size > max) {
				aviso.textContent = 'El archivo pesa ' + (archivo.size / 1048576).toFixed(2) + ' MB y supera el máximo de 20 MB.';
				boton.disabled = true;
				boton.classList.add('opacity-50');
			} else {
				aviso.textContent = '';
				boton.disabled = false;
				boton.classList.remove('opacity-50');
			}
		});
	</script>

	<?php require_once(appRoot . '/views/includes/pie.php'); ?>
</div>
</body>
</html>