var divResposta = document.getElementById("resposta");
var selectProduto = document.getElementById("produto"); // O select de produtos
var inputQuantidade = document.getElementById("quantidade");
var tituloPedido = document.getElementById("tituloPedido");

const params = new URLSearchParams(window.location.search); // Pegar o ID do pedido que veio na URL (?id=X)
const idPedidoAtual = params.get('id');

var listaProdutos = [];

document.addEventListener('DOMContentLoaded', iniciarPagina);
document.getElementById('botaoEnviar').addEventListener('click', postItem);

async function iniciarPagina() {
    if (!idPedidoAtual) {
        alert("ID do pedido não encontrado!");
        return;
    }
    tituloPedido.innerHTML = "Pedido #" + idPedidoAtual;
    
    await carregarProdutos();// Carrega os produtos para o Select primeiro
    getItem();
}

async function carregarProdutos() {
    var requisicao = await fetch("http://localhost/2026-3ano/Projetos/cafeteria-api/backend/produtos");
    var resposta = await requisicao.json();
    
    listaProdutos = resposta.data;

    var opcoes = "<option value=''>Selecione um produto</option>";
    resposta.data.forEach(prod => {
        opcoes += `<option value='${prod.id}'>${prod.nome} - R$ ${prod.preco}</option>`;
    });
    selectProduto.innerHTML = opcoes;
}

async function getItem() {
    var requisicao = await fetch("http://localhost/2026-3ano/Projetos/cafeteria-api/backend/pedido_itens/" + idPedidoAtual);
    var resposta = await requisicao.json();
    console.log("Status de conexão (GET): '" + resposta.status + "'")

    const linhas = resposta.data.map(item => {
        const produto = listaProdutos.find(p => p.id == item.produto_id);
        const nomeProd = produto ? produto.nome : "Produto Indisponível";
        
        const subtotal = item.quantidade * item.preco;

        return `
        <tr>
            <td>${item.id}</td>
            <td>${nomeProd}</td> 
            <td>${item.quantidade}</td>
            <td>${parseFloat(item.preco).toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'})}</td>
            <td>${subtotal.toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'})}</td>
            <td>
                <button onclick="deleteItem(${item.id})">Remover</button>
            </td>
        </tr>
    `;
    }).join("");

    divResposta.innerHTML = `
        <table class="sua-classe">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Preço Unitário</th>
                    <th>Subtotal</th>
                    <th>Opções</th>
                </tr>
            </thead>
            <tbody>${linhas}</tbody>
        </table>
    `;
}

async function postItem() {
    if (selectProduto.value === "" || inputQuantidade.value <= 0) {
        alert("Selecione um produto e uma quantidade válida.");
        return;
    }

    const produtoSelecionado = listaProdutos.find(p => p.id == selectProduto.value);

    var dados = {
        pedido_id: idPedidoAtual,
        produto_id: selectProduto.value,
        quantidade: inputQuantidade.value,
        preco: produtoSelecionado.preco
    };

    var requisicao = await fetch("http://localhost/2026-3ano/Projetos/cafeteria-api/backend/pedido_itens", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(dados)
    });

    var resposta = await requisicao.json();
    console.log("Status de conexão (POST): '" + resposta.status + "'\nMensagem: '" + resposta.message + "' ID: " + resposta.idItem)
    
    inputQuantidade.value = "";
    getItem(); 
}

async function deleteItem(idItem) {
    var requisicao = await fetch("http://localhost/2026-3ano/Projetos/cafeteria-api/backend/pedido_itens/" + idItem, {
        method: "DELETE"
    });
    var resposta = await requisicao.json();
    console.log("Status de conexão (DELETE): '" + resposta.status + "'\nMensagem: '" + resposta.message + "' ID: " + resposta.idItem)

    getItem();
}