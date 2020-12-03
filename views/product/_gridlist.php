<?php
use app\view\components\ActionColumn;
use yii\widgets\Pjax;

?>

<?php
$this->registerJs(
    "$.pjax.reload({container:'#products'});"
);
$this->registerJs(
    "jQuery(document).ready(function($) {
    /*
     * Добавление товара в корзину с использованием AJAX
     */
    $(document).on('click','.basket-btn', function (event) {
        let url = $(this).data().url;
        $.ajax({
            url: url,
            type: 'get',
            dataType: 'text',
            success: function (response) {
                response = $.parseJSON(response);
                if(response.status == 'ok'){
                    alert('Товар успешно добавлен в корзину');
                }else{
                    if (response.status == 'error'){
                        alert(response.text);
                    }
                }
                
            },
            error: function () {
                alert('Произошла ошибка при добавлении товара в корзину');
            }
        });
        event.preventDefault();
    });
});"
);
?>


<div class="row">
    <?php Pjax::begin(['id' => 'products']) ?>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'price',
            [
                'class' => \yii\grid\ActionColumn::className(),
                'buttons'=>[
                    'add_card'=>function ($url, $model) {
                        $basket_url=Yii::$app->getUrlManager()->createUrl(['basket/add','id'=>$model['id']]);
                        return \yii\helpers\Html::a( '<span class="glyphicon glyphicon-shopping-cart basket-btn" data-url='.$basket_url.'></span>', 'javascript:void(0)',
                            ['title' => Yii::t('yii', 'В корзину'), 'data-pjax'=>'0',]);
                    }
                ],
                'template'=>'{add_card}',
            ]
        ],
    ]); ?>
    <?php Pjax::end() ?>
</div>
