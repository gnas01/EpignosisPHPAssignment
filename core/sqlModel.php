<?php

require_once "./connection.php";

abstract class SQLModel 
{
    public int $id = 0;

    abstract public static function getTableName();

    public static function getPrimaryKeyName()
    {
        return 'id';
    }

    public function save()
    {
        global $database;

        $tableName = $this->getTableName();
        $attributes = get_object_vars($this);

        //exlude id as it is autoincremented in the database
        unset($attributes[$this->getPrimaryKeyName()]);

        //INSERT INTO users_details (id, user_id, first_name, last_name, is_admin) VALUES (:id, :user_id, :first_name, :last_name, :is_admin)
        $stmt = $database->prepare("INSERT INTO $tableName (" . implode(', ', array_keys($attributes)) . ") VALUES (:" . implode(', :', array_keys($attributes)) . ")");
        $bindedAttributes = array_combine(array_map(function($key) { return ":$key"; }, array_keys($attributes)), array_values($attributes));
        $stmt->execute($bindedAttributes);

        //update the model's self with the new id
        $this->id = $database->lastInsertId();
    }

    public static function findOne($filter)
    {
        global $database;

        $tableName = static::getTableName();
        $attributes = get_class_vars(get_class(new static()));

        $stmt = $database->prepare("SELECT * FROM $tableName WHERE ".$filter);
            
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row)
        {
            $model = new static();

            foreach($attributes as $key => $value)
            {
                $model->$key = $row[$key];
            }

            return $model;
        }
        else
        {
            return null;
        }
    }
            
    public static function findAll()
    {
        global $database;

        $tableName = static::getTableName();
        $attributes = get_class_vars(get_class(new static()));

        $stmt = $database->prepare("SELECT * FROM $tableName");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $models = [];

        foreach($rows as $row)
        {
            $model = new static();

            foreach($attributes as $key => $value)
            {
                $model->$key = $row[$key];
            }

            $models[] = $model;
        }

        return $models;
    }

    public static function find($filter)
    {
        global $database;

        $tableName = static::getTableName();
        $attributes = get_class_vars(get_class(new static()));

        $stmt = $database->prepare("SELECT * FROM $tableName WHERE ".$filter);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $models = [];

        foreach($rows as $row)
        {
            $model = new static();

            foreach($attributes as $key => $value)
            {
                $model->$key = $row[$key];
            }

            $models[] = $model;
        }

        return $models;
    }

    public static function findOneAndUpdate($filter, $data)
    {

        $dataKeys = array_keys($data);
        $dataValues = array_values($data);

        global $database;

        $tableName = static::getTableName();

        /*Using ? instead of :value
        due to the format of the update statement:
        value = :value.
        When using implode all the values will be satisfied 
        but the last one: fist_name = ?, last_name = ?, is_admin
        Hence, using ? we can simply concatenate it one last time in the string */
        $stmt = $database->prepare("UPDATE $tableName SET ".implode(' = ?, ', $dataKeys)." = ? WHERE ".$filter);

        $stmt->execute($dataValues);

        if($stmt->rowCount())
        {
            return self::findOne($filter);
        }

        return null;
    }
}

?>