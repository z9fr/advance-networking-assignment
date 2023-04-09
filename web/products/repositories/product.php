<?php

class ProductRepository {
  private $conn;

  public function __construct() {
    $host = getenv("DB_HOST");
    $username = getenv("DB_USERNAME");
    $password = getenv("DB_PASSWORD");
    $database = getenv("DATABASE");

    $this->conn = new mysqli();
    $this->conn->real_connect($host, $username, $password, $database);

    if ($this->conn->connect_error) {
      die("Connection failed: " . $this->conn->connect_error);
    }
}


public function create($productData) {
    $stmt = $this->conn->prepare("INSERT INTO products (title, body, url, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $productData['title'], $productData['body'], $productData['url'], $productData['price']);

    if ($stmt->execute()) {
      return $stmt->insert_id;
    } else {
      return false;
    }
}


public function read($id = null) {
    if ($id) {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
    } else {
        $stmt = $this->conn->prepare("SELECT * FROM products");
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


  public function update($id, $productData) {
    $stmt = $this->conn->prepare("UPDATE products SET title = ?, body = ?, url = ?, price = ? WHERE id = ?");
    $stmt->bind_param("sssii", $productData['title'], $productData['body'], $productData['url'], $productData['price'], $id);

    if ($stmt->execute()) {
      return true;
    } else {
      return false;
    }
}

  public function delete($id) {
    $stmt = $this->conn->prepare("DELETE FROM products WHERE id = ?");
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
