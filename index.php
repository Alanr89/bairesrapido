<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema Baires Rápido</title>
    <style>
        :root { --primary: #003366; --bg: #f4f4f4; --accent: #00a8e8; --sidebar-w: 260px; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: sans-serif; }
        body { display: flex; height: 100vh; background: var(--bg); overflow: hidden; }
        .sidebar { width: var(--sidebar-w); background: var(--primary); color: white; display: flex; flex-direction: column; padding: 20px 0; box-shadow: 4px 0 10px rgba(0,0,0,0.1); }
        .logo-container { padding: 10px 20px 40px; text-align: center; }
        .logo { max-width: 100%; height: auto; border-radius: 8px; background: white; padding: 5px; }
        .nav-menu { display: flex; flex-direction: column; gap: 5px; }
        .nav-btn { background: transparent; border: none; color: rgba(255,255,255,0.8); padding: 18px 25px; text-align: left; font-size: 16px; cursor: pointer; transition: all 0.3s; border-left: 5px solid transparent; }
        .nav-btn:hover { background: rgba(255,255,255,0.1); color: white; padding-left: 30px; }
        .nav-btn.active { background: rgba(255,255,255,0.15); color: white; border-left: 5px solid var(--accent); }
        .main-content { flex: 1; padding: 30px; overflow-y: auto; }
        .display-card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); min-height: 80vh; }
        h1 { color: var(--primary); margin-bottom: 10px; }
        .subtitle { color: #666; margin-bottom: 30px; }
        hr { border: 0; border-top: 1px solid #eee; margin: 20px 0; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="logo-container"><img src="assets/LogoHeader.jpg" alt="Logo" class="logo"></div>
        <nav class="nav-menu">
            <button class="nav-btn active" onclick="showModule('inicio', this)">Inicio</button>
            <button class="nav-btn" onclick="showModule('casos', this)">Casos</button>
            <button class="nav-btn" onclick="showModule('coordinacion', this)">Coordinación</button>
        </nav>
    </aside>
    <main class="main-content">
        <div id="display-area" class="display-card">
            <h1>Bienvenido al Sistema de Operaciones</h1>
            <p class="subtitle">Gestión y Coordinación Baires Rápido</p>
            <hr>
            <p>Seleccione un módulo para comenzar.</p>
        </div>
    </main>
    <script>
        function showModule(m, e) {
            const d = document.getElementById('display-area');
            document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
            e.classList.add('active');
            if(m === 'inicio') {
                d.innerHTML = '<h1>Bienvenido</h1><p class=\"subtitle\">Gestión y Coordinación</p><hr><p>Seleccione un módulo.</p>';
            } else if(m === 'casos') {
                d.innerHTML = '<h1>Módulo de Casos</h1><p class=\"subtitle\">Seguimiento de Operaciones</p><hr><p>Panel de gestión de casos.</p>';
            } else if(m === 'coordinacion') {
                d.innerHTML = '<h1>Módulo de Coordinación</h1><p class=\"subtitle\">Asignación de Recursos</p><hr><p>Panel de coordinación.</p>';
            }
        }
    </script>
</body>
</html>