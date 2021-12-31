<?php
namespace common\widgets;

use Yii;
use light\widgets\AjaxFormAsset;
use yii\helpers\Html;
use yii\helpers\Json;
/**
 * Alert widget renders a message from session flash. All flash messages are displayed
 * in the sequence they were assigned using setFlash. You can set message as following:
 *
 * ```php
 * Yii::$app->session->setFlash('error', 'This is the message');
 * Yii::$app->session->setFlash('success', 'This is the message');
 * Yii::$app->session->setFlash('info', 'This is the message');
 * ```
 *
 * Multiple messages could be set as follows:
 *
 * ```php
 * Yii::$app->session->setFlash('error', ['Error 1', 'Error 2']);
 * ```
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @author Alexander Makarov <sam@rmcreative.ru>
 */
class Ajaxform extends \yii\bootstrap5\Widget
{
    /**
     * @var bool If enable the ajax submit Multiple form ajax submit call
     'ajaxMutlipleForm' => [
        [
            'id' => 'test-modal',
            'enableAjaxSubmit' => true,
            'ajaxSubmitOptions' => [
                'beforeSend' => new JsExpression('function() {}'),
                'success' => new JsExpression('function(response) {}'),
                'complete' => new JsExpression('function() {}')
            ]
        ],
        [
            'id' => 'test2-modal',
            'enableAjaxSubmit' => true,
            'ajaxSubmitOptions' => [
                'beforeSend' => new JsExpression('function() {}'),
                'success' => new JsExpression('function(response) {}'),
                'complete' => new JsExpression('function() {}')
            ]
        ]
    ],
     */
    public $ajaxMutlipleForm = [];
    /**
     * @var bool If enable the ajax submit
     */
    public $enableAjaxSubmit = false;
    /**
     * @var array The options passed to jquery.form, Please see the jquery.form document Single Form ajax submit
     */
    public $ajaxSubmitOptions = [];

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if($this->ajaxMutlipleForm){
            $view = $this->getView();
            AjaxFormAsset::register($view);
            foreach($this->ajaxMutlipleForm as $row){
                if ($row['enableAjaxSubmit']) {
                    $id = $row['id'];
                    $_options = Json::htmlEncode($row['ajaxSubmitOptions']);
                    $view->registerJs("jQuery('#$id').yiiActiveForm().on('beforeSubmit', function(_event) { jQuery(_event.target).ajaxSubmit($_options); return false;});");
                }
            }
        }
        if ($this->enableAjaxSubmit) {
            $id = $this->options['id'];
            $view = $this->getView();
            AjaxFormAsset::register($view);
            $_options = Json::htmlEncode($this->ajaxSubmitOptions);
            $view->registerJs("jQuery('#$id').yiiActiveForm().on('beforeSubmit', function(_event) { jQuery(_event.target).ajaxSubmit($_options); return false;});");
        }
        
    }
}
