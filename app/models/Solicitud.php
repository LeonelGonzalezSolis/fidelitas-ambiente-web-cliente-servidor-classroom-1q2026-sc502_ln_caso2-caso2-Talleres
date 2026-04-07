<?php
class Solicitud
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Buscar solicitud por ID
    public function findById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM solicitudes WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Aprobar solicitud
    
    public function aprobar($id)
    {
    $stmt = $this->conn->prepare("UPDATE solicitudes SET estado='aprobada' WHERE id=? AND estado='pendiente'");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
    }

    // Rechazar solicitud
    public function rechazar($id)
    {
    $stmt = $this->conn->prepare("UPDATE solicitudes SET estado='rechazada' WHERE id=? AND estado='pendiente'");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
    }


    // Lista solicitudes pendientes
     public function pendientes()
    {
    $sql = "SELECT 
                s.id,
                s.taller_id,
                s.usuario_id,
                s.fecha_solicitud,
                s.estado
            FROM solicitudes s
            WHERE s.estado = 'pendiente'";
    
    $result = $this->conn->query($sql);
    $solicitudes = [];
    while ($row = $result->fetch_assoc()) {
        $solicitudes[] = $row;
    }
    return $solicitudes;
    }


    //Nueva solicitud
    public function crear($usuarioId, $tallerId)
    {
        // Validar duplicado
        $stmt = $this->conn->prepare("SELECT * FROM solicitudes WHERE usuario_id=? AND taller_id=? AND estado IN ('pendiente')");
        $stmt->bind_param("ii", $usuarioId, $tallerId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->fetch_assoc()) {
            return ["success"=>false,"message"=>"Ya tienes una solicitud activa para este taller."];
        }

        // Validar cupo
        $stmt = $this->conn->prepare("SELECT cupo_disponible FROM talleres WHERE id=?");
        $stmt->bind_param("i", $tallerId);
        $stmt->execute();
        $result = $stmt->get_result();
        $taller = $result->fetch_assoc();
        if (!$taller || $taller['cupo_disponible'] <= 0) {
            return ["success"=>false,"message"=>"No hay cupos disponibles."];
        }

        // Insertar solicitud
        $stmt = $this->conn->prepare("INSERT INTO solicitudes (usuario_id, taller_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $usuarioId, $tallerId);
        $stmt->execute();

        return ["success"=>true,"message"=>"Solicitud enviada correctamente."];
    }
}
