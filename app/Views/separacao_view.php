<?php
$data['titulo_pagina'] = 'Separação de Materiais';
echo view('base/header.php', $data);
?>

<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Dados da Entrada</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body" style="background-color: #F3F781;">

        <!-- Main content -->
        <div class="invoice p-3 mb-3" style="background-color: #F3F781;">
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
                    Peso Disponível (KG)
                    <address>
                        <strong><spam class="peso_disponivel"><?= $peso_disponivel ?></spam></strong>
                    </address>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- Table row -->
            <div class="row">
                <div class="col-12 table-responsive">

                    <table id="table" class="table table-striped table-bordered teste" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Codigo</th>
                                <th>Data</th>
                                <th>Material</th>
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
                    <input type="hidden" id="id_entrada_origem" name="id_entrada_origem" value="">
                    <div class="form-group">
                        <label>Tipo de Material</label>
                        <select id="id_tipo_material" name="id_tipo_material" class="form-control">
                        </select>
                        <div class="validate" id="id_tipo_material_msg"></div>
                    </div>
                    <div class="form-group">
                        <label for="peso_bruto">Peso (KG)</label>
                        <input type="number" class="form-control" id="peso_bruto" name="peso_bruto" maxlength="15" placeholder="Peso (KG)">
                        <div class="validate" id="peso_bruto_msg"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSalvar" onclick="salvar_separacao('<?= base_url('separacao/salvar') ?>')">Salvar</button>
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
        load_separacao("<?= base_url('/separacao/listar') . '/' . $id_entrada ?>");
        carrega_tipo_material();
    });

    //var num = $('#peso_bruto').maskMoney({allowNegative: false, thousands:'.', precision: 3, decimal:',', affixesStay: false});

    //////////////////////////////
    function load_separacao(url) {
    //////////////////////////////

        table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            filter: false,
            paginate: false,
            order: [[0, 'desc']],
            ajax: url,
            language: {
                url: "<?= base_url('/assets/plugins/DataTables/portugues.json'); ?>"
            },
            dom: 'Bfrtlip',
            columnDefs: [
                {
                    className: 'text-right',
                    targets: 3
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
                titleAttr: 'Nova Separação',
                action: function () {
                    nova_separacao(<?= $id_entrada ?>);
                }
            },
            ],
        });
    }

    /////////////////////////////////////
    function nova_separacao(id_entrada) {
    /////////////////////////////////////
        remove_validate();
        $('#form')[0].reset();
        $('.modal-title').text('Nova Separação');
        $('#id_entrada_origem').val(id_entrada);
        $('#modal_form').modal('show');
    }

    /////////////////////////////////
    function salvar_separacao(url) {
    /////////////////////////////////
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
    function carrega_tipo_material() {
    ///////////////////////////////////

        //SELECTIZE ITEM
        var element = $('#id_tipo_material');

        if(element[0].selectize) {
            element[0].selectize.destroy();
        }

        element.selectize({
            labelField: 'descricao',
            valueField: 'id_tipo_material',
            searchField: 'descricao',
            sortField: 'descricao',
            hideSelected: true,
            persist: false,
            create: false,
            placeholder: "Selecionar"
        });

        $.ajax({
            type:"POST",
            url: "<?= base_url('tipos_materiais/listar_select') ?>",
            data:{},
            success:function(result) {
                var selectize = element[0].selectize;
                var rs = JSON.parse(result.trim());
                var my_data = rs;
                if(my_data.length) {
                    for(var i=0;i < my_data.length;i++) {
                        var item = my_data[i];
                        var data = {
                            'id_tipo_material':item.id_tipo_material,
                            'descricao':item.descricao,
                        };
                        selectize.addOption(data);
                        selectize.refreshOptions();
                    }
                }
            },
            error:function(error){
                bootbox.alert(error.responseJSON.message);
            }
        });
    }

    ////////////////////////////////////
    function atualizar_peso_entrada() {
    ////////////////////////////////////

        var url = "<?= base_url('entradas/buscar_peso_entrada') ?>" + "/" + $("#id_entrada_origem").val();
        $.ajax({
                url: url,
                type: "POST",
                success: function (data) {
                    var retorno = JSON.parse(data.trim());
                    $(".peso_disponivel").text(retorno.peso_disponivel);
                }
        });
    }

</script>

<?php
echo view('base/footer.php');
?>