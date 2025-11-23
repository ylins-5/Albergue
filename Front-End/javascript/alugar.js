document.addEventListener("DOMContentLoaded", () => {

  const containers = document.querySelectorAll(".container");
  const API_BASE_URL = "http://localhost/albergue/public";

  containers.forEach((container, index) => {

    const roomNumber = index + 1;
    const checkinInput = container.querySelector(`#checkin${roomNumber}`);
    const checkoutInput = container.querySelector(`#checkout${roomNumber}`);
    const diariasSelect = container.querySelector(`#diarias${roomNumber}`);
    const alugarBtn = container.querySelector(".alugar-btn");
    const precoElement = container.querySelector(".preco");
    const precoDiaria = parseFloat(
      precoElement.textContent.replace("R$", "").replace(",", ".").trim()
    );

    const calcularDiarias = () => {
      const checkin = checkinInput.value;
      const checkout = checkoutInput.value;

      if (checkin && checkout) {
        const date1 = new Date(checkin + "T00:00:00");
        const date2 = new Date(checkout + "T00:00:00");

        if (date2 <= date1) {
          alert("A data de Check-out deve ser posterior à data de Check-in.");
          checkoutInput.value = ""; 
          diariasSelect.value = "1"; 
          return;
        }

        const diffTime = Math.abs(date2 - date1);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        if (diariasSelect.querySelector(`option[value="${diffDays}"]`)) {
          diariasSelect.value = diffDays;
        } else {
          diariasSelect.value = diariasSelect.lastElementChild.value;
        }
      }
    };

    checkinInput.addEventListener("change", calcularDiarias);
    checkoutInput.addEventListener("change", calcularDiarias);

    alugarBtn.addEventListener("click", async (e) => {
      
      let usuarioId = null;
      try {
          const resp = await fetch(`${API_BASE_URL}/session`);
          if (resp.ok) {
              const data = await resp.json();
              if (data.authenticated) usuarioId = data.user.id;
          }
      } catch(err) {}

      if (!usuarioId) {
          alert("Você precisa estar logado para alugar!");
          const modalLogin = document.getElementById("modalLogin");
          if(modalLogin) modalLogin.classList.add("ativo");
          return;
      }

      const vagasSelect = container.querySelector(
        ".form-row:nth-of-type(2) .select-group:nth-child(2) select"
      );

      if (!checkinInput.value || !checkoutInput.value) {
        e.preventDefault(); 
        alert("Por favor, selecione as datas de Check-in e Check-out.");
        return;
      }

      if (vagasSelect.value === "Selecione") {
        e.preventDefault();
        alert("Por favor, selecione o número de camas/vagas.");
        return;
      }

      const numDiarias = parseInt(diariasSelect.value);
      const vagas = vagasSelect.value;
      const precoTotal = (precoDiaria * numDiarias)
        .toFixed(2)
        .replace(".", ",");
      const quartoId = alugarBtn.getAttribute('data-room');

      const reservaData = {
          quarto_id: quartoId,
          data_entrada: checkinInput.value,
          data_saida: checkoutInput.value,
          vagas: vagas,
          valor_total: precoDiaria * numDiarias
      };

      try {
          const response = await fetch(`${API_BASE_URL}/reservas`, {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify(reservaData)
          });

          const result = await response.json();

          if (response.ok) {
              alert(`
                ✅ Reserva Confirmada!
                ------------------------------------
                Quarto: ${container.querySelector("h1").textContent}
                Diárias: ${numDiarias}
                Vagas: ${vagas}
                Período: ${checkinInput.value} até ${checkoutInput.value}
                Valor Total Estimado: R$${precoTotal}
                ------------------------------------
                Redirecionando...
              `);
              window.location.href = "home.html";
          } else {
              alert("Erro na reserva: " + (result.error || "Erro desconhecido"));
          }
      } catch (error) {
          alert("Erro de conexão.");
      }
    });
  });
});