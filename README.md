# Tech Experience 2026

Portal acadêmico de inscrição em oficinas de **HTML & CSS**, **JavaScript DOM** e **PHP Backend**. O participante conhece as oficinas, preenche o formulário e recebe uma página de confirmação ou de erros após a validação pelo PHP.

> **Projeto demonstrativo:** a inscrição é validada e exibida na resposta, mas **não é salva em banco de dados, arquivo ou serviço externo**. Uma confirmação não representa reserva de vaga.


## Passo a passo: como rodar

Você precisa apenas de **PHP 8 ou superior**, com a extensão **mbstring** ativa, e de um navegador atualizado. Não há dependências para instalar no projeto.

1. **Encontre a pasta do projeto.** Abra a pasta `tech-experience` e confirme que ela contém `index.html`, `style.css`, `script.js` e `processar.php`.

2. **Abra um terminal nessa pasta.** No Windows, abra a pasta no Explorador de Arquivos, digite `powershell` na barra de endereço e pressione **Enter**. No macOS/Linux, abra o Terminal, navegue até a pasta que contém o projeto e execute `cd tech-experience`. Se o terminal já estiver dentro de `tech-experience`, não precisa usar `cd`.

3. **Confira o PHP.** No terminal recém-aberto, execute:

   ```bash
   php -v
   php -m
   ```

   O primeiro comando deve indicar a versão **8 ou superior**; procure `mbstring` na lista mostrada pelo segundo. Se o Windows não reconhecer `php`, veja [Windows com XAMPP](#windows-com-xampp) antes de continuar.

4. **Inicie o servidor local.** Ainda dentro de `tech-experience`, execute:

   ```bash
   php -S localhost:8000
   ```

   Deixe esse terminal **aberto**: é nele que o servidor fica rodando. Não é necessário instalar Node.js nem iniciar Apache ou MySQL.

5. **Abra o site.** No navegador, acesse **http://localhost:8000/index.html**. Você deve ver a página inicial com as oficinas e o formulário de inscrição. Não abra `index.html` com duplo clique ou pelo endereço `file://`: desse modo o PHP não processa o formulário.

6. **Teste uma inscrição.** Informe nome e e-mail, selecione experiência e turno e marque pelo menos uma oficina. Clique em **Confirmar inscrição**. Uma página exibirá os dados validados; se houver erros, ela indicará o que corrigir. A mensagem de motivação é opcional.

7. **Encerre o servidor.** Volte ao terminal da etapa 4 e pressione **Ctrl+C**.

### Windows com XAMPP

Se `php -v` mostrar “comando não reconhecido”, mas você tem XAMPP instalado, siga estas etapas:

1. Abra a pasta `tech-experience` no Explorador de Arquivos, digite `powershell` na barra de endereço e pressione **Enter**.
2. Verifique se `C:\xampp\php\php.exe` existe; se o XAMPP estiver em outra pasta, localize o `php.exe` correspondente.
3. No PowerShell, rode o servidor com o caminho completo do executável:

   ```powershell
   & "C:\xampp\php\php.exe" -S localhost:8000
   ```

4. Mantenha o PowerShell aberto e acesse **http://localhost:8000/index.html**. Para parar, pressione **Ctrl+C**. Não é necessário iniciar Apache ou MySQL.

Se você não tiver PHP instalado, instale o PHP 8+ com `mbstring` e repita a partir da etapa 2. Se a porta 8000 estiver em uso, consulte [Problemas comuns](#problemas-comuns).

### Adicionar PHP ao PATH (opcional)

Isso só é necessário se você quiser executar `php -v` e `php -S` sem informar o caminho completo:

1. No Windows, abra o menu **Iniciar** e pesquise **Editar as variáveis de ambiente da sua conta**.
2. Na janela aberta, selecione **Path** em *Variáveis de usuário* e clique em **Editar**.
3. Clique em **Novo**, adicione `C:\xampp\php` (a **pasta**, não o arquivo `php.exe`) e confirme as janelas com **OK**. Adicione apenas se essa pasta existir e ainda não estiver na lista.
4. **Feche e abra novamente** o PowerShell e o editor para que recebam o `PATH` atualizado. Execute `php -v` para confirmar.

A alteração do `PATH` não é necessária para usar o projeto: o comando com `& "C:\xampp\php\php.exe"` acima já funciona.

## Como o projeto funciona

```text
tech-experience/
├── index.html     Página inicial, oficinas e formulário
├── style.css      Layout, cores, estados de interação e responsividade
├── script.js      Menu mobile, contador e validação no navegador
├── processar.php  Validação no servidor e páginas de confirmação/erro
└── README.md      Este guia
```

**Fluxo:** `index.html` → formulário `POST` → `processar.php` → confirmação (`200`) ou lista de erros (`422`). O JavaScript orienta o participante, mas **não substitui a validação no servidor**. Acesso direto a `processar.php` por `GET` retorna `405`.

O PHP valida nome, e-mail, experiência, turno, seleção de oficinas e tamanho da mensagem. Valores de experiência, turno e oficinas são comparados a listas de opções permitidas. Informações exibidas nas respostas passam por `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` para evitar execução de HTML/JavaScript enviado no formulário.

## Roteiro de testes

Com o servidor em execução, verifique:

| Cenário | Ação | Resultado esperado |
| --- | --- | --- |
| Inscrição válida | Preencha os campos e selecione uma ou mais oficinas | Página de confirmação com os dados enviados; HTTP `200` |
| Sem oficina | Preencha os demais campos e envie sem marcar oficinas | Aviso e destaque no formulário; envio bloqueado pelo JavaScript |
| Nome ou e-mail inválido | Envie valores inválidos diretamente ao PHP | Lista de erros; HTTP `422` |
| Opção manipulada | Envie experiência, turno ou oficina fora das opções disponíveis | Rejeição pelo PHP; HTTP `422` |
| Mensagem longa | Envie mais de 200 caracteres diretamente ao PHP | Rejeição pelo PHP; HTTP `422` |
| XSS | Use `<script>alert("XSS")</script>` no nome e conclua uma inscrição válida | O código aparece como texto escapado; nenhum alerta executa |
| Método incorreto | Abra `http://localhost:8000/processar.php` diretamente | HTTP `405 Method Not Allowed` |
| Interface | Teste menu e formulário no celular, navegação por teclado e contador de caracteres | Interações funcionais, foco visível e layout sem rolagem horizontal |

Para conferir o envio HTTP no navegador, abra as **Ferramentas do desenvolvedor → Rede/Network** antes de clicar em **Confirmar inscrição**. A requisição para `processar.php` deve usar o método `POST` e conter os campos preenchidos.

### Exemplos de requisição direta

Estes comandos são opcionais; servem para testar o PHP **sem depender da validação do navegador**. Em PowerShell, use `curl.exe` no lugar de `curl`.

```bash
# Inscrição válida: resposta HTTP 200
curl -i -d "nome=Ana+Silva&email=ana%40exemplo.com&experiencia=iniciante&turno=manha&oficinas%5B%5D=html_css" http://localhost:8000/processar.php

# Ausência de oficinas: resposta HTTP 422
curl -i -d "nome=Ana+Silva&email=ana%40exemplo.com&experiencia=iniciante&turno=manha" http://localhost:8000/processar.php

# Acesso por GET: resposta HTTP 405
curl -i http://localhost:8000/processar.php
```

## Problemas comuns

| Problema | O que verificar |
| --- | --- |
| `php` não é reconhecido | Instale PHP 8+ ou use o caminho completo para `php.exe` (exemplo do XAMPP acima). |
| Erro sobre `mb_strlen()` | Habilite a extensão `mbstring` na instalação do PHP usada pelo terminal e confirme com `php -m`. |
| Porta 8000 em uso | Pare o outro servidor ou execute `php -S localhost:8001` e acesse `http://localhost:8001/index.html`. |
| Formulário não processa o envio | Acesse o site pelo endereço `http://localhost:8000/index.html`, não por `file://`, e mantenha o servidor ligado. |
| Página de erro após o envio | Leia a lista de erros apresentada pelo PHP e volte ao formulário para corrigir os dados. |

**Tecnologias:** HTML5, CSS3, JavaScript puro (ES6+) e PHP 8+. Sem React, bibliotecas de interface, APIs externas ou banco de dados.
