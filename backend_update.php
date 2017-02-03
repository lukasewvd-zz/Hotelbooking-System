<?php
require_once '_db.php';

$stmt = $db->prepare("UPDATE reservations SET name = :name, note = :note, phoneNumber = :phoneNumber, amtAdults = :amtAdults, amtYouth = :amtYouth, amtChildren = :amtChildren, start = :start, end = :end, room_id = :room, status = :status, paid = :paid WHERE id = :id");
$stmt->bindParam(':id', $_POST['id']);
$stmt->bindParam(':name', $_POST['name']);
$stmt->bindParam(':note', $_POST['note']);
$stmt->bindParam(':amtAdults', $_POST['amtAdults']);
$stmt->bindParam(':amtYouth', $_POST['amtYouth']);
$stmt->bindParam(':amtChildren', $_POST['amtChildren']);
$stmt->bindParam(':phoneNumber', $_POST['phoneNumber']);
$stmt->bindParam(':start', $_POST['start']);
$stmt->bindParam(':end', $_POST['end']);
$stmt->bindParam(':room', $_POST['room']);
$stmt->bindParam(':status', $_POST['status']);
$stmt->bindParam(':paid', $_POST['paid']);
$stmt->execute();

class Result {}

$response = new Result();
$response->result = 'OK';
$response->message = 'Update successful';

header('Content-Type: application/json');
echo json_encode($response);

?>
