<?php

namespace pisc\Shopware\CategoryTree;

class CategoryTree
{
	/**
	 * @var array
	 */
	protected $categoryIndex;

	public function __construct($categories = [])
	{
		$this->constructTree($categories);
	}

	public function constructTree($categories = [])
	{
		// sort categories on parent id
		usort($categories, function($c1, $c2) {
			if( $c1['parent'] === $c2['parent'] )
			{
				return 0;
			}

			return $c1['parent'] < $c2['parent'] ? -1 : 1;
		});

		// while array_pop
		// check parent exist
		//   add parent
		//   add as child of parent
		while($category = array_shift($categories))
		{
			// find parent
			$parentId = $category['parent'];
			$parent = isset($this->categoryIndex[$parentId]) ? $this->categoryIndex[$parentId] : null;

			$entry = new CategoryTreeEntry($category, $parent);

			// add child to parent
			if(!is_null($parent)) $parent->addChild($entry);

			// add to index
			$this->categoryIndex[$entry->getId()] = $entry;
		}
	}

	public function getRoot()
	{
		$index = $this->categoryIndex;
		return count($index) > 0 ? array_shift($index) : null;
	}

	public function getLeaves()
	{
		return array_filter($this->categoryIndex, function($entry) {
			return count($entry->getChildren()) <= 0;
		});
	}

	public function getPaths($onlyLeaves = false)
	{
		$root = $this->getRoot();
		return is_null($root) ? [] : $root->getPaths($onlyLeaves);
	}

	public function getById($id)
	{
		return isset($this->categoryIndex[$id]) ? $this->categoryIndex[$id] : null;
	}
}