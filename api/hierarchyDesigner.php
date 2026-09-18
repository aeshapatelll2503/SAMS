<?php
// api/hierarchyDesigner.php

header('Content-Type: application/json');

require_once dirname(__DIR__) . '/config.php';
// login check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// ===================================================
// Helper Function: Create Automatic Version Snapshot
// ===================================================
function createVersionSnapshot($pdo, $remarks = 'Hierarchy updated') {
    try {
        // 1. पूरी Hierarchy का वर्तमान Snapshot फ़ैच करें
        $stmt = $pdo->query("SELECT * FROM hierarchy");
        $nodes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $snapshotPayload = json_encode(['nodes' => $nodes]);

        // 2. Metadata तैयार करें
        $versionId = 'ver_' . time() . '_' . rand(100, 999);
        $versionTag = 'v1.' . time();
        $targetType = 'HIERARCHY';
        $targetId = 'main_hierarchy';
        $createdBy = $_SESSION['user_id'] ?? $_SESSION['user_name'] ?? 'System';

        // 3. 'version' टेबल में इंसर्ट करें
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
        // Version log fail होने पर मुख्य फ्लो न रुके
        error_log("Version snapshot creation failed: " . $e->getMessage());
    }
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {

    try {
        $stmt = $pdo->query("SELECT * FROM hierarchy");
        $nodes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($nodes);

    } catch (PDOException $e) {

        echo json_encode([
            'success' => false,
            'error'   => $e->getMessage()
        ]);
    }
}

elseif ($method === 'POST') {

    $input  = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';

    try {

        // ==========================================
        // A. CREATE NEW NODE
        // ==========================================
        if ($action === 'create') {

            $id = 'NOD-' . time() . '-' . rand(100, 999);
            $role_name = $input['name'] ?? 'New Node';

            $stmt = $pdo->prepare("
                INSERT INTO hierarchy
                (id, role_name, pos_x, pos_y, parent_id, role_category, description, visual_color)
                VALUES
                (:id, :role_name, 100, 100, NULL, 'HR Manager', '', '#004ac6')
            ");

            $stmt->execute([
                'id'        => $id,
                'role_name' => $role_name
            ]);

            // Save Version Entry
            createVersionSnapshot($pdo, "Created node: '{$role_name}' ({$id})");

            echo json_encode([
                'success' => true,
                'id'      => $id
            ]);
        }


        // ==========================================
        // B. UPDATE NODE PROPERTIES
        // ==========================================
        elseif ($action === 'update_name') {

            $id            = $input['id'] ?? '';
            $role_name     = $input['name'] ?? 'New Node';
            $role_category = $input['type'] ?? 'HR Manager';
            $description   = $input['description'] ?? '';
            $visual_color  = $input['color'] ?? '#004ac6';

            $stmt = $pdo->prepare("
                UPDATE hierarchy
                SET
                    role_name     = :role_name,
                    role_category = :role_category,
                    description   = :description,
                    visual_color  = :visual_color
                WHERE id = :id
            ");

            $stmt->execute([
                'role_name'     => $role_name,
                'role_category' => $role_category,
                'description'   => $description,
                'visual_color'  => $visual_color,
                'id'            => $id
            ]);

            // Save Version Entry
            createVersionSnapshot($pdo, "Updated properties for node: '{$role_name}'");

            echo json_encode([
                'success' => true
            ]);
        }


        // ==========================================
        // C. UPDATE NODE POSITION
        // ==========================================
        elseif ($action === 'update_position') {

            $id = $input['id'] ?? '';
            $x  = (int)($input['pos_x'] ?? 0);
            $y  = (int)($input['pos_y'] ?? 0);

            $stmt = $pdo->prepare("
                UPDATE hierarchy
                SET pos_x = :pos_x, pos_y = :pos_y
                WHERE id = :id
            ");

            $stmt->execute([
                'pos_x' => $x,
                'pos_y' => $y,
                'id'    => $id
            ]);

            // Save Version Entry
            createVersionSnapshot($pdo, "Moved node position (ID: {$id})");

            echo json_encode([
                'success' => true
            ]);
        }


        // ==========================================
        // D. CONNECT CHILD NODE TO PARENT NODE
        // ==========================================
        elseif ($action === 'connect_nodes') {

            $child_id  = $input['child_id'] ?? '';
            $parent_id = !empty($input['parent_id']) ? $input['parent_id'] : null;

            $stmt = $pdo->prepare("
                UPDATE hierarchy
                SET parent_id = :parent_id
                WHERE id = :child_id
            ");

            $stmt->execute([
                'parent_id' => $parent_id,
                'child_id'  => $child_id
            ]);

            // Save Version Entry
            createVersionSnapshot($pdo, "Connected node {$child_id} to parent " . ($parent_id ?? 'Root'));

            echo json_encode([
                'success' => true
            ]);
        }


        // ==========================================
        // E. DELETE NODE
        // ==========================================
        elseif ($action === 'delete_node') {

            $id = $input['id'] ?? '';

            // Remove parent connection from child nodes
            $detachChildren = $pdo->prepare("
                UPDATE hierarchy
                SET parent_id = NULL
                WHERE parent_id = :id
            ");
            $detachChildren->execute(['id' => $id]);

            // Delete selected node
            $stmt = $pdo->prepare("
                DELETE FROM hierarchy
                WHERE id = :id
            ");
            $stmt->execute(['id' => $id]);

            // Save Version Entry
            createVersionSnapshot($pdo, "Deleted node (ID: {$id})");

            echo json_encode([
                'success' => true
            ]);
        }


        // ==========================================
        // INVALID ACTION
        // ==========================================
        else {

            echo json_encode([
                'success' => false,
                'error'   => 'Invalid action'
            ]);
        }

    } catch (PDOException $e) {

        echo json_encode([
            'success' => false,
            'error'   => $e->getMessage()
        ]);
    }
}
?>