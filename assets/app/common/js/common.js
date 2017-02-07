$(window).load(function () {
	$.each($('.grid-container'), function() {
		var gc = $(this);
		var outerWidth = gc.width();
		var outerHeight = gc.height();
//console.log('outer height = ' + outerHeight);
		$.each(gc.find('.grid'), function(){
			$(this).jqGrid('setGridWidth', outerWidth - 1);
			//$(this).jqGrid('setGridHeight', outerHeight - 1);
		});
	});
	
	// 탭 컨텐츠 영역에 스크롤 추가
	//$.each($('.nav-tabs-custom'), function() {
	//	$(this).slimScroll({height: gApp.tab_contents_height});
	//});
});

$(window).resize(function () {
	$.each($('.grid-container'), function() {
		var gc = $(this);
		var outerWidth = gc.width();
		var outerHeight = gc.height();

		$.each(gc.find('.grid'), function(){
			$(this).jqGrid('setGridWidth', outerWidth - 1);
			//$(this).jqGrid('setGridHeight', outerHeight - 1);
		});
	});
	
	// 탭 컨텐츠 영역에 스크롤 추가
	//$.each($('.nav-tabs-custom'), function() {
	//	$(this).slimScroll({height: gApp.tab_contents_height});
	//});
});

var monthpicker = {
	closeText                       :'선택',
	prevText                        :'이전달',
	nextText                        :'다음달',
	currentText                     :'이번달',
	monthNames                      :['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
	monthNamesShort         		:['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
	dayNames                        :['일','월','화','수','목','금','토'],
	dayNamesShort           		:['일','월','화','수','목','금','토'],
	dayNamesMin                     :['일','월','화','수','목','금','토'],
	weekHeader                      :'Wk',
	dateFormat                      :'yy-mm',
	firstDay                        :0,
	isRTL                           :false,
	showMonthAfterYear      		:true,
	yearSuffix                      :'년',
	yearRange						: 'c-100:c+3', // 년도 선택 셀렉트박스를 현재 년도에서 이전, 이후로 얼마의 범위를 표시할것인가.
	changeMonth						: true, // 월을 바꿀수 있는 셀렉트 박스를 표시한다.
	changeYear						: true, // 년을 바꿀 수 있는 셀렉트 박스를 표시한다.
	showButtonPanel					: true, // 캘린더 하단에 버튼 패널을 표시한다. 
	onClose: function(dateText, inst) { 
		var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
		var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
		$(this).datepicker('setDate', new Date(year, month, 1));
	}
};
/*
$(document).ready(function(){
	$("a.fancybox").fancybox({
		"transitionIn"	: "elastic",
		"transitionOut"	: "elastic",
		"cyclic"		: true,
		"speedIn"		: 600,
		"speedOut"		: 200,
		"overlayShow"	: false
	});
});
*/
//(function($){
$.datepicker.regional['ko']={
	closeText                       :'닫기',
	prevText                        :'이전달',
	nextText                        :'다음달',
	currentText                     :'오늘',
	monthNames                      :['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
	monthNamesShort         		:['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
	dayNames                        :['일','월','화','수','목','금','토'],
	dayNamesShort           		:['일','월','화','수','목','금','토'],
	dayNamesMin                     :['일','월','화','수','목','금','토'],
	weekHeader                      :'Wk',
	dateFormat                      :'yy-mm-dd',
	firstDay                        :0,
	isRTL                           :false,
	showMonthAfterYear      		:true,
	yearSuffix                      :'년',
	yearRange						: 'c-100:c+3', // 년도 선택 셀렉트박스를 현재 년도에서 이전, 이후로 얼마의 범위를 표시할것인가.
	changeMonth						: true, // 월을 바꿀수 있는 셀렉트 박스를 표시한다.
	changeYear						: true, // 년을 바꿀 수 있는 셀렉트 박스를 표시한다.
	showButtonPanel					: true // 캘린더 하단에 버튼 패널을 표시한다. 
};
$.datepicker.setDefaults($.datepicker.regional['ko']);
/*
// 참조 : http://trentrichardson.com/examples/timepicker/
$.timepicker.regional['ko']={
	timeOnlyTitle					:'시간선택',
	closeText						:'완료/닫기',
	currentText                     :'현재시간',
	timeText                     	:'시간',
	hourText                     	:'시',
	minuteText                     	:'분',
	secondText                     	:'초',
	timeFormat						:'HH:mm:ss',
	showMillisec					:false,
	showMicrosec					:false,
	showTimezone					:false,
	showTime						:false,
	controlType						:'select',
	oneLine							:true
};
$.timepicker.setDefaults($.timepicker.regional['ko']);
*/	
//})(jQuery);	
//});
/*
Dropzone.options.ezUploadForm = {
	uploadMultiple: true,
	parallelUploads: 10,
	init: function() {
		// 업로드를 완료한 경우 결과를 폼에 적용
		this.on('success', function(file, response) {
//console.log(response);
			
			var colId = $("#ret_col").val();
			var resultId = $("#result_id").val();
//console.log('resultId = ' + resultId);
			var colIdVal = $("#" + colId).val();
			var resultIdVal = $("#" + resultId).val();
			
			var msg = '';
			
			for(i=0; i<response.length; i++) {
//console.log(response[i]);
				if(msg) msg += '\n';
				msg += response[i].msg;
				
				// 업로드 실패인 경우 메시지에 기록만 함
				if(response[i].success==0) continue;
				
				if(colIdVal) colIdVal += ',';
				if(resultIdVal) resultIdVal += '<br>';
				
				colIdVal += response[i].idx;
//console.log(colId + ' : ' + colIdVal);
				resultIdVal += response[i].file_name;
//console.log(resultId + ' : ' + resultIdVal);
			}
				
			$("#" + colId).val(colIdVal);
			$("#" + resultId).html(resultIdVal);
			
			alert(msg);
		});
		
		this.on('complete', function(file) {
			this.removeAllFiles(true);
			$("#ezUploadModal_btnClose").trigger('click');
		});
	}
};
*/

$.extend($.jqgrid, {
	jqID : function(sid){
		sid = sid + "";
		return sid.replace(/([\\~\\.\\:\\[\\]])/g,"\\\\$1");
	}
});

function getColumnIndexByName(gridId, columnName) 
{
	var cm = $('#' + gridId).jqGrid('getGridParam', 'colModel'), i, l = cm.length;
	for (i = 0; i < l; i += 1) {
		if (cm[i].name === columnName) {
			return i; // return the index
		}
	}
	return -1;
};
function modifySearchingFilter(gridId, separator) 
{
	var i, l, rules, rule, parts, j, group, str, iCol, cmi, cm = this.p.colModel,
		filters = $.parseJSON(this.p.postData.filters);
	if (filters && typeof filters.rules !== 'undefined' && filters.rules.length > 0) {
		rules = filters.rules;
		for (i = 0; i < rules.length; i++) {
			rule = rules[i];
			iCol = getColumnIndexByName(gridId, rule.field);
			cmi = cm[iCol];
			if (iCol >= 0 && ((typeof (cmi.searchoptions) === "undefined" ||
				  typeof (cmi.searchoptions.sopt) === "undefined")
				 && rule.op === myDefaultSearch) ||
					(typeof (cmi.searchoptions) === "object" &&
						$.isArray(cmi.searchoptions.sopt) &&
						cmi.searchoptions.sopt[0] === rule.op)) {
				// make modifications only for the 'contains' operation
				parts = rule.data.split(separator);
				if (parts.length > 1) {
					if (typeof filters.groups === 'undefined') {
						filters.groups = [];
					}
					group = {
						groupOp: 'OR',
						groups: [],
						rules: []
					};
					filters.groups.push(group);
					for (j = 0, l = parts.length; j < l; j++) {
						str = parts[j];
						if (str) {
							// skip empty '', which exist in case of two separaters of once
							group.rules.push({
								data: parts[j],
								op: rule.op,
								field: rule.field
							});
						}
					}
					rules.splice(i, 1);
					i--; // to skip i++
				}
			}
		}
		this.p.postData.filters = JSON.stringify(filters);
	}
};
function dataInitMultiselect(elem) 
{
  setTimeout(function () {
	 var $elem = $(elem), id = elem.id,
		 inToolbar = typeof id === "string" && id.substr(0,3) === "gs_";
		 options = {
			 selectedList: 2,
			 height: "auto",
			 checkAllText: "all",
			 uncheckAllText: "no",
			 noneSelectedText: "Any",
			 open: function () {
				 var $menu = $(".ui-multiselect-menu:visible");
				 $menu.width("auto");
				 return;
			 }
		 };
	 if (inToolbar) {
		 options.minWidth = 'auto';
	 }
	 $elem.multiselect(options);
	 $elem.siblings('button.ui-multiselect').css({
		 width: inToolbar? "98%": "100%",
		 marginTop: "1px",
		 marginBottom: "1px",
		 paddingTop: "3px"
	 });
 }, 50);
};


// 파일삭제 : table 테이블명, col 컬럼명, idx 테이블PK, fileIdx 파일인덱스
function removeFile(table, col, idx, fileIdx)
{
	if(!confirm("선택하신 파일을 삭제하시겠습니까?")) return;
	
	$.ajax({
		url: '/common/removeFile',
		data: {table:table, col:col, idx:idx, fileIdx: fileIdx},
		type: 'post',
		success: function(data) {
			if(data.success) {
				$("#" + col).val( data.idxList );
				$("#" + col + '_result').html( decodeURIComponent(data.msg) );
			} else alert( decodeURIComponent(data.msg) );
		}
	});
}

// 업로드 되는 파일은 ret_table의 ret_idx와 관련이 있음
// 예) swcms_registration.idx 즉 특정 인사와 관련
function openUploadForm(retTable, retCol, retIdx, resultDivId)
{
	/*
	if(retIdx=='') {
		alert('업로드는 초기 입력 시에는 지원되지 않습니다. 먼저 입력 후 수정에서 업로드를 해 주시기 바랍니다.');
		return;
	}
	*/
	
	// 모달창 열기
	$('#ezUploadModal').modal('show');
	
	// 파일과 관련된 테이블
	$("#ret_table").val(retTable);
	
	// 파일 구분 : 예) visa, profile, gallery
	$("#ret_col").val(retCol);
	
	// 파일과 관련된 테이블의 idx
	$("#ret_idx").val(retIdx);
	
	// 업로드 성공 후 파일명을 보여줄 div id
	$("#result_id").val(resultDivId);
}

function makeMonthpicker(datepickerId)
{
	$("#" + datepickerId).datepicker(monthpicker);
	$("#" + datepickerId).focus(function() {
		$(".ui-datepicker-calendar").hide();
        $("#ui-datepicker-div").position({
            my: "center top",
            at: "center bottom",
            of: $(this)
        }); 
	});
}

function formReset(formId)
{
	$("form").each(function() {
		if(this.id==formId) this.reset();
	});
}

function incCheckbox(formId, emptyVal)
{
	var formData = '';
	
	if(typeof emptyVal==='undefined') emptyVal = false;
	
	$.each($('#' + formId + ' input[type=checkbox]')
		.filter(function(idx){
//console.log($(this).attr('id') + ' : ' + $(this).prop('disabled'));
			return ($(this).prop('checked') === false && $(this).prop('disabled') === false);
		}),
		function(idx, el){
			if(formData!='') formData += '&';
			formData += $(el).attr('name') + '=' + emptyVal;
		}
	);
	return formData;
}

// grid의 select type list 문자열을 select option으로 변경
function str2option(str, preset)
{
	var opt = '', strArr = str.split(';'), item, sel;
	
	if(typeof preset==='undefined') preset = '';
	
	for(var i in strArr) {
		item = strArr[i].split(':');
		sel = (item[0]==preset) ? ' selected' : '';
//console.log(item[0] + '-' + item[1]);
		opt += '<option value="' + item[0] + '"' + sel + '>' + item[1] + '</option>';
	}
	return opt;
}

function createModal(modalID, title, contents)
{
    $( "body" ).append('\
        <div class="modal hide fade" id="' + modalID + '" tabindex="-1" role="dialog" aria-labelledby="' + modalID + 'Label">\
          <div class="modal-dialog" role="document">\
            <div class="modal-content">\
              <div class="modal-header">\
                <a href="#" class="close" data-dismiss="modal"><i class="icon-remove"></i>&times;</a>\
                <h4 class="modal-title" id="myModalLabel">' + title + '</h4>\
              </div>\
              <div class="modal-body">' + contents + '</div>\
            </div>\
          </div>\
        </div>\
    ');
}

function createUrlModal(modalID, title, url, style)
{
    var addStyle = (typeof style==='undefined') ? '' : style;
	
	$( "body" ).append('\
		<div class="modal fade ' + addStyle + '" id="modal_' + modalID + '"  role="dialog" aria-labelledby="modal_' + modalID + 'Label">\
		  <div class="modal-dialog grid-container" id="modal_dialog_' + modalID + '" role="document">\
			<div class="modal-content">\
			  <div class="modal-header">\
				<a href="#" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></a>\
				<h4 class="modal-title" id="myModalLabel">' + title + '</h4>\
			  </div>\
			  <div class="modal-body" id="' + modalID + '_contents"></div>\
			</div>\
		  </div>\
		</div>\
	');
	
	$("#" + modalID + '_contents').load(url);
}

function changeModalSize(modalID, sizeClass)
{
	$("#modal_dialog_" + modalID).addClass(sizeClass);
}

function openModal(modalID, focusedItem)
{
    
	$('#modal_' + modalID).on('shown.bs.modal', function () {
		/*
		var zIndex = 1040 + (10 * $(".modal:visible").length);
		$(this).css("z-index", zIndex);
		setTimeout(function() {
			$('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
		}, 0);
		*/
		$("#" + focusedItem).focus();
    });
    $("#modal_" + modalID).modal();
	$("#modal_" + modalID + ' > .modal-dialog').draggable({handle:".modal-header"});
}

function openUrlModal(modalID, title, url, style)
{
	// 수납창이 생성되었는지 체크
	if($("#" + modalID).length==0) {
//console.log('crate modal : main_payment_form');
		createUrlModal(modalID, title, url, style);
		//$("#modal_main_payment_form .modal-body").css('background-color','#ECF0F5');
	} else $("#" + modalID + '_contents').load(url);

	openModal(modalID);
}

function closeModal(modalID)
{
	$("#modal_" + modalID).modal("hide");
}

function openWindow(url, width, height)
{
	var specs = 'menubar=0,resizable=0,scrollbars=0,status=0,titlebar=0,toolbar=0';
	if (typeof width !== 'undefined') specs += ',width=' + width;
	if (typeof height !== 'undefined') specs += ',height=' + height;
	
	window.open(url, '', specs);
}

function closeWindow()
{
	window.open('about:blank', '_self').close();
}

function openStudentInfo(sno)
{
	setSelectedSno(sno);
	openWindow('/student/student_info?sno=' + sno, 800, 700);
}

function openSendmsgForm(sno)
{
	openWindow('/sms/sendmsg_form?_ut_=1&sno=' + sno, 400, 600);
}


// 탭 표출
// tabIdx : 탭의 인덱스 번호 -> 0부터 시작
function dispUrlTab(tabId, tabIdx, param) 
{
	var tart = $('#' + tabId + ' a:eq(' + tabIdx + ')');
	var targ = tart.attr('data-target');
	
	if(typeof param === 'undefined') param = '';

	$.get(tart.attr('href') + param, function(data) {
		$(targ).html(data);
	});

	tart.tab('show');
	return false;
}

///////////////////////////////////////////////////////////////////////////////////

function bootstrapAutoSize(id)
{
	var parentWidth = $("#" + id).closest($("." + id + "-container")).width();
//console.log('container-width : ' + parentWidth);
	$("#" + id).width(parentWidth);
}

///////////////////////////////////////////////////////////////////////////////////

// 현재 활성화된 노드를 포함해 하위의 모든 노드를 포함한 courceList 반환
function dynatreeGetActiveCourse(treeId)
{
    var ActiveNode	=	$("#" + treeId).dynatree("getActiveNode");
    return (ActiveNode == null) ? '' : ActiveNode.data.courseList;
}


///////////////////////////////////////////////////////////////////////////////////

// 그리드의 특정 컬럼의 모든 값을 콤마로 연결하여 리턴함
function jqGridGetAllColVal(gridId, colId)
{
	var strVal = '';
	var allIDs = $('#' + gridId).jqGrid('getDataIDs');
	for(var i=0; i<allIDs.length; i++){
		row = $("#" + gridId).jqGrid('getRowData', allIDs[i]);
		
		if(strVal!='') strVal += ",";
		strVal += "'" + row[colId] + "'";
	}
	return strVal;
}

// 그리드에서 현재 폼 상태로 존재하는 row ID 리턴
function jqGridGetEditableRowId(gridId, colId)
{
	var allIDs = $('#' + gridId).jqGrid('getDataIDs');
	for(var i=0; i<allIDs.length; i++){
		row = $("#" + gridId).jqGrid('getRowData', allIDs[i]);
		if($('#' + allIDs[i] + '_' + colId).length>0) return allIDs[i];
	}
	return '';
}

// 그리드 인덱스(0부터 시작)로 rowId 값 추출
function jqGridGetRowIdByIndex(gridId, idx)
{
	return $("#" + gridId + " tr:eq(" + idx + ")").attr("id");
}

// 제목줄에 html 추가
function jqGridAddHtmlToTitleBar(gridId, html)
{
	$("#gview_" + gridId + " .ui-jqgrid-titlebar").append(html);
}

// jqGrid 넓이 자동조설
function jqGridAutoWidthSize(gridId, spaceWidth)
{
	//var parentWidth = $(".ui-jqgrid").parent().width();
	 var parentWidth = $("#" + gridId + '_container').width()
	$("#" + gridId).jqGrid('setGridWidth', parentWidth - spaceWidth, true);
}

// spaceHeight : 남겨 둬야 할 여유분 높이
function jqGridAutoHeightSize(gridId, spaceHeight)
{
	$("#" + gridId).jqGrid('setGridHeight', $(window).height() - spaceHeight, true);
}

function jqGridAutoSize(gridId, spaceWidth, spaceHeight)
{
	//var parentWidth = $("#" + gridId).closest('.tab-pane').width();
	var parentWidth = $("#" + gridId).closest($(".grid-container")).width();
	var realSpaceWidth = (typeof spaceWidth == "undefined") ? 0 : spaceWidth;
	$("#" + gridId).jqGrid('setGridWidth', parentWidth - realSpaceWidth, true);
//console.log(gridId + ' width : ' + (parentWidth - realSpaceWidth));
	if(typeof spaceHeight != "undefined")
		$("#" + gridId).jqGrid('setGridHeight', $(window).height() - spaceHeight, true);
}

// 선택된 rowId 리턴
function jqGridGetSelRow(gridId)
{
	return $("#" + gridId).jqGrid('getGridParam', 'selrow');
}

// rowId 선택
function jqGridSetSelRow(gridId, rowId)
{
	if(gridId=='' || typeof rowId==='undefined' || rowId=='') return;
	
	$("#" + gridId).jqGrid('setSelection', rowId, true);
}

// index로 행 선택
function jqGridSetSelRowByIndex(gridId, index)
{
	if(gridId=='') return;
	if(typeof index==='undefined') index = 0;

	var rowId = $("#" + gridId).getDataIDs()[0]; 
//console.log('rowId = ' + $("#" + gridId).getDataIDs() + ', gridId = ' + gridId);
	$("#" + gridId).jqGrid('setSelection', rowId, true);
	
	return rowId;
}

// 특정 컬럼의 값으로 행 선택
function jqGridSetSelRowByVal(gridId, colName, colVal)
{
	if(gridId=='' || typeof colName==='undefined' || colName=='') return;
	
	var rowIds = $("#" + gridId).jqGrid('getDataIDs');

    for (i = 0; i < rowIds.length; i++) {
        rowData = $("#" + gridId).jqGrid('getRowData', rowIds[i]);

        if (rowData[colName] == colVal ) {
			// 아직 선택되지 않은 경우만 선택함
			if(jqGridIsSelectedRow(gridId, rowIds[i])==false) {
	
				$("#" + gridId).jqGrid('setSelection', rowIds[i], false); // false인 경우 onselectrow 비활성화
//console.log('select row : ' + 'col = ' + colName, ', val = ' + colVal);
			}
        } 
    } 
}


// 특정 컬럼의 값으로 행 선택 해제
function jqGridResetSelRowByVal(gridId, colName, colVal)
{
	if(gridId=='' || typeof colName==='undefined' || colName=='') return;
	
	var rowIds = $("#" + gridId).jqGrid('getDataIDs');

    for (i = 0; i < rowIds.length; i++) {
        rowData = $("#" + gridId).jqGrid('getRowData', rowIds[i]);

        if (rowData[colName] == colVal ) {
			// 선택된 경우만 선택 해제함
			if(jqGridIsSelectedRow(gridId, rowIds[i])==true)
				$("#" + gridId).jqGrid('resetSelection', rowIds[i], false); // false인 경우 onselectrow 비활성화
        } 
    } 
}


// 선택한 row의 컬럼값 리턴
function jqGridGetSelRowData(gridId, colName)
{
	var sel = $("#" + gridId).jqGrid('getGridParam', 'selrow');
	var row = $("#" + gridId).jqGrid('getRowData', sel);
	return (typeof colName==='undefined' || colName=='') ? row : row[colName];
}

// 선택한 row 리턴
function jqGridGetSelAllRowData(gridId)
{
	var arr = [];
	var selRowIds = $("#" + gridId).jqGrid("getGridParam", "selarrrow");
	
	for(var i=0; i<selRowIds.length; i++) {
		arr[i] = $("#" + gridId).jqGrid('getRowData', selRowIds[i]);
	}
	return arr;
}

// 모든 Row
function jqGridGetAllRowData(gridId)
{
	//return $("#" + gridId).jqGrid('getGridParam','data');
	return $("#" + gridId).jqGrid('getRowData');
}

function jqGridIsSelectedRow(gridId, rowId)
{
	var selRowIds = $("#" + gridId).jqGrid("getGridParam", "selarrrow");
	return ($.inArray(rowId, selRowIds) >= 0) ? true : false;
}

function jqGridSaveAllRow(gridId)
{
	var rowIds = $("#" + gridId).jqGrid('getDataIDs');
	for (i = 0; i < rowIds.length; i++) {
		$("#" + gridId).jqGrid('saveRow', rowIds[i]);
	}
}

// rowId로 행 정보 읽기
function jqGridGetRowData(gridId, rowId)
{
	return $("#" + gridId).jqGrid('getRowData', rowId);
}

function jqGridBindKeys(gridId)
{
	$("#" + gridId).jqGrid('bindKeys');
}

function jqGridOnloadSelectRow(gridId, col, val)
{
	if(typeof val==='undefined') val = '';
	if(typeof col!=='undefined' && col!='' && val!='')
		jqGridSetSelRowByVal(gridId, col, val);
	else jqGridSetSelRowByIndex(gridId, 0);
}

function getSelectedSno()
{
	/*
	var sel = $("#sl").jqGrid('getGridParam', 'selrow');
	var row = $("#sl").jqGrid('getRowData', sel);
	return row.s01_recordNum;
	*/

	return ($("#selected_sno").length==0) ? '' : $("#selected_sno").val();
	//return jqGridGetSelRowData('sl', 's01_recordNum');
}

function setSelectedSno(sno)
{
	$("#selected_sno").val(sno);
console.log('selected_sno = ' + sno);
}

function getGridSelectedSno()
{

	var sel = $("#sl").jqGrid('getGridParam', 'selrow');
	var row = $("#sl").jqGrid('getRowData', sel);
//console.log('getGridSelectedSno: sno = ' + row.s01_recordNum);
	return row.s01_recordNum;

	//return $("#selected_student_no").val();
	//return jqGridGetSelRowData('sl', 's01_recordNum');
}


///////////////////////////////////////////////////////////////////////////////////


//	TRIM
String.prototype.trim = function () {
    return this.replace(/^\s*/, "").replace(/\s*$/, "");
}

// Bootstrap 특정 탭 선택
function setActivePane(tabId, paneId, parameter)
{
	var tart;
	if(paneId=='') tart =  $('#' + tabId + ' a:first');
	else tart = $('#' + tabId + '.nav-tabs a[data-target="#' + paneId + '"]');
	var targ = tart.attr('data-target');
//console.log('setActivePane: /' + tabId + '/' + paneId + '/, url = ' + tart.attr('href') + parameter);
	/*
	$.get(tart.attr('href') + parameter, function(data) {
//console.log('#' + tabId + ' ' + targ);
		$(targ).html(data);
	});
	*/
	
	if(tart.attr('href')!='#') {
		$.ajax({
			url: tart.attr('href'),
			type: 'post',
			data: 'tab=' + paneId + '&' + parameter,
			success: function(data) {
				$(targ).html(data);
			}
		});
	}
	
	tart.tab('show');
	return false;
}

function clearActivePane(tabId, html)
{
	var paneId = $('[data-toggle="tab"]').attr('data-target').substr(1); // 현재 선택된 paneId	
	var tart = $('#' + tabId + '.nav-tabs a[data-target="#' + paneId + '"]');
	var targ = tart.attr('data-target');
//console.log(paneId + ', ' + targ);
	if(typeof html === 'undefined') html = '';
	$(targ).html(html);
}

// Bootstrap 선택된 탭
function getActivePane(tabId)
{
//console.log($('#' + tabId + '.nav-tabs .active a'));
	// #을 제거한 후 리턴함
	return $('#' + tabId + '.nav-tabs .active a').attr('data-target').substr(1);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	DATE FORMAT	/////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
Date.prototype.format = function(f) {
    if (!this.valueOf()) return " ";
 
    var weekName = ["일요일", "월요일", "화요일", "수요일", "목요일", "금요일", "토요일"];
    var d = this;
     
    return f.replace(/(yyyy|yy|MM|dd|E|hh|mm|ss|a\/p)/gi, function($1) {
        switch ($1) {
            case "yyyy": return d.getFullYear();
            case "yy": return (d.getFullYear() % 1000).zf(2);
            case "MM": return (d.getMonth() + 1).zf(2);
            case "dd": return d.getDate().zf(2);
            case "E": return weekName[d.getDay()];
            case "HH": return d.getHours().zf(2);
            case "hh": return ((h = d.getHours() % 12) ? h : 12).zf(2);
            case "mm": return d.getMinutes().zf(2);
            case "ss": return d.getSeconds().zf(2);
            case "a/p": return d.getHours() < 12 ? "오전" : "오후";
            default: return $1;
        }
    });
};

String.prototype.string = function(len){var s = '', i = 0; while (i++ < len) { s += this; } return s;};
String.prototype.zf = function(len){return "0".string(len - this.length) + this;};
Number.prototype.zf = function(len){return this.toString().zf(len);};

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////









//	Number Only
function getNumberOnly(getVal)
{
	var rtnValue	=	"";
	var rtnValue	=	new String(getVal);
	var regex		=	/[^0-9]/g;
		rtnValue	= 	rtnValue.replace(regex, '');
    return rtnValue;
}//	end function


function movePage(sendValue)
{
	var targetUrl	=	"./index.php";	
	var tempArr1	=	sendValue.split(",");
	var tempArr2	=	null;
	var tempCnt1	=	0;
	
	for( tempCnt1=0; tempArr1.length > tempCnt1; tempCnt1++ )
	{
		if(tempCnt1 == 0)
		{
			targetUrl	=	targetUrl + "?";	
		}else{
			targetUrl	=	targetUrl + "&";
		}//	end if
		tempArr2	=	tempArr1[tempCnt1].split(":");
		targetUrl	=	targetUrl + tempArr2[0] + "=" + tempArr2[1];
	}//	end for
	
	location.href	=	targetUrl;
	
}// end function




// 이메일 체크 
function checkMail(strMail)
{ 
   /** 체크사항 
     - @가 2개이상일 경우 
     - .이 붙어서 나오는 경우 
     -  @.나  .@이 존재하는 경우 
     - 맨처음이.인 경우 
     - @이전에 하나이상의 문자가 있어야 함 
     - @가 하나있어야 함 
     - Domain명에 .이 하나 이상 있어야 함 
     - Domain명의 마지막 문자는 영문자 2~4개이어야 함 **/ 

    var check1 = /(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/;  

    var check2 = /^[a-zA-Z0-9\-\.\_]+\@[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4})$/; 
     
    if ( !check1.test(strMail) && check2.test(strMail) ) { 
        return true; 
    } else { 
        return false; 
    } 
}// end function 



// str은 0~9까지 숫자만 가능하다. 
function checkNumber(str)
{ 
	var flag=true; 
	if (str.length > 0) { 
		for (i = 0; i < str.length; i++) {  
			if (str.charAt(i) < '0' || str.charAt(i) > '9') { 
				flag=false; 
			} 
		} 
	} 
	return flag; 
}//	end function




//콤마찍기
function comma(str)
{
	var rtnStr	=	"";

	
	
	
		if( str == "-" )
		{
			alert("-");
			rtnStr	=	str;
		}else{
			rtnStr	=	Number(str).toLocaleString('en');	
		}
	
		

		//	rtnStr	=	str.replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,');
	

	return rtnStr;
} 


//콤마풀기
function uncomma(str)
{
	var rtnStr	=	"";
	
	var str 	=	String(str);
	if( str == "-" )
	{
		rtnStr	=	str;
	}else{
		var intType	=	str.substr(0,1);
		
		if( intType == "-" )
		{
			rtnStr	=	"-" + str.replace(/[^\d]+/g, '');
		}else{
			rtnStr	=	str.replace(/[^\d]+/g, '');
		}//	end if			
	}//	end if
	
	

	
	return rtnStr;
}





function sleep(gap)
{   
	var then,now;
	then = new Date().getTime();
	now=then;
	while((now-then)<gap)
	{
		now = new Date().getTime();
	}// end while

}// end function


//////////////////////////////////////////////////////////////////////////////////////////



function PopupDiv_show()
{	
	$( "body" ).append("<div id='BackFilm' style='display:none; z-index:9998'></div>");
	$( "body" ).append("<div id='divPop' style='display:none; padding: 0px 0px 0px 0px; z-index:9999'></div>");
	//----------------------------------------------------------------------------
		$("#BackFilm").css('display'		,	"block"							);
		$("#BackFilm").css('position'		,	"absolute"						);
		$("#BackFilm").css('background'	,	"#cccccc"						);
		$("#BackFilm").css('opacity'		,	0.7								);
		$("#BackFilm").css('top'			,	'0'								);
		$("#BackFilm").css('left'			,	'0'								);
		$("#BackFilm").css('width'			,	$(document).width()		);
		$("#BackFilm").css('height'		,	$(document).height() * 2	);
	//----------------------------------------------------------------------------
		$("#divPop").css('display'			,	"block"							);
		$("#divPop").css('position'		,	"absolute"						);
	//	$("#divPop").css('background'		,	"#ffffff"						);
		$("#divPop").css('opacity'			,	1.0								);

	//	레이어 팝업 드레그 지원 여부
		$( "#divPop" ).draggable();
		
	//----------------------------------------------------------------------------
	
	
}//	end function

function PopupDiv_hidden()
{	
	$("#BackFilm").remove();
	$("#divPop").remove();
	$( "body" ).append("<div id='divPop' style='display:none; padding: 0px 0px 0px 0px; z-index:9999'></div>");
	
	
	$("#BackFilm").html('');
	$("#BackFilm").css('display', "none");
	
}//	end function

//document.writeln("<div id='BackFilm' style='display:none; z-index:9998'></div>");
//document.writeln("<div id='divPop' style='display:none; padding: 0px 0px 0px 0px; z-index:9999'></div>");
document.writeln("<div id='temp' style='background-color:yellow;'></div>");


//////////////////////////////////////////////////////////////////////////////////////////

function debug(msg)
{
	if($("#debugPannel").length == 0){
		var html = "<div id='debugPannel' style='position:fixed;bottom:0px;z-index:99999;background:white;width:100%;'>";
		html += "<div class='title' style='padding:5px;border-bottom:2px solid #DDD;background:#555;color:white;'>Debug Mode";
		html += "<div class='fa fa-close ' style='float:right;' onclick=\"$('#debugPannel').css('display','none');\">";
		html += "</div>";
		html += "</div>";
		html += "<div class='message' style='padding:10px;max-height:200px;overflow:auto;'>";
		html += "</div>";
		html += "</div>";
		
		$("body").append(html);
		
	}
	
	var date = new Date();
	var Year = date.getFullYear();
	var Month = date.getMonth()+1;
	if(Month<10)Month = "0"+Month
	var Day = date.getDay();
	if(Day<10)Day = "0"+Day
	var Hours = date.getHours();
	if(Hours<10)Hours = "0"+Hours
	var Minutes = date.getMinutes();
	if(Minutes<10)Minutes = "0"+Minutes
	var Seconds = date.getSeconds();
	if(Seconds<10)Seconds = "0"+Seconds
	
	var current_time =  Year + "-" + Month + "-" + Day + " " + Hours + ":" + Minutes + ":" + Seconds;
	
	$("#debugPannel").css("display","block");
	$("#debugPannel .message").append("<span style='color:red;text-weight:bold;'>"+current_time + "</span> : " + msg+"<br/>");
	var scrollHeight = $("#debugPannel .message")[0].scrollHeight
	$("#debugPannel .message").scrollTop(scrollHeight);

}// end function

var infoTime = null;
function info(msg,alert)
{
	if($("#infoPannel").length == 0){
		var html = "<div id='infoPannel' class='col-sm-offset-6 col-sm-6 col-xs-12' style='position:fixed;bottom:0px;z-index:999;display:none;'>";
		html += "</div>";
		
		$("body").append(html);
	}
	
	if(typeof alert==='undefined' || alert=="") alert = "info";
	html = "<div id='infoContent' class='alert alert-"+alert+" alert-dismissable'>";
	html += "</div>";
	$("#infoPannel").html(html);
	
	$("#infoPannel").show();
	$("#infoPannel #infoContent").html(msg);
	
	if(infoTime) clearTimeout(infoTime);
	infoTime = setTimeout(closeInfo,2000);
	
}// end function

function closeInfo(){
	$("#infoPannel #infoContent").html("");
	$("#infoPannel").hide();
}// end function

//////////////////////////////////////////////////////////////////////////////////////////

