<?php


use yii\helpers\Html;

$this->title = 'Корзина';
?>
<div class="site-index">

    <div class="jumbotron">
        <h2>Корзина</h2>
    </div>

    <div class="body-content">
        <?php
            echo $this->render('_gridlist', ['dataProvider' => $dataProvider, 'total'=>$total]);
        ?>

    </div>
</div>
