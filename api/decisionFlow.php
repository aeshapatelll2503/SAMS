<?php
header('Content-Type: application/json');

require_once dirname(__DIR__) . '/config.php';

// login check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$storageFile = __DIR__ . '/decision_flow.json';

// Initialize default state if file doesn't exist
if (!file_exists($storageFile)) {
    file_put_contents($storageFile, json_encode([
        'nodes' => [],
        'connections' => []
    ]));
}

// Helper Function: Insert snapshot entry into 'version' table
function logDecisionFlowVersion($pdo, $flowData, $remarks) {
    try {
        $snapshotPayload = json_encode($flowData);

        $versionId = 'ver_' . time() . '_' . rand(100, 999);
        $versionTag = 'v1.' . time();
        $targetType = 'DECISION_FLOW';
        $targetId = 'main_decision_flow';
        $createdBy = $_SESSION['user_id'] ?? $_SESSION['user_name'] ?? 'System';

        $versionStmt = $pdo->prepare("
            INSERT INTO version 
            (id, version_tag, target_type, target_id, snapshot_payload, remarks, created_by) 
            VALUES 
            (:id, :version_tag, :target_type, :target_id, :snapshot_payload, :remarks, :created_by)
        ");

        $versionStmt->execute([
            ':id'               => $versionId,
            ':version_tag'      => $versionTag,
            ':target_type'     => $targetType,
            ':target_id'       => $targetId,
            ':snapshot_payload' => $snapshotPayload,
            ':remarks'         => $remarks,
            ':created_by'       => $createdBy
        ]);
    } catch (Exception $e) {
        error_log("Decision Flow version snapshot creation failed: " . $e->getMessage());
    }
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        echo file_get_contents($storageFile);
        break;

    case 'POST':
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if ($data === null) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid JSON structure payload']);
            exit;
        }

        // 1. JSON file me save karein
        file_put_contents($storageFile, json_encode($data, JSON_PRETTY_PRINT));

        // 2. Custom action ke basis par message / version snapshot log karein
        $nodeCount = isset($data['nodes']) ? count($data['nodes']) : 0;
        $remarks = "Saved Decision Flow graph with {$nodeCount} nodes";
        
        if (isset($data['action']) && $data['action'] === 'clear') {
            $remarks = "Cleared Decision Flow canvas layout";
        }

        // 3. Version Table me entry insert karein (Dashboard History sync ke liye)
        logDecisionFlowVersion($pdo, $data, $remarks);

        echo json_encode(['status' => 'success', 'message' => 'Flow graph saved successfully']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
        break;
}
?>