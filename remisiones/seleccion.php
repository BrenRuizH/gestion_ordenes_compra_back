<?php

if($_SERVER["REQUEST_METHOD"]=="GET")
{
    require_once '../conexion.php';
    include '../config.php';

    $remision_id = $_GET['remision_id'];

    $itemRecords=array();
    $itemRecords["items1"]=array();
    $itemRecords["items2"]=array();

    $query1="SELECT r.id, r.fecha, r.cliente_id, c.codigo AS cliente, r.total_pares, r.precio_final
             FROM remisiones r 
             INNER JOIN clientes c ON r.cliente_id = c.id
             WHERE r.id = $remision_id;";

    $resultado1=$mysql->query($query1);

    if($resultado1->num_rows > 0) {
        while ($item1 = $resultado1->fetch_assoc()) {
            extract($item1);
            $itemDetails1=array(
                "id" => $id,
                "fecha" => $fecha,
                "cliente_id" => $cliente_id,
                "cliente" => $cliente,
                "total_pares" => $total_pares,
                "precio_final" => $precio_final
            );
            array_push($itemRecords["items1"], $itemDetails1);
        }
    }

    $query2="SELECT id, folio
             FROM remision_detalles
             WHERE remision_id = $remision_id;";

    $resultado2=$mysql->query($query2);

    if($resultado2->num_rows > 0) {
        while ($item2 = $resultado2->fetch_assoc()) {
            extract($item2);
            $itemDetails2=array(
                "id" => $id,
                "folio" => $folio
            );
            array_push($itemRecords["items2"], $itemDetails2);
        }
    }

    if(empty($itemRecords["items1"])) {
        http_response_code(404);
        echo json_encode(array("message" => "No se encontraron remisiones."));
    } else {
        http_response_code(200);
        echo json_encode($itemRecords);
    }
}
?>
