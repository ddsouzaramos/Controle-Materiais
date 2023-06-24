<?php
$data['titulo_pagina'] = 'Usuários';
echo view('base/header.php', $data);
?>

<table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Código</th>
            <th>Nome</th>
            <th>Usuário</th>
            <th style="width:100px;">Ação</th>
        </tr>
    </thead>
</table>

<div class="modal fade" id="modal_form">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Large Modal</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group">
                        <label for="nome_completo">Nome Completo</label>
                        <input type="text" class="form-control" id="nome_completo" name="nome_completo" maxlength="60" placeholder="Nome Completo">
                        <div class="validate" id="nome_completo_msg"></div>
                    </div>
                    <div class="form-group">
                        <label for="usuario">Usuário</label>
                        <input type="text" class="form-control col-6" id="usuario" name="usuario" maxlength="60" placeholder="Nome do usuário">
                        <div class="validate" id="usuario_msg"></div>
                    </div>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="alterar_senha" name="alterar_senha">
                        <label class="custom-control-label" for="alterar_senha">Alterar Senha</label>
                    </div>
                    <div class="form-group">
                        <label for="senha">Senha</label>
                        <input type="password" class="form-control col-6" id="senha" name="senha" placeholder="Senha">
                        <div class="validate" id="senha_msg"></div>
                    </div>
                    <div class="form-group">
                        <label for="confirmacao_senha">Confirmação de Senha</label>
                        <input type="password" class="form-control col-6" id="confirmacao_senha" name="confirmacao_senha" placeholder="Confirmação de Senha">
                        <div class="validate" id="confirmacao_senha_msg"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" id="btnSalvar" onclick="save('<?= base_url('usuarios/salvar') ?>')">Salvar</button>
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
        load_usuarios();
    });

    $("#alterar_senha").click(function() {
        if($("#alterar_senha").prop("checked")) {
            $("#senha").prop("disabled", false);
            $("#confirmacao_senha").prop("disabled", false);
        }
        else {
            $("#senha").prop("disabled", true);
            $("#confirmacao_senha").prop("disabled", true);
        }
    });

    ///////////////////////////
    function load_usuarios() {
    ///////////////////////////

        table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            order: [[1, "asc"]],
            ajax: "<?= base_url('/usuarios/listar'); ?>",
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
                titleAttr: 'Novo Usuário',
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
        $('#form')[0].reset();
        remove_validate();

        $("#usuario").attr("readonly", true);
        $("#alterar_senha").attr("disabled", false);
        $("#senha").attr("disabled", true);
        $("#confirmacao_senha").attr("disabled", true);

        //Ajax Load data from ajax
        $.ajax({
            url : "<?= base_url('/usuarios/editar') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('#id').val(data.id_usuario);
                $('#nome_completo').val(data.nome_completo);
                $('#usuario').val(data.usuario);
                $('#modal_form').modal('show');
                $('.modal-title').text('Editar Processo');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                bootbox.alert('Erro ao recuperar os dados o servidor!');
            }
        });
    }

    /////////////////
    function add() {
    /////////////////
        remove_validate();
        $("#usuario").attr("readonly", false);
        $("#alterar_senha").attr("disabled", true);
        $("#senha").attr("disabled", false);
        $("#confirmacao_senha").attr("disabled", false);
        $('#form')[0].reset();
        $('#id').val('');
        $('#modal_form').modal('show');
        $('.modal-title').text('Novo Registro');
    }

</script>

<?php
echo view('base/footer.php');
?>