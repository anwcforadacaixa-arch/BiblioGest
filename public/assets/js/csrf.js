// Busca o token CSRF da meta tag
function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute("content") : "";
}

// Função global para fazer pedidos POST com CSRF
function postJson(url, dados) {
    return fetch(url, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": getCsrfToken()
        },
        body: JSON.stringify(dados)
    }).then(r => r.json());
}