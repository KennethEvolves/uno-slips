<?php
ob_start();


error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
ini_set('display_errors', 1);

require_once('../../../config.php');

require_once(ROOT_PATH . 'vendor/autoload.php');
require_once(ROOT_PATH . 'includes/IdiomaHelper.php');
require_once(ROOT_PATH . 'includes/Conexion.php');

$con = obtenerConexion();

$matricula = isset($_GET['matricula']) ? strtoupper(trim($_GET['matricula'])) : '';

$id_ficha = isset($_GET['id_ficha']) ? intval($_GET['id_ficha']) : 0;

if ($id_ficha === 0) {
    die("Error: No se seleccionó una ficha válida.");
}

$sqlFicha = "SELECT * FROM ficha_licenciatura WHERE id = $id_ficha LIMIT 1";
$resFicha = mysqli_query($con, $sqlFicha);
$datosFicha = mysqli_fetch_assoc($resFicha);
if (!$datosFicha)
    die("Error: La configuración de la ficha no existe.");

$id_data = 1;
$sqlData = "SELECT * FROM uno_data WHERE id_uno_data = $id_data LIMIT 1";
$resData = mysqli_query($con, $sqlData);
$datosData = mysqli_fetch_assoc($resData);
if (!$datosData)
    die("Error: La configuración de los datos bancarios no existen.");

$concepto = $datosFicha['concepto'];

if (empty($matricula))
    die("Por favor ingresa una matrícula.");

$matricula = mysqli_real_escape_string($con, $matricula);
$sqlAlu = "SELECT a.*, e.nombre_estatus
           FROM alumnos_activos a
           INNER JOIN estatus e ON a.id_estatus = e.id_estatus
           WHERE a.matricula = '$matricula' LIMIT 1";
$resAlu = mysqli_query($con, $sqlAlu);
$datosAlumno = mysqli_fetch_assoc($resAlu);


if (!$datosAlumno) {

    ob_end_clean();


    die('
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Matrícula No Encontrada</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    </head>
    <body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
        <div class="card shadow p-5 text-center" style="max-width: 500px; border-radius: 15px;">
            <div style="font-size: 50px;">⚠️</div>
            <h3 class="text-danger fw-bold mt-3">Matrícula No Encontrada</h3>
            <p class="text-muted mt-3" style="font-size: 1.1rem;">
                No encontramos registros para la matrícula: <br>
                <strong class="text-dark bg-warning px-2 rounded">' . htmlspecialchars($matricula) . '</strong>
            </p>
            <hr class="my-4">
            <p class="small text-secondary">
            Verifica que esté escrita correctamente. Si el problema persiste, contacta al Ing. Kenneth Iuit en el depto de Comunicación Social.
            </p>
            <a href="index_cuatrimestral.php" class="btn btn-secondary btn-lg mt-3 w-100">Intenta de Nuevo</a>
        </div>
    </body>
    </html>
    ');
}

$montoFormato = '$' . $datosFicha['monto'] . ' MXN';

$referencia = IdiomaHelper::generarReferencia($datosFicha['concepto'], $matricula);
mysqli_close($con);


class MYPDF extends TCPDF
{
    public $infoFicha;
    public function Header()
    {
        $img_file = ROOT_PATH . 'uno.jpg';
        $html = '
        <table border="0" cellspacing="0" cellpadding="2">
            <tr>
                <td width="15%" align="left" valign="middle"><img src="' . $img_file . '" width="65"></td>
                <td width="85%" align="left" valign="middle" style="line-height: 1.3;">
                    <span style="font-family:helvetica; font-weight:bold; font-size:11pt; color:#222;">' . $this->infoFicha['titulo'] . '</span><br>
                    <span style="font-family:helvetica; font-size:11pt; color:#333;">' . $this->infoFicha['periodo'] . '</span><br>
                    <span style="font-family:helvetica; font-size:9pt; color:#777;">Departamento de Recursos Financieros</span>
                </td>
            </tr>
        </table>
        <hr style="height:1px; color:#d0d0d0;">';
        $this->writeHTMLCell(0, 0, 15, 10, $html, 0, 1, 0, true, '', true);
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->infoFicha = $datosFicha;
$pdf->SetCreator('Universidad de Oriente');
$pdf->SetMargins(15, 38, 15);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->setPrintFooter(false);
$pdf->AddPage();

$html = '
<style>
    table { font-family: Helvetica, sans-serif; }
    
    .lbl { color: #666; font-size: 8pt; text-transform: uppercase; }
    .val { color: #000; font-size: 11pt; font-weight: bold; }
    .val-ref { color: #000; font-size: 13pt; font-weight: bold; letter-spacing: 0.5px; }

    .seccion-titulo { 
        color: #8B1D35; 
        font-weight: bold; 
        font-size: 10pt; 
        text-transform: uppercase;
    }
    .beca { color: #28a745; font-weight: bold; }
</style>

<table border="0" cellpadding="2" cellspacing="0">
    <tr>
        <td width="60%">
            <span class="lbl">Nombre del Estudiante</span><br>
            <span class="val" style="font-size:12pt;">' . $datosAlumno['nombres'] . '</span>
        </td>
        <td width="40%" align="right">
            <span class="lbl">Matrícula</span><br>
            <span class="val">' . $matricula . '</span>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <span class="lbl">Carrera / Estatus</span><br>
            <span class="val" style="font-size:9pt; color:#555;">' . $datosAlumno['carrera'] . ' - ' . $datosAlumno['nombre_estatus'] . '</span>
        </td>
    </tr>
</table>

<table border="0"><tr><td height="30"></td></tr></table>

<span class="seccion-titulo">PAGO EN VENTANILLA O APP BBVA</span>
<br><hr style="height:1px; color:#ccc;">
<table border="0" cellpadding="5" cellspacing="0">
    <tr>
        <td width="30%">
            <span class="lbl">Convenio CIE</span><br>
            <span class="val">' . $datosData['convenio'] . '</span>
        </td>
        <td width="45%">
            <span class="lbl">Referencia</span><br>
            <span class="val-ref">' . $referencia . '</span>
        </td>
        <td width="25%" align="right">
            <span class="lbl">Importe</span><br>
            <span class="val ' . ($montoFinal == 0 ? 'beca' : '') . '">' . $montoFormato . '</span>
        </td>
    </tr>
</table>

<table border="0"><tr><td height="60"></td></tr></table>

<span class="seccion-titulo">TRANSFERENCIA INTERBANCARIA</span>
<br><hr style="height:1px; color:#ccc;">
<table border="0" cellpadding="5" cellspacing="0">
    <tr>
        <td width="30%">
            <span class="lbl">CLABE</span><br>
            <span class="val">' . $datosData['clabe'] . '</span>
        </td>
        <td width="45%">
            <span class="lbl">Concepto</span><br>
            <span class="val-ref">' . $referencia . '</span>
        </td>
        <td width="25%" align="right">
            <span class="lbl">Importe</span><br>
            <span class="val ' . ($montoFinal == 0 ? 'beca' : '') . '">' . $montoFormato . '</span>
        </td>
    </tr>
</table>

<table border="0"><tr><td height="80"></td></tr></table>

<table border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td width="4px" style="background-color: #8B1D35;"></td>
        <td width="10px"></td>
        <td width="95%">
            <span style="color:#8B1D35; font-weight:bold; font-size:10pt;">
                FECHA LÍMITE: ' . $datosFicha['fecha_limite'] . '
            </span>
            <br><br>
            <span style="color:#444; font-size:8pt; line-height: 1.3; text-align:justify;">
            <b>Nota:</b> Es obligatorio cambiar el voucher original de la ficha, en el Departamento de Contabilidad. En caso de no entregar el voucher, no se reconocerá el pago realizado. El cambio de las fichas NO es PERSONAL, puede pasar cualquier persona a realizar el cambio.
            <br><br>
            <b>Horario de atención de Contabilidad:</b><br>
            Lunes a Viernes: 9:00 a.m. – 1:00 p.m. y 3:00 p.m. – 4:30 p.m.
            </span>
        </td>
    </tr>
</table>
';

$pdf->writeHTML($html, true, false, true, false, '');

if (ob_get_length())
    ob_clean();
$pdf->Output('Ficha_' . $datosFicha['titulo'] . '_' . $matricula . '.pdf', 'I');
?>