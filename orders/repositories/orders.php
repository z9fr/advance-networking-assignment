<?php

class OrderRepository {
  private $conn;

  public function __construct() {
    $host = $_ENV["HOST"];
    $username = $_ENV["USERNAME"];
    $password = $_ENV["PASSWORD"];
    $database = $_ENV["DATABASE"];

    $this->conn = new mysqli();
    $this->conn->real_connect($host, $username, $password, $database);

    if ($this->conn->connect_error) {
      die("Connection failed: " . $this->conn->connect_error);
    }
  }

  public function create($orderData) {
    $stmt = $this->conn->prepare("INSERT INTO orders (title, userId, productId, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $orderData['title'], $orderData['userId'], $orderData['productId'], $orderData['price']);

    if ($stmt->execute()) {
      return $stmt->insert_id;
    } else {
      return false;
    }
  }

  public function read($id = null) {
    if ($id) {
        $stmt = $this->conn->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->bind_param("i", $id);
    } else {
        $stmt = $this->conn->prepare("SELECT * FROM orders");
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $rows = array();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    } else {
        return array(); // return empty array
    }
  }

  public function update($id, $orderData) {
    $stmt = $this->conn->prepare("UPDATE orders SET title = ?, userId = ?, productId = ?, price = ? WHERE id = ?");
    $stmt->bind_param("sssii", $orderData['title'], $orderData['userId'], $orderData['productId'], $orderData['price'], $id);

    if ($stmt->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function delete($id) {
    $stmt = $this->conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function __destruct() {
    $this->conn->close();
  }
}

