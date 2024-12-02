<?php
    // Include the router and controller
    require_once __DIR__ . '/../Router.php';  // Adjust path as needed
    require_once __DIR__ . '/../controllers/BidController.php';  // Adjust path as needed

    // Instantiate Router
    $router = new Router();

    // Define routes
    $router->add('POST', '/bid', 'BidController@handleBidRequest');

    // Dispatch the request
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
?>