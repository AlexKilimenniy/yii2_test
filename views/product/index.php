<?php


use yii\helpers\Html;

$this->title = 'Товары';
?>
<div class="site-index">

    <div class="jumbotron">
        <h2>Товары</h2>
    </div>

    <div class="body-content">
        <?php
            echo $this->render('_gridlist', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
        ?>
    </div>
</div>
