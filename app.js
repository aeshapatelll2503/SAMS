document.addEventListener("DOMContentLoaded", () => {
  const canvas = document.getElementById("canvas");
  const svgCanvas = document.getElementById("svg-canvas");
  let activeNode = null;
  let allNodesData = [];

  // 1. Load nodes from DB
  async function loadNodes() {
    const existingNodes = canvas.querySelectorAll(".node-card");
    existingNodes.forEach((n) => n.remove());
    svgCanvas.innerHTML = "";

    const res = await fetch("http://localhost/sams/api.php");
    allNodesData = await res.json();

    allNodesData.forEach(renderNode);
    drawAllConnections();
  }

  // 2. Render node object onto canvas
  function renderNode(node) {
    const div = document.createElement("div");
    div.className = `node-card ${node.node_type.toLowerCase()}`;
    div.style.left = `${node.pos_x}px`;
    div.style.top = `${node.pos_y}px`;
    div.id = node.id;
    div.innerHTML = `
            <strong>${node.node_type}</strong>
            <div>${node.node_name}</div>
            <small>ID: ${node.id}</small>
        `;

    div.addEventListener("mousedown", (e) => startDrag(e, div, node));
    div.addEventListener("click", () => openProperties(node));

    canvas.appendChild(div);
  }

  // 3. Smart Drag and Drop with Collision Detection
  function startDrag(e, element, node) {
    let startX = e.clientX - element.offsetLeft;
    let startY = e.clientY - element.offsetTop;
    element.style.zIndex = "100"; // Drag karte waqt sabse upar dikhe

    function doDrag(ev) {
      const newX = ev.clientX - startX;
      const newY = ev.clientY - startY;
      element.style.left = `${newX}px`;
      element.style.top = `${newY}px`;

      node.pos_x = newX;
      node.pos_y = newY;

      // Live connection redraw karo
      drawAllConnections();

      // Visual indicator: Check karo kya yeh kisi dusre node ke upar hai?
      let targetParent = checkCollision(element, node.id);
      if (targetParent) {
        targetParent.style.border = "2px dashed #34a853"; // Green border if hover
      } else {
        // Purane targets ki border clean karo
        canvas.querySelectorAll(".node-card").forEach((nc) => {
          if (nc.id !== element.id) nc.style.border = "";
        });
      }
    }

    async function stopDrag() {
      window.removeEventListener("mousemove", doDrag);
      window.removeEventListener("mouseup", stopDrag);
      element.style.zIndex = "10";

      // Check karo ki kis node par drop kiya gaya hai
      let targetParentElement = checkCollision(element, node.id);

      if (targetParentElement) {
        // Naya parent mil gaya!
        node.parent_id = targetParentElement.id;
        targetParentElement.style.border = ""; // Border reset karo
      }

      // Sync database with new Position and new Parent ID
      await updateNodeInDB(node);

      // Refresh takki hierarchy updates screen par dikhein
      loadNodes();
    }

    window.addEventListener("mousemove", doDrag);
    window.addEventListener("mouseup", stopDrag);
  }

  // 4. Collision Detection Logic (Overlap find karne ke liye)
  function checkCollision(draggedElement, draggedId) {
    const draggedRect = draggedElement.getBoundingClientRect();
    const allNodeElements = canvas.querySelectorAll(".node-card");
    let matchedElement = null;

    allNodeElements.forEach((target) => {
      if (target.id !== draggedId) {
        // Khud se collision check nahi karna hai
        const targetRect = target.getBoundingClientRect();

        // Check overlap math formula
        const isOverlapping = !(
          draggedRect.right < targetRect.left ||
          draggedRect.left > targetRect.right ||
          draggedRect.bottom < targetRect.top ||
          draggedRect.top > targetRect.bottom
        );

        if (isOverlapping) {
          matchedElement = target;
        }
      }
    });

    return matchedElement;
  }

  // 5. Draw connections line
  function drawAllConnections() {
    svgCanvas.innerHTML = "";
    allNodesData.forEach((node) => {
      if (node.parent_id) {
        const parentNode = allNodesData.find((n) => n.id === node.parent_id);
        if (parentNode) {
          drawLine(parentNode, node);
        }
      }
    });
  }

  function drawLine(parent, child) {
    const nodeWidth = 220;
    const nodeHeight = 80;

    const startX = parseInt(parent.pos_x) + nodeWidth / 2;
    const startY = parseInt(parent.pos_y) + nodeHeight;
    const endX = parseInt(child.pos_x) + nodeWidth / 2;
    const endY = parseInt(child.pos_y);

    const controlY = startY + (endY - startY) / 2;
    const pathData = `M ${startX} ${startY} C ${startX} ${controlY}, ${endX} ${controlY}, ${endX} ${endY}`;

    const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
    path.setAttribute("d", pathData);
    path.setAttribute("stroke", "#1a73e8");
    path.setAttribute("stroke-width", "2");
    path.setAttribute("fill", "none");

    svgCanvas.appendChild(path);
  }

  // 6. Properties form elements filling and Node Highlighting
  function openProperties(node) {
    activeNode = node;

    // Step A: Pehle se agar koi node selected hai, toh uski highlight hatao
    canvas.querySelectorAll(".node-card").forEach((nc) => {
      nc.classList.remove("selected");
    });

    // Step B: Ab jo node click hua hai, usko HTML me dhoodh kar '.selected' class de do
    const currentSelectedElement = document.getElementById(node.id);
    if (currentSelectedElement) {
      currentSelectedElement.classList.add("selected");
    }

    // Step C: Properties form me data fill karo
    document.getElementById("prop-id").value = node.id;
    document.getElementById("prop-name").value = node.node_name;
    document.getElementById("prop-type").value = node.node_type;
    document.getElementById("prop-limit").value = node.headcount_limit;
    document.getElementById("prop-status").value = node.status;
  }

  // Save Changes Button Event Listener
  document.getElementById("btn-save").addEventListener("click", () => {
    if (!activeNode) return;
    activeNode.node_name = document.getElementById("prop-name").value;
    activeNode.node_type = document.getElementById("prop-type").value; // Sahi jagah merge kiya
    activeNode.headcount_limit = document.getElementById("prop-limit").value;
    activeNode.status = document.getElementById("prop-status").value;

    updateNodeInDB(activeNode).then(() => loadNodes());
  });

  // Create New Node Button Event Listener
  document
    .getElementById("btn-create-node")
    .addEventListener("click", async () => {
      const randomId = "TM-" + Math.floor(100 + Math.random() * 900);
      const parentId = activeNode ? activeNode.id : null;

      const payload = {
        id: randomId,
        node_name: "New Team Group",
        node_type: "TEAM",
        parent_id: parentId,
        headcount_limit: 100,
        current_headcount: 0,
        status: "Active",
        pos_x: activeNode ? parseInt(activeNode.pos_x) + 50 : 200,
        pos_y: activeNode ? parseInt(activeNode.pos_y) + 150 : 200,
      };

      await fetch("http://localhost/sams/api.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });
      loadNodes();
    });
  // Canvas ki khali jagah par click karne se highlight hatane ke liye
  canvas.addEventListener("click", (e) => {
    // Agar click direct canvas par hua hai (kisi node card par nahi)
    if (e.target === canvas || e.target === svgCanvas) {
      activeNode = null;
      canvas
        .querySelectorAll(".node-card")
        .forEach((nc) => nc.classList.remove("selected"));
      document.getElementById("properties-form").reset(); // Form clear karne ke liye
    }
  });
  // Initial Load
  loadNodes();
});
