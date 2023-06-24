<?php
$data['titulo_pagina'] = 'Itens do Forno';
echo view('base/header.php', $data);
?>

<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Processamento de Materiais</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <!-- Main content -->
        <div class="invoice p-3 mb-3">
            <!-- title row -->
            <div class="row">
                <div class="col-4">
                    <h4>
                        <small>Código Processo: <strong><?= $id_processo ?></strong></small>
                    </h4>
                </div>
                <!-- /.col -->
                <div class="col-8">
                    <h4>
                        <small class="float-right">Data do Processo: <strong><?= $data_processo ?></strong></small>
                    </h4>
                </div>
                <!-- /.col -->
            </div>
            <!-- info row -->
            <div class="row invoice-info">
                <div class="col-12 invoice-col">
                    Peso Bruto Total (KG)
                    <address>
                        <strong><spam class="float-right peso_bruto"><?= $peso_bruto ?></spam></strong>
                    </address>
                </div>
            </div>
            <!-- /.row -->

            <!-- Table row -->
            <div class="row">
                <div class="col-12 table-responsive">

                    <table id="table_itens" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Cód.Ent.</th>
                                <th>Ent.Origem</th>
                                <th>Data Entrada</th>
                                <th>Fornecedor</th>
                                <th>Tipo Material</th>
                                <th>Peso Utilizado (KG)</th>
                                <th>Ação</th>
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

            <div class="card card-secondary">
                <div class="card-body">
                    <div class="modal-body">
                        <input type="hidden" id="id" name="id">
                        <!-- CARD FILTRO -->
                        <div class="card card-default" id="card_filtro" name="card_filtro">
                            <div class="card-header">
                                <h3 class="card-title">Filtros</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Código da Entrada</label>
                                            <input type="text" class="form-control" name="id_entrada_filtro" id="id_entrada_filtro"/>
                                            <div class="validate" id="id_entrada_filtro"></div>
                                        </div>
                                        <div class="form-group">
                                            <label>Fornecedor</label>
                                            <select class="form-control" id="id_fornecedor_filtro" name="id_fornecedor_filtro">
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Tipo de Material</label>
                                            <select class="form-control" id="id_tipo_material_filtro" name="id_tipo_material_filtro">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.row -->
                                <div class="btn-toolbar" role="toolbar">
                                    <div class="btn-group mr-2" role="group">
                                        <button type="button" class="btn btn-primary" onclick="filtrar_entradas_disponiveis()"><i class="fas fa-filter"></i> Filtrar</button>
                                    </div>
                                    <div class="btn-group mr-2" role="group">
                                        <button type="button" class="btn btn-warning" onclick="limpar_filtro_entradas_disponiveis()"><i class="fas fa-eraser"></i> Limpar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 table-responsive">
                        <table id="table_entradas_selecao" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Cód.Ent</th>
                                    <th>Ent.Origem</th>
                                    <th>Data da Entrada</th>
                                    <th>Fornecedor</th>
                                    <th>Material</th>
                                    <th>Peso Disponível</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary" id="btnSalvar" onclick="salvar_item('<?= base_url('processos/salvar_item') ?>')">Selecionar</button>
                    </div>

                </div>

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
    var table_entradas_selecao;

    $(document).ready(function() {
        load_processos_itens("<?= base_url('/processos/listar_itens') . '/' . $id_processo ?>");
        load_entradas_disponiveis("<?= base_url('/processos/listar_entradas_disponiveis') ?>");
        carrega_fornecedores_filtro();
        carrega_tipo_material_filtro();
        $("#card_filtro").CardWidget('collapse');
    });

    /////////////////////////////////////
    function load_processos_itens(url) {
    /////////////////////////////////////

        table = $('#table_itens').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            filter: false,
            paginate: false,
            order: [[1, "asc"]],
            ajax: url,
            language: {
                url: "<?= base_url('/assets/plugins/DataTables/portugues.json'); ?>"
            },
            dom: 'Bfrtlip',
            columnDefs: [
                {
                    visible: false,
                    targets: 0
                },
                {
                    className: 'text-right',
                    targets: 5
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
                titleAttr: 'Novo Item em Forno',
                action: function () {
                    novo_item(<?= $id_processo ?>);
                }
            },
            ],
        });
    }

    //////////////////////////////////////////
    function load_entradas_disponiveis(url) {
    //////////////////////////////////////////

        table_entradas_selecao = $('#table_entradas_selecao').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            filter: false,
            pageLength: 5,
            lengthChange: false,
            order: [],
            ajax: {
                "url": url,
                data: function (d) {
                    d.id_entrada_filtro = $('#id_entrada_filtro').val();
                    d.id_fornecedor_filtro = $('#id_fornecedor_filtro').val();
                    d.id_tipo_material_filtro = $('#id_tipo_material_filtro').val();
                },
            },
            language: {
                url: "<?= base_url('/assets/plugins/DataTables/portugues.json'); ?>"
            },
            dom: 'rtlip',
            columnDefs: [
                {
                    orderable: false,
                    className: 'select-checkbox',
                    targets: 0
                },
                {
                    className: 'text-right',
                    targets: 5
                },
            ],
            select: {
                style: 'os',
                selector: 'td:first-child'
            },
        });
    }

    //////////////////////////////////
    function novo_item(id_processo) {
    //////////////////////////////////
        remove_validate();
        $('#id').val(id_processo);
        $('.modal-title').text('Nova Item do Processo');
        $('#id_processo').val(id_processo);
        $('#modal_form').modal('show');
    }

    ////////////////////////////////////////
    function carrega_fornecedores_filtro() {
    ////////////////////////////////////////

        //SELECTIZE ITEM
        var element = $('#id_fornecedor_filtro');

        if(element[0].selectize) {
            element[0].selectize.destroy();
        }

        element.selectize({
            labelField: 'nome',
            valueField: 'id_fornecedor_filtro',
            searchField: 'nome',
            sortField: 'nome',
            hideSelected: true,
            persist: false,
            create: false,
            placeholder: "Selecionar"
        });

        $.ajax({
            type:"POST",
            url: "<?= base_url('fornecedores/listar_select') ?>",
            data:{},
            success:function(result) {
                var selectize = element[0].selectize;
                var rs = JSON.parse(result.trim());
                var my_data = rs;
                if(my_data.length) {
                    for(var i=0;i < my_data.length;i++) {
                        var item = my_data[i];
                        var data = {
                            'id_fornecedor_filtro':item.id_fornecedor,
                            'nome':item.nome,
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

    /////////////////////////////////////////
    function carrega_tipo_material_filtro() {
    /////////////////////////////////////////

        //SELECTIZE ITEM
        var element = $('#id_tipo_material_filtro');

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

    /////////////////////////////////////////
    function filtrar_entradas_disponiveis() {
    /////////////////////////////////////////
        table_entradas_selecao.ajax.reload();
    }

    ///////////////////////////////////////////////
    function limpar_filtro_entradas_disponiveis() {
    ///////////////////////////////////////////////
        $('#id_entrada_filtro').val('');

        var $select_id_fornecedor_filtro = $('#id_fornecedor_filtro').selectize();
        var control_id_fornecedor_filtro = $select_id_fornecedor_filtro[0].selectize;
        control_id_fornecedor_filtro.clear();

        var $select_id_tipo_material_filtro = $('#id_tipo_material_filtro').selectize();
        var control_id_tipo_material_filtro = $select_id_tipo_material_filtro[0].selectize;
        control_id_tipo_material_filtro.clear();

        $("#card_filtro").CardWidget('collapse');
        table_entradas_selecao.ajax.reload();
    }

    /////////////////////////
    function salvar_item() {
    /////////////////////////
        var linha_selecionada = table_entradas_selecao.rows( { selected: true } ).data()[0];

        if(linha_selecionada != null) {

            bootbox.prompt({
                title: "Especifique o peso utilizado (KG)",
                centerVertical: true,
                inputType: 'number',
                buttons: {
                            confirm: {
                                label: 'OK'
                            },
                            cancel: {
                                label: 'Cancelar'
                            }
                        },
                callback: function(peso_utilizado) {
                    if(peso_utilizado) {
                        peso_utilizado = peso_utilizado.replace('.', '').replace(',', '.');
                        $.ajax({
                            type:"POST",
                            url: "<?= base_url('processos/salvar_item') ?>",
                            data:{
                                id_processo: $('#id').val(),
                                id_entrada: linha_selecionada[1],
                                peso_utilizado: peso_utilizado
                            },
                            success:function(result) {
                                var retorno = JSON.parse(result.trim());
                                //bootbox.alert(retorno.mensagem);
                                $('#modal_form').modal('toggle');
                                limpar_filtro_entradas_disponiveis();
                                table.ajax.reload();
                            },
                            error:function(error){
                                bootbox.alert(error.responseJSON.message);
                            }
                        });
                    }
                    else {
                        bootbox.alert('É necessário especificar o peso utilizado!');
                    }
                }
            });
            //$('.bootbox-input-number').css("text-align", "right");
            //$('.bootbox-input-number').attr('maxlength','11');
            //var num = $('.bootbox-input-number').maskMoney({allowNegative: false, thousands:'.', decimal:',', affixesStay: false});
        }
        else {
            bootbox.alert('É necessário selecionar uma entrada!');
        }

    }

</script>

<?php
echo view('base/footer.php');
?>