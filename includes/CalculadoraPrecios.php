<?php
class CalculadoraPrecios
{
    public static function calcularMonto($alumno, $configFicha)
    {
        if (isset($alumno['es_exento']) && $alumno['es_exento'] == 1) {
            return 0.00;
        }

        $montoBase = floatval($configFicha['monto']);

        if ($configFicha['calculo_monto'] == 0) {
            return $montoBase;
        }

        if ($alumno['carrera'] === 'TI' && $alumno['sexo'] === 'M') {
            return 1.00;
        }

        if ($alumno['id_estatus'] == 3) {
            return floatval($configFicha['monto_reincorporado']);
        }

        return $montoBase;
    }
}