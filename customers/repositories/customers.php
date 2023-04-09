<?php

class CustomersRepository {
  private $conn;

  public function __construct() {
    $host = $_ENV["HOST"];
    $username = $_ENV["USERNAME"];
    $password = $_ENV["PASSWORD"];
    $database = $_ENV["DATABASE"];

    $this->conn = new mysqli();
    $this->conn->ssl_set(NULL, NULL, "/etc/ssl/cert.pem", NULL, NULL);
    $this->conn->real_connect($host, $username, $password, $database);

    if ($this->conn->connect_error) {
      die("Connection failed: " . $this->conn->connect_error);
    }
  }

  public function create($customerData) {
    $stmt = $this->conn->prepare("INSERT INTO customers (name, body, profileImageUrl) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $customerData['name'], $customerData['body'], $customerData['profileImageUrl']);

    if ($stmt->execute()) {
      return $stmt->insert_id;
    } else {
      return false;
    }
  }

  public function read($id = null) {
    if ($id) {
      $stmt = $this->conn->prepare("SELECT * FROM customers WHERE id = ?");
      $stmt->bind_param("i", $id);
    } else {
      $stmt = $this->conn->prepare("SELECT * FROM customers");
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

  public function update($id, $customerData) {
    $stmt = $this->conn->prepare("UPDATE customers SET name = ?, body = ?, profileImageUrl = ? WHERE id = ?");
    $stmt->bind_param("sssi", $customerData['name'], $customerData['body'], $customerData['profileImageUrl'], $id);

    if ($stmt->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function delete($id) {
    $stmt = $this->conn->prepare("DELETE FROM customers WHERE id = ?");
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