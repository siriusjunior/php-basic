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
    $title = $this->article->getTitle();
    $body = $this->article->getBody();
    if ($this->article->getId()) {
      // IDが存在する場合は上書き
      $id = $this->article->getId();
      $stmt = $this->dbh->prepare(
        "UPDATE articles SET title=:title, body=:body, updated_at=NOW() WHERE id=:id"
      );
      $stmt->bindParam(':title', $title, PDO::PARAM_STR);
      $stmt->bindParam(':body', $body, PDO::PARAM_STR);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
    } else {
      // article新規作成
      // ファイル名設定とアップロードファイルの移動
      if ($file = $this->article->getFile()) {
        $old_name = $file['tmp_name'];
        $new_name = date('YmdHis') . mt_rand();
        // アップロード可否,デフォは不可
        $is_upload = false;
        // 拡張子設定
        $type = exif_imagetype($old_name);
        switch ($type) {
          case IMAGETYPE_JPEG:
            $new_name .= '.jpg';
            $is_upload = true;
            break;
          case IMAGETYPE_GIF:
            $new_name .= '.gif';
            $is_upload = true;
            break;
          case IMAGETYPE_PNG:
            $new_name .= '.png';
            $is_upload = true;
            break;
        }
        // テンポラリディレクトリ($old_name)から新しい場所へ移動,__DIR__は実行ファイルのディレクトリ出力
        if ($is_upload && move_uploaded_file($old_name, __DIR__ . './../album/' . $new_name)) {
          $this->article->setFilename($new_name);
          $file_name = $this->article->getFilename();
        }

        $stmt = $this->dbh->prepare("INSERT INTO articles (title, body, filename, created_at, updated_at) VALUES (:title, :body, :filename, NOW(), NOW())");
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':body', $body, PDO::PARAM_STR);
        $stmt->bindParam(':filename', $file_name, PDO::PARAM_STR);
        $stmt->execute();
      }
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
