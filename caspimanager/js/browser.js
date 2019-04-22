//<![CDATA[
	// KCFinder standalone
	function openKCFinder1(field) {
		window.KCFinder = {
			callBack: function(url) {
				window.KCFinder = null;
				field.value = url;
			}
		};
		window.open('browser/browse.php?type=files&dir=files', 'kcfinder_textbox',
			'status=0, toolbar=0, location=0, menubar=0, directories=0, ' +
			'resizable=1, scrollbars=0, width=700, height=500'
		);
	}
	
	// file browser
	function openKCFinder(field_name, url, type, win) {
		tinyMCE.activeEditor.windowManager.open({
			file: 'browser/browse.php?opener=tinymce&type=' + type,
			title: 'KCFinder',
			width: 700,
			height: 500,
			resizable: "yes",
			inline: true,
			close_previous: "yes",
			popup_css: false
		}, {
			window: win,
			input: field_name
		});
		return false;
	};

	// tiny MCE init
	tinyMCE.init({
		skin : "default",
		mode : "exact", //mode : "specific_textareas",
		editor_deselector : "mceNoEditor",
		theme : "advanced",
		theme_advanced_font_sizes: "6px,7px,8px,9px,10px,11px,12px,13px,14px,15px,16px,17px,18px,19px,20px,21px,22px,23px,24px,25px,26px,27px,28px,29px,30px,31px,32px,36px,38px,40px",
		font_size_style_values: "6px,7px,8px,9px,10px,11px,12px,13px,14px,15px,16px,17px,18px,19px,20px,21px,22px,23px,24px,25px,26px,27px,28px,29px,30px,31px,32px,36px,38px,40px",
		theme_advanced_fonts : "Andale Mono=andale mono,times;"+
                "Arial=arial,helvetica,sans-serif;"+
                "Arial Black=arial black,avant garde;"+
                "Book Antiqua=book antiqua,palatino;"+
                "Comic Sans MS=comic sans ms,sans-serif;"+
                "Courier New=courier new,courier;"+
                "Georgia=georgia,palatino;"+
                "Helvetica=helvetica;"+
                "Impact=impact,chicago;"+
                "Symbol=symbol;"+
                "Tahoma=tahoma,arial,helvetica,sans-serif;"+
                "Terminal=terminal,monaco;"+
                "Times New Roman=times new roman,times;"+
                "Trebuchet MS=trebuchet ms,geneva;"+
                "Verdana=verdana,geneva;"+
                "Webdings=webdings;"+
                "Wingdings=wingdings,zapf dingbats",
		elements: 'editor,editor1,editor2,editor3,editor4,editor5,editor6,editor7,editor8,editor8,editor9,editor10,editor11,editor12,editor13,editor14,editor15,editor16,editor17,editor18,editor19,editor20,editor21,editor22,editor23,editor24,editor25,editor26,editor27,editor28,editor29,editor30,editor31,editor32,editor33,editor34,editor35,editor36,editor37,editor38,editor39,editor40,editor41,editor42,editor43,editor44,editor45,editor46,editor47,editor48,editor49,editor50,editor51,editor52,editor53,editor54,editor55,editor56,editor57', // BU setri textarea editor olacaq
		plugins : "inlinepopups,advlink,advimage,contextmenu,media,paste,table",
		language : "en",
		dialog_type : "modal",
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,fontsizeselect,fontselect,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect",
		theme_advanced_buttons2 : "pastetext,pasteword,|,bullist,numlist,|,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,code,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,|,sub,sup,|,charmap,media",
		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : false,
		width : '100%',
		height : 500,
		relative_urls : true,
		remove_script_host : true,
		convert_urls : false,
		document_base_url : "",
		disk_cache : true,
		accessibility_warnings : false,
		file_browser_callback : "openKCFinder",

	});
//]]>