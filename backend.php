<?php
include 'lib/secure.php';
include 'lib/connect.php';
include 'lib/queryArticle.php';
include 'lib/article.php';

$queryArticle = new QueryArticle();
$articles = $queryArticle->findAll();
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
        <h1>記事一覧</h1>
        <?php if ($articles) : ?>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>ID</th>
                <th>タイトル</th>
                <th>本文</th>
                <th>画像</th>
                <th>作成日</th>
                <th>更新日</th>
                <th>編集</th>
                <th>削除</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($articles as $article) : ?>
                <tr>
                  <td><?php echo $article->getId(); ?></td>
                  <td><?php echo $article->getTitle(); ?></td>
                  <td><?php echo $article->getBody(); ?></td>
                  <td><?php echo $article->getFilename() ? '<img src="./album/thumbs-' . $article->getFilename() . '">' : 'なし'; ?></td>
                  <td><?php echo $article->getCreatedAt(); ?></td>
                  <td><?php echo $article->getUpdatedAt(); ?></td>
                  <td>
                    <a href="edit.php?id=<?php echo $article->getId(); ?>" class="btn btn-success">編集</a>
                  </td>
                  <td>
                    <a href="delete.php?id=<?php echo $article->getId() ?>" class="btn btn-danger">削除</a>
                  </td>
                </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        <?php else : ?>
          <div class="alert alert-info">
            <p>投稿はまだありません。</p>
          </div>
        <?php endif ?>
      </div>
      <!-- ./col-md-12 -->
    </div>
    <!-- ./row -->
  </main>
  <!-- ./container -->
</body>

</html>