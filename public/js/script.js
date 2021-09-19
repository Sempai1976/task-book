let login_form = $('#login_form');
let login_box = $('#login-modal');

const loginModal = new Object({
    login_user: function() {
    	let login_body = $('#modal-body');
        let unknown_error = 'An unknown error occurred while sending the message, please try again later.';
        let login_error_box = '<div class="login_error alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><div class="error-content"></div></div>';
		$('input[type=submit]').attr('disabled','disabled');
        $.ajax({
            url: login_form.attr('action'),
            type: 'POST',
		    dataType: 'json',
		    cache: false,
            data: login_form.serialize(),
            success: function(data) {
				if (data.answer == 'success') {
					location.reload();
			    } else {
					$('input[type=submit]').removeAttr('disabled','disabled');
					if (login_body.find('.login_error').length < 1) {
						login_body.prepend(login_error_box);
					}
					$('.login_error').fadeIn(500).find('.error-content').html(data.error);                
			    }
            },
            error: function(data) {
				$('input[type=submit]').removeAttr('disabled','disabled');
				if (login_body.find('.login_error').length < 1) {
					login_body.prepend(login_error_box);
				}
				$('.login_error').fadeIn(500).find('.error-content').html(unknown_error);
            }
        });
    },
	clear_form: function() {
		login_form[0].reset();
		$('.login_error').hide();
		login_form.find(":input").css("box-shadow", "");
    },
	cancel_form: function() {
		login_box.children('.login_error').fadeOut().find('.error-content').empty();
		login_form[0].reset();
    }
});

function Tasks_search() {
	let search_task = $("#search_task").val();
	let url = window.location.origin + '?search_str='+search_task;
	window.location.replace(url);
}

$('.sort_arr').on('click', function(){
	Tasks_sort_by(this);
});

function Tasks_sort_by(sel, type) {
	let url = window.location.href;
	let sort_by = (type) ? sel.value : sel.getAttribute('name');
    let cur_sort = pd_get_param(url, 'sort_by');
    let new_url;
		
	if (!cur_sort) {
		let pref = (url.includes("?")) ? '&' : '?';
	    new_url = url + pref + sort_by;
	} else {
		new_url = url.replace('sort_by='+cur_sort, sort_by);
	}
	
	window.location.replace(new_url);
}

function pd_get_param(url,key) {
	let s = url.match(new RegExp(key + '=([^&=]+)'));
	return s ? s[1] : false;
}

function getUrlParams(num) {
    const params = {};
    const parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        params[key] = value;
    });
    return (!num) ? params : Object.keys(params).length;
}

function validate(e) {
	let $valid = true;
	let form = (e.id !== undefined) ? e.id : e;
	if (form) {
        let inputs = document.getElementById(form).elements;
        for (i = 0; i < inputs.length; i++) {
	        if (inputs[i].nodeName === "INPUT" && inputs[i].required === true) {
                if (inputs[i].value == "") {
                	$valid = false;
            	    inputs[i].style.boxShadow = "0 0 0 2px red";
			    } else {
				    inputs[i].style.boxShadow = "0 0 0 2px #00bf78";
//                  inputs[i].style.boxShadow = "";
			    }
            }
        }
	}
	return $valid;	
}

$('#confirm-part-delete').on('show.bs.modal', function(e) {
   $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
});

$(function() {
	$("#login_form").submit(function(event){
        loginModal.login_user();
		event.preventDefault();
	});
	$("#search_task").keyup(function(){
		let search_len = this.value.length;
		let clear_btn = $('#clear_search');
		let url = window.location.href;
		let cur_search = pd_get_param(url, 'search_str');
		if (search_len > 0 || cur_search) {
			clear_btn.show();
		} else {
			clear_btn.hide();
		}
	});
	$("#search_form").submit(function(event){
		Tasks_search();
		event.preventDefault();
	});
	 $('#clear_search').click(function(event){
        let url = window.location.href;
	    let search_task = $("#search_task");
	    let search_str = 'search_str='+search_task.val();
        let cur_search = pd_get_param(url, 'search_str');
	    if (cur_search) {
	    	if (url.includes("?search_str=")){
				let num_url_params = getUrlParams(true);
				if (num_url_params > 1) {
					window.location.replace(url.replace('?search_str='+cur_search+'&', '?'));
				} else {
					window.location.replace(url.replace('?search_str='+cur_search, ''));
				}
			} else {
				window.location.replace(url.replace('&search_str='+cur_search, ''));
			}
	    } else {
		    search_task.val('');
	    }
		event.preventDefault();
	});
});

(function (win,doc) {
    'use strict';
    const container = doc.querySelector('table#tasks');
    if (!container) {
		return;
	}
    const entries = container.querySelectorAll('td.s_name > div:not(.avatar), td.s_email, td.s_task');
    let search_words = $("#search_task").val();
	if (search_words.length > 0 && entries.length > 0) {
        let reg_replace = new RegExp(search_words, 'gi');
        let highlighting = '#FFFA51';
        let i;	
        for (i = 0; i < entries.length; i = i + 1) {
             if (reg_replace) {
				 entries[i].innerHTML = entries[i].innerHTML.replace(reg_replace,'<span style="background-color:'+highlighting+'">'+search_words+'</span>');
             }
        }
    }
}(this, this.document));
