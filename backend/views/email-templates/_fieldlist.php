<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\models\Role */
?>
<div class="row mt-2 mb-2">
    <div class="col-sm-12 col-md-12 col-lg-12">
        <ul class="todo-list ui-sortable row" data-widget="todo-list">
        <?php
        if ($model) {
            foreach ($model as $value) {
                $deleteAction_url = Url::to(['email-templates/deletefield','id'=>$value->id,'email_template_id' => $email_template_id],true)
                ?>
                <li class="col-sm-12 col-md-6 col-lg-6">
                    <span class="text"><?= $value->field ?></span><br>
                    <span class=""><?= $value->description ?></span>
                    <div class="tools d-block">
                        <?php if ($value->is_default == 0) { ?>
                            <a class="float-end" onclick="deleteTodoAction('<?= $deleteAction_url; ?>')"><i class="fas fa-trash" ></i></a>
                        <?php } ?>
                    </div>
                </li>
                <?php
            }
        }
        ?>
        </ul>
    </div>
</div>
<script>
function deleteTodoAction(url){
    $.post({
        url: url,
        success: function (response) {
            fieldlist('<?=$email_template_id?>');
        },
    });
}
</script>