# 📲 plg_system_btnwhatsapp — Plugin de Botão Flutuante WhatsApp para Joomla

![Status](https://img.shields.io/badge/status-ativo-success)
![Joomla](https://img.shields.io/badge/Joomla-4.x%20%7C%205.x%20%7C%206.x-blue)
![Tipo](https://img.shields.io/badge/Tipo-Plugin%20System-orange)
![Licença](https://img.shields.io/badge/licença-GPL-lightgrey)

---

## 📌 Visão Geral

O **plg_system_btnwhatsapp** é um plugin do tipo **System** para Joomla 4 e 5 que adiciona automaticamente um **botão flutuante de WhatsApp** no frontend do site.

O objetivo é permitir:

- Comunicação rápida com visitantes
- Personalização de número e mensagem
- Controle de exibição
- Implementação leve e desacoplada do template

---

# 🏗 Arquitetura do Plugin

O plugin atua na camada de sistema do Joomla, interceptando o ciclo de renderização da página.

```
┌─────────────────────────────┐
│          Frontend Joomla    │
└──────────────┬──────────────┘
               │
               ▼
┌─────────────────────────────┐
│     Evento onAfterRender    │
└──────────────┬──────────────┘
               │
               ▼
┌─────────────────────────────┐
│  Injeção do HTML/CSS/JS     │
│   Botão Flutuante WhatsApp  │
└─────────────────────────────┘
```

---

# 📁 Estrutura do Plugin

```
plg_system_btnwhatsapp/
│
├── btnwhatsapp.php
├── btnwhatsapp.xml
├── index.html
└── media/
    ├── css/
    ├── js/
    └── img/
```

---

# ⚙️ Funcionamento Técnico

O plugin:

1. É carregado como **plugin do tipo system**
2. Intercepta o evento:
   ```
   onAfterRender()
   ```
3. Verifica se está no **frontend**
4. Injeta:
  - HTML do botão
  - CSS de posicionamento
  - JS opcional (interações)
5. Renderiza o botão flutuante na página

---

# 🎯 Objetivo Técnico

- Implementação desacoplada do template
- Independência do layout do site
- Injeção leve via ciclo de renderização
- Compatibilidade com Joomla 4 e 5
- Fácil instalação e configuração

---

# 📦 Instalação

1. Compactar a pasta:
   ```
   plg_system_btnwhatsapp.zip
   ```
2. Acessar:
   ```
   Painel Administrativo Joomla → Sistema → Instalar Extensões
   ```
3. Enviar o arquivo ZIP
4. Ativar o plugin em:
   ```
   Sistema → Plugins → plg_system_btnwhatsapp
   ```

---

# 🔧 Configuração

Configurações disponíveis:

- Número do WhatsApp
- Mensagem padrão
- Exibir em todas as páginas ou apenas específicas
- Posição do botão (direita/esquerda)
- Cor personalizada (se aplicável)

---

# 🧠 Decisões Técnicas

| Decisão | Justificativa |
|----------|---------------|
| Tipo System | Permite injeção global no frontend |
| Evento onAfterRender | Garante HTML final antes da saída |
| Separação media/ | Organização e cache adequado |
| Injeção dinâmica | Evita alteração de template |

---

# 🧾 Boas Práticas Aplicadas

- Verificação de contexto (`isClient('site')`)
- Não interferência no backend
- Estrutura organizada de mídia
- XML de instalação padronizado
- Compatibilidade com Joomla 5

---

# 🚀 Roadmap Futuro

- Suporte a múltiplos números
- Integração com WhatsApp Business API
- Animações personalizadas
- Delay programável de exibição
- Configuração por menu item
- Modo dark automático

---

# 🛠 Tecnologias Utilizadas

- PHP 8+
- Joomla 4 / 5 / 6
- HTML5
- CSS3
- JavaScript

---

# 📬 Autor

Hirlei Carlos  
Desenvolvedor Web Sênior | PHP & Joomla | Sistemas Corporativos | Governo e Educação

- LinkedIn: https://linkedin.com/in/hirleicarlos
- GitHub: https://github.com/hirleicarlos
- Site: https://hirleicarlos.github.io

---

© 2026 — plg_system_btnwhatsapp
