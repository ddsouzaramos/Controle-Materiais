<?php
$data['titulo_pagina'] = 'Forno';
echo view('base/header.php', $data);
?>

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
            <div class="col-md-6">
                <div class="form-group">
                    <label>Código do Processo</label>
                    <input type="text" class="form-control" name="id_processo_filtro" id="id_processo_filtro"/>
                    <div class="validate" id="id_processo_filtro"></div>
                </div>
                <div class="form-group">
                    <label>Período de:</label>
                    <input type="date" class="form-control" name="data_processo_de_filtro" id="data_processo_de_filtro"/>
                    <div class="validate" id="data_processo_de_filtro"></div>
                </div>
                <div class="form-group">
                    <label>Período até:</label>
                    <input type="date" class="form-control" name="data_processo_ate_filtro" id="data_processo_ate_filtro"/>
                    <div class="validate" id="data_processo_ate_filtro"></div>
                </div>
                <div class="form-group">
                    <label>Forno</label>
                    <select class="form-control" id="id_forno_filtro" name="id_forno_filtro">
                    </select>
                </div>
            </div>
        </div>
        <!-- /.row -->
        <div class="btn-toolbar" role="toolbar">
            <div class="btn-group mr-2" role="group">
                <button type="button" class="btn btn-primary" onclick="filtrar_processos()"><i class="fas fa-filter"></i> Filtrar</button>
            </div>
            <div class="btn-group mr-2" role="group">
                <button type="button" class="btn btn-warning" onclick="limpar_filtros()"><i class="fas fa-eraser"></i> Limpar</button>
            </div>
        </div>
    </div>
</div>

<table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Código</th>
            <th>Dt. Processo</th>
            <th>Forno</th>
            <th>Peso Processado (KG)</th>
            <th>Peso Saída (KG)</th>
            <th style="width:190px;">Ação</th>
        </tr>
    </thead>
</table>

<div id="modal_form" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
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
                        <label>Data do Processo</label>
                        <input type="date" class="form-control" name="data_processo" id="data_processo"/>
                        <div class="validate" id="data_processo_msg"></div>
                    </div>
                    <div class="form-group">
                        <label>Forno</label>
                        <select id="id_forno" name="id_forno" class="form-control">
                        </select>
                        <div class="validate" id="id_forno_msg"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" id="btnSalvar" onclick="salvar_processo('<?= base_url('processos/salvar') ?>')">Salvar</button>
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

    $(document).ready(function() {
        load_processos("<?= base_url('/processos/listar') ?>");
        carrega_fornos();
        carrega_fornos_filtro();
        $("#card_filtro").CardWidget('collapse');
    });

    //////////////////////////////
    function filtrar_processos() {
    //////////////////////////////
        table.ajax.reload();
    }

    /////////////////////////////
    function limpar_filtros() {
    /////////////////////////////
        $('#id_processo_filtro').val('');
        $('#data_processo_de_filtro').val('');
        $('#data_processo_ate_filtro').val('');
        $("#card_filtro").CardWidget('collapse');
        table.ajax.reload();
    }

    ///////////////////////////////
    function load_processos(url) {
    ///////////////////////////////

        table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            filter: false,
            order: [[0, 'desc']],
            ajax: {
                "url": url,
                data: function (d) {
                    d.id_processo_filtro = $('#id_processo_filtro').val();
                    d.data_processo_de_filtro = $('#data_processo_de_filtro').val();
                    d.data_processo_ate_filtro = $('#data_processo_ate_filtro').val();
                },
            },
            language: {
                url: "<?= base_url('/assets/plugins/DataTables/portugues.json'); ?>"
            },
            dom: 'Bfrtlip',
            columnDefs: [
                {
                    className: 'text-right',
                    targets: [3, 4]
                },
                {
                    orderable: false,
                    targets: -1
                }
            ],
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
                titleAttr: 'Novo Processo em Forno',
                action: function () {
                    add();
                }
            },
            ],
        });
    }

    ////////////////
    function add() {
    ////////////////
        remove_validate();
        clear_selectize('id_forno');
        $('#form')[0].reset();
        $('#id').val('');

        //SETA DATA ATUAL NA DATA DA ENTRADA
        var today = moment().format('YYYY-MM-DD');
        $("#data_processo").val(today);

        $('#modal_form').modal('show');
        $('.modal-title').text('Novo Processo de Materiais');
    }

    //////////////////////////////
    function editar_processo(id) {
    //////////////////////////////
        $('#form')[0].reset();
        remove_validate();

        //Ajax Load data from ajax
        $.ajax({
            url : "<?= base_url('/processos/editar') ?>" + "/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {

                $('#id').val(data.id_processo);
                $("#data_processo").val(data.data_processo);

                console.log();

                //FORNO
                var $select_forno = $('#id_forno').selectize();
                var selectize_forno = $select_forno[0].selectize;
                selectize_forno.setValue(data.id_forno);

                $('#modal_form').modal('show');
                $('.modal-title').text('Editar Processo');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                bootbox.alert('Erro ao recuperar os dados o servidor');
            }
        });
    }

    ////////////////////////////////
    function salvar_processo(url) {
    ////////////////////////////////
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
                    if(retorno.mensagem != '') {
                        bootbox.alert(retorno.mensagem);
                        carrega_fornos();
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

    ////////////////////////////
    function carrega_fornos() {
    ////////////////////////////

        //SELECTIZE ITEM
        var element = $('#id_forno');

        if(element[0].selectize) {
            element[0].selectize.destroy();
        }

        element.selectize({
            labelField: 'descricao',
            valueField: 'id_forno',
            searchField: 'descricao',
            sortField: 'descricao',
            hideSelected: true,
            persist: false,
            create: false,
            placeholder: "Selecionar"
        });

        $.ajax({
            type:"POST",
            url: "<?= base_url('fornos/listar_fornos_ativos') ?>",
            data:{},
            success:function(result) {
                var selectize = element[0].selectize;
                var rs = JSON.parse(result.trim());
                var my_data = rs;
                if(my_data.length) {
                    for(var i=0;i < my_data.length;i++) {
                        var item = my_data[i];
                        var data = {
                            'id_forno':item.id_forno,
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

    ///////////////////////////////////
    function carrega_fornos_filtro() {
    ///////////////////////////////////

        //SELECTIZE ITEM
        var element = $('#id_forno_filtro');

        if(element[0].selectize) {
            element[0].selectize.destroy();
        }

        element.selectize({
            labelField: 'descricao',
            valueField: 'id_forno_filtro',
            searchField: 'descricao',
            sortField: 'descricao',
            hideSelected: true,
            persist: false,
            create: false,
            placeholder: "Selecionar"
        });

        $.ajax({
            type:"POST",
            url: "<?= base_url('fornos/listar_select') ?>",
            data:{},
            success:function(result) {
                var selectize = element[0].selectize;
                var rs = JSON.parse(result.trim());
                var my_data = rs;
                if(my_data.length) {
                    for(var i=0;i < my_data.length;i++) {
                        var item = my_data[i];
                        var data = {
                            'id_forno_filtro':item.id_forno,
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

    ///////////////////////////////////////////
    function informar_peso_saida(id_processo) {
    ///////////////////////////////////////////

        bootbox.prompt({
            title: "Especifique o peso de saída (KG)",
            centerVertical: true,
            inputType: 'text',
            buttons: {
                            confirm: {
                                label: 'OK'
                            },
                            cancel: {
                                label: 'Cancelar'
                            }
                        },
            callback: function(peso_saida) {
                if(peso_saida != null)
                    peso_saida = peso_saida.replace('.', '').replace(',', '.');

                if(peso_saida > 0) {
                    $.ajax({
                        type:"POST",
                        url: "<?= base_url('processos/peso_saida') ?>",
                        data:{
                            id_processo: id_processo,
                            peso_saida: peso_saida
                        },
                        success:function(result) {
                            var retorno = JSON.parse(result.trim());
                            bootbox.alert(retorno.mensagem);
                            table.ajax.reload();
                        },
                        error:function(error){
                            bootbox.alert(error.responseJSON.message);
                        }
                    });

                }
                else {
                    bootbox.alert('Informe o peso de saída!');
                }
            }
        });
        $('.bootbox-input-text').css("text-align", "right");
        $('.bootbox-input-text').attr('maxlength','18');
        //var num = $('.bootbox-input-text').maskMoney({allowNegative: false, thousands:'.', precision: 3, decimal:',', affixesStay: false});
    }

    ////////////////////////////////////
    function remover_processo(url, id) {
    ////////////////////////////////////

        bootbox.confirm({
            message: "Deseja excluir o item de código: " + id + "?",
            buttons: {
                confirm: {
                    label: 'Excluir',
                    className: 'btn-primary',
                },
                cancel: {
                    label: 'Cancelar',
                    className: 'btn-danger',
                }
            },
            callback: function (result) {
                if (result) {
                    $.ajax({
                        url: url + "/" + id,
                        type: "POST",
                        dataType: "JSON",
                        success: function (data) {
                            $('#modal_form').modal('hide');
                            bootbox.alert('Registro excluído com sucesso!');
                            reload_table();
                            carrega_fornos();
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            bootbox.alert('Erro ao excluir o registro!');
                        }
                    });
                }
            }
        })
    }

</script>

<?php
echo view('base/footer.php');
?>