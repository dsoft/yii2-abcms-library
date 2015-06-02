<?php
use yii\helpers\Html;
$divId = "location-map";
?>
<br />
<?php 
// Default XY
$x = ($model[$attribute]) ?: '33.89';
$y = ($model[$attribute2]) ?: '35.51';
echo Html::activeTextInput($model, $attribute, ['id'=>'locationX']);
echo Html::activeTextInput($model, $attribute2, ['id'=>'locationY']);
?>
<br /><br />
<div id="location-map" style="height:400px;">

</div>
<br />
<script>
<?php

$this->registerJs("initializeMap('$divId', '$x', '$y')", yii\web\View::POS_LOAD);
?>
</script>