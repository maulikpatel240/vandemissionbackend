<?php
/* @var $this yii\web\View */

use yii\bootstrap5\Modal;

$this->title = 'BeLocum';
?>
<style type="text/css">
    .nursingModal .info {
        color: red;
        font-size: 12px;
    }
</style>
<div class="row">
    <div class="col-12 text-center pt-4 pb-5">
        <a href="<?= $_baseUrl ?>" class="">
            <img src="<?= $_basePath . Yii::$app->params['LOGO'] ?>" class="img-fluid" alt="" />
        </a>
        <h3 class="pt-5 mt-2"><?= Yii::t('app', 'Where are you from?') ?></h3>
    </div>
</div>
<div class="col-md-8 m-auto">
    <div class="row text-center pt-5">
        <?php
        if ($_module && is_array($_module)) {
            $value = $_module;
            $imagearray = array();
            $imagearray['width'] = 150;
            $imagearray['height'] = 150;
            $imagearray['fancybox'] = false;
            $imagearray['caption'] = "";
            ?>
            <div class="col-md-4  m-auto">
                <a href="#moduleModal<?= $value['module_id'] ?>" data-toggle="modal" data-backdrop="static" data-keyboard="false">
                    <div class="icon">
                        <center><?= Yii::$app->FrontFunctions->ImageSrcHtml($value['image_url'], $imagearray) ?></center>
                    </div>
                </a>
                <a href="#moduleModal<?= $value['module_id'] ?>" style="text-align: center;color: #fff;" data-toggle="modal" data-backdrop="static" data-keyboard="false"><h3 style="text-align: center;color: #fff;"><?= Yii::t('app', $value['name']) ?></h3></a>
            </div> 
            <?php
            Modal::begin([
                'title' => '<h4 class="text-center font-weight-bold">' . Yii::t('app', $value['name']) . '</h4>',
                'id' => 'moduleModal' . $value['module_id'],
                'class' => 'modal',
                'size' => 'modal-md modal-dialog-centered',
                'clientOptions' => ['backdrop' => 'static', 'keyboard' => false],
            ]);
            ?>
            <div class="row text-center">
                <div class="col-md-12 mb-3">
                    <h4 class="text-center font-weight-bold"><?= Yii::t('app', 'What type of user are you?') ?></h4>
                </div>
                <div class="col-md-12">
                    <a href="<?= $_baseUrl . $value['unique_name'] . '/before-staff'; ?>" class="border-bottom text-dark pb-3"><?= Yii::t('app', 'I am looking for <span class="font-weight-bold">temporary work</span>') ?></a>    
                </div>
                <div class="col-md-12 pt-3 mt-2">
                    <a href="<?= $_baseUrl . $value['unique_name'] . '/before-owner'; ?>"  class="text-dark"><?= Yii::t('app', 'I am looking for <span class="font-weight-bold">temporary staff</span>') ?></a>
                    <p class="text-danger text-sm"><?= Yii::t('app', 'I am an owner, manager or administrator.') ?></p>
                </div>
            </div>
            <?php
            Modal::end();
            ?>
            <?php
        }
        ?>
    </div>
</div>
<div class="row text-center pt-5">
    <div class="col-md-12 pt-3">
        <a href="https://belocum.com/bl/homemodule" class="">
            <img src="<?= Yii::getAlias('@web'); ?>/images/back-btn.png" class="img-fluid" alt="" />
        </a>
    </div>
</div>