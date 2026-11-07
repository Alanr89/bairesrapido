<?php
session_start();
if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit(); }
$rol = $_SESSION['rol'] ?? 'operador';
$nombre = $_SESSION['nombre'] ?? 'Usuario';
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Sistema Baires Rápido</title><style>
:root{--primary:#003366;--bg:#f4f4f4;--accent:#00a8e8;--sidebar-w:260px}
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif}
body{display:flex;height:100vh;background:var(--bg);overflow:hidden}
.sidebar{width:var(--sidebar-w);background:var(--primary);color:white;display:flex;flex-direction:column;padding:20px 0;box-shadow:4px 0 10px rgba(0,0,0,0.1)}
.logo-container{padding:10px 20px 40px;text-align:center}
.logo{max-width:100%;height:auto;border-radius:8px;background:white;padding:5px}
.nav-menu{display:flex;flex-direction:column;gap:5px}
.nav-btn{background:transparent;border:none;color:rgba(255,255,255,0.8);padding:18px 25px;text-align:left;font-size:16px;cursor:pointer;transition:all 0.3s;border-left:5px solid transparent}
.nav-btn:hover{background:rgba(255,255,255,0.1);color:white;padding-left:30px}
.nav-btn.active{background:rgba(255,255,255,0.15);color:white;border-left:5px solid var(--accent)}
.main-content{flex:1;padding:30px;overflow-y:auto}
.display-card{background:white;padding:40px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.08);min-height:80vh}
h1{color:var(--primary);margin-bottom:10px}
.subtitle{color:#666;margin-bottom:30px}
hr{border:0;border-top:1px solid #eee;margin:20px 0}
.modulo-grid{display:flex;flex-direction:column;gap:12px;align-items:flex-start;margin-top:30px}
.modulo-btn{padding:22px 40px;border:none;border-radius:10px;background:var(--primary);color:white;font-size:18px;font-weight:600;cursor:pointer;transition:all 0.3s;box-shadow:0 4px 12px rgba(0,0,0,0.15);width:240px;text-align:left}
.modulo-btn:hover{background:var(--accent);transform:translateY(-3px);box-shadow:0 8px 16px rgba(0,0,0,0.2)}

/* Vista Usuarios */
.usuarios-toolbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:15px}
.usuarios-actions-left{display:flex;align-items:center;gap:10px}
.usuarios-actions-right{display:flex;align-items:center;gap:10px}
.btn-agregar{background:var(--accent);color:white;border:none;padding:10px 20px;border-radius:6px;font-weight:600;cursor:pointer;transition:all 0.3s;display:flex;align-items:center;gap:6px}
.btn-agregar:hover{background:#0088c8;transform:translateY(-1px)}
.btn-eliminar{background:#dc3545;color:white;border:none;padding:10px 20px;border-radius:6px;font-weight:600;cursor:pointer;transition:all 0.3s;display:flex;align-items:center;gap:6px}
.btn-eliminar:hover{background:#c82333;transform:translateY(-1px)}
.btn-eliminar:disabled{background:#999;cursor:not-allowed;transform:none}
.btn-buscar{background:var(--primary);color:white;border:none;padding:10px 16px;border-radius:6px;font-weight:600;cursor:pointer;transition:all 0.3s}
.btn-buscar:hover{background:#002244}
.buscar-wrapper{position:relative;display:flex;align-items:center}
.input-buscar{padding:10px 14px;border:1px solid #ddd;border-radius:6px;font-size:14px;width:260px;transition:border-color 0.3s}
.input-buscar:focus{outline:none;border-color:var(--accent)}
.autocomplete-list{position:absolute;top:100%;left:0;right:0;background:white;border:1px solid #ddd;border-top:none;border-radius:0 0 6px 6px;max-height:200px;overflow-y:auto;z-index:100;box-shadow:0 4px 12px rgba(0,0,0,0.15);display:none}
.autocomplete-item{padding:10px 14px;cursor:pointer;border-bottom:1px solid #eee;font-size:14px;transition:background 0.2s}
.autocomplete-item:hover{background:#f0f7ff;color:var(--primary)}
.autocomplete-item:last-child{border-bottom:none}
.tabla-scroll-container{max-height:520px;overflow-y:auto;border:1px solid #e0e0e0;border-radius:8px}
.tabla-scroll-container table.usuarios-table{border-radius:0;border:none}
.tabla-scroll-container table.usuarios-table th{position:sticky;top:0;background:#f8f9fa;z-index:2}
table.usuarios-table{width:100%;border-collapse:collapse;background:white;border-radius:8px;overflow:hidden;border:1px solid #e0e0e0}
table.usuarios-table th{background:#f8f9fa;color:#333;font-weight:600;padding:14px 12px;text-align:left;font-size:14px;border-bottom:2px solid #e0e0e0;white-space:nowrap}
table.usuarios-table td{padding:12px;border-bottom:1px solid #e8e8e8;font-size:14px;color:#555;vertical-align:middle}
table.usuarios-table tr:last-child td{border-bottom:none}
table.usuarios-table tr:hover{background:#f5f9ff}
table.usuarios-table tr.seleccionada{background:#e3f2fd!important}
.cel-check{width:50px;text-align:center}
.cel-check input{cursor:pointer;transform:scale(1.2)}
.cel-acciones{width:100px;text-align:center}
.btn-editar-fila{background:var(--primary);color:white;border:none;padding:6px 14px;border-radius:4px;font-size:13px;font-weight:600;cursor:pointer;transition:all 0.2s;margin-top:5px;display:none}
.btn-editar-fila.visible{display:block}
.btn-editar-fila:hover{background:#002244}
.btn-atras{background:#6c757d;color:white;border:none;padding:10px 20px;border-radius:6px;font-weight:600;cursor:pointer;transition:all 0.3s;display:flex;align-items:center;gap:6px;margin-top:20px}
.btn-atras:hover{background:#5a6268}
.atras-wrapper{text-align:right}

/* Modal */
.modal-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);display:none;justify-content:center;align-items:center;z-index:1000}
.modal{background:white;border-radius:10px;padding:30px;max-width:450px;width:90%;box-shadow:0 10px 40px rgba(0,0,0,0.3)}
.modal h2{margin-top:0;color:var(--primary);font-size:18px}
.modal p{font-size:15px;color:#333;margin:15px 0}
.modal .usuario-nombre{font-weight:700;color:#dc3545}
.modal-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:25px}
.btn-cancelar{background:#6c757d;color:white;border:none;padding:10px 20px;border-radius:6px;font-weight:600;cursor:pointer;transition:background 0.2s}
.btn-cancelar:hover{background:#5a6268}
.btn-confirmar{background:var(--primary);color:white;border:none;padding:10px 20px;border-radius:6px;font-weight:600;cursor:pointer;transition:background 0.2s}
.btn-confirmar:hover{background:#002244}

/* Mensajes */
.msg-vacio{text-align:center;color:#888;padding:40px;font-size:16px}
.msg-exito{background:#d4edda;color:#155724;padding:12px 16px;border-radius:6px;margin-bottom:20px;border:1px solid #c3e6cb;font-size:14px}
.msg-error{background:#f8d7da;color:#721c24;padding:12px 16px;border-radius:6px;margin-bottom:20px;border:1px solid #f5c6cb;font-size:14px}
</style></head><body>
<aside class="sidebar"><div class="logo-container"><img src="assets/LogoHeader.jpg" alt="Logo" class="logo"></div><nav class="nav-menu">
<?php if($rol==='admin'): ?><button class="nav-btn active" onclick="showModule('inicio',this)">Inicio</button><?php endif; ?>
<button class="nav-btn <?= $rol==='operador'?'active':'' ?>" onclick="showModule('casos',this)">Casos</button>
<button class="nav-btn" onclick="showModule('coordinacion',this)">Coordinación</button>
<button class="nav-btn" onclick="window.location.href='login.php?logout=1'">Salir</button>
</nav></aside>
<main class="main-content"><div id="display-area" class="display-card"><h1>Bienvenido al Sistema de Operaciones</h1><p class="subtitle">Gestión y Coordinación</p><hr><p>Seleccione un módulo para comenzar.</p><div class="modulo-grid"><button class="modulo-btn" onclick="cargarVistaUsuarios()">Usuarios</button><button class="modulo-btn" onclick="alert('Módulo Flota')">Flota</button><button class="modulo-btn" onclick="alert('Módulo Compañías')">Compañías</button></div></div></main>
<script>
function showModule(m,e){const d=document.getElementById('display-area');document.querySelectorAll('.nav-btn').forEach(b=>b.classList.remove('active'));if(e)e.classList.add('active');if(m==='inicio'){d.innerHTML='<h1>Bienvenido</h1><p class="subtitle">Gestión y Coordinación</p><hr><p>Seleccione un módulo para comenzar.</p><div class="modulo-grid"><button class="modulo-btn" onclick="cargarVistaUsuarios()">Usuarios</button><button class="modulo-btn" onclick="alert(\'Módulo Flota\')">Flota</button><button class="modulo-btn" onclick="alert(\'Módulo Compañías\')">Compañías</button></div>'}else if(m==='casos'){d.innerHTML='<h1>Módulo de Casos</h1><p class="subtitle">Seguimiento de Operaciones</p><hr><p>Panel de gestión de casos.</p>'}else if(m==='coordinacion'){d.innerHTML='<h1>Módulo de Coordinación</h1><p class="subtitle">Asignación de Recursos</p><hr><p>Panel de coordinación.</p>'}}
document.addEventListener('DOMContentLoaded',function(){const rol='<?= $rol ?>';if(rol==='admin'){showModule('inicio',document.querySelector('.nav-btn.active'))}else{showModule('casos',document.querySelector('.nav-btn.active'))}});
let usuariosSeleccionados=new Set();let todosUsuarios=[];

function cargarVistaUsuarios(){const d=document.getElementById('display-area');d.innerHTML='<h1>Gestión de Usuarios</h1><p class="subtitle">Administración de usuarios del sistema</p><hr><div class="usuarios-toolbar"><div class="usuarios-actions-left"><button class="btn-agregar" onclick="abrirAgregarUsuario()">+ Agregar</button></div><div class="usuarios-actions-right"><button class="btn-eliminar" id="btnEliminar" disabled onclick="eliminarSeleccionados()">- Eliminar</button><div class="buscar-wrapper"><input type="text" class="input-buscar" id="inputBuscar" placeholder="Buscar usuario..."><button class="btn-buscar" onclick="buscarUsuarios(document.getElementById(\'inputBuscar\').value)">🔍</button><div class="autocomplete-list" id="autocompleteList"></div></div></div></div><div id="msgArea"></div><div class="tabla-scroll-container"><table class="usuarios-table" id="tablaUsuarios"><thead><tr><th class="cel-check"><input type="checkbox" id="chkTodos" onclick="toggleSeleccionarTodos()"></th><th>Nombre de usuario</th><th>Nombre y apellido</th><th>Tipo</th><th class="cel-acciones">Acciones</th></tr></thead><tbody id="cuerpoTablaUsuarios"></tbody></table></div><div id="mensajeVacio" class="msg-vacio" style="display:none;">No se encontraron usuarios.</div><div class="atras-wrapper"><button class="btn-atras" onclick="cargarVistaInicio()">← Atrás</button></div><div class="modal-overlay" id="modalEliminar"><div class="modal"><h2>Confirmar eliminación</h2><p>¿Desea eliminar al usuario: <span class="usuario-nombre" id="modalUsuarioNombre"></span>?</p><div class="modal-actions"><button class="btn-cancelar" onclick="cerrarModalEliminar()">Cancelar</button><button class="btn-confirmar" onclick="confirmarEliminar()">Aceptar</button></div></div></div><div class="modal-overlay" id="overlayModalUsuario"><div class="modal" style="max-width:500px"><h2 id="modalUsuarioTitulo">Agregar Usuario</h2><form id="formUsuario" onsubmit="guardarUsuario(event)"><input type="hidden" id="usuarioId" value=""><div style="margin-bottom:15px"><label style="display:block;margin-bottom:6px;font-weight:600;color:#333">Nombre y apellido</label><input type="text" id="modalNombre" placeholder="Nombre y apellido" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;font-size:14px;box-sizing:border-box"></div><div style="margin-bottom:15px"><label style="display:block;margin-bottom:6px;font-weight:600;color:#333">Nombre de usuario</label><input type="text" id="modalUsuario" placeholder="usuario" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;font-size:14px;box-sizing:border-box"></div><div style="margin-bottom:15px"><label style="display:block;margin-bottom:6px;font-weight:600;color:#333">Tipo de usuario</label><select id="modalRol" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;font-size:14px;box-sizing:border-box"><option value="operador">Operador</option><option value="admin">Administrador</option></select></div><div style="margin-bottom:20px"><label style="display:block;margin-bottom:6px;font-weight:600;color:#333">Contraseña</label><input type="password" id="modalPassword" placeholder="Contraseña (mín. 4 caracteres)" required minlength="4" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;font-size:14px;box-sizing:border-box"></div><div class="modal-actions" style="justify-content:flex-end"><button type="button" class="btn-cancelar" onclick="cerrarModalUsuario()">Cancelar</button><button type="submit" class="btn-confirmar" style="background:var(--primary)">Guardar</button></div></form></div></div>';cargarUsuariosDesdeAPI('')}
async function cargarUsuariosDesdeAPI(search=''){const url=search?'usuarios_api.php?search='+encodeURIComponent(search):'usuarios_api.php';try{const resp=await fetch(url);todosUsuarios=await resp.json();renderizarTablaUsuarios(todosUsuarios);usuariosSeleccionados=new Set();actualizarEstadoBotones()}catch(err){mostrarMensaje('Error al cargar usuarios','error')}}
function buscarUsuarios(termino){const lista=document.getElementById('autocompleteList');const input=document.getElementById('inputBuscar');if(termino.length<2){lista.style.display='none';lista.innerHTML='';if(termino.length===0){cargarUsuariosDesdeAPI('')}return}const coincidencias=todosUsuarios.filter(function(u){return u.usuario.toLowerCase().includes(termino.toLowerCase())||(u.nombre||'').toLowerCase().includes(termino.toLowerCase())});lista.innerHTML='';coincidencias.slice(0,8).forEach(function(u){const item=document.createElement('div');item.className='autocomplete-item';item.textContent=u.usuario+' - '+(u.nombre||'')+' ('+u.rol+')';item.onclick=function(){input.value=u.usuario;lista.style.display='none';cargarUsuariosDesdeAPI(input.value)};lista.appendChild(item)});lista.style.display=coincidencias.length>0?'block':'none';if(termino.length>=2){cargarUsuariosDesdeAPI(termino)}}
function renderizarTablaUsuarios(usuarios){const tbody=document.getElementById('cuerpoTablaUsuarios');const msgVacio=document.getElementById('mensajeVacio');tbody.innerHTML='';if(!usuarios||usuarios.length===0){msgVacio.style.display='block';return}msgVacio.style.display='none';usuarios.forEach(function(u){const estaSeleccionado=usuariosSeleccionados.has(u.id);const rolLabel=u.rol==='admin'?'Administrador':'Operador';const fila=document.createElement('tr');fila.className=estaSeleccionado?'seleccionada':'';fila.innerHTML='<td class="cel-check"><input type="checkbox" value="'+u.id+'" '+(estaSeleccionado?'checked':'')+' onchange="toggleSeleccion('+u.id+',this)"></td><td><strong>'+escapeHtml(u.usuario)+'</strong></td><td>'+escapeHtml(u.nombre||'-')+'</td><td><span style="background:'+(u.rol==='admin'?'#e3f2fd':'#e8f5e9')+';color:'+(u.rol==='admin'?'#003366':'#2e7d32')+';padding:4px 10px;border-radius:12px;font-size:12px;font-weight:600">'+rolLabel+'</span></td><td class="cel-acciones"><button class="btn-editar-fila" id="btnEditar'+u.id+'" onclick="editarUsuario('+u.id+')">Editar</button></td>';tbody.appendChild(fila)})}function toggleSeleccion(id,checkbox){if(checkbox.checked){usuariosSeleccionados.add(id)}else{usuariosSeleccionados.delete(id)}const fila=checkbox.closest('tr');if(checkbox.checked){fila.classList.add('seleccionada')}else{fila.classList.remove('seleccionada')}document.querySelectorAll('.btn-editar-fila').forEach(function(btn){btn.classList.remove('visible')});if(usuariosSeleccionados.size===1){const btn=document.getElementById('btnEditar'+id);if(btn)btn.classList.add('visible')}actualizarEstadoBotones()}

function toggleSeleccionarTodos(){const chk=document.getElementById('chkTodos');const checks=document.querySelectorAll('#cuerpoTablaUsuarios input[type="checkbox"]');checks.forEach(function(c){c.checked=chk.checked;const id=parseInt(c.value);if(chk.checked){usuariosSeleccionados.add(id);c.closest('tr').classList.add('seleccionada')}else{usuariosSeleccionados.delete(id);c.closest('tr').classList.remove('seleccionada')}});document.querySelectorAll('.btn-editar-fila').forEach(function(btn){btn.classList.remove('visible')});if(usuariosSeleccionados.size===1){const id=Array.from(usuariosSeleccionados)[0];const btn=document.getElementById('btnEditar'+id);if(btn)btn.classList.add('visible')}actualizarEstadoBotones()}

function actualizarEstadoBotones(){const btnEliminar=document.getElementById('btnEliminar');if(usuariosSeleccionados.size>0){btnEliminar.disabled=false;btnEliminar.textContent='- Eliminar ('+usuariosSeleccionados.size+')'}else{btnEliminar.disabled=true;btnEliminar.textContent='- Eliminar'}}

function editarUsuario(id){const u=todosUsuarios.find(function(x){return String(x.id)===String(id)});if(!u){mostrarMensaje('Usuario no encontrado','error');return}document.getElementById('modalUsuarioTitulo').textContent='Editar Usuario';document.getElementById('usuarioId').value=String(u.id);document.getElementById('modalNombre').value=u.nombre||'';document.getElementById('modalUsuario').value=u.usuario||'';document.getElementById('modalRol').value=u.rol||'operador';document.getElementById('modalPassword').value=u.password||'';document.getElementById('modalPassword').required=false;document.getElementById('modalPassword').placeholder='Contraseña';document.getElementById('modalUsuario').required=false;document.getElementById('modalNombre').required=false;document.getElementById('modalUsuario').readOnly=false;document.getElementById('modalUsuario').placeholder='';document.getElementById('modalUsuario').style.background='';document.getElementById('overlayModalUsuario').style.display='flex'}

function cerrarModalUsuario(){document.getElementById('overlayModalUsuario').style.display='none'}

async function guardarUsuario(event){event.preventDefault();const idRaw=String(document.getElementById('usuarioId').value||'').trim();const idNumerico=parseInt(idRaw);const esNuevo=!idRaw||isNaN(idNumerico);const nombre=document.getElementById('modalNombre').value.trim();const usuario=document.getElementById('modalUsuario').value.trim();const rol=document.getElementById('modalRol').value;const password=document.getElementById('modalPassword').value;if(!nombre||!usuario||!rol){mostrarMensaje('Completa nombre, usuario y tipo','error');return}if(esNuevo&&!password){mostrarMensaje('La contraseña es requerida para nuevos usuarios','error');return}if(password&&password.length<4){mostrarMensaje('La contraseña debe tener al menos 4 caracteres','error');return}const payload={usuario:usuario,nombre:nombre,rol:rol};if(!esNuevo){payload.id=idNumerico}if(password){payload.password=password}try{const method=esNuevo?'POST':'PUT';const resp=await fetch('usuarios_api.php',{method:method,headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)});const data=await resp.json();if(data.error){mostrarMensaje(data.error,'error');return}mostrarMensaje(esNuevo?'Usuario agregado correctamente':'Usuario actualizado correctamente','exito');cerrarModalUsuario();cargarUsuariosDesdeAPI(document.getElementById('inputBuscar').value||'')}catch(err){mostrarMensaje('Error al guardar usuario','error')}}

function abrirAgregarUsuario(){document.getElementById('modalUsuarioTitulo').textContent='Agregar Usuario';document.getElementById('usuarioId').value='';document.getElementById('modalNombre').value='';document.getElementById('modalUsuario').value='';document.getElementById('modalRol').value='operador';document.getElementById('modalPassword').value='';document.getElementById('modalPassword').required=true;document.getElementById('modalPassword').placeholder='Contraseña';document.getElementById('modalUsuario').required=true;document.getElementById('modalNombre').required=true;document.getElementById('modalUsuario').readOnly=false;document.getElementById('modalUsuario').placeholder='usuario';document.getElementById('modalUsuario').style.background='';document.getElementById('modalUsuario').focus();document.getElementById('overlayModalUsuario').style.display='flex'}

function eliminarSeleccionados(){if(usuariosSeleccionados.size===0)return;pendientesEliminar=[];usuariosSeleccionados.forEach(function(id){const u=todosUsuarios.find(function(x){return x.id==id});if(u)pendientesEliminar.push({id:u.id,usuario:u.usuario})});if(pendientesEliminar.length===0)return;if(pendientesEliminar.length===1){document.getElementById('modalUsuarioNombre').textContent=pendientesEliminar[0].usuario;document.getElementById('modalEliminar').style.display='flex'}else{if(!confirm('¿Desea eliminar '+pendientesEliminar.length+' usuario(s) seleccionado(s)?'))return;ejecutarEliminacion()}}

function cerrarModalEliminar(){document.getElementById('modalEliminar').style.display='none';pendientesEliminar=[]}

function confirmarEliminar(){document.getElementById('modalEliminar').style.display='none';ejecutarEliminacion()}

async function ejecutarEliminacion(){if(pendientesEliminar.length===0)return;try{for(var i=0;i<pendientesEliminar.length;i++){var p=pendientesEliminar[i];const resp=await fetch('usuarios_api.php',{method:'DELETE',headers:{'Content-Type':'application/json'},body:JSON.stringify({id:p.id})});const data=await resp.json();if(data.error){mostrarMensaje('Error al eliminar "'+p.usuario+'": '+data.error,'error')}}mostrarMensaje('Se eliminaron '+pendientesEliminar.length+' usuario(s) correctamente','exito')}catch(err){mostrarMensaje('Error al eliminar usuarios','error')}pendientesEliminar=[];usuariosSeleccionados=new Set();document.getElementById('chkTodos').checked=false;cargarUsuariosDesdeAPI(document.getElementById('inputBuscar').value||'')}

function mostrarMensaje(texto,tipo){const area=document.getElementById('msgArea');area.innerHTML='<div class="msg-'+tipo+'">'+escapeHtml(texto)+'</div>';setTimeout(function(){area.innerHTML=''},4000)}

function escapeHtml(texto){if(!texto)return'';const div=document.createElement('div');div.textContent=texto;return div.innerHTML}

document.addEventListener('click',function(e){const lista=document.getElementById('autocompleteList');const input=document.getElementById('inputBuscar');if(lista&&!lista.contains(e.target)&&e.target!==input){lista.style.display='none'}const modalUsuario=document.getElementById('overlayModalUsuario');const modalEliminar=document.getElementById('modalEliminar');if(modalUsuario&&!modalUsuario.contains(e.target)){if(e.target.classList.contains('modal-overlay')){modalUsuario.style.display='none'}}if(modalEliminar&&!modalEliminar.contains(e.target)){if(e.target.classList.contains('modal-overlay')){modalEliminar.style.display='none';pendientesEliminar=[]}}})

function cargarVistaInicio(){const d=document.getElementById('display-area');d.innerHTML='<h1>Bienvenido</h1><p class="subtitle">Gestión y Coordinación</p><hr><p>Seleccione un módulo para comenzar.</p><div class="modulo-grid"><button class="modulo-btn" onclick="cargarVistaUsuarios()">Usuarios</button><button class="modulo-btn" onclick="alert(\'Módulo Flota\')">Flota</button><button class="modulo-btn" onclick="alert(\'Módulo Compañías\')">Compañías</button></div>'}

</script></html>