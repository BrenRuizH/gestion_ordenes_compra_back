<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    require_once '../conexion.php';
    include '../config.php';
    
    $id = $_POST['id'];

    try {
        $mysql->begin_transaction();

        // Verificar si la orden ya tiene un número de remisión
        $stmtVerificar = $mysql->prepare("SELECT remision FROM ordenes_compra WHERE id = ?;");
        $stmtVerificar->bind_param("i", $id);
        $stmtVerificar->execute();
        $resultado = $stmtVerificar->get_result();
        $orden = $resultado->fetch_assoc();

        if ($orden && $orden['remision']) {
            // La orden ya tiene un número de remisión
            echo json_encode(["status"=>"success","message" => "La orden ya cuenta con un número de remisión"]);
        } else {
            // Obtener el último número de remisión y calcular el siguiente
            $stmtUltimaRemision = $mysql->prepare("SELECT MAX(remision) as ultimoNumero FROM ordenes_compra;");
            $stmtUltimaRemision->execute();
            $resultadoRemision = $stmtUltimaRemision->get_result();
            $ultimaRemision = $resultadoRemision->fetch_assoc();
            $nuevaRemision = $ultimaRemision['ultimoNumero'] + 1;

            // Actualizar la orden con el nuevo número de remisión
            $stmtActualizar = $mysql->prepare("UPDATE ordenes_compra SET remision = ? WHERE id = ?;");
            $stmtActualizar->bind_param("ii", $nuevaRemision, $id);
            if (!$stmtActualizar->execute()) {
                throw new Exception("Error al asignar el número de remisión a la orden: ".$stmtActualizar->error);
            }

            $mysql->commit();

            echo json_encode(["status"=>"success","message" => "Número de remisión asignado a la orden exitosamente", "remision" => $nuevaRemision]);
        }
        
    } catch (Exception $e) {
        $mysql->rollback();

        echo json_encode(["status"=>"error","message" => $e->getMessage()]);
    }
}
?>
