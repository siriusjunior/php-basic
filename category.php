<?php
include 'lib/secure.php';
include 'lib/connect.php';
include 'lib/queryCategory.php';

$queryCategory = new QueryCategory();
$formCategory = null;

// カテゴリー新規作成
if (!empty($_POST['action']) && $_POST['action'] == 'add' && !empty($_POST['name'])) {
  $category = new Category();
  $category->setName($_POST['name']);
  $category->save();
} else if (!empty($_GET['action']) && $_GET['action'] == 'edit' && !empty($_GET['id'])) {
  // フォームに編集カテゴリーをセット
  $formCategory = $queryCategory->find($_GET['id']);
} else if (!empty($_POST['action']) && $_POST['action'] == 'edit' && !empty($_POST['id']) && !empty($_POST['name'])) {
  $category = $queryCategory->find($_POST['id']);
  if ($category) {
    $category->setName($_POST['name']);
    $category->save();
  }
}

$categories =  $queryCategory->findAll();
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
        <h1>カテゴリー</h1>
        <?php if ($formCategory) : ?>
          <h2>編集</h2>
          <form action="category.php" method="post" class="row">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?php echo $formCategory->getId() ?>">
            <div class="col-md-6">
              <input type="text" name="name" value="<?php echo $formCategory->getName() ?>" class="form-control">
            </div>
            <div class="col-md-6">
              <button type="submit" class="btn btn-primary">編集する</button>
            </div>
          </form>
          <hr>
        <?php endif ?>
        <h2>新規追加</h2>
        <form action="category.php" method="post" class="row">
          <input type="hidden" name="action" value="add">
          <div class="col-md-6">
            <input type="text" name="name" class="form-control">
          </div>
          <div class="col-md-6">
            <button type="submit" class="btn btn-primary">追加する</button>
          </div>
        </form>
        <hr>
        <?php if ($categories) : ?>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>ID</th>
                <th>カテゴリー名</th>
                <th>編集</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($categories as $c) : ?>
                <tr>
                  <td><?php echo $c->getId(); ?></td>
                  <td><?php echo $c->getName(); ?></td>
                  <td><a href="category.php?action=edit&id=<?php echo $c->getId(); ?>" class="btn btn-success">編集</a></td>
                </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        <?php else : ?>
          <div class="alert alert-info">カテゴリーはまだ登録されていません。</div>
        <?php endif; ?>
      </div>
      <!-- ./col-md-12 -->
    </div>
    <!-- ./row -->
  </main>
  <!-- ./container -->
</body>

</html>