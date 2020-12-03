<?php
use app\view\components\ActionColumn;
use yii\widgets\Pjax;

?>

<?php
$this->registerJs(
    "$.pjax.reload({container:'#basket'});"
);
$this->registerJs(
    "jQuery(document).ready(function($) {
    $(document).on('click','.remove-btn', function (event) {
        let url = $(this).data().url;
        $.ajax({
            url: url,
            type: 'get',
            dataType: 'text',
            success: function (response) {
                response = $.parseJSON(response);
                if(response.status == 'ok'){
                    $.pjax.reload({container:'#basket'});
                    alert('Товар успешно удален из корзины');
                }else{
                    if (response.status == 'error'){
                        alert(response.text);
                    }
                }
                
            },
            error: function () {
                alert('Произошла ошибка');
            }
        });
        event.preventDefault();
    });
    
     $('#save').on('click', function (event) {
        let url = $(this).data().action;
        $.ajax({
            url: url,
            type: 'get',
            dataType: 'text',
            success: function (response) {
                response = $.parseJSON(response);
                if(response.status == 'ok'){
                    alert('Успех');
                }else{
                    if (response.status == 'error'){
                        alert(response.text);
                    }
                }
                
            },
            error: function () {
                alert('Произошла ошибка');
            }
        });
        event.preventDefault();
    });
    
});"
);

?>


<div class="row">
    <?php if ($dataProvider->totalCount <= 0) {?>
        <h4>В корзине нет товаров</h4>
    <?php } ?>
    <?php Pjax::begin(['id' => 'basket']) ?>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'price',
            [
                'class' => \yii\grid\ActionColumn::className(),
                'buttons'=>[
                    'add_card'=>function ($url, $model) {
                        $basket_url=Yii::$app->getUrlManager()->createUrl(['basket/remove','id'=>$model['id']]);
                        return \yii\helpers\Html::a( '<span class="glyphicon glyphicon glyphicon-remove remove-btn" data-url='.$basket_url.'></span>', 'javascript:void(0)',
                            ['title' => Yii::t('yii', 'Удалить из корзины'), 'data-pjax'=>'0',]);
                    }
                ],
                'template'=>'{add_card}',
            ]
        ],
    ]); ?>

    <div class="col-md-6">
        <p><strong>ИТОГО:</strong><?=$total?></p>
    </div>
    <?php Pjax::end() ?>

    <?php if ($dataProvider->totalCount > 0) {?>
        <div class="col-md-6">
            <button class="btn btn-success" data-action="<?=Yii::$app->getUrlManager()->createUrl(['basket/save'])?>" id="save">Купит(сохранить корзину)</button>
        </div>
    <?php } ?>
</div>
