<?php
class IdiomaHelper {

    private static $valores_letras = [
        "A"=>1,"B"=>2,"C"=>3,"D"=>4,"E"=>5,"F"=>6,"G"=>7,"H"=>8,"I"=>9,
        "J"=>1,"K"=>2,"L"=>3,"M"=>4,"N"=>5,"O"=>6,"P"=>7,"Q"=>7,"R"=>9,
        "S"=>1,"T"=>2,"U"=>3,"V"=>4,"W"=>5,"X"=>6,"Y"=>7,"Z"=>8
    ];

    public static function generarReferencia($concepto, $matricula) {
        if(strlen($matricula) < 9) return "ERROR_MATRICULA";

        $ingreso = substr($matricula, 0, 2);
        $matricula_recortada = substr($matricula, 4, 5);
        
        $referencia_base = $concepto . $ingreso . $matricula_recortada;

        $numeros = [];
        for ($i = 0; $i < strlen($referencia_base); $i++) {
            $char = strtoupper($referencia_base[$i]);
            $numeros[] = is_numeric($char) ? intval($char) : (self::$valores_letras[$char] ?? 0);
        }

        $suma = 0;

        for ($i = 0; $i < count($numeros); $i++) {
            $factor = ($i % 2 == 0) ? 2 : 1;
            $resultado = $numeros[$i] * $factor;

            if ($resultado > 9) {
                $suma += floor($resultado / 10) + ($resultado % 10);
            } else {
                $suma += $resultado;
            }
        }

        $residuo = $suma % 10;
        $verificador = ($residuo == 0) ? 0 : (10 - $residuo);

        return $referencia_base . $verificador;
    }
}