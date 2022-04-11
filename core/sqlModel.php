<?php

/**contains all core components*/
namespace core;

use PDO;

require_once "connection.php";

/** Base class for all the models,
 * contains the basic CRUD operations.
 * When inherited, the variables of the child class
 * will automatically be proccessed by the get_object_vars()
 * function by passing the current instance as the argument.
 */
abstract class SQLModel 
{
    /** The primary key id value of the table */
    public int $id = 0;

    /** The name of the table, meant to be overrided by child class */
    abstract public static function getTableName() : string;

    /** Returns the name of the primary key 
     * ALL MODELS MUST USE THE SAME PRIMARY KEY NAME
    */
    public static function getPrimaryKeyName(): string
    {
        return 'id';
    }

    /** Saves the model instance to the databse (insert operation) */
    public function save(): void
    {
        $tableName = $this->getTableName();
        $attributes = get_object_vars($this);

        //exlude id as it is autoincremented in the database
        unset($attributes[$this->getPrimaryKeyName()]);

        //INSERT INTO users_details (id, user_id, first_name, last_name, is_admin) VALUES (:id, :user_id, :first_name, :last_name, :is_admin)
        $stmt = Database::getConnection()->prepare("INSERT INTO $tableName (" . implode(', ', array_keys($attributes)) . ") VALUES (:" . implode(', :', array_keys($attributes)) . ")");
        $bindedAttributes = array_combine(array_map(function($key) { return ":$key"; }, array_keys($attributes)), array_values($attributes));
        $stmt->execute($bindedAttributes);

        //update the model's self with the new id
        $this->id = Database::getConnection()->lastInsertId();
    }

    /** Finds one record of the model based on the filters provided 
     * @param array $filter The filters to be used in the query, 
     * the array that will be passed must have 2 keys, "conditions" which will
     * describe the operation in the form of a prepared statement and "bind" which
     * will contain the values that will be binded accordingly.
     * 
     * @return SQLModel|null The model instance if found, null otherwise.
    */
    public static function findOne(array $filter) : ?SQLModel
    {
        $tableName = static::getTableName();
        $attributes = get_class_vars(get_class(new static()));

        $stmt = Database::getConnection()->prepare("SELECT * FROM $tableName WHERE " . $filter['conditions']);

        $stmt->execute($filter['bind']);
            
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row)
        {
            $model = new static();

            foreach(array_keys($attributes) as $key)
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
    
    /** Finds all the records of a model
     * @return array An array of SQLModel instances
     */
    public static function findAll() : array
    {
        $tableName = static::getTableName();
        $attributes = get_class_vars(get_class(new static()));

        $stmt = Database::getConnection()->prepare("SELECT * FROM $tableName");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $models = [];

        foreach($rows as $row)
        {
            $model = new static();

            foreach(array_keys($attributes) as $key)
            {
                $model->$key = $row[$key];
            }

            $models[] = $model;
        }

        return $models;
    }

    /** Finds all the records of the model based on the filters provided 
     * @param array $filter The filters to be used in the query, 
     * the array that will be passed must have 2 keys, "conditions" which will
     * describe the operation in the form of a prepared statement and "bind" which
     * will contain the values that will be binded accordingly.
     * 
     * @return array An array of SQLModel instances.
    */
    public static function find(array $filter): array
    {
        $tableName = static::getTableName();
        $attributes = get_class_vars(get_class(new static()));

        $stmt = Database::getConnection()->prepare("SELECT * FROM $tableName WHERE " . $filter['conditions']);

        $stmt->execute($filter['bind']);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $models = [];

        foreach($rows as $row)
        {
            $model = new static();

            foreach(array_keys($attributes) as $key)
            {
                $model->$key = $row[$key];
            }

            $models[] = $model;
        }

        return $models;
    }

    /**
     * Updates the model instance in the database based on the filters provided
     * @param array $filter The filters to be used in the query,
     * the array that will be passed must have 2 keys, "conditions" which will
     * describe the operation in the form of a prepared statement and "bind" which
     * will contain the values that will be binded accordingly.
     * 
     * @param array $data The data to be updated.
     * 
     * @return SQLModel|null The model instance that was updated, null otherwise.
     */
    public static function findOneAndUpdate(array $filter, array $data): ?SQLModel
    {

        $dataKeys = array_keys($data);
        $dataValues = array_values($data);


        $tableName = static::getTableName();

        /*Using ? instead of :value
        due to the format of the update statement:
        value = :value.
        When using implode all the values will be satisfied 
        but the last one: fist_name = ?, last_name = ?, is_admin
        Hence, using ? we can simply concatenate it one last time in the string */
        $stmt = Database::getConnection()->prepare("UPDATE $tableName SET ".implode(' = ?, ', $dataKeys)." = ? WHERE ".$filter['conditions']);
        
        $bindedData = array_merge($dataValues, $filter['bind']);

        $stmt->execute($bindedData);

        if($stmt->rowCount())
        {
            return self::findOne($filter);
        }

        return null;
    }
}

?>