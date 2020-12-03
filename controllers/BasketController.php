<?php

namespace app\controllers;

use app\models\Basket;
use app\models\Product;
use app\models\search\ProductSearch;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class BasketController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = new Basket();
        $user_basket = $model->getUserBasketSession();
        $products_data = [];
        $products = json_decode($user_basket['products'], true);
        $products = !$products ? [] : $products;
        foreach ($products as $id => $product){
            $products_data[] = array(
                'id' => $id,
                'name' => $product['name'],
                'price' => $product['price'],
            );
        }
        $total = $user_basket['total'];

        $dataProvider = new ArrayDataProvider([
            'allModels' => $products_data,
            'sort' => [
                'attributes' => ['name', 'price'],
            ],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'total' => $total,
            ]);
    }

    public function actionAdd($id)
    {
        $basket = new Basket();
        if (Yii::$app->getRequest()->isAjax) {
            if ($basket->add($id)){
                echo json_encode(['status' => 'ok']);
            } else {
                json_encode(['status' => 'error', 'text' => 'Что-то пошло не так!']);
            }
            die();
        }

        echo json_encode(['status' => 'error', 'text' => 'Что-то пошло не так!']);
        die();
    }

    public function actionRemove($id)
    {
        $basket = new Basket();
        if (Yii::$app->getRequest()->isAjax) {
            if ($basket->remove($id)){
                echo json_encode(['status' => 'ok']);
            } else {
                json_encode(['status' => 'error', 'text' => 'Что-то пошло не так!']);
            }
            die();
        }

        echo json_encode(['status' => 'error', 'text' => 'Что-то пошло не так!']);
        die();
    }

    public function actionSave()
    {
        $result = False;
        $model = new Basket();
        $user_basket = $model->getUserBasketSession();
        $model->session_id = $user_basket['session_id'];
        $model->user_id = $user_basket['user_id'];
        $model->created_at = $user_basket['created_at'];
        $model->updated_at = $user_basket['updated_at'];
        $model->ip = $user_basket['ip'];
        $model->products = $user_basket['products'];
        if ($model->save()){
            $model->dropUserBasketSession();
            $result = True;
        }
        if (Yii::$app->getRequest()->isAjax) {
            echo $result ? json_encode(['status' => 'ok']) : json_encode(['status' => 'error', 'text' => 'Что-то пошло не так!']);
            die();
        }
        return $result;

    }

}
