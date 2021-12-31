<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class PluginAsset extends AssetBundle {

    //public $sourcePath = '@web/';
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $depends = [
        'frontend\assets\AppAsset'
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
        // 'home' => [
        //     'css' => [
        //         ['pages/afterlogin/css/calendar.css',['position' => \yii\web\View::POS_BEGIN]],
        //     ]
        // ]
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
