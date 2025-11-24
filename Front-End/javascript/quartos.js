const API_BASE_URL = "http://localhost/albergue/public";

async function carregarQuartos() {
  const container = document.getElementById("lista-quartos");
  if (!container) return;

  try {
    const resposta = await fetch(`${API_BASE_URL}/quartos`);
    const quartos = await resposta.json();

    container.innerHTML = "";

    if (quartos.length === 0) {
      container.innerHTML =
        "<p style='text-align:center; width:100%'>Nenhum quarto encontrado.</p>";
      return;
    }

    quartos.forEach((q, index) => {
      const reverso = index % 2 !== 0 ? "reverso" : "quarto-item";

      const bloco = `
            <div class="${reverso}">
                <div class="quarto-imagem">
                    <img src="${q.imagem}" alt="${q.numero}">
                </div>
                <div class="quarto-descricao">
                    <h3>${q.numero}</h3>
                    <p>${q.descricao}</p>
                    <button class="btn" onclick="window.location.href='alugar.html?room_id=${
                      q.id
                    }'">
                        Alugar
                    </button>
                </div>
            </div>`;
      container.innerHTML += bloco;
    });
  } catch (e) {
    console.error(e);
  }
}

carregarQuartos();
