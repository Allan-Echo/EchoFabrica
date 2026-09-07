# 📊 Relatório de Auditoria de Código (PSR-12)

> **Projeto:** `ProjetoFabrica`  
> **Data de Geração:** 07/09/2026 21:47:09

## 📈 Resumo Geral

| 🚨 Erros | ⚠️ Avisos | 🛠️ Erros Corrigíveis | 📁 Arquivos Analisados |
| :---: | :---: | :---: | :---: |
| **6** | **13** | **0** | **20** |

## 🔍 Detalhes por Arquivo

### 📄 `sistema/controlador/SiteControlador.php`
**Erros:** 1 | **Avisos:** 0

| Linha | Coluna | Tipo | Regra | Mensagem |
| :---: | :---: | :---: | :--- | :--- |
| 73 | 12 | 🔴 ERRO | `NotCamelCaps` | Method name &quot;SiteControlador::produção&quot; is not in camel caps format |

---

### 📄 `sistema/modelo/Layout.php`
**Erros:** 0 | **Avisos:** 1

| Linha | Coluna | Tipo | Regra | Mensagem |
| :---: | :---: | :---: | :--- | :--- |
| 1 | 1 | 🟡 AVISO | `Mixed` | File has mixed line endings; this may cause incorrect results |

---

### 📄 `sistema/modelo/LayoutMaquina.php`
**Erros:** 1 | **Avisos:** 2

| Linha | Coluna | Tipo | Regra | Mensagem |
| :---: | :---: | :---: | :--- | :--- |
| 1 | 1 | 🟡 AVISO | `Mixed` | File has mixed line endings; this may cause incorrect results |
| 34 | 12 | 🔴 ERRO | `NotCamelCaps` | Method name &quot;LayoutMaquina::buscarLayout_Machine&quot; is not in camel caps format |
| 36 | 18 | 🟡 AVISO | `TooLong` | Line exceeds 120 characters; contains 172 characters |

---

### 📄 `sistema/modelo/UsuarioModelo.php`
**Erros:** 0 | **Avisos:** 2

| Linha | Coluna | Tipo | Regra | Mensagem |
| :---: | :---: | :---: | :--- | :--- |
| 23 | 9 | 🟡 AVISO | `TooLong` | Line exceeds 120 characters; contains 277 characters |
| 26 | 9 | 🟡 AVISO | `TooLong` | Line exceeds 120 characters; contains 254 characters |

---

### 📄 `sistema/nucleo/configuracoes.php`
**Erros:** 0 | **Avisos:** 1

| Linha | Coluna | Tipo | Regra | Mensagem |
| :---: | :---: | :---: | :--- | :--- |
| 1 | 1 | 🟡 AVISO | `FoundWithSymbols` | A file should declare new symbols (classes, functions, constants, etc.) and cause no other side effects, or it should execute logic with side effects, but should not do both. The first symbol is defined on line 4 and the first side effect is on line 3. |

---

### 📄 `sistema/nucleo/Helpers.php`
**Erros:** 0 | **Avisos:** 1

| Linha | Coluna | Tipo | Regra | Mensagem |
| :---: | :---: | :---: | :--- | :--- |
| 38 | 5 | 🟡 AVISO | `TooLong` | Line exceeds 120 characters; contains 926 characters |

---

### 📄 `sistema/nucleo/Mensagem.php`
**Erros:** 0 | **Avisos:** 1

| Linha | Coluna | Tipo | Regra | Mensagem |
| :---: | :---: | :---: | :--- | :--- |
| 54 | 5 | 🟡 AVISO | `TooLong` | Line exceeds 120 characters; contains 712 characters |

---

### 📄 `sistema/nucleo/Modelo.php`
**Erros:** 0 | **Avisos:** 1

| Linha | Coluna | Tipo | Regra | Mensagem |
| :---: | :---: | :---: | :--- | :--- |
| 227 | 1 | 🟡 AVISO | `TooLong` | Line exceeds 120 characters; contains 146 characters |

---

### 📄 `sistema/nucleo/Sessao.php`
**Erros:** 0 | **Avisos:** 3

| Linha | Coluna | Tipo | Regra | Mensagem |
| :---: | :---: | :---: | :--- | :--- |
| 37 | 5 | 🟡 AVISO | `TooLong` | Line exceeds 120 characters; contains 155 characters |
| 44 | 5 | 🟡 AVISO | `TooLong` | Line exceeds 120 characters; contains 259 characters |
| 52 | 5 | 🟡 AVISO | `TooLong` | Line exceeds 120 characters; contains 593 characters |

---

### 📄 `sistema/nucleo/suporte/EasyPDO.php`
**Erros:** 4 | **Avisos:** 0

| Linha | Coluna | Tipo | Regra | Mensagem |
| :---: | :---: | :---: | :--- | :--- |
| 305 | 12 | 🔴 ERRO | `NotCamelCaps` | Method name &quot;EasyPDO::select_start&quot; is not in camel caps format |
| 330 | 12 | 🔴 ERRO | `NotCamelCaps` | Method name &quot;EasyPDO::select_next_row&quot; is not in camel caps format |
| 360 | 12 | 🔴 ERRO | `NotCamelCaps` | Method name &quot;EasyPDO::select_end&quot; is not in camel caps format |
| 405 | 12 | 🔴 ERRO | `NotCamelCaps` | Method name &quot;EasyPDO::available_drivers&quot; is not in camel caps format |

---

### 📄 `sistema/nucleo/suporte/Template.php`
**Erros:** 0 | **Avisos:** 1

| Linha | Coluna | Tipo | Regra | Mensagem |
| :---: | :---: | :---: | :--- | :--- |
| 34 | 16 | 🟡 AVISO | `TooLong` | Line exceeds 120 characters; contains 170 characters |

---

