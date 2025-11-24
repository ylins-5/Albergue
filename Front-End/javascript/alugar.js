document.addEventListener("DOMContentLoaded", async () => {
    const API_BASE_URL = "http://localhost/albergue/public";
    const container = document.getElementById('reserva-container');
    const noRoomMsg = document.getElementById('no-room-msg');

    // Mostrar loading
    container.style.display = "block";
    container.innerHTML = "<p>Carregando quartos disponíveis...</p>";

    try {
        // 1. Primeiro busca os quartos
        const res = await fetch(`${API_BASE_URL}/quartos`);
        if (!res.ok) throw new Error("Não foi possível carregar os quartos.");
        const quartos = await res.json();

        if (quartos.length === 0) {
            noRoomMsg.style.display = "block";
            noRoomMsg.querySelector("h2").textContent = "Nenhum quarto encontrado.";
            return;
        }

        // 2. Define período para busca (próximos 30 dias como exemplo)
        const hoje = new Date();
        const dataInicio = hoje.toISOString().split('T')[0]; // YYYY-MM-DD
        const dataFim = new Date(hoje.setDate(hoje.getDate() + 30)).toISOString().split('T')[0];

        // 3. Busca camas disponíveis para o período
        let camasDisponiveis = [];
        try {
            const disponiveisRes = await fetch(`${API_BASE_URL}/reservas/disponiveis?inicio=${dataInicio}&fim=${dataFim}`);
            if (disponiveisRes.ok) {
                camasDisponiveis = await disponiveisRes.json();
            }
        } catch (e) {
            console.warn("Erro ao buscar camas disponíveis:", e);
        }

        // 4. Processa cada quarto
        container.innerHTML = ""; // Limpa loading

        for (const quarto of quartos) {
            try {
                // Busca todas as camas deste quarto
                const bedsRes = await fetch(`${API_BASE_URL}/quartos/${quarto.id}/camas`);
                if (!bedsRes.ok) continue;

                const todasCamasQuarto = await bedsRes.json();
                
                // Filtra apenas as camas disponíveis deste quarto
                const camasDisponiveisNoQuarto = todasCamasQuarto.filter(cama => 
                    camasDisponiveis.some(disponivel => disponivel.id === cama.id)
                );

                const camasLivres = camasDisponiveisNoQuarto.length;
                const totalCamas = todasCamasQuarto.length;

                // Cria o card do quarto
                const bloco = document.createElement("div");
                bloco.classList.add("quarto-card");

                // Define cor do badge baseado na disponibilidade
                let badgeClass = "badge-camas ";
                if (camasLivres === 0) {
                    badgeClass += "indisponivel";
                } else if (camasLivres <= totalCamas * 0.3) {
                    badgeClass += "poucas-vagas";
                } else {
                    badgeClass += "disponivel";
                }

                bloco.innerHTML = `
                    <span class="${badgeClass}">
                        ${camasLivres}/${totalCamas} ${camasLivres === 1 ? 'cama livre' : 'camas livres'}
                    </span>

                    <div class="image-area">
                        <img src="${quarto.imagem || 'default-room.jpg'}" alt="Foto do quarto ${quarto.numero}" 
                             onerror="this.src='default-room.jpg'">
                    </div>

                    <div class="info-area">
                        <h1>Quarto ${quarto.numero}</h1>
                        <p class="detalhes">${quarto.descricao || 'Sem descrição'}</p>

                        <h2 class="preco">R$ ${(quarto.preco ? quarto.preco.toFixed(2) : "70.00")}</h2>

                        ${camasLivres > 0 ? 
                            `<a href="alugar.html?room_id=${quarto.id}" 
                                class="alugar-btn" 
                                style="margin-top: 15px; display: inline-block; text-decoration: none;">
                                Reservar Agora
                            </a>` :
                            `<button class="btn-indisponivel" disabled style="margin-top: 15px; display: inline-block; 
                                background: #ccc; color: #666; padding: 10px 20px; border: none; border-radius: 5px; cursor: not-allowed;">
                                Indisponível
                            </button>`
                        }
                    </div>
                `;

                container.appendChild(bloco);

            } catch (error) {
                console.error(`Erro ao processar quarto ${quarto.id}:`, error);
                // Cria card básico em caso de erro
                const blocoErro = document.createElement("div");
                blocoErro.classList.add("quarto-card", "erro");
                blocoErro.innerHTML = `
                    <span class="badge-camas indisponivel">Erro</span>
                    <div class="info-area">
                        <h1>Quarto ${quarto.numero}</h1>
                        <p class="detalhes">Erro ao carregar informações</p>
                        <button class="btn-indisponivel" disabled>Indisponível</button>
                    </div>
                `;
                container.appendChild(blocoErro);
            }
        }

        // Se nenhum quarto foi carregado com sucesso
        if (container.children.length === 0) {
            throw new Error("Não foi possível carregar nenhum quarto.");
        }

    } catch (err) {
        console.error("Erro geral:", err);
        noRoomMsg.style.display = 'block';
        noRoomMsg.querySelector('h2').textContent = "Erro ao carregar lista de quartos.";
        container.style.display = "none";
    }
});