<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TransactionsController
{
  private function db(){
    return mysqli_connect('my_mariadb', 'root', '', 'bank');
  }

  public function list($req, $res, $args){
    $db = $this->db();
    $accountId = (int)$args['id'];

    $sql = "SELECT id, type, amount, description, created_at
        FROM transactions
        WHERE account_id = ?
        ORDER BY created_at DESC";

    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $accountId);
    $stmt->execute();
    $result = $stmt->get_result();

    $rows = [];
    while ($r = $result->fetch_assoc()) {
      $rows[] = $r;
    }

    $res->getBody()->write(json_encode($rows));
    return $res->withHeader('Content-Type', 'application/json');
  }

  public function detail($req, $res, $args){
    $db = $this->db();
    $transactionId = (int)$args['idT'];

    $sql = "SELECT * FROM transactions WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $transactionId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if (!$row) {
      $res->getBody()->write(json_encode(["error" => "Not found"]));
      return $res->withStatus(404)->withHeader('Content-Type', 'application/json');
    }

    $res->getBody()->write(json_encode($row));
    return $res->withHeader('Content-Type', 'application/json');
  }

  public function deposit($req, $res, $args){
    $db = $this->db();
    $accountId = (int)$args['id'];

    $data = json_decode($req->getBody(), true);
    $amount = (float)($data['amount'] ?? 0);
    $desc = $data['description'] ?? '';

    if ($amount <= 0) {
      $res->getBody()->write(json_encode(["error" => "Invalid amount"]));
      return $res->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $balance = $this->getBalance($db, $accountId);
    $newBalance = $balance + $amount;

    $sql = "INSERT INTO transactions (account_id, type, amount, description, balance_after)
        VALUES (?, 'deposit', ?, ?, ?)";

    $stmt = $db->prepare($sql);
    $stmt->bind_param('idsd', $accountId, $amount, $desc, $newBalance);
    $stmt->execute();

    $res->getBody()->write(json_encode([
      "message" => "Deposit ok",
      "balance" => $newBalance
    ]));

    return $res->withStatus(201)->withHeader('Content-Type', 'application/json');
  }

  public function withdrawal($req, $res, $args){
    $db = $this->db();
    $accountId = (int)$args['id'];

    $data = json_decode($req->getBody(), true);
    $amount = (float)($data['amount'] ?? 0);
    $desc = $data['description'] ?? '';

    if ($amount <= 0) {
      $res->getBody()->write(json_encode(["error" => "Invalid amount"]));
      return $res->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $balance = $this->getBalance($db, $accountId);

    if ($amount > $balance) {
      $res->getBody()->write(json_encode(["error" => "Insufficient balance"]));
      return $res->withStatus(422)->withHeader('Content-Type', 'application/json');
    }

    $newBalance = $balance - $amount;

    $sql = "INSERT INTO transactions (account_id, type, amount, description, balance_after)
        VALUES (?, 'withdrawal', ?, ?, ?)";

    $stmt = $db->prepare($sql);
    $stmt->bind_param('idsd', $accountId, $amount, $desc, $newBalance);
    $stmt->execute();

    $res->getBody()->write(json_encode([
      "message" => "Withdrawal ok",
      "balance" => $newBalance
    ]));

    return $res->withStatus(201)->withHeader('Content-Type', 'application/json');
  }

  public function update($req, $res, $args){
    $db = $this->db();
    $transactionId = (int)$args['idT'];

    $data = json_decode($req->getBody(), true);
    $desc = $data['description'] ?? '';

    $sql = "UPDATE transactions SET description = ? WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('si', $desc, $transactionId);
    $stmt->execute();

    $res->getBody()->write(json_encode(["message" => "Updated"]));
    return $res->withHeader('Content-Type', 'application/json');
  }

  public function delete($req, $res, $args){
    $db = $this->db();
    $accountId = (int)$args['id'];
    $transactionId = (int)$args['idT'];

    $sqlLast = "SELECT id FROM transactions
          WHERE account_id = ?
          ORDER BY created_at DESC
          LIMIT 1";

    $stmt = $db->prepare($sqlLast);
    $stmt->bind_param('i', $accountId);
    $stmt->execute();
    $last = $stmt->get_result()->fetch_assoc();

    if (!$last || $last['id'] != $transactionId) {
      $res->getBody()->write(json_encode(["error" => "Only last transaction can be deleted"]));
      return $res->withStatus(403)->withHeader('Content-Type', 'application/json');
    }

    $sqlDelete = "DELETE FROM transactions WHERE id = ?";
    $stmt = $db->prepare($sqlDelete);
    $stmt->bind_param('i', $transactionId);
    $stmt->execute();

        $res->getBody()->write(json_encode(["message" => "Deleted"]));
        return $res->withHeader('Content-Type', 'application/json');
    }

    private function getBalance($db, $accountId){
      $sql = "SELECT
              COALESCE(SUM(CASE WHEN type='deposit' THEN amount ELSE 0 END),0) -
              COALESCE(SUM(CASE WHEN type='withdrawal' THEN amount ELSE 0 END),0) AS balance
              FROM transactions
              WHERE account_id = ?";

      $stmt = $db->prepare($sql);
      $stmt->bind_param('i', $accountId);
      $stmt->execute();
      $row = $stmt->get_result()->fetch_assoc();
      return (float)$row['balance'];
    }
}
