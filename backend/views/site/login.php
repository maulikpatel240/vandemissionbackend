<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Login';
?>

<div class="login-box">
    <!-- /.login-logo -->
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <a href="#" class="h1"><b><?= Html::encode($this->title) ?></b></a>
        </div>
        <div class="card-body">
            <p class="login-box-msg">Sign in to start your session</p>

            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
            <?=
            $form->field($model, 'username', [
                'template' => '{label}
                    <div class="input-group mb-3">{input}
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        {hint}{error}
                    </div>'
            ])->textInput(['autofocus' => true])
            ?>
            <?=
            $form->field($model, 'password', [
                'template' => '{label}
                    <div class="input-group mb-3">{input}
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        {hint}{error}
                    </div>'
            ])->textInput(['autofocus' => true])
            ?>
            
                <div class="row">
                    <div class="col-8">
                        <?= $form->field($model, 'rememberMe')->checkbox() ?>
                    </div>
                    <div class="col-4">
                        <?= Html::submitButton('Login', ['class' => 'btn btn-primary btn-block', 'name' => 'login-button']) ?>
                    </div>
                </div>
            <?php ActiveForm::end(); ?>

            <p class="mb-1">
                <a href="#">I forgot my password</a>
            </p>
        </div>
    </div>
</div>