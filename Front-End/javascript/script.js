const API_BASE_URL = "http://localhost/albergue/public";

const modalLogin = document.getElementById("modalLogin");
const modalCadastro = document.getElementById("modalCadastro");
const modalEditar = document.getElementById("modalEditar");

const abrirLoginBtn = document.getElementById("abrirLoginBtn");
const abrirCadastroNavBtn = document.getElementById("abrirCadastroNavBtn");

const fecharLoginBtn = document.getElementById("fecharLoginBtn");
const fecharCadastroBtn = document.getElementById("fecharCadastroBtn");
const fecharEditarBtn = document.getElementById("fecharEditarBtn");

const irParaCadastroBtn = document.getElementById("irParaCadastroBtn");
const irParaLoginBtn = document.getElementById("irParaLoginBtn");

const formLogin = document.getElementById("formLogin");
const formCadastro = document.getElementById("formCadastro");
const formEditar = document.getElementById("formEditar");

let usuarioLogado = null;
let dadosCompletosUsuario = null;

async function verificarSessao() {
  try {
    const response = await fetch(`${API_BASE_URL}/session`);
    if (response.ok) {
      const data = await response.json();
      if (data.authenticated) {
        usuarioLogado = data.user;
        carregarDadosCompletos(usuarioLogado.id);
        atualizarInterfaceLogado();
      }
    }
  } catch (error) {
    console.log("Usuário não logado");
  }
}

async function carregarDadosCompletos(id) {
  try {
    const res = await fetch(`${API_BASE_URL}/usuarios/${id}`);
    if (res.ok) {
      dadosCompletosUsuario = await res.json();
    }
  } catch (e) {
    console.error("Erro ao buscar detalhes", e);
  }
}

verificarSessao();

function atualizarInterfaceLogado() {
  if (abrirCadastroNavBtn) abrirCadastroNavBtn.style.display = "none";

  let menu = document.getElementById("userDropdown");
  if (!menu) {
    const divContainer = document.createElement("div");
    divContainer.className = "user-menu-container";

    abrirLoginBtn.parentNode.insertBefore(divContainer, abrirLoginBtn);
    divContainer.appendChild(abrirLoginBtn);

    menu = document.createElement("div");
    menu.id = "userDropdown";
    menu.className = "user-dropdown";

    menu.innerHTML = `
            <a class="user-name-display">Olá, ${
              usuarioLogado.nome.split(" ")[0]
            }</a>
            <a href="#" id="btnMeusDados">Meus Dados</a> 
            <a href="#">Minhas Reservas</a>
            <a href="#" id="btnSair">Sair</a>
        `;
    divContainer.appendChild(menu);

    document
      .getElementById("btnSair")
      .addEventListener("click", realizarLogout);
    document
      .getElementById("btnMeusDados")
      .addEventListener("click", abrirModalEdicao);
  }

  const nomeDisplay = menu.querySelector(".user-name-display");
  if (nomeDisplay)
    nomeDisplay.textContent = `Olá, ${usuarioLogado.nome.split(" ")[0]}`;

  const novoIcone = abrirLoginBtn.cloneNode(true);
  abrirLoginBtn.parentNode.replaceChild(novoIcone, abrirLoginBtn);

  novoIcone.addEventListener("click", (e) => {
    e.stopPropagation();
    document.getElementById("userDropdown").classList.toggle("show-menu");
  });

  window.addEventListener("click", () => {
    const d = document.getElementById("userDropdown");
    if (d) d.classList.remove("show-menu");
  });
}

function abrirModalEdicao(e) {
  e.preventDefault();
  if (!dadosCompletosUsuario) return;

  document.getElementById("nomeEditar").value = dadosCompletosUsuario.nome;
  document.getElementById("emailEditar").value = dadosCompletosUsuario.email;
  document.getElementById("docEditar").value = dadosCompletosUsuario.documento;
  document.getElementById("senhaEditar").value = "";

  modalEditar.classList.add("ativo");
}

async function realizarLogout() {
  await fetch(`${API_BASE_URL}/logout`, { method: "POST" });
  location.reload();
}

if (abrirLoginBtn) {
  abrirLoginBtn.addEventListener("click", () => {
    if (!usuarioLogado) {
      modalLogin.classList.add("ativo");
      modalCadastro.classList.remove("ativo");
    }
  });
}

if (abrirCadastroNavBtn) {
  abrirCadastroNavBtn.addEventListener("click", () => {
    modalCadastro.classList.add("ativo");
    modalLogin.classList.remove("ativo");
  });
}

if (fecharLoginBtn)
  fecharLoginBtn.addEventListener("click", () =>
    modalLogin.classList.remove("ativo")
  );
if (fecharCadastroBtn)
  fecharCadastroBtn.addEventListener("click", () =>
    modalCadastro.classList.remove("ativo")
  );
if (fecharEditarBtn)
  fecharEditarBtn.addEventListener("click", () =>
    modalEditar.classList.remove("ativo")
  );

if (irParaCadastroBtn) {
  irParaCadastroBtn.addEventListener("click", () => {
    modalLogin.classList.remove("ativo");
    modalCadastro.classList.add("ativo");
  });
}

if (irParaLoginBtn) {
  irParaLoginBtn.addEventListener("click", () => {
    modalCadastro.classList.remove("ativo");
    modalLogin.classList.add("ativo");
  });
}

window.addEventListener("click", (e) => {
  if (e.target === modalLogin) modalLogin.classList.remove("ativo");
  if (e.target === modalCadastro) modalCadastro.classList.remove("ativo");
  if (e.target === modalEditar) modalEditar.classList.remove("ativo");
});

if (formCadastro) {
  formCadastro.addEventListener("submit", async (e) => {
    e.preventDefault();
    const dados = {
      nome: document.getElementById("nomeCadastro").value,
      documento: document.getElementById("docCadastro").value,
      email: document.getElementById("emailCadastro").value,
      senha: document.getElementById("senhaCadastro").value,
    };

    try {
      const response = await fetch(`${API_BASE_URL}/usuarios`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(dados),
      });
      const result = await response.json();

      if (response.ok) {
        alert("Conta criada! Faça login.");
        modalCadastro.classList.remove("ativo");
        modalLogin.classList.add("ativo");
        formCadastro.reset();
      } else {
        alert("Erro: " + (result.error || "Erro desconhecido"));
      }
    } catch (error) {
      console.error(error);
    }
  });
}

if (formLogin) {
  formLogin.addEventListener("submit", async (e) => {
    e.preventDefault();
    const dados = {
      email: document.getElementById("emailLogin").value,
      senha: document.getElementById("senhaLogin").value,
    };

    try {
      const response = await fetch(`${API_BASE_URL}/login`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(dados),
      });
      const result = await response.json();

      if (response.ok) {
        modalLogin.classList.remove("ativo");
        localStorage.setItem("usuario", JSON.stringify(result.user));
        location.reload();
      } else {
        alert("Erro: " + (result.error || "Credenciais inválidas"));
      }
    } catch (error) {
      console.error(error);
    }
  });
}

if (formEditar) {
  formEditar.addEventListener("submit", async (e) => {
    e.preventDefault();

    const dados = {
      nome: document.getElementById("nomeEditar").value,
      email: document.getElementById("emailEditar").value,
      documento: document.getElementById("docEditar").value,
      senha: document.getElementById("senhaEditar").value,
    };

    try {
      const response = await fetch(
        `${API_BASE_URL}/usuarios/${usuarioLogado.id}`,
        {
          method: "PUT",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(dados),
        }
      );

      const result = await response.json();

      if (response.ok) {
        alert("Dados atualizados com sucesso!");
        modalEditar.classList.remove("ativo");

        usuarioLogado.nome = result.nome;
        usuarioLogado.email = result.email;
        dadosCompletosUsuario = result;

        atualizarInterfaceLogado();
      } else {
        alert("Erro ao atualizar: " + (result.error || "Erro desconhecido"));
      }
    } catch (error) {
      console.error("Erro:", error);
      alert("Erro de conexão.");
    }
  });
}

async function carregarQuartos() {
  const container = document.getElementById("lista-quartos");
  if (!container) return;

  try {
    const resposta = await fetch(`${API_BASE_URL}/quartos`);
    const quartos = await resposta.json();

    container.innerHTML = "";
    quartos.forEach((q, index) => {
      const reverso = index % 2 !== 0 ? "reverso" : "quarto-item";
      const bloco = `
            <div class="${reverso}">
                <div class="quarto-imagem"><img src="${q.imagem}" alt="${q.numero}"></div>
                <div class="quarto-descricao">
                    <h3>${q.numero}</h3>
                    <p>${q.descricao}</p>
                    <a href="alugar.html?room_id=${q.id}" class="btn" style="margin-top:10px; display:inline-block; text-align:center;">Reservar Agora</a>
                </div>
            </div>`;
      container.innerHTML += bloco;
    });
  } catch (e) {
    console.error("Erro ao carregar quartos na home:", e);
  }
}
carregarQuartos();
