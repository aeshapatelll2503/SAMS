<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Drag &amp; Drop Hierarchy Designer</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=JetBrains+Mono:wght@400&amp;display=swap" rel="stylesheet">
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
                        "mono-md": ["13px", {"lineHeight": "20px", "fontWeight": "400"}],
                        "title-lg": ["20px", {"lineHeight": "28px", "fontWeight": "600"}],
                        "display-lg": ["48px", {"lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                        "body-lg": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                        "headline-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                        "label-md": ["12px", {"lineHeight": "16px", "letterSpacing": "0.02em", "fontWeight": "500"}],
                        "title-md": ["16px", {"lineHeight": "24px", "fontWeight": "600"}],
                        "headline-md": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                        "body-md": ["14px", {"lineHeight": "20px", "fontWeight": "400"}]
                    }
                },
            },
        }
    </script>
    <style>
        .grid-pattern {
            background-size: 24px 24px;
            background-image:
                linear-gradient(to right, #e0e3e5 1px, transparent 1px),
                linear-gradient(to bottom, #e0e3e5 1px, transparent 1px);
        }

        .svg-connections {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 10;
        }

        .connection-line {
            stroke: #c3c6d7;
            stroke-width: 2;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .connection-line.active {
            stroke: #2563eb;
            stroke-width: 2.5;
        }

        .node-dragging {
            opacity: 0.8;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            transform: scale(1.02);
            z-index: 50;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24
        }

        .material-symbols-outlined.fill {
            font-variation-settings: 'FILL' 1;
        }
    </style>
</head>

<body class="bg-surface text-on-surface h-screen w-screen overflow-hidden flex flex-col font-body-md antialiased">
    <nav class="bg-surface-container-lowest text-primary font-body-md text-body-md w-full sticky top-0 z-50 border-b border-outline-variant flex justify-between items-center px-lg h-16">
        <div class="flex items-center gap-md">
            <span class="font-headline-md text-headline-md font-bold text-primary">SAMS</span>
        </div>
        <div class="flex-1 max-w-xl mx-lg relative">
            <span class="material-symbols-outlined absolute left-sm top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
            <input class="w-full pl-xl pr-md py-sm bg-surface-container-low border border-outline-variant rounded focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all font-mono-md text-mono-md placeholder:text-on-surface-variant" placeholder="Search nodes..." type="text">
        </div>
        <div class="flex items-center gap-md">
            
            <div class="flex items-center gap-4 text-on-surface">
                <a href="./api/logout.php" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm font-medium transition-colors">Logout</a>
            </div>
        </div>
    </nav>
    <div class="flex flex-1 overflow-hidden h-[calc(100vh-64px)] relative">
        <aside class="bg-surface-container-low text-primary font-label-md text-label-md w-64 border-r border-outline-variant flex flex-col z-40 shrink-0 relative h-full overflow-y-auto">
            <div class="p-lg border-b border-outline-variant flex flex-col gap-sm">
                <div class="flex items-center gap-sm">
                    <div class="w-10 h-10 rounded bg-primary-container text-on-primary flex items-center justify-center font-bold text-title-lg shadow-sm">
                        SA
                    </div>
                    <div>
                        <h2 class="font-title-md text-title-md font-bold text-on-surface leading-tight">Structural Authority</h2>
                    </div>
                </div>
                <button id="create-node-btn" class="relative z-50 mt-md w-full bg-primary-container text-on-primary py-sm px-md rounded-DEFAULT font-label-md text-label-md font-medium hover:bg-primary-fixed-variant transition-colors flex items-center justify-center gap-xs">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Create New Node
                </button>
            </div>
            <nav class="flex-1 py-md">
                <ul class="flex flex-col">
                    <li>
                        <a class="group flex items-center px-md py-sm cursor-pointer text-secondary hover:bg-surface-variant transition-all border-l-4 border-transparent hover:border-outline-variant relative" href="./dashboard.php">
                            <span class="material-symbols-outlined mr-sm text-[20px]">dashboard</span>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a class="group flex items-center px-md py-sm cursor-pointer bg-secondary-container text-on-secondary-container border-l-4 border-primary font-bold relative" href="./hierarchyDesigner.php">
                            <span class="material-symbols-outlined mr-sm text-[20px] text-primary fill">account_tree</span>
                            Hierarchy Designer
                        </a>
                    </li>
                    <li>
                        <a class="group flex items-center px-md py-sm cursor-pointer text-secondary hover:bg-surface-variant transition-all border-l-4 border-transparent hover:border-outline-variant relative" href="./roleMapper.php">
                            <span class="material-symbols-outlined mr-sm text-[20px]">badge</span>
                            Role Mapper
                        </a>
                    </li>
                    <li>
                        <a class="group flex items-center px-md py-sm cursor-pointer text-secondary hover:bg-surface-variant transition-all border-l-4 border-transparent hover:border-outline-variant relative" href="./decisionFlow.php">
                            <span class="material-symbols-outlined mr-sm text-[20px]">account_tree</span>
                            Decision Flow
                        </a>
                    </li>
                    <li>
                        <a class="group flex items-center px-md py-sm cursor-pointer text-secondary hover:bg-surface-variant transition-all border-l-4 border-transparent hover:border-outline-variant relative" href="./importExport.php">
                            <span class="material-symbols-outlined mr-sm text-[20px]">swap_vert</span>
                            Import/Export
                        </a>
                    </li>
                    
                </ul>
            </nav>
        </aside>

        <div class="flex-1 relative overflow-hidden flex h-full">
            <main class="w-full h-full overflow-auto flex bg-surface-bright" id="canvas-container">
                <div id="canvas-content" class="w-[3000px] h-[3000px] relative grid-pattern shrink-0 origin-top-left transition-transform duration-100 ease-out">
                    <svg class="svg-connections w-full h-full absolute top-0 left-0 pointer-events-none" id="svg-canvas"></svg>
                    <div id="nodes-layer" class="absolute inset-0 w-full h-full"></div>
                </div>
            </main>

            <div class="absolute bottom-md right-md z-40 bg-surface-container-lowest border border-outline-variant rounded shadow-md flex items-center p-xs gap-xs pointer-events-auto">
                <button id="zoom-out-btn" class="w-8 h-8 flex items-center justify-center rounded hover:bg-surface-variant text-on-surface transition-colors">
                    <span class="material-symbols-outlined text-[20px]">remove</span>
                </button>
                <span id="zoom-level-text" class="font-mono-md text-mono-md text-on-surface px-sm min-w-[50px] text-center select-none">100%</span>
                <button id="zoom-in-btn" class="w-8 h-8 flex items-center justify-center rounded hover:bg-surface-variant text-on-surface transition-colors">
                    <span class="material-symbols-outlined text-[20px]">add</span>
                </button>
            </div>
        </div>

        <aside id="properties-panel" class="hidden w-[320px] bg-surface-container-lowest border-l border-outline-variant flex flex-col z-30 shrink-0 h-full overflow-y-auto">
            <div class="p-md border-b border-outline-variant flex justify-between items-center bg-surface sticky top-0 z-10">
                <h2 class="font-title-lg text-title-lg text-on-surface">Properties</h2>
                <button onclick="closeProperties()" class="text-on-surface-variant hover:text-on-surface p-1 rounded hover:bg-surface-variant">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-md flex flex-col gap-6">
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 bg-primary-container/10 rounded flex items-center justify-center border border-primary-container/20">
                        <span class="material-symbols-outlined text-primary text-[24px]">groups</span>
                    </div>
                    <div>
                        <div class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider mb-1">Role Configuration</div>
                        <div id="prop-header-name" class="font-title-lg text-title-lg text-on-surface">Select a node</div>
                    </div>
                </div>
                
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="font-label-md text-label-md text-on-surface-variant">Role Name</label>
                        <input id="prop-node-name" class="w-full bg-surface border border-outline-variant rounded px-3 py-2 font-body-md text-body-md text-on-surface focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all" type="text">
                    </div>
                    
                   <div class="flex flex-col gap-1">
    <label class="font-label-md text-label-md text-on-surface-variant">
        Role Category
    </label>

    <select id="prop-role-category"
        class="w-full bg-surface border border-outline-variant rounded px-3 py-2
        font-body-md text-body-md text-on-surface focus:border-primary
        focus:ring-1 focus:ring-primary focus:outline-none transition-all">

        <option value="CEO">CEO</option>
        <option value="Super Admin">Super Admin</option>
        <option value="Admin">Admin</option>
        <option value="Project Manager">Project Manager</option>
        <option value="Team Lead">Team Lead</option>
        <option value="Software Developer">Software Developer</option>
        <option value="Frontend Developer">Frontend Developer</option>
        <option value="Backend Developer">Backend Developer</option>
        <option value="Full Stack Developer">Full Stack Developer</option>
        <option value="UI/UX Designer">UI/UX Designer</option>
        <option value="QA / Software Tester">QA / Software Tester</option>
        <option value="DevOps Engineer">DevOps Engineer</option>
        <option value="Database Administrator (DBA)">Database Administrator (DBA)</option>
        <option value="Business Analyst">Business Analyst</option>
        <option value="HR Manager" selected>HR Manager</option>
        <option value="Employee">Employee</option>

    </select>
</div>

                    <div class="flex flex-col gap-1">
                        <label class="font-label-md text-label-md text-on-surface-variant">Description</label>
                        <textarea id="prop-description" rows="3" class="w-full bg-surface border border-outline-variant rounded px-3 py-2 font-body-md text-body-md text-on-surface focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all resize-none" placeholder="Enter functional details..."></textarea>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="font-label-md text-label-md text-on-surface-variant">Visual Label Color</label>
                        <div class="flex items-center gap-sm">
                            <input id="prop-label-color" type="color" class="w-10 h-10 border border-outline-variant rounded cursor-pointer p-0 bg-transparent">
                            <span class="text-xs text-on-surface-variant font-mono-md">Select color accent</span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="font-label-md text-label-md text-on-surface-variant">Node ID (System Generated)</label>
                        <input id="prop-node-id" class="w-full bg-surface-variant border border-outline-variant rounded px-3 py-2 font-mono-md text-mono-md text-on-surface-variant cursor-not-allowed" disabled type="text">
                    </div>
                </div>
            </div>
            
            <div class="mt-auto p-md border-t border-outline-variant bg-surface sticky bottom-0 z-10 flex gap-3">
                <button id="delete-node-btn" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-title-md text-body-md py-2 rounded transition-colors flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                    Delete
                </button>
                <button id="save-properties-btn" class="flex-1 bg-primary-container text-on-primary font-title-md text-body-md py-2 rounded hover:bg-primary transition-colors">Save Changes</button>
            </div>
        </aside>
    </div>

    <script>
        let currentZoom = 1; 
        const minZoom = 0.25; 
        const maxZoom = 2.0;  
        const zoomStep = 0.1; 

        const canvasContent = document.getElementById('canvas-content');
        const zoomInBtn = document.getElementById('zoom-in-btn');
        const zoomOutBtn = document.getElementById('zoom-out-btn');
        const zoomLevelText = document.getElementById('zoom-level-text');

        function applyZoom() {
            canvasContent.style.transform = `scale(${currentZoom})`;
            zoomLevelText.innerText = `${Math.round(currentZoom * 100)}%`;
        }

        if (zoomInBtn) {
            zoomInBtn.addEventListener('click', () => {
                if (currentZoom < maxZoom) {
                    currentZoom = Math.min(maxZoom, currentZoom + zoomStep);
                    applyZoom();
                }
            });
        }

        if (zoomOutBtn) {
            zoomOutBtn.addEventListener('click', () => {
                if (currentZoom > minZoom) {
                    currentZoom = Math.max(minZoom, currentZoom - zoomStep);
                    applyZoom();
                }
            });
        }

        const canvasContainer = document.getElementById('canvas-container');
        const nodesLayer = document.getElementById('nodes-layer');
        const svgCanvas = document.getElementById('svg-canvas');
        const createNodeBtn = document.getElementById('create-node-btn');

        let nodesData = [];
        let draggedNodeId = null;
        let offset = { x: 0, y: 0 };
        let selectedNodeId = null; 

        async function loadHierarchy() {
            try {
                const response = await fetch('./api/hierarchyDesigner.php');
                nodesData = await response.json();
                renderCanvas();
            } catch (err) {
                console.error("Error retrieving architectural nodes structure:", err);
            }
        }

        function renderCanvas() {
            nodesLayer.innerHTML = '';
            svgCanvas.innerHTML = '';

            nodesData.forEach(node => {
                const nodeEl = document.createElement('div');
                
                const nodeName = node.role_name || node.name || 'New Node';
                const nodeColor = node.visual_color || node.color || '#004ac6';
                const nodeCategory = node.role_category || node.type || 'Executor';
                const nodeDesc = node.description || '';

                nodeEl.className = `absolute bg-surface-container-lowest border border-outline-variant rounded shadow-sm cursor-move select-none z-20 hover:border-primary flex flex-col min-w-[240px] max-w-[280px] overflow-hidden`;
                nodeEl.style.left = `${node.pos_x}px`;
                nodeEl.style.top = `${node.pos_y}px`;
                nodeEl.setAttribute('data-id', node.id);

                nodeEl.innerHTML = `
                    <div class="w-full h-1.5" style="background-color: ${nodeColor}"></div>
                    <div class="p-3 flex flex-col gap-2 pointer-events-none">
                        <div class="flex justify-between items-start gap-1">
                            <div>
                                <div class="text-[9px] font-mono-md uppercase tracking-wider text-on-surface-variant opacity-60">ID: ${node.id}</div>
                                <div class="font-bold text-base text-on-surface leading-snug">${nodeName}</div>
                            </div>
                            <span class="text-[10px] px-2 py-0.5 rounded font-bold bg-surface-container-high text-on-surface-variant border border-outline-variant shrink-0">${nodeCategory}</span>
                        </div>
                        ${nodeDesc ? `<p class="text-xs text-on-surface-variant border-t border-outline-variant pt-1.5 line-clamp-2">${nodeDesc}</p>` : ''}
                    </div>
                `;

                nodeEl.addEventListener('mousedown', onMouseDown);
                nodeEl.addEventListener('click', (e) => {
                    if (nodeEl.classList.contains('node-dragging')) return;
                    
                    selectedNodeId = node.id;
                    const panel = document.getElementById('properties-panel');
                    panel.classList.remove('hidden');
                    panel.classList.add('flex');
                    
                    document.getElementById('prop-header-name').innerText = nodeName;
                    document.getElementById('prop-node-name').value = nodeName;
                    document.getElementById('prop-role-category').value = nodeCategory;
                    document.getElementById('prop-description').value = nodeDesc;
                    document.getElementById('prop-label-color').value = nodeColor;
                    document.getElementById('prop-node-id').value = node.id;
                });
                nodesLayer.appendChild(nodeEl);
            });

            nodesData.forEach(node => {
                if (node.parent_id) {
                    const parent = nodesData.find(n => n.id === node.parent_id);
                    if (parent) {
                        drawConnection(parent, node);
                    }
                }
            });
        }

        function drawConnection(parent, child) {
            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            
            const pX = parseInt(parent.pos_x) + 120;
            const pY = parseInt(parent.pos_y) + 65;
            const cX = parseInt(child.pos_x) + 120;
            const cY = parseInt(child.pos_y);

            const controlY = pY + (cY - pY) / 2;
            const d = `M ${pX} ${pY} C ${pX} ${controlY}, ${cX} ${controlY}, ${cX} ${cY}`;

            path.setAttribute('d', d);
            path.setAttribute('class', 'connection-line active');
            svgCanvas.appendChild(path);
        }

        function onMouseDown(e) {
            const nodeEl = e.currentTarget;
            draggedNodeId = nodeEl.getAttribute('data-id');
            
            const rect = nodeEl.getBoundingClientRect();
            offset.x = e.clientX - rect.left;
            offset.y = e.clientY - rect.top;

            nodeEl.classList.add('node-dragging');

            document.addEventListener('mousemove', onMouseMove);
            document.addEventListener('mouseup', onMouseUp);
        }

        function onMouseMove(e) {
            if (!draggedNodeId) return;

            const nodeEl = document.querySelector(`[data-id="${draggedNodeId}"]`);
            const contentRect = canvasContent.getBoundingClientRect();

            let left = (e.clientX - contentRect.left) / currentZoom - (offset.x / currentZoom);
            let top = (e.clientY - contentRect.top) / currentZoom - (offset.y / currentZoom);

            left = Math.max(0, Math.min(left, canvasContent.offsetWidth - nodeEl.offsetWidth));
            top = Math.max(0, Math.min(top, canvasContent.offsetHeight - nodeEl.offsetHeight));

            nodeEl.style.left = `${left}px`;
            nodeEl.style.top = `${top}px`;

            const nodeData = nodesData.find(n => n.id === draggedNodeId);
            if (nodeData) {
                nodeData.pos_x = left;
                nodeData.pos_y = top;
            }

            svgCanvas.innerHTML = '';
            nodesData.forEach(node => {
                if (node.parent_id) {
                    const parent = nodesData.find(n => n.id === node.parent_id);
                    if (parent) drawConnection(parent, node);
                }
            });
        }

        async function onMouseUp(e) {
            if (!draggedNodeId) return;

            const draggedEl = document.querySelector(`[data-id="${draggedNodeId}"]`);
            draggedEl.classList.remove('node-dragging');

            const dragRect = draggedEl.getBoundingClientRect();
            let targetParentId = null;

            document.querySelectorAll('#nodes-layer > div').forEach(el => {
                const id = el.getAttribute('data-id');
                if (id !== draggedNodeId) {
                    const rect = el.getBoundingClientRect();
                    const centerX = dragRect.left + dragRect.width / 2;
                    const centerY = dragRect.top + dragRect.height / 2;

                    if (centerX >= rect.left && centerX <= rect.right &&
                        centerY >= rect.top && centerY <= rect.bottom) {
                        targetParentId = id;
                    }
                }
            });

            const currentMovedNode = nodesData.find(n => n.id === draggedNodeId);

            if (targetParentId) {
                if (currentMovedNode.parent_id !== targetParentId) {
                    currentMovedNode.parent_id = targetParentId;
                    await fetch('api/hierarchyDesigner.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'connect_nodes', child_id: draggedNodeId, parent_id: targetParentId })
                    });
                }
            } 
            
            await fetch('api/hierarchyDesigner.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'update_position',
                    id: draggedNodeId,
                    pos_x: currentMovedNode.pos_x,
                    pos_y: currentMovedNode.pos_y
                })
            });

            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
            draggedNodeId = null;

            renderCanvas();
        }

        if (createNodeBtn) {
            createNodeBtn.addEventListener('click', async () => {
                try {
                    const defaultName = "New Node"; 
                    const res = await fetch('./api/hierarchyDesigner.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'create', name: defaultName })
                    });

                    if (!res.ok) {
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }

                    const data = await res.json();
                    if (data.success) {
                        loadHierarchy(); 
                    } else {
                        alert("डेटाबेस एरर: " + data.error);
                    }
                } catch (err) {
                    alert("जावास्क्रिप्ट एरर: API फ़ाइल तक रिक्वेस्ट नहीं पहुँच पा रही है।");
                    console.error("Error creating node:", err);
                }
            });
        }

        function closeProperties() {
            selectedNodeId = null;
            const panel = document.getElementById('properties-panel');
            panel.classList.remove('flex');
            panel.classList.add('hidden');
            
            document.getElementById('prop-header-name').innerText = "Select a node";
            document.getElementById('prop-node-name').value = "";
            document.getElementById('prop-role-category').value = "Executor";
            document.getElementById('prop-description').value = "";
            document.getElementById('prop-label-color').value = "#004ac6";
            document.getElementById('prop-node-id').value = "";
        }

        const savePropertiesBtn = document.getElementById('save-properties-btn');
        if (savePropertiesBtn) {
            savePropertiesBtn.addEventListener('click', async () => {
                if (!selectedNodeId) {
                    alert("Please select a node first.");
                    return;
                }

                const updatedName = document.getElementById('prop-node-name').value;
                const updatedCategory = document.getElementById('prop-role-category').value;
                const updatedDescription = document.getElementById('prop-description').value;
                const updatedColor = document.getElementById('prop-label-color').value;

                try {
                    const res = await fetch('./api/hierarchyDesigner.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ 
                            action: 'update_name', 
                            id: selectedNodeId, 
                            name: updatedName,
                            type: updatedCategory, 
                            description: updatedDescription,
                            color: updatedColor
                        })
                    });

                    const data = await res.json();
                    if (data.success) {
                        const node = nodesData.find(n => n.id === selectedNodeId);
                        if (node) {
                            node.role_name = updatedName;
                            node.name = updatedName;
                            node.role_category = updatedCategory;
                            node.type = updatedCategory;
                            node.description = updatedDescription;
                            node.visual_color = updatedColor;
                            node.color = updatedColor;
                        }
                        
                        document.getElementById('prop-header-name').innerText = updatedName;
                        renderCanvas(); 
                        alert("नोड डिटेल्स सफलतापूर्वक अपडेट हो गई हैं!");
                    } else {
                        alert("अपडेट करने में एरर आया: " + data.error);
                    }
                } catch (err) {
                    console.error("Error updating node properties:", err);
                }
            });
        }

        const deleteNodeBtn = document.getElementById('delete-node-btn');
        if (deleteNodeBtn) {
            deleteNodeBtn.addEventListener('click', async () => {
                if (!selectedNodeId) {
                    alert("कृपया पहले किसी नोड को सेलेक्ट करें।");
                    return;
                }

                if (confirm("क्या आप वाकई इस नोड को हटाना चाहते हैं?")) {
                    try {
                        const res = await fetch('./api/hierarchyDesigner.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ 
                                action: 'delete_node', 
                                id: selectedNodeId 
                            })
                        });

                        const data = await res.json();
                        if (data.success) {
                            nodesData = nodesData.filter(n => n.id !== selectedNodeId);
                            nodesData.forEach(n => {
                                if (n.parent_id === selectedNodeId) n.parent_id = null;
                            });

                            closeProperties(); 
                            renderCanvas();    
                            alert("नोड सफलतापूर्वक डिलीट हो गया है!");
                        } else {
                            alert("डिलीट करने में एरर आया: " + data.error);
                        }
                    } catch (err) {
                        console.error("Error deleting architecture node layout:", err);
                    }
                }
            });
        }

        loadHierarchy();
    </script>
</body>

</html>

