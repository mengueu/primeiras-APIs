var divResposta = document.getElementById("resposta")
var inputNome   = document.getElementById("nome")   

document.addEventListener('DOMContentLoaded', getCategorias)
document.getElementById('botaoEnviar').addEventListener('click', postCategoria)

async function getCategorias() {
    var requisicao = await fetch("http://localhost/2026-3ano/Aula-FrontEnd/Prova/backend/categorias")
    var resposta = await requisicao.json()
    console.log("Status de conexão (GET): '" + resposta.status + "'")

    const linhas = resposta.data.map(item => `
        <tr>
            <td>${item.id}</td>
            <td>${item.nome}</td>
            <td><button onclick="deleteCategoria(${item.id})">Deletar</button></td>
        </tr>
    `).join("");
    
    divResposta.innerHTML = `
        <table class="sua-classe">
            <thead>
                <tr>
                    <th colspan="3" ><center>Categorias Cadastradas</center></th>
                </tr>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Opções</th>
                </tr>
            </thead>
            <tbody>${linhas}</tbody>
        </table>
    `;
}

async function postCategoria() {

    if (inputNome.value.trim() === "") {
        alert("Por favor, digite o nome da categoria.");
        return;
    }

    var requisicao = await fetch("http://localhost/2026-3ano/Aula-FrontEnd/Prova/backend/categorias", {
        method:  "POST",
        headers: {
            "Content-Type": "application/json" 
        },
        body: JSON.stringify({ nome: inputNome.value })
    })
    var resposta = await requisicao.json()
    console.log("Status de conexão (POST): '" + resposta.status + "'\nMensagem: '" + resposta.message + "' ID: " + resposta.idCategoria)
    
    inputNome.value = ""

    getCategorias()
}

async function deleteCategoria(id) {
    var requisicao = await fetch("http://localhost/2026-3ano/Aula-FrontEnd/Prova/backend/categorias/" + id, {
        method: "DELETE"
    }) 
    var resposta = await requisicao.json()
    console.log("Status de conexão (DELETE): '" + resposta.status + "'\nMensagem: '" + resposta.message + "' ID: " + resposta.idCategoria)
 
    getCategorias()
}