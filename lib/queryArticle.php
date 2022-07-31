<?php
class QueryArticle extends connect
{
  private $article;

  public function __construct()
  {
    parent::__construct();
  }

  // Articleクラスの引数の型指定
  public function setArticle(Article $article)
  {
    // 引数で受けた$articleを自身のパラメーターとして保持($article内のメソッド使用可exl.20,24)
    $this->article = $article;
  }

  public function save()
  {
    if ($this->article->getId()) {
      // IDが存在する場合は上書き
      $id = $this->article->getId();
      $title = $this->article->getTitle();
      $body = $this->article->getBody();
      $stmt = $this->dbh->prepare(
        "UPDATE articles SET title=:title, body=:body, updated_at=NOW() WHERE id=:id"
      );
      $stmt->bindParam(':title', $title, PDO::PARAM_STR);
      $stmt->bindParam(':body', $body, PDO::PARAM_STR);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
    } else {
      // 新規作成
      $title = $this->article->getTitle();
      $body = $this->article->getBody();
      $stmt = $this->dbh->prepare("INSERT INTO articles (title, body, created_at, updated_at) VALUES (:title, :body, NOW(), NOW())");
      $stmt->bindParam(':title', $title, PDO::PARAM_STR);
      $stmt->bindParam(':body', $body, PDO::PARAM_STR);
      $stmt->execute();
    }
  }

  public function findAll()
  {
    $stmt = $this->dbh->prepare("SELECT * FROM articles");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $articles = array();
    foreach ($results as $result) {
      $article = new Article();
      $article->setId($result['id']);
      $article->setTitle($result['title']);
      $article->setBody($result['body']);
      $article->setCreatedAt($result['created_at']);
      $article->setUpdatedAt($result['updated_at']);
      $articles[] = $article;
    }
    return $articles;
  }

  public function find($id)
  {
    $stmt = $this->dbh->prepare("SELECT * FROM articles WHERE id=:id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $article = null;
    if ($result) {
      $article = new Article();
      $article->setId($result['id']);
      $article->setTitle($result['title']);
      $article->setBody($result['body']);
      $article->setCreatedAt($result['created_at']);
      $article->setUpdatedAt($result['updated_at']);
    }
    return $article;
  }
}
