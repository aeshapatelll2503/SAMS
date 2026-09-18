<?php
// api/importExport.php

header('Content-Type: application/json');
require_once dirname(__DIR__) . '/config.php';
// login check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$method = $_SERVER['REQUEST_METHOD'];

// ==========================================
// A. EXPORT DATA (GET)
// ==========================================
if ($method === 'GET') {
    $source = $_GET['source'] ?? 'hierarchy'; // 'hierarchy', 'role_mapper', or 'decision_flow'
    $format = $_GET['format'] ?? 'json';      // 'json', 'yaml', 'visual'

    try {
        if ($source === 'role_mapper') {
            $query = "SELECT r.*, h.role_name 
                      FROM responsibility r 
                      LEFT JOIN hierarchy h ON r.hierarchy_id = h.id 
                      ORDER BY r.created_at DESC";
            $stmt = $pdo->query($query);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $filenamePrefix = "role_mapper_export_";

        } elseif ($source === 'decision_flow') {
            $flowFile = __DIR__ . '/decision_flow.json';
            $content = file_exists($flowFile) ? file_get_contents($flowFile) : json_encode(['nodes' => [], 'connections' => []]);
            $data = json_decode($content, true) ?? ['nodes' => [], 'connections' => []];
            $filenamePrefix = "decision_flow_export_";

        } else {
            $stmt = $pdo->query("SELECT * FROM hierarchy");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $filenamePrefix = "hierarchy_export_";
        }

        // 1. JSON EXPORT
        if ($format === 'json') {
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="' . $filenamePrefix . date('Y-m-d') . '.json"');
            echo json_encode($data, JSON_PRETTY_PRINT);
            exit;

        // 2. YAML EXPORT
        } elseif ($format === 'yaml') {
            header('Content-Type: text/yaml');
            header('Content-Disposition: attachment; filename="' . $filenamePrefix . date('Y-m-d') . '.yaml"');
            
            if ($source === 'decision_flow') {
                echo "nodes:\n";
                foreach (($data['nodes'] ?? []) as $n) {
                    echo "  - id: \"" . ($n['id'] ?? '') . "\"\n";
                    echo "    type: \"" . ($n['type'] ?? 'Step') . "\"\n";
                    echo "    label: \"" . addslashes($n['label'] ?? '') . "\"\n";
                    echo "    x: " . ((int)($n['x'] ?? 0)) . "\n";
                    echo "    y: " . ((int)($n['y'] ?? 0)) . "\n";
                }
                echo "connections:\n";
                foreach (($data['connections'] ?? []) as $c) {
                    echo "  - from: \"" . ($c['from'] ?? '') . "\"\n";
                    echo "    to: \"" . ($c['to'] ?? '') . "\"\n";
                    echo "    fromPort: \"" . ($c['fromPort'] ?? 'default') . "\"\n";
                }
            } else {
                foreach ($data as $item) {
                    echo "- id: \"" . ($item['id'] ?? '') . "\"\n";
                    if ($source === 'role_mapper') {
                        echo "  role_name: \"" . addslashes($item['role_name'] ?? '') . "\"\n";
                        echo "  department: \"" . addslashes($item['department'] ?? 'General') . "\"\n";
                        echo "  owns: " . ($item['owns'] ?? '[]') . "\n";
                        echo "  reviews: " . ($item['reviews'] ?? '[]') . "\n";
                        echo "  approves: " . ($item['approves'] ?? '[]') . "\n";
                        echo "  escalates: " . ($item['escalates'] ?? '[]') . "\n";
                        echo "  status: \"" . ($item['status'] ?? 'Active') . "\"\n\n";
                    } else {
                        echo "  role_name: \"" . addslashes($item['role_name'] ?? '') . "\"\n";
                        echo "  pos_x: " . ((int)($item['pos_x'] ?? 0)) . "\n";
                        echo "  pos_y: " . ((int)($item['pos_y'] ?? 0)) . "\n";
                        echo "  parent_id: " . ($item['parent_id'] ? '"' . $item['parent_id'] . '"' : 'null') . "\n";
                        echo "  role_category: \"" . ($item['role_category'] ?? 'HR Manager') . "\"\n";
                        echo "  description: \"" . addslashes($item['description'] ?? '') . "\"\n";
                        echo "  visual_color: \"" . ($item['visual_color'] ?? '#004ac6') . "\"\n\n";
                    }
                }
            }
            exit;

        // 3. VISUAL EXPORT (SVG Vector Graphics)
        } elseif ($format === 'visual' || $format === 'svg') {
            header('Content-Type: image/svg+xml');
            header('Content-Disposition: attachment; filename="' . $filenamePrefix . 'diagram_' . date('Y-m-d') . '.svg"');

            if ($source === 'decision_flow') {
                // Decision Flow SVG Workflow Diagram
                $svgWidth = 2500;
                $svgHeight = 2000;
                $nodes = $data['nodes'] ?? [];
                $connections = $data['connections'] ?? [];

                echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
                echo '<svg width="' . $svgWidth . '" height="' . $svgHeight . '" xmlns="http://www.w3.org/2000/svg">' . "\n";
                echo '<defs>
                        <marker id="arrow" viewBox="0 0 10 10" refX="6" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                            <path d="M 0 1 L 10 5 L 0 9 z" fill="#64748b" />
                        </marker>
                      </defs>' . "\n";
                echo '<style>
                        .bg { fill: #f8fafc; }
                        .node { fill: #ffffff; stroke: #94a3b8; stroke-width: 2; rx: 8; }
                        .node-start { stroke: #22c55e; }
                        .node-condition { stroke: #f59e0b; }
                        .node-approval { stroke: #3b82f6; }
                        .node-escalation { stroke: #a855f7; }
                        .node-stop { stroke: #e11d48; }
                        .text-title { font-family: sans-serif; font-weight: bold; font-size: 13px; fill: #1e293b; }
                        .line { stroke: #64748b; stroke-width: 2; fill: none; marker-end: url(#arrow); }
                      </style>' . "\n";
                echo '<rect width="100%" height="100%" class="bg"/>' . "\n";
                echo '<text x="20" y="40" font-family="sans-serif" font-weight="bold" font-size="22" fill="#2563eb">SAMS - Decision Flow Diagram</text>' . "\n";

                // Connections
                foreach ($connections as $conn) {
                    $fromNode = array_values(array_filter($nodes, fn($n) => $n['id'] === $conn['from']))[0] ?? null;
                    $toNode = array_values(array_filter($nodes, fn($n) => $n['id'] === $conn['to']))[0] ?? null;

                    if ($fromNode && $toNode) {
                        $x1 = ((int)$fromNode['x']) + 224;
                        $y1 = ((int)$fromNode['y']) + 40;
                        $x2 = (int)$toNode['x'];
                        $y2 = ((int)$toNode['y']) + 40;
                        $offset = max(40, abs($x2 - $x1) * 0.5);

                        echo "<path d=\"M {$x1} {$y1} C " . ($x1 + $offset) . " {$y1}, " . ($x2 - $offset) . " {$y2}, {$x2} {$y2}\" class=\"line\" />\n";
                    }
                }

                // Nodes
                foreach ($nodes as $n) {
                    $x = (int)($n['x'] ?? 0);
                    $y = (int)($n['y'] ?? 0);
                    $type = htmlspecialchars($n['type'] ?? 'Step');
                    $label = htmlspecialchars($n['label'] ?? 'Node');

                    $classStr = 'node';
                    if ($type === 'Start') $classStr .= ' node-start';
                    elseif ($type === 'Condition') $classStr .= ' node-condition';
                    elseif ($type === 'Approval') $classStr .= ' node-approval';
                    elseif ($type === 'Escalation') $classStr .= ' node-escalation';
                    elseif ($type === 'Stop') $classStr .= ' node-stop';

                    echo "<g transform=\"translate({$x}, {$y})\">\n";
                    echo "  <rect class=\"{$classStr}\" width=\"224\" height=\"80\" />\n";
                    echo "  <text x=\"15\" y=\"30\" font-family=\"sans-serif\" font-size=\"10\" font-weight=\"bold\" fill=\"#64748b\">{$type}</text>\n";
                    echo "  <text x=\"15\" y=\"55\" class=\"text-title\">{$label}</text>\n";
                    echo "</g>\n";
                }
                echo '</svg>';
                exit;

            } elseif ($source === 'role_mapper') {
                // Role Mapper Matrix SVG
                $cardWidth = 320;
                $cardHeight = 180;
                $cols = 3;
                $totalItems = count($data);
                $rows = max(1, ceil($totalItems / $cols));
                $svgWidth = max(1000, $cols * ($cardWidth + 20) + 40);
                $svgHeight = max(600, $rows * ($cardHeight + 20) + 80);

                echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
                echo '<svg width="' . $svgWidth . '" height="' . $svgHeight . '" xmlns="http://www.w3.org/2000/svg">' . "\n";
                echo '<style>
                        .bg { fill: #f7f9fb; }
                        .card { fill: #ffffff; stroke: #c3c6d7; stroke-width: 1; rx: 8; }
                        .title { font-family: sans-serif; font-weight: bold; font-size: 16px; fill: #004ac6; }
                        .dept { font-family: sans-serif; font-size: 11px; fill: #505f76; }
                        .label { font-family: sans-serif; font-weight: 600; font-size: 11px; fill: #191c1e; }
                      </style>' . "\n";
                echo '<rect width="100%" height="100%" class="bg"/>' . "\n";
                echo '<text x="20" y="40" font-family="sans-serif" font-weight="bold" font-size="22" fill="#004ac6">SAMS - Role Responsibilities Matrix</text>' . "\n";

                $i = 0;
                foreach ($data as $row) {
                    $c = $i % $cols;
                    $r = floor($i / $cols);
                    $x = 20 + $c * ($cardWidth + 20);
                    $y = 60 + $r * ($cardHeight + 20);

                    $roleName = htmlspecialchars($row['role_name'] ?? 'Role');
                    $dept = htmlspecialchars($row['department'] ?? 'General');
                    $status = htmlspecialchars($row['status'] ?? 'Active');

                    $ownsArr = json_decode($row['owns'] ?? '[]', true) ?? [];
                    $ownsTxt = htmlspecialchars(implode(', ', array_slice($ownsArr, 0, 3)));
                    if (empty($ownsTxt)) $ownsTxt = '—';

                    echo "<g transform=\"translate({$x}, {$y})\">\n";
                    echo "  <rect class=\"card\" width=\"{$cardWidth}\" height=\"{$cardHeight}\" />\n";
                    echo "  <rect x=\"0\" y=\"0\" width=\"{$cardWidth}\" height=\"6\" fill=\"#2563eb\" rx=\"3\" />\n";
                    echo "  <text x=\"15\" y=\"30\" class=\"title\">{$roleName}</text>\n";
                    echo "  <text x=\"15\" y=\"50\" class=\"dept\">Department: {$dept} | Status: {$status}</text>\n";
                    echo "  <line x1=\"15\" y1=\"60\" x2=\"" . ($cardWidth - 15) . "\" y2=\"60\" stroke=\"#e0e3e5\" />\n";
                    echo "  <text x=\"15\" y=\"80\" class=\"label\">Owns: {$ownsTxt}</text>\n";
                    echo "</g>\n";
                    $i++;
                }
                echo '</svg>';
                exit;

            } else {
                // Hierarchy Designer Tree Diagram SVG
                $svgWidth = 3000;
                $svgHeight = 2000;

                echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
                echo '<svg width="' . $svgWidth . '" height="' . $svgHeight . '" xmlns="http://www.w3.org/2000/svg">' . "\n";
                echo '<style>
                        .node-card { fill: #ffffff; stroke: #c3c6d7; stroke-width: 1; rx: 8; }
                        .node-title { font-family: sans-serif; font-weight: bold; font-size: 14px; fill: #191c1e; }
                        .node-cat { font-family: sans-serif; font-size: 10px; fill: #434655; }
                        .line { stroke: #2563eb; stroke-width: 2; fill: none; }
                      </style>' . "\n";
                echo '<rect width="100%" height="100%" fill="#f7f9fb"/>' . "\n";

                // Draw Lines
                foreach ($data as $node) {
                    if (!empty($node['parent_id'])) {
                        $parent = array_values(array_filter($data, fn($n) => $n['id'] === $node['parent_id']))[0] ?? null;
                        if ($parent) {
                            $pX = ((int)$parent['pos_x']) + 120;
                            $pY = ((int)$parent['pos_y']) + 65;
                            $cX = ((int)$node['pos_x']) + 120;
                            $cY = (int)$node['pos_y'];
                            $controlY = $pY + ($cY - $pY) / 2;

                            echo "<path d=\"M {$pX} {$pY} C {$pX} {$controlY}, {$cX} {$controlY}, {$cX} {$cY}\" class=\"line\" />\n";
                        }
                    }
                }

                // Draw Nodes
                foreach ($data as $node) {
                    $x = (int)($node['pos_x'] ?? 0);
                    $y = (int)($node['pos_y'] ?? 0);
                    $color = $node['visual_color'] ?? '#004ac6';
                    $name = htmlspecialchars($node['role_name'] ?? 'Node');
                    $cat = htmlspecialchars($node['role_category'] ?? 'Role');

                    echo "<g transform=\"translate({$x}, {$y})\">\n";
                    echo "  <rect class=\"node-card\" width=\"240\" height=\"80\" />\n";
                    echo "  <rect x=\"0\" y=\"0\" width=\"240\" height=\"6\" fill=\"{$color}\" rx=\"3\" />\n";
                    echo "  <text x=\"15\" y=\"35\" class=\"node-title\">{$name}</text>\n";
                    echo "  <text x=\"15\" y=\"58\" class=\"node-cat\">Category: {$cat}</text>\n";
                    echo "</g>\n";
                }

                echo '</svg>';
                exit;
            }
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// ==========================================
// B. IMPORT DATA (POST)
// ==========================================
elseif ($method === 'POST') {
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'error' => 'File upload failed or no file selected.']);
        exit;
    }

    $fileTmpPath = $_FILES['file']['tmp_name'];
    $fileName = $_FILES['file']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $content = file_get_contents($fileTmpPath);

    if ($fileExtension === 'json') {
        $importedData = json_decode($content, true);
        if (!is_array($importedData)) {
            echo json_encode(['success' => false, 'error' => 'Invalid JSON file structure.']);
            exit;
        }

        try {
            // Check if Decision Flow JSON
            if (isset($importedData['nodes']) && isset($importedData['connections'])) {
                $flowFile = __DIR__ . '/decision_flow.json';
                file_put_contents($flowFile, json_encode($importedData, JSON_PRETTY_PRINT));
                echo json_encode(['success' => true, 'count' => count($importedData['nodes'])]);
                exit;
            }

            $pdo->beginTransaction();

            // Check if Role Mapper JSON
            $isRoleMapper = isset($importedData[0]['owns']) || isset($importedData[0]['hierarchy_id']);

            if ($isRoleMapper) {
                $pdo->exec("DELETE FROM responsibility");
                $stmt = $pdo->prepare("
                    INSERT INTO responsibility (id, hierarchy_id, role_id, department, owns, reviews, approves, escalates, status, alerts)
                    VALUES (:id, :hierarchy_id, :role_id, :department, :owns, :reviews, :approves, :escalates, :status, :alerts)
                ");

                foreach ($importedData as $row) {
                    $stmt->execute([
                        'id'           => $row['id'] ?? ('RES-' . rand(100000, 999999)),
                        'hierarchy_id' => $row['hierarchy_id'] ?? $row['id'],
                        'role_id'      => $row['role_id'] ?? $row['hierarchy_id'] ?? $row['id'],
                        'department'   => $row['department'] ?? 'General',
                        'owns'         => is_array($row['owns']) ? json_encode($row['owns']) : ($row['owns'] ?? '[]'),
                        'reviews'      => is_array($row['reviews']) ? json_encode($row['reviews']) : ($row['reviews'] ?? '[]'),
                        'approves'     => is_array($row['approves']) ? json_encode($row['approves']) : ($row['approves'] ?? '[]'),
                        'escalates'    => is_array($row['escalates']) ? json_encode($row['escalates']) : ($row['escalates'] ?? '[]'),
                        'status'       => $row['status'] ?? 'Active',
                        'alerts'       => '[]'
                    ]);
                }
            } else {
                // Hierarchy Designer
                $pdo->exec("SET FOREIGN_KEY_CHECKS = 0; TRUNCATE TABLE hierarchy; SET FOREIGN_KEY_CHECKS = 1;");
                $stmt = $pdo->prepare("
                    INSERT INTO hierarchy (id, role_name, pos_x, pos_y, parent_id, role_category, description, visual_color)
                    VALUES (:id, :role_name, :pos_x, :pos_y, :parent_id, :role_category, :description, :visual_color)
                ");

                foreach ($importedData as $node) {
                    $stmt->execute([
                        'id'            => $node['id'] ?? ('NOD-' . time() . '-' . rand(100, 999)),
                        'role_name'     => $node['role_name'] ?? ($node['name'] ?? 'New Node'),
                        'pos_x'         => (int)($node['pos_x'] ?? 100),
                        'pos_y'         => (int)($node['pos_y'] ?? 100),
                        'parent_id'     => !empty($node['parent_id']) ? $node['parent_id'] : null,
                        'role_category' => $node['role_category'] ?? ($node['type'] ?? 'HR Manager'),
                        'description'   => $node['description'] ?? '',
                        'visual_color'  => $node['visual_color'] ?? ($node['color'] ?? '#004ac6')
                    ]);
                }
            }

            $pdo->commit();
            echo json_encode(['success' => true, 'count' => count($importedData)]);

        } catch (PDOException $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Only JSON file imports are currently supported.']);
    }
}
?>