=== AI Content Generator ===
Contributors: seuusuario
Tags: ai, content generator, artificial intelligence, openrouter, groq, content creation, auto content
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.5.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Gere conteúdo de alta qualidade automaticamente usando Inteligência Artificial com suporte a múltiplos provedores (OpenRouter e Groq).

== Description ==

O AI Content Generator é um plugin WordPress que utiliza Inteligência Artificial para gerar conteúdo de alta qualidade automaticamente. Com suporte a múltiplos provedores de IA (OpenRouter e Groq) e diversos modelos de linguagem, o plugin oferece uma solução completa para criação de conteúdo.

= Características =

* Suporte a múltiplos provedores de IA (OpenRouter e Groq)
* Geração de diferentes tipos de conteúdo (posts, páginas, produtos)
* Formatação em texto simples ou HTML
* Cache integrado para melhor performance
* Suporte a múltiplos modelos de IA
* Otimização SEO automática
* Interface em português
* Configurações personalizáveis

= Modelos de IA Suportados =

* OpenRouter (Gratuitos):
  * Dolphin 3.0 R1 Mistral 24B
  * Mistral Small 24B Instruct
  * DeepSeek R1 Distill LLaMA 70B
  * Mistral 7B Instruct
  * OpenChat 3.5 7B
  * E outros...

* OpenRouter (Pagos):
  * GPT-4
  * Claude 3 Opus
  * Claude 3 Sonnet
  * E outros...

* Groq:
  * Llama2 70B
  * Mixtral 8x7B
  * Gemma 7B

= Recursos Adicionais =

* Geração de títulos otimizados para SEO
* Criação de meta descrições
* Geração de FAQs
* Criação de esboços de artigos
* Expansão de tópicos
* Otimização de conteúdo existente

= Shortcodes =

O plugin oferece os seguintes shortcodes:

* [aicg_content] - Gera conteúdo dinamicamente
* [aicg_faq] - Gera FAQs sobre um tópico
* [aicg_outline] - Gera um esboço de artigo

== Installation ==

1. Faça o upload da pasta `ai-content-generator` para o diretório `/wp-content/plugins/`
2. Ative o plugin através do menu 'Plugins' no WordPress
3. Configure suas chaves de API em Configurações > AI Content Generator

= Configuração =

1. Obtenha suas chaves de API:
   * OpenRouter: https://openrouter.ai/
   * Groq: https://groq.com/
2. Acesse o painel WordPress > Configurações > AI Content Generator
3. Insira suas chaves de API
4. Selecione o provedor padrão (OpenRouter ou Groq)

== Frequently Asked Questions ==

= Preciso de chave de API para usar o plugin? =

Sim, você precisa de pelo menos uma chave de API (OpenRouter ou Groq) para usar o plugin.

= Os modelos gratuitos têm limitações? =

Sim, os modelos gratuitos têm limites de uso definidos pelos provedores. Consulte a documentação de cada provedor para mais detalhes.

= O plugin funciona com o Gutenberg? =

Sim, o plugin é compatível tanto com o editor clássico quanto com o Gutenberg.

= Como funciona o cache? =

O plugin armazena em cache as respostas da API para prompts idênticos, melhorando a performance e reduzindo custos.

== Screenshots ==

1. Painel de geração de conteúdo
2. Configurações do plugin
3. Seleção de modelos de IA
4. Exemplo de conteúdo gerado

== Changelog ==

= 1.5.0 =
* Adicionado suporte ao Groq API
* Implementação de cache
* Novos modelos de IA
* Melhorias na interface
* Correções de bugs

= 1.4.0 =
* Interface redesenhada
* Suporte a múltiplos modelos
* Otimização de performance
* Novos shortcodes

== Upgrade Notice ==

= 1.5.0 =
Esta versão adiciona suporte ao Groq API e traz melhorias significativas de performance com implementação de cache.

== Privacy Policy ==

Este plugin não coleta dados pessoais dos usuários. No entanto, utiliza serviços de terceiros (OpenRouter e Groq) para geração de conteúdo. Consulte as políticas de privacidade desses serviços para mais informações. 