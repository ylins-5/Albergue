const modalLogin = document.getElementById('modalLogin');
const abrirLoginBtn = document.getElementById('abrirLoginBtn'); 
const fecharLoginBtn = document.getElementById('fecharLoginBtn'); 

const modalCadastro = document.getElementById('modalCadastro');
const abrirCadastroNavBtn = document.getElementById('abrirCadastroNavBtn'); 
const fecharCadastroBtn = document.getElementById('fecharCadastroBtn'); 

const irParaCadastroBtn = document.getElementById('irParaCadastroBtn');
const irParaLoginBtn = document.getElementById('irParaLoginBtn');

abrirLoginBtn.addEventListener('click', () => {
    modalLogin.classList.add('ativo');
    modalCadastro.classList.remove('ativo'); // Garante que o outro feche
});

abrirCadastroNavBtn.addEventListener('click', () => {
    modalCadastro.classList.add('ativo');
    modalLogin.classList.remove('ativo');
});


fecharLoginBtn.addEventListener('click', () => {
    modalLogin.classList.remove('ativo');
});

fecharCadastroBtn.addEventListener('click', () => {
    modalCadastro.classList.remove('ativo');
});

irParaCadastroBtn.addEventListener('click', () => {
    modalLogin.classList.remove('ativo');
    modalCadastro.classList.add('ativo');
});

irParaLoginBtn.addEventListener('click', () => {
    modalCadastro.classList.remove('ativo');
    modalLogin.classList.add('ativo');
});

modalLogin.addEventListener('click', (evento) => {
    if (evento.target === modalLogin) {
        modalLogin.classList.remove('ativo');
    }
});

modalCadastro.addEventListener('click', (evento) => {
    if (evento.target === modalCadastro) {
        modalCadastro.classList.remove('ativo');
    }
});