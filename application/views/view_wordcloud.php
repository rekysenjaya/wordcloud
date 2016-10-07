<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Upload File Excel</title>
        <link href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>assets/css/bootstrap-responsive.min.css" rel="stylesheet">
        <script defer src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
        <script defer src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
        <script defer src="<?php echo base_url(); ?>assets/js/wordcloud2.js"></script>
        <script>

            var examples = {
                'web-tech': {
                    list: '<?php echo $word;?>',
                    option: '{\n' +
                            '  gridSize: 18,\n' +
                            '  weightFactor: 3,\n' +
                            '  fontFamily: \'Finger Paint, cursive, sans-serif\',\n' +
                            '  color: \'#f0f0c0\',\n' +
                            '  hover: window.drawBox,\n' +
                            '  click: function(item) {\n' +
                            '    alert(item[0] + \': \' + item[1]);\n' +
                            '  },\n' +
                            '  backgroundColor: \'#001f00\'\n' +
                            '}',
                    fontCSS: 'https://fonts.googleapis.com/css?family=Finger+Paint'
                }
            };
        </script>
        <script defer src="<?php echo base_url(); ?>assets/js/index.js"></script>

        <style>

            *[hidden] {
                display: none;
            }

            #canvas-container {
                overflow-x: auto;
                overflow-y: visible;
                position: relative;
                margin-top: 20px;
                margin-bottom: 20px;
            }
            .canvas {
                display: block;
                position: relative;
                overflow: hidden;
            }

            .canvas.hide {
                display: none;
            }

            #html-canvas > span {
                transition: text-shadow 1s ease, opacity 1s ease;
                -webkit-transition: text-shadow 1s ease, opacity 1s ease;
                -ms-transition: text-shadow 1s ease, opacity 1s ease;
            }

            #html-canvas > span:hover {
                text-shadow: 0 0 10px, 0 0 10px #fff, 0 0 10px #fff, 0 0 10px #fff;
                opacity: 0.5;
            }

            #box {
                pointer-events: none;
                position: absolute;
                box-shadow: 0 0 200px 200px rgba(255, 255, 255, 0.5);
                border-radius: 50px;
                cursor: pointer;
            }

            textarea {
                height: 20em;
            }
            #config-option {
                font-family: monospace;
            }
            select { width: 100%; }

            #loading {
                animation: blink 2s infinite;
                -webkit-animation: blink 2s infinite;
            }
            @-webkit-keyframes blink {
                0% { opacity: 1; }
                100% { opacity: 0; }
            }
            @keyframes blink {
                0% { opacity: 1; }
                100% { opacity: 0; }
            }

        </style>
    </head>
    <body>
        <div class="container">
            <form id="form" method="get" action="">
                <div class="row">
                    <div class="span12" id="canvas-container">
                        <canvas id="canvas" class="canvas"></canvas>
                        <div id="html-canvas" class="canvas hide" style="width: 80%"></div>
                    </div>
                    <div class="span6" style="padding-bottom: 20px">
                        <button style="display: none" class="btn btn-primary" type="submit">Run</button>
                        <a class="btn btn-warning" id="btn-save" href="#" download="wordcloud.png" title="Save canvas">Save Image</a>
                        <a class="btn btn-info" href="<?php echo base_url();?>wordcloud">Upload File Excel</a>
                        <span id="loading" hidden>......</span>
                    </div>
                </div>
                <div class="tabbable" style="display: none">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab-list" data-toggle="tab">List</a></li>
                        <li><a href="#tab-config" data-toggle="tab">Configuration</a></li>
                        <li><a href="#tab-dim" data-toggle="tab">Dimension</a></li>
                        <li><a href="#tab-mask" data-toggle="tab">Mask image</a></li>
                        <li><a href="#tab-webfont" data-toggle="tab">Web Font</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-list">
                            <textarea id="input-list" placeholder="Put your list here." rows="2" cols="30" class="span12"></textarea>
                        </div>
                        <div class="tab-pane" id="tab-config">
                            <label>Options as a literal Javascript object</label>
                            <textarea id="config-option" placeholder="Put your literal option object here." rows="2" cols="30" class="span12">
                                
                            </textarea>
                            <span class="help-block">See 
                                <a href="https://github.com/timdream/wordcloud2.js/blob/gh-pages/API.md">API</a>
                                document for available options.
                            </span>
                        </div>
                        <div class="tab-pane" id="tab-dim">
                            <label for="config-width">Width</label>
                            <div class="input-append">
                                <input type="number" id="config-width" class="input-small" min="1">
                                <span class="add-on">px</span>
                            </div>
                            <span class="help-block">Leave blank to use page width.</span>
                            <label for="config-height">Height</label>
                            <div class="input-append">
                                <input type="number" id="config-height" class="input-small" min="1">
                                <span class="add-on">px</span>
                            </div>
                            <span class="help-block">Leave blank to use 0.65x of the width.</span>
                            <label for="config-height">Device pixel density (<span title="Dots per 'px' unit">dppx</span>)</label>
                            <div class="input-append">
                                <input type="number" id="config-dppx" class="input-mini" min="1" value="1" required>
                                <span class="add-on">x</span>
                            </div>
                            <span class="help-block">Adjust the weight, grid size, and canvas pixel size for high pixel density displays.</span>
                        </div>
                        <div class="tab-pane" id="tab-mask">
                            <label for="config-mask">Image mask</label>
                            <input type="file" id="config-mask"><button id="config-mask-clear" class="btn" type="button">Clear</button>
                            <span class="help-block">A silhouette image which the white area will be excluded from drawing texts. The <code>shape</code> option will continue to apply as the shape of the cloud to grow.</span>
                            <span class="help-block">When there is an image set, <code>clearCanvas</code> will be set to <code>false</code>.</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </body>
</html>
