<?php
include 'condb.php';

header('Content-Type: application/json');

$name = $_POST['name'];
$address = $_POST['address'];
$province = $_POST['province'];
$description = $_POST['description'];
$create = $_POST['created_at'];

////////////////////////////////////////////////////////////
// ✅ รับรูปภาพ
////////////////////////////////////////////////////////////

$imageName = "";

if (isset($_FILES['image'])) {

    $targetDir = "images/";   // ✅ โฟลเดอร์เก็บรูป
    $imageName = time() . "_" . basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . $imageName;

    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        echo json_encode([
            "success" => false,
            "error" => "Upload image failed"
        ]);
        exit;
    }
}

////////////////////////////////////////////////////////////
// ✅ Insert DB
////////////////////////////////////////////////////////////

try {

    $stmt = $conn->prepare("
        INSERT INTO places (name, address, province, description, image, created_at)
        VALUES (:name, :address, :province, :description, :image, :created_at)
    ");

    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":address", $address);
    $stmt->bindParam(":province", $province);
    $stmt->bindParam(":description", $description);
    $stmt->bindParam(":image", $imageName);
    $stmt->bindParam(":created_at", $create);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false]);
    }

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
