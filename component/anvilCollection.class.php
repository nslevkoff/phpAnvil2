<?php

require_once('anvilObject.abstract.php');


/**
 * Base Collection Class
 *
 * This class is used for managing collections of objects.
 *
 * @copyright       Copyright (c) 2010-2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilCollection extends anvilObjectAbstract implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * Version number for this class release.
     *
     */
    const VERSION = '1.0';


    private $_items = array();


    public function add($item, $key = null)
    {
        if ($key) {
            if (isset($this->_items[$key])) {
                //				throw new KeyInUseException("Key \"$key\" already in use!");
            } else {
                $this->_items[$key] = $item;
            }
        } else {
            $this->_items[] = $item;
        }
    }


    public function remove($key)
    {
        if (isset($this->_items[$key])) {
            unset($this->_items[$key]);
        }
    }


    public function exists($key)
    {
        //        FB::log($key, '$key');
        //        FB::log($this->_items, '$this->_items');
        return isset($this->_items[$key]);
    }


    public function keys()
    {
        return array_keys($this->_items);
    }


    public function contains($key)
    {
        return (array_key_exists($key, $this->_items));
    }


    /**
     * Moves the index pointer to the first item in the collection.
     *
     * @return mixed
     */
    public function moveFirst()
    {
        //		$this->index = 0;
        return reset($this->_items);
    }


    /**
     * Moves the index pointer to the next item in the collection.
     *
     * @return mixed The next item in the array, or false.
     */
    public function moveNext()
    {
        return next($this->_items);
    }


    /**
     * Moves the index pointer to the previous item in the collection.
     *
     * @return bool True if successful, otherwise false if there are no previous items in the collection.
     */
    public function movePrev()
    {
        return prev($this->_items);
    }


    /**
     * Moves the index pointer to the last item in the collection.
     *
     * @return mixed
     */
    public function moveLast()
    {
        return end($this->_items);
    }


    /**
     * Determin if there are more items in the collection after the current index.
     *
     * @return boolean True if there are more items after the current indexed item in the collection.
     */
    public function hasMore()
    {
        return current($this->_items);
    }


    /**
     * @return anvilControlAbstract
     */
    public function current()
    {
        return current($this->_items);
    }


    #==========================================
    #====  ArrayAccessInterface Functions =====

    public function offsetSet($offset, $value)
    {
        $this->_items[$offset] = $value;
    }


    public function offsetUnset($offset)
    {
        unset($this->_items[$offset]);
    }


    public function offsetGet($offset)
    {
        return isset($this->_items[$offset])
                ? $this->_items[$offset]
                : null;
    }


    public function offsetExists($offset)
    {
        return isset($this->_items[$offset]);
    }


    #==========================================
    #==== IteratorAggregate Interface Functions =====

    public function getIterator()
    {
        return new ArrayIterator($this->_items);
    }


    #==========================================
    #==== Countable Interface Functions =====

    /**
     * Returns the number of items within the collection.
     *
     * @return int Total number of items within the collection.
     */
    public function count()
    {
        return count($this->_items);
    }

}

?>
