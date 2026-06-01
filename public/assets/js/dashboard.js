// Verificar empréstimos atrasados
fetch("/api/verificar-atrasos").then(r => r.json());

// Carregar estatísticas
fetch("/api/estatisticas")
    .then(r => r.json())
    .then(res => {
        if (!res.sucesso) return;
        const d = res.dados;
        document.getElementById("total-alunos").textContent      = d.alunos;
        document.getElementById("total-livros").textContent      = d.livros;
        document.getElementById("total-emprestimos").textContent = d.emprestimos;
        document.getElementById("total-entradas").textContent    = d.entradas;
    });

// Cores padrão
const cores = [
    "#2E5496", "#4472C4", "#70AD47", "#ED7D31",
    "#FFC000", "#FF0000", "#7030A0", "#00B0F0"
];

// ─── GRÁFICO 1 — Empréstimos Mensais (Linha) ──────────────────
fetch("/api/graficos/emprestimos-mensais")
    .then(r => r.json())
    .then(res => {
        const dados  = res.dados || [];
        const labels = dados.map(d => {
            const [ano, mes] = d.mes.split("-");
            return `${mes}/${ano}`;
        });
        const valores = dados.map(d => d.total);

        new Chart(document.getElementById("grafico-emprestimos-mensais"), {
            type: "line",
            data: {
                labels,
                datasets: [{
                    label: "Empréstimos",
                    data: valores,
                    borderColor: "#2E5496",
                    backgroundColor: "rgba(46,84,150,0.1)",
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: "#2E5496",
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    });

// ─── GRÁFICO 2 — Cursos com Mais Frequência (Barras) ─────────
fetch("/api/graficos/cursos-frequentes")
    .then(r => r.json())
    .then(res => {
        const dados   = res.dados || [];
        const labels  = dados.map(d => d.curso);
        const valores = dados.map(d => d.total);

        new Chart(document.getElementById("grafico-cursos"), {
            type: "bar",
            data: {
                labels,
                datasets: [{
                    label: "Entradas",
                    data: valores,
                    backgroundColor: cores.slice(0, labels.length),
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { ticks: { font: { size: 10 } } }
                }
            }
        });
    });

// ─── GRÁFICO 3 — Categorias Mais Solicitadas (Doughnut) ──────
fetch("/api/graficos/categorias-emprestadas")
    .then(r => r.json())
    .then(res => {
        const dados   = res.dados || [];
        const labels  = dados.map(d => d.categoria);
        const valores = dados.map(d => d.total);

        new Chart(document.getElementById("grafico-categorias"), {
            type: "doughnut",
            data: {
                labels,
                datasets: [{
                    data: valores,
                    backgroundColor: cores.slice(0, labels.length),
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: "bottom",
                        labels: { font: { size: 11 }, boxWidth: 12 }
                    }
                }
            }
        });
    });

// ─── GRÁFICO 4 — Fluxo por Hora (Barras) ─────────────────────
fetch("/api/graficos/fluxo-horario")
    .then(r => r.json())
    .then(res => {
        const dados = res.dados || [];

        // Criar array com todas as horas 0-23
        const todasHoras = Array.from({length: 24}, (_, i) => i);
        const mapa       = Object.fromEntries(dados.map(d => [parseInt(d.hora), d.total]));
        const valores    = todasHoras.map(h => mapa[h] || 0);
        const labels     = todasHoras.map(h => `${String(h).padStart(2,"0")}h`);

        new Chart(document.getElementById("grafico-fluxo"), {
            type: "bar",
            data: {
                labels,
                datasets: [{
                    label: "Entradas",
                    data: valores,
                    backgroundColor: valores.map(v =>
                        v === Math.max(...valores) ? "#ED7D31" : "rgba(46,84,150,0.6)"
                    ),
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { ticks: { font: { size: 9 } } }
                }
            }
        });
    });