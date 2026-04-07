<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Solicitudes pendientes</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="public/js/jquery-4.0.0.min.js"></script>
</head>
<body>
    <nav>
        <div>
            <a href="index.php?page=talleres">Talleres</a>
            <a href="index.php?page=admin">Gestionar Solicitudes</a>
        </div>
        <div>
            <span>Admin: <?= htmlspecialchars($_SESSION['nombre'] ?? $_SESSION['user'] ?? 'Administrador') ?></span>
            <button id="btnLogout" class="btn-logout">Cerrar sesión</button>
        </div>
    </nav>
    
    <main>
        <h2>Solicitudes pendientes de aprobación</h2>
        
        <div class="table-container">
            <table id="tabla-solicitudes">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Taller</th>
                        <th>Solicitante</th>
                        <th>Usuario</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="solicitudes-body">
                    <tr>
                        <td colspan="6" class="loader">Cargando solicitudes...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>

    <div id="mensaje"></div>

    <script>
    $(document).ready(function(){
        // Cargar solicitudes pendientes
        $.getJSON("index.php?option=solicitudes_json", function(data) {
            let html = "";
            if (data.length === 0) {
                html = `<tr><td colspan="6">No hay solicitudes pendientes</td></tr>`;
            } else {
                data.forEach(s => {
    html += `
        <tr>
            <td>${s.id}</td>
            <td>${s.taller_id}</td>
            <td>${s.usuario_id}</td>
            <td>${s.fecha_solicitud}</td>
            <td>${s.estado}</td>
            <td>
                <button onclick="aprobar(${s.id})">Aprobar</button>
                <button onclick="rechazar(${s.id})">Rechazar</button>
            </td>
        </tr>`;
});


            }
            $("#solicitudes-body").html(html);
        });

        // Aprobar solicitud
        window.aprobar = function(id) {
            $.post("index.php", { option: "aprobar", id_solicitud: id }, function(resp) {
                alert(resp.message || "Solicitud aprobada");
                location.reload();
            }, "json");
        }

        // Rechazar solicitud
        window.rechazar = function(id) {
            $.post("index.php", { option: "rechazar", id_solicitud: id }, function(resp) {
                alert(resp.message || "Solicitud rechazada");
                location.reload();
            }, "json");
        }

        // Logout
        $("#btnLogout").click(function(){
            $.post("index.php", { option: "logout" }, function(){
                window.location.href = "index.php?page=login";
            });
        });
    });
    </script>
</body>
</html>
