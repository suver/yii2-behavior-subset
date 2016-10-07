<?php

namespace suver\behavior;

use yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * This is just an example.
 */
class Subset extends Behavior
{
    public $relation;
    public $relationMethod;
    public $attribute;
    public $_values;

    public function events() {
        return [
            //ActiveRecord::EVENT_INIT => 'init',
            ActiveRecord::EVENT_AFTER_INSERT => 'saveSubset',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'saveSubset',
            ActiveRecord::EVENT_AFTER_FIND => 'searchSubset',
            ActiveRecord::EVENT_AFTER_UPDATE => 'searchSubset',
        ];
    }

    public function init() {
    }

    public function attach($owner)
    {
        //$owner->setAttributes([$this->attribute => [] ]);
        parent::attach($owner);

        $this->relationMethod = 'get' . ucfirst($this->relation);
    }

    public function searchSubset() {

        if (is_array($ownerPk = $this->owner->getPrimaryKey())) {
            throw new ErrorException("This behavior does not support composite primary keys");
        }

        if(!method_exists($this->owner, $this->relationMethod)) {
            throw new yii\base\Exception("Incorrect relation");
        }

        //var_dump($this->owner->{$this->relationMethod}());exit;

        $realtion = $this->owner->{$this->relation};
        $this->owner->{$this->attribute} = yii\helpers\ArrayHelper::getColumn($realtion, 'id');


    }

    public function saveSubset() {

        if (is_array($ownerPk = $this->owner->getPrimaryKey())) {
            throw new ErrorException("This behavior does not support composite primary keys");
        }

        if(!method_exists($this->owner, $this->relationMethod)) {
            throw new yii\base\Exception("Incorrect relation");
        }

        $realtion = $this->owner->{$this->relationMethod}();

        $link = isset($realtion->link) ? $realtion->link : null;
        $via = isset($realtion->via) ? $realtion->via : null;
        $viaTable = isset($via) && isset($via->from) ? $via->from : null;
        $viaLink = isset($via) && isset($via->link) ? $via->link : null ;
        //var_dump($this->owner->primaryKey());

        foreach ($this->owner->primaryKey() as $key) {
            $primaryIds[$key] = $this->owner->{$key};
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {

            foreach($primaryIds as $k=>$v) {
                if($v) {
                    $viaKey = array_search($k, $viaLink);
                    $relationsLink[] = "{$viaKey}=:$viaKey";
                    $relationsParam[$viaKey] = $v;
                }
            }

            if($relationsLink) {
                Yii::$app->db->createCommand()
                    ->delete($viaTable[0], implode(" AND ", $relationsLink), $relationsParam)
                    ->execute();
            }



            if (!empty($this->_values[$this->attribute])) {

                foreach($primaryIds as $k=>$v) {
                    if($v) {
                        $viaKey = array_search($k, $viaLink);
                        $relationsCollumns[] = $viaKey;
                        $relationsRows[$viaKey] = $v;
                    }
                }


                if($relationsLink) {

                    $relationsCollumns[] = $link[key($link)];
                    $rows = [];

                    foreach($this->_values[$this->attribute] as $value) {
                        $row = $relationsRows;
                        $row[key($link)] = $value;
                        $rows[] = $row;
                    }

                    Yii::$app->db->createCommand()
                        ->batchInsert($viaTable[0], $relationsCollumns, $rows)
                        ->execute();
                }

            }

            $transaction->commit();
        } catch (\yii\db\Exception $ex) {
            $transaction->rollback();
            throw $ex;
        }

    }

    public function canSetProperty($name) {
        return ($name == $this->attribute) ? true : false;
    }

    public function canGetProperty($name, $checkVars=true) {
        return ($name == $this->attribute) ? true : false;
    }

    /**
     * @inheritdoc
     */
    public function __set($name, $value)
    {
        if ($name == $this->attribute) {
            $this->_values[$name] = $value;
        }
    }

    public function __get($name)
    {
        if (isset($this->_values[$name])) {
            return $this->_values[$name];
        }
    }
}
