<?php

namespace backend\assets;

use yii\web\AssetBundle;

class PluginAsset extends AssetBundle {

    //public $sourcePath = '@web/';
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $depends = [
        'backend\assets\AppAsset'
    ];
    //Page css and js
    public static $pluginMap = [
        'blank' => [
            'css' => [
                'css/main.css',
                'css/blank.css',
                ],
            'js' => [
                ],
        ],
        
        'beforelogin' => [
            'css' => [
                'plugins/waves/dist/waves.min.css',
                //scroll
                'plugins/overlayScrollbars/css/OverlayScrollbars.min.css',
                //font
                'plugins/fontawesome-free/css/all.min.css',
                //select2
                'plugins/select2/css/select2.min.css',
                'plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css',
                //selectpicker
                'plugins/selectpicker/css/bootstrap-select.min.css',
                //toastr
                'plugins/toastr/toastr.min.css',
                //fancybox
                'plugins/fancybox/dist/jquery.fancybox.min.css',
                //datetimepicker
                //'plugins/tempusdominus-bootstrap-4/css/bootstrap-datetimepicker.css',
                //adminlte theme
                'dist/css/adminlte.css',
                ],
            'js' => [
                'dist/js/adminlte.min.js',
                'plugins/waves/dist/waves.min.js',
                //scroll
                'plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js',
                //select2
                'plugins/select2/js/select2.full.min.js',
                //selectpicker
                'plugins/selectpicker/js/bootstrap-select.js',
                //toastr
                'plugins/toastr/toastr.min.js',
                //fancybox
                'plugins/fancybox/dist/jquery.fancybox.min.js',
                //datetimepicker
                'plugins/moment/moment.min.js',
                //'plugins/tempusdominus-bootstrap-4/js/bootstrap-datetimepicker.js',
                
                //extra
                'dist/js/demo.js'
            ],
        ],
        
        'afterlogin' => [
            'css' => [
                'plugins/waves/dist/waves.min.css',
                //scroll
                'plugins/overlayScrollbars/css/OverlayScrollbars.min.css',
                //font
                'plugins/fontawesome-free/css/all.min.css',
                'plugins/tempusdominus-bootstrap-4/css/bootstrap-datetimepicker.css',
                //animate
                'css/other/animate.modal.css',
                //other
                'dist/css/adminlte.css',
                'css/site.css',
                'css/HARIKRISHNA.TTF',
            ],
            'js' => [
                //adminlte
                'dist/js/adminlte.min.js',
                'plugins/waves/dist/waves.min.js',
                //scroll
                'plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js',
                //datetimepicker
                'plugins/moment/moment.min.js',
                'plugins/tempusdominus-bootstrap-4/js/bootstrap-datetimepicker.js',
                
                //other
                'js/bs4datetimepicker.js',
                'dist/js/demo.js',
                'js/script.js'
            ],
        ],
       // ['pages/afterlogin/css/calendar.css',['position' => \yii\web\View::POS_BEGIN]],
         'material' => [
             'css' => [
                 ['plugins/materialize/css/materialize.css'],
             ],
             'js' => [
                 ['plugins/materialize/js/materialize.js'],
             ]
         ]
    ];

    /**
     * add a plugin dynamically
     * @param $pluginName
     * @return $this
     */
    public function add($pluginName) {
        $pluginName = (array) $pluginName;

        foreach ($pluginName as $name) {
            $plugin = $this->getPluginConfig($name);
            if (isset($plugin['css'])) {
                foreach ((array) $plugin['css'] as $v) {
                    $this->css[] = $v;
                }
            }
            if (isset($plugin['js'])) {
                foreach ((array) $plugin['js'] as $v) {
                    $this->js[] = $v;
                }
            }
        }

        return $this;
    }

    /**
     * @param $name plugin name
     * @return array|null
     */
    private function getPluginConfig($name) {
        return self::$pluginMap[$name] ?? null ?? null;
    }

}
