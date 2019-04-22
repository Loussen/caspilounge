$(document).ready(function()
{
	var first_selected=$(".left_switch").attr('id');
	if(first_selected!=='undefined')
	{
		first_selected=first_selected.substr(8);
		$('#tab_lang'+first_selected).addClass('active');	
		$('.tab_content').addClass('hide');
		$('#tab'+first_selected+'_content').removeClass('hide');
	}
	
	$("#hamisini_sec").click(function(){
		var all_check_changed=$("#all_check_changed").val();
		if(all_check_changed==0){
			all_check_changed=1;
			$("#link_url").val("-");
			var link_url=$("#link_url").val();
			$('td input:checkbox').each(function() {
				$(this).attr("checked", "checked");
				$(this).prop("checked", true);
				id=this.id;
				id=id.substr(5);
				link_url=link_url+id+"-";
			});
			$("#link_url").val(link_url);
		}
		else{
			all_check_changed=0;
			$('td input:checkbox').each(function() {
				$(this).removeAttr("checked", "checked");
				$(this).prop("checked", false);
			});
			
			$("#link_url").val("-");
		}
		$("#all_check_changed").val(all_check_changed);
	});
	
	$(".chbx_del").click(function(){
		var delete_text2=$("#delete_text2").val();
		var sual=confirm(delete_text2);
		if(!sual) return false;
		
		var link_url=$("#link_url").val();
		var current_link=$("#current_link").val();
		if(link_url.length>1) window.location.href = current_link+'&checkbox_del=1&checkboxes='+link_url;
	});
	
	$(".chbx_active").click(function(){
		var val=$(this).data("val");
		var special=parseInt($(this).data("special"));
		var link_url=$("#link_url").val();
		var current_link=$("#current_link").val();
		if(link_url.length>1) window.location.href = current_link+'&active='+val+'&checkboxes='+link_url+'&special='+special;
	});
	
});

// select checkbox multi
function chbx_(id){
	id=id.substr(5);
	if($("#chbx_"+id).is(":checked")){
		var link_url=$("#link_url").val();
		link_url=link_url+id+"-";
		$("#link_url").val(link_url);
	}
	else{
		var link_url=$("#link_url").val();
		link_url=link_url.replace("-"+id+"-","-");
		$("#link_url").val(link_url);
	}
}

function aktivlik(table,value,title)
{
	var id=value.substr(5);
	var new_title;
	var new_activlik;
	if(title=="Active") { new_title="Deactive"; new_activlik=0; } else { new_title="Active"; new_activlik=1; }
	$("#info_"+id).attr("src","images/"+new_activlik+"_lamp.png");
	$("#info_"+id).attr("title",new_title);
	$.post("jquery_post.php",{table:table,info_id:id,new_activlik:new_activlik},function(data){});

}

function tab_select(id)
{
	var idi=id.substr(8);	
	$('.left_switch').removeClass('active');
	$('#tab_lang'+idi).addClass('active');
	
	$('.tab_content').addClass('hide');
	$('#tab'+idi+'_content').removeClass('hide');
}

function show_flag(value)
{
	$("#flag_image").attr("src",value);
}

(function( $ ) {
    $.widget( "custom.combobox", {
      _create: function() {
        this.wrapper = $( "<span>" )
          .addClass( "custom-combobox" )
          .insertAfter( this.element );
 
        this.element.hide();
        this._createAutocomplete();
        this._createShowAllButton();
      },
 
      _createAutocomplete: function() {
        var selected = this.element.children( ":selected" ),
          value = selected.val() ? selected.text() : "";
 
        this.input = $( "<input>" )
          .appendTo( this.wrapper )
          .val( value )
          .attr( "title", "" )
          .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
          .autocomplete({
            delay: 0,
            minLength: 0,
            source: $.proxy( this, "_source" )
          })
          .tooltip({
            tooltipClass: "ui-state-highlight"
          });
 
        this._on( this.input, {
          autocompleteselect: function( event, ui ) {
            ui.item.option.selected = true;
            this._trigger( "select", event, {
              item: ui.item.option
            });
          },
 
          autocompletechange: "_removeIfInvalid"
        });
      },
 
      _createShowAllButton: function() {
        var input = this.input,
          wasOpen = false;
 
        $( "<a>" )
          .attr( "tabIndex", -1 )
          .attr( "title", "Show All Items" )
          .tooltip()
          .appendTo( this.wrapper )
          .button({
            icons: {
              primary: "ui-icon-triangle-1-s"
            },
            text: false
          })
          .removeClass( "ui-corner-all" )
          .addClass( "custom-combobox-toggle ui-corner-right" )
          .mousedown(function() {
            wasOpen = input.autocomplete( "widget" ).is( ":visible" );
          })
          .click(function() {
            input.focus();
 
            // Close if already visible
            if ( wasOpen ) {
              return;
            }
 
            // Pass empty string as value to search for, displaying all results
            input.autocomplete( "search", "" );
          });
      },
 
      _source: function( request, response ) {
        var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
        response( this.element.children( "option" ).map(function() {
          var text = $( this ).text();
          if ( this.value && ( !request.term || matcher.test(text) ) )
            return {
              label: text,
              value: text,
              option: this
            };
        }) );
      },
 
      _removeIfInvalid: function( event, ui ) {
 
        // Selected an item, nothing to do
        if ( ui.item ) {
          return;
        }
 
        // Search for a match (case-insensitive)
        var value = this.input.val(),
          valueLowerCase = value.toLowerCase(),
          valid = false;
        this.element.children( "option" ).each(function() {
          if ( $( this ).text().toLowerCase() === valueLowerCase ) {
            this.selected = valid = true;
            return false;
          }
        });
 
        // Found a match, nothing to do
        if ( valid ) {
          return;
        }
 
        // Remove invalid value
        this.input
          .val( "" )
          .attr( "title", value + " didn't match any item" )
          .tooltip( "open" );
        this.element.val( "" );
        this._delay(function() {
          this.input.tooltip( "close" ).attr( "title", "" );
        }, 2500 );
        this.input.autocomplete( "instance" ).term = "";
      },
 
      _destroy: function() {
        this.wrapper.remove();
        this.element.show();
      }
    });
  })( jQuery );
 
  $(function() {
    $( ".combobox" ).combobox();
    $( "#toggle" ).click(function() {
      $( ".combobox" ).toggle();
    });
  });