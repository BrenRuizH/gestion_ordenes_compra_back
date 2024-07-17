<?php

if($_SERVER["REQUEST_METHOD"]=="GET")
{
    require_once '../conexion.php';
    include '../config.php';

    $remision_id = $_GET['remision_id'];

    $itemRecords=array();
    $itemRecords["items1"]=array();
    $itemRecords["items2"]=array();

    $query1="SELECT r.id, r.fecha, r.cliente_id, c.codigo AS cliente, r.total_pares, r.precio_final, r.extra, r.descripcion
             FROM remisiones r 
             INNER JOIN clientes c ON r.cliente_id = c.id
             WHERE r.id = $remision_id;";

    $resultado1 = $mysql->query($query1);
    $cliente_id = null;

    if($resultado1->num_rows > 0) {
        while ($item1 = $resultado1->fetch_assoc()) {
            extract($item1);
            $cliente_id = $cliente_id;
            $itemDetails1=array(
                "id" => $id,
                "fecha" => $fecha,
                "cliente_id" => $cliente_id,
                "cliente" => $cliente,
                "total_pares" => $total_pares,
                "precio_final" => $precio_final,
                "extra" => $extra,
                "descripcion" => $descripcion
            );
            array_push($itemRecords["items1"], $itemDetails1);
        }
    }

    if ($cliente_id == 36) {
        $query2="SELECT rpc.id, rpc.oc, rpc.horma_id, h.nombre AS horma rpc.punto, rpc.cantidad
                 FROM remision_puntos_cantidades rpc
                 INNER JOIN
                 WHERE remision_id = $remision_id;";
    } else {
        $query2="SELECT id, folio, oc
                 FROM remision_detalles
                 WHERE remision_id = $remision_id;";
    }

    $resultado2 = $mysql->query($query2);

    if($resultado2->num_rows > 0) {
        while ($item2 = $resultado2->fetch_assoc()) {
            extract($item2);
            $itemDetails2 = $cliente_id == 36
                ? array(
                    "id" => $id,
                    "oc" => $oc,
                    "horma_id" => $horma_id,
                    "horma" => $horma,
                    "punto" => $punto,
                    "cantidad" => $cantidad
                )
                : array(
                    "id" => $id,
                    "folio" => $folio,
                    "oc" => $oc
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
