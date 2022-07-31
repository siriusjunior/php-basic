<?php
class connect
{
  const DB_NAME = "blog";
  const HOST = "localhost";
  const USER = "user";
  const PASS = "password6989";

  // dbh(DataBaseHandle)
  // protectedで継承先のクラスでも参照可,privateはクラス内のみ
  protected $dbh;

  public function __construct()
  {
    // DataSourceName
    $dsn = "mysql:host=" . self::HOST . ";dbname=" . self::DB_NAME . ";charset=utf8mb4";
    try {
      // PDO(PhpDataObject)のインスタンスをクラス変数へ格納
      $this->dbh = new PDO($dsn, self::USER, self::PASS);
    } catch (Exception $e) {
      // Exceptionが発生したら終了
      exit($e->getMessage());
    }
    // DBエラーの表示モードを指定
    $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
  }

  public function query($sql, $param = null)
  {
    // プリペアドステートメント
    $stmt = $this->dbh->prepare($sql);
    $stmt->execute($param);
    return $stmt;
  }
}
