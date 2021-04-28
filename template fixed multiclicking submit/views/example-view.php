<?php

use yii\widgets\ActiveForm;
use yii\bootstrap\Html;

$lang = Yii::$app->language;

?>

<?php $form = ActiveForm::begin(['id' => 'form']); ?>
<?php echo Yii::$app->generateToken->setFormToken(); ?>
<?php ActiveForm::end(); ?>
