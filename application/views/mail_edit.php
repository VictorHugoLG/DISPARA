	<form method="post" action="<?php echo site_url('mail/save'); ?>">
        <h4>Editor de campanhas</h4>
	    <input placeholder="Nome da campanha" id="name" type="text" name="mail[name]" value="<?php echo set_value('mail[name]'); ?>" required>
        <br>
        <input placeholder="Data inicial" id="dt_begin" type="text" class="date" name="mail[dt_begin]" value="<?php echo set_value('mail[dt_begin]'); ?>" required>
		<br>
        <input placeholder="Data final" id="dt_end" type="text" class="date" name="mail[dt_end]" value="<?php echo set_value('mail[dt_end]'); ?>" required>
    	<br>
        <textarea id="sms" placeholder="Mensagem SMS" name="mail[sms]"><?php echo set_value('mail[sms]'); ?></textarea>
        <br>
        <input placeholder="Assunto do email" id="subject" type="text" name="mail[subject]" value="<?php echo set_value('mail[subject]'); ?>">

		<!-- Gets replaced with TinyMCE, remember HTML in a textarea should be encoded -->
		<div>
			<textarea id="html" class="mceEditor" name="mail[html]" style="width: 100%"><?php echo htmlspecialchars_decode(set_value('mail[html]')); ?></textarea>
		</div>

		<br>
        <div class="btn-group">
            <button type="submit" class="btn btn-primary">
                <i class="icon-save icon-white"></i> Salvar
            </button>
            <button type="reset" class="btn">
                <i class="icon-undo icon-white"></i> Resetar
            </button>
        </div>
        
        <script type="text/javascript">
                $(function()
                {
                        if(!Modernizr.input.placeholder)
                        {
                                $('[placeholder]').focus(function() {
                                  var input = $(this);
                                  if (input.val() == input.attr('placeholder')) {
                                        input.val('');
                                        input.removeClass('placeholder');
                                  }
                                }).blur(function() {
                                  var input = $(this);
                                  if (input.val() == '' || input.val() == input.attr('placeholder')) {
                                        input.addClass('placeholder');
                                        input.val(input.attr('placeholder'));
                                  }
                                }).blur();
                                $('[placeholder]').parents('form').submit(function() {
                                  $(this).find('[placeholder]').each(function() {
                                        var input = $(this);
                                        if (input.val() == input.attr('placeholder')) {
                                          input.val('');
                                        }
                                  })
                                });
                        }

                        //if (!Modernizr.inputtypes.date)
                        //$('input[type=date]').datepicker({ changeYear: true, dateFormat: 'yy-mm-dd'});
                        $('input.date').datepicker({ changeYear: true, dateFormat: 'yy-mm-dd'});

                        if (document.location.protocol == 'file:')
                                alert("Might not work properly on the local file system due to security settings in your browser. Please use a real webserver.");
                });
        </script>

        <!-- TinyMCE -->
        <script type="text/javascript" src="<?php echo base_url('resources/js/tinymce/jscripts/tiny_mce/tiny_mce.js'); ?>"></script>
        <script type="text/javascript">
                tinyMCE.init({
                        // General options
                        language : "pt",
                        mode : "specific_textareas",
                        editor_selector : "mceEditor",
                        theme : "advanced",
                        plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave,visualblocks,tretaimgupload",

                        // Theme options
                        theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
                        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,tretaimgupload,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
                        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
                        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft,visualblocks",
                        theme_advanced_toolbar_location : "top",
                        theme_advanced_toolbar_align : "left",
                        theme_advanced_statusbar_location : "bottom",
                        theme_advanced_resizing : true,

                        // Example content CSS (should be your site CSS)
                        content_css : "css/content.css",

                        // Drop lists for link/image/media/template dialogs
                        template_external_list_url : "lists/template_list.js",
                        external_link_list_url : "lists/link_list.js",
                        external_image_list_url : "lists/image_list.js",
                        media_external_list_url : "lists/media_list.js",

                        // Style formats
                        style_formats : [
                                {title : 'Bold text', inline : 'b'},
                                {title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
                                {title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
                                {title : 'Example 1', inline : 'span', classes : 'example1'},
                                {title : 'Example 2', inline : 'span', classes : 'example2'},
                                {title : 'Table styles'},
                                {title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
                        ],

                        // Replace values for the template plugin
                        template_replace_values : {
                                username : "Some User",
                                staffid : "991234"
                        }
                });
        </script>
        <!-- Some integration calls -->
        <!--
        <a href="javascript:;" onclick="tinyMCE.get('elm1').show();return false;">[Show]</a>
        <a href="javascript:;" onclick="tinyMCE.get('elm1').hide();return false;">[Hide]</a>
        <a href="javascript:;" onclick="tinyMCE.get('elm1').execCommand('Bold');return false;">[Bold]</a>
        <a href="javascript:;" onclick="alert(tinyMCE.get('elm1').getContent());return false;">[Get contents]</a>
        <a href="javascript:;" onclick="alert(tinyMCE.get('elm1').selection.getContent());return false;">[Get selected HTML]</a>
        <a href="javascript:;" onclick="alert(tinyMCE.get('elm1').selection.getContent({format : 'text'}));return false;">[Get selected text]</a>
        <a href="javascript:;" onclick="alert(tinyMCE.get('elm1').selection.getNode().nodeName);return false;">[Get selected element]</a>
        <a href="javascript:;" onclick="tinyMCE.execCommand('mceInsertContent',false,'<b>Hello world!!</b>');return false;">[Insert HTML]</a>
        <a href="javascript:;" onclick="tinyMCE.execCommand('mceReplaceContent',false,'<b>{$selection}</b>');return false;">[Replace selection]</a>
        -->
        <!-- /TinyMCE -->
        <?php
            if (!empty($mail_data))
            {
                $mail_data->html = str_replace('"', '\"', preg_replace('/\s/',' ',$mail_data->html));
                echo'
                    <script type="text/javascript">
                        $(function()
                        {
                            $("#name").val("'.$mail_data->name.'");
                            $("#html").val("'.$mail_data->html.'");
                            $("#subject").val("'.$mail_data->subject.'");
                            $("#dt_begin").val("'.$mail_data->dt_begin.'");
                            $("#dt_end").val("'.$mail_data->dt_end.'");
                            $("#sms").val("'.$mail_data->sms.'");
                        });
                    </script>';
                if (empty($copy))
                    echo '<input type="hidden" name="mail[id]" value="'.$mail_data->id.'">';
            }
        ?>
	</form>