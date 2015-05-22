<?php
/**
 * Base class for Active Records
 *
 * @version    1.0
 * @package    database
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
abstract class TRecord
{
    protected $data;  // array containing the data of the object
    protected $attributes; // array of attributes
    
    /**
     * Class Constructor
     * Instantiates the Active Record
     * @param [$id] Optional Object ID, if passed, load this object
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        $this->attributes = array();
        
        if ($id) // if the user has informed the $id
        {
            // load the object identified by ID
            if ($callObjectLoad)
            {
                $object = $this->load($id);
            }
            else
            {
                $object = self::load($id);
            }
            
            if ($object)
            {
                $this->fromArray($object->toArray());
            }
            else
            {
                throw new Exception(TAdiantiCoreTranslator::translate('Object ^1 not found in ^2', $id, constant(get_class($this).'::TABLENAME')));
            }
        }
    }
    
    /**
     * Executed when the programmer clones an Active Record
     * In this case, we have to clear the ID, to generate a new one
     */
    public function __clone()
    {
        $pk = $this->getPrimaryKey();
        unset($this->$pk);
    }
    
    /**
     * Executed whenever a property is accessed
     * @param $property Name of the object property
     * @return          The value of the property
     */
    public function __get($property)
    {
        // check if exists a method called get_<property>
        if (method_exists($this, 'get_'.$property))
        {
            // execute the method get_<property>
            return call_user_func(array($this, 'get_'.$property));
        }
        else
        {
            if (strpos($property, '->') !== FALSE)
            {
                $parts = explode('->', $property);
                $container = $this;
                foreach ($parts as $part)
                {
                    if (is_object($container))
                    {
                        $result = $container->$part;
                        $container = $result;
                    }
                    else
                    {
                        throw new Exception(TAdiantiCoreTranslator::translate('Trying to access a non-existent property (^1)', $property));
                    }
                }
                return $result;
            }
            else
            {
                // returns the property value
                if (isset($this->data[$property]))
                {
                    return $this->data[$property];
                }
            }
        }
    }
    
    /**
     * Executed whenever a property is assigned
     * @param $property Name of the object property
     * @param $value    Value of the property
     */
    public function __set($property, $value)
    {
        if ($property == 'data')
        {
            throw new Exception(TAdiantiCoreTranslator::translate('Reserved property name (^1) in class ^2', $property, get_class($this)));
        }
        
        // check if exists a method called set_<property>
        if (method_exists($this, 'set_'.$property))
        {
            // executed the method called set_<property>
            call_user_func(array($this, 'set_'.$property), $value);
        }
        else
        {
            if ($value === NULL)
            {
                unset($this->data[$property]);
            }
            else
            {
                // assign the property's value
                $this->data[$property] = $value;
            }
        }
    }
    
    /**
     * Returns if a property is assigned
     * @param $property Name of the object property
     */
    public function __isset($property)
    {
        return isset($this->data[$property]) or method_exists($this, 'get_'.$property);
    }
    
    /**
     * Unset a property
     * @param $property Name of the object property
     */
    public function __unset($property)
    {
        unset($this->data[$property]);
    }
    
    /**
     * Returns the name of database entity
     * @return A String containing the name of the entity
     */
    protected function getEntity()
    {
        // get the Active Record class name
        $class = get_class($this);
        // return the TABLENAME Active Record class constant
        return constant("{$class}::TABLENAME");
    }
    
    /**
     * Returns the the name of the primary key for that Active Record
     * @return A String containing the primary key name
     */
    private function getPrimaryKey()
    {
        // get the Active Record class name
        $class = get_class($this);
        // returns the PRIMARY KEY Active Record class constant
        return constant("{$class}::PRIMARYKEY");
    }
    
    /**
     * Returns the the name of the sequence for primary key
     * @return A String containing the sequence name
     */
    private function getSequenceName()
    {
        // get the Active Record class name
        $class = get_class($this);
        
        if (defined("{$class}::SEQUENCE"))
        {
            return constant("{$class}::SEQUENCE");
        }
        else
        {
            return $this->getEntity().'_'. $this->getPrimaryKey().'_seq';
        }
    }
    
    /**
     * Fill the Active Record properties from another Active Record
     * @param $object An Active Record
     */
    public function mergeObject(TRecord $object)
    {
        $data = $object->toArray();
        foreach ($data as $key => $value)
        {
            $this->data[$key] = $value;
        }
    }
    
    /**
     * Fill the Active Record properties from an indexed array
     * @param $data An indexed array containing the object properties
     */
    public function fromArray($data)
    {
        $this->data = $data;
    }
    
    /**
     * Return the Active Record properties as an indexed array
     * @return An indexed array containing the object properties
     */
    public function toArray()
    {
        return $this->data;
    }
    
    /**
     * Limit the attributes that will be stored in the Active Record
     */
    public function addAttribute($attribute)
    {
        if ($attribute == 'data')
        {
            throw new Exception(TAdiantiCoreTranslator::translate('Reserved property name (^1) in class ^2', $attribute, get_class($this)));
        }
        
        $this->attributes[] = $attribute;
    }
    
    /**
     * Store the objects into the database
     * @return      The number of affected rows
     * @exception   Exception if there's no active transaction opened
     */
    public function store()
    {
        // get the Active Record class name
        $class = get_class($this);
        
        // check if the object has an ID or exists in the database
        $pk = $this->getPrimaryKey();
        
        if (empty($this->data[$pk]) or (!self::load($this->$pk)))
        {
            // increments the ID
            if (empty($this->data[$pk]))
            {
                if ((defined("{$class}::IDPOLICY")) AND (constant("{$class}::IDPOLICY") == 'serial'))
                {
                    unset($this->$pk);
                }
                else
                {
                    $this->$pk = $this->getLastID() +1;
                }
            }
            // creates an INSERT instruction
            $sql = new TSqlInsert;
            $sql->setEntity($this->getEntity());
            // iterate the object data
            foreach ($this->data as $key => $value)
            {
                // check if the field is a calculated one
                if ( !method_exists($this, 'get_' . $key) OR (count($this->attributes) > 0) )
                {
                    if (count($this->attributes) > 0)
                    {
                        // set just attributes defined by the addAttribute()
                        if ((in_array($key, $this->attributes)) OR ($key == $pk))
                        {
                            // pass the object data to the SQL
                            $sql->setRowData($key, $this->$key);
                        }
                    }
                    else
                    {
                        // pass the object data to the SQL
                        $sql->setRowData($key, $this->$key);
                    }
                }
            }
        }
        else
        {
            // creates an UPDATE instruction
            $sql = new TSqlUpdate;
            $sql->setEntity($this->getEntity());
            // creates a select criteria based on the ID
            $criteria = new TCriteria;
            $criteria->add(new TFilter($pk, '=', $this->$pk));
            $sql->setCriteria($criteria);
            // interate the object data
            foreach ($this->data as $key => $value)
            {
                if ($key !== $pk) // there's no need to change the ID value
                {
                    // check if the field is a calculated one
                    if ( !method_exists($this, 'get_' . $key) OR (count($this->attributes) > 0) )
                    {
                        if (count($this->attributes) > 0)
                        {
                            // set just attributes defined by the addAttribute()
                            if ((in_array($key, $this->attributes)) OR ($key == $pk))
                            {
                                // pass the object data to the SQL
                                $sql->setRowData($key, $this->$key);
                            }
                        }
                        else
                        {
                            // pass the object data to the SQL
                            $sql->setRowData($key, $this->$key);
                        }
                    }
                }
            }
        }
        // get the connection of the active transaction
        if ($conn = TTransaction::get())
        {
            // register the operation in the LOG file
            // (if the user has registered a TLogger)
            TTransaction::log($sql->getInstruction());
            $result = $conn->exec($sql->getInstruction());
            
            if ((defined("{$class}::IDPOLICY")) AND (constant("{$class}::IDPOLICY") == 'serial'))
            {
                if ($sql instanceof TSqlInsert)
                {
                    $this->$pk = $conn->lastInsertId( $this->getSequenceName() );
                }
            }
            
            // test if the cache is enabled
            if ((defined("{$class}::OBJECTCACHE")) AND (constant("{$class}::OBJECTCACHE") == TRUE) AND extension_loaded('apc'))
            {
                apc_store($class . '['. $this->$pk . ']', $this->toArray());
                TTransaction::log($class . '['. $this->$pk . '] stored in cache');
            }
            
            // return the result of the exec() method
            return $result;
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception(TAdiantiCoreTranslator::translate('No active transactions') . ': ' . __METHOD__ .' '. $this->getEntity());
        }
    }
    
    /**
     * ReLoad an Active Record Object from the database
     */
    public function reload()
    {
        // discover the primary key name 
        $pk = $this->getPrimaryKey();
        
        $this->load($this->$pk);
    }
    
    /**
     * Load an Active Record Object from the database
     * @param $id  The object ID
     * @return     The Active Record Object
     * @exception  Exception if there's no active transaction opened
     */
    public function load($id)
    {
        // get the Active Record class name
        $class = get_class($this);
        // discover the primary key name 
        $pk = $this->getPrimaryKey();
        if ((defined("{$class}::OBJECTCACHE")) AND (constant("{$class}::OBJECTCACHE") == TRUE) AND extension_loaded('apc'))
        {
            if ($fetched_object_array = apc_fetch($class . '['. $id . ']'))
            {
                $fetched_object = clone $this;
                $fetched_object->fromArray($fetched_object_array);
                TTransaction::log($class . '['. $id . '] loaded from cache');
                return $fetched_object;
            }
        }
        // creates a SELECT instruction
        $sql = new TSqlSelect;
        $sql->setEntity($this->getEntity());
        $sql->addColumn('*');
        
        // creates a select criteria based on the ID
        $criteria = new TCriteria;
        $criteria->add(new TFilter($pk, '=', $id));
        // define the select criteria
        $sql->setCriteria($criteria);
        // get the connection of the active transaction
        if ($conn = TTransaction::get())
        {
            // register the operation in the LOG file
            TTransaction::log($sql->getInstruction());
            $result= $conn->Query($sql->getInstruction());
            // if there's a result
            if ($result)
            {
                // returns the data as an object of this class
                $object = $result->fetchObject(get_class($this));
            }
            return $object;
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception(TAdiantiCoreTranslator::translate('No active transactions') . ': ' . __METHOD__ .' '. $this->getEntity());
        }
    }
    
    /**
     * Delete an Active Record object from the database
     * @param [$id]     The Object ID
     * @exception       Exception if there's no active transaction opened
     */
    public function delete($id = NULL)
    {
        $class = get_class($this);
        
        // discover the primary key name
        $pk = $this->getPrimaryKey();
        // if the user has not passed the ID, take the object ID
        $id = $id ? $id : $this->$pk;
        // creates a DELETE instruction
        $sql = new TSqlDelete;
        $sql->setEntity($this->getEntity());
        
        // creates a select criteria
        $criteria = new TCriteria;
        $criteria->add(new TFilter($pk, '=', $id));
        // assign the criteria to the delete instruction
        $sql->setCriteria($criteria);
        
        // get the connection of the active transaction
        if ($conn = TTransaction::get())
        {
            // register the operation in the LOG file
            TTransaction::log($sql->getInstruction());
            $result = $conn->exec($sql->getInstruction());
            
            unset($this->data);
            
            if ((defined("{$class}::OBJECTCACHE")) AND (constant("{$class}::OBJECTCACHE") == TRUE) AND extension_loaded('apc'))
            {
                apc_delete($class . '['. $id . ']');
                TTransaction::log($class . '['. $id . '] deleted from cache');
            }
            
            // return the result of the exec() method
            return $result;
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception(TAdiantiCoreTranslator::translate('No active transactions') . ': ' . __METHOD__ .' '. $this->getEntity());
        }
    }
    
    /**
     * Returns the FIRST Object ID from database
     * @return      An Integer containing the FIRST Object ID from database
     * @exception   Exception if there's no active transaction opened
     */
    public function getFirstID()
    {
        $pk = $this->getPrimaryKey();
        
        // get the connection of the active transaction
        if ($conn = TTransaction::get())
        {
            // instancia instrução de SELECT
            $sql = new TSqlSelect;
            $sql->addColumn("min({$pk}) as {$pk}");
            $sql->setEntity($this->getEntity());
            // register the operation in the LOG file
            TTransaction::log($sql->getInstruction());
            $result= $conn->Query($sql->getInstruction());
            // retorna os dados do banco
            $row = $result->fetch();
            return $row[0];
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception(TAdiantiCoreTranslator::translate('No active transactions') . ': ' . __METHOD__ .' '. $this->getEntity());
        }
    }
    
    /**
     * Returns the LAST Object ID from database
     * @return      An Integer containing the LAST Object ID from database
     * @exception   Exception if there's no active transaction opened
     */
    public function getLastID()
    {
        $pk = $this->getPrimaryKey();
        
        // get the connection of the active transaction
        if ($conn = TTransaction::get())
        {
            // instancia instrução de SELECT
            $sql = new TSqlSelect;
            $sql->addColumn("max({$pk}) as {$pk}");
            $sql->setEntity($this->getEntity());
            // register the operation in the LOG file
            TTransaction::log($sql->getInstruction());
            $result= $conn->Query($sql->getInstruction());
            // retorna os dados do banco
            $row = $result->fetch();
            return $row[0];
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception(TAdiantiCoreTranslator::translate('No active transactions') . ': ' . __METHOD__ .' '. $this->getEntity());
        }
    }
    
    /**
     * Method getObjects
     * @param $criteria Optional criteria
     * @returns Array of Active Records within $criteria
     */
    public static function getObjects($criteria = NULL)
    {
        // get the Active Record class name
        $class = get_called_class();
        
        // create the repository
        $repository = new TRepository( $class );
        if(!$criteria)
        {
            $criteria = new TCriteria;
        }
        
        return $repository->load( $criteria );
    }
    
    /**
     * Load composite objects (parts in composition relationship)
     * @param $composite_class Active Record Class for composite objects
     * @param $foreign_key Foreign key in composite objects
     * @param $id Primary key of parent object
     * @returns Array of Active Records
     */
    public function loadComposite($composite_class, $foreign_key, $id)
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter($foreign_key, '=', $id));
        
        $repository = new TRepository($composite_class);
        $objects = $repository->load($criteria);
        return $objects;
    }

    /**
     * Delete composite objects (parts in composition relationship)
     * @param $composite_class Active Record Class for composite objects
     * @param $foreign_key Foreign key in composite objects
     * @param $id Primary key of parent object
     */
    public function deleteComposite($composite_class, $foreign_key, $id)
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter($foreign_key, '=', $id));
        
        $repository = new TRepository($composite_class);
        return $repository->delete($criteria);
    }
    
    /**
     * Save composite objects (parts in composition relationship)
     * @param $composite_class Active Record Class for composite objects
     * @param $foreign_key Foreign key in composite objects
     * @param $id Primary key of parent object
     * @param $objects Array of Active Records to be saved
     */
    public function saveComposite($composite_class, $foreign_key, $id, $objects)
    {
        $this->deleteComposite($composite_class, $foreign_key, $id);
        
        if ($objects)
        {
            foreach ($objects as $object)
            {
                $object-> $foreign_key  = $id;
                $object->store();
            }
        }
    }
    
    /**
     * Load aggregated objects (parts in aggregation relationship)
     * @param $aggregate_class Active Record Class for aggregated objects
     * @param $join_class Active Record Join Class (Parent / Aggregated)
     * @param $foreign_key_parent Foreign key in Join Class to parent object
     * @param $foreign_key_child Foreign key in Join Class to child object
     * @param $id Primary key of parent object
     * @returns Array of Active Records
     */
    public function loadAggregate($aggregate_class, $join_class, $foreign_key_parent, $foreign_key_child, $id)
    {
        $criteria   = new TCriteria;
        $criteria->add(new TFilter($foreign_key_parent, '=', $id));
        
        $repository = new TRepository($join_class);
        $objects = $repository->load($criteria);
        
        $aggregates = array();
        if ($objects)
        {
            foreach ($objects as $object)
            {
                $aggregates[] = new $aggregate_class($object-> $foreign_key_child);
            }
        }
        return $aggregates;
    }
    
    /**
     * Save aggregated objects (parts in aggregation relationship)
     * @param $join_class Active Record Join Class (Parent / Aggregated)
     * @param $foreign_key_parent Foreign key in Join Class to parent object
     * @param $foreign_key_child Foreign key in Join Class to child object
     * @param $id Primary key of parent object
     * @param $objects Array of Active Records to be saved
     */
    public function saveAggregate($join_class, $foreign_key_parent, $foreign_key_child, $id, $objects)
    {
        $this->deleteComposite($join_class, $foreign_key_parent, $id);
        
        if ($objects)
        {
            foreach ($objects as $object)
            {
                $join = new $join_class;
                $join-> $foreign_key_parent = $id;
                $join-> $foreign_key_child  = $object->id;
                $join->store();
            }
        }
    }
}
?>
