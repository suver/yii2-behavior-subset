Frapse Behavior Subset
======================
Behavior Subset

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require suver/yii2-behavior-subset
```

or add

```
"suver/yii2-behavior-subset": "*"
```

to the require section of your `composer.json` file.

Configure
---------

Write you behaviors section like this for Many-To-Many

```php
public function behaviors()
{
    return [
        [
            'class' => '\suver\behavior\Subset',
            'relation' => 'authors', // you relation
            'attribute' => 'authors_ids',
        ]
    ];
}

/**
 * Relation with Other Model
 *
 * @return \yii\db\ActiveQuery
 */
public function getAuthors()
{
    return $this->hasMany(OtherModel::className(), ['id' => 'other_model_id'])->viaTable('this_model_to_other_model', ['this_model_id' => 'id']);
}
```

Usage
-----

Once the extension is installed, simply use it in your code by  :

```php

// save relation
$model->authors_ids = [1,2,3,4];
$model->save();

// get realtion
var_dump($model->authors)


```

# yii2-behavior-subset
Behavior subset. Esey work with many to many relations
