jQuery(function($){
	$('[name="check_all"]').click(function(){
		if($(this).is(':checked'))
			$('.rec-c').prop('checked', true);
		else
			$('.rec-c').prop('checked', false);
	});
	var tblName = $('.db-content tbody').attr('tblname');
	$(document).on('dblclick', '.my-db .db-content tbody td span', function(){
		$(this).replaceWith('<textarea class="editor" colname="'+$(this).attr('colname')+'">'+$(this).text()+'</textarea>')
		$('.my-db .db-content tbody td .editor').focus()
	})
	var recordO = '';
	$(document).on('focus', '.my-db .db-content tbody td .editor', function(){
		recordO = $(this).val();
	})
	$(document).on('blur', '.my-db .db-content tbody td .editor', function(){
		var val = recordO;
		if (recordO != $(this).val()) {
			if(confirm('Are you sure You want to update?')){
				$('.loading').fadeIn();
				val = $(this).val();
				var updateData = {tblName: tblName, val: val, col: $(this).attr('colname'), valO: recordO, recID: $(this).closest('tr').attr('rec-id'), recIDKey: $(this).closest('tr').attr('rec-id-key'), elem: this, action: 'update'};
				var fd = new FormData();
				for(ud in updateData){
					fd.append(ud, updateData[ud])
				}
				ajax(fd)
					.then((data) =>{
						console.log(data)
						$('.loading').fadeOut();
						var _val = recordO;
						if(data == 'Update Successfully!'){
							_val = val;
						}
						$(this).replaceWith('<span colname="'+$(this).attr('colname')+'">'+escapeHtml(_val)+'</span>')
						setTimeout(function(){
							alert(data)
						}, 400)
					});
			}
			else{
				$(this).replaceWith('<span colname="'+$(this).attr('colname')+'">'+escapeHtml(val)+'</span>')
			}
		}
		else{
			$(this).replaceWith('<span colname="'+$(this).attr('colname')+'">'+escapeHtml(val)+'</span>')
		}
	})
	$(document).on('keypress', '.my-db .db-content tbody td .editor', function(e){
	})
	$(document).on('click', '.my-db .db-content tbody td .delete', function(e){
		if(confirm('Are you sure?')){
			$('.loading').fadeIn();
			var parent = $(this).closest('tr');
			var deleteData = {id: parent.attr('rec-id'), col: parent.attr('rec-id-key'), tblName: tblName, action: 'delete'};
			var fd = new FormData();
			for(dd in deleteData){
				fd.append(dd, deleteData[dd])
			}
			ajax(fd)
				.then((data) =>{
					$('.loading').fadeOut();
					setTimeout(function(){
						alert(data)
						$('.my-db .db-content tbody tr[rec-id="'+parent.attr('rec-id')+'"]').remove();
					}, 400)
				});
		}
	})
	$(document).on('click', '.my-db .db-content tbody td .edit', function(e){
		var parent = $(this).closest('tr');
		var showRec = {id: parent.attr('rec-id'), col: parent.attr('rec-id-key'), tblName: tblName, action: 'show_rec'};
		var fd = new FormData();
		for(sr in showRec){
			fd.append(sr, showRec[sr])
		}
		ajax(fd).then((data) => {
			let obj = JSON.parse(data);
			var html = '';
			$.each(obj, function(k, v){
				var rec = v[0];
				for(let r in v[0]){
					html += '<div class="row"><div class="col-sm-4"><label >'+r+'</label></div><div class="col-sm-4"><textarea class="form-control e-value" name="'+r+'[value]">'+(v[0][r])+'</textarea></div><div class="col-sm-4"><select class="form-control e-type" name="'+r+'[type]"><option>Simple Text</option><option>MD5</option></select></div></div>';
				}
				$('.update-rec .field-wrap').html(html);
			})
			$('.update-rec').attr({id: parent.attr('rec-id'), col: parent.attr('rec-id-key')});
			$('#editform').modal('toggle');
		});
	})
	$(document).on('submit', '.update-rec', function(e){
		e.preventDefault();
		var curElem = $(this);
		var fd = new FormData($('.update-rec')[0]);
		fd.append('action', 'update_rec');
		fd.append('id', curElem.attr('id'));
		fd.append('col', curElem.attr('col'));
		fd.append('tblName', tblName);
		ajax(fd).then((data) => {
			if(data == 'success') {
				$('.e-value').each(function(i){
					// $('[rec-id-key="'+$(this).attr('col')+'"][rec-id="'+$(this).attr('id')+'"]').find('td').eq(i+2).length
					// console.log('[rec-id-key="'+curElem.attr('col')+'"][rec-id="'+curElem.attr('id')+'"]');
					console.log($('[rec-id-key="'+curElem.attr('col')+'"][rec-id="'+curElem.attr('id')+'"]').find('td').eq(i+2).find('span').html(this.value))
				});
				$('#editform').modal('toggle');
				alert('Update Successfully!');
			}
			else
				alert('Something went wrong!');
		})	
	})
	function ajax(args){
		let data = args;
		data.delete('elem');
		return $.ajax({
			url: ajaxurl,
			type: "post",
			data: data,
			cache: false,
            processData: false, 
            contentType: false,
            error: function(){
            	$('.loading').fadeOut();
				alert('Something went wrong!')
			}, 
		})
	}
	function escapeHtml(text) {
		return text
			.replace(/&/g, "&amp;")
			.replace(/</g, "&lt;")
			.replace(/>/g, "&gt;")
			.replace(/"/g, "&quot;")
			.replace(/'/g, "&#039;");
	}
});