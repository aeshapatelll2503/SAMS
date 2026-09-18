<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Import/Export - SAMS</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=JetBrains+Mono:wght@400&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "on-error-container": "#93000a",
                        "tertiary": "#4d556b",
                        "tertiary-fixed-dim": "#bec6e0",
                        "on-tertiary": "#ffffff",
                        "on-surface-variant": "#434655",
                        "surface-container-highest": "#e0e3e5",
                        "primary-fixed": "#dbe1ff",
                        "on-surface": "#191c1e",
                        "background": "#f7f9fb",
                        "on-primary-fixed-variant": "#003ea8",
                        "surface-bright": "#f7f9fb",
                        "on-secondary-fixed": "#0b1c30",
                        "secondary-fixed-dim": "#b7c8e1",
                        "on-tertiary-fixed": "#131b2e",
                        "on-primary-fixed": "#00174b",
                        "on-secondary-fixed-variant": "#38485d",
                        "secondary": "#505f76",
                        "surface-dim": "#d8dadc",
                        "surface-variant": "#e0e3e5",
                        "on-tertiary-fixed-variant": "#3f465c",
                        "inverse-primary": "#b4c5ff",
                        "secondary-container": "#d0e1fb",
                        "surface-container": "#eceef0",
                        "on-primary-container": "#eeefff",
                        "surface-container-high": "#e6e8ea",
                        "tertiary-fixed": "#dae2fd",
                        "surface-tint": "#0053db",
                        "on-primary": "#ffffff",
                        "surface-container-low": "#f2f4f6",
                        "primary-fixed-dim": "#b4c5ff",
                        "on-tertiary-container": "#eef0ff",
                        "primary-container": "#2563eb",
                        "on-secondary": "#ffffff",
                        "tertiary-container": "#656d84",
                        "inverse-surface": "#2d3133",
                        "error": "#ba1a1a",
                        "on-error": "#ffffff",
                        "primary": "#004ac6",
                        "surface-container-lowest": "#ffffff",
                        "outline-variant": "#c3c6d7",
                        "inverse-on-surface": "#eff1f3",
                        "error-container": "#ffdad6",
                        "surface": "#f7f9fb",
                        "outline": "#737686",
                        "on-secondary-container": "#54647a",
                        "secondary-fixed": "#d3e4fe",
                        "on-background": "#191c1e"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.125rem",
                        "lg": "0.25rem",
                        "xl": "0.5rem",
                        "full": "0.75rem"
                    },
                    "spacing": {
                        "lg": "24px",
                        "2xl": "48px",
                        "xl": "32px",
                        "sm": "8px",
                        "xs": "4px",
                        "gutter": "16px",
                        "base": "4px",
                        "md": "16px",
                        "margin-mobile": "16px",
                        "margin-desktop": "32px"
                    },
                    "fontFamily": {
                        "mono-md": ["JetBrains Mono"],
                        "title-lg": ["Inter"],
                        "display-lg": ["Inter"],
                        "body-lg": ["Inter"],
                        "headline-lg": ["Inter"],
                        "label-md": ["Inter"],
                        "title-md": ["Inter"],
                        "headline-md": ["Inter"],
                        "body-md": ["Inter"]
                    },
                    "fontSize": {
                        "mono-md": ["13px", { "lineHeight": "20px", "fontWeight": "400" }],
                        "title-lg": ["20px", { "lineHeight": "28px", "fontWeight": "600" }],
                        "display-lg": ["48px", { "lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                        "body-lg": ["16px", { "lineHeight": "24px", "fontWeight": "400" }],
                        "headline-lg": ["32px", { "lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "600" }],
                        "label-md": ["12px", { "lineHeight": "16px", "letterSpacing": "0.02em", "fontWeight": "500" }],
                        "title-md": ["16px", { "lineHeight": "24px", "fontWeight": "600" }],
                        "headline-md": ["24px", { "lineHeight": "32px", "fontWeight": "600" }],
                        "body-md": ["14px", { "lineHeight": "20px", "fontWeight": "400" }]
                    }
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .material-symbols-outlined.fill {
            font-variation-settings: 'FILL' 1;
        }

        .drag-active {
            border-color: #2563eb !important;
            background-color: #f0fdfa !important;
        }
    </style>
</head>

<body class="bg-surface text-on-surface font-body-md h-screen flex flex-col overflow-hidden antialiased">
    <!-- TopNavBar -->
    <nav class="bg-surface-container-lowest text-primary font-body-md text-body-md w-full shrink-0 border-b border-outline-variant flex justify-between items-center px-lg h-16 z-50">
        <div class="flex items-center gap-md">
            <span class="font-headline-md text-headline-md font-bold text-primary">SAMS</span>
        </div>
        <div class="flex-1 max-w-xl mx-lg relative">
            <span class="material-symbols-outlined absolute left-sm top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
            <input class="w-full pl-xl pr-md py-sm bg-surface-container-low border border-outline-variant rounded focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all font-mono-md text-mono-md placeholder:text-on-surface-variant" placeholder="Search structures, roles, or nodes..." type="text">
        </div>
        <div class="flex items-center gap-md">
            <div class="flex items-center gap-4 text-on-surface">
                <a href="./api/logout.php" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm font-medium transition-colors">Logout</a>
            </div>
        </div>
    </nav>
    
    <!-- Main Flex Container -->
    <div class="flex flex-1 overflow-hidden">
        <!-- SideNavBar -->
        <aside class="bg-surface-container-low text-primary font-label-md text-label-md w-64 border-r border-outline-variant flex flex-col shrink-0 hidden md:flex h-full overflow-y-auto">
            <div class="p-lg border-b border-outline-variant flex flex-col gap-sm">
                <div class="flex items-center gap-sm">
                    <div class="w-10 h-10 rounded bg-primary-container text-on-primary flex items-center justify-center font-bold text-title-lg shadow-sm">SA</div>
                    <div>
                        <h2 class="font-title-md text-title-md font-bold text-on-surface leading-tight">Structural Authority</h2>
                    </div>
                </div>
            </div>
            <nav class="flex-1 py-md">
                <ul class="flex flex-col">
                    <li>
                        <a class="group flex items-center px-md py-sm cursor-pointer text-secondary hover:bg-surface-variant transition-all border-l-4 border-transparent hover:border-outline-variant" href="./dashboard.php">
                            <span class="material-symbols-outlined mr-sm text-[20px]">dashboard</span>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a class="group flex items-center px-md py-sm cursor-pointer text-secondary hover:bg-surface-variant transition-all border-l-4 border-transparent hover:border-outline-variant" href="./hierarchyDesigner.php">
                            <span class="material-symbols-outlined mr-sm text-[20px]">account_tree</span>
                            Hierarchy Designer
                        </a>
                    </li>
                    <li>
                        <a class="group flex items-center px-md py-sm cursor-pointer text-secondary hover:bg-surface-variant transition-all border-l-4 border-transparent hover:border-outline-variant" href="./roleMapper.php">
                            <span class="material-symbols-outlined mr-sm text-[20px]">badge</span>
                            Role Mapper
                        </a>
                    </li>
                    <li>
                        <a class="group flex items-center px-md py-sm cursor-pointer text-secondary hover:bg-surface-variant transition-all border-l-4 border-transparent hover:border-outline-variant" href="./decisionFlow.php">
                            <span class="material-symbols-outlined mr-sm text-[20px]">account_tree</span>
                            Decision Flow
                        </a>
                    </li>
                    <li>
                        <a class="group flex items-center px-md py-sm cursor-pointer bg-secondary-container text-on-secondary-container border-l-4 border-primary font-bold" href="./importExport.php">
                            <span class="material-symbols-outlined mr-sm text-[20px] fill">swap_vert</span>
                            Import/Export
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto p-margin-mobile md:p-margin-desktop bg-background">
            <div class="max-w-7xl mx-auto">
                <header class="mb-lg flex flex-col md:flex-row md:items-end justify-between gap-md">
                    <div>
                        <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface">Data Exchange</h1>
                        <p class="font-body-lg text-body-lg text-secondary mt-xs">Backup and restore, share configurations, or generate visual documentation.</p>
                    </div>
                </header>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-lg mb-xl">
                    <!-- Import Section -->
                    <section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg flex flex-col h-full">
                        <div class="flex items-center gap-sm mb-md">
                            <span class="material-symbols-outlined text-primary-container text-[24px]">upload</span>
                            <h3 class="font-title-lg text-title-lg text-on-surface">Import Data</h3>
                        </div>
                        <p class="font-body-md text-body-md text-on-surface-variant mb-lg">Upload configuration files to restore or build structural nodes.</p>
                        
                        <div class="flex-1 border-2 border-dashed border-outline-variant rounded-lg bg-surface flex flex-col items-center justify-center p-xl transition-colors hover:bg-surface-container-low cursor-pointer min-h-[240px]" id="drop-zone">
                            <input type="file" id="file-input" accept=".json,.yaml" class="hidden" />
                            <span class="material-symbols-outlined text-outline text-[48px] mb-md">cloud_upload</span>
                            <h4 class="font-title-md text-title-md text-on-surface mb-xs">Drag and drop file here</h4>
                            <p class="font-body-md text-body-md text-secondary mb-lg">Supports .json (Primary)</p>
                            <button type="button" class="px-md py-sm bg-surface-container-highest border border-outline-variant text-on-surface font-label-md text-label-md rounded-DEFAULT hover:bg-surface-variant transition-colors pointer-events-none">
                                Browse Files
                            </button>
                        </div>
                        <div class="mt-md text-center">
                            <p id="upload-status" class="font-body-md text-body-md text-on-surface-variant italic">Waiting for file upload...</p>
                        </div>
                    </section>

                    <!-- Export Section -->
                    <section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg flex flex-col h-full">
                        <div class="flex items-center gap-sm mb-md">
                            <span class="material-symbols-outlined text-primary-container text-[24px]">download</span>
                            <h3 class="font-title-lg text-title-lg text-on-surface">Export Structure</h3>
                        </div>
                        <p class="font-body-md text-body-md text-on-surface-variant mb-md">Download structure for backup, sharing configurations, or visual documentation.</p>

                        <!-- Source Data Selection Box -->
                        <div class="bg-surface rounded-lg p-md border border-outline-variant mb-md">
                            <label class="font-label-md text-label-md font-semibold text-on-surface block mb-xs">Select Data Source</label>
                            <select id="export-source" class="w-full bg-surface-container-lowest border border-outline-variant rounded px-3 py-2 font-body-md text-body-md text-on-surface focus:border-primary focus:outline-none">
                                <option value="hierarchy">Hierarchy Designer (Organizational Structure & Nodes)</option>
                                <option value="role_mapper">Role Mapper (User Roles, Responsibilities & Permissions)</option>
                                <option value="decision_flow">Decision Flow (Workflows, Logic & Decision Trees)</option>
                            </select>
                        </div>

                        <div class="flex-1 flex flex-col gap-md">
                            <div class="grid grid-cols-3 gap-md">
                                <label class="border border-outline-variant rounded-lg p-md cursor-pointer hover:border-primary-container hover:bg-surface-container-low transition-colors flex flex-col items-center text-center relative">
                                    <input checked="" class="absolute top-2 right-2 text-primary-container focus:ring-primary-container" name="export-format" type="radio" value="json">
                                    <span class="material-symbols-outlined text-secondary mb-sm">data_object</span>
                                    <span class="font-label-md text-label-md font-semibold text-on-surface block">JSON</span>
                                    <span class="font-label-md text-label-md text-secondary text-[10px] block mt-xs">Primary</span>
                                </label>
                                <label class="border border-outline-variant rounded-lg p-md cursor-pointer hover:border-primary-container hover:bg-surface-container-low transition-colors flex flex-col items-center text-center relative">
                                    <input class="absolute top-2 right-2 text-primary-container focus:ring-primary-container" name="export-format" type="radio" value="yaml">
                                    <span class="material-symbols-outlined text-secondary mb-sm">code</span>
                                    <span class="font-label-md text-label-md font-semibold text-on-surface block">YAML</span>
                                    <span class="font-label-md text-label-md text-secondary text-[10px] block mt-xs">Optional</span>
                                </label>
                                <label class="border border-outline-variant rounded-lg p-md cursor-pointer hover:border-primary-container hover:bg-surface-container-low transition-colors flex flex-col items-center text-center relative">
                                    <input class="absolute top-2 right-2 text-primary-container focus:ring-primary-container" name="export-format" type="radio" value="visual">
                                    <span class="material-symbols-outlined text-secondary mb-sm">image</span>
                                    <span class="font-label-md text-label-md font-semibold text-on-surface block">SVG</span>
                                    <span class="font-label-md text-label-md text-secondary text-[10px] block mt-xs">Visual Export</span>
                                </label>
                            </div>
                            <div class="bg-surface rounded-lg p-md border border-outline-variant mt-auto">
                                <h4 class="font-label-md text-label-md font-semibold text-on-surface mb-md">Use Cases & Mappings</h4>
                                <div class="space-y-sm">
                                    <label class="flex items-center gap-sm cursor-pointer">
                                        <input checked="" class="text-primary-container rounded-sm border-outline focus:ring-primary-container" type="checkbox">
                                        <span class="font-body-md text-body-md text-on-surface-variant">Backup & Restore Configurations</span>
                                    </label>
                                    <label class="flex items-center gap-sm cursor-pointer">
                                        <input class="text-primary-container rounded-sm border-outline focus:ring-primary-container" type="checkbox">
                                        <span class="font-body-md text-body-md text-on-surface-variant">Include Visual Documentation Meta</span>
                                    </label>
                                </div>
                            </div>
                            <button id="btn-generate-export" class="w-full bg-primary-container text-on-primary rounded-DEFAULT font-title-md text-title-md py-md hover:bg-primary-fixed-variant transition-colors mt-sm shadow-sm">
                                Generate Export
                            </button>
                        </div>
                    </section>
                </div>

               
            </div>
        </main>
    </div>

    <script>
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('file-input');
        const statusText = document.getElementById('upload-status');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.add('drag-active'), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.remove('drag-active'), false);
        });

        dropZone.addEventListener('click', () => {
            fileInput.click();
        });

        dropZone.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length) handleFileUpload(files[0]);
        });

        if (fileInput) {
            fileInput.addEventListener('change', (e) => {
                if (e.target.files.length) handleFileUpload(e.target.files[0]);
            });
        }

        // Import handler
        async function handleFileUpload(file) {
            if (statusText) statusText.innerText = `Uploading and processing: ${file.name}...`;

            const formData = new FormData();
            formData.append('file', file);

            try {
                const res = await fetch('./api/importExport.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await res.json();
                if (data.success) {
                    if (statusText) statusText.innerText = `Success! Processed ${data.count} items.`;
                    alert("Data imported successfully!");
                } else {
                    if (statusText) statusText.innerText = "Error importing file.";
                    alert("Import Error: " + (data.error || "Failed to process file"));
                }
            } catch (err) {
                console.error(err);
                if (statusText) statusText.innerText = "Server connection error.";
                alert("Error connecting to server.");
            }
        }

        // Export Handler
        const btnExport = document.getElementById('btn-generate-export');
        if (btnExport) {
            btnExport.addEventListener('click', () => {
                const source = document.getElementById('export-source').value || 'hierarchy';
                const selectedFormat = document.querySelector('input[name="export-format"]:checked')?.value || 'json';

                // Redirects to API endpoint
                window.location.href = `./api/importExport.php?source=${source}&format=${selectedFormat}`;
            });
        }
    </script>
</body>

</html>