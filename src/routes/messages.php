<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

// Allow CORS
$app->options('/{routes:.+}', function ($request, $response, $args) {
  return $response;
});

$app->add(function($req, $res, $next) {
  $response = $next($req, $res);
  return $response
          ->withHeader('Access-Control-Allow-Origin', '*')
          ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
          ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

// Get all messages
$app->get('/api/messages', function(Request $request, Response $response){

  $sql = "SELECT * FROM messages";

  try {
    // Get DB object
    $db = new db();
    // Connect
    $db = $db->connect();

    $stmt = $db->query($sql);
    $messages = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    $response->getBody()->write(json_encode($messages));

  } catch(PDOException $e) {
    echo '{"error" : {"text": '.$e->getMessage().'}';
  }
});

// Get single messages
$app->get('/api/message/{id}', function(Request $request, Response $response){

  $id = $request->getAttribute('id');

  $sql = "SELECT * FROM messages WHERE id = $id";

  try {
    // Get DB object
    $db = new db();
    // Connect
    $db = $db->connect();

    $stmt = $db->query($sql);
    $message = $stmt->fetch(PDO::FETCH_OBJ);
    $db = null;
    $response->getBody()->write(json_encode($message));

  } catch(PDOException $e) {
    echo '{"error" : {"text": '.$e->getMessage().'}';
  }
});

// Add single messages
$app->post('/api/message/add', function(Request $request, Response $response){

  $Sender_firstname = $request->getParam('Sender_firstname');
  $Sender_lastname = $request->getParam('Sender_lastname');
  $Message = $request->getParam('Message');
  $Timestamp = $request->getParam('Timestamp');
  $Readmore = $request->getParam('Readmore');
  $Readmore_message = $request->getParam('Readmore_message');

  $sql = "INSERT INTO messages (Sender_firstname, Sender_lastname, Message, Timestamp, Readmore, Readmore_message)
          VALUES (:Sender_firstname, :Sender_lastname, :Message, :Timestamp, :Readmore, :Readmore_message)";

  try {
    // Get DB object
    $db = new db();
    // Connect
    $db = $db->connect();

    $stmt = $db->prepare($sql);

    $stmt->bindParam(':Sender_firstname', $Sender_firstname);
    $stmt->bindParam(':Sender_lastname', $Sender_lastname);
    $stmt->bindParam(':Message', $Message);
    $stmt->bindParam(':Timestamp', $Timestamp);
    $stmt->bindParam(':Readmore', $Readmore);
    $stmt->bindParam(':Readmore_message', $Readmore_message);

    $stmt->execute();

    echo '{"notice": {"text": "Message Added"}';
  } catch(PDOException $e) {
    echo '{"error" : {"text": '.$e->getMessage().'}';
  }
});

// Update message
$app->put('/api/message/update/{id}', function(Request $request, Response $response){

  $id               = $request->getAttribute('id');

  $Sender_firstname = $request->getParam('Sender_firstname');
  $Sender_lastname  = $request->getParam('Sender_lastname');
  $Message          = $request->getParam('Message');
  $Timestamp        = $request->getParam('Timestamp');
  $Readmore         = $request->getParam('Readmore');
  $Readmore_message = $request->getParam('Readmore_message');

  $sql = "UPDATE messages SET
            Sender_firstname  = :Sender_firstname,
            Sender_lastname   = :Sender_lastname,
            Message           = :Message,
            Timestamp         = :Timestamp,
            Readmore          = :Readmore,
            Readmore_message  = :Readmore_message
          WHERE id = $id";

  try {
    // Get DB object
    $db = new db();
    // Connect
    $db = $db->connect();

    $stmt = $db->prepare($sql);

    $stmt->bindParam(':Sender_firstname', $Sender_firstname);
    $stmt->bindParam(':Sender_lastname', $Sender_lastname);
    $stmt->bindParam(':Message', $Message);
    $stmt->bindParam(':Timestamp', $Timestamp);
    $stmt->bindParam(':Readmore', $Readmore);
    $stmt->bindParam(':Readmore_message', $Readmore_message);

    $stmt->execute();

    echo '{"notice": {"text": "Message updated"}';
  } catch(PDOException $e) {
    echo '{"error" : {"text": '.$e->getMessage().'}';
  }
});

// Delete single message
$app->delete('/api/message/delete/{id}', function(Request $request, Response $response){

  $id = $request->getAttribute('id');

  $sql = "DELETE * FROM messages WHERE id = $id";

  try {
    // Get DB object
    $db = new db();
    // Connect
    $db = $db->connect();

    $stmt = $db->prepare($sql);
    $stmt->execute();
    $db = null;

    echo '{"notice": {"text": "Message deleted"}';
  } catch(PDOException $e) {
    echo '{"error" : {"text": '.$e->getMessage().'}';
  }
});
