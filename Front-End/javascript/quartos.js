let editId = null;

/* ---------------------------- CARREGAR QUARTOS ---------------------------- */
async function loadRooms() {
    const res = await fetch('http://localhost/albergue/public/quartos');
    const data = await res.json();
    const list = document.getElementById('room-list');
    list.innerHTML = '';

    data.forEach(room => {
        list.innerHTML += `
            <div class='room-card'>
                <h3>${room.numero}</h3>
                <p>${room.descricao}</p>

                <button class='edit-btn' onclick="openModalEdit(${room.id})">
                    <i class="fas fa-edit"></i> Editar
                </button>

                <button class='beds-btn' onclick="goToBeds(${room.id})">
    <i class="fas fa-bed"></i> Camas
</button>

                <button class='delete-btn' onclick="deleteRoom(${room.id})">
                    <i class="fas fa-trash"></i> Excluir
                </button>
            </div>
        `;
    });
}

/* ---------------------------- ABRIR / FECHAR MODAIS ---------------------------- */
function openModalCreate() {
    document.getElementById('numeroCreate').value = '';
    document.getElementById('descricaoCreate').value = '';
    document.getElementById('imagemCreate').value = '';
    document.getElementById('modalCreate').style.display = 'flex';
}

function openModalEdit(id) {
    editId = id;
    fetch(`http://localhost/albergue/public/quartos/${id}`)
        .then(res => res.json())
        .then(room => {
            document.getElementById('numeroEdit').value = room.numero;
            document.getElementById('descricaoEdit').value = room.descricao;
            document.getElementById('modalEdit').style.display = 'flex';
        });
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

/* ---------------------------- CRIAR QUARTO ---------------------------- */
async function saveRoom() {
    const numero = document.getElementById('numeroCreate').value;
    const descricao = document.getElementById('descricaoCreate').value;
    const imagem = document.getElementById('imagemCreate').files[0];

    const form = new FormData();
    form.append('numero', numero);
    form.append('descricao', descricao);
    if (imagem) form.append('imagem', imagem);

    await fetch('http://localhost/albergue/public/quartos', {
        method: 'POST',
        body: form
    });

    closeModal('modalCreate');
    loadRooms();
}

/* ---------------------------- EDITAR QUARTO ---------------------------- */
async function updateRoom() {
    const numero = document.getElementById('numeroEdit').value;
    const descricao = document.getElementById('descricaoEdit').value;

    await fetch(`http://localhost/albergue/public/quartos/${editId}`, {
        method: 'PUT',
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ numero, descricao })
    });

    closeModal('modalEdit');
    loadRooms();
}

/* ---------------------------- DELETAR ---------------------------- */
async function deleteRoom(id) {
    await fetch(`http://localhost/albergue/public/quartos/${id}`, {
        method: 'DELETE'
    });
    loadRooms();
}

function goToBeds(id) {
    window.location.href = `camas.html?id_quarto=${id}`;
}


loadRooms();