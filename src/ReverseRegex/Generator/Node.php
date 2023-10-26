<?php
namespace ReverseRegex\Generator;

use ArrayObject;
use Closure;
use SplObjectStorage;
use ArrayAccess;
use Countable;
use Iterator;

/**
  *  Base to all Generator Scopes 
  *
  *  @author Lewis Dyer <getintouch@icomefromthenet.com>
  *  @since 0.0.1
  */
class Node implements ArrayAccess, Countable, Iterator
{
    /**
      *  @var string name of the node 
      */
    protected $label;
    
    /**
      *  @var ArrayObject container for node metadata 
      */
    protected $attrs;
    
    /**
      *  @var SplObjectStorage container for node relationships 
      */
    protected $links;

    /**
      *  Class Constructor
      *
      *  @access public
      *  @param string $label
      */
    public function __construct(string $label = 'node')
    {
        $this->attrs = new ArrayObject();
        $this->links = new SplObjectStorage();

        $this->setLabel($label);
    }

    /**
      *  Fetch the nodes label
      *
      *  @access public
      *  @return string the nodes label
      */
    public function getLabel(): string
	{
        return $this->label;
    }

    /**
      *  Sets the node label
      *
      *  @access public
      *  @param string $label the nodes label
      */
    public function setLabel(string $label)
    {
        if (!(is_scalar($label) || is_null($label))) {
            return false;
        }

        $this->label = $label;
    }


    /**
      *  Attach a node
      *
      *  @access public
      *  @param Node $node the node to attach
      *  @return Node
      */
    public function &attach(Node $node): Node
	{
        $this->links->attach($node);

        return $this;
    }

    /**
      *  Detach a node
      *
      *  @access public
      *  @return Node
      *  @param Node $node the node to remove
      */
    public function &detach(Node $node): Node
	{
        foreach ($this->links as $linked_node) {
            if ($linked_node == $node) {
                $this->links->detach($node);
            }
        }

        return $this;
    }

    /**
      *  Search for node in its relations
      *
      *  @access public
      *  @return boolean true if found
      *  @param Node $node the node to search for
      */
    public function contains(Node $node): bool
	{
        foreach ($this->links as $linked_node) {
            if ($linked_node == $node) {
                return true;
            }
        }

        return false;
    }
  
   /**
     *  Apply a closure to all relations
     *
     *  @access public
     *  @param Closure the function to apply
     */
    public function map(Closure $function): void
    {
        foreach ($this->links as $node) {
            $function($node);
        }
    }
    
    //------------------------------------------------------------------
    # Countable
    
    public function count(): int
	{
        return count($this->links);
    }
    
    //------------------------------------------------------------------
    # Iterator

    public function current(): mixed
    {
        return $this->links->current();
    }
    public function key(): mixed
    {
        $this->links->key();
    }
    public function next(): void
    {
        $this->links->next();
    }
    public function rewind(): void
    {
        $this->links->rewind();
    }
    public function valid(): bool
	{
        return $this->links->valid();
    }
    
    //------------------------------------------------------------------
    # ArrayAccess Implementation

    public function offsetGet(mixed $offset): mixed
    {
        return $this->attrs->offsetGet($offset);
    }

    public function offsetSet(mixed $offset, $value): void
    {
        $this->attrs->offsetSet($offset, $value);
    }

    public function offsetExists(mixed $offset): bool
	{
        return $this->attrs->offsetExists($offset);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->attrs->offsetUnset($offset);
    }
}

/* End of Class */