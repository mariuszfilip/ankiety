(function ($) {
    $.fn.selectChain = function (options) {
    	    
        var defaults = {
            key: "id",
            value: "label",
            action: "action",
        };
        
        var settings = $.extend({}, defaults, options);
        
        if (!(settings.target instanceof $)) settings.target = $(settings.target);
        
        return this.each(function () {
            var $$ = $(this);
            
            $$.change(function () {
                var data = null;
                if (typeof settings.data == 'string') {
                    data = settings.data + '&' + this.name + '=' + $$.val();
                } else if (typeof settings.data == 'object') {
                    data = settings.data;
                    data[this.name] = $$.val();
                }
                
                settings.target.empty();
                
               
                if(this.name == "report" && ($$.val() == '1' || $$.val() == '2')) {
                	$.ajax({
			    url: settings.url,
			    data: data,
			    type: (settings.type || 'get'),
			    dataType: 'text',
			    success: function (j) {
				$('#list-element').html(j);
			    },
			    error: function (xhr, desc, er) {
				alert("an error occurred");
			    }
			});	
                }
                else {
                	$.ajax({
			    url: settings.url,
			    data: data,
			    type: (settings.type || 'get'),
			    dataType: 'json',
			    success: function (j) {
				var options = [], i = 0, o = null;
				
				for (i = 0; i < j.length; i++) {
				    o = document.createElement("OPTION");
				    o.value = typeof j[i] == 'object' ? j[i][settings.key] : j[i];
				    o.text = typeof j[i] == 'object' ? j[i][settings.value] : j[i];
				    o.onclick = typeof j[i] == 'object' ? j[i][settings.action] : j[i];
				    //data = typeof j[i] == 'object' ? j[i][settings.action] : j[i];
				    //$('#list').html(data);
				    settings.target.get(0).options[i] = o;
				}
				/*setTimeout(function () {
				    settings.target
					.find('option:first')
					.attr('selected', 'selected')
					.parent('select')
					.trigger('change');
				}, 0);*/
			    },
			    error: function (xhr, desc, er) {
				alert("an error occurred");
			    }
			});  
		}
            });
        });
    };
    
})(jQuery);
