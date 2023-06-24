<?php
$data['titulo_pagina'] = 'Pesagem de Materiais';
echo view('base/header.php', $data);
?>

<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Dados da Entrada</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">

        <!-- Main content -->
        <div class="invoice p-3 mb-3">
            <!-- title row -->
            <div class="row">
                <div class="col-4">
                    <h4>
                        <small>Código Entrada: <strong><?= $id_entrada ?></strong></small>
                    </h4>
                </div>
                <!-- /.col -->
                <div class="col-8">
                    <h4>
                        <small class="float-right">Data da Entrada: <strong><?= $data_entrada ?></strong></small>
                    </h4>
                </div>
                <!-- /.col -->
            </div>
            <!-- info row -->
            <div class="row invoice-info">
                <div class="col-sm-5 invoice-col">
                    Tipo de Material
                    <address>
                        <strong><?= $descricao ?></strong>
                    </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-5 invoice-col">
                    Fornecedor
                    <address>
                        <strong><?= $nome ?></strong>
                    </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-2 invoice-col">
                    Peso Bruto Total (KG)
                    <address>
                        <strong><spam class="peso_bruto"><?= $peso_bruto ?></spam></strong>
                    </address>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- Table row -->
            <div class="row">
                <div class="col-12 table-responsive">

                    <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Sequencia</th>
                                <th>Peso Bruto (KG)</th>
                                <th style="width:100px;">Ação</th>
                            </tr>
                        </thead>
                    </table>

                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

        </div>
        <!-- /.invoice -->

    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->

<div class="modal fade" id="modal_form">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form">
                    <input type="hidden" id="id_entrada" name="id_entrada" value="">
                    <div class="form-group">
                        <label for="peso_bruto">Peso (KG)</label>
                        <input type="number" class="form-control" id="peso_bruto" name="peso_bruto" maxlength="15" placeholder="Peso">
                        <div class="validate" id="peso_bruto_msg"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" id="btnSalvar" onclick="salvar_pesagem('<?= base_url('pesagens/salvar') ?>')">Salvar</button>
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
        load_pesagens("<?= base_url('/pesagens/listar') . '/' . $id_entrada ?>");
    });

    //var num = $('#peso_bruto').maskMoney({allowNegative: false, thousands:'.', precision: 3, decimal:',', affixesStay: false});

    //////////////////////////////
    function load_pesagens(url) {
    //////////////////////////////

        table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            filter: false,
            paginate: false,
            order: [[1, 'asc']],
            ajax: url,
            language: {
                url: "<?= base_url('/assets/plugins/DataTables/portugues.json'); ?>"
            },
            dom: 'Bfrtlip',
            columnDefs: [
                {
                    targets: 0,
                    visible: false
                },
                {
                    className: 'text-right',
                    targets: [1, 2]
                },
                {
                    orderable: false,
                    targets: -1
                }
            ],
            buttons: [
            {
                text: '<i class="fa fa-sync-alt" aria-hidden="true"></i>' + '',
                titleAttr: 'Atualizar Dados',
                action: function () {
                    reload_table();
                }
            },
            {
                text: '<i class="fa fa-plus" aria-hidden="true"></i>' + '',
                titleAttr: 'Nova Pesagem',
                action: function () {
                    adicionar_pesagem(<?= $id_entrada ?>);
                }
            },
            ],
        });
    }

    /////////////////////////////////////////
    function adicionar_pesagem(id_entrada) {
    /////////////////////////////////////////
        remove_validate();
        $('#form')[0].reset();
        $('.modal-title').text('Nova Pesagem');
        $('#id_entrada').val(id_entrada);
        $('#modal_form').modal('show');
    }

    ///////////////////////////////
    function salvar_pesagem(url) {
    ///////////////////////////////
        remove_validate();
        $('.form-group').removeClass('is-valid');
        $('.form-group').removeClass('is-invalid');
        $('#btnSalvar').text('Salvando...');
        $('#btnSalvar').attr('disabled', true);

        // ajax adding data to database
        $.ajax({
            url: url,
            type: "POST",
            data: $('#form').serialize(),
            success: function (data) {
                var retorno = JSON.parse(data.trim());

                // VERIFICA SE NÃO RETORNOU ERRO DA CAMADA DE VALIDAÇÃO
                $.each(get_validate(), function (key, value) {

                    // COLACA EM ARRAY OS CAMPOS QUE NÃO FORAM VALIDADOS
                    arr_fields = (Object.keys(retorno.erros_validacao));

                    if(arr_fields.indexOf(value.replace('_msg', '')) < 0) {
                        $('#' + value).addClass('valid-feedback');
                        $('#' + value.replace('_msg', '')).addClass('is-valid');
                    }
                });

                // ADICIONA A DESCRIÇÃO DO ERRO RETORNADO DA VALIDAÇÃO NO CAMPO
                $.each(retorno.erros_validacao, function (key, value) {
                    $('#' + key + '_msg').addClass('invalid-feedback');
                    $('#' + key + '_msg').html(value);
                    $('#' + key).addClass('is-invalid');
                });

                // SE RETORNOU COM SUCESSO
                if (retorno.status == true) {
                    $('#modal_form').modal('hide');
                    reload_table();
                    atualizar_peso_entrada();
                    if(retorno.mensagem != '') {
                        //bootbox.alert(retorno.mensagem);
                    }
                }

                $('#btnSalvar').text('Salvar');
                $('#btnSalvar').attr('disabled', false);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                bootbox.alert('Erro ao inserir/atualizar os dados no servidor! Tente novamente mais tarde.');
                $('#btnSalvar').text('Salvar');
                $('#btnSalvar').attr('disabled', false);
            }
        });
    }


    ///////////////////////////////////
    function atualizar_peso_entrada() {
    ///////////////////////////////////

        var url = "<?= base_url('entradas/buscar_peso_entrada') ?>" + "/" + $("#id_entrada").val();
        $.ajax({
                url: url,
                type: "POST",
                success: function (data) {
                    var retorno = JSON.parse(data.trim());
                    $(".peso_bruto").text(retorno.peso_bruto);
                }
        });
    }

</script>

<?php
echo view('base/footer.php');
?>