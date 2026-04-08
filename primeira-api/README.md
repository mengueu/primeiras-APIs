# 🚀 Primeira API - Funcionamento

<p>Esta é uma API simples desenvolvida em PHP para demonstrar os conceitos básicos de requisições HTTP (GET e POST), integração entre FrontEnd e BackEnd, e manipulação de dados em formato JSON.</p>

<br>

# 🧠 Como a API Funciona

<p>A aplicação é dividida em duas partes:</p>

- **FrontEnd (HTML + JavaScript)** → Responsável pela interface e envio das requisições  
- **BackEnd (PHP)** → Responsável por processar as requisições e retornar respostas  

<br>

# 🔗 Endpoints Disponíveis

## ✅ GET /hello
<p>Retorna uma mensagem simples confirmando que a API está funcionando.</p>

**Resposta:**
```json
{
  "status": "success",
  "message": "Olá! A API está funcionando!"
}
```

## 🔁 POST /echo
<p>Recebe um texto enviado pelo usuário e retorna exatamente o mesmo conteúdo (efeito "eco").</p>

Exemplo de envio:

```json
{
  "message": "Olá API"
}
```

Resposta:

```json
{
  "status": "success",
  "echo": {
    "message": "Olá API"
  }
}
```
<br>

# ⚙️ Fluxo da Aplicação
<p>→ O usuário interage com a interface (botões e input).</p>
<p>→ O JavaScript utiliza a função fetch() para enviar requisições HTTP.</p>
<p>→ O PHP recebe a requisição e identifica:</p>
<p>→ O método (GET ou POST)</p>
<p>→ O endpoint acessado</p>
<p>→ A API processa a lógica e retorna uma resposta em JSON.</p>
<p>→ O JavaScript exibe a resposta na tela.</p>

<br>

# 🧩 Tecnologias Utilizadas
- HTML5
- CSS3
- JavaScript (Fetch API + Async/Await)
- PHP
- JSON

<br>

# 🔧 Configuração Técnica
- O arquivo .htaccess utiliza URL Rewrite para redirecionar todas as requisições para o index.php.
- O PHP identifica o endpoint através da URL (REQUEST_URI).
- A API permite requisições externas utilizando CORS.
- As respostas são sempre retornadas no formato JSON.

<br>

# 📌 Objetivo
<p>Este projeto tem como finalidade o aprendizado prático de:</p>

- Estrutura de uma API REST básica
- Métodos HTTP (GET e POST)
- Comunicação entre FrontEnd e BackEnd
- Manipulação de dados com JSON
- Organização de rotas em PHP
