<?php
require_once('config.php');
require_once(ROOT_PATH . 'includes/Conexion.php');
$con = obtenerConexion();
$sql = "SELECT * FROM cat_departamentos ORDER BY id_departamento ASC";
$res = mysqli_query($con, $sql);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Plataforma de Gestión de Fichas UNO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --geist-foreground: #000;
            --geist-background: #fff;
            --accents-1: #fafafa;
            --accents-2: #eaeaea;
            --brand-maroon: #8B1D35;
            --velocidad-transicion: 0.3s;
        }

        body {
            background-color: var(--accents-1);
            font-family: 'Inter', sans-serif;
            color: var(--geist-foreground);
            -webkit-font-smoothing: antialiased;
        }

        .container-selection {
            max-width: 900px;
            margin: 100px auto;
            padding: 20px;
        }

        .main-title {
            font-weight: 800;
            letter-spacing: -0.04em;
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-align: center;
        }

        .sub-title {
            color: #666;
            text-align: center;
            margin-bottom: 50px;
        }

        .dept-card {
            background: #fff;
            border: 1px solid var(--accents-2);
            border-radius: 12px;
            padding: 30px;
            text-decoration: none;
            color: inherit;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
            cursor: pointer;
        }

        .dept-card:hover {
            border-color: var(--geist-foreground);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transform: translateY(-4px);
        }

        .dept-name {
            font-weight: 700;
            font-size: 1.25rem;
            margin-bottom: 8px;
        }

        .dept-desc {
            font-size: 0.9rem;
            color: #666;
            line-height: 1.5;
        }

        .arrow {
            margin-top: auto;
            padding-top: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--brand-maroon);
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .dept-card:hover .arrow {
            opacity: 1;
        }

        @media (max-width: 768px) {
            .container-selection {
                margin: 40px auto;
            }

            .main-title {
                font-size: 1.8rem;
            }

            .dept-card {
                padding: 20px;
            }

            .arrow {
                opacity: 1;
                margin-top: 15px;
            }
        }
    </style>
</head>

<body>

    <div class="container-selection">
        <h1 class="main-title">Plataforma de Gestión de Fichas UNO</h1>
        <p class="sub-title">Seleccione el departamento correspondiente para iniciar con la emisión de su ficha de pago.
        </p>

        <div class="row g-4">
            <?php while ($dept = mysqli_fetch_assoc($res)): ?>
                <div class="col-md-4">
                    <a href="app/pages/generador_ficha/index.php?dept=<?php echo $dept['id_departamento']; ?>"
                        class="dept-card">
                        <div class="mb-3 text-secondary" style="font-size: 1.5rem;">
                            <?php
                            $nombre = strtolower($dept['nombre_departamento']);

                            if (strpos($nombre, 'idiomas') !== false) {
                                echo '🌐';
                            } elseif (strpos($nombre, 'escolar') !== false) {
                                echo '🎓';
                            } elseif (strpos($nombre, 'finanzas') !== false || strpos($nombre, 'recursos') !== false) {
                                echo '💰';
                            } else {
                                echo '📂';
                            }
                            ?>
                        </div>
                        <div class="dept-name"><?php echo $dept['nombre_departamento']; ?></div>
                        <div class="dept-desc">
                            Módulo de emisión de fichas de pago para trámites académicos.
                        </div>
                        <div class="arrow">Ir al módulo →</div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    </div>

</body>

</html>