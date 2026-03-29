var divResposta = document.getElementById("resposta")

document.addEventListener('DOMContentLoaded', getCategorias)

async function getCategorias() {
    var requisicao = await fetch('http://localhost/2026-3ano/Aula-BackEnd2/aula07/cafeteria-api/categorias')
}
// http://localhost/2026-3ano/Aula-BackEnd2/aula07/cafeteria-api/categorias