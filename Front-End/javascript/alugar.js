document.addEventListener("DOMContentLoaded", async () => {
  const API_BASE_URL = "http://localhost/albergue/public";
  const containerLista = document.getElementById("lista-quartos-alugar");
  const loadingMsg = document.getElementById("loading-msg");

  let usuarioLogadoId = null;

  // Verificar Sessão
  try {
    const resp = await fetch(`${API_BASE_URL}/session`);
    if (resp.ok) {
      const data = await resp.json();
      if (data.authenticated) usuarioLogadoId = data.user.id;
    }
  } catch (e) {}

  // Carregar Quartos
  async function carregarQuartosComReserva() {
    try {
      const res = await fetch(`${API_BASE_URL}/quartos`);
      if (!res.ok) throw new Error("Erro ao buscar quartos");
      const quartos = await res.json();

      loadingMsg.style.display = "none";

      if (quartos.length === 0) {
        containerLista.innerHTML =
          "<p style='color:white; text-align:center;'>Nenhum quarto disponível.</p>";
        return;
      }

      const promises = quartos.map(async (quarto) => {
        let camasHtml = '<option value="">Sem camas disponíveis</option>';
        let temCamas = false;

        try {
          const resCamas = await fetch(
            `${API_BASE_URL}/quartos/${quarto.id}/camas`
          );
          if (resCamas.ok) {
            const camas = await resCamas.json();
            if (camas.length > 0) {
              temCamas = true;
              camasHtml = '<option value="">Selecione uma cama...</option>';
              camas.forEach((cama) => {
                camasHtml += `<option value="${cama.id}">Cama ${cama.numero}</option>`;
              });
            }
          }
        } catch (e) {}

        let imagemSrc = "../img/quartos/quarto1.jpg";
        if (quarto.imagem) {
          imagemSrc =
            quarto.imagem.startsWith("http") || quarto.imagem.startsWith("../")
              ? quarto.imagem
              : `../${quarto.imagem}`;
        }

        const preco = parseFloat(quarto.preco) || 70.0;
        const disabled = !temCamas ? "disabled" : "";
        const textoBotao = temCamas ? "RESERVAR" : "INDISPONÍVEL";

        return `
                <div class="card-reserva" id="card-quarto-${quarto.id}">
                    <div class="card-img-wrapper">
                        <img src="${imagemSrc}" alt="${
          quarto.numero
        }" onerror="this.src='../img/quartos/quarto1.jpg'">
                    </div>
                    <div class="card-content">
                        <h3>${quarto.numero}</h3>
                        <p class="preco-destaque" data-preco="${preco}">R$ ${preco.toFixed(
          2
        )} <small>/ dia</small></p>
                        <p class="descricao-curta">${quarto.descricao || ""}</p>
                        
                        <div class="form-reserva-inline">
                            <div>
                                <label>Check-in</label>
                                <input type="date" class="input-checkin" data-id="${
                                  quarto.id
                                }">
                            </div>
                            <div>
                                <label>Check-out</label>
                                <input type="date" class="input-checkout" data-id="${
                                  quarto.id
                                }">
                            </div>
                            
                            <div>
                                <label>Cama</label>
                                <select class="select-cama" id="cama-${
                                  quarto.id
                                }" ${disabled}>
                                    ${camasHtml}
                                </select>
                            </div>

                            <div class="resumo-box">
                                Total: <span id="total-${
                                  quarto.id
                                }">R$ 0,00</span>
                            </div>

                            <button class="btn-reservar" onclick="confirmarReserva(${
                              quarto.id
                            })" ${disabled}>
                                ${textoBotao}
                            </button>
                        </div>
                    </div>
                </div>
                `;
      });

      const cardsHtml = await Promise.all(promises);
      containerLista.innerHTML = cardsHtml.join("");

      ativarCalculos();
    } catch (err) {
      console.error(err);
      loadingMsg.textContent = "Erro ao carregar lista.";
      loadingMsg.style.color = "white";
    }
  }

  function ativarCalculos() {
    const inputs = document.querySelectorAll(".input-checkin, .input-checkout");
    inputs.forEach((input) => {
      input.addEventListener("change", (e) => {
        const idQuarto = e.target.getAttribute("data-id");
        calcularTotal(idQuarto);
      });
    });
  }

  function calcularTotal(id) {
    const card = document.getElementById(`card-quarto-${id}`);
    const inDate = card.querySelector(".input-checkin").value;
    const outDate = card.querySelector(".input-checkout").value;
    const totalSpan = document.getElementById(`total-${id}`);
    const precoDia = parseFloat(
      card.querySelector(".preco-destaque").getAttribute("data-preco")
    );

    if (inDate && outDate) {
      const d1 = new Date(inDate);
      const d2 = new Date(outDate);

      if (d2 > d1) {
        const diffTime = Math.abs(d2 - d1);
        const dias = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        const total = dias * precoDia;
        totalSpan.textContent = `R$ ${total
          .toFixed(2)
          .replace(".", ",")} (${dias}d)`;
        totalSpan.style.color = "#333";
      } else {
        totalSpan.textContent = "Data Inválida";
        totalSpan.style.color = "red";
      }
    }
  }

  window.confirmarReserva = async (id) => {
    if (!usuarioLogadoId) {
      alert("Faça login para reservar.");
      const modal = document.getElementById("modalLogin");
      if (modal) modal.classList.add("ativo");
      return;
    }

    const card = document.getElementById(`card-quarto-${id}`);
    const camaId = card.querySelector(".select-cama").value;
    const inDate = card.querySelector(".input-checkin").value;
    const outDate = card.querySelector(".input-checkout").value;
    const totalTexto = document.getElementById(`total-${id}`).textContent;

    if (!camaId) return alert("Selecione uma cama.");
    if (!inDate || !outDate) return alert("Selecione as datas.");
    if (totalTexto.includes("Inválida")) return alert("Datas inválidas.");

    const payload = {
      quarto_id: id,
      bed_id: camaId,
      data_inicio: inDate,
      data_fim: outDate,
    };

    try {
      const btn = card.querySelector(".btn-reservar");
      const txtOriginal = btn.textContent;
      btn.textContent = "...";
      btn.disabled = true;

      const res = await fetch(`${API_BASE_URL}/reservas`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });

      const json = await res.json();

      if (res.ok) {
        alert(`Reserva Confirmada!\n${totalTexto}`);
        window.location.href = "home.html";
      } else {
        alert("Erro: " + (json.error || "Não foi possível reservar."));
        btn.textContent = txtOriginal;
        btn.disabled = false;
      }
    } catch (e) {
      console.error(e);
      alert("Erro de conexão.");
    }
  };

  carregarQuartosComReserva();
});
