# 📊 Relatório de Auditoria de Código (PSR-12)

> **Projeto:** `ProjetoFabrica`  
> **Data de Geração:** 07/09/2026 18:54:56

## 📈 Resumo Geral

| 🚨 Erros | ⚠️ Avisos | 🛠️ Erros Corrigíveis | 📁 Arquivos Analisados |
| :---: | :---: | :---: | :---: |
| **27** | **13** | **21** | **20** |

## 🔍 Detalhes por Arquivo

### 📄 `sistema/controlador/DashboardControlador.php`
**Erros:** 1 | **Avisos:** 0

| Linha | Coluna | Tipo | Regra | Mensagem |
| :---: | :---: | :---: | :--- | :--- |
| 29 | 1 | 🔴 ERRO | `CloseBraceAfterBody` | The closing brace for the class must go on the next line after the body 🛠️ |

---

### 📄 `sistema/controlador/LoginControlador.php`
**Erros:** 3 | **Avisos:** 0

| Linha | Coluna | Tipo | Regra | Mensagem |
| :---: | :---: | :---: | :--- | :--- |
| 35 | 1 | 🔴 ERRO | `SpacingBeforeClose` | Blank line found at end of control structure 🛠️ |
| 42 | 1 | 🔴 ERRO | `Indent` | Multi-line function call not indented correctly; expected 12 spaces but found 8 🛠️ |
| 42 | 9 | 🔴 ERRO | `Incorrect` | Line indented incorrectly; expected at least 12 spaces, found 8 🛠️ |

---

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
**Erros:** 3 | **Avisos:** 1

| Linha | Coluna | Tipo | Regra | Mensagem |
| :---: | :---: | :---: | :--- | :--- |
| 1 | 1 | 🟡 AVISO | `FoundWithSymbols` | A file should declare new symbols (classes, functions, constants, etc.) and cause no other side effects, or it should execute logic with side effects, but should not do both. The first symbol is defined on line 5 and the first side effect is on line 4. |
| 1 | 1 | 🔴 ERRO | `SpacingAfterTagBlock` | Header blocks must be separated by a single blank line 🛠️ |
| 6 | 19 | 🔴 ERRO | `NoSpaceBefore` | Expected at least 1 space before &quot;.&quot;; 0 found 🛠️ |
| 6 | 34 | 🔴 ERRO | `NoSpaceBefore` | Expected at least 1 space before &quot;.&quot;; 0 found 🛠️ |

---

### 📄 `sistema/nucleo/Helpers.php`
**Erros:** 2 | **Avisos:** 1

| Linha | Coluna | Tipo | Regra | Mensagem |
| :---: | :---: | :---: | :--- | :--- |
| 25 | 25 | 🔴 ERRO | `NoSpaceBefore` | Expected at least 1 space before &quot;.&quot;; 0 found 🛠️ |
| 25 | 25 | 🔴 ERRO | `NoSpaceAfter` | Expected at least 1 space after &quot;.&quot;; 0 found 🛠️ |
| 38 | 5 | 🟡 AVISO | `TooLong` | Line exceeds 120 characters; contains 926 characters |

---

### 📄 `sistema/nucleo/Mensagem.php`
**Erros:** 0 | **Avisos:** 1

| Linha | Coluna | Tipo | Regra | Mensagem |
| :---: | :---: | :---: | :--- | :--- |
| 54 | 5 | 🟡 AVISO | `TooLong` | Line exceeds 120 characters; contains 712 characters |

---

### 📄 `sistema/nucleo/Modelo.php`
**Erros:** 4 | **Avisos:** 1

| Linha | Coluna | Tipo | Regra | Mensagem |
| :---: | :---: | :---: | :--- | :--- |
| 200 | 5 | 🔴 ERRO | `SpacingBeforeClose` | Function closing brace must go on the next line following the body; found 1 blank lines before brace 🛠️ |
| 228 | 1 | 🟡 AVISO | `TooLong` | Line exceeds 120 characters; contains 146 characters |
| 247 | 13 | 🔴 ERRO | `SpacingAfterOpen` | Blank line found at start of control structure 🛠️ |
| 264 | 70 | 🔴 ERRO | `NoSpaceBefore` | Expected at least 1 space before &quot;.&quot;; 0 found 🛠️ |
| 264 | 70 | 🔴 ERRO | `NoSpaceAfter` | Expected at least 1 space after &quot;.&quot;; 0 found 🛠️ |

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
**Erros:** 12 | **Avisos:** 0

| Linha | Coluna | Tipo | Regra | Mensagem |
| :---: | :---: | :---: | :--- | :--- |
| 127 | 13 | 🔴 ERRO | `SpacingAfterOpen` | Blank line found at start of control structure 🛠️ |
| 184 | 13 | 🔴 ERRO | `SpacingAfterOpen` | Blank line found at start of control structure 🛠️ |
| 218 | 13 | 🔴 ERRO | `SpacingAfterOpen` | Blank line found at start of control structure 🛠️ |
| 252 | 13 | 🔴 ERRO | `SpacingAfterOpen` | Blank line found at start of control structure 🛠️ |
| 286 | 13 | 🔴 ERRO | `SpacingAfterOpen` | Blank line found at start of control structure 🛠️ |
| 310 | 12 | 🔴 ERRO | `NotCamelCaps` | Method name &quot;EasyPDO::select_start&quot; is not in camel caps format |
| 320 | 13 | 🔴 ERRO | `SpacingAfterOpen` | Blank line found at start of control structure 🛠️ |
| 336 | 12 | 🔴 ERRO | `NotCamelCaps` | Method name &quot;EasyPDO::select_next_row&quot; is not in camel caps format |
| 366 | 12 | 🔴 ERRO | `NotCamelCaps` | Method name &quot;EasyPDO::select_end&quot; is not in camel caps format |
| 386 | 16 | 🔴 ERRO | `SpacingAfterOpen` | Blank line found at start of control structure 🛠️ |
| 390 | 17 | 🔴 ERRO | `SpacingAfterOpen` | Blank line found at start of control structure 🛠️ |
| 413 | 12 | 🔴 ERRO | `NotCamelCaps` | Method name &quot;EasyPDO::available_drivers&quot; is not in camel caps format |

---

### 📄 `sistema/nucleo/suporte/Template.php`
**Erros:** 0 | **Avisos:** 1

| Linha | Coluna | Tipo | Regra | Mensagem |
| :---: | :---: | :---: | :--- | :--- |
| 34 | 16 | 🟡 AVISO | `TooLong` | Line exceeds 120 characters; contains 170 characters |

---

