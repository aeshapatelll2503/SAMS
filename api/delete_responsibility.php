<?php
header('Content-Type: application/json');

require_once dirname(__DIR__) . '/config.php';

/** @var PDO $pdo */

// Helper function to insert into version table
function logRoleMapperVersion($pdo, $remarks) {
    try {
        $stmt = $pdo->query("SELECT * FROM responsibility");
        $allResponsibilities = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $snapshotPayload = json_encode(['responsibilities' => $allResponsibilities]);

        $versionId = 'ver_' . time() . '_' . rand(100, 999);
        $versionTag = 'v1.' . time();
        $targetType = 'ROLE_MAPPER';
        $targetId = 'responsibility_mapping';
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
        error_log("Role mapper version snapshot creation failed: " . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!can_edit()) {
        echo json_encode(['success' => false, 'message' => 'Permission denied: Viewers have read-only access.']);
        exit;
    }
    $id = $_POST['id'] ?? '';

    if (empty($id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM responsibility WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            // Save Version Entry
            logRoleMapperVersion($pdo, "Deleted responsibility mapping (ID: {$id})");

            echo json_encode(['success' => true, 'message' => 'Successfully deleted.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Record not found or already deleted.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }

    $stmt = null;
}
?>