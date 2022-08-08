<?php
class QueryCategory extends connect
{
  private $category;

  public function __construct()
  {
    parent::__construct();
  }

  public function setCategory(Category $category)
  {
    $this->category = $category;
  }
}

class Category
{
  private $id = null;
  private $name = null;

  public function getId()
  {
    return $this->id;
  }

  public function getName()
  {
    return $this->name;
  }

  public function setid($id)
  {
    $this->id = $id;
  }

  public function setName($name)
  {
    $this->name = $name;
  }
}
