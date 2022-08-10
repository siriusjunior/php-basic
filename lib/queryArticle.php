<?php
class QueryArticle extends connect
{
  private $article;

  const THUMBS_WIDTH = 200; //サムネイル幅

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

  // 画像保存処理と新規ファイル名返却
  private function saveFile($old_name)
  {
    $new_name = date('YmdHis') . mt_rand();

    if ($type = exif_imagetype($old_name)) {
      // 元画像サイズを取得
      list($width, $height) = getimagesize($old_name);
      // サムネイルの比率算出
      $rate = self::THUMBS_WIDTH / $width;
      $thumbs_height = $height * $rate;
      // キャンバス作成
      $canvas = imagecreatetruecolor(self::THUMBS_WIDTH, $thumbs_height);
      // 拡張子設定
      switch ($type) {
        case IMAGETYPE_JPEG:
          $new_name .= '.jpg';
          // サムネイルを保存,パスから画像取得($image)
          $image = imagecreatefromjpeg($old_name);
          // canvasと元画像を使用してサムネイル作成
          imagecopyresampled($canvas, $image, 0, 0, 0, 0, self::THUMBS_WIDTH, $thumbs_height, $width, $height);
          // サムネ画像リソースを指定パス保存
          imagejpeg($canvas, __DIR__ . '/../album/thumbs-' . $new_name);
          break;
        case IMAGETYPE_GIF:
          $new_name .= '.gif';
          // サムネイルを保存,パスから画像取得($image)
          $image = imagecreatefromgif($old_name);
          // canvasと元画像を使用してサムネイル作成
          imagecopyresampled($canvas, $image, 0, 0, 0, 0, self::THUMBS_WIDTH, $thumbs_height, $width, $height);
          // サムネ画像リソースを指定パス保存
          imagegif($canvas, __DIR__ . '/../album/thumbs-' . $new_name);
          break;
        case IMAGETYPE_PNG:
          $new_name .= '.png';
          // サムネイルを保存,パスから画像取得($image)
          $image = imagecreatefrompng($old_name);
          // canvasと元画像を使用してサムネイル作成
          imagecopyresampled($canvas, $image, 0, 0, 0, 0, self::THUMBS_WIDTH, $thumbs_height, $width, $height);
          // サムネ画像リソースを指定パス保存
          imagepng($canvas, __DIR__ . '/../album/thumbs-' . $new_name);
          break;
        default:
          // JPEG,GIF,PNG以外の場合は何も返さない
          imagedestroy($canvas);
          return null;
      }
    } else {
      // 画像以外の場合
      return null;
    }
    // テンポラリディレクトリ($old_name)からアップロードリソースを指定移動,__DIR__は実行ファイルのディレクトリ出力
    move_uploaded_file($old_name, __DIR__ . './../album/' . $new_name);
    // ファイル名を返却
    return $new_name;
  }

  public function save()
  {
    $title = $this->article->getTitle();
    $body = $this->article->getBody();
    $filename = $this->article->getFilename();
    $category_id = $this->article->getCategoryId();

    if ($this->article->getId()) {
      // IDが存在する場合は上書き
      $id = $this->article->getId();
      // 差替えファイルのアップロードが確認できる
      if ($file = $this->article->getFile()) { //cf.edit.php(l.40)
        // 既存ファイルの確認,削除
        $this->deleteFile();
        // 差替えファイルのアップロード
        $this->article->setFilename($this->saveFile($file['tmp_name']));
        $filename = $this->article->getFilename();
      }

      $stmt = $this->dbh->prepare(
        "UPDATE articles SET title=:title, body=:body,filename=:filename,category_id=:category_id, updated_at=NOW() WHERE id=:id"
      );
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    } else {
      // IDが存在しない場合は新規作成
      if ($file = $this->article->getFile()) { //cf.post.php(l.24-26)
        // パス指定でファイル移動保存,返却値のファイル名設置
        $this->article->setFilename($this->saveFile($file['tmp_name']));
        $filename = $this->article->getFilename();
      }
      $stmt = $this->dbh->prepare("INSERT INTO articles (title, body, filename, category_id, created_at, updated_at) VALUES (:title, :body, :filename, :category_id, NOW(), NOW())");
    }
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':body', $body, PDO::PARAM_STR);
    $stmt->bindParam(':filename', $filename, PDO::PARAM_STR);
    $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
    $stmt->execute();
  }

  private function deleteFile()
  {
    if ($this->article->getFilename()) {
      unlink(__DIR__ . '/../album/thumbs-' . $this->article->getFilename());
      unlink(__DIR__ . '/../album/' . $this->article->getFilename());
    }
  }

  public function delete()
  {
    if ($this->article->getId()) {
      $this->deleteFile();
      $id = $this->article->getId();
      $stmt = $this->dbh->prepare("UPDATE articles SET is_delete=1 WHERE id=:id");
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
    }
  }

  public function find($id)
  {
    $stmt = $this->dbh->prepare("SELECT * FROM articles WHERE id=:id AND is_delete=0");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $articles = $this->getArticles($stmt->fetchAll(PDO::FETCH_ASSOC));
    return $articles[0];
  }

  public function findAll()
  {
    $stmt = $this->dbh->prepare("SELECT * FROM articles WHERE is_delete=0 ORDER BY created_at DESC");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $articles = $this->getArticles($results);
    return $articles;
  }


  public function getPager($page = 1, $limit = 10, $month = null)
  {
    $start = ($page - 1) * $limit;
    $pager = array('total' => null, 'articles' => null);
    if ($month) {
      // 月指定があれば「2021 - 01 % 」のように検索できるよう末尾に追加
      $month .= '%';
    }
    // 総記事数の取得
    if ($month) {
      $stmt = $this->dbh->prepare("SELECT COUNT(*)FROM articles WHERE is_delete=0 AND created_at LIKE :month");
      $stmt->bindParam(':month', $month, PDO::PARAM_STR);
    } else {
      $stmt = $this->dbh->prepare("SELECT COUNT(*)FROM articles WHERE is_delete=0");
    }
    $stmt->execute();
    $pager['total'] = $stmt->fetchColumn();

    // 記事のインスタンス取得
    if ($month) {
      $stmt = $this->dbh->prepare("SELECT * FROM articles
        WHERE is_delete=0 AND created_at LIKE :month
        ORDER BY created_at DESC
        LIMIT :start, :limit");
      $stmt->bindParam(':month', $month, PDO::PARAM_STR);
    } else {
      $stmt = $this->dbh->prepare("SELECT * FROM articles WHERE is_delete=0 
        ORDER BY created_at DESC 
        LIMIT :start, :limit");
    }
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $pager['articles'] = $this->getArticles($stmt->fetchAll(PDO::FETCH_ASSOC));
    return $pager;
  }

  public function getMonthlyArchiveMenu()
  {
    // 月別アーカイブ件数を配列で取得
    $stmt = $this->dbh->prepare("
      SELECT DATE_FORMAT(created_at, '%Y-%m') AS month_menu, COUNT(*) AS count
      FROM articles
      WHERE is_delete = 0
      GROUP BY DATE_FORMAT(created_at, '%Y-%m')
      ORDER BY month_menu DESC");
    $stmt->execute();
    $return = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $return[] = array('month' => $row['month_menu'], 'count' => $row['count']);
    }
    return $return;
  }

  private function getArticles($results)
  {
    $articles = array();
    foreach ($results as $result) {
      $article = new Article();
      $article->setId($result['id']);
      $article->setTitle($result['title']);
      $article->setBody($result['body']);
      $article->setFilename($result['filename']);
      $article->setCategoryId($result['category_id']);
      $article->setCreatedAt($result['created_at']);
      $article->setUpdatedAt($result['updated_at']);
      $articles[] = $article;
    }
    return $articles;
  }
}
