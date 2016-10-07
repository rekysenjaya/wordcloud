<html>
    <head>
        <title>Upload File Excel</title>
        <link href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>assets/css/bootstrap-responsive.min.css" rel="stylesheet">
        <script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
        <style>
            #loading {
                width: 100%;
                height: 100%;
                top: 0;
                left: 0;
                position: fixed;
                display: block;
                opacity: 0.7;
                background-color: #fff;
                z-index: 99;
                text-align: center;
            }

            #loading-image {
                position: absolute;
                top: 100px;
                z-index: 100;
            }

            .btn-primary {
                background-color: #428bca;
                border-color: #357ebd;
                color: #fff;
            }
            .btn {
                -moz-user-select: none;
                background-image: none;
                border: 1px solid transparent;
                border-radius: 4px;
                cursor: pointer;
                display: inline-block;
                font-size: 14px;
                font-weight: 400;
                line-height: 1.42857;
                margin-bottom: 0;
                padding: 6px 12px;
                text-align: center;
                vertical-align: middle;
                white-space: nowrap;
            }
        </style>
    </head>
    <body>
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <blockquote>
                    <p>Upload Excel to Word cloud Image PNG</p>
                    <footer>Max File Excel<cite title="Source Title"> 2MB</cite></footer>
                </blockquote>
                <br/>
                <div id="formUpload">
                    <label class="btn btn-primary">
                        Browse…
                        <input id="excelture" type="file" name="excel" style="display: none;">
                    </label>
                    <button id="upload" type="button" class="btn btn-info">Upload</button>
                </div>
                <div class="formSheert alert alert-info" style="display: none">
                    <strong>Submit!</strong> Excel convert to Word Cloud.
                </div>
                <div id="formSheert" style="display: none">
                    <!--<form class="form-horizontal" action="<?php // echo base_url();   ?>wordCloud/postExcel" method="post">-->
                    <!--<form class="form-horizontal" id="form">-->
                    <input id="id" name="id" type="hidden"/>
                    <input id="fileName" name="fileName" type="hidden"/>
                    <div class="form-group">
                        <!--<div class="col-sm-offset-2 col-sm-10">-->
                        <button type="submit" id="submit" class="btn btn-success">Submit</button>
                        <a class="btn btn-info" id="viewFile" href="">View File Excel</a>
                        <!--</div>-->
                    </div>
                    <div id="loading" style="display: none">
                        <img id="loading-image" src="http://code.jquery.com/mobile/1.3.1/images/ajax-loader.gif" alt="Loading..." />
                    </div>
                    <!--</form>-->
                    <br/>
                </div>
                <div id="wordcloud">

                </div>
            </div>
        </div>
        <script>
            $(document).ready(function () {

                $('#excelture').bind('change', function () {
                    if (this.files[0].size / 1024 / 1024 > 2) {
                        alert('Max Size file 2MB');
                        $("#excelture").val('');
                    }
                });

                $('#upload').on('click', function () {
                    var file_data = $('#excelture').prop('files')[0];
                    var form_data = new FormData();
                    form_data.append('file', file_data);
                    $.ajax({
                        url: "<?php echo base_url() ?>wordcloud/getupload",
                        dataType: 'text',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        success: function (data) {
                            var obj = jQuery.parseJSON(data);
                            if (obj.status == '1') {
                                $('#formUpload').hide(300);
                                $('#formSheert').show(300);
                                $('.formSheert').show(300);
                                $('#id').val(obj.id);
                                $('#fileName').val(obj.file);
                                $('#viewFile').attr('href', "<?php echo base_url(); ?>/assets/" + obj.file);
                                $('#formSheert').prepend('<div class="alert alert-success uploads"><strong>Success!</strong> Upload Excel</div>');
                                $('.uploads').delay(1000).hide(300);
                            } else {
                                alert(obj.message);
                            }
                        }
                    });
                });

                $('#submit').click(function () {
                    $('#loading').show(300);
                    window.location.replace("<?php echo base_url() ?>wordcloud/postExcel/" + $('#id').val());
                })

                $("body").on("submit", "#form", function (e) {
                    $('#loading').show(300);
                    e.preventDefault();
                    var form = $(e.target);
                    $.post("<?php echo base_url() ?>wordcloud/saveExcel", form.serialize(), function (res) {
                        try {
                            var obj = jQuery.parseJSON(res);
                            if (obj.status == '1') {
//                            $.get("<?php // echo base_url()         ?>wordcloud/wordCloud/" + $('#id').val(), function (htmls) {
//                                $('#wordcloud').html(htmls)
//                            });
                                window.location.replace("<?php echo base_url() ?>wordcloud/wordCloud/" + $('#id').val());
                                $('#formSheert').hide(300);
                            } else {
                                alert(obj.message);
                            }
                        } catch (err) {
                            alert("Error: " + err + ".");
                        } finally {
                            $('#loading').hide(300);
                        }
                        $('#loading').hide(300);
                    });
                });
            });
        </script>

    </body>
</html>