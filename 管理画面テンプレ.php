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
  <nav class="navbar navbar-expand-md navbar-dark bg-red fixed-top">
    <div class="container"><a class="navbar-brand" href="/blog/backend.php">My Blog Backend</a>
      <ul class="navbar-nav me-auto mb-2 mb-md-0">
        <li class="nav-link" href="#">記事を書く</li>
        <li class="nav-link" href="#">ログアウト</li>
      </ul>
    </div>
  </nav>
  <main class="container">
    <div class="row">
      <div class="col-md-12">
        <p>本文がここに入ります。</p>
      </div>
    </div>
    <!-- ./row -->
  </main>
  <!-- ./container -->
</body>

</html>