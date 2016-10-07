<html>
    <head>
        <title>Ajax CRUD with Bootstrap modals and Datatables</title>
        <link href="<?php echo base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/css/dataTables.bootstrap.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/css/bootstrap-datepicker3.min.css') ?>" rel="stylesheet">
        
        <script src="<?php echo base_url('assets/js/jquery-2.1.4.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/js/bootstrap.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/js/jquery.dataTables.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/js/dataTables.bootstrap.js') ?>"></script>
        <script src="<?php echo base_url('assets/js/bootstrap-datepicker.min.js') ?>"></script>
    </head> 
    <body>
        <div class="container">
            <h1 style="font-size:20pt">Ajax CRUD with Bootstrap modals and Datatables with Server side Validation</h1>
            <h3>Person Data</h3>
            <br />
            <button class="btn btn-success" onclick="add_crud()"><i class="glyphicon glyphicon-plus"></i> Add Person</button>
            <button class="btn btn-default" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
            <br />
            <br />
            <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Gender</th>
                        <th>Address</th>
                        <th>Date of Birth</th>
                        <th style="width:125px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Gender</th>
                        <th>Address</th>
                        <th>Date of Birth</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <script type="text/javascript">
                var save_method;
                var table;
                $(document).ready(function () {
                    table = $('#table').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "order": [],

                        "ajax": {
                            "url": "<?php echo base_url('crud/ajax_list') ?>",
                            "type": "POST"
                        },
                        "columnDefs": [
                            {
                                "targets": [-1],
                                "orderable": false,
                            },
                        ],
                    });
                    $('.datepicker').datepicker({
                        autoclose: true,
                        format: "yyyy-mm-dd",
                        todayHighlight: true,
                        orientation: "top auto",
                        todayBtn: true,
                        todayHighlight: true,
                    });
                    $("input").change(function () {
                        $(this).parent().parent().removeClass('has-error');
                        $(this).next().empty();
                    });
                    $("textarea").change(function () {
                        $(this).parent().parent().removeClass('has-error');
                        $(this).next().empty();
                    });
                    $("select").change(function () {
                        $(this).parent().parent().removeClass('has-error');
                        $(this).next().empty();
                    });
                });
                
                function add_crud()
                {
                    save_method = 'add';
                    $('#form')[0].reset();
                    $('.form-group').removeClass('has-error');
                    $('.help-block').empty();
                    $('#modal_form').modal('show');
                    $('.modal-title').text('Add Person');
                }

                function edit_crud(id)
                {
                    save_method = 'update';
                    $('#form')[0].reset();
                    $('.form-group').removeClass('has-error');
                    $('.help-block').empty();
                    $.ajax({
                        url: "<?php echo base_url('crud/ajax_edit/') ?>/" + id,
                        type: "GET",
                        dataType: "JSON",
                        success: function (data)
                        {
                            $('[name="id"]').val(data.id);
                            $('[name="firstName"]').val(data.firstName);
                            $('[name="lastName"]').val(data.lastName);
                            $('[name="gender"]').val(data.gender);
                            $('[name="address"]').val(data.address);
                            $('[name="dob"]').datepicker('update', data.dob);
                            $('#modal_form').modal('show');
                            $('.modal-title').text('Edit Person');
                        },
                        error: function ()
                        {
                            alert('Error get data from ajax');
                        }
                    });
                }

                function reload_table()
                {
                    table.ajax.reload(null, false);
                }

                function save()
                {
                    $('#btnSave').text('saving...');
                    $('#btnSave').attr('disabled', true);
                    var url;

                    if (save_method == 'add') {
                        url = "<?php echo base_url('crud/ajax_add') ?>";
                    } else {
                        url = "<?php echo base_url('crud/ajax_update') ?>";
                    }

                    $.ajax({
                        url: url,
                        type: "POST",
                        data: $('#form').serialize(),
                        dataType: "JSON",
                        success: function (data)
                        {

                            if (data.status)
                            {
                                $('#modal_form').modal('hide');
                                reload_table();
                            }
                            else
                            {
                                for (var i = 0; i < data.inputerror.length; i++)
                                {
                                    $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
                                    $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
                                }
                            }
                            $('#btnSave').text('save');
                            $('#btnSave').attr('disabled', false);
                        },
                        error: function ()
                        {
                            alert('Error adding / update data');
                            $('#btnSave').text('save');
                            $('#btnSave').attr('disabled', false);

                        }
                    });
                }

                function delete_crud(id)
                {
                    if (confirm('Are you sure delete this data?'))
                    {
                        $.ajax({
                            url: "<?php echo base_url('crud/ajax_delete') ?>/" + id,
                            type: "POST",
                            dataType: "JSON",
                            success: function ()
                            {
                                $('#modal_form').modal('hide');
                                reload_table();
                            },
                            error: function ()
                            {
                                alert('Error deleting data');
                            }
                        });
                    }
                }
        </script>

        <div class="modal fade" id="modal_form" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h3 class="modal-title">Person Form</h3>
                    </div>
                    <div class="modal-body form">
                        <form action="#" id="form" class="form-horizontal">
                            <input type="hidden" value="" name="id"/> 
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="control-label col-md-3">First Name</label>
                                    <div class="col-md-9">
                                        <input name="firstName" placeholder="First Name" class="form-control" type="text">
                                        <span class="help-block"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Last Name</label>
                                    <div class="col-md-9">
                                        <input name="lastName" placeholder="Last Name" class="form-control" type="text">
                                        <span class="help-block"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Gender</label>
                                    <div class="col-md-9">
                                        <select name="gender" class="form-control">
                                            <option value="">--Select Gender--</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                        </select>
                                        <span class="help-block"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Address</label>
                                    <div class="col-md-9">
                                        <textarea name="address" placeholder="Address" class="form-control"></textarea>
                                        <span class="help-block"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Date of Birth</label>
                                    <div class="col-md-9">
                                        <input name="dob" placeholder="yyyy-mm-dd" class="form-control datepicker" type="text">
                                        <span class="help-block"></span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>