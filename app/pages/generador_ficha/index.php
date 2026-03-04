<?php

require_once('../../../config.php');

$conexionPath = ROOT_PATH . 'includes/Conexion.php';

if (file_exists($conexionPath)) {
    require_once($conexionPath);
} else {
    echo 'error_sistema';
    exit;
}

$con = obtenerConexion();

$id_dept = isset($_GET['dept']) ? intval($_GET['dept']) : 1;

$sqlDept = "SELECT nombre_departamento FROM cat_departamentos WHERE id_departamento = $id_dept LIMIT 1";
$resDept = mysqli_query($con, $sqlDept);
$datosDept = mysqli_fetch_assoc($resDept);
$nombreDept = $datosDept['nombre_departamento'] ?? 'Departamento General';

$hoy = date('Y-m-d');

$sqlFichas = "SELECT id, titulo, periodo FROM ficha_licenciatura 
              WHERE estatus_ficha = 1 
              AND id_departamento = $id_dept 
              AND '$hoy' >= fecha_inicio 
              AND '$hoy' <= fecha_fin 
              ORDER BY id DESC";
$resFichas = mysqli_query($con, $sqlFichas);

$fichasDisponibles = [];
while ($f = mysqli_fetch_assoc($resFichas)) {
    $fichasDisponibles[] = $f;
}

$tituloDinamico = $fichasDisponibles[0]['titulo'] ?? 'No hay fichas disponibles';
$periodoDinamico = $fichasDisponibles[0]['periodo'] ?? 'Fuera de periodo';

if (isset($_POST['validar_ajax'])) {
    $matricula = mysqli_real_escape_string($con, $_POST['matricula']);

    $sql = "SELECT id FROM alumnos_activos WHERE matricula = '$matricula' LIMIT 1";
    $res = mysqli_query($con, $sql);

    if (mysqli_num_rows($res) > 0) {
        echo 'existe';
    } else {
        echo 'no_existe';
    }

    mysqli_close($con);

    exit;
}
$tituloDinamico = 'Selecciona una ficha';
$periodoDinamico = 'Esperando selección...';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>
        <?php echo $tituloDinamico ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --geist-foreground: #000;
            --geist-background: #fff;
            --accents-1: #fafafa;
            --accents-2: #eaeaea;
            --accents-3: #999;
            --accents-5: #666;
            --geist-success: #3D0C16;
            --geist-error: #ee0000;
            --brand-maroon: #8B1D35;
            --velocidad-transicion: 0.3s;
        }

        body {
            background-color: var(--accents-1);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: var(--geist-foreground);
            -webkit-font-smoothing: antialiased;
        }

        .card-login {
            max-width: 480px;
            margin: 80px auto;
            border: 1px solid var(--accents-2);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.04);
            border-radius: 12px;
            background-color: var(--geist-background);
            padding: 40px !important;
        }

        .header-logo {
            max-height: 50px;
            margin-bottom: 32px;
            filter: grayscale(0.2);
            transition: filter 0.3s ease;
        }

        .header-logo:hover {
            filter: grayscale(0);
        }

        h4 {
            font-weight: 700;
            letter-spacing: -0.02em;
            font-size: 1.5rem;
            margin-bottom: 8px;
        }

        .badge-cuatri {
            background-color: var(--accents-1);
            color: var(--accents-5);
            padding: 6px 12px;
            border: 1px solid var(--accents-2);
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
            margin-bottom: 24px;
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--accents-5);
            margin-bottom: 8px;
        }

        .form-select,
        .form-control {
            border: 1px solid var(--accents-2);
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 0.95rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-select {
            cursor: pointer;
        }

        .form-select:focus,
        .form-control:focus {
            outline: none;
            border-color: var(--brand-maroon);
            box-shadow: 0 0 0 4px rgba(139, 29, 53, 0.1);
            color: #000;
        }

        .input-group-text {
            background-color: transparent;
            border: 1px solid var(--accents-2);
            border-right: none;
            color: var(--accents-3);
        }

        .btn-uno {
            background-color: var(--geist-foreground);
            color: var(--geist-background);
            padding: 14px;
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: 8px;
            border: 1px solid var(--geist-foreground);
            transition: all 0.2s ease;
            margin-top: 10px;
        }

        .btn-uno:hover {
            background-color: var(--geist-background);
            color: var(--geist-foreground);
            border-color: var(--geist-foreground);

        }

        .btn-s {
            background: linear-gradient(145deg, #000 0%, var(--brand-maroon) 100%) !important;
            border: 1px solid #000 !important;
            color: #fff !important;
            padding: 14px !important;
            margin-top: 10px;
            font-weight: 600 !important;
            font-size: 0.95rem !important;

            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);

            animation: slideInSuccess 0.4s cubic-bezier(0.22, 1, 0.36, 1);
        }

        @keyframes slideInSuccess {
            from {
                transform: scale(0.98);
                opacity: 0.8;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        #error-mensaje {
            color: var(--geist-error);
            font-size: 0.85rem;
            margin-top: 12px;
            display: none;
            padding: 10px;
            border-radius: 8px;
            background: rgba(238, 0, 0, 0.05);
            border: 1px solid rgba(238, 0, 0, 0.1);
        }

        .info-text {
            font-size: 0.8rem;
            color: var(--accents-3);
            text-align: center;
        }

        .cambio-profundo {
            animation: efectoFluent var(--velocidad-transicion) cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }

        @keyframes efectoFluent {
            0% {
                opacity: 0;
                filter: blur(4px);
                transform: translateY(4px) scale(0.98);
            }

            100% {
                opacity: 1;
                filter: blur(0);
                transform: translateY(0) scale(1);
            }
        }

        .footer-copy {
            font-size: 0.75rem;
            color: var(--accents-3);
            margin-top: 40px;
        }

        .contact-link {
            color: inherit;
            text-decoration: none;
            transition: color 0.2s ease;
            display: inline-block;
        }

        .contact-link:hover {
            color: var(--brand-maroon);
            text-decoration: underline;
            text-underline-offset: 4px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="card card-login">
            <div class="text-center">
                <img src="../../../uno.jpg" alt="Logo Universidad" class="header-logo">
                <div class="mb-1">
                    <span
                        style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; color: var(--accents-5); opacity: 0.8;">
                        <?php echo $nombreDept; ?>
                    </span>
                </div>
                <h4 id="display-titulo">
                    <?php echo $tituloDinamico; ?>
                </h4>
                <div id="display-periodo" class="badge-cuatri">
                    <?php echo $periodoDinamico; ?>
                </div>
            </div>

            <form id="formFicha" method="GET">
                <div class="mb-4 pt-2">
                    <label class="form-label">Elegir ficha</label>
                    <select name="id_ficha" id="id_ficha" class="form-select" required>
                        <option value="" disabled selected>-- Haz clic para ver opciones --</option>

                        <?php foreach ($fichasDisponibles as $ficha): ?>
                            <option value="<?php echo $ficha['id']; ?>"
                                data-titulo="<?php echo htmlspecialchars($ficha['titulo']); ?>"
                                data-periodo="<?php echo htmlspecialchars($ficha['periodo']); ?>">
                                <?php echo $ficha['titulo']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="matricula" class="form-label">Ingresa tu matrícula</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-person-vcard" viewBox="0 0 16 16">
                                <path
                                    d="M5 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4m4-2.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5M9 8a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4A.5.5 0 0 1 9 8m1 2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5" />
                                <path
                                    d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1z" />
                            </svg>
                        </span>
                        <input type="text" name="matricula" id="matricula" class="form-control text-uppercase"
                            placeholder="Ej. 26UOGA032" required maxlength="15" autocomplete="off">
                    </div>
                    <div id="error-mensaje">⚠️ La matrícula no coincide con nuestros registros.</div>
                </div>

                <button type="submit" id="btnGenerar" class="btn btn-uno w-100">
                    <span id="spinner" class="spinner-border spinner-border-sm d-none" role="status"
                        aria-hidden="true"></span>
                    <span id="btnText">GENERAR FICHA</span>
                </button>
            </form>

            <div class="mt-5 pt-4 border-top">
                <p class="info-text mb-1 fw-medium text-dark">Soporte técnico</p>
                <p class="info-text mb-0">
                    <a href="mailto:kenneth.iuit@uno.edu.mx" class="contact-link">
                        kenneth.iuit@uno.edu.mx
                    </a>
                </p>
                <p class="info-text">
                    <a href="https://wa.me/529851079678" target="_blank" class="contact-link">
                        985-107-9678
                    </a>
                </p>
            </div>
        </div>

        <div class="text-center footer-copy">
            <p>&copy; 2026 Universidad de Oriente &middot; Valladolid, Yucatán, México.</p>
        </div>
    </div>

    <script>
        const form = document.getElementById('formFicha');
        const input = document.getElementById('matricula');
        const errorDiv = document.getElementById('error-mensaje');
        const btn = document.getElementById('btnGenerar');
        const btnText = document.getElementById('btnText');
        const spinner = document.getElementById('spinner');
        const selectFicha = document.getElementById('id_ficha');
        const displayTitulo = document.getElementById('display-titulo');
        const displayPeriodo = document.getElementById('display-periodo');

        if (selectFicha.value === "") {
            btn.disabled = true;
            btn.style.opacity = "0.5";
            btn.style.cursor = "not-allowed";
        }

        selectFicha.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];

            if (this.value !== "") {
                const nuevoTitulo = selectedOption.getAttribute('data-titulo');
                const nuevoPeriodo = selectedOption.getAttribute('data-periodo');

                btn.disabled = false;
                btn.style.opacity = "1";
                btn.style.cursor = "pointer";

                displayTitulo.classList.remove('cambio-profundo');
                displayPeriodo.classList.remove('cambio-profundo');
                void displayTitulo.offsetWidth;

                displayTitulo.innerText = nuevoTitulo;
                displayPeriodo.innerText = nuevoPeriodo;
                document.title = nuevoTitulo;

                displayTitulo.classList.add('cambio-profundo');
                displayPeriodo.classList.add('cambio-profundo');
            }
        });

        window.addEventListener('pageshow', function (event) {
            restablecerEstadoBoton();
        });

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            errorDiv.style.display = 'none';
            input.classList.remove('is-invalid');

            const val = input.value.trim();
            if (val === '') return;

            btn.disabled = true;
            spinner.classList.remove('d-none');

            const datos = new FormData();
            datos.append('validar_ajax', '1');
            datos.append('matricula', val);

            fetch(window.location.href, {
                method: 'POST',
                body: datos
            })
                .then(res => res.text())
                .then(respuesta => {
                    if (respuesta.trim() === 'existe') {
                        btn.classList.remove('btn-uno');
                        btn.classList.add('btn-s');
                        btnText.innerText = "¡Matrícula encontrada!";

                        setTimeout(() => {
                            btnText.innerText = "Generando...";

                            setTimeout(() => {
                                form.action = 'ficha.php';
                                form.target = '_self';
                                form.submit();
                            }, 1000);

                        }, 1000);

                    } else {

                        restablecerEstadoBoton();
                        errorDiv.style.display = 'block';
                        input.classList.add('is-invalid');
                        input.focus();
                    }
                })
                .catch(err => {
                    console.error(err);
                    restablecerEstadoBoton();
                    alert("Error de conexión.");
                });
        });

        function restablecerEstadoBoton() {
            btn.disabled = false;
            btn.classList.remove('btn-s');
            btn.classList.add('btn-uno');
            btnText.innerText = "GENERAR FICHA DE PAGO";
            spinner.classList.add('d-none');
        }

        input.addEventListener('input', function () {
            if (errorDiv.style.display === 'block') {
                errorDiv.style.display = 'none';
                input.classList.remove('is-invalid');
            }
        });
    </script>
</body>

</html>