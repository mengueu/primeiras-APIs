var divResposta = document.getElementById("resposta")
var inputCliente   = document.getElementById("cliente")

document.addEventListener('DOMContentLoaded', getPedidos)
document.getElementById('botaoEnviar').addEventListener('click', postPedidos)

async function getPedidos() {
    var requisicao = await fetch("http://localhost/2026-3ano/Projetos/cafeteria-api/backend/pedidos")
    var resposta = await requisicao.json()
    console.log("Status de conexão (GET): '" + resposta.status + "'")

    const linhas = resposta.data.map(item => {

    const dataFormatada = new Date(item.criado_em).toLocaleString('pt-BR'); // Formatação da data
    const totalFormatado = parseFloat(item.total).toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'}); // Formatação do valor

    return `
        <tr>
            <td>${item.id}</td>
            <td>${item.cliente}</td> 
            <td>${totalFormatado}</td>
            <td>${dataFormatada}</td>
            <td>
                <a href="pedido_itens.html?id=${item.id}">Detalhes</a>
                <button onclick="deletePedidos(${item.id})">Deletar</button>
            </td>
        </tr>
    `;
    }).join("");
    
    divResposta.innerHTML = `
        <table class="sua-classe">
            <thead>
                <tr>
                    <th colspan="5"><center>Pedidos Cadastrados</center></th>
                </tr>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Total</th>
                    <th>Data</th>
                    <th>Opções</th>
                </tr>
            </thead>
            <tbody>${linhas}</tbody>
        </table>
    `;
}

async function postPedidos() {
    if (inputCliente.value.trim() === "") {
        alert("Por favor, digite o nome do cliente.");
        return;
    }

    var requisicao = await fetch("http://localhost/2026-3ano/Projetos/cafeteria-api/backend/pedidos", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ 
            cliente: inputCliente.value 
        })
    });

    var resposta = await requisicao.json();
    console.log("Status de conexão (POST): '" + resposta.status + "'\nMensagem: '" + resposta.message + "' ID: " + resposta.idPedidos)
    
    inputCliente.value = ""; 
    getPedidos();
}

async function deletePedidos(id) {
    var requisicao = await fetch("http://localhost/2026-3ano/Projetos/cafeteria-api/backend/pedidos/" + id, {
        method: "DELETE"
    }) 
    var resposta = await requisicao.json()
    console.log("Status de conexão (DELETE): '" + resposta.status + "'\nMensagem: '" + resposta.message + "' ID: " + resposta.idPedidos)
 
    getPedidos()
}