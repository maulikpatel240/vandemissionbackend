<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\detail\DetailView;
use newerton\fancybox3\FancyBox;
use yii\widgets\Breadcrumbs;
use yii\bootstrap5\Modal;

/* @var $this yii\web\View */
/* @var $model backend\models\Subdistricts */

$this->title = $model->first_name . ' ' . $model->last_name;

echo FancyBox::widget();
$imagesrc = Html::img(Yii::$app->urlManager->baseUrl . '/uploads/user/avatar/' . $model->avatar, ['width' => '100']);
$imagehtml = Html::a($imagesrc, Yii::$app->urlManager->baseUrl . '/uploads/user/avatar/' . $model->avatar, ['data-fancybox' => true]);

$imagesrc_aadhaar = Html::img(Yii::$app->urlManager->baseUrl . '/uploads/user/aadhaar/' . $model->aadhaar_card_photo, ['width' => '100']);
$imagehtml_aadhaar = Html::a($imagesrc_aadhaar, Yii::$app->urlManager->baseUrl . '/uploads/user/aadhaar/' . $model->aadhaar_card_photo, ['data-fancybox' => true]);

$this->title = $model->first_name . ' ' . $model->last_name;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
//Create
$popupmodal = false;
$popupmodalAttr = [];
$popupmodalAttr['class'] = 'ms-1 me-1 text-danger';
if (!empty(Yii::$app->BackFunctions->checkaccess('popupmodal', Yii::$app->controller->id))) {
    $popupmodal = 'showModalButton';
    $popupmodalAttr['value'] = Url::to(['users/popupmodal', 'id' => $model->id]);
    $popupmodalAttr['title'] = 'Edit';
    $popupmodalAttr['class'] = 'showModalButton ms-1 me-1 text-primary';
    $popupmodalAttr['data-pjax'] = '0';
    $popupmodalAttr['data-toggle'] = "modal";
    $popupmodalAttr['data-target'] = "#popupmodal";
    $popupmodalAttr['data-size'] = "modal-sm";
}
$updateBtn = "";
if (!empty(Yii::$app->BackFunctions->checkaccess('update', Yii::$app->controller->id))) {
    $updateBtn = '{update}';
}
$deleteBtn = "";
if (!empty(Yii::$app->BackFunctions->checkaccess('delete', Yii::$app->controller->id))) {
    $deleteBtn = '{delete}';
}
$attributes = [
    [
        'group' => true,
        'label' => 'SECTION 1: Identification Information',
        'rowOptions' => ['class' => 'table-info']
    ],
    [
        'columns' => [
            [
                'attribute' => 'avatar',
                'value'=> $imagehtml,
                'format' => 'raw',
                'displayOnly' => true,
            ],
        ]
    ],
    [
        'columns' => [
            [
                'attribute' => 'first_name',
                'format' => 'raw',
                'displayOnly' => true,
                'valueColOptions' => ['style' => 'width:30%']
            ],
            [
                'attribute' => 'last_name',
                'format' => 'raw',
                'displayOnly' => true,
                'valueColOptions' => ['style' => 'width:30%']
            ]
        ],
    ],
    [
        'columns' => [
            [
                'attribute' => 'middle_name',
                'format' => 'raw',
                'displayOnly' => true,
                'valueColOptions' => ['style' => 'width:30%']
            ],
            [
                'attribute' => 'username',
                'format' => 'raw',
                'value' => '<kbd>' . $model->username . '</kbd>',
                'displayOnly' => true,
                'valueColOptions' => ['style' => 'width:30%']
            ],
        ],
    ],
    [
        'columns' => [
            [
                'attribute' => 'phone_number',
                'format' => 'raw',
                'displayOnly' => true,
                'valueColOptions' => ['style' => 'width:30%']
            ],
            [
                'attribute' => 'phone_number_verify',
                'label' => 'Verified Number',
                'format' => 'raw',
                'value' => call_user_func(function ($data) use ($popupmodalAttr, $popupmodal) {
                    $popupmodalAttr['data-name'] = 'phone_number_verify';
                    if ($data->phone_number_verify == 0) {
                        $btnicon = ($popupmodal) ? '<span class="fas fa-pencil-alt"></span>' : '<span class="fas fa-times"></span>';
                        return Html::a($btnicon, FALSE, $popupmodalAttr);
                    } else {
                        return '<i class="fas fa-check text-success"></i>';
                    }
                }, $model),
                'displayOnly' => true,
                'valueColOptions' => ['style' => 'width:30%']
            ],
        ],
    ],
    [
        'columns' => [
            [
                'attribute' => 'email',
                'format' => 'raw',
                'displayOnly' => true,
                'valueColOptions' => ['style' => 'width:30%']
            ],
            [
                'attribute' => 'email_verify',
                'label' => 'Verified Email',
                'format' => 'raw',
                'value' => call_user_func(function ($data) use ($popupmodalAttr, $popupmodal) {
                    $popupmodalAttr['data-name'] = 'phone_number_verify';
                    if ($data->email_verify == 0) {
                        $btnicon = ($popupmodal) ? '<span class="fas fa-pencil-alt"></span>' : '<span class="fas fa-times"></span>';
                        return Html::a($btnicon, FALSE, $popupmodalAttr);
                    } else {
                        return '<i class="fas fa-check text-success"></i>';
                    }
                }, $model),
                'displayOnly' => true,
                'valueColOptions' => ['style' => 'width:30%']
            ],
        ],
    ],
    [
        'columns' => [
            [
                'attribute' => 'aadhaar_card_number',
                'format' => 'raw',
                'displayOnly' => true,
                'valueColOptions' => ['style' => 'width:30%']
            ],
            [
                'attribute' => 'aadhaar_card_photo',
                'value'=> $imagehtml_aadhaar,
                'format' => 'raw',
                'displayOnly' => true,
                'valueColOptions' => ['style' => 'width:30%']
            ],
        ],
    ],                    
                        
];
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
                $this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
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
        <div class="row mb-2">
            <div class="col-12">
                <?=
                DetailView::widget([
                    'model' => $model,
                    'attributes' => $attributes,
                    'mode' => 'view',
                    'bordered' => true,
                    'striped' => true,
                    'condensed' => false,
                    'responsive' => true,
                    'hover' => true,
                    'hAlign' => 'right',
                    'vAlign' => 'middle',
                    'fadeDelay' => '750',
                    'panel' => [
                        'type' => 'primary',
                        'heading' => '<i class="fas fa-book"></i> User Details',
                        'footer' => '<div class="text-center text-muted">This is a sample footer message for the detail view.</div>'
                    ],
                    'buttons1' => $updateBtn . '  ' . $deleteBtn,
                    'tooltips' => false,
                    'deleteOptions' => [// your ajax delete parameters
                        'params' => ['id' => 1000, 'kvdelete' => true],
                    ],
                    'container' => ['id' => 'kv-viewdata'],
                    'formOptions' => ['action' => Url::current(['#' => 'kv-viewdata'])] // your action to delete
                ]);
                ?>
                <?php
//                        DetailView::widget([
//                            'model' => $model,
//                            'attributes' => [
//                                'id',
//                                'username',
//                                'email:email',
//                                'phone_number',
//                                'first_name',
//                                'last_name',
//                                'middle_name',
//                                'avatar',
//                                'aadhaar_card_number',
//                                'aadhaar_card_photo',
//                                'gender',
//                                'birthday',
//                                'email_code:email',
//                                'sms_code',
//                                'verified',
//                                'address',
//                                'latitude',
//                                'longitude',
//                                'ip_address',
//                                'birth_privacy',
//                                [
//                                    'attribute' => 'avatar',
//                                    'value' => $imagehtml,
//                                    'format' => 'raw',
//                                ],
//                            ],
//                        ]);
                ?>
            </div>
        </div>
    </div>
</section>
<?php
Modal::begin([
    'title' => 'Edit',
    'id' => 'popupmodal',
    'size' => 'modal-md',
    'clientOptions' => ['backdrop' => 'static', 'keyboard' => FALSE],
    'closeButton' => ['id' => 'close-button'],
]);
echo \Yii::$app->view->renderFile('@app/views/temp/loader.php', ['theme_loader' => 'div', 'loader' => 'loader_div', 'display' => 'none']);
echo '<div id="modalContent"></div>';
Modal::end();
?>
<script>
    $(document).on('click', '.showModalButton', function () {
        var datasize = $(this).attr('data-size');
        $('#popupmodal').find('.modal-dialog').removeClass('modal-sm');
        $('#popupmodal').find('.modal-dialog').removeClass('modal-md');
        $('#popupmodal').find('.modal-dialog').removeClass('modal-lg');
        $('#popupmodal').find('.modal-dialog').removeClass('modal-xl');
        $('#popupmodal').find('.modal-dialog').addClass(datasize);

        $('#popupmodal').find('#modalContent').html('');
        $('.loader_div').show();
        document.getElementById('popupmodal-label').innerHTML = $(this).attr('title');
        var data = {
            '<?= Yii::$app->request->csrfParam ?>': '<?= Yii::$app->request->csrfToken ?>',
            column: $(this).attr('data-name'),
        }
        $.ajax({
            url: $(this).attr('value'),
            type: 'post',
            data: data,
            beforeSend: function () {
                $('.loader_div').show();
            },
            complete: function () {
                $('.loader_div').hide();
            },
            success: function (response) {
                $('#popupmodal').find('#modalContent').html(response);
            }
        });
    });
</script>