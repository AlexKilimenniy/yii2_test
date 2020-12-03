<?php

namespace app\models;

use app\models\search\ProductSearch;
use yii\behaviors\TimestampBehavior;

class Basket extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'basket';
    }
    public function behaviors(){
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['session_id'], 'required'],
            [['user_id', 'created_at', 'updated_at'], 'integer'],
            [['ip'], 'string', 'max' => 16],
            [['products'], 'string']
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'            => "ID",
            'cookie_id'     => "Cookie",
            'user_id'       => "Клиент",
            'products'      => "Тоавры",
            'created_at'    => 'Перв. действие',
            'updated_at'    => 'Посл. действие',
        ];
    }


    public function add($id)
    {
        $id = abs((int)$id);
        $product = $this->getProduct($id);
        if (!$product) return False;

        $basket_products = $this->getUserBasketProducts();
        if (!isset($basket_products[$product->id])){
            $basket_products[$product->id] = array(
                'name' => $product->name,
                'price' => $product->price,
            );
        }
        $this->saveBasketProducts($basket_products);
        return True;
    }

    public function remove($id)
    {
        $id = abs((int)$id);
        $product = $this->getProduct($id);
        if (!$product) return False;

        $basket_products = $this->getUserBasketProducts();
        if (isset($basket_products[$product->id])){
            unset($basket_products[$product->id]);
        }
        $this->saveBasketProducts($basket_products);
        return True;
    }

    private function getProduct($id){
        if (!is_numeric($id)) return FALSE;
        return ProductSearch::findOne($id);
    }

    public function getUserBasketSession()
    {
        $session = \Yii::$app->session;
        $session->open();
        if (!$session->has('basket')) {
            $session->set('basket', []);
            $basket = [];
        }else {
            $basket = $session->get('basket');
        }
        $session_id = md5(\Yii::$app->request->userIP);
        if (!isset($basket[$session_id])){
            $basket[$session_id] =  [
               'user_id' => \Yii::$app->user->identity->id,
               'ip' => \Yii::$app->request->userIP,
               'created_at' => time(),
               'updated_at' => Null,
               'products' => Null,
               'total' => 0,
               'session_id' => $session_id,
           ];

           $session->set('basket', $basket);
        }
        $user_basket = $session->get('basket')[$session_id];
        $session->close();

        return $user_basket;
    }

    private function saveBasketProducts($products)
    {
        $session = \Yii::$app->session;
        $session->open();
        $session_id = md5(\Yii::$app->request->userIP);

        $user_basket = $this->getUserBasketSession();

        $user_basket['updated_at'] = time();
        $total_price = 0;
        foreach ($products as $product){
            $total_price += (float)$product['price'];
        }

        $user_basket['products'] = json_encode($products);
        $user_basket['total'] = $total_price;

        $basket[$session_id] = $user_basket;
        $session->set('basket', $basket);
        $session->close();
    }

    public function dropUserBasketSession()
    {
        $session = \Yii::$app->session;
        $session->open();
        if (!$session->has('basket')) {
            $session->set('basket', []);
            $basket = [];
        } else {
            $basket = $session->get('basket');
        }
        $session_id = md5(\Yii::$app->request->userIP);
        if (isset($basket[$session_id])){
            $basket[$session_id] =  [
                'user_id' => \Yii::$app->user->identity->id,
                'ip' => \Yii::$app->request->userIP,
                'created_at' => time(),
                'updated_at' => Null,
                'products' => Null,
                'total' => 0,
                'session_id' => $session_id,
            ];

            $session->set('basket', $basket);
        }
        $user_basket = $session->get('basket')[$session_id];
        $session->close();

        return $user_basket;
    }

    public function getUserBasketProducts()
    {
        $basket = $this->getUserBasketSession();
        return json_decode($basket['products'], true);
    }

}
