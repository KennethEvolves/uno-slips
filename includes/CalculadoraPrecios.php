<?php
class CalculadoraPrecios
{

    public static function calcularMonto($alumno, $montoBase)
    {
        // Regla 1: Mujeres de TICS (TI) no pagan
        if ($alumno['carrera'] == 'TI' && $alumno['sexo'] == 'M') {
            return 0.00;
        }

        // Regla 2: Reincorporados pagan tarifa especial
        if ($alumno['estatus'] == 'REINCORPORADO') {
            return 1561.00;
        }

        // Regla 3: Todos los demás pagan el monto base
        return floatval($montoBase);
    }
}
?>