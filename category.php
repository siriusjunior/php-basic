<?php
include 'lib/secure.php';
include 'lib/connect.php';
include 'lib/queryCategory.php';
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
      </div>
      <!-- ./col-md-12 -->
    </div>
    <!-- ./row -->
  </main>
  <!-- ./container -->
</body>

</html>