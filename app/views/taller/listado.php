<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado Talleres</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="public/js/jquery-4.0.0.min.js"></script>
</head>
<body class="container mt-5">

    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php?page=talleres">Talleres</a>
            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                <a class="nav-link" href="index.php?page=admin">Gestionar Solicitudes</a>
            <?php endif; ?>
            <div class="ms-auto">
                <span class="me-3"><?= htmlspecialchars($_SESSION['nombre'] ?? $_SESSION['user'] ?? 'Usuario') ?></span>
                <button id="btnLogout" class="btn btn-outline-primary btn-sm">Cerrar sesión</button>
            </div>
        </div>
    </nav>

    <main>
        <h3 class="mb-3">Talleres disponibles</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Cupos disponibles</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody id="tablaTalleres"></tbody>
        </table>
    </main>

    <script>
    // Cargar talleres
    $.getJSON("index.php?option=talleres_json", function(data) {
        let html = "";
        data.forEach(t => {
            html += `
                <tr>
                    <td>${t.nombre}</td>
                    <td>${t.descripcion}</td>
                    <td>${t.cupo_disponible}</td>
                    <td><button class="btn btn-primary btn-sm" onclick="solicitar(${t.id})">Inscribirse</button></td>
                </tr>`;
        });
        $("#tablaTalleres").html(html);
    });

    // Solicitar inscripción
    function solicitar(id) {
        $.post("index.php", { option: "solicitar", taller_id: id }, function(resp) {
            alert(resp.message);
            location.reload(); 
        }, "json");
    }

    // Logout
    $("#btnLogout").click(function(){
        $.post("index.php", { option: "logout" }, function(){
            window.location.href = "index.php?page=login";
        });
    });
    </script>
</body>
</html>
