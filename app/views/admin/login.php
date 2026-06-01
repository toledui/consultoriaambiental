<div class="w-full max-w-md px-8">
  <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
    <div class="text-center mb-8">
      <img alt="Consultoría Ambiental" class="h-16 mx-auto mb-4" src="<?= BASE_URL ?>/images/consultoria-ambiental-logo.png"/>
      <h1 class="text-2xl font-bold text-ca-navy">Iniciar Sesión</h1>
      <p class="text-sm text-gray-500 mt-1">Panel de administración</p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm">
        <i class="fas fa-exclamation-circle mr-1"></i> <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/admin/login" class="space-y-5">
      <div>
        <label class="block text-sm font-medium text-ca-dark-gray mb-1.5" for="username">Usuario</label>
        <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green outline-none transition" id="username" name="username" type="text" required placeholder="Ingresa tu usuario"/>
      </div>
      
      <div>
        <label class="block text-sm font-medium text-ca-dark-gray mb-1.5" for="password">Contraseña</label>
        <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green outline-none transition" id="password" name="password" type="password" required placeholder="Ingresa tu contraseña"/>
      </div>
      
      <button class="w-full bg-ca-green hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors shadow-md" type="submit">
        <i class="fas fa-sign-in-alt mr-2"></i> Entrar
      </button>
    </form>
    
    <div class="mt-6 text-center">
      <a href="<?= BASE_URL ?>" class="text-sm text-ca-green hover:text-ca-navy transition-colors">
        <i class="fas fa-arrow-left mr-1"></i> Volver al sitio
      </a>
    </div>
  </div>
</div>
