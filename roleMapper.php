<?php

require_once 'config.php';

// Login check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$roles_dropdown = [];
$departments_dropdown = [];
$all_responsibilities = [];

try {
    
    $hierarchy_result = $pdo->query("SELECT id, role_name, role_category FROM hierarchy WHERE role_name IS NOT NULL AND role_name != ''");
    while($row = $hierarchy_result->fetch(PDO::FETCH_ASSOC)) {
        $roles_dropdown[] = $row;
       
        if(!empty($row['role_category']) && !in_array($row['role_category'], $departments_dropdown)) {
            $departments_dropdown[] = $row['role_category'];
        }
    }

  
    $table_query = "SELECT r.*, h.role_name FROM responsibility r LEFT JOIN hierarchy h ON r.hierarchy_id = h.id ORDER BY r.created_at DESC";
    $table_result = $pdo->query($table_query);
    $all_responsibilities = $table_result->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Role Mapper - Structural Authority</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=JetBrains+Mono:wght@400&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "outline-variant": "#c3c6d7", "primary": "#004ac6", "outline": "#737686",
                        "tertiary-container": "#656d84", "primary-container": "#2563eb", "on-primary": "#ffffff",
                        "on-tertiary": "#ffffff", "inverse-primary": "#b4c5ff", "surface-container-low": "#f2f4f6",
                        "primary-fixed": "#dbe1ff", "on-primary-fixed": "#00174b", "on-tertiary-fixed-variant": "#3f465c",
                        "surface-variant": "#e0e3e5", "secondary-fixed": "#d3e4fe", "primary-fixed-dim": "#b4c5ff",
                        "surface-tint": "#0053db", "on-secondary-container": "#54647a", "tertiary": "#4d556b",
                        "surface-container-highest": "#e0e3e5", "on-tertiary-fixed": "#131b2e", "on-background": "#191c1e",
                        "surface-container-high": "#e6e8ea", "surface": "#f7f9fb", "surface-container": "#eceef0",
                        "on-primary-fixed-variant": "#003ea8", "tertiary-fixed-dim": "#bec6e0", "surface-dim": "#d8dadc",
                        "on-secondary-fixed": "#0b1c30", "on-secondary-fixed-variant": "#38485d", "on-secondary": "#ffffff",
                        "on-primary-container": "#eeefff", "surface-bright": "#f7f9fb", "surface-container-lowest": "#ffffff",
                        "on-error-container": "#93000a", "background": "#f7f9fb", "secondary-container": "#d0e1fb",
                        "on-tertiary-container": "#eef0ff", "on-error": "#ffffff", "error-container": "#ffdad6",
                        "secondary-fixed-dim": "#b7c8e1", "inverse-on-surface": "#eff1f3", "secondary": "#505f76",
                        "on-surface": "#191c1e", "inverse-surface": "#2d3133", "on-surface-variant": "#434655",
                        "tertiary-fixed": "#dae2fd", "error": "#ba1a1a"
                    },
                    "borderRadius": { "DEFAULT": "0.125rem", "lg": "0.25rem", "xl": "0.5rem", "full": "0.75rem" },
                    "spacing": { "md": "16px", "sm": "8px", "xl": "32px", "margin-mobile": "16px", "base": "4px", "xs": "4px", "2xl": "48px", "gutter": "16px", "lg": "24px", "margin-desktop": "32px" }
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .material-symbols-outlined.fill { font-variation-settings: 'FILL' 1; }
        .custom-scrollbar::-webkit-scrollbar { width: 8px; height: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #c3c6d7; border-radius: 4px; }
    </style>
</head>

<body class="bg-surface text-on-surface font-body-md min-h-screen flex flex-col antialiased">
    <nav class="bg-surface-container-lowest text-primary font-body-md text-body-md w-full sticky top-0 z-50 border-b border-outline-variant flex justify-between items-center px-lg h-16">
        <div class="flex items-center gap-md">
            <span class="font-headline-md text-headline-md font-bold text-primary text-[24px]">SAMS</span>
        </div>
        <div class="flex-1 max-w-xl mx-lg relative">
            <span class="material-symbols-outlined absolute left-sm top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
            <input class="w-full pl-xl pr-md py-sm bg-surface-container-low border border-outline-variant rounded focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all font-mono-md text-mono-md placeholder:text-on-surface-variant" placeholder="Search nodes..." type="text">
        </div>
        <div class="flex items-center gap-md">
            <div class="flex items-center gap-3 text-on-surface">
                <span class="font-medium text-sm">Hi, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>!</span>
                <?php echo render_role_badge(); ?>
                <a href="./api/logout.php" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm font-medium transition-colors">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="flex flex-1 overflow-hidden">
        <aside class="bg-surface-container-low text-primary font-label-md text-label-md w-64 border-r border-outline-variant flex flex-col z-40 shrink-0 hidden md:flex h-[calc(100vh-64px)] sticky top-16 overflow-y-auto">
            <div class="p-lg border-b border-outline-variant flex flex-col gap-sm">
                <div class="flex items-center gap-sm">
                    <div class="w-10 h-10 rounded bg-primary-container text-on-primary flex items-center justify-center font-bold text-title-lg shadow-sm">SA</div>
                    <div><h2 class="font-title-md text-title-md font-bold text-on-surface leading-tight">Structural<br>Authority</h2></div>
                </div>
            </div>
            <nav class="flex-1 py-md">
                <ul class="flex flex-col">
                    <li>
                        <a class="group flex items-center px-md py-sm cursor-pointer text-secondary hover:bg-surface-variant transition-all border-l-4 border-transparent hover:border-outline-variant relative" href="./dashboard.php">
                            <span class="material-symbols-outlined mr-sm text-[20px]">dashboard</span>
                            <span class="text-sm">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a class="group flex items-center px-md py-sm cursor-pointer text-secondary hover:bg-surface-variant transition-all border-l-4 border-transparent hover:border-outline-variant relative" href="./hierarchyDesigner.php">
                            <span class="material-symbols-outlined mr-sm text-[20px]">account_tree</span>
                            <span class="text-sm">Hierarchy Designer</span>
                        </a>
                    </li>
                    <li>
                        <a class="group flex items-center px-md py-sm cursor-pointer bg-secondary-container text-on-secondary-container border-l-4 border-primary font-bold relative" href="./roleMapper.php">
                            <span class="material-symbols-outlined mr-sm text-[20px] text-primary fill">badge</span>
                            <span class="text-sm">Role Mapper</span>
                        </a>
                        
                    </li>
                    <li>
                        <a class="group flex items-center px-md py-sm cursor-pointer text-secondary hover:bg-surface-variant transition-all border-l-4 border-transparent hover:border-outline-variant relative" href="./decisionFlow.php">
                            <span class="material-symbols-outlined mr-sm text-[20px]">account_tree</span>
                            <span class="text-sm">Decision Flow</span>
                        </a>
                    </li>
                    <li>
                        <a class="group flex items-center px-md py-sm cursor-pointer text-secondary hover:bg-surface-variant transition-all border-l-4 border-transparent hover:border-outline-variant relative" href="./importExport.php">
                            <span class="material-symbols-outlined mr-sm text-[20px]">swap_vert</span>
                            <span class="text-sm">Import/Export</span>
                        </a>
                    </li>
                    
                </ul>
            </nav>
        </aside>

        <main class="flex-1 overflow-y-auto p-margin-mobile md:p-margin-desktop bg-background custom-scrollbar">
            <div class="flex flex-wrap justify-between items-center gap-md mb-lg">
                <div class="flex items-center gap-sm">
                    <?php if (is_viewer()): ?>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">
                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                        Viewer Mode &bull; Read-Only Access
                    </span>
                    <?php endif; ?>
                </div>
                <?php if (!is_viewer()): ?>
                <button onclick="openAddModal()" class="bg-primary-container text-on-primary py-sm px-md rounded-DEFAULT font-label-md font-medium hover:bg-primary-fixed-variant transition-colors flex items-center gap-xs shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">add</span>Add Responsibility
                </button>
                <?php else: ?>
                <span class="text-xs text-secondary italic">Changes restricted to Admin and Manager</span>
                <?php endif; ?>
            </div>

            <div class="bg-surface-container-lowest rounded-lg border border-outline-variant shadow-sm overflow-hidden flex flex-col">
                <div class="overflow-x-auto w-full custom-scrollbar">
                    <table class="w-full text-left border-collapse min-w-[1000px]">
                        <thead>
                            <tr class="bg-surface-container-low border-b border-outline-variant">
                                <th class="p-md font-label-md text-label-md text-on-surface-variant uppercase">Role</th>
                                <th class="p-md font-label-md text-label-md text-on-surface-variant uppercase">Owns</th>
                                <th class="p-md font-label-md text-label-md text-on-surface-variant uppercase">Reviews</th>
                                <th class="p-md font-label-md text-label-md text-on-surface-variant uppercase">Approves</th>
                                <th class="p-md font-label-md text-label-md text-on-surface-variant uppercase">Escalates</th>
                                <th class="p-md font-label-md text-label-md text-on-surface-variant uppercase">Department</th>
                                <th class="p-md font-label-md text-label-md text-on-surface-variant uppercase text-center">Status</th>
                                <th class="p-md font-label-md text-label-md text-on-surface-variant uppercase text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="table-body" class="divide-y divide-slate-200 text-sm">

<?php
$formatTags = function($jsonStr){
    $arr = json_decode($jsonStr, true) ?? [];
    if(empty($arr)){
        return '<span class="text-outline italic text-[13px]">—</span>';
    }
    return '<div class="flex flex-wrap gap-1">' . implode('', array_map(function($t) {
        return '<span class="inline-block bg-surface-container border border-outline-variant text-[12px] px-2 py-0.5 rounded-full text-on-surface-variant font-mono-md shadow-sm">'.htmlspecialchars($t).'</span>';
    }, $arr)) . '</div>';
};

// यदि टेबल में डेटा मौजूद है
if(!empty($all_responsibilities)):
foreach($all_responsibilities as $row):
$isActive = ($row['status'] ?? '') === "Active";
?>

<tr class="hover:bg-slate-50 transition border-b border-outline-variant align-top" id="row-<?= $row['id'] ?>">
    <td class="p-md font-bold text-on-surface row-role-name">
        <?= htmlspecialchars($row['role_name']) ?>
    </td>

    <td class="p-md row-owns" data-raw="<?= htmlspecialchars($row['owns']) ?>">
        <?= $formatTags($row['owns']) ?>
    </td>

    <td class="p-md row-reviews" data-raw="<?= htmlspecialchars($row['reviews']) ?>">
        <?= $formatTags($row['reviews']) ?>
    </td>

    <td class="p-md row-approves" data-raw="<?= htmlspecialchars($row['approves']) ?>">
        <?= $formatTags($row['approves']) ?>
    </td>

    <td class="p-md row-escalates" data-raw="<?= htmlspecialchars($row['escalates']) ?>">
        <?= $formatTags($row['escalates']) ?>
    </td>

    <td class="p-md row-dept" data-val="<?= htmlspecialchars($row['department'] ?? 'General') ?>">
        <span class="bg-surface-variant text-on-surface-variant font-mono-md text-[13px] px-sm py-xs rounded">
            <?= htmlspecialchars($row['department'] ?? 'General') ?>
        </span>
    </td>

    <td class="p-md text-center row-status" data-val="<?= htmlspecialchars($row['status']) ?>">
        <span class="inline-flex items-center gap-xs px-sm py-xs rounded-full text-label-md font-bold <?= $isActive ? 'bg-primary-container text-on-primary' : 'bg-surface-container-high text-on-surface-variant' ?>">
            <?= htmlspecialchars($row['status']) ?>
        </span>
    </td>

    <td class="p-md text-center">
        <?php if (!is_viewer()): ?>
        <div class="flex items-center justify-center gap-sm">
            <button onclick="openEditModal('<?= $row['id'] ?>', '<?= htmlspecialchars($row['hierarchy_id']) ?>')" class="text-primary hover:text-primary-container transition-colors flex items-center" title="Edit">
                <span class="material-symbols-outlined text-[20px]">edit</span>
            </button>
            <button onclick="deleteResponsibility('<?= $row['id'] ?>')" class="text-error hover:text-red-700 transition-colors flex items-center" title="Delete">
                <span class="material-symbols-outlined text-[20px]">delete</span>
            </button>
        </div>
        <?php else: ?>
        <span class="text-xs text-secondary italic">View Only</span>
        <?php endif; ?>
    </td>
</tr>

<?php
endforeach;
else: // 🔥 यहाँ बदला गया है: अगर कोई डेटा उपलब्ध नहीं है तो यह 1 लाइन प्रिंट होगी
?>

<tr id="empty-state">
    <td colspan="8" class="p-xl text-center text-on-surface-variant italic font-medium bg-surface-container-low">
        <div class="flex flex-col items-center justify-center py-md gap-xs">
            <span class="material-symbols-outlined text-outline text-[32px]">info</span>
            <span>No responsibilities mapped yet. Click "Add Responsibility" to create one.</span>
        </div>
    </td>
</tr>

<?php
endif;
?>

</tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div id="responsibility-modal" class="fixed inset-0 bg-black/40 z-[100] flex items-center justify-center hidden opacity-0 transition-opacity duration-200">
        <div class="bg-surface-container-lowest w-full max-w-lg rounded-xl border border-outline-variant shadow-2xl overflow-hidden transform scale-95 transition-transform duration-200 flex flex-col max-h-[90vh]">
            <div class="px-lg py-md border-b border-outline-variant flex justify-between items-center bg-surface-container-low">
                <h3 id="modal-title" class="text-title-lg font-title-lg font-bold text-on-surface">Add Responsibility</h3>
                <button onclick="toggleModal(false)" class="text-secondary hover:text-on-surface transition-colors flex items-center">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form id="responsibility-form" onsubmit="handleFormSubmit(event)" class="p-lg overflow-y-auto space-y-md custom-scrollbar flex-1">
                <input type="hidden" name="responsibility_id" id="form-responsibility-id" value="">
                
                <div>
                    <label class="block text-label-md font-label-md text-on-surface-variant mb-xs font-semibold">Role</label>
                    <select name="role" id="form-role" class="w-full p-sm bg-surface border border-outline-variant rounded focus:outline-none focus:border-primary text-body-md">
                        <?php foreach($roles_dropdown as $role_item): ?>
                            <option value="<?= htmlspecialchars($role_item['id']) ?>">
                                <?= htmlspecialchars($role_item['role_name'])  ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <hr class="border-outline-variant" />
                
                <div>
                    <label class="block text-label-md font-label-md text-on-surface-variant mb-xs font-semibold">Owns</label>
                    <div class="flex gap-sm">
                        <input type="text" id="input-owns" placeholder="e.g., Sprint Planning Execution" class="flex-1 p-sm bg-surface border border-outline-variant rounded text-body-md">
                        <button type="button" onclick="addTag('owns')" class="px-md py-sm bg-surface border border-outline border-dashed text-primary font-medium hover:bg-surface-container-low rounded text-label-md flex items-center gap-xs">
                            <span class="material-symbols-outlined text-[16px]">add</span> Add
                        </button>
                    </div>
                    <div id="container-owns" class="flex flex-wrap gap-xs mt-sm"></div>
                </div>

                <hr class="border-outline-variant" />

                <div>
                    <label class="block text-label-md font-label-md text-on-surface-variant mb-xs font-semibold">Reviews</label>
                    <div class="flex gap-sm">
                        <input type="text" id="input-reviews" placeholder="e.g., Technical Design Documents" class="flex-1 p-sm bg-surface border border-outline-variant rounded text-body-md">
                        <button type="button" onclick="addTag('reviews')" class="px-md py-sm bg-surface border border-outline border-dashed text-primary font-medium hover:bg-surface-container-low rounded text-label-md flex items-center gap-xs">
                            <span class="material-symbols-outlined text-[16px]">add</span> Add
                        </button>
                    </div>
                    <div id="container-reviews" class="flex flex-wrap gap-xs mt-sm"></div>
                </div>

                <hr class="border-outline-variant" />

                <div>
                    <label class="block text-label-md font-label-md text-on-surface-variant mb-xs font-semibold">Approves</label>
                    <div class="flex gap-sm">
                        <input type="text" id="input-approves" placeholder="e.g., Resource Allocation Request" class="flex-1 p-sm bg-surface border border-outline-variant rounded text-body-md">
                        <button type="button" onclick="addTag('approves')" class="px-md py-sm bg-surface border border-outline border-dashed text-primary font-medium hover:bg-surface-container-low rounded text-label-md flex items-center gap-xs">
                            <span class="material-symbols-outlined text-[16px]">add</span> Add
                        </button>
                    </div>
                    <div id="container-approves" class="flex flex-wrap gap-xs mt-sm"></div>
                </div>

                <hr class="border-outline-variant" />
                <div>
                    <label class="block text-label-md font-label-md text-on-surface-variant mb-xs font-semibold">Escalates</label>
                    <div class="flex gap-sm">
                        <input type="text" id="input-escalates" placeholder="e.g., Critical Scope Changes" class="flex-1 p-sm bg-surface border border-outline-variant rounded text-body-md">
                        <button type="button" onclick="addTag('escalates')" class="px-md py-sm bg-surface border border-outline border-dashed text-primary font-medium hover:bg-surface-container-low rounded text-label-md flex items-center gap-xs">
                            <span class="material-symbols-outlined text-[16px]">add</span> Add
                        </button>
                    </div>
                    <div id="container-escalates" class="flex flex-wrap gap-xs mt-sm"></div>
                </div>

                <hr class="border-outline-variant" />

                <div class="grid grid-cols-2 gap-md">
                    <div>
                        <label class="block text-label-md font-label-md text-on-surface-variant mb-xs font-semibold">Department</label>
                        <select name="department" id="form-department" class="w-full p-sm bg-surface border border-outline-variant rounded text-body-md">
                            <?php foreach($departments_dropdown as $dept): ?>
                                <option value="<?= htmlspecialchars($dept) ?>"><?= htmlspecialchars($dept) ?></option>
                            <?php endforeach; ?>
                            <?php if(empty($departments_dropdown)): ?>
                                <option value="Management">Management</option>
                                <option value="HR Manager">HR Manager</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-label-md font-label-md text-on-surface-variant mb-xs font-semibold">Status</label>
                        <select name="status" id="form-status" class="w-full p-sm bg-surface border border-outline-variant rounded text-body-md">
                            <option value="Active">Active</option>
                            <option value="Draft">Draft</option>
                            <option value="Review">Review</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end items-center gap-md pt-md border-t border-outline-variant mt-lg">
                    <button type="button" onclick="toggleModal(false)" class="text-secondary font-medium hover:text-on-surface py-sm px-md text-label-md">Cancel</button>
                    <button type="submit" class="bg-primary-container text-on-primary font-medium hover:bg-primary-fixed-variant py-sm px-md rounded-DEFAULT shadow-sm text-label-md">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let entries = { owns: [], reviews: [], approves: [], escalates: [] };

        function toggleModal(show) {
            const modal = document.getElementById('responsibility-modal');
            const innerContainer = modal.firstElementChild;
            if (show) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    innerContainer.classList.remove('scale-95');
                }, 10);
            } else {
                modal.classList.add('opacity-0');
                innerContainer.classList.add('scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    resetForm();
                }, 200);
            }
        }

        function resetForm() {
            document.getElementById('responsibility-form').reset();
            document.getElementById('form-responsibility-id').value = '';
            document.getElementById('modal-title').innerText = "Add Responsibility";
            entries = { owns: [], reviews: [], approves: [], escalates: [] };
            ['owns', 'reviews', 'approves', 'escalates'].forEach(renderTags);
        }

        function openAddModal() {
            resetForm();
            toggleModal(true);
        }

        function openEditModal(id, hierarchyId) {
            resetForm();
            document.getElementById('form-responsibility-id').value = id;
            document.getElementById('modal-title').innerText = "Edit Responsibility";
            
            const row = document.getElementById(`row-${id}`);
            
            // सेलेक्ट बॉक्सेस में वैल्यू सेट करना
            document.getElementById('form-role').value = hierarchyId;
            document.getElementById('form-department').value = row.querySelector('.row-dept').getAttribute('data-val');
            document.getElementById('form-status').value = row.querySelector('.row-status').getAttribute('data-val');
            
            // JSON एरे टैग्स को लोड करना
            ['owns', 'reviews', 'approves', 'escalates'].forEach(field => {
                const rawData = row.querySelector(`.row-${field}`).getAttribute('data-raw');
                try {
                    entries[field] = JSON.parse(rawData) || [];
                } catch(e) {
                    entries[field] = [];
                }
                renderTags(field);
            });

            toggleModal(true);
        }

        function addTag(field) {
            const input = document.getElementById(`input-${field}`);
            const text = input.value.trim();
            if (!text) return;

            if(!entries[field].includes(text)) {
                entries[field].push(text);
            }
            input.value = '';
            renderTags(field);
        }

        function removeTag(field, index) {
            entries[field].splice(index, 1);
            renderTags(field);
        }

        function renderTags(field) {
            const container = document.getElementById(`container-${field}`);
            container.innerHTML = '';
            entries[field].forEach((tag, index) => {
                const span = document.createElement('span');
                span.className = "inline-flex items-center gap-1 bg-surface-container border border-outline-variant text-[13px] pl-2 pr-1 py-0.5 rounded-full text-on-surface-variant";
                span.innerHTML = `
                    <span>${tag}</span>
                    <button type="button" onclick="removeTag('${field}', ${index})" class="text-secondary hover:text-error flex items-center">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                    </button>
                `;
                container.appendChild(span);
            });
        }

        function handleFormSubmit(e) {
            e.preventDefault();
            
            ['owns', 'reviews', 'approves', 'escalates'].forEach(field => {
                const inputVal = document.getElementById(`input-${field}`).value.trim();
                if (inputVal && !entries[field].includes(inputVal)) {
                    entries[field].push(inputVal);
                }
            });
            
            const form = e.target;
            const formData = new FormData(form);
            const resId = document.getElementById('form-responsibility-id').value;
            
            formData.append('owns_data', JSON.stringify(entries.owns));
            formData.append('reviews_data', JSON.stringify(entries.reviews));
            formData.append('approves_data', JSON.stringify(entries.approves));
            formData.append('escalates_data', JSON.stringify(entries.escalates));

            fetch('api/save_responsibility.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(res => {
                if(res.success) {
                    // पेज रीलोड करने के बजाय डायनामिकली अपडेट/इन्सर्ट करना
                    location.reload(); 
                } else {
                    alert('Error: ' + res.message);
                }
            })
            .catch(err => {
                console.error('Submission Failed:', err);
                alert('Database save karne me error aayi.');
            });
        }

        function deleteResponsibility(id) {
            if (confirm('क्या आप वाकई इस जिम्मेदारी को हटाना चाहते हैं?')) {
                const formData = new FormData();
                formData.append('id', id);

                fetch('api/delete_responsibility.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(res => {
                    if (res.success) {
                        const row = document.getElementById(`row-${id}`);
                        if (row) row.remove();
                    } else {
                        alert('Error: ' + res.message);
                    }
                })
                .catch(err => {
                    console.error('Delete Failed:', err);
                    alert('Data delete karne me error aayi.');
                });
            }
        }
    </script>
</body>
</html>
<?php 
$pdo = null; 
?>