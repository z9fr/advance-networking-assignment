<?php

// In case one is using PHP 5.4's built-in server
$filename = __DIR__ . preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

require __DIR__ . '/vendor/autoload.php';
include './repositories/product.php';


$router = new \Bramus\Router\Router();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$productRepo = new ProductRepository();


$router->set404('(/.*)?', function() {
    header('HTTP/1.1 404 Not Found');
    header('Content-Type: application/json');

    $jsonArray = array();
    $jsonArray['status'] = "404";
    $jsonArray['status_text'] = "Not found";

    echo json_encode($jsonArray);
});

$router->get('/', function () {
    include ('views/home.php');
});

$router->get('/products', function () use ($productRepo) {
    $products = $productRepo->read();
    header('Content-Type: application/json');
    echo json_encode($products);
});

$router->get('/products/(\d+)', function ($id) use ($productRepo) {
    $product = $productRepo->read($id);
    header('Content-Type: application/json');
    echo json_encode($product);
});

$router->post('/products', function () use ($productRepo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $newProduct = [
        'title' => $data['title'],
        'body' => $data['body'],
        'url' => $data['url'],
        'price' => $data['price']
    ];

    $id = $productRepo->create($newProduct);
    header('Content-Type: application/json');
    echo json_encode(['id' => $id]);
});


$router->put('/products/(\d+)', function ($id) use ($productRepo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $updatedProduct = [
        'title' => $data['title'],
        'body' => $data['body'],
        'url' => $data['url'],
        'price' => $data['price']
    ];

    $productRepo->update($id, $updatedProduct);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Product updated successfully']);
});

$router->delete('/products/(\d+)', function ($id) use ($productRepo) {
    $productRepo->delete($id);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Product deleted successfully']);
});



$router->run();