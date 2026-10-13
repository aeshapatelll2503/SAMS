<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>SAMS - Decision Flow Designer</title>
    <!-- Tailwind CSS v3 with Plugins -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Material Symbols for sidebar icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style data-purpose="custom-grid">
        .bg-grid-pattern {
            background-size: 20px 20px;
            background-image:
                linear-gradient(to right, rgba(229, 231, 235, 0.5) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(229, 231, 235, 0.5) 1px, transparent 1px);
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Scale Container Wrapper for Smooth Zooming */
        #zoomWrapper {
            transform-origin: 0 0;
            transition: transform 0.1s ease-out;
        }
    </style>
    <style data-purpose="layout-adjustments">
        body,
        html {
            height: 100%;
            margin: 0;
            overflow: hidden;
        }

        .app-container {
            display: grid;
            grid-template-rows: 64px 1fr;
            height: 100vh;
        }

        .content-area {
            display: grid;
            grid-template-columns: 260px 1fr 300px;
            height: calc(100vh - 64px);
        }

        /* Prevent text selection when drawing wires */
        .noselect {
            user-select: none;
        }
    </style>
</head>

<body class="bg-gray-50 text-slate-800 noselect">
    <!-- BEGIN: AppContainer -->
    <div class="app-container">
        <!-- BEGIN: TopNavigationBar -->
        <header class="bg-white border-b border-gray-200 flex items-center justify-between px-6 z-20">
            <div class="flex items-center gap-2">
                <span class="text-2xl font-bold text-blue-700 tracking-tight">SAMS</span>
            </div>
            
            <div class="flex items-center gap-md">
                <div class="flex items-center gap-3 text-on-surface">
                    <span class="font-medium text-sm">Hi, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>!</span>
                    <?php echo render_role_badge(); ?>
                    <a href="./api/logout.php" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm font-medium transition-colors">Logout</a>
                </div>
            </div>
        </header>
        <!-- END: TopNavigationBar -->

        <!-- Main Content Wrapper -->
        <div class="content-area">
            <!-- BEGIN: LeftSidebar -->
            <aside class="bg-gray-100 border-r border-gray-200 flex flex-col z-10 shrink-0 relative h-full overflow-y-auto">
                <div class="p-4 border-b border-gray-200 flex flex-col gap-2">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded bg-blue-600 text-white flex items-center justify-center font-bold text-lg shadow-sm">
                            SA
                        </div>
                        <div>
                            <h2 class="font-bold text-sm text-gray-800 leading-tight">Structural <br> Authority</h2>
                        </div>
                    </div>
                </div>
                <nav class="flex-1 py-4">
                    <ul class="flex flex-col space-y-1">
                        <li>
                            <a class="group flex items-center px-6 py-1 cursor-pointer text-gray-600 hover:bg-gray-50 transition-all border-l-4 border-transparent hover:border-blue-600 text-sm font-medium" href="./dashboard.php">
                                <span class="material-symbols-outlined mr-3 text-[20px]">dashboard</span>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a class="group flex items-center px-6 py-1 cursor-pointer text-gray-600 hover:bg-gray-50 transition-all border-l-4 border-transparent hover:border-blue-600 text-sm font-medium" href="./hierarchyDesigner.php">
                                <span class="material-symbols-outlined mr-3 text-[20px]">account_tree</span>
                                Hierarchy Designer
                            </a>
                        </li>
                        <li>
                            <a class="group flex items-center px-6 py-1 cursor-pointer text-gray-600 hover:bg-gray-50 transition-all border-l-4 border-transparent hover:border-blue-600 text-sm font-medium" href="./roleMapper.php">
                                <span class="material-symbols-outlined mr-3 text-[20px]">badge</span>
                                Role Mapper
                            </a>
                        </li>
                        <li>
                            <a class="group flex items-center px-6 py-1 cursor-pointer bg-blue-100 border-l-4 border-blue-600 font-bold text-sm" href="./decisionFlow.php">
                                <span class="material-symbols-outlined mr-3 text-[20px] text-blue-700">account_tree</span>
                                Decision Flow
                            </a>
                        </li>
                        <li>
                            <a class="group flex items-center px-6 py-1 cursor-pointer text-gray-600 hover:bg-gray-50 transition-all border-l-4 border-transparent hover:border-blue-600 text-sm font-medium" href="./importExport.php">
                                <span class="material-symbols-outlined mr-3 text-[20px]">swap_vert</span>
                                Import/Export
                            </a>
                        </li>
                    </ul>
                </nav>
            </aside>
            <!-- END: LeftSidebar -->

            <!-- BEGIN: MainCanvasArea -->
            <main id="canvasContainer" class="relative bg-white bg-grid-pattern overflow-auto p-0 shadow-inner select-none">
                
                <!-- Zoom Container Wrapper -->
                <div id="zoomWrapper" class="relative w-[2500px] h-[2500px]">
                    <!-- Interactive Connection Layer -->
                    <svg id="svgWorkspace" class="absolute top-0 left-0 w-[2500px] h-[2500px] pointer-events-none z-0">
                        <defs>
                            <marker id="arrow" viewBox="0 0 10 10" refX="6" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                                <path d="M 0 1 L 10 5 L 0 9 z" fill="#64748b" />
                            </marker>
                        </defs>
                    </svg>

                    <div id="flowCanvas" class="relative w-[2500px] h-[2500px] pointer-events-auto z-10">
                        <!-- Dynamic nodes spawn here -->
                    </div>
                </div>

                <!-- Floating Zoom / Clear Controls -->
                <div class="fixed bottom-6 right-[320px] bg-white border border-gray-200 rounded-md shadow-lg flex items-center divide-x divide-gray-200 z-30 p-1 gap-1">
                    <!-- Clear Canvas Button -->
                    <button id="clearCanvasBtn" title="Clear Canvas" class="p-2 hover:bg-red-50 text-red-600 flex items-center gap-1 px-3 text-xs font-semibold rounded transition-colors">
                        <span class="material-symbols-outlined text-[16px]">delete_sweep</span> 
                        Clear All
                    </button>
                    
                    <!-- Zoom Controls -->
                    <div class="flex items-center p-1 gap-1">
                        <button id="zoom-out-btn" title="Zoom Out" class="w-8 h-8 flex items-center justify-center rounded hover:bg-gray-100 text-gray-700 transition-colors">
                            <span class="material-symbols-outlined text-[20px]">remove</span>
                        </button>
                        <span id="zoom-level-text" class="font-mono text-sm text-gray-700 px-2 min-w-[50px] text-center select-none">100%</span>
                        <button id="zoom-in-btn" title="Zoom In" class="w-8 h-8 flex items-center justify-center rounded hover:bg-gray-100 text-gray-700 transition-colors">
                            <span class="material-symbols-outlined text-[20px]">add</span>
                        </button>
                    </div>
                </div>
            </main>
            <!-- END: MainCanvasArea -->

            <!-- BEGIN: ElementPalette -->
            <aside class="bg-white border-l border-gray-200 flex flex-col z-10 w-full overflow-hidden">
                <div class="p-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-gray-500">Element Palette</h2>
                    <p class="text-[11px] text-gray-400 mt-0.5">Drag blocks onto the designer canvas grid layout</p>
                </div>
                <div class="flex-1 overflow-y-auto p-4 space-y-4">
                    <div class="space-y-2">
                        <!-- Start Node -->
                        <div class="palette-item group flex items-center gap-3 p-3 bg-white border border-gray-200 rounded shadow-sm hover:border-blue-500 transition-all cursor-grab active:cursor-grabbing" draggable="true" data-type="Start">
                            <div class="w-8 h-8 rounded-full bg-green-50 flex items-center justify-center text-green-600 shrink-0">
                                <span class="material-symbols-outlined text-[20px]">play_arrow</span>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-700">Start Node</div>
                                <div class="text-[11px] text-gray-400">Entrypoint definition</div>
                            </div>
                        </div>

                        <!-- Condition -->
                        <div class="palette-item group flex items-center gap-3 p-3 bg-white border border-gray-200 rounded shadow-sm hover:border-blue-500 transition-all cursor-grab active:cursor-grabbing" draggable="true" data-type="Condition">
                            <div class="w-8 h-8 rounded bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 shrink-0 rotate-45">
                                <span class="material-symbols-outlined text-[18px] -rotate-45">alt_route</span>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-700">Condition Block</div>
                                <div class="text-[11px] text-gray-400">Branching Logic (IF / ELSE)</div>
                            </div>
                        </div>

                        <!-- Approval Step -->
                        <div class="palette-item group flex items-center gap-3 p-3 bg-white border border-gray-200 rounded shadow-sm hover:border-blue-500 transition-all cursor-grab active:cursor-grabbing" draggable="true" data-type="Approval">
                            <div class="w-8 h-8 rounded bg-blue-50 flex items-center justify-center text-blue-600 shrink-0">
                                <span class="material-symbols-outlined text-[20px]">verified</span>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-700">Approval Step</div>
                                <div class="text-[11px] text-gray-400">Authority signature required</div>
                            </div>
                        </div>

                        <!-- Escalation Step -->
                        <div class="palette-item group flex items-center gap-3 p-3 bg-white border border-gray-200 rounded shadow-sm hover:border-purple-500 transition-all cursor-grab active:cursor-grabbing" draggable="true" data-type="Escalation">
                            <div class="w-8 h-8 rounded bg-purple-50 flex items-center justify-center text-purple-600 shrink-0">
                                <span class="material-symbols-outlined text-[20px]">warning</span>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-700">Escalation Step</div>
                                <div class="text-[11px] text-gray-400">SLA or priority redirect</div>
                            </div>
                        </div>

                        <!-- Stop Node -->
                        <div class="palette-item group flex items-center gap-3 p-3 bg-white border border-gray-200 rounded shadow-sm hover:border-rose-600 transition-all cursor-grab active:cursor-grabbing" draggable="true" data-type="Stop">
                            <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-rose-700 shrink-0">
                                <span class="material-symbols-outlined text-[20px]">stop</span>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-700">Stop Node</div>
                                <div class="text-[11px] text-gray-400">Workflow termination</div>
                            </div>
                        </div>

                        <button id="saveBtn" class="flex items-center gap-1.5 px-4 py-1.5 bg-blue-600 text-white hover:bg-blue-700 text-sm font-medium rounded shadow-sm transition-all w-full justify-center mt-4">
                            <span class="material-symbols-outlined text-[18px]">save</span>
                            Save
                        </button>
                    </div>
                </div>
            </aside>
            <!-- END: ElementPalette -->
        </div>
        <!-- END: ContentArea -->
    </div>
    <!-- END: AppContainer -->

    <!-- BEGIN: Workflow Application Logic Engine -->
    <script>
        // State Repositories
        let flowData = {
            nodes: [],
            connections: [] 
        };

        // Wire Connection State Tracker
        let activeLinkage = null;

        // Zoom State Engine
        let currentZoom = 1.0;
        const MIN_ZOOM = 0.5;
        const MAX_ZOOM = 2.0;
        const ZOOM_STEP = 0.1;

        const flowCanvas = document.getElementById('flowCanvas');
        const svgWorkspace = document.getElementById('svgWorkspace');
        const canvasContainer = document.getElementById('canvasContainer');
        const zoomWrapper = document.getElementById('zoomWrapper');
        const zoomLevelText = document.getElementById('zoom-level-text');

        // Initialize App Engine Configuration
        window.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
            loadFlowConfiguration();
            setupPaletteDragAndDrop();
            setupGlobalEventListeners();
            setupZoomControls();
        });

        // Zoom Engine Implementation
        function setupZoomControls() {
            const zoomInBtn = document.getElementById('zoom-in-btn');
            const zoomOutBtn = document.getElementById('zoom-out-btn');

            zoomInBtn.addEventListener('click', () => {
                if (currentZoom < MAX_ZOOM) {
                    currentZoom = Math.min(MAX_ZOOM, currentZoom + ZOOM_STEP);
                    applyZoom();
                }
            });

            zoomOutBtn.addEventListener('click', () => {
                if (currentZoom > MIN_ZOOM) {
                    currentZoom = Math.max(MIN_ZOOM, currentZoom - ZOOM_STEP);
                    applyZoom();
                }
            });
        }

        function applyZoom() {
            zoomWrapper.style.transform = `scale(${currentZoom})`;
            zoomLevelText.textContent = `${Math.round(currentZoom * 100)}%`;
            renderConnections();
        }

        // Configures the HTML5 item palette drag/drop handlers
        function setupPaletteDragAndDrop() {
            const items = document.querySelectorAll('.palette-item');
            items.forEach(item => {
                item.addEventListener('dragstart', (e) => {
                    e.dataTransfer.setData('node-type', item.getAttribute('data-type'));
                });
            });

            flowCanvas.addEventListener('dragover', (e) => {
                e.preventDefault();
            });

            flowCanvas.addEventListener('drop', (e) => {
                e.preventDefault();
                const type = e.dataTransfer.getData('node-type');
                if (!type) return;

                const rect = zoomWrapper.getBoundingClientRect();
                const x = (e.clientX - rect.left) / currentZoom - 100;
                const y = (e.clientY - rect.top) / currentZoom - 40;

                createNewNode(type, 'New ' + type + ' Option', Math.max(10, x), Math.max(10, y));
            });
        }

        function createNewNode(type, label, x, y, id = null) {
            const nodeId = id || 'node_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
            
            const nodeObj = {
                id: nodeId,
                type: type,
                label: label,
                x: x,
                y: y
            };

            flowData.nodes.push(nodeObj);
            renderNodeToCanvas(nodeObj);
            renderConnections();
        }

        function renderNodeToCanvas(node) {
            const nodeEl = document.createElement('div');
            nodeEl.id = node.id;
            nodeEl.style.left = node.x + 'px';
            nodeEl.style.top = node.y + 'px';
            
            const isCondition = node.type === 'Condition';
            const isStop = node.type === 'Stop';
            const isEscalation = node.type === 'Escalation';
            
            nodeEl.className = `absolute w-56 bg-white border-2 rounded-lg shadow-sm z-20 hover:shadow-md transition-shadow group flex flex-col p-3 ${
                node.type === 'Start' ? 'border-green-500' :
                isCondition ? 'border-amber-500 min-h-[110px]' :
                node.type === 'Approval' ? 'border-blue-500' :
                isEscalation ? 'border-purple-500' :
                isStop ? 'border-rose-600 bg-white' : 'border-gray-400'
            }`;

            // Heading & Action Bar
            const header = document.createElement('div');
            header.className = 'flex items-center justify-between border-b border-gray-100 pb-1.5 mb-2 cursor-move node-drag-handle';
            
            let typeBadgeColor = 'bg-gray-100 text-gray-700';
            if(node.type === 'Start') typeBadgeColor = 'bg-green-100 text-green-700';
            if(node.type === 'Condition') typeBadgeColor = 'bg-amber-100 text-amber-700';
            if(node.type === 'Approval') typeBadgeColor = 'bg-blue-100 text-blue-700';
            if(isEscalation) typeBadgeColor = 'bg-purple-100 text-purple-700';
            if(isStop) typeBadgeColor = 'bg-rose-600 text-white font-bold';

            header.innerHTML = `
                <span class="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded ${typeBadgeColor}">${node.type}</span>
                <button class="text-gray-400 hover:text-red-500 transition-colors remove-node-btn flex items-center">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                </button>
            `;
            nodeEl.appendChild(header);

            // Input Field
            const inputContainer = document.createElement('div');
            inputContainer.className = 'w-full';
            const placeholderText = isCondition ? 'e.g., Amount > $5000' : isStop ? 'End State Title' : 'Step Title';
            
            const input = document.createElement('input');
            input.type = 'text';
            input.value = node.label;
            input.placeholder = placeholderText;
            input.className = 'w-full text-xs font-medium text-gray-700 bg-gray-50 border border-gray-200 rounded px-2 py-1 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500';
            input.addEventListener('input', (e) => {
                node.label = e.target.value;
            });
            inputContainer.appendChild(input);
            nodeEl.appendChild(inputContainer);

            // Left Input Junction (all except Start)
            if (node.type !== 'Start') {
                const inputPort = document.createElement('div');
                inputPort.className = 'absolute -left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 bg-gray-300 border-2 border-white rounded-full hover:bg-blue-500 transition-colors cursor-crosshair connection-port input-port';
                inputPort.setAttribute('data-node-id', node.id);
                nodeEl.appendChild(inputPort);
            }

            // Right Output Junctions (Except Stop node)
            if (!isStop) {
                if (isCondition) {
                    // IF Branch Output (Top Right)
                    const ifPortContainer = document.createElement('div');
                    ifPortContainer.className = 'absolute -right-2.5 top-8 flex items-center gap-1';
                    
                    const ifLabel = document.createElement('span');
                    ifLabel.className = 'text-[9px] font-bold text-green-600 bg-green-50 px-1 rounded border border-green-200 -translate-x-6 select-none pointer-events-none';
                    ifLabel.innerText = 'IF';
                    
                    const ifPort = document.createElement('div');
                    ifPort.className = 'w-4 h-4 bg-green-500 border-2 border-white rounded-full hover:bg-green-700 transition-colors cursor-crosshair connection-port output-port';
                    ifPort.setAttribute('data-node-id', node.id);
                    ifPort.setAttribute('data-port-type', 'if');

                    ifPortContainer.appendChild(ifLabel);
                    ifPortContainer.appendChild(ifPort);
                    nodeEl.appendChild(ifPortContainer);

                    // ELSE Branch Output (Bottom Right)
                    const elsePortContainer = document.createElement('div');
                    elsePortContainer.className = 'absolute -right-2.5 bottom-3 flex items-center gap-1';
                    
                    const elseLabel = document.createElement('span');
                    elseLabel.className = 'text-[9px] font-bold text-amber-600 bg-amber-50 px-1 rounded border border-amber-200 -translate-x-8 select-none pointer-events-none';
                    elseLabel.innerText = 'ELSE';

                    const elsePort = document.createElement('div');
                    elsePort.className = 'w-4 h-4 bg-amber-500 border-2 border-white rounded-full hover:bg-amber-700 transition-colors cursor-crosshair connection-port output-port';
                    elsePort.setAttribute('data-node-id', node.id);
                    elsePort.setAttribute('data-port-type', 'else');

                    elsePortContainer.appendChild(elseLabel);
                    elsePortContainer.appendChild(elsePort);
                    nodeEl.appendChild(elsePortContainer);

                } else {
                    // Standard Single Output (Right Center)
                    const outputPort = document.createElement('div');
                    outputPort.className = 'absolute -right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 bg-blue-500 border-2 border-white rounded-full hover:bg-blue-700 transition-colors cursor-crosshair connection-port output-port';
                    outputPort.setAttribute('data-node-id', node.id);
                    outputPort.setAttribute('data-port-type', 'default');
                    nodeEl.appendChild(outputPort);
                }
            }

            // Drag Placement Engine Logic
            let isDragging = false;
            let startMouseX, startMouseY;
            let startNodeX, startNodeY;

            header.addEventListener('mousedown', (e) => {
                if(e.target.closest('.remove-node-btn')) return;
                isDragging = true;
                nodeEl.style.zIndex = 50;
                startMouseX = e.clientX;
                startMouseY = e.clientY;
                startNodeX = node.x;
                startNodeY = node.y;
                e.preventDefault();
            });

            document.addEventListener('mousemove', (e) => {
                if (!isDragging) return;

                const dx = (e.clientX - startMouseX) / currentZoom;
                const dy = (e.clientY - startMouseY) / currentZoom;

                let newX = Math.max(0, Math.min(startNodeX + dx, 2300));
                let newY = Math.max(0, Math.min(startNodeY + dy, 2300));

                node.x = newX;
                node.y = newY;
                nodeEl.style.left = newX + 'px';
                nodeEl.style.top = newY + 'px';

                renderConnections();
            });

            document.addEventListener('mouseup', () => {
                if (isDragging) {
                    isDragging = false;
                    nodeEl.style.zIndex = 20;
                }
            });

            // Node Removal
            nodeEl.querySelector('.remove-node-btn').addEventListener('click', () => {
                flowData.nodes = flowData.nodes.filter(n => n.id !== node.id);
                flowData.connections = flowData.connections.filter(c => c.from !== node.id && c.to !== node.id);
                nodeEl.remove();
                renderConnections();
            });

            flowCanvas.appendChild(nodeEl);
        }

        // Interactive Connection Drawing
        function setupGlobalEventListeners() {
            document.addEventListener('mousedown', (e) => {
                if (e.target.classList.contains('output-port')) {
                    const nodeId = e.target.getAttribute('data-node-id');
                    const portType = e.target.getAttribute('data-port-type') || 'default';
                    const portRect = e.target.getBoundingClientRect();
                    const canvasRect = zoomWrapper.getBoundingClientRect();

                    activeLinkage = {
                        from: nodeId,
                        fromPort: portType,
                        startX: (portRect.left - canvasRect.left) / currentZoom + 8,
                        startY: (portRect.top - canvasRect.top) / currentZoom + 8
                    };
                    e.preventDefault();
                }
            });

            document.addEventListener('mousemove', (e) => {
                if (!activeLinkage) return;

                const canvasRect = zoomWrapper.getBoundingClientRect();
                const currentX = (e.clientX - canvasRect.left) / currentZoom;
                const currentY = (e.clientY - canvasRect.top) / currentZoom;

                renderConnections();
                drawTempLine(activeLinkage.startX, activeLinkage.startY, currentX, currentY);
            });

            document.addEventListener('mouseup', (e) => {
                if (!activeLinkage) return;

                const targetPort = e.target.closest('.input-port');
                if (targetPort) {
                    const destinationNodeId = targetPort.getAttribute('data-node-id');
                    
                    if (destinationNodeId !== activeLinkage.from) {
                        const duplicate = flowData.connections.some(c => 
                            c.from === activeLinkage.from && 
                            c.to === destinationNodeId && 
                            (c.fromPort || 'default') === activeLinkage.fromPort
                        );
                        
                        if (!duplicate) {
                            flowData.connections.push({
                                from: activeLinkage.from,
                                to: destinationNodeId,
                                fromPort: activeLinkage.fromPort
                            });
                        }
                    }
                }

                activeLinkage = null;
                const tempLine = document.getElementById('tempPath');
                if (tempLine) tempLine.remove();

                renderConnections();
            });

            document.getElementById('saveBtn').addEventListener('click', saveFlowConfiguration);
            
            // Updated Clear All with API logging sync
            document.getElementById('clearCanvasBtn').addEventListener('click', async () => {
                if(confirm('Are you sure you want to completely flush the active flow setup?')) {
                    flowData = { nodes: [], connections: [] };
                    flowCanvas.innerHTML = '';
                    renderConnections();

                    try {
                        await fetch('api/decisionFlow.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ ...flowData, action: 'clear' })
                        });
                    } catch (err) {
                        console.error('Clear log sync error:', err);
                    }
                }
            });
        }

        function drawTempLine(x1, y1, x2, y2) {
            let path = document.getElementById('tempPath');
            if (!path) {
                path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                path.id = 'tempPath';
                path.setAttribute('stroke', '#3b82f6');
                path.setAttribute('stroke-width', '2.5');
                path.setAttribute('fill', 'none');
                path.setAttribute('stroke-dasharray', '5,5');
                svgWorkspace.appendChild(path);
            }
            
            const controlOffset = Math.abs(x2 - x1) * 0.5;
            const d = `M ${x1} ${y1} C ${x1 + controlOffset} ${y1}, ${x2 - controlOffset} ${y2}, ${x2} ${y2}`;
            path.setAttribute('d', d);
        }

        // Re-render SVG Lines & Path Badges cleanly
        function renderConnections() {
            const existingElements = Array.from(svgWorkspace.children);
            existingElements.forEach(el => {
                if (el.tagName.toLowerCase() !== 'defs' && el.id !== 'tempPath') {
                    el.remove();
                }
            });

            const canvasRect = zoomWrapper.getBoundingClientRect();

            flowData.connections.forEach((conn, index) => {
                const fromNode = document.getElementById(conn.from);
                const toNode = document.getElementById(conn.to);

                if (fromNode && toNode) {
                    const portType = conn.fromPort || 'default';
                    const fromPort = fromNode.querySelector(`.output-port[data-port-type="${portType}"]`) || fromNode.querySelector('.output-port');
                    const toPort = toNode.querySelector('.input-port');

                    if (fromPort && toPort) {
                        const rFrom = fromPort.getBoundingClientRect();
                        const rTo = toPort.getBoundingClientRect();

                        const x1 = (rFrom.left - canvasRect.left) / currentZoom + 8;
                        const y1 = (rFrom.top - canvasRect.top) / currentZoom + 8;
                        const x2 = (rTo.left - canvasRect.left) / currentZoom + 8;
                        const y2 = (rTo.top - canvasRect.top) / currentZoom + 8;

                        const controlOffset = Math.max(60, Math.abs(x2 - x1) * 0.5);
                        const d = `M ${x1} ${y1} C ${x1 + controlOffset} ${y1}, ${x2 - controlOffset} ${y2}, ${x2} ${y2}`;

                        const group = document.createElementNS('http://www.w3.org/2000/svg', 'g');
                        group.setAttribute('class', 'connection-group cursor-pointer');

                        const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                        path.setAttribute('d', d);
                        
                        const strokeColor = portType === 'if' ? '#16a34a' : portType === 'else' ? '#d97706' : '#64748b';
                        path.setAttribute('stroke', strokeColor);
                        path.setAttribute('stroke-width', '2');
                        path.setAttribute('fill', 'none');
                        path.setAttribute('marker-end', 'url(#arrow)');

                        group.appendChild(path);

                        if (portType === 'if' || portType === 'else') {
                            const midX = (x1 + x2) / 2;
                            const midY = (y1 + y2) / 2;

                            const textBg = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                            textBg.setAttribute('x', midX - 16);
                            textBg.setAttribute('y', midY - 10);
                            textBg.setAttribute('width', '32');
                            textBg.setAttribute('height', '20');
                            textBg.setAttribute('rx', '4');
                            textBg.setAttribute('fill', portType === 'if' ? '#dcfce7' : '#fef3c7');
                            textBg.setAttribute('stroke', portType === 'if' ? '#86efac' : '#fde68a');

                            const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                            text.setAttribute('x', midX);
                            text.setAttribute('y', midY + 4);
                            text.setAttribute('text-anchor', 'middle');
                            text.setAttribute('font-size', '10');
                            text.setAttribute('font-weight', 'bold');
                            text.setAttribute('fill', portType === 'if' ? '#15803d' : '#b45309');
                            text.textContent = portType.toUpperCase();

                            group.appendChild(textBg);
                            group.appendChild(text);
                        }

                        group.addEventListener('dblclick', () => {
                            flowData.connections.splice(index, 1);
                            renderConnections();
                        });

                        svgWorkspace.appendChild(group);
                    }
                }
            });
        }

        function validateFlowArchitecture(showAlertSuccess = false) {
            const nodes = flowData.nodes;
            const startNodes = nodes.filter(n => n.type === 'Start');

            if (startNodes.length === 0) {
                alert('⚠️ Validation Failure: Your structural flowchart logic must contain at least one designated "Start Node".');
                return false;
            }

            if (nodes.length > 1 && flowData.connections.length === 0) {
                alert('⚠️ Validation Failure: Multiple node blocks exist, but no path connections link them together.');
                return false;
            }

            if (showAlertSuccess) {
                alert('✅ Structural Integrity Verified: Decision flow structure meets rules.');
            }
            return true;
        }

        async function loadFlowConfiguration() {
            try {
                const response = await fetch('api/decisionFlow.php');
                const data = await response.json();
                
                flowCanvas.innerHTML = '';
                flowData = { nodes: [], connections: [] };

                if (data.nodes && data.nodes.length > 0) {
                    data.nodes.forEach(node => {
                        createNewNode(node.type, node.label, node.x, node.y, node.id);
                    });
                    flowData.connections = data.connections || [];
                    renderConnections();
                }
            } catch (err) {
                console.error('Failed to read workflow payload stream context:', err);
            }
        }

        async function saveFlowConfiguration() {
            if (!validateFlowArchitecture(false)) return;

            try {
                const response = await fetch('api/decisionFlow.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(flowData)
                });
                const res = await response.json();
                if (res.status === 'success') {
                    alert('🎉 Decision flow structure saved successfully.');
                } else {
                    alert('Error saving workflow config: ' + res.message);
                }
            } catch (err) {
                alert('Critical connectivity error during save execution.');
                console.error(err);
            }
        }
    </script>
    <!-- END: Workflow Application Logic Engine -->
</body>

</html>