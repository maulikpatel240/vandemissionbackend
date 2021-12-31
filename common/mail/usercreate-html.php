<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

?>
<div class="verify-email">
    <p>Hello, <?= Html::encode($full_name) ?>,</p>

    <p>Welcome to BeLocum, please login using below Password:</p>

    <p>Password: <?= $user_password ?></p>

    <p><?= Html::a('Login', $loginurl) ?></p>
</div>
