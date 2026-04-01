<?php
class CalculadoraPrecios
{
    public static function calcularMonto($alumno, $configFicha)
    {

        $montoBase = floatval($configFicha['monto']);

        if ($configFicha['calculo_monto'] == 0) {
            return $montoBase;
        }

        if ($alumno['id_estatus'] == 3) {
            return floatval($configFicha['monto_reincorporado']);
        }

        if (isset($alumno['es_exento']) && $alumno['es_exento'] == 1) {
            return 0.00;
        }

        if ($alumno['carrera'] === 'TI' && $alumno['sexo'] === 'M') {
            return 1.00;
        }

        return $montoBase;
    }
}