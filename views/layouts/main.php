<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body class="d-flex flex-column h-100">
    <?php $this->beginBody() ?>

    <header id="header">
        <!--
        Se define el contendor de la navbar
        -> navbar: activa el componente
        -> navbar-expand-lg: define cuando se expande o se colapsa
        -> bg-body-tertiary: el color de fondo
        -->
        <nav class="navbar bg-body-tertiary">
            <div class="container-fluid">
                <button class="navbar-toggler">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <!-- Se define el nombre o logo de sitio
                navbar-brand: información del sitio
                -->
                <a class="navbar-brand" href="#">
                    <img src="<?= Yii::getAlias('@web/imagenes/logo.svg') ?>" alt="logo icono" width="40" height="40">
                    Tareas
                </a>
                <!--Contenido del navbar 
                navbar-nav: contiene los enlaces
                nav-item: cada opción del menu
                nav-link: los enlaces clicables
                -->
                <ul class="navbar-nav ms-auto d-flex flex-row gap-3">
                    <li class="nav-item">
                        <a href="#">
                            <img src="<?= Yii::getAlias('@web/imagenes/ayuda.svg') ?>" alt="ayuda icono" width="24" height="24">
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Pricing</a>
                    </li>
                    <?php if (Yii::$app->user->isGuest): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?r=auth/login">Iniciar Sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?r=auth/registro">Registrarse</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            Hola, <?= Yii::$app->user->identity->nombre ?>!
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?r=auth/logout">Cerrar Sesión</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>

    <main id="main" class="flex-shrink-0" role="main">
        <div class="container-fluid">
            <?php if (!empty($this->params['breadcrumbs'])): ?>
                <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
            <?php endif ?>
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    </main>

    <footer id="footer" class="mt-auto py-3 bg-light">
        <div class="container">
            <div class="row text-muted">
                <div class="col-md-6 text-center text-md-start">&copy; My Company <?= date('Y') ?></div>
                <div class="col-md-6 text-center text-md-end"><?= Yii::powered() ?></div>
            </div>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>