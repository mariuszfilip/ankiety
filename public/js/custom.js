$(function() {

	var availableTags = [ "ActionScript", "AppleScript", "Asp", "BASIC", "C", "C++", "Clojure", "COBOL", "ColdFusion", "Erlang", "Fortran", "Groovy", "Haskell", "Java", "JavaScript", "Lisp", "Perl", "PHP", "Python", "Ruby", "Scala", "Scheme" ];
	$( "#ac" ).autocomplete({
	});	
	
$('a[href=#]').live('click', function(){return false;});

//===== HTML Salad =====//

//===== Close Widget =====//

$(".closeWidget").click(function() {
	$(this).closest('.widget').fadeTo(200, 0.00, function(){ //fade
		$(this).closest('.widget').slideUp(300, function() { //slide up
			$(this).closest('.widget').remove(); //then remove from the DOM
		});
	});
});


//===== Toggle Widget =====//

$(".toggleWidget").click(function() {
	$(this).closest('.widget').find('.widgetContent').slideToggle(300);
});


//===== Notification boxes =====//

$(".hideit").click(function() {
	$(this).fadeTo(200, 0.00, function(){ //fade
		$(this).slideUp(300, function() { //slide up
			$(this).remove(); //then remove from the DOM
		});
	});
});


//===== WYSIWYG editor =====//
/*
$("#editor").cleditor({
	width:"100%", 
	height:"100%",
	bodyStyle: "margin: 10px; font: 12px Arial,Verdana; cursor:text"
});
*/

//===== Middle navigation dropdowns =====//

$('.mUser').click(function () {
	$('.mSub1').slideToggle(100);
});
$(document).bind('click', function(e) {
	var $clicked = $(e.target);
	if (! $clicked.parents().hasClass("mUser"))
	$(".mSub1").slideUp(100);
});

$('.mMessages').click(function () {
	$('.mSub2').slideToggle(100);
});
$(document).bind('click', function(e) {
	var $clicked = $(e.target);
	if (! $clicked.parents().hasClass("mMessages"))
	$(".mSub2").slideUp(100);
});

$('.mFiles').click(function () {
	$('.mSub3').slideToggle(100);
});
$(document).bind('click', function(e) {
	var $clicked = $(e.target);
	if (! $clicked.parents().hasClass("mFiles"))
	$(".mSub3").slideUp(100);
});

$('.mOrders').click(function () {
	$('.mSub4').slideToggle(100);
});
$(document).bind('click', function(e) {
	var $clicked = $(e.target);
	if (! $clicked.parents().hasClass("mOrders"))
	$(".mSub4").slideUp(100);
});


//===== Collapsible elements management =====//

$('.exp').collapsible({
	defaultOpen: 'current',
	cookieName: 'navAct',
	cssOpen: 'active',
	cssClose: 'inactive',
	speed: 200
});
	
$('.opened').collapsible({
	defaultOpen: 'opened,toggleOpened',
	cssOpen: 'inactive',
	cssClose: 'normal',
	speed: 200
});

$('.closed').collapsible({
	defaultOpen: '',
	cssOpen: 'inactive',
	cssClose: 'normal',
	speed: 200
});


$('.goTo').collapsible({
	defaultOpen: 'openedDrop',
	cookieName: 'smallNavAct',
	cssOpen: 'active',
	cssClose: 'inactive',
	speed: 100
});


//===== User nav dropdown =====//		

$('.dd').click(function () {
	$('.userDropdown').slideToggle(200);
});
$(document).bind('click', function(e) {
	var $clicked = $(e.target);
	if (! $clicked.parents().hasClass("dd"))
	$(".userDropdown").slideUp(200);
});


//===== Lightbox =====//

$("a[rel^='lightbox']").prettyPhoto({
	social_tools: false,
	theme: 'pp_default'
});

/*
$("a.oLightbox").click(function(){
	var id = '#'+$(this).attr('rel');
	$.prettyPhoto.open(id);
	setTimeout(function(){
		$('.pp_content select, .pp_content input:checkbox').uniform();
	}, 500);
});

$("a.oLightboxInput").click(function(){
	var id = '#'+$(this).attr('rel');
	var inputVal = $(this).find('.userId').html();
	$.prettyPhoto.open(id);
	setTimeout(function(){
		$('.pp_content select, .pp_content input:checkbox').uniform();
	}, 500);
	$('#userIdInput').val(inputVal);
});
*/

//===== Statistics row dropdowns =====//	
	
$('.ticketsStats > h2 a').click(function () {
	$('#s1').slideToggle(150);
});
$(document).bind('click', function(e) {
	var $clicked = $(e.target);
	if (! $clicked.parents().hasClass("ticketsStats"))
	$("#s1").slideUp(150);
});


$('.visitsStats > h2 a').click(function () {
	$('#s2').slideToggle(150);
});
$(document).bind('click', function(e) {
	var $clicked = $(e.target);
	if (! $clicked.parents().hasClass("visitsStats"))
	$("#s2").slideUp(150);
});


$('.usersStats > h2 a').click(function () {
	$('#s3').slideToggle(150);
});
$(document).bind('click', function(e) {
	var $clicked = $(e.target);
	if (! $clicked.parents().hasClass("usersStats"))
	$("#s3").slideUp(150);
});


$('.ordersStats > h2 a').click(function () {
	$('#s4').slideToggle(150);
});
$(document).bind('click', function(e) {
	var $clicked = $(e.target);
	if (! $clicked.parents().hasClass("ordersStats"))
	$("#s4").slideUp(150);
});

/* UI stuff
================================================== */


//===== Sparklines =====//

$('.negBar').sparkline('html', {type: 'bar', barColor: '#db6464'} );
$('.posBar').sparkline('html', {type: 'bar', barColor: '#6daa24'} );
$('.zeroBar').sparkline('html', {type: 'bar', barColor: '#4e8fc6'} ); 


//===== Animated progress bars =====//
var percent = $('#bar1').attr('title');
$('#bar1').animate({width: percent},1000);

var percent = $('#bar2').attr('title');
$('#bar2').animate({width: percent},1000);

var percent = $('#bar3').attr('title');
$('#bar3').animate({width: percent},1000);

var percent = $('#bar4').attr('title');
$('#bar4').animate({width: percent},1000);


//===== Tooltips =====//

$('.tipN').tipsy({gravity: 'n',fade: true});
$('.tipS').tipsy({gravity: 's',fade: true});
$('.tipW').tipsy({gravity: 'w',fade: true});
$('.tipE').tipsy({gravity: 'e',fade: true});

//===== Datepicker =====//

$(".datepicker").datepicker({
	showOn: "button",
	buttonImage: "images/calendar.gif",
	buttonImageOnly: true
});

//===== Tabs =====//
	
$.fn.contentTabs = function(){ 

	$(this).find(".tab_content").hide(); //Hide all content
	$(this).find("ul.tabs li:first").addClass("activeTab").show(); //Activate first tab
	$(this).find(".tab_content:first").show(); //Show first tab content

	$("ul.tabs li").click(function() {
		$(this).parent().parent().find("ul.tabs li").removeClass("activeTab"); //Remove any "active" class
		$(this).addClass("activeTab"); //Add "active" class to selected tab
		$(this).parent().parent().find(".tab_content").hide(); //Hide all tab content
		var activeTab = $(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
		$(activeTab).show(); //Fade in the active content
		return false;
	});

};
$("div[class^='widget']").contentTabs(); //Run function on any div with class name of "Content Tabs"
	

/* Tables
================================================== */


	//===== Check all checbboxes =====//
	
	$(".titleIcon input:checkbox").click(function() {
		var checkedStatus = this.checked;
		$("#checkAll tbody tr td:first-child input:checkbox").each(function() {
			this.checked = checkedStatus;
				if (checkedStatus == this.checked) {
					$(this).closest('.checker > span').removeClass('checked');
				}
				if (this.checked) {
					$(this).closest('.checker > span').addClass('checked');
				}
		});
	});	
	
	$('#checkAll tbody tr td:first-child').next('td').css('border-left-color', '#CBCBCB');
	
	
	
	//===== Resizable columns =====//
	
	$("#res, #res1").colResizable({
		liveDrag:true,
		draggingClass:"dragging" 
	});
	  
	  
	  
	//===== Sortable columns =====//
	
	$("table").tablesorter();
	
	
	
	//===== Dynamic data table =====//
	
	/*oTable = $('.dTable').dataTable({
	"bJQueryUI": true,
	"sPaginationType": "full_numbers",
	"sDom": '<""l>t<"F"fp>',
    "fnDrawCallback": function() {
			$("input:checkbox").uniform();
        },
	"sScrollX": "100%",
	"bScrollCollapse": true
    });
	
	oTable = $('.dTableScrollX').dataTable({
	"bJQueryUI": true,
	"sPaginationType": "full_numbers",
	"sDom": '<""l>t<"F"fp>',
    "fnDrawCallback": function() {
			$("select, input:checkbox, input:radio, input:file").not('.lBox select, .lBox input:checkbox').uniform();
        },
	"sScrollX": "100%",
	//"sScrollXInner": "150%",
	"bScrollCollapse": true
    });*/
	
	//===== Show/Hide All Fields =====//
	/*
	$(".basicFields").click(function(){
		var rel = $(this).attr('rel');
		$('.topLinks a').removeClass('active');
		$(this).addClass('active');
		$('.'+rel+' .extraField').hide();
	});
	$(".allFields").click(function(){
		var rel = $(this).attr('rel');
		$('.topLinks a').removeClass('active');
		$(this).addClass('active');
		$('.'+rel+' .extraField').show();
	});
	*/
	

/* Forms
================================================== */


	//===== Validation engine =====//

	$(".validate").validationEngine();


	//===== Form elements styling =====//

	$("select, input:checkbox, input:radio, input:file").not('.lBox select, .lBox input:checkbox').uniform();
		
		
	//===== Dual select boxes =====//

	$.configureBoxes();

});


$(document).ready(function () {
$(".gallery ul li").hover(
		function() { $(this).children(".actions").show("fade", 200); },
		function() { $(this).children(".actions").hide("fade", 200); }
	);
});
function ajaxForm( setUrl, setLocation ) {

	$('.ajaxWrapper form').submit(function(){
		if($(this).data('formstatus') !== 'submitting'){
			var form = $(this),
				formData = form.serialize(),
				formUrl = setUrl,
				formMethod = form.attr('method'), 
				responseMsg = $('.ajaxResponse');

			form.data('formstatus','submitting');

			responseMsg.hide()
					   .text('Proszę czekać...')
					   .fadeIn(200);

			$.colorbox.resize();

			$.ajax({
				url: formUrl,
				type: formMethod,
				data: formData,
				success: function(result){
					var responseData = jQuery.parseJSON(result);

					switch(responseData.result){
						case 'error':
							responseError();
						break;
						case 'success':
							responseSuccess();
						break;
					}
					
					function responseError() {
						responseMsg.fadeOut(200,function(){						
							$(this).html(responseData.messages).fadeIn(200,function(){
								$.colorbox.resize();
							
								setTimeout(function(){
									form.data('formstatus','idle');
								},3000);
							});
						});
					}

					function responseSuccess() {
						responseMsg.fadeOut(200, function(){
							$(this).html(responseData.messages).fadeIn(200, function(){						
								$.colorbox.resize();
								setTimeout(function(){
									window.location.href=setLocation; 
								}, 3000);
							})
						});
					}
				}
			});
		}

		return false;
	});

}

function ajaxFormRefresh( setUrl, setLocation ) {

	$('.ajaxWrapper form').submit(function(){
		if($(this).data('formstatus') !== 'submitting'){
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
			var form = $(this),
				formData = form.serialize(),
				formUrl = setUrl,
				formMethod = form.attr('method'), 
				responseMsg = $('.ajaxResponse');

			form.data('formstatus','submitting');

			responseMsg.hide()
					   .text('Proszę czekać...')
					   .fadeIn(200);

			$.colorbox.resize();

			$.ajax({
				url: formUrl,
				type: formMethod,
				data: formData,
				success: function(result){
					var responseData = jQuery.parseJSON(result);

					switch(responseData.result){
						case 'error':
							responseError();
						break;
						case 'success':
							responseSuccess();
						break;
					}
					
					function responseError() {
						responseMsg.fadeOut(200,function(){						
							$(this).html(responseData.messages).fadeIn(200,function(){
								$.colorbox.resize();
							
								setTimeout(function(){
									form.data('formstatus','idle');
								},3000);
							});
						});
					}

					function responseSuccess() {
						responseMsg.fadeOut(200, function(){
							$(this).html(responseData.messages).fadeIn(200, function(){						
								$.colorbox.resize();
								//ajaxRefresh( setLocation );
								if(setLocation!=''){
                                    /*$("#list-table").dataTable().fnDraw(false);
                                    setTimeout(function(){
                                        $.colorbox.close();
                                    },3000);*/
                                    window.location=setLocation;
                                }
							});
						});
					}
				}
			});
		}

		return false;
	});

}

function ajaxRefresh( setLocation ) {

	$('.ajaxTable').load( setLocation + '.ajaxTable' );

}

function initClickAction(){
	$('.addForm').click(function(event){
		event.preventDefault();
		var link = $(this).attr('href');		
		initColorbox(link);
	});

	$('.editForm').click(function(event){
		event.preventDefault();
		var link = $(this).attr('href');
		initColorbox(link);
	});

	$('.deleteForm').click(function(event){
		event.preventDefault();
		var link = $(this).attr('href');
		initColorbox(link);
	});
}

function initColorbox(link){
	$('#ajaxForm').load(link, function(){
		$.colorbox({
			inline: true,
			href: '.ajaxWrapper',
			width: '70%',
			onComplete: function(){
				$('.ajaxWrapper select, .ajaxWrapper input:checkbox').uniform();
				$('.closeLb').click(function(event){
					event.preventDefault();
					$.colorbox.close();
				});
			}
		});
	});
}
