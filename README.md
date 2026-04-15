# 📲 plg_system_btnwhatsapp — Botão Flutuante WhatsApp para Joomla

![Versão](https://img.shields.io/badge/versão-3.1.0-blue)
![Status](https://img.shields.io/badge/status-ativo-success)
![Joomla](https://img.shields.io/badge/Joomla-4.x%20%7C%205.x%20%7C%206.x-blue)
![PHP](https://img.shields.io/badge/PHP-8.3%2B-purple)
![Tipo](https://img.shields.io/badge/Tipo-Plugin%20System-orange)
![Licença](https://img.shields.io/badge/licença-GPL%20v2-lightgrey)

---

## 📌 Visão Geral

O **plg_system_btnwhatsapp** é um plugin do tipo **System** para Joomla 4, 5 e 6 que adiciona automaticamente um **botão flutuante de WhatsApp** no frontend do site — sem necessidade de alterar template ou inserir código manual.

Totalmente configurável pelo painel administrativo do Joomla, com suporte a:

- Personalização visual completa (cores, tamanho, forma, ícone)
- Controle de exibição por página, dispositivo e horário
- Animações de entrada e tooltip automático
- Horário de atendimento por dia da semana com fuso horário
- Ícone customizável via imagem, SVG ou classe CSS
- Zero dependências externas

---

## 🏗 Arquitetura

O plugin atua na camada de sistema do Joomla, interceptando dois momentos do ciclo de renderização:

```
┌──────────────────────────────────┐
│           Frontend Joomla        │
└─────────────────┬────────────────┘
                  │
       ┌──────────▼──────────┐
       │  onBeforeCompileHead │  → Registra e carrega CSS + JS via WebAssetManager
       └──────────┬──────────┘
                  │
       ┌──────────▼──────────┐
       │    onAfterRender     │  → Verifica regras, monta dados e injeta o HTML
       └──────────┬──────────┘
                  │
       ┌──────────▼──────────┐
       │   Layout default.php │  → Renderiza o botão com CSS variables + data-attrs
       └─────────────────────┘
```

---

## 📁 Estrutura de Arquivos

```
plg_system_btnwhatsapp/
│
├── btnwhatsapp.php                         # Entry point do plugin (obrigatório Joomla)
├── btnwhatsapp.xml                         # Manifesto: metadados, arquivos e campos de config
├── index.html                              # Proteção de listagem de diretório
│
├── src/
│   └── Extension/
│       └── Btnwhatsapp.php                 # Classe principal — lógica, eventos e render
│
├── services/
│   └── provider.php                        # Service Provider — injeção de dependência (DI)
│
├── layouts/
│   └── default.php                         # Template HTML do botão flutuante
│
├── media/
│   ├── css/
│   │   └── btnwhatsapp.css                 # Estilos: posição, shape, size, animações, tooltip
│   ├── js/
│   │   └── btnwhatsapp.js                  # JS: animação de entrada + tooltip automático
│   └── index.html
│
└── language/
    ├── pt-BR/
    │   ├── pt-BR.plg_system_btnwhatsapp.ini
    │   └── pt-BR.plg_system_btnwhatsapp.sys.ini
    └── en-GB/
        ├── en-GB.plg_system_btnwhatsapp.ini
        └── en-GB.plg_system_btnwhatsapp.sys.ini
```

---

## ⚙️ Funcionamento Técnico

### 1. Carregamento de assets — `onBeforeCompileHead`
Registra e enfileira o CSS e o JS via **WebAssetManager** do Joomla, garantindo versionamento correto e compatibilidade com o pipeline de assets do framework.

### 2. Renderização — `onAfterRender`
Executa todas as verificações antes de injetar o botão:

- **Contexto:** somente frontend (`isClient('site')`)
- **Dispositivo:** filtra por desktop, mobile ou ambos via `HTTP_USER_AGENT`
- **Página:** verifica o item de menu ativo contra a lista configurada
- **Horário:** calcula se está dentro do horário de atendimento pelo fuso configurado
- **Telefone:** sanitiza, aceita apenas dígitos
- **Mensagem:** substitui variáveis dinâmicas `{url}`, `{title}`, `{sitename}`
- **Link:** gera URL `wa.me` (mobile) ou `api.whatsapp.com` (desktop)

### 3. Layout — `default.php`
Recebe todos os dados via `$displayData`, resolve o ícone (classe CSS > SVG > imagem > padrão), monta as CSS variables inline e os `data-attributes` que o JS consome para animação e tooltip.

### 4. JavaScript — `btnwhatsapp.js`
Após o `DOMContentLoaded`, lê os `data-attributes` do wrapper e:
- Aplica a classe de animação de entrada com o delay configurado
- Exibe o tooltip automaticamente após o delay configurado
- Gerencia hover/focus/blur para re-exibição do tooltip

---

## 📦 Instalação

**Pré-requisitos:** Joomla 4.x, 5.x ou 6.x · PHP 8.3+

1. Baixe o arquivo `plg_system_btnwhatsapp_vX.X.X.zip`
2. No painel administrativo acesse:
   ```
   Sistema → Instalar → Extensões
   ```
3. Envie o arquivo ZIP pela aba **"Enviar arquivo de pacote"**
4. Após a instalação, ative o plugin em:
   ```
   Sistema → Gerenciar → Plugins → busque "WhatsApp Button"
   ```
5. Clique no plugin para abrir as configurações e preencha ao menos o **número de telefone**
6. Salve — o botão já estará visível no frontend

---

## 🔧 Configuração — Abas e Campos

### 📞 Aba: WhatsApp

| Campo | Descrição |
|---|---|
| **Número de Telefone** | Somente dígitos com DDI. Ex: `5561999999999` |
| **Mensagem Padrão** | Texto pré-preenchido no chat. Suporta `{url}`, `{title}`, `{sitename}` |
| **Posição do Botão** | Direita (end) ou Esquerda (start) — segue direção do layout (LTR/RTL) |

> 💡 **Variáveis dinâmicas:** `{url}` insere a URL atual, `{title}` o título da página, `{sitename}` o nome do site.

---

### 👁 Aba: Exibição

| Campo | Descrição |
|---|---|
| **Modo de Exibição** | `Todas as páginas` / `Apenas selecionadas` / `Ocultar nas selecionadas` |
| **Itens de Menu** | Visível quando o modo não é "Todas". Seleção múltipla de itens de menu |
| **Dispositivo** | Exibe para `Todos` / `Somente Desktop` / `Somente Mobile` |

---

### 🎨 Aba: Layout

| Campo | Descrição |
|---|---|
| **Tipo de Botão** | `Somente ícone` / `Somente texto` / `Ícone + texto` |
| **Imagem do Ícone** | Seletor de mídia nativo do Joomla. Prioridade 3 |
| **SVG do Ícone** | Cole o código `<svg...>`. Scripts e eventos inline são removidos. Prioridade 2 |
| **Classe CSS do Ícone** | Ex: `fa fa-whatsapp`. Usa biblioteca já carregada no site. Prioridade 1 |
| **Texto do Botão** | Texto exibido quando o layout inclui texto |
| **Forma** | `Círculo` / `Pílula` / `Arredondado` / `Quadrado` |
| **Tamanho** | `Pequeno` / `Médio` / `Grande` |

> 💡 **Prioridade do ícone:** Classe CSS → SVG → Imagem → SVG padrão WhatsApp. Se nenhum campo for preenchido, o ícone oficial do WhatsApp é usado automaticamente.

> ⚠️ Os campos de ícone só aparecem quando o tipo de botão inclui ícone.

---

### 🖌 Aba: Design

| Campo | Descrição |
|---|---|
| **Cor de fundo** | Cor principal do botão. Padrão: `#25D366` |
| **Cor do texto** | Cor do texto e ícone. Padrão: `#FFFFFF` |
| **Cor da borda** | Use `transparent` para sem borda |
| **Fundo (hover)** | Cor do fundo ao passar o mouse |
| **Texto (hover)** | Cor do texto ao passar o mouse |
| **Borda (hover)** | Cor da borda ao passar o mouse |
| **Sombra** | Ativa/desativa sombra no botão |
| **Distância do rodapé (px)** | Espaçamento vertical em relação à borda inferior |
| **Distância lateral (px)** | Espaçamento horizontal em relação à borda lateral |
| **Z-index** | Camada de sobreposição. Padrão: `999999` |

---

### ⚡ Aba: Comportamento

| Campo | Descrição |
|---|---|
| **Animação de entrada** | `Nenhuma` / `Slide` / `Bounce` / `Fade` |
| **Delay da animação (ms)** | Tempo até o botão aparecer após o carregamento. Ex: `1000` = 1 segundo |
| **Ativar Tooltip** | Exibe um balão de chamada acima do botão |
| **Texto do tooltip** | Mensagem do balão. Ex: `Fale conosco!` |
| **Delay do tooltip (ms)** | Tempo após o botão aparecer para exibir o tooltip |
| **Cor de fundo do tooltip** | Padrão: `#075E54` |
| **Cor do texto do tooltip** | Padrão: `#FFFFFF` |

> 💡 O tooltip também é exibido/ocultado no hover e focus do botão, garantindo acessibilidade.

---

### 🕐 Aba: Horário de Atendimento

| Campo | Descrição |
|---|---|
| **Ativar horário** | Liga/desliga o controle de horário |
| **Fuso horário** | Seletor de timezone. Padrão: `America/Sao_Paulo` |
| **Mensagem fora do horário** | Texto exibido no botão quando fora do expediente |
| **Dias da semana** | Para cada dia: ativar/desativar + horário de abertura e fechamento |

> 💡 Fora do horário configurado o botão muda para o estado **offline** (cinza, sem link), exibindo a mensagem configurada. Dentro do horário funciona normalmente.

---

## 🧠 Decisões Técnicas

| Decisão | Justificativa |
|---|---|
| Plugin tipo System | Injeção global no frontend sem depender de template |
| `onBeforeCompileHead` + `onAfterRender` | Assets via WebAssetManager + HTML injetado no `</body>` |
| CSS Variables inline | Permite personalização por instância sem sobrescrever folhas de estilo |
| `data-attributes` no wrapper | Desacopla configuração PHP do comportamento JS |
| Service Provider (DI) | Padrão moderno Joomla 4+ para instanciação de plugins |
| Sanitização de SVG no PHP | Segurança: remove `on*`, `<script>` e `javascript:` antes de salvar |
| Campo `type="media"` para imagem | Integração nativa com o Gerenciador de Mídia do Joomla |
| Layout `FileLayout` separado | Separa lógica de negócio da apresentação |

---

## 🛠 Tecnologias

- PHP 8.3+
- Joomla 4.x / 5.x / 6.x
- HTML5 semântico
- CSS3 (Custom Properties, Flexbox, `@keyframes`, lógicas `inset-*`)
- JavaScript ES5 puro (sem dependências)

---

## 🚀 Roadmap

- [ ] Suporte a múltiplos atendentes / números
- [ ] Integração com WhatsApp Business API
- [ ] Modo escuro automático
- [ ] Configuração de cor por item de menu
- [ ] Widget de pré-chat com nome/assunto

---

## 📬 Autor

**Hirlei Carlos Pereira de Araújo**  
Desenvolvedor Web Sênior · PHP & Joomla · Sistemas Corporativos · Governo e Educação

- 🔗 LinkedIn: [linkedin.com/in/hirleicarlos](https://linkedin.com/in/hirleicarlos)
- 🐙 GitHub: [github.com/hirleicarlos](https://github.com/hirleicarlos)
- 🌐 Site: [hirleicarlos.github.io](https://hirleicarlos.github.io)

---

© 2026 — plg_system_btnwhatsapp · GPL v2