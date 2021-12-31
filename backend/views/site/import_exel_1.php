
<style> 
    /*@font-face {
       font-family: myFirstFont;
       src: url(https://localhost/vandemission/adminpanel/css/HARIKRISHNA.TTF);
    }
    
    * {
       font-family: HARIKRISHNA;
    }*/
    .gujarati{
        font-family: HARIKRISHNA;
    }
</style>
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<?php
if (isset($data) && $data) {
    ?>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">id</th>
                <th scope="col">item_category_id</th>
                <th scope="col">gujarati</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($data['items'] as $row) {
                //echo "INSERT INTO `items`(`id`, `item_category_id`,`gujarati`) VALUES ('".$row['id']."','".$row['item_category_id']."','".'<p class="gujarati">' . $row['gujarati'] . '</p>'."')<br>";
                ?>
                <tr>
                    <th scope="row"><?= $row['id'] ?></th>
                    <td><?= $row['item_category_id'] ?></td>
                    <td><?= '<p class="gujarati">' . $row['gujarati'] . '</p>'; ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <?php
} else {
    $form = ActiveForm::begin([
                'id' => 'upload',
                'options' => ['enctype' => 'multipart/form-data'],
            ])
    ?>
    <?= $form->field($model, 'file')->fileInput(['multiple' => 'multiple']) ?>

    <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end() ?>
<?php } ?>

