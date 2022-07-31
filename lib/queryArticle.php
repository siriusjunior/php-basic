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
}