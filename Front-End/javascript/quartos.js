document.addEventListener("DOMContentLoaded", () => {
  const roomBlocksContainer = document.getElementById("roomBlocksContainer");
  const addRoomBlockBtn = document.getElementById("addRoomBlockBtn");
  let roomIndex = roomBlocksContainer.children.length; 

  function updateElementIndices(clone, index) {
    clone.setAttribute("data-room-index", index);

    clone.querySelectorAll("[name], [id]").forEach((input) => {
      const baseAttribute = input
        .getAttribute(input.name ? "name" : "id")
        .replace(/\d+$/, "");
      const newAttribute = baseAttribute + index;

      if (input.name) {
        input.name = newAttribute;
      }
      if (input.id) {
        input.id = newAttribute;
      }

      if (input.tagName === "INPUT" || input.tagName === "TEXTAREA") {
        input.value = "";
      }
    });

    clone.querySelectorAll("label").forEach((label) => {
      if (label.htmlFor && label.htmlFor.startsWith("img")) {
        label.htmlFor = "img" + index;
      }
    });
  }

  function addRoomBlock() {
    roomIndex++;
    const templateBlock = roomBlocksContainer
      .querySelector(".room-block")
      .cloneNode(true);

    updateElementIndices(templateBlock, roomIndex);

    const removeButton = document.createElement("button");
    removeButton.innerHTML = '<i class="fas fa-trash"></i> Remover';
    removeButton.type = "button";
    removeButton.classList.add("remove-room-button");
    removeButton.onclick = function () {
      if (roomBlocksContainer.children.length > 1) {
        templateBlock.remove();
      } else {
        alert("É necessário ter pelo menos um bloco de quarto.");
      }
    };
    templateBlock.appendChild(removeButton);

    roomBlocksContainer.appendChild(templateBlock);
  }

  function addInitialRemoveButton(block) {
    const removeButton = document.createElement("button");
    removeButton.innerHTML = '<i class="fas fa-trash"></i> Remover';
    removeButton.type = "button";
    removeButton.classList.add("remove-room-button");
    removeButton.onclick = function () {
      if (roomBlocksContainer.children.length > 1) {
        block.remove();
      } else {
        alert("É necessário ter pelo menos um bloco de quarto.");
      }
    };
    block.appendChild(removeButton);
  }

  const initialBlock = roomBlocksContainer.querySelector(".room-block");
  if (initialBlock) {
    addInitialRemoveButton(initialBlock);
  }

  addRoomBlockBtn.addEventListener("click", addRoomBlock);
});
