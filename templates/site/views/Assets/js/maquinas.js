document.addEventListener("DOMContentLoaded", function () {
  const colunasCheck = document.querySelectorAll('.col-selecionar');
  const btnAtivarDetalhes = document.getElementById('btnAtivarDetalhes');
  const btnAtivarExclusao = document.getElementById('btnAtivarExclusao');
  const btnAcaoDetalhes = document.getElementById('btnAcaoDetalhes');
  const btnAcaoExcluir = document.getElementById('btnAcaoExcluir');
  const selectAll = document.getElementById('selectAll');

  // Controla o visual e visibilidade da coluna de checkboxes à esquerda
  function alternarColunaCheck(mostrar) {
    colunasCheck.forEach(col => {
      if (mostrar) {
        col.classList.remove('d-none');
      } else {
        col.classList.add('d-none');
        // Desmarca os checkboxes caso o usuário feche a seleção
        const checkboxes = col.querySelectorAll('.form-check-input');
        checkboxes.forEach(cb => cb.checked = false);
      }
    });
    if (!mostrar && selectAll) selectAll.checked = false;
  }

  // Reseta o estado de destaque e tamanho dos botões circulares do cabeçalho
  function limparDestaques() {
    if (btnAtivarDetalhes && btnAtivarExclusao) {
      btnAtivarDetalhes.className = "btn btn-outline-primary rounded-circle d-inline-flex align-items-center justify-content-center me-2";
      btnAtivarExclusao.className = "btn btn-outline-danger rounded-circle d-inline-flex align-items-center justify-content-center";
    }
    if (btnAcaoDetalhes) btnAcaoDetalhes.classList.add('d-none');
    if (btnAcaoExcluir) btnAcaoExcluir.classList.add('d-none');
  }

  // Evento do botão Detalhes (Ícone do Olho no cabeçalho)
  if (btnAtivarDetalhes) {
    btnAtivarDetalhes.addEventListener('click', function () {
      if (this.classList.contains('btn-primary')) {
        limparDestaques();
        alternarColunaCheck(false);
      } else {
        limparDestaques();
        this.className = "btn btn-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-2";
        alternarColunaCheck(true);
        if (btnAcaoDetalhes) btnAcaoDetalhes.classList.remove('d-none');
      }
    });
  }

  // Evento do botão Excluir (Ícone da Lixeira no cabeçalho)
  if (btnAtivarExclusao) {
    btnAtivarExclusao.addEventListener('click', function () {
      if (this.classList.contains('btn-danger')) {
        limparDestaques();
        alternarColunaCheck(false);
      } else {
        limparDestaques();
        this.className = "btn btn-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center";
        alternarColunaCheck(true);
        if (btnAcaoExcluir) btnAcaoExcluir.classList.remove('d-none');
      }
    });
  }

  // Selecionar ou desmarcar todos os checkboxes de vez através do cabeçalho
  if (selectAll) {
    selectAll.addEventListener('change', function () {
      const checkboxes = document.querySelectorAll('.check-maquina');
      checkboxes.forEach(cb => cb.checked = this.checked);
    });
  }

  // REGRA DE UX: Mapeia as máquinas selecionadas e filtra o Modal de Detalhes
  if (btnAcaoDetalhes) {
    btnAcaoDetalhes.addEventListener('click', function () {
      // 1. Captura os checkboxes que o operador marcou na tabela
      const marcados = document.querySelectorAll('.check-maquina:checked');

      // 2. Cria um array dinâmico apenas com os IDs selecionados
      let idsSelecionados = [];
      marcados.forEach(cb => idsSelecionados.push(cb.value));

      // 3. Pega todos os itens pré-renderizados pelo Twig no modal
      const blocosDetalhes = document.querySelectorAll('.detalhe-maquina-item');

      // 4. Varre os blocos escondidos e decide quem deve aparecer
      blocosDetalhes.forEach(bloco => {
        const idBloco = bloco.getAttribute('data-id-maquina');

        // Se o ID do bloco do Twig foi marcado, remove o d-none, senão oculta
        if (idsSelecionados.includes(idBloco)) {
          bloco.classList.remove('d-none');
        } else {
          bloco.classList.add('d-none');
        }
      });
    });
  }
});