<?php

// In case one is using PHP 5.4's built-in server
$filename = __DIR__ . preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

require __DIR__ . '/vendor/autoload.php';
include './repositories/orders.php';


$router = new \Bramus\Router\Router();
$orderRepo = new orderRepository();

$router->set404('(/.*)?', function() {
    header('HTTP/1.1 404 Not Found');
    header('Content-Type: application/json');

    $jsonArray = array();
    $jsonArray['status'] = "404";
    $jsonArray['status_text'] = "Not found";

    echo json_encode($jsonArray);

});

$router->get('/', function () use ($orderRepo) {
    $products = $orderRepo->read();
    header('Content-Type: application/json');
    echo json_encode($products);
});

$router->get('(\d+)', function ($id) use ($orderRepo) {
    $product = $orderRepo->read($id);
    header('Content-Type: application/json');
    echo json_encode($product);
});

$router->post('/', function () use ($orderRepo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $newProduct = [
        'title' => $data['title'],
        'body' => $data['body'],
        'url' => $data['productId'],
        'price' => $data['price'],
    ];

    $id = $orderRepo->create($newProduct);
    header('Content-Type: application/json');
    echo json_encode(['id' => $id]);
});


$router->put('(\d+)', function ($id) use ($orderRepo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $updatedProduct = [
        'title' => $data['title'],
        'userId' => $data['userId'],
        'productId' => $data['productId'],
        'price' => $data['price']
    ];

    $orderRepo->update($id, $updatedProduct);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Product updated successfully']);
});

$router->delete('(\d+)', function ($id) use ($orderRepo) {
    $orderRepo->delete($id);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Product deleted successfully']);
});



$router->run();