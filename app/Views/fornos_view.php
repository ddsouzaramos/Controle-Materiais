<?php
$data['titulo_pagina'] = 'Fornos';
echo view('base/header.php', $data);
?>

<table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Código</th>
            <th>Descrição</th>
            <th style="width:100px;">Ação</th>
        </tr>
    </thead>
</table>

<div class="modal fade" id="modal_form">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group">
                        <label for="descricao">Descrição</label>
                        <input type="text" class="form-control" id="descricao" name="descricao" maxlength="60" placeholder="Descrição do Material">
                        <div class="validate" id="descricao_msg"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" id="btnSalvar" onclick="save('<?= base_url('fornos/salvar') ?>')">Salvar</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<?php
echo view('base/scripts.php');
?>

<script>

    var table;

    $(document).ready(function() {
        load_fornos();
    });

    /////////////////////////
    function load_fornos() {
    /////////////////////////

        table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            order: [[1, "asc"]],
            ajax: "<?= base_url('/fornos/listar') ?>",
            language: {
                url: "<?= base_url('/assets/plugins/DataTables/portugues.json'); ?>"
            },
            dom: 'Bfrtlip',
            columnDefs: [{
                "targets": [-1],
                "orderable": false,
            },],
            buttons: [{
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel" aria-hidden="true"></i>' + '',
                titleAttr: 'Exportar para Excel'
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fa fa-file-pdf"></i>' + '',
                titleAttr: 'Exportar para PDF'
            },
            {
                text: '<i class="fa fa-sync-alt" aria-hidden="true"></i>' + '',
                titleAttr: 'Atualizar Dados',
                action: function () {
                    reload_table();
                }
            },
            {
                text: '<i class="fa fa-plus" aria-hidden="true"></i>' + '',
                titleAttr: 'Novo Forno',
                action: function () {
                    add();
                }
            },
            ],
        });
    }

    ////////////////////
    function edit(id) {
    ////////////////////
        save_method = 'update';
        $('#form')[0].reset();
        remove_validate();

        //Ajax Load data from ajax
        $.ajax({
            url : "<?= base_url('/fornos/editar') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('#id').val(data.id_forno);
                $('#descricao').val(data.descricao);
                $('#modal_form').modal('show');
                $('.modal-title').text('Editar Forno');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                bootbox.alert('Erro ao recuperar os dados o servidor');
            }
        });
    }

</script>

<?php
echo view('base/footer.php');
?>