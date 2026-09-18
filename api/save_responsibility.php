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
    $responsibility_id = $_POST['responsibility_id'] ?? '';
    $hierarchy_id = $_POST['role'] ?? ''; 
    $department = $_POST['department'] ?? 'General';
    $status = $_POST['status'] ?? 'Active';
    
    $owns = $_POST['owns_data'] ?? '[]';
    $reviews = $_POST['reviews_data'] ?? '[]';
    $approves = $_POST['approves_data'] ?? '[]';
    $escalates = $_POST['escalates_data'] ?? '[]';
    $alerts = '[]'; 

    if (empty($hierarchy_id)) {
        echo json_encode(['success' => false, 'message' => 'Role select karna zaroori hai.']);
        exit;
    }

    try {
        $role_stmt = $pdo->prepare("SELECT role_name FROM hierarchy WHERE id = ?");
        $role_stmt->execute([$hierarchy_id]);
        $role_row = $role_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$role_row) {
            echo json_encode(['success' => false, 'message' => 'चयनित रोल Hierarchy में नहीं मिला।']);
            exit;
        }

        $roleName = $role_row['role_name'] ?? 'Role';

        if (!empty($responsibility_id)) {
            // ==================== EDIT (UPDATE) ====================
            $stmt = $pdo->prepare("UPDATE responsibility SET hierarchy_id = ?, role_id = ?, department = ?, owns = ?, reviews = ?, approves = ?, escalates = ?, status = ? WHERE id = ?");
            $stmt->execute([$hierarchy_id, $hierarchy_id, $department, $owns, $reviews, $approves, $escalates, $status, $responsibility_id]);
            $is_success = true;
            $final_id = $responsibility_id;

            // Save Version Entry
            logRoleMapperVersion($pdo, "Updated responsibilities for role '{$roleName}' ({$final_id})");
        } else {
            // ==================== ADD (INSERT) ====================
            $final_id = 'RES-' . rand(100000, 999999);
            $stmt = $pdo->prepare("INSERT INTO responsibility (id, hierarchy_id, role_id, department, owns, reviews, approves, escalates, status, alerts) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$final_id, $hierarchy_id, $hierarchy_id, $department, $owns, $reviews, $approves, $escalates, $status, $alerts]);
            $is_success = ($stmt->rowCount() > 0);

            // Save Version Entry
            if ($is_success) {
                logRoleMapperVersion($pdo, "Added new responsibility mapping for role '{$roleName}' ({$final_id})");
            }
        }

        if ($is_success) {
            echo json_encode([
                'success' => true,
                'data' => [
                    'id' => $final_id,
                    'hierarchy_id' => $hierarchy_id,
                    'role_name' => $roleName,
                    'department' => $department,
                    'owns' => json_decode($owns),
                    'reviews' => json_decode($reviews),
                    'approves' => json_decode($approves),
                    'escalates' => json_decode($escalates),
                    'status' => $status
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Data save nahi ho saka.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    
    $role_stmt = null;
    $stmt = null;
}
?>