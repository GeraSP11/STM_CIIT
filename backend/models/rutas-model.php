<?php
require_once __DIR__ . "/../config/conexion.php";

class RutasModel {

    // ------------------------------------------------------------------
    // CONSULTAR
    // ------------------------------------------------------------------

    public function obtenerLocalidades(): array {
        global $pdo;

        $sql = "SELECT id_localidad,
                       nombre_centro_trabajo,
                       localidad,
                       estado
                FROM   localidades
                ORDER  BY estado, localidad";

        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarRutasConsulta(string $idOrigen, string $idDestino): array {
        global $pdo;

        $condiciones = [];
        $params      = [];

        if ($idOrigen !== "") {
            $condiciones[] = "r.localidad_origen = ?";
            $params[]      = $idOrigen;
        }
        if ($idDestino !== "") {
            $condiciones[] = "r.localidad_destino = ?";
            $params[]      = $idDestino;
        }

        $where = "WHERE " . implode(" AND ", $condiciones);

        $sql = "SELECT r.id_ruta,
                       lo.nombre_centro_trabajo AS nombre_origen_ct,
                       lo.localidad             AS localidad_origen_nombre,
                       lo.estado                AS estado_origen,
                       ld.nombre_centro_trabajo AS nombre_destino_ct,
                       ld.localidad             AS localidad_destino_nombre,
                       ld.estado                AS estado_destino,
                       r.modalidad_ruta,
                       r.tipo_ruta,
                       r.distancia,
                       r.peso_soportado
                FROM   rutas r
                JOIN   localidades lo ON lo.id_localidad = r.localidad_origen
                JOIN   localidades ld ON ld.id_localidad = r.localidad_destino
                $where
                ORDER  BY r.id_ruta";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rutas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rutas as &$ruta) {
            $ruta["nombre_origen"]  = $this->formatearNombre(
                $ruta["nombre_origen_ct"],
                $ruta["localidad_origen_nombre"],
                $ruta["estado_origen"]
            );
            $ruta["nombre_destino"] = $this->formatearNombre(
                $ruta["nombre_destino_ct"],
                $ruta["localidad_destino_nombre"],
                $ruta["estado_destino"]
            );
        }

        return $rutas;
    }

    public function obtenerRutaDetalle(string $idRuta) {
        global $pdo;

        $sql = "SELECT r.id_ruta,
                       lo.nombre_centro_trabajo AS nombre_origen_ct,
                       lo.localidad             AS localidad_origen_nombre,
                       lo.estado                AS estado_origen,
                       ld.nombre_centro_trabajo AS nombre_destino_ct,
                       ld.localidad             AS localidad_destino_nombre,
                       ld.estado                AS estado_destino,
                       r.modalidad_ruta,
                       r.tipo_ruta,
                       r.distancia,
                       r.peso_soportado,
                       r.descripcion
                FROM   rutas r
                JOIN   localidades lo ON lo.id_localidad = r.localidad_origen
                JOIN   localidades ld ON ld.id_localidad = r.localidad_destino
                WHERE  r.id_ruta = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idRuta]);
        $ruta = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ruta) return false;

        $ruta["nombre_origen"]  = $this->formatearNombre(
            $ruta["nombre_origen_ct"],
            $ruta["localidad_origen_nombre"],
            $ruta["estado_origen"]
        );
        $ruta["nombre_destino"] = $this->formatearNombre(
            $ruta["nombre_destino_ct"],
            $ruta["localidad_destino_nombre"],
            $ruta["estado_destino"]
        );

        return $ruta;
    }

    // ------------------------------------------------------------------
    // ACTUALIZAR
    // ------------------------------------------------------------------

    public function buscarRutas(string $idRuta): array {
        global $pdo;

        $sql = "SELECT r.id_ruta,
                       r.modalidad_ruta AS modalidad
                FROM   rutas r
                WHERE  (? = '' OR CAST(r.id_ruta AS TEXT) ILIKE ?)
                ORDER  BY r.id_ruta
                LIMIT  50";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idRuta, "%{$idRuta}%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerRuta(string $idRuta) {
        global $pdo;

        $sql = "SELECT id_ruta,
                       localidad_origen,
                       localidad_destino,
                       modalidad_ruta  AS modalidad,
                       tipo_ruta,
                       distancia,
                       peso_soportado,
                       descripcion
                FROM   rutas
                WHERE  id_ruta = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idRuta]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarRuta(array $datos): bool {
        global $pdo;

        $sql = "UPDATE rutas
                SET    localidad_origen  = ?,
                       localidad_destino = ?,
                       modalidad_ruta    = ?,
                       tipo_ruta         = ?,
                       distancia         = ?,
                       peso_soportado    = ?,
                       descripcion       = ?
                WHERE  id_ruta = ?";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $datos["localidad_origen"]  ?: null,
            $datos["localidad_destino"] ?: null,
            $datos["modalidad_ruta"]    ?: null,
            $datos["tipo_ruta"]         ?: null,
            $datos["distancia"]  !== "" ? $datos["distancia"]     : null,
            $datos["peso_soportado"] !== "" ? $datos["peso_soportado"] : null,
            $datos["descripcion"]       ?: null,
            $datos["id_ruta"],
        ]);
    }

    // ------------------------------------------------------------------
    // HELPER
    // ------------------------------------------------------------------

    private function formatearNombre(?string $nombreCT, ?string $localidad, ?string $estado): string {
        if ($nombreCT) {
            return "{$nombreCT} — {$localidad}, {$estado}";
        }
        return "{$localidad}, {$estado}";
    }
}