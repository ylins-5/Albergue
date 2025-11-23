document.addEventListener("DOMContentLoaded", () => {

  const containers = document.querySelectorAll(".container");

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
        const date1 = new Date(checkin + "T00:00:00"); // Adiciona T00 para evitar problemas de fuso horário
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

    alugarBtn.addEventListener("click", (e) => {
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

      alert(`
                        ✅ Reserva Confirmada!
                        ------------------------------------
                        Quarto: ${container.querySelector("h1").textContent}
                        Diárias: ${numDiarias}
                        Vagas: ${vagas}
                        Período: ${checkinInput.value} até ${
        checkoutInput.value
      }
                        Valor Total Estimado: R$${precoTotal}
                        ------------------------------------
                        Prosseguindo para o pagamento...
                    `);
    });
  });
});
