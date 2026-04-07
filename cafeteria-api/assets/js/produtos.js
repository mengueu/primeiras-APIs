// Mapeamento dos elementos do HTML
var divResposta = document.getElementById("resposta")
var inputNome = document.getElementById("nome")
var inputPreco = document.getElementById("preco")
var selectCategorias = document.getElementById("categorias")

var listaCategorias = []; // Variável para para guardar as categorias que vêm do banco. 
                          // Isso permite que o código "lembre" os nomes das categorias sem precisar perguntar ao PHP toda hora.

document.addEventListener('DOMContentLoaded', getProdutos) 
document.getElementById('botaoEnviar').addEventListener('click', postProdutos) // Quando o botão de enviar for clicado, executa a função de cadastro.

// Mostrar as categorias no cadastro dos produtos
// Usamos async() pois o fetch é uma promessa que leva tempo para ser concluída.
document.addEventListener('DOMContentLoaded', async() => {
    var requisicao = await fetch("http://localhost/2026-3ano/Projetos/cafeteria-api/backend/categorias")
    var resposta = await requisicao.json()
    var opcoes = "<option value=''>Selecione uma Categoria</option>"

    listaCategorias = resposta.data // Armazena os dados para uso futuro na tabela de produtos.

    resposta.data.forEach(categoria => { // Para cada categoria recebida, cria uma tag <option> com o ID no valor e o Nome no texto.
        opcoes += `<option value='${categoria.id}'>${categoria.nome}</option>`
    });
    selectCategorias.innerHTML = opcoes;

    getProdutos()
});

// GET: Visualização da tabela 'produtos' do banco de dados
async function getProdutos() {
    var requisicao = await fetch("http://localhost/2026-3ano/Projetos/cafeteria-api/backend/produtos")
    var resposta = await requisicao.json()
    console.log("Status de conexão (GET): '" + resposta.status + "'")

    const linhas = resposta.data.map(item => { // .map percorre cada produto e transforma em uma linha (<tr>) da tabela.
        const categoriaEncontrada = listaCategorias.find(cat => cat.id == item.categoria_id); // Procura dentro da 'listaCategorias' qual categoria tem o ID igual ao 'categoria_id' do produto.
        const nomeCategoria = categoriaEncontrada ? categoriaEncontrada.nome : "Não encontrada"; // Se encontrar, usa o nome. Se não (caso a categoria tenha sido excluída), mostra um aviso.
        return `
        <tr>
            <td>${item.id}</td>
            <td>${item.nome}</td>
            <td>${item.preco}</td>
            <td>${nomeCategoria}</td>
            <td><button onclick="deleteProdutos(${item.id})">Deletar</button></td>
        </tr>
    `}).join(""); // .join("") remove as vírgulas que o map coloca entre os itens.
    
    divResposta.innerHTML = `
        <table class="sua-classe">
            <thead>
                <tr>
                    <th colspan="5" ><center>Produtos Cadastrados</center></th>
                </tr>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Categoria</th>
                    <th>Opções</th>
                </tr>
            </thead>
            <tbody>${linhas}</tbody>
        </table>
    `;
}

// POST: Enviando novos dados (através do método POST) ao banco de dados
async function postProdutos() {

    if (inputNome.value.trim() === "" || inputPreco.value.trim() === "" || selectCategorias.value.trim() === "" ) {
        alert("Por favor, preencha todos os campos.");
        return;
    }

    var requisicao = await fetch("http://localhost/2026-3ano/Projetos/cafeteria-api/backend/produtos", {
        method: "POST", // Define que estamos enviando dados.
        headers: {
            "Content-Type": "application/json" // Avisa o PHP que o conteúdo é um JSON.
        },
        body: JSON.stringify({ // Transforma o objeto do JavaScript em uma string de texto JSON.
            nome: inputNome.value, 
            preco: inputPreco.value, 
            categorias: selectCategorias.value
        })
    })
    var resposta = await requisicao.json()
    console.log("Status de conexão (POST): '" + resposta.status + "'\nMensagem: '" + resposta.message + "' ID: " + resposta.idProduto)
    
    // Limpa os campos do formulário após o sucesso.
    inputNome.value = ""
    inputPreco.value = ""

    getProdutos()
}

// DELETE: Excluindo dados
async function deleteProdutos(id) {
    var requisicao = await fetch("http://localhost/2026-3ano/Projetos/cafeteria-api/backend/produtos/" + id, {
        method: "DELETE"
    })
 
    var resposta = await requisicao.json()
    console.log("Status de conexão (DELETE): '" + resposta.status + "'\nMensagem: '" + resposta.message + "' ID: " + resposta.idProduto)
 
    getProdutos()
}