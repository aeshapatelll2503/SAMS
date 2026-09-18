<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT");
header("Access-Control-Allow-Headers: Content-Type");

$host = "localhost";
$db   = "sams_db";
$user = "root"; // Update with your DB credentials
$pass = ""; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Connection failed: " . $e->getMessage()]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

// 1. GET: Fetch all nodes
if ($method === 'GET') {
    $stmt = $pdo->query("SELECT * FROM org_nodes");
    echo json_encode($stmt->fetchAll());
}

// 2. POST: Create a new node
if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Agar id frontend se nahi aayi toh yahan generate hogi
    $id = !empty($data['id']) ? $data['id'] : 'TM-' . rand(100, 999);
    
    $sql = "INSERT INTO org_nodes (id, node_name, node_type, parent_id, headcount_limit, current_headcount, status, pos_x, pos_y) 
            VALUES (:id, :node_name, :node_type, :parent_id, :headcount_limit, :current_headcount, :status, :pos_x, :pos_y)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $id,
        ':node_name' => $data['node_name'] ?? 'New Node',
        ':node_type' => $data['node_type'] ?? 'TEAM',
        ':parent_id' => !empty($data['parent_id']) ? $data['parent_id'] : null,
        ':headcount_limit' => $data['headcount_limit'] ?? 100,
        ':current_headcount' => $data['current_headcount'] ?? 0,
        ':status' => $data['status'] ?? 'Active',
        ':pos_x' => $data['pos_x'] ?? 150,
        ':pos_y' => $data['pos_y'] ?? 150
    ]);
    
    echo json_encode(["status" => "success", "message" => "Node created", "id" => $id]);
    exit;
}

// 3. PUT: Update node position or properties (Upgraded for Type update)
if ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    $sql = "UPDATE org_nodes SET 
            node_name = :node_name, 
            node_type = :node_type, /* <-- Yeh field add hui hai */
            pos_x = :pos_x, 
            pos_y = :pos_y, 
            parent_id = :parent_id,
            headcount_limit = :headcount_limit,
            status = :status
            WHERE id = :id";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $data['id'],
        ':node_name' => $data['node_name'],
        ':node_type' => $data['node_type'], /* <-- Bind array node_type value */
        ':pos_x' => $data['pos_x'],
        ':pos_y' => $data['pos_y'],
        ':parent_id' => !empty($data['parent_id']) ? $data['parent_id'] : null,
        ':headcount_limit' => $data['headcount_limit'],
        ':status' => $data['status']
    ]);
    
    echo json_encode(["status" => "success", "message" => "Node updated successfully"]);
    exit;
}

?>

