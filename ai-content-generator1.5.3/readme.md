# AI Content Generator for WordPress

## Descrição
O AI Content Generator é um plugin WordPress que utiliza Inteligência Artificial para gerar conteúdo de alta qualidade automaticamente. Com suporte a múltiplos provedores de IA (OpenRouter e Groq) e diversos modelos de linguagem, o plugin oferece uma solução completa para criação de conteúdo.

## Características
- 🤖 Suporte a múltiplos provedores de IA (OpenRouter e Groq)
- 📝 Geração de diferentes tipos de conteúdo (posts, páginas, produtos)
- 🎨 Formatação em texto simples ou HTML
- 🔄 Cache integrado para melhor performance
- 🌐 Suporte a múltiplos modelos de IA
- 🎯 Otimização SEO automática
- 💬 Interface em português
- 🛠️ Configurações personalizáveis

## Requisitos
- WordPress 5.0 ou superior
- PHP 7.4 ou superior
- Chave de API do OpenRouter e/ou Groq

## Instalação
1. Faça o upload da pasta `ai-content-generator` para o diretório `/wp-content/plugins/`
2. Ative o plugin através do menu 'Plugins' no WordPress
3. Configure suas chaves de API em Configurações > AI Content Generator

## Configuração
1. Obtenha suas chaves de API:
   - OpenRouter: [https://openrouter.ai/](https://openrouter.ai/)
   - Groq: [https://groq.com/](https://groq.com/)
2. Acesse o painel WordPress > Configurações > AI Content Generator
3. Insira suas chaves de API
4. Selecione o provedor padrão (OpenRouter ou Groq)

## Como Usar
1. Ao criar/editar um post ou página, você verá o botão "Generate with AI"
2. Clique no botão para abrir o painel de geração
3. Configure as opções:
   - Tipo de conteúdo (post, página, produto)
   - Modelo de IA
   - Formato (texto ou HTML)
   - Tom de voz
   - Comprimento do texto
   - Otimização SEO
4. Digite seu prompt ou tópico
5. Clique em "Gerar" e aguarde o resultado

## Modelos Disponíveis

### OpenRouter (Gratuitos)
- Dolphin 3.0 R1 Mistral 24B
- Mistral Small 24B Instruct
- DeepSeek R1 Distill LLaMA 70B
- Mistral 7B Instruct
- OpenChat 3.5 7B
- E outros...

### OpenRouter (Pagos)
- GPT-4
- Claude 3 Opus
- Claude 3 Sonnet
- E outros...

### Groq
- Llama2 70B
- Mixtral 8x7B
- Gemma 7B

## Recursos Adicionais
- Geração de títulos otimizados para SEO
- Criação de meta descrições
- Geração de FAQs
- Criação de esboços de artigos
- Expansão de tópicos
- Otimização de conteúdo existente

## Shortcodes
O plugin oferece shortcodes para uso em posts e páginas:
- `[aicg_content]` - Gera conteúdo dinamicamente
- `[aicg_faq]` - Gera FAQs sobre um tópico
- `[aicg_outline]` - Gera um esboço de artigo

## Suporte
Para suporte, dúvidas ou sugestões, entre em contato através do GitHub ou WordPress.org.

## Changelog

### 1.5.0
- Adicionado suporte ao Groq API
- Implementação de cache
- Novos modelos de IA
- Melhorias na interface
- Correções de bugs

### 1.4.0
- Interface redesenhada
- Suporte a múltiplos modelos
- Otimização de performance
- Novos shortcodes

## Licença
Este plugin é licenciado sob a GPL v2 ou posterior.

## Créditos
Desenvolvido por [Seu Nome/Empresa] 