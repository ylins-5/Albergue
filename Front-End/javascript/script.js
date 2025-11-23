const API_BASE_URL = "http://localhost/albergue/public";

const modalLogin = document.getElementById("modalLogin");
const modalCadastro = document.getElementById("modalCadastro");
const abrirLoginBtn = document.getElementById("abrirLoginBtn");
const abrirCadastroNavBtn = document.getElementById("abrirCadastroNavBtn");
const fecharLoginBtn = document.getElementById("fecharLoginBtn");
const fecharCadastroBtn = document.getElementById("fecharCadastroBtn");
const irParaCadastroBtn = document.getElementById("irParaCadastroBtn");
const irParaLoginBtn = document.getElementById("irParaLoginBtn");

const formLogin = document.getElementById("formLogin");
const formCadastro = document.getElementById("formCadastro");

abrirLoginBtn.addEventListener("click", () => {
  modalLogin.classList.add("ativo");
  modalCadastro.classList.remove("ativo");
});

abrirCadastroNavBtn.addEventListener("click", () => {
  modalCadastro.classList.add("ativo");
  modalLogin.classList.remove("ativo");
});

fecharLoginBtn.addEventListener("click", () =>
  modalLogin.classList.remove("ativo")
);
fecharCadastroBtn.addEventListener("click", () =>
  modalCadastro.classList.remove("ativo")
);

irParaCadastroBtn.addEventListener("click", () => {
  modalLogin.classList.remove("ativo");
  modalCadastro.classList.add("ativo");
});

irParaLoginBtn.addEventListener("click", () => {
  modalCadastro.classList.remove("ativo");
  modalLogin.classList.add("ativo");
});

window.addEventListener("click", (e) => {
  if (e.target === modalLogin) modalLogin.classList.remove("ativo");
  if (e.target === modalCadastro) modalCadastro.classList.remove("ativo");
});

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
      alert("Conta criada com sucesso! Faça login.");
      modalCadastro.classList.remove("ativo");
      modalLogin.classList.add("ativo");
      formCadastro.reset();
    } else {
      alert("Erro ao cadastrar: " + (result.error || "Erro desconhecido"));
    }
  } catch (error) {
    console.error("Erro:", error);
    alert("Erro de conexão com o servidor.");
  }
});

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
      alert("Bem-vindo, " + result.user.nome + "!");
      modalLogin.classList.remove("ativo");

      localStorage.setItem("usuario", JSON.stringify(result.user));
    } else {
      alert("Erro no login: " + (result.error || "Credenciais inválidas"));
    }
  } catch (error) {
    console.error("Erro:", error);
    alert("Erro de conexão com o servidor.");
  }
});


async function carregarQuartos() {
    const resposta = await fetch('http://localhost/albergue/public/quartos');
    const quartos = await resposta.json();

    const container = document.getElementById('lista-quartos');
    container.innerHTML = "";

    quartos.forEach((q, index) => {
        
        const reverso = index % 2 !== 0 ? 'reverso' : 'quarto-item';

        const bloco = `
        <div class="${reverso}">
            
            <div class="quarto-imagem">
                <img src="${q.imagem}" alt="${q.numero}">
            </div>

            <div class="quarto-descricao">
                <h3>${q.numero}</h3>
                <p>${q.descricao}</p>
            </div>

        </div>
        `;

        container.innerHTML += bloco;
    });
}
carregarQuartos();
