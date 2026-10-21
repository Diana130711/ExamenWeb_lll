<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once(appRoot . '/views/includes/enca.php'); ?>
	<title><?php echo siteName; ?></title>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
	<header>
		<?php require_once(appRoot . '/views/includes/menu.php'); ?>
	</header>

	<main class="my-6">
		<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">

			<div class="flex items-center justify-between mb-4">
				<h2 class="text-xl font-bold text-slate-800">
					<?php echo $data['carpetaActual']->nombre === 'raiz' ? 'Mi Cajon de Archivos' : htmlspecialchars($data['carpetaActual']->nombre); ?>
				</h2>

				<?php if (!empty($data['carpetaActual']->carpeta_padre_id)): ?>
					<a href="<?php echo urlRoot; ?>/carpetas/index/<?php echo $data['carpetaActual']->carpeta_padre_id; ?>" class="text-sm text-indigo-600 hover:underline">⬅ Volver a la carpeta anterior</a>
				<?php endif; ?>
			</div>

			<div class="flex flex-wrap gap-3 mb-6">
				<form action="<?php echo urlRoot; ?>/carpetas/crear/<?php echo $data['carpetaActual']->id; ?>" method="post" class="flex gap-2">
					<input type="text" name="txtNombre" placeholder="Nombre de nueva carpeta" class="border border-slate-300 rounded-lg px-3 py-2 text-sm" required>
					<button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg">Crear carpeta</button>
				</form>

				<form action="<?php echo urlRoot; ?>/carpetas/compartir/<?php echo $data['carpetaActual']->id; ?>" method="post" class="flex gap-2">
					<select name="txtUsuarioDestino" class="border border-slate-300 rounded-lg px-3 py-2 text-sm" required>
						<option value="">-- Compartir con... --</option>
						<?php foreach ($data['usuarios'] as $u): ?>
							<option value="<?php echo $u->id; ?>"><?php echo htmlspecialchars($u->nombre); ?></option>
						<?php endforeach; ?>
					</select>
					<button type="submit" class="bg-slate-700 hover:bg-slate-800 text-white text-sm font-medium px-4 py-2 rounded-lg">Compartir esta carpeta</button>
				</form>

				<a href="<?php echo urlRoot; ?>/archivos/agregar/<?php echo $data['carpetaActual']->id; ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg">Agregar archivo</a>
			</div>

			<table class="w-full text-sm border-collapse">
				<thead>
					<tr class="bg-slate-100 text-slate-600 text-left">
						<th class="px-3 py-2">Nombre</th>
						<th class="px-3 py-2">Tipo</th>
						<th class="px-3 py-2">Comentario</th>
						<th class="px-3 py-2">Peso (MB)</th>
						<th class="px-3 py-2">Fecha</th>
						<th class="px-3 py-2">Compartida</th>
						<th class="px-3 py-2">Compartida conmigo</th>
						<th class="px-3 py-2">Acción</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($data['subcarpetas'] as $sub): ?>
						<tr class="border-b border-slate-100">
							<td class="px-3 py-2">
								<a href="<?php echo urlRoot; ?>/carpetas/index/<?php echo $sub->id; ?>" class="text-indigo-600 hover:underline font-medium">📁 <?php echo htmlspecialchars($sub->nombre); ?></a>
							</td>
							<td class="px-3 py-2">Directorio</td>
							<td class="px-3 py-2">-</td>
							<td class="px-3 py-2">-</td>
							<td class="px-3 py-2"><?php echo date('d/m/Y', strtotime($sub->fecha_creacion)); ?></td>
                            <td class="px-3 py-2"><?php echo isset($sub->compartida) && $sub->compartida ? 'Si' : 'No'; ?></td>							
                            <td class="px-3 py-2"><?php echo isset($sub->compartidaConmigo) && $sub->compartidaConmigo ? 'Si' : 'No'; ?></td>
							<td class="px-3 py-2">
								<form action="<?php echo urlRoot; ?>/carpetas/borrar/<?php echo $sub->id; ?>" method="post">
									<button type="submit" class="text-rose-600 hover:underline text-xs">Borrar</button>
								</form>
							</td>
						</tr>
					<?php endforeach; ?>

					<?php foreach ($data['archivos'] as $arch): ?>
						<tr class="border-b border-slate-100">
							<td class="px-3 py-2">
								<a href="<?php echo urlRoot; ?>/archivos/abrir/<?php echo $arch->id; ?>" class="text-slate-700 hover:underline">📄 <?php echo htmlspecialchars($arch->nombre); ?></a>
							</td>
							<td class="px-3 py-2"><?php echo htmlspecialchars($arch->tipo); ?></td>
							<td class="px-3 py-2"><?php echo htmlspecialchars($arch->comentario ?? ''); ?></td>
							<td class="px-3 py-2"><?php echo round($arch->peso_bytes / 1048576, 2); ?></td>
							<td class="px-3 py-2"><?php echo date('d/m/Y', strtotime($arch->fecha_subida)); ?></td>
							<td class="px-3 py-2">-</td>
							<td class="px-3 py-2">-</td>
							<td class="px-3 py-2">
								<form action="<?php echo urlRoot; ?>/archivos/borrar/<?php echo $arch->id; ?>" method="post">
									<button type="submit" class="text-rose-600 hover:underline text-xs">Borrar</button>
								</form>
							</td>
						</tr>
					<?php endforeach; ?>

					<?php if (empty($data['subcarpetas']) && empty($data['archivos'])): ?>
						<tr>
							<td colspan="8" class="px-3 py-6 text-center text-slate-400">Esta carpeta está vacía</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</main>

	<?php require_once(appRoot . '/views/includes/pie.php'); ?>
</div>
</body>
</html>