document.addEventListener("DOMContentLoaded", async () => {
    
    const urlParams = new URLSearchParams(window.location.search);
    const roomId = urlParams.get('room_id');
    const API_BASE_URL = "http://localhost/albergue/public";

    const container = document.getElementById('reserva-container');
    const noRoomMsg = document.getElementById('no-room-msg');
    
    if (!roomId) {
        noRoomMsg.style.display = 'block';
        return;
    }

    const imgEl = document.getElementById('room-img');
    const titleEl = document.getElementById('room-title');
    const priceEl = document.getElementById('room-price');
    const descEl = document.getElementById('room-desc');
    const bedSelect = document.getElementById('bed-select');
    
    const checkinInput = document.getElementById('checkin');
    const checkoutInput = document.getElementById('checkout');
    const totalDaysEl = document.getElementById('total-days');
    const totalValueEl = document.getElementById('total-value');
    const btnConfirmar = document.getElementById('btn-confirmar-reserva');
    const errorMsg = document.getElementById('error-msg');

    let roomPriceValue = 0;

    try {
        const res = await fetch(`${API_BASE_URL}/quartos/${roomId}`);
        if(!res.ok) throw new Error("Quarto não encontrado");
        
        const quarto = await res.json();

        container.style.display = 'flex';
        titleEl.textContent = quarto.numero;
        descEl.textContent = quarto.descricao;
        imgEl.src = quarto.imagem ? `../${quarto.imagem}` : '../img/quartos/quarto1.jpg';
        
        roomPriceValue = quarto.preco || 70.00; 
        priceEl.textContent = `R$ ${roomPriceValue.toFixed(2)}`;

    } catch (err) {
        console.error(err);
        noRoomMsg.style.display = 'block';
        noRoomMsg.querySelector('h2').textContent = "Erro ao carregar quarto.";
        return;
    }

    try {
        const resBeds = await fetch(`${API_BASE_URL}/quartos/${roomId}/camas`);
        const beds = await resBeds.json();

        bedSelect.innerHTML = '<option value="">Selecione uma cama...</option>';
        
        if(beds.length > 0) {
            beds.forEach(bed => {
                bedSelect.innerHTML += `<option value="${bed.id}">Cama ${bed.numero}</option>`;
            });
        } else {
            bedSelect.innerHTML = '<option value="">Sem camas cadastradas</option>';
        }

    } catch (err) {
        console.error("Erro ao carregar camas", err);
    }

    function atualizarCalculos() {
        errorMsg.textContent = "";
        
        const inDate = checkinInput.value;
        const outDate = checkoutInput.value;

        if (!inDate || !outDate) return;

        const d1 = new Date(inDate);
        const d2 = new Date(outDate);

        if (d2 <= d1) {
            errorMsg.textContent = "Data de saída deve ser depois da entrada.";
            totalDaysEl.textContent = "0";
            totalValueEl.textContent = "R$ 0,00";
            return;
        }

        const diffTime = Math.abs(d2 - d1);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        totalDaysEl.textContent = diffDays;
        
        const total = diffDays * roomPriceValue;
        totalValueEl.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
    }

    checkinInput.addEventListener('change', atualizarCalculos);
    checkoutInput.addEventListener('change', atualizarCalculos);

    btnConfirmar.addEventListener('click', async () => {
        
        let usuarioId = null;
        try {
            const resp = await fetch(`${API_BASE_URL}/session`);
            if (resp.ok) {
                const data = await resp.json();
                if (data.authenticated) usuarioId = data.user.id;
            }
        } catch(e){}

        if(!usuarioId) {
            alert("Faça login para continuar.");
            const modalLogin = document.getElementById("modalLogin");
            if(modalLogin) modalLogin.classList.add("ativo");
            return;
        }

        const bedId = bedSelect.value;
        const dataInicio = checkinInput.value;
        const dataFim = checkoutInput.value;

        if(!bedId) { alert("Selecione uma cama!"); return; }
        if(!dataInicio || !dataFim) { alert("Selecione as datas!"); return; }
        if(errorMsg.textContent !== "") { alert("Corrija as datas!"); return; }

        const payload = {
            quarto_id: roomId,
            bed_id: bedId,
            data_inicio: dataInicio,
            data_fim: dataFim
        };

        try {
            const res = await fetch(`${API_BASE_URL}/reservas`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const json = await res.json();

            if(res.ok) {
                alert("Reserva realizada com sucesso!");
                window.location.href = "home.html";
            } else {
                alert("Erro: " + (json.error || "Falha ao reservar"));
            }

        } catch(e) {
            console.error(e);
            alert("Erro de conexão.");
        }
    });

});