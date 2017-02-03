<?php
require_once '_db.php';

$stmt = $db->prepare("INSERT INTO reservations (name, note, amtStarter, amtMain, amtDessert, start, end, room_id, status, paid) VALUES (:name, :note, :amtStarter, :amtMain, :amtDessert, :start, :end, :room, :status, 'No')");
$stmt->bindParam(':start', $_POST['start']);
$stmt->bindParam(':end', $_POST['end']);
$stmt->bindParam(':name', $_POST['name']);
$stmt->bindParam(':note', $_POST['note']);
$stmt->bindParam(':amtStarter', $_POST['amtStarter']);
$stmt->bindParam(':amtMain', $_POST['amtMain']);
$stmt->bindParam(':amtDessert', $_POST['amtDessert']);
$stmt->bindParam(':room', $_POST['room']);
$stmt->bindParam(':status', $_POST['status']);
$stmt->execute();

class Result {}

$response = new Result();
$response->result = 'OK';
$response->message = 'Created with id: '.$db->lastInsertId();
$response->id = $db->lastInsertId();

header('Content-Type: application/json');
echo json_encode($response);

?>
