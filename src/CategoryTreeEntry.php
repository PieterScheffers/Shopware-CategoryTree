<?php

namespace pisc\Shopware\CategoryTree;

class CategoryTreeEntry
{
	/**
	 * @var array
	 */
	protected $attributes;

	/**
	 * @var CategoryTreeEntry
	 */
	protected $parent;

	/**
	 * @var array[CategoryTreeEntry]
	 */
	protected $children = [];

	public function __construct($attributes, CategoryTreeEntry $parent = null)
	{
		if(!isset($attributes['id'])) throw new Exception('attributes should contain an id key');
		if(!isset($attributes['description'])) throw new Exception('attributes should contain an description key');

		$this->attributes = $attributes;
		$this->parent = $parent;
	}

	public function getId()
	{
		return $this->attributes['id'];
	}

	public function getDescription()
	{
		return $this->attributes['description'];
	}

	public function getAttributes()
	{
		return $this->attributes;
	}

	public function addChild(CategoryTreeEntry $child)
	{
		$this->children[$child->getId()] = $child;
	}

	public function getParent()
	{
		return $this->parent;
	}

	public function getDeepParents()
	{
		$parent = $this;

		$parents = [];

		while($parent = $parent->getParent())
		{
			$parents[$parent->getId()] = $parent;
		}

		return $parents;
	}

	public function hasDeepParent($id)
	{
		$parents = $this->getDeepParents();
		return isset($parents[$id]);
	}

	public function getChildren()
	{
		return $this->children;
	}

	public function getDeepChildren()
	{
		return array_reduce($this->children, function($children, $child) {
			return array_merge($children, $child->getDeepChildren());
		}, $this->children);
	}

	public function hasChild($id)
	{
		return isset($this->children[$id]);
	}

	public function hasDeepChild($id)
	{
		$children = $this->getDeepChildren();
		return isset($children[$id]);
	}

	public function getPaths($onlyLeaves = false)
	{
		$ownPath = [ $this->getId() => $this ];
		if( count($this->children) <= 0 ) return [ $ownPath ];

		return array_reduce($this->children, function($paths, $child) use ($ownPath) {
			return array_merge(
				$paths,
				array_map(function($path) use ($ownPath) {
					return array_merge($ownPath, $path);
				}, $child->getPaths())
			);
		}, $onlyLeaves ? [] : [ $ownPath ]);
	}
}