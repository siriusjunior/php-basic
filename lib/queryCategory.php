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

  public function save()
  {
    $id = $this->category->getId();
    $name = $this->category->getName();
    if ($this->category->getId()) {
      // IDがあれば上書き
      $stmt = $this->dbh->prepare("UPDATE categories SET name=:name WHERE id=:id");
      $stmt->bindParam(':name', $name, PDO::PARAM_STR);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    } else {
      // IDがなければ新規作成
      $stmt = $this->dbh->prepare("INSERT INTO categories(name) VALUES(:name)");
      $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    }
    $stmt->execute();
  }

  public function delete()
  {
    $id = $this->category->getId();
    $stmt = $this->dbh->prepare("UPDATE articles SET category_id = NULL WHERE category_id=:id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $stmt = $this->dbh->prepare("DELETE FROM categories WHERE id=:id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
  }

  public function find($id)
  {
    $stmt = $this->dbh->prepare("SELECT * FROM categories WHERE id=:id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
      $category = new Category();
      $category->setId($result['id']);
      $category->setName($result['name']);
      return $category;
    }
    return null;
  }

  public function findAll()
  {
    $stmt = $this->dbh->prepare("SELECT * FROM categories");
    $stmt->execute();
    $categories = array();
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $result) {
      $category = new Category();
      $category->setId($result['id']);
      $category->setName($result['name']);
      $categories[$result['id']] = $category;
    }
    return $categories;
  }
}


class Category
{
  private $id = null;
  private $name = null;

  public function save()
  {
    $queryCategory = new QueryCategory();
    $queryCategory->setCategory($this);
    $queryCategory->save();
  }

  public function delete()
  {
    $queryCategory = new QueryCategory();
    $queryCategory->setCategory($this);
    $queryCategory->delete();
  }

  public function getId()
  {
    return $this->id;
  }

  public function getName()
  {
    return $this->name;
  }

  public function setId($id)
  {
    $this->id = $id;
  }

  public function setName($name)
  {
    $this->name = $name;
  }
}
