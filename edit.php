<?php
include 'lib/secure.php';
include 'lib/connect.php';
include 'lib/queryArticle.php';
include 'lib/article.php';
include 'lib/queryCategory.php';

$title = ""; //タイトル
$body = ""; //本文
$id = ""; //id
$category_id = ""; //カテゴリーID
$title_alert = ""; //タイトルのエラー文言
$body_alert = ""; //本文のエラー文言

// カテゴリーの取得
$queryCategory = new QueryCategory();
$categories = $queryCategory->findAll();

// 編集データを取得
if (isset($_GET['id'])) {
  $queryArticle = new QueryArticle();
  $article = $queryArticle->find($_GET['id']);

  if ($article) {
    // 記事データがあればフォームに反映
    $id = $article->getId();
    $title = $article->getTitle();
    $body = $article->getBody();
    $category_id = $article->getCategoryId();
  } else {
    // 記事データがない
    header('Location:backend.php');
    exit;
  }
  // 編集データを送信,各々のバリデーションを満たす
} else if (!empty($_POST['id']) && !empty($_POST['title']) && !empty($_POST['body'])) {
  $title = $_POST['title'];
  $body = $_POST['body'];

  $queryArticle = new QueryArticle();
  $article = $queryArticle->find($_POST['id']);

  if ($article) {
    // 既存のarticleが存在,上書き保存
    $article->setTitle($title);
    $article->setBody($body);
    // 画像がアップロードされていたとき
    if (isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
      $article->setFile($_FILES['image']);
    }
    // カテゴリーが選択されているとき
    if (!empty($_POST['category'])) {
      $category = $queryCategory->find($_POST['category']);
      if ($category) {
        $article->setCategoryId($category->getId());
      }
    } else {
      $article->setCategoryId(null);
    }
    $article->save();
  }
  header('Location:backend.php');
  exit;
} else if (!empty($_POST)) {
  // titleまたはbodyの不足の場合
  if (!empty($_POST['id'])) {
    $id = $_POST['id'];
  } else {
    // idが空の場合
    header('Location: backend.php');
    exit;
  }
  // titleの有無判定
  if (!empty($_POST['title'])) {
    $title = $_POST['title'];
  } else {
    $title_alert = "タイトルを入力してください。";
  }
  // bodyの有無判定
  if (!empty($_POST['body'])) {
    $body = $_POST['body'];
  } else {
    $body_alert = "本文を入力してください。";
  }
}
?>

<!doctype html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Blog Backend</title>
  <!-- Bootstrap core CSS -->
  <link href="./css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      padding-top: 5rem;
    }

    .bd-placeholder-img {
      font-size: 1.125rem;
      text-anchor: middle;
      -webkit-user-select: none;
      -moz-user-select: none;
      user-select: none;
    }

    @media (min-width: 768px) {
      .bd-placeholder-img-lg {
        font-size: 3.5rem;
      }
    }

    .bg-red {
      background-color: #ff6644 !important;
    }
  </style>
  <!-- Custom styles for this template -->
  <link href="./css/blog.css" rel="stylesheet">
</head>

<body>
  <?php include 'lib/nav.php' ?>
  <main class="container">
    <div class="row">
      <div class="col-md-12">
        <h1>記事の編集</h1>

        <form action="edit.php" method="post" enctype="multipart/form-data">
          <input type="hidden" name="id" value="<?php echo $id ?>">
          <div class="mb-3">
            <label class="form-label">タイトル</label>
            <?php echo !empty($title_alert) ? '<div class="alert alert-danger">' . $title_alert . '</div>' : ''; ?>
            <input type="text" name="title" value="<?php echo $title; ?>" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">本文</label>
            <?php echo !empty($body_alert) ? '<div class="alert alert-danger">' . $body_alert . '</div>' : ''; ?>
            <textarea name="body" class="form-control" rows="10"><?php echo $body; ?></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">カテゴリー</label>
            <select name="category" class="form-control">
              <option value="0">なし</option>
              <?php foreach ($categories as $c) : ?>
                <option value="<?php echo $c->getId() ?>" <?php echo $category_id == $c->getId() ? 'selected="selected"' : '' ?>><?php echo $c->getName() ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <?php if ($article->getFilename()) : ?>
            <div class="mb-3">
              <img src="./album/tumbs-<?php echo $article->getFilename() ?>">
            </div>
          <?php endif; ?>
          <div class="mb-3">
            <label class="form-label">画像</label>
            <input type="file" name="image" class="form-control">
          </div>

          <div class="mb-3">
            <button type="submit" class="btn btn-primary">投稿する</button>
          </div>
        </form>
      </div>
    </div>
    <!-- ./row -->
  </main>
  <!-- ./container -->
</body>

</html>