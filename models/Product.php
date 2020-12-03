<?php

namespace app\models;

class Product extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['price'], 'number']
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => "ID",
            'name' => "Наименование",
            'price' => "Цена",
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function getCount(){
        return $this->find()->count();
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return isset(self::$items[$id]) ? new static(self::$items[$id]) : null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByName($name)
    {
        foreach (self::$items as $item) {
            if (strcasecmp($item['name'], $name) === 0) {
                return new static($item);
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }


}
