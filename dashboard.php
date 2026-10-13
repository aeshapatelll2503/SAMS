<?php

require_once __DIR__ . '/config.php'; 

// login check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

try {
    // hierarchy data count
    $structuresCount = $pdo->query("SELECT COUNT(*) FROM hierarchy")->fetchColumn();

    // roles data count
    $rolesCount = $pdo->query("SELECT COUNT(*) FROM responsibility")->fetchColumn();

    // decision flow data count from JSON file
    $jsonFilePath = __DIR__ . '/api/decision_flow.json';
    $flowsCount = 0;

    if (file_exists($jsonFilePath)) {
        $jsonContent = file_get_contents($jsonFilePath);
        $flowData = json_decode($jsonContent, true);
        if (isset($flowData['nodes']) && is_array($flowData['nodes'])) {
            $flowsCount = count($flowData['nodes']);
        }
    }

    // users data count and role distribution
    $usersCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $roleStats = ['admin' => 0, 'manager' => 0, 'viewer' => 0];
    try {
        $roleStmt = $pdo->query("SELECT COALESCE(role, 'viewer') as role, COUNT(*) as cnt FROM users GROUP BY role");
        while ($rRow = $roleStmt->fetch(PDO::FETCH_ASSOC)) {
            $rKey = strtolower($rRow['role']);
            if (isset($roleStats[$rKey])) {
                $roleStats[$rKey] = (int)$rRow['cnt'];
            }
        }
    } catch (Exception $e) {}

    // 5. Version History Extraction (Fetching ALL versions for view-all modal)
    $allActivities = [];
    try {
        $versionQuery = "SELECT 
            v.id, 
            v.version_tag, 
            COALESCE(u.full_name, CONCAT('User #', v.created_by), 'System') AS user_name, 
            COALESCE(v.remarks, 'Configuration snapshot updated') AS description, 
            v.created_at
            FROM version v
            LEFT JOIN users u ON CAST(v.created_by AS CHAR) = CAST(u.id AS CHAR)
            ORDER BY v.created_at DESC";

        $activityStmt = $pdo->query($versionQuery);
        $allActivities = $activityStmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        $fallbackQuery = "SELECT 
            id, 
            version_tag, 
            CONCAT('User #', COALESCE(created_by, 'System')) AS user_name, 
            COALESCE(remarks, 'Configuration snapshot updated') AS description, 
            created_at
            FROM version 
            ORDER BY created_at DESC";

        $activityStmt = $pdo->query($fallbackQuery);
        $allActivities = $activityStmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Recent 5 activities for main dashboard card
    $activities = array_slice($allActivities, 0, 5);

} catch (PDOException $e) {
    die("डेटाबेस लोड एरर: " . $e->getMessage());
}


function time_ago($timestamp) {
    $time_ago = strtotime($timestamp);
    $current_time = time();
    $time_difference = $current_time - $time_ago;
    $seconds = $time_difference;
    
    $minutes = round($seconds / 60);
    $hours   = round($seconds / 3600);
    $days    = round($seconds / 86400);

    if ($seconds <= 60) return "Just now";
    else if ($minutes <= 60) return "$minutes minutes ago";
    else if ($hours <= 24) return "$hours hours ago";
    else return "$days days ago";
}
?>

<!DOCTYPE html>
<html class="light h-full" lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Structural Authority Mapping System (SAMS) - Dashboard</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "outline-variant": "#c3c6d7",
                        "primary": "#004ac6",
                        "outline": "#737686",
                        "tertiary-container": "#656d84",
                        "primary-container": "#2563eb",
                        "on-primary": "#ffffff",
                        "on-tertiary": "#ffffff",
                        "inverse-primary": "#b4c5ff",
                        "surface-container-low": "#f2f4f6",
                        "primary-fixed": "#dbe1ff",
                        "on-primary-fixed": "#00174b",
                        "on-tertiary-fixed-variant": "#3f465c",
                        "surface-variant": "#e0e3e5",
                        "secondary-fixed": "#d3e4fe",
                        "primary-fixed-dim": "#b4c5ff",
                        "surface-tint": "#0053db",
                        "on-secondary-container": "#54647a",
                        "tertiary": "#4d556b",
                        "surface-container-highest": "#e0e3e5",
                        "on-tertiary-fixed": "#131b2e",
                        "on-background": "#191c1e",
                        "surface-container-high": "#e6e8ea",
                        "surface": "#f7f9fb",
                        "surface-container": "#eceef0",
                        "on-primary-fixed-variant": "#003ea8",
                        "tertiary-fixed-dim": "#bec6e0",
                        "surface-dim": "#d8dadc",
                        "on-secondary-fixed": "#0b1c30",
                        "on-secondary-fixed-variant": "#38485d",
                        "on-secondary": "#ffffff",
                        "on-primary-container": "#eeefff",
                        "surface-bright": "#f7f9fb",
                        "surface-container-lowest": "#ffffff",
                        "on-error-container": "#93000a",
                        "background": "#f7f9fb",
                        "secondary-container": "#d0e1fb",
                        "on-tertiary-container": "#eef0ff",
                        "on-error": "#ffffff",
                        "error-container": "#ffdad6",
                        "secondary-fixed-dim": "#b7c8e1",
                        "inverse-on-surface": "#eff1f3",
                        "secondary": "#505f76",
                        "on-surface": "#191c1e",
                        "inverse-surface": "#2d3133",
                        "on-surface-variant": "#434655",
                        "tertiary-fixed": "#dae2fd",
                        "error": "#ba1a1a"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.125rem",
                        "lg": "0.25rem",
                        "xl": "0.5rem",
                        "full": "0.75rem"
                    },
                    "spacing": {
                        "md": "16px",
                        "sm": "8px",
                        "xl": "32px",
                        "margin-mobile": "16px",
                        "base": "4px",
                        "xs": "4px",
                        "2xl": "48px",
                        "gutter": "16px",
                        "lg": "24px",
                        "margin-desktop": "32px"
                    }
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24
        }
        .material-symbols-outlined.fill {
            font-variation-settings: 'FILL' 1;
        }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 3px; }
    </style>
</head>

<body class="bg-surface text-on-surface font-body-md h-full flex flex-col antialiased overflow-hidden">
    <nav class="bg-surface-container-lowest text-primary font-body-md text-body-md w-full sticky top-0 z-50 border-b border-outline-variant flex justify-between items-center px-lg h-16 shrink-0">
        <div class="flex items-center gap-md">
            <span class="font-headline-xl text-headline-xl font-bold text-primary text-[25px]">SAMS</span>
        </div>
        
        <div class="flex items-center gap-md">
            <div class="flex items-center gap-3 text-on-surface">
                <span class="font-medium text-sm">Hi, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>!</span>
                <?php echo render_role_badge(); ?>
                <a href="./api/logout.php" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm font-medium transition-colors">Logout</a>
            </div>
        </div>
    </nav>

    <div class="flex flex-1 h-[calc(100vh-64px)] w-full overflow-hidden">
        <aside class="bg-surface-container-low text-primary font-label-md text-label-md w-64 border-r border-outline-variant flex flex-col z-40 shrink-0 hidden md:flex h-full sticky top-0 overflow-y-auto">
            <div class="p-lg border-b border-outline-variant flex flex-col gap-sm">
                <div class="flex items-center gap-sm">
                    <div class="w-10 h-10 rounded bg-primary-container text-on-primary flex items-center justify-center font-bold text-title-lg shadow-sm">
                        SA
                    </div>
                    <div>
                        <h2 class="font-title-md text-title-md font-bold text-on-surface leading-tight">Structural <br/> Authority</h2>
                    </div>
                </div>
            </div>
            <nav class="flex-1 pt-0 pb-md">
                <ul class="flex flex-col m-0 p-0 list-none">
                    <li>
                        <a class="group flex items-center px-md py-sm cursor-pointer bg-secondary-container text-on-secondary-container border-l-4 border-primary font-bold" href="dashboard.php">
                            <span class="material-symbols-outlined mr-sm text-[17px] text-primary fill">dashboard</span>
                            <span class="text-sm">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a class="group flex items-center px-md py-sm cursor-pointer text-secondary hover:bg-surface-variant transition-all border-l-4 border-transparent hover:border-outline-variant" href="hierarchyDesigner.php">
                            <span class="material-symbols-outlined mr-sm text-[17px]">account_tree</span>
                            <span class="text-sm">Hierarchy Designer</span>
                        </a>
                    </li>
                    <li>
                        <a class="group flex items-center px-md py-sm cursor-pointer text-secondary hover:bg-surface-variant transition-all border-l-4 border-transparent hover:border-outline-variant" href="roleMapper.php">
                            <span class="material-symbols-outlined mr-sm text-[17px]">badge</span>
                            <span class="text-sm">Role Mapper</span>
                        </a>
                    </li>
                    <li>
                        <a class="group flex items-center px-md py-sm cursor-pointer text-secondary hover:bg-surface-variant transition-all border-l-4 border-transparent hover:border-outline-variant" href="decisionFlow.php">
                            <span class="material-symbols-outlined mr-sm text-[17px]">alt_route</span>
                            <span class="text-sm">Decision Flow</span>
                        </a>
                    </li>
                    <li>
                        <a class="group flex items-center px-md py-sm cursor-pointer text-secondary hover:bg-surface-variant transition-all border-l-4 border-transparent hover:border-outline-variant" href="importExport.php">
                            <span class="material-symbols-outlined mr-sm text-[17px]">swap_vert</span>
                            <span class="text-sm">Import/Export</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="flex-1 overflow-y-auto p-margin-mobile md:p-margin-desktop bg-background custom-scrollbar">
            <header class="mb-lg flex flex-col md:flex-row md:items-end justify-between gap-md">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></h1>
                        <?php echo render_role_badge(); ?>
                    </div>
                    <p class="font-body-lg text-body-lg text-secondary mt-xs">
                        <?php 
                        $curRole = get_current_user_role();
                        if ($curRole === 'admin') {
                            echo "Admin Workspace &bull; You have full administrative control and management authority across all system modules.";
                        } elseif ($curRole === 'manager') {
                            echo "Manager Workspace &bull; You have authority to design hierarchies, map responsibilities, and configure decision flows.";
                        } else {
                            echo "Viewer Workspace &bull; You have read-only access to inspect organizational structures, mapped roles, and decision flows.";
                        }
                        ?>
                    </p>
                </div>
            </header>

            <?php if (is_viewer()): ?>
            <div class="mb-lg p-md rounded-lg bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-sm">
                    <span class="material-symbols-outlined text-[22px] text-emerald-600">visibility</span>
                    <span class="text-sm"><strong>Viewer Mode:</strong> You are logged in with a standard email (read-only mode). To get Admin access use an <code>.admin@</code> email or for Manager use <code>.manager@</code> email.</span>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 uppercase tracking-wider shrink-0">Read Only</span>
            </div>
            <?php endif; ?>

            <!-- Overview Cards Grid -->
            <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-md mb-xl">
                <div class="bg-surface-container-lowest border border-outline-variant rounded-lg p-md flex flex-col justify-between relative overflow-hidden group hover:border-primary transition-colors cursor-default">
                    <div class="flex justify-between items-start mb-md">
                        <div>
                            <p class="font-label-md text-label-md text-secondary uppercase tracking-wider">Total Structures</p>
                            <h3 class="font-display-lg text-display-lg font-bold text-on-surface mt-xs"><?php echo $structuresCount; ?></h3>
                        </div>
                        <div class="w-8 h-8 rounded bg-primary-fixed flex items-center justify-center text-on-primary-fixed">
                            <span class="material-symbols-outlined text-[20px]">account_tree</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-xs text-secondary text-label-md font-label-md">
                        <span class="material-symbols-outlined text-[16px] text-[#16a34a]">trending_up</span>
                        <span class="text-[#16a34a] font-medium">Live</span> mapping active
                    </div>
                </div>

                <div class="bg-surface-container-lowest border border-outline-variant rounded-lg p-md flex flex-col justify-between relative overflow-hidden group hover:border-primary transition-colors cursor-default">
                    <div class="flex justify-between items-start mb-md">
                        <div>
                            <p class="font-label-md text-label-md text-secondary uppercase tracking-wider">Active Roles</p>
                            <h3 class="font-display-lg text-display-lg font-bold text-on-surface mt-xs"><?php echo $rolesCount; ?></h3>
                        </div>
                        <div class="w-8 h-8 rounded bg-secondary-fixed flex items-center justify-center text-on-secondary-fixed">
                            <span class="material-symbols-outlined text-[20px]">badge</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-xs text-secondary text-label-md font-label-md">
                        <span class="material-symbols-outlined text-[16px] text-[#16a34a]">trending_up</span>
                        Mapped system wide
                    </div>
                </div>

                <div class="bg-surface-container-lowest border border-outline-variant rounded-lg p-md flex flex-col justify-between relative overflow-hidden group hover:border-primary transition-colors cursor-default">
                    <div class="flex justify-between items-start mb-md">
                        <div>
                            <p class="font-label-md text-label-md text-secondary uppercase tracking-wider">Decision Flows</p>
                            <h3 class="font-display-lg text-display-lg font-bold text-on-surface mt-xs"><?php echo $flowsCount; ?></h3>
                        </div>
                        <div class="w-8 h-8 rounded bg-tertiary-fixed flex items-center justify-center text-on-tertiary-fixed">
                            <span class="material-symbols-outlined text-[20px]">alt_route</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-xs text-secondary text-label-md font-label-md">
                        <span class="material-symbols-outlined text-[16px] text-on-surface-variant">trending_flat</span>
                        Active flow nodes
                    </div>
                </div>

                <div class="bg-surface-container-lowest border border-outline-variant rounded-lg p-md flex flex-col justify-between relative overflow-hidden group hover:border-primary transition-colors cursor-default">
                    <div class="flex justify-between items-start mb-md">
                        <div>
                            <p class="font-label-md text-label-md text-secondary uppercase tracking-wider">Team Members</p>
                            <h3 class="font-display-lg text-display-lg font-bold text-on-surface mt-xs"><?php echo $usersCount; ?></h3>
                        </div>
                        <div class="w-8 h-8 rounded bg-surface-variant flex items-center justify-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-[20px]">groups</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] pt-1.5 border-t border-outline-variant/50 font-medium">
                        <span class="text-purple-600 font-semibold" title="Admin Accounts">Admin: <?php echo $roleStats['admin']; ?></span>
                        <span class="text-blue-600 font-semibold" title="Manager Accounts">Mgr: <?php echo $roleStats['manager']; ?></span>
                        <span class="text-emerald-600 font-semibold" title="Viewer Accounts">Viewer: <?php echo $roleStats['viewer']; ?></span>
                    </div>
                </div>
            </section>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-md">
                <!-- VERSION HISTORY SECTION -->
                <section class="lg:col-span-2 bg-surface-container-lowest border border-outline-variant rounded-lg flex flex-col">
                    <div class="p-md border-b border-outline-variant flex justify-between items-center">
                        <div>
                            <h2 class="font-title-lg text-title-lg font-bold text-on-surface">Version History</h2>
                            <p class="text-xs text-secondary">Track structural changes and rollback snapshots</p>
                        </div>
                        <button onclick="openAllVersionsModal()" class="text-primary font-label-md text-label-md hover:underline flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">history</span> View All Versions
                        </button>
                    </div>
                    <div class="p-md flex-1 overflow-y-auto">
                        <ul class="space-y-md relative before:absolute before:inset-y-0 before:left-[15px] before:w-px before:bg-outline-variant before:-z-10 ml-xs">
                            <?php if (empty($activities)): ?>
                                <p class="text-secondary text-sm p-4">No versions recorded yet in database.</p>
                            <?php else: ?>
                                <?php foreach ($activities as $activity): 
                                    $versionTag = $activity['version_tag'] ?? ('v1.' . $activity['id']);
                                ?>
                                <li class="flex gap-md relative">
                                    <div class="w-8 h-8 rounded-full bg-primary-container text-on-primary border-2 border-surface-container-lowest flex items-center justify-center shrink-0 z-10 font-mono text-xs font-bold">
                                        <span class="material-symbols-outlined text-[16px]">commit</span>
                                    </div>
                                    
                                    <div class="pb-md pt-xs flex-1 bg-surface-container-low p-sm rounded border border-outline-variant">
                                        <div class="flex justify-between items-center mb-xs">
                                            <span class="bg-primary/10 text-primary px-2 py-0.5 rounded text-xs font-mono font-bold">
                                                <?php echo htmlspecialchars($versionTag); ?>
                                            </span>
                                            <span class="text-xs text-secondary flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[14px]">schedule</span>
                                                <?php echo time_ago($activity['created_at']); ?>
                                            </span>
                                        </div>

                                        <p class="font-body-md text-sm text-on-surface mt-xs">
                                            <span class="font-semibold text-primary"><?php echo htmlspecialchars($activity['user_name']); ?></span> 
                                            - <?php echo htmlspecialchars($activity['description']); ?>
                                        </p>

                                        <div class="mt-sm pt-xs border-t border-outline-variant/50 flex gap-md text-xs">
                                            <button onclick="compareVersion('<?php echo $activity['id']; ?>')" class="text-primary hover:underline flex items-center gap-1 font-medium">
                                                <span class="material-symbols-outlined text-[14px]">difference</span> Compare Changes
                                            </button>
                                            <button onclick="rollbackVersion('<?php echo $activity['id']; ?>')" class="text-error hover:underline flex items-center gap-1 font-medium ml-auto">
                                                <span class="material-symbols-outlined text-[14px]">undo</span> Rollback Version
                                            </button>
                                        </div>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </section>

                <section class="bg-surface-container-lowest border border-outline-variant rounded-lg flex flex-col">
                    <div class="p-md border-b border-outline-variant">
                        <h2 class="font-title-lg text-title-lg font-bold text-on-surface">Quick Actions</h2>
                    </div>
                    <div class="p-md flex flex-col gap-sm">
                        <a href="hierarchyDesigner.php?action=new" class="w-full text-left p-md border border-outline-variant rounded-DEFAULT hover:border-primary hover:bg-surface-container-low transition-all group flex items-start gap-md">
                            <div class="w-10 h-10 rounded bg-primary-container text-on-primary flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                <span class="material-symbols-outlined text-[20px]">account_tree</span>
                            </div>
                            <div>
                                <h3 class="font-title-md text-title-md font-semibold text-on-surface group-hover:text-primary transition-colors">Create New Hierarchy</h3>
                                <p class="font-body-md text-body-md text-secondary mt-xs text-sm">Start a blank organizational map.</p>
                            </div>
                        </a>
                        <a href="roleMapper.php" class="w-full text-left p-md border border-outline-variant rounded-DEFAULT hover:border-primary hover:bg-surface-container-low transition-all group flex items-start gap-md">
                            <div class="w-10 h-10 rounded bg-secondary-fixed text-on-secondary-fixed flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                <span class="material-symbols-outlined text-[20px]">badge</span>
                            </div>
                            <div>
                                <h3 class="font-title-md text-title-md font-semibold text-on-surface group-hover:text-primary transition-colors">Map Roles</h3>
                                <p class="font-body-md text-body-md text-secondary mt-xs text-sm">Assign users to specific structural nodes.</p>
                            </div>
                        </a>
                        <a href="importExport.php" class="w-full text-left p-md border border-outline-variant rounded-DEFAULT hover:border-primary hover:bg-surface-container-low transition-all group flex items-start gap-md">
                            <div class="w-10 h-10 rounded bg-surface-variant text-on-surface-variant flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                <span class="material-symbols-outlined text-[20px]">upload_file</span>
                            </div>
                            <div>
                                <h3 class="font-title-md text-title-md font-semibold text-on-surface group-hover:text-primary transition-colors">Import Data</h3>
                                <p class="font-body-md text-body-md text-secondary mt-xs text-sm">Upload CSV or JSON organizational data.</p>
                            </div>
                        </a>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- ALL VERSIONS HISTORY MODAL POPUP -->
    <div id="all-versions-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[100] flex items-center justify-center hidden">
        <div class="bg-surface-container-lowest w-full max-w-3xl rounded-xl border border-outline-variant shadow-2xl overflow-hidden flex flex-col max-h-[85vh] m-4">
            <div class="px-lg py-md border-b border-outline-variant flex justify-between items-center bg-surface-container-low">
                <div>
                    <h3 class="text-title-lg font-title-lg font-bold text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">history</span>
                        Full Version History
                    </h3>
                    <p class="text-xs text-secondary">Complete audit trail of all structural changes</p>
                </div>
                <button onclick="closeAllVersionsModal()" class="text-secondary hover:text-on-surface transition-colors p-1 rounded hover:bg-surface-variant">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="p-lg overflow-y-auto space-y-md custom-scrollbar flex-1">
                <ul class="space-y-md relative before:absolute before:inset-y-0 before:left-[15px] before:w-px before:bg-outline-variant before:-z-10 ml-xs">
                    <?php if (empty($allActivities)): ?>
                        <p class="text-secondary text-sm p-4">No version records found in database.</p>
                    <?php else: ?>
                        <?php foreach ($allActivities as $act): 
                            $vTag = $act['version_tag'] ?? ('v1.' . $act['id']);
                        ?>
                        <li class="flex gap-md relative">
                            <div class="w-8 h-8 rounded-full bg-primary-container text-on-primary border-2 border-surface-container-lowest flex items-center justify-center shrink-0 z-10 font-mono text-xs font-bold">
                                <span class="material-symbols-outlined text-[16px]">commit</span>
                            </div>
                            
                            <div class="pb-sm pt-xs flex-1 bg-surface-container-low p-sm rounded border border-outline-variant">
                                <div class="flex justify-between items-center mb-xs">
                                    <span class="bg-primary/10 text-primary px-2 py-0.5 rounded text-xs font-mono font-bold">
                                        <?php echo htmlspecialchars($vTag); ?>
                                    </span>
                                    <span class="text-xs text-secondary flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">schedule</span>
                                        <?php echo time_ago($act['created_at']); ?> (<?php echo date('Y-m-d H:i', strtotime($act['created_at'])); ?>)
                                    </span>
                                </div>

                                <p class="font-body-md text-sm text-on-surface mt-xs">
                                    <span class="font-semibold text-primary"><?php echo htmlspecialchars($act['user_name']); ?></span> 
                                    - <?php echo htmlspecialchars($act['description']); ?>
                                </p>

                                <div class="mt-sm pt-xs border-t border-outline-variant/50 flex gap-md text-xs">
                                    <button onclick="compareVersion('<?php echo $act['id']; ?>')" class="text-primary hover:underline flex items-center gap-1 font-medium">
                                        <span class="material-symbols-outlined text-[14px]">difference</span> Compare
                                    </button>
                                    <button onclick="rollbackVersion('<?php echo $act['id']; ?>')" class="text-error hover:underline flex items-center gap-1 font-medium ml-auto">
                                        <span class="material-symbols-outlined text-[14px]">undo</span> Rollback
                                    </button>
                                </div>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="px-lg py-sm border-t border-outline-variant bg-surface-container-low flex justify-end">
                <button onclick="closeAllVersionsModal()" class="px-md py-sm bg-surface border border-outline-variant text-on-surface rounded font-medium text-xs hover:bg-surface-variant transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        function openAllVersionsModal() {
            document.getElementById('all-versions-modal').classList.remove('hidden');
        }

        function closeAllVersionsModal() {
            document.getElementById('all-versions-modal').classList.add('hidden');
        }

        function compareVersion(versionId) {
            alert("Comparing hierarchy/flow changes for Version ID: " + versionId);
        }

        function rollbackVersion(versionId) {
            if (confirm("Are you sure you want to rollback configuration to Version ID " + versionId + "?")) {
                alert("Configuration successfully rolled back to Version ID: " + versionId);
            }
        }
    </script>
</body>
</html>