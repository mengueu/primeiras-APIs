var divResposta = document.getElementById("resposta");
var inputNome = document.getElementById("item");
var selectCategorias = document.getElementById("categorias");

document.getElementById('botaoEnviar').addEventListener('click', postItem);

document.addEventListener('DOMContentLoaded', async () => {
    var requisicao = await fetch("http://localhost/2026-3ano/Aula-FrontEnd/Prova/backend/categorias");
    var resposta = await requisicao.json();
    var opcoes = "<option value=''>Selecione uma Categoria</option>";

    resposta.data.forEach(categoria => {
        opcoes += `<option value='${categoria.id}'>${categoria.nome}</option>`;
    });
    selectCategorias.innerHTML = opcoes;

    getItem();
});

async function getItem() {
    var requisicao = await fetch("http://localhost/2026-3ano/Aula-FrontEnd/Prova/backend/itens");
    var resposta = await requisicao.json();

    const linhas = resposta.data.map(item => { 
        const checkStatus = item.feito ? "checked" : "";
        const textoStatus = item.feito ? "<span>Concluído</span>" : "<span>Pendente</span>";

        return `
        <tr>
            <td>${item.id}</td>
            <td style="text-align: left;">${item.nome}</td>
            <td>${item.categoria_nome}</td>
            <td>
                <label style="cursor: pointer;">
                    <input type="checkbox" onchange="check(${item.id}, this.checked)" ${checkStatus}>${textoStatus}
                </label>
            </td>
            <td>
                <button onclick="deleteItem(${item.id})">Deletar</button>
            </td>
        </tr>
    `}).join("");
    
    divResposta.innerHTML = `
        <table class="sua-classe">
            <thead>
                <tr>
                    <th colspan="5"><center>Itens Cadastrados</center></th>
                </tr>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Categoria</th>
                    <th>Status</th>
                    <th>Opções</th>
                </tr>
            </thead>
            <tbody>${linhas}</tbody>
        </table>
    `;
}

async function postItem() {
    if (inputNome.value.trim() === "" || selectCategorias.value.trim() === "" ) {
        alert("Por favor, preencha todos os campos.");
        return;
    }

    await fetch("http://localhost/2026-3ano/Aula-FrontEnd/Prova/backend/itens", {
        method: "POST", 
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            nome: inputNome.value, 
            categoria_id: selectCategorias.value 
        })
    });
    
    inputNome.value = "";
    selectCategorias.value = "";
    getItem();
}

async function check(id, foiChecado) {
    await fetch("http://localhost/2026-3ano/Aula-FrontEnd/Prova/backend/itens/" + id, {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ feito: foiChecado })
    });

    getItem();
}

async function deleteItem(id) {
    if (!confirm("Tem certeza que deseja deletar este item?")){
        return;
    } 

    await fetch("http://localhost/2026-3ano/Aula-FrontEnd/Prova/backend/itens/" + id, {
        method: "DELETE"
    });
 
    getItem();
}