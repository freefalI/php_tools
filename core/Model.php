<?php

/**
 * ORM
 * 
 */

class Model
{
    protected $table;//static ?
    protected $primaryKey  = 'id';//static ?
    protected $isAutoInc = true;//static ?
    protected $isNew;
    protected $attributes = [];
    protected $builder;


    private function __construct($attrs = null, $isNew = true)
    {
        $this->isNew = $isNew;
        $this->setTable();
        $this->builder = SQL::table($this->table);
        if (isset($attrs))
            $this->attributes = $attrs;
    }

    private function setTable()
    {
        if (!isset ($this->table))
            $this->table = strtolower(static::class) . 's';
    }

    public static function __callStatic($methodName, $arguments)
    {
        $instance = new static();
        return call_user_func_array([$instance, $methodName], $arguments);
    }

    public function __call($methodName, $arguments)
    {
        $res = call_user_func_array([$this->builder, $methodName], $arguments);
        if ($res instanceof SQL)
            return $this;
        return $res;
    }
    public function __set($name, $value)
    {
        if (isset($this->$name))
            $this->$name = $value;
        else {
            if (isset($this->attributes[$name]))
                $this->attributes[$name] = $value;
            else
                throw  new Exception("Error: Non-existed field!");
        }
    }

    public function __get($name)
    {
        if (isset($this->$name))
            return $this->$name;
        else {
            if (isset($this->attributes[$name]))
                return $this->attributes[$name];
        }
        throw  new Exception("Error: Non-existed field!");
    } 
    
    public function get()
    {
        $records = $this->builder->get();
        if (count($records)>1){
            $models = [];
            foreach ($records as $rec)
                $models[] = new static($rec, false);
            return $models;
        }
        elseif(count($records)===1){
            return new static($records[0], false);
        } 
        else return null;
    }

    public function first()
    {
        $record = $this->builder->first();    
        return $record ? new static($record, false) : null;
    }

    public static function find($id)
    {
        $instance = new static();
        if(!is_array($id)){
            $model = $instance->where($instance->primaryKey  . " = " . $id)->first();
            return  $model;
        }
        else{
            $conds = [];
            foreach ($id as $i){
                $conds[] = $instance->primaryKey . " = " . $i; 
            }
            $cond = implode(' or ',$conds);
            return $instance->where($cond)->get();
        }
    }

    public static function all()
    {
        return (new static())->get();
    }

    public function save()
    {
        if (!$this->isNew) {
            $attrsWithoutPrimaryKey = $this->attributes;
            unset($attrsWithoutPrimaryKey[$this->primaryKey]);
            return $this->where($this->primaryKey  . " = " . $this->attributes[$this->primaryKey ])->update($attrsWithoutPrimaryKey);
        } 
    }

    public function delete(){
        $status = $this->builder->where($this->primaryKey  . " = " . $this->attributes[$this->primaryKey ])->delete();
        if ($status) {
            $this->isNew = true;
        }
        return $status;
    }

    public static function destroy($id){
        $instance = new static();
        return $instance->builder->where($instance->primaryKey  . " = " . $id)->delete();
    }

    public static function create($attrs){
        $instance = new static($attrs,true);
        if ($instance->isAutoInc){
            $status = $newId = $instance->insertGetId($instance->attributes);
            $instance->attributes[$instance->primaryKey] = $newId;
        }
        else{
            $status = $instance->insert($instance->$attributes);
        }
        if ($status){
            $instance->isNew = false;
        }
        return $status ? $instance : null;
    }

    // public function __toString()
    // {
    //     $a = '';
    //     foreach ($this->attributes as $key => $value)
    //         $a .= "\t" . $key . " => " . $value . "\n";
    //     return $a;
    // }
}
