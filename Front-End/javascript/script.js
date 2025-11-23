const abrirModalBtn = document.getElementById('abrirModalBtn');
const fecharModalBtn = document.getElementById('fecharModalBtn');
const modalOverlay = document.getElementById('modalOverlay');

abrirModalBtn.addEventListener('click', () => {
    modalOverlay.classList.add('ativo');
});

fecharModalBtn.addEventListener('click', () => {
    modalOverlay.classList.remove('ativo');
});


modalOverlay.addEventListener('click', (evento) => {
    if (evento.target === modalOverlay) {
        modalOverlay.classList.remove('ativo');
    }
});