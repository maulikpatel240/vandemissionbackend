<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use yii\widgets\Breadcrumbs;
use yii\bootstrap5\Modal;
use backend\models\Countries;
use backend\models\States;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\RoleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Districts';
//List Index page
if (empty(Yii::$app->BackFunctions->checkaccess(Yii::$app->controller->action->id, Yii::$app->controller->id))) {
    throw new \yii\web\HttpException('403', Yii::$app->params['permission_message']);
}
//Create
$createBtn = "";
if (!empty(Yii::$app->BackFunctions->checkaccess('create', Yii::$app->controller->id))) {
    $createBtn = Html::a('<i class="fas fa-plus"></i>', FALSE, ['value' => Url::to(['districts/create']), 'title' => 'Create District', 'class' => 'showModalButton btn btn-primary']);
}
//Export
$exportBtn = '';
if (!empty(Yii::$app->BackFunctions->checkaccess('export', Yii::$app->controller->id))) {
    $exportBtn = '{export}';
}
//Apply dropdwon status, delete
$applydropdwon = "";
if (!empty(Yii::$app->BackFunctions->checkaccess('status', Yii::$app->controller->id)) && !empty(Yii::$app->BackFunctions->checkaccess('delete', Yii::$app->controller->id))) {
    $applydropdwon = Html::dropDownList('apply', '', ['' => '--Select--', 'Active' => 'Active', 'Inactive' => 'Inactive', 'Delete' => 'Delete'], ['class' => 'form-select', 'id' => 'applyoption']);
} elseif (!empty(Yii::$app->BackFunctions->checkaccess('status', Yii::$app->controller->id)) && empty(Yii::$app->BackFunctions->checkaccess('delete', Yii::$app->controller->id))) {
    $applydropdwon = Html::dropDownList('apply', '', ['' => '--Select--', 'Active' => 'Active', 'Inactive' => 'Inactive'], ['class' => 'form-select', 'id' => 'applyoption']);
} elseif (empty(Yii::$app->BackFunctions->checkaccess('status', Yii::$app->controller->id)) && !empty(Yii::$app->BackFunctions->checkaccess('delete', Yii::$app->controller->id))) {
    $applydropdwon = Html::dropDownList('apply', '', ['' => '--Select--', 'Delete' => 'Delete'], ['class' => 'form-select', 'id' => 'applyoption']);
}
$applyafter = "";
if (!empty(Yii::$app->BackFunctions->checkaccess('status', Yii::$app->controller->id)) || !empty(Yii::$app->BackFunctions->checkaccess('delete', Yii::$app->controller->id))) {
    $applySubmit = Html::button('Apply', ['class' => 'btn btn-primary', 'id' => 'applysubmit', 'onclick' => 'applyjs(this);']);
    $applyafter = '<div class="row">'
            . '<div class="col-md-2 col-lg-2 col-sm-6">' . $applydropdwon
            . '</div>'
            . '<div class="col-md-3 col-lg-3 col-sm-6">' . $applySubmit
            . '</div>'
            . '</div>';
}
?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><?= Html::encode($this->title) ?></h1>
            </div>
            <div class="col-sm-6">
                <?php
                $this->params['breadcrumbs'] = array();
                $this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => false];
                echo Breadcrumbs::widget([
                    'tag' => 'ol',
                    'options' => ['class' => 'breadcrumb float-sm-end'],
                    'homeLink' => [
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'itemTemplate' => '<li class="breadcrumb-item">{link}</li>', // template for all links
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);
                ?>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <?php
                echo GridView::widget([
                    'id' => 'gridtable',
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'pjax' => true, // pjax is set to always true for this demo
                    'pjaxSettings' => [
                        'neverTimeout' => true,
                        'options' => [
                            'id' => 'gridtable-pjax',
                        ]
                    ],
                    'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
                    'headerRowOptions' => ['class' => 'kartik-sheet-style'],
                    'filterRowOptions' => ['class' => 'kartik-sheet-style'],
                    'columns' => [
                        [
                            'class' => 'kartik\grid\SerialColumn',
                            'contentOptions' => ['class' => 'kartik-sheet-style'],
                            'width' => '36px',
                            'header' => 'No',
                            'headerOptions' => ['class' => 'kartik-sheet-style']
                        ],
                        [
                            'class' => 'kartik\grid\CheckboxColumn',
                            'headerOptions' => ['class' => 'kartik-sheet-style'],
                        ],
//                                    [
//                                        'class' => 'kartik\grid\ExpandRowColumn',
//                                        'width' => '50px',
//                                        'value' => function ($model, $key, $index, $column) {
//                                            return GridView::ROW_COLLAPSED;
//                                        },
//                                        // uncomment below and comment detail if you need to render via ajax
//                                        // 'detailUrl' => Url::to(['/site/book-details']),
//                                        'detail' => function ($model, $key, $index, $column) {
//                                            return Yii::$app->controller->renderPartial('_expand-row-details', ['model' => $model]);
//                                        },
//                                        'headerOptions' => ['class' => 'kartik-sheet-style'],
//                                        'expandOneOnly' => true
//                                    ],
                        [
                            'attribute' => 'english',
                            'vAlign' => 'middle',
                            'hAlign' => 'left',
                            'format' => 'raw',
                            'value' => function ($model, $key, $index, $widget) {
                                $name = '<div>'.$model->english.'</div>';
                                if (!empty(Yii::$app->BackFunctions->checkaccess('index', 'subdistricts'))) {
                                    $name .= '<div class="float-start">'.Html::a('SD', Url::to(['/subdistricts', 'SubdistrictsSearch[country_id]' => $model->country_id, 'SubdistrictsSearch[state_id]' => $model->state_id, 'SubdistrictsSearch[district_id]' => $model->id]), ['title' => 'View Subdistrics', 'class' => '']).'</div>';
                                }
                                if (!empty(Yii::$app->BackFunctions->checkaccess('index', 'blocks'))) {
                                    $name .= '<div class="float-end">'.Html::a('BL', Url::to(['/blocks', 'BlocksSearch[country_id]' => $model->country_id, 'BlocksSearch[state_id]' => $model->state_id, 'BlocksSearch[district_id]' => $model->id]), ['title' => 'View Blocks', 'class' => '']).'</div>';
                                }
                                return $name;
                            },
                        ],
                        [
                            'attribute' => 'gujarati',
                            'vAlign' => 'middle',
                            'hAlign' => 'left',
                            'format' => 'raw',
                            'value' => function ($model, $key, $index, $widget) {
                                return $model->gujarati;
                            },
                        ],
                        [
                            'attribute' => 'hindi',
                            'vAlign' => 'middle',
                            'hAlign' => 'left',
                            'format' => 'raw',
                            'value' => function ($model, $key, $index, $widget) {
                                return $model->hindi;
                            },
                        ],
                        [
                            'attribute' => 'state_id',
                            'vAlign' => 'middle',
                            'hAlign' => 'left',
                            'format' => 'raw',
                            'filter' => ArrayHelper::map(States::find()->where(['status' => 'Active'])->asArray()->all(), 'id', 'english'),
                            'filterInputOptions' => ['class' => 'form-select'],
                            'value' => function ($model, $key, $index, $widget) {
                                return $model->state->english;
                            },
                        ],
                        [
                            'attribute' => 'country_id',
                            'vAlign' => 'middle',
                            'hAlign' => 'left',
                            'format' => 'raw',
                            'filter' => ArrayHelper::map(Countries::find()->where(['status' => 'Active'])->asArray()->all(), 'id', 'english'),
                            'filterInputOptions' => ['class' => 'form-select'],
                            'value' => function ($model, $key, $index, $widget) {
                                return $model->country->english;
                            },
                        ],  
                        [
                            'class' => 'kartik\grid\EnumColumn',
                            'attribute' => 'status',
                            'filterInputOptions' => ['class' => 'form-select'],
                            'enum' => [
                                "Active" => 'Active',
                                "Inactive" => 'Inactive'
                            ],
                            //'filter' => ["Active" => "Active", "Inactive" => "Inactive"],
                            'loadEnumAsFilter' => true,
                            'vAlign' => 'middle',
                            'width' => '100px',
                            'contentOptions' => ['style' => 'text-align: center;'],
                            'format' => 'raw',
                            'value' => function ($model, $key, $index, $widget) {
                                $btnbg = ($model->status == 'Active') ? 'success' : 'danger';
                                if (!empty(Yii::$app->BackFunctions->checkaccess('status', Yii::$app->controller->id))) {
                                    return Html::a($model->status,
                                                    'javascript:void(0)',
                                                    [
                                                        'class' => 'btn btn-' . $btnbg . ' btn-sm',
                                                        'id' => 'status_' . $model->id,
                                                        'title' => $model->status,
                                                        'onclick' => '$.post({
                                                            url: "' . Yii::$app->homeUrl . 'districts/statusupdate?id=' . $model->id . '",
                                                            success: function (response) {
                                                                $.pjax.reload({container: "#gridtable-pjax"});
                                                            },
                                                        });'
                                                    ]
                                    );
                                } else {
                                    return Html::a($model->status,
                                                    'javascript:void(0)',
                                                    [
                                                        'class' => 'btn btn-' . $btnbg . ' btn-sm',
                                                        'id' => 'status_' . $model->id,
                                                        'title' => $model->status
                                                    ]
                                    );
                                }
                            }
                        ],
                        [
                            'class' => 'kartik\grid\ActionColumn',
                            'header' => 'Action',
                            'headerOptions' => ['width' => '80'],
                            'width' => '130px',
                            'template' => '{access} {view} {update} {delete}',
                            'buttons' => [
                                'view' => function ($url, $model) {
                                    if (!empty(Yii::$app->BackFunctions->checkaccess('view', Yii::$app->controller->id))) {
                                        return Html::a(
                                                        '<span class="fas fa-eye"></span>',
                                                        FALSE,
                                                        [
                                                            'value' => Url::to(['districts/view', 'id' => $model->id]),
                                                            'title' => 'View District',
                                                            'class' => 'showModalButton ms-1 me-1 text-warning',
                                                            'data-pjax' => '0',
                                                        ]
                                        );
                                    }
                                    return;
                                },
                                'update' => function ($url, $model) {
                                    if (!empty(Yii::$app->BackFunctions->checkaccess('update', Yii::$app->controller->id))) {
                                        return Html::a(
                                                        '<span class="fas fa-pencil-alt"></span>',
                                                        FALSE,
                                                        [
                                                            'value' => Url::to(['districts/update', 'id' => $model->id]),
                                                            'title' => 'Edit District',
                                                            'class' => 'showModalButton ms-1 me-1 text-primary',
                                                            'data-pjax' => '0',
                                                        ]
                                        );
                                    }
                                    return;
                                },
                                'delete' => function ($url) {
                                    if (!empty(Yii::$app->BackFunctions->checkaccess('delete', Yii::$app->controller->id))) {
                                        return Html::a(
                                                        '<span class="fas fa-trash-alt"></span>',
                                                        $url,
                                                        [
                                                            'title' => 'Delete District',
                                                            'class' => 'ms-1 me-1 text-danger',
                                                            'data-method' => "post",
                                                            'data-confirm' => "Are you sure to delete?",
                                                            'data-toggle' => "tooltip",
                                                            'data-pjax' => '0',
                                                        ]
                                        );
                                    }
                                    return;
                                }
                            ],
                        ],
                    ],
                    // set your toolbar
                    'toolbar' => [
                        [
                            'content' =>
                            $createBtn .
                            Html::a('<i class="fas fa-redo"></i>', ['index'], [
                                'class' => 'btn btn-outline-secondary',
                                'title' => 'Reset',
                                'data-pjax' => 1,
                            ]).
                            Html::a('<i class="fas fa-arrow-left"></i>', Url::to(['/states']), ['title' => 'View States', 'class' => 'btn btn-outline-dark']).
                            Html::a('<i class="fas fa-arrow-right"></i> Subdistricts', Url::to(['/subdistricts']), ['title' => 'View Subdistricts', 'class' => 'btn btn-outline-dark']).
                            Html::a('<i class="fas fa-arrow-right"></i> Blocks', Url::to(['/blocks']), ['title' => 'View Blocks', 'class' => 'btn btn-outline-dark']),
                            'options' => ['class' => 'btn-group me-2']
                        ],
                        '{export}',
                        '{toggleData}',
                    ],
                    'toggleDataContainer' => ['class' => 'btn-group me-2'],
                    // set export properties
                    'export' => [
                        'fontAwesome' => true,
                        'icon' => 'fas fa-share-square'
                    ],
                    'pager' => [
                        'options' => ['class' => 'pagination justify-content-center align-self-center'],
                    ],
                    // parameters from the demo form
                    'bordered' => true,
                    'striped' => false,
                    'condensed' => true,
                    'responsive' => true,
                    'responsiveWrap' => false,
                    'hover' => true,
                    'showPageSummary' => false,
                    'panel' => [
                        'type' => GridView::TYPE_PRIMARY,
                        'heading' => Html::encode($this->title),
                        'after' => $applyafter,
                        'footer' => ''
                    ],
                    'persistResize' => false,
                    'toggleDataOptions' => ['minCount' => 10],
                    'exportConfig' => [
                        GridView::EXCEL => true
                    ],
                    'itemLabelSingle' => 'District',
                    'itemLabelPlural' => 'Districts',
                ]);
                ?>
            </div>
        </div>
    </div>
</section>
<?php
Modal::begin([
    'title' => 'District',
    'id' => 'formmodal',
    'size' => 'modal-lg',
    'clientOptions' => ['backdrop' => 'static', 'keyboard' => FALSE],
    'closeButton' => ['id' => 'close-button'],
]);
echo \Yii::$app->view->renderFile('@app/views/temp/loader.php', ['theme_loader' => 'div', 'loader' => 'loader_div', 'display' => 'none']);
echo "<div id='modalContent'></div>";
Modal::end();
?>
<script type="text/javascript">
    function applyjs(e) {
        var keys = $('#gridtable').yiiGridView('getSelectedRows');
        var applyoption = $('#applyoption').val();
        $.post({
            url: '<?= Yii::$app->homeUrl . 'districts/applystatus' ?>',
            dataType: 'json',
            data: {keylist: keys, applyoption: applyoption},
            success: function (response) {
                //alert('I did it! Processed checked rows.')
                $.pjax.reload({container: "#gridtable-pjax"});
            },
        });
    }
//get the click of modal button to create / update item
//we get the button by class not by ID because you can only have one id on a page and you can
//have multiple classes therefore you can have multiple open modal buttons on a page all with or without
//the same link.
//we use on so the dom element can be called again if they are nested, otherwise when we load the content once it kills the dom element and wont let you load anther modal on click without a page refresh
    $(document).on('click', '.showModalButton', function () {
        //check if the modal is open. if it's open just reload content not whole modal
        //also this allows you to nest buttons inside of modals to reload the content it is in
        //the if else are intentionally separated instead of put into a function to get the 
        //button since it is using a class not an #id so there are many of them and we need
        //to ensure we get the right button and content. 
        $('#formmodal').find('#modalContent').html('');
        $('.loader_div').show();
        if ($('#formmodal').data('bs.modal').isShown) {
            $('#formmodal').find('#modalContent').load($(this).attr('value'), function () {
                $('.loader_div').hide();
            });
            //dynamiclly set the header for the modal via title tag
            document.getElementById('formmodal-label').innerHTML = $(this).attr('title');
        } else {
            //if modal isn't open; open it and load content
            $('#formmodal').modal('show').find('#modalContent').load($(this).attr('value'), function () {
                $('.loader_div').hide();
            });
            //dynamiclly set the header for the modal via title tag
            document.getElementById('formmodal-label').innerHTML = $(this).attr('title');
        }
    });
</script>
