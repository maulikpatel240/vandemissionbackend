<?php
/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;
use backend\models\Modules;

//use kartik\icons\FontAwesomeAsset;
//FontAwesomeAsset::register($this);

AppAsset::register($this);
$bootstrapbundle = \backend\assets\PluginAsset::register($this)->add(['afterlogin']);

$controller = Yii::$app->controller->id;
$action = Yii::$app->controller->action->id;

$_baseUrl = Yii::$app->homeUrl;
$current_url = $controller;

global $vm, $db, $adminuser;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php $this->registerCsrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <link rel="stylesheet" href="">
        <?php $this->head() ?>
        <!--<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">-->
    </head>
    <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed position-relative">
        <?php $this->beginBody() ?>
        <div class="wrapper">
            <!-- Navbar -->
            <nav class="main-header navbar navbar-expand navbar-dark navbar-gray-dark">
                <!-- header Left navbar links -->
                <?php
                $headerMenuLeft = array();

                $headerMenuLeft[] = '<li class="nav-item">'
                        . Html::a('<i class="fas fa-bars"></i>', ['#'], ['class' => 'nav-link', 'data-widget' => "pushmenu", 'role' => 'button'])
                        . '</li>';

                $headerMenuLeft[] = '<li class="nav-item d-none d-sm-inline-block">'
                        . Html::a('Home', ['index'], ['class' => 'nav-link'])
                        . '</li>';
                echo Nav::widget([
                    'options' => ['class' => 'navbar-nav'],
                    'items' => $headerMenuLeft,
                ]);
                ?>
                <!-- header Right navbar links -->
                <?php
                $headerMenuRight = array();
                $headerMenuRight[] = '<li class="nav-item">'
                        . Html::beginForm(['/site/logout'], 'post')
                        . Html::submitButton(
                                'Logout (' . Yii::$app->user->identity->username . ')',
                                ['class' => 'btn btn-link logout nav-link']
                        )
                        . Html::endForm()
                        . '</li>';
                echo Nav::widget([
                    'options' => ['class' => 'navbar-nav ms-auto'],
                    'items' => $headerMenuRight,
                ]);
                ?>
            </nav>
            <!-- /.navbar -->

            <!-- Main Sidebar Container -->
            <aside class="main-sidebar sidebar-dark-primary elevation-4">
                <!-- Brand Logo -->
                <a href="index3.html" class="brand-link navbar-gray-dark">
                    <img src="<?= $_baseUrl; ?>dist/img/AdminLTELogo.png" alt="" class="brand-image img-circle elevation-3" style="opacity: .8">
                    <span class="brand-text font-weight-light">AdminLTE 3</span>
                </a>

                <!-- Sidebar -->
                <div class="sidebar">
                    <!-- Sidebar user panel (optional) -->
                    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                        <div class="image">
                            <img src="<?= $_baseUrl; ?>dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
                        </div>
                        <div class="info">
                            <a href="#" class="d-block"><?= Yii::$app->user->identity->username ?></a>
                        </div>
                    </div>

                    <!-- SidebarSearch Form -->
                    <div class="form-inline">
                        <div class="input-group" data-widget="sidebar-search">
                            <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                            <div class="input-group-append">
                                <button class="btn btn-sidebar">
                                    <i class="fas fa-search fa-fw"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Menu -->
                    <nav class="mt-2">
                        <?php
                        $sideMenu = array();
                        $modules = Modules::find()->where(['menu_id' => 0, 'parent_menu_id' => 0, 'parent_submenu_id' => 0])->andWhere(['!=', 'title', ''])->all();
                        if($modules){
                            foreach ($modules as $key => $value){

                                //$sideMenu[] = '<li class="nav-header">'.$value['title'].'</li>';

                                $menus = Modules::find()->where(['type' => 'Menu','menu_id' => $value['id'] ,'status'=>'Active'])->andWhere(['!=', 'title', ''])->orderBy(['menu_position'=>SORT_ASC])->all();
                                $side_menu = array();
                                if($menus){
                                    foreach ($menus as $key_menu => $value_menu){
                                        $active_sidemenu = '';
                                        $nav_open_sidemenu = '';

                                        if($value_menu['controller'] && $value_menu['action'] == 'index'){
                                            $url = $value_menu['controller'];
                                        }elseif($value_menu['controller'] && $value_menu['action'] != 'index'){
                                            $url = $value_menu['controller'].'/'.$value_menu['action'];
                                        }else{
                                            $url = false;
                                        }
                                        $childMenu = "";
                                        $icon = "";
                                        $submenu = Modules::find()->where(['type' => 'Submenu','menu_id' => $value['id'],'parent_menu_id' => $value_menu['id'] ,'status'=>'Active'])->andWhere(['!=', 'title', ''])->orderBy(['submenu_position'=>SORT_ASC])->all();
                                        if ($submenu) {
                                            $childMenu .= '<ul class="nav nav-treeview">';
                                            foreach ($submenu as $key_submenu => $value_submenu) {
                                                if($value_submenu['controller'] && $value_submenu['action'] == 'index'){
                                                    $url_childmenu = $value_submenu['controller'];
                                                }elseif($value_submenu['controller'] && $value_submenu['action'] != 'index'){
                                                    $url_childmenu = $value_submenu['controller'].'/'.$value_submenu['action'];
                                                }else{
                                                    $url_childmenu = false;
                                                }
                                                $active_childmenu = '';
                                                if ($url_childmenu && $controller == $value_submenu['controller'] && $action == $value_submenu['action']) {
                                                    $active_sidemenu = 'active';
                                                    $active_childmenu = 'active';
                                                    $nav_open_sidemenu = 'menu-is-opening menu-open';
                                                }
                                                $iconchild = "";
                                                $childInMenu = "";
                                                $subsubmenu = Modules::find()->where(['type' => 'Subsubmenu','menu_id' => $value['id'],'parent_menu_id' => $value_menu['id'],'parent_submenu_id' => $value_submenu['id'] ,'status'=>'Active'])->andWhere(['!=', 'title', ''])->orderBy(['submenu_position'=>SORT_ASC])->all();
                                                if($subsubmenu){  
                                                  $childInMenu .= '<ul class="nav nav-treeview">';
                                                  foreach ($subsubmenu as $key_subsubmenu => $value_subsubmenu) {
                                                    if($value_subsubmenu['controller'] && $value_subsubmenu['action'] == 'index'){
                                                        $url_childInmenu = $value_subsubmenu['controller'];
                                                    }elseif($value_subsubmenu['controller'] && $value_subsubmenu['action'] != 'index'){
                                                        $url_childInmenu = $value_subsubmenu['controller'].'/'.$value_subsubmenu['action'];
                                                    }else{
                                                        $url_childInmenu = false;
                                                    }
                                                      $active_childInmenu = '';
                                                      if ($url_childInmenu && $controller == $value_subsubmenu['controller'] && $action == $value_subsubmenu['action']) {
                                                          $active_sidemenu = 'active';
                                                          $active_childmenu = 'active';
                                                          $active_childInmenu = 'active';
                                                          $nav_open_sidemenu = 'menu-is-opening menu-open';
                                                      }
                                                      if(!empty(Yii::$app->BackFunctions->checkaccess($value_subsubmenu['action'], $value_subsubmenu['controller']))){
                                                        $childInMenu .= '<li class="nav-item">'
                                                                . Html::a('<i class="nav-icon ' . $value_subsubmenu['icon'] . '"></i><p>' . $value_subsubmenu['title'] . '</p>', $vm['base_url'].'/'.$url_childInmenu, ['class' => 'nav-link ' . $active_childInmenu, 'data-ajax'=>'?path='.$url_childInmenu])
                                                                . '</li>';
                                                      }
                                                  }
                                                  $childInMenu .= "</ul>";
                                                  $iconchild .= '<i class="fas fa-angle-left right"></i>';
                                                }
                                                if(!empty(Yii::$app->BackFunctions->checkaccess($value_submenu['action'], $value_submenu['controller']))){
                                                    $childMenu .= '<li class="nav-item">'
                                                            . Html::a('<i class="nav-icon ' . $value_submenu['icon'] . '"></i><p>' . $value_submenu['title'] . $iconchild . '</p>', $vm['base_url'].'/'.$url_childmenu, ['class' => 'nav-link ' . $active_childmenu, 'data-ajax'=>'?path='.$url_childmenu])
                                                            . $childInMenu
                                                            . '</li>';
                                                }
                                            }
                                            $childMenu .= "</ul>";
                                            $icon .= '<i class="fas fa-angle-left right"></i>';
                                        }else{
                                            if ($url && $controller == $value_menu['controller'] && $action == $value_menu['action']) {
                                                $active_sidemenu = 'active';
                                                $nav_open_sidemenu = 'menu-is-opening menu-open';
                                            }
                                        }
                                        //echo Yii::$app->BackFunctions->checkaccess($value_menu['action'], $value_menu['controller']);
                                        if(!empty(Yii::$app->BackFunctions->checkaccess($value_menu['action'], $value_menu['controller']))){
                                            $sideMenu[] = '<li class="nav-item ' . $nav_open_sidemenu . '">'
                                            . Html::a('<i class="nav-icon ' . $value_menu['icon'] . '"></i><p>' . $value_menu['title'] . $icon . '</p>', $vm['base_url'].'/'.$url, ['class' => 'nav-link ' . $active_sidemenu, 'data-ajax'=>'?path='.$url])
                                            . $childMenu
                                            . '</li>';
                                        }else{
                                            $sideMenu[] = '<li class="nav-item ' . $nav_open_sidemenu . '">'
                                            . Html::a('<i class="nav-icon ' . $value_menu['icon'] . '"></i><p>' . $value_menu['title'] . $icon . '</p>', $vm['base_url'].'/'.$url, ['class' => 'nav-link ' . $active_sidemenu, 'data-ajax'=>'?path='.$url])
                                            . $childMenu
                                            . '</li>';
                                        }
                                    } 
                                }

                            }
                        }
                        echo Nav::widget([
                            'options' => ['class' => 'nav nav-pills nav-sidebar flex-column', 'data-widget' => "treeview", 'role' => "menu", 'data-accordion' => "false"],
                            'items' => $sideMenu,
                        ]);
                        ?>
                    </nav>
                </div>
            </aside>
            <div class="content-wrapper">
                <section class="alert_section">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-12">
                                <?= Alert::widget([
                                    'options' => [
                                        'class' => 'm-2',
                                    ]
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </section>
                <div class="contentpage">
                    <?= $content ?>
                </div>
            </div>
        </div>
        <?php $this->endBody() ?>
        <script type="text/javascript">
            var btnscrollup = $('.scrollToTop');
            $(window).scroll(function() {
                if ($(window).scrollTop() > 300) {
                  btnscrollup.removeClass('d-none');
                } else {
                  btnscrollup.addClass('d-none');
                }
              });

            btnscrollup.on('click', function(e) {
                e.preventDefault();
                $('html, body').animate({scrollTop:0}, '300');
            });
            $(document).ready(function () {
                $('.theme_loader_fix').fadeOut('slow', function () {
                    $(this).hide();
                });
            });
            $(document).on('click', '.showModalButton', function () {
                document.getElementById('formmodal-label').innerHTML = $(this).attr('title');
                $.ajax({
                    url: $(this).attr('value'),
                    beforeSend: function () {
                        $('#formmodal').modal('show')
                        $('#formmodal').find('#modalContent').html('');
                        $('.loader_div').show();
                    },
                    success: function (response) {
                        $('#formmodal').find('#modalContent').html(response);
                    },
                    complete: function () {
                        $('.loader_div').hide();
                    }
                });
            });
        </script>
    </body>
</html>
<?php $this->endPage() ?>
