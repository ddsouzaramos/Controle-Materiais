<?php
$data['titulo_pagina'] = 'Entradas';
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
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="exibir_saldo_zero" id="exibir_saldo_zero">
                        <label class="form-check-label" for="exibir_saldo_zero"><strong>Exibir entradas com saldo zerado</strong></label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Código da Entrada</label>
                    <input type="text" class="form-control" name="id_entrada_filtro" id="id_entrada_filtro"/>
                    <div class="validate" id="id_entrada_filtro"></div>
                </div>
                <div class="form-group">
                    <label>Período de:</label>
                    <input type="date" class="form-control" name="data_entrada_de_filtro" id="data_entrada_de_filtro"/>
                    <div class="validate" id="data_entrada_de_filtro"></div>
                </div>
                <div class="form-group">
                    <label>Período até:</label>
                    <input type="date" class="form-control" name="data_entrada_ate_filtro" id="data_entrada_ate_filtro"/>
                    <div class="validate" id="data_entrada_ate_filtro"></div>
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
                <button type="button" class="btn btn-primary" onclick="filtrar_entradas()"><i class="fas fa-filter"></i> Filtrar</button>
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
            <th>Cód.Ent.</th>
            <th>Ent.Origem</th>
            <th>Cód.Ent./Seq.</th>
            <th>Dt. Entrada</th>
            <th>Fornecedor</th>
            <th>Material</th>
            <th>Peso Bruto (KG)</th>
            <th>Peso Limpo (KG)</th>
            <th>Peso Disponível (KG)</th>
            <th style="width:200px;">Ação</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
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
                        <label>Data da Entrada</label>
                        <input type="date" class="form-control" name="data_entrada" id="data_entrada"/>
                        <div class="validate" id="data_entrada_msg"></div>
                    </div>
                    <div class="form-group">
                        <label>Fornecedor</label>
                        <select id="id_fornecedor" name="id_fornecedor" class="form-control">
                        </select>
                        <div class="validate" id="id_fornecedor_msg"></div>
                    </div>
                    <div class="form-group">
                        <label>Tipo de Material</label>
                        <select id="id_tipo_material" name="id_tipo_material" class="form-control">
                        </select>
                        <div class="validate" id="id_tipo_material_msg"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" id="btnSalvar" onclick="save('<?= base_url('entradas/salvar') ?>')">Salvar</button>
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
        load_entradas("<?= base_url('/entradas/listar') ?>");
        carrega_fornecedores();
        carrega_fornecedores_filtro();
        carrega_tipo_material();
        carrega_tipo_material_filtro();
        $("#card_filtro").CardWidget('collapse');
    });

    //////////////////////////////
    function filtrar_entradas() {
    //////////////////////////////
        table.ajax.reload();
    }

    /////////////////////////////
    function limpar_filtros() {
    /////////////////////////////
        $('#exibir_saldo_zero').prop( "checked", false);
        $('#id_entrada_filtro').val('');
        $('#data_entrada_de_filtro').val('');
        $('#data_entrada_ate_filtro').val('');

        var $select_id_fornecedor_filtro = $('#id_fornecedor_filtro').selectize();
        var control_id_fornecedor_filtro = $select_id_fornecedor_filtro[0].selectize;
        control_id_fornecedor_filtro.clear();

        var $select_id_tipo_material_filtro = $('#id_tipo_material_filtro').selectize();
        var control_id_tipo_material_filtro = $select_id_tipo_material_filtro[0].selectize;
        control_id_tipo_material_filtro.clear();

        $("#card_filtro").CardWidget('collapse');
        table.ajax.reload();
    }

    /////////////////////////////
    function load_entradas(url) {
    /////////////////////////////

/*
    $(document).ready(function() {
 var collapsedGroups = {};

    var table = $('#example').DataTable({
      order: [[2, 'asc']],
      rowGroup: {
        // Uses the 'row group' plugin
        dataSrc: 2,
        startRender: function (rows, group) {
            var collapsed = !!collapsedGroups[group];

            rows.nodes().each(function (r) {
                r.style.display = collapsed ? 'none' : '';
            });

            // Add category name to the <tr>. NOTE: Hardcoded colspan
            return $('<tr/>')
                .append('<td colspan="8">' + group + ' (' + rows.count() + ')</td>')
                .attr('data-name', group)
                .toggleClass('collapsed', collapsed);
        }
      }
    });

   $('#example tbody').on('click', 'tr.group-start', function () {
        var name = $(this).data('name');
        collapsedGroups[name] = !collapsedGroups[name];
        table.draw(false);
    });

});
*/
var collapsedGroups = {};
        table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            filter: false,
            order: [[0, 'desc']],
            rowGroup: {
                // Uses the 'row group' plugin
                dataSrc: 1,
                startRender: function (rows, group) {
                    var collapsed = !!collapsedGroups[group];

                    rows.nodes().each(function (r) {
                        r.style.display = collapsed ? 'none' : '';
                    });
                    // Add category name to the <tr>. NOTE: Hardcoded colspan
                    return $('<tr/>')
                        .append('<td colspan="8">' + group + ' (' + rows.count() + ')</td>')
                        .attr('data-name', group)
                        .toggleClass('collapsed', collapsed);
                }
            },
            ajax: {
                "url": url,
                data: function (d) {
                    d.exibir_saldo_zero = $('#exibir_saldo_zero').is(":checked") ? true : false;
                    d.id_entrada_filtro = $('#id_entrada_filtro').val();
                    d.data_entrada_de_filtro = $('#data_entrada_de_filtro').val();
                    d.data_entrada_ate_filtro = $('#data_entrada_ate_filtro').val();
                    d.id_fornecedor_filtro = $('#id_fornecedor_filtro').val();
                    d.id_tipo_material_filtro = $('#id_tipo_material_filtro').val();
                },
            },
            language: {
                url: "<?= base_url('/assets/plugins/DataTables/portugues.json'); ?>"
            },
            dom: 'Bfrtlip',
            columnDefs: [
                {
                    visible: false,
                    searchable: false,
                    targets: [0, 1]
                },
                {
                    orderable: false,
                    targets: [-1]
                },
                {
                    className: 'text-right',
                    targets: [4, 5, 6, 7]
                }
            ],
            buttons: [{
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel" aria-hidden="true"></i>' + '',
                titleAttr: 'Exportar para Excel',
                customize: function( xlsx, row ) {
                            var sheet = xlsx.xl.worksheets['sheet1.xml'];

                            $('row c[r^="E"]', sheet).each( function () {
                            // Get the value
                            //$(this).attr( 's', '20' );
                            $('is t', this).text().replace('.', '');
                            //console.log(texto.replace('.', ''));
                            });

                            //$('row c[r^="E"]', sheet).attr('s', 52);
                }
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
                titleAttr: 'Nova Entrada',
                action: function () {
                    add();
                }
            },
            ],
        });

        $('#table tbody').on('click', 'tr.group-start', function () {
            alert('ó eu aqui');
            var name = $(this).data('name');
            collapsedGroups[name] = !collapsedGroups[name];
            table.draw(false);
        });

    }

    ////////////////
    function add() {
    ////////////////
        remove_validate();
        clear_selectize('id_fornecedor');
        clear_selectize('id_tipo_material');
        $('#form')[0].reset();
        $('#id').val('');

        //SETA DATA ATUAL NA DATA DA ENTRADA
        var today = moment().format('YYYY-MM-DD');
        $("#data_entrada").val(today);

        $('#modal_form').modal('show');
        $('.modal-title').text('Nova Entrada de Materiais');
    }

    //////////////////////////////
    function editar_entrada(id) {
    //////////////////////////////
        $('#form')[0].reset();
        remove_validate();

        //Ajax Load data from ajax
        $.ajax({
            url : "<?= base_url('/entradas/editar') ?>" + "/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                console.log(data);

                $('#id').val(data.id_entrada);
                $("#data_entrada").val(data.data_entrada);

                //FORNECEDOR
                var $select_fornecedor = $('#id_fornecedor').selectize();
                var selectize_fornecedor = $select_fornecedor[0].selectize;
                selectize_fornecedor.setValue(data.id_fornecedor);

                //TIPO MATERIAL
                var $select_tipo_material = $('#id_tipo_material').selectize();
                var selectize_tipo_material = $select_tipo_material[0].selectize;
                selectize_tipo_material.setValue(data.id_tipo_material);

                $('#modal_form').modal('show');
                $('.modal-title').text('Editar Entrada');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                bootbox.alert('Erro ao recuperar os dados o servidor');
            }
        });
    }

    //////////////////////////////////
    function carrega_fornecedores() {
    /////////////////////////////////

        //SELECTIZE ITEM
        var element = $('#id_fornecedor');

        if(element[0].selectize) {
            element[0].selectize.destroy();
        }

        element.selectize({
            labelField: 'nome',
            valueField: 'id_fornecedor',
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
                            'id_fornecedor':item.id_fornecedor,
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
            valueField: 'id_tipo_material_filtro',
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
                            'id_tipo_material_filtro':item.id_tipo_material,
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

</script>

<?php
echo view('base/footer.php');
?>