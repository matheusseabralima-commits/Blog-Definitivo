<?php
// Este arquivo é o app/View/Elements/pagination-controls.ctp

// Verifica se há mais de uma página para exibir os controles
if ($this->Paginator->param('pageCount') > 1):
?>
<ul class="pagination">
    <?php
    // Link para a primeira página
    echo $this->Paginator->first('<< Primeira', array('class' => 'page-item'), null, array('class' => 'page-item disabled'));
    
    // Link para a página anterior
    echo $this->Paginator->prev('< Anterior', array('class' => 'page-item'), null, array('class' => 'page-item disabled'));

    // Links numéricos para as páginas
    echo $this->Paginator->numbers(array('separator' => '', 'class' => 'page-item', 'tag' => 'li', 'currentClass' => 'active'));

    // Link para a próxima página
    echo $this->Paginator->next('Próxima >', array('class' => 'page-item'), null, array('class' => 'page-item disabled'));

    // Link para a última página
    echo $this->Paginator->last('Última >>', array('class' => 'page-item'), null, array('class' => 'page-item disabled'));
    ?>
</ul>

<p class="mt-2 text-muted">
    <?php
    // Exibe o número da página atual, total de páginas e total de registros
    echo $this->Paginator->counter(array(
        'format' => __('Página {:page} de {:pages}, mostrando {:current} registros de um total de {:count}, iniciando no registro {:start}, terminando em {:end}.')
    ));
    ?>
</p>
<?php endif; ?>