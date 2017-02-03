<?php
require_once '_db.php';

$stmt = $db->prepare("INSERT INTO reservations (name, note, phoneNumber, amtAdults, amtYouth, amtChildren, start, end, room_id, status, paid) VALUES (:name, :note, :phoneNumber, :amtAdults, :amtYouth, :amtChildren, :start, :end, :room, 'New', 'No')");
$stmt->bindParam(':start', $_POST['start']);
$stmt->bindParam(':end', $_POST['end']);
$stmt->bindParam(':name', $_POST['name']);
$stmt->bindParam(':note', $_POST['note']);
$stmt->bindParam(':phoneNumber', $_POST['phoneNumber']);
$stmt->bindParam(':amtAdults', $_POST['amtAdults']);
$stmt->bindParam(':amtYouth', $_POST['amtYouth']);
$stmt->bindParam(':amtChildren', $_POST['amtChildren']);
$stmt->bindParam(':room', $_POST['room']);
$stmt->execute();

class Result {}

$response = new Result();
$response->result = 'OK';
$response->message = 'Created with id: '.$db->lastInsertId();
$response->id = $db->lastInsertId();

header('Content-Type: application/json');
echo json_encode($response);

?>
