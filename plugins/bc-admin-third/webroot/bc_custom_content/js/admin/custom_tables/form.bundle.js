(()=>{"use strict";var t,e={9021:(t,e,i)=>{var n=function(){var t=this,e=t._self._c;return t.showModal?e("transition",{attrs:{name:"modal",appear:""}},[e("div",{staticClass:"modal modal-overlay",on:{click:function(e){return e.target!==e.currentTarget?null:t.closeModal.apply(null,arguments)}}},[e("div",{staticClass:"modal-window"},[e("div",{staticClass:"modal-content"},[t._t("default")],2),t._v(" "),e("footer",{staticClass:"modal-footer"},[t._t("footer",(function(){return[e("button",{attrs:{type:"button"},on:{click:t.closeModal}},[t._v("Close")])]}))],2)])])]):t._e()};n._withStripped=!0;const o={props:{scrollable:Boolean},data:function(){return{showModal:!1}},methods:{openModal:function(t){this.showModal=!0,this.$nextTick((function(){this.scrollable?($(".modal-overlay").css("align-items","normal"),$(".modal-window").css("overflow","scroll").css("display","grid")):($(".modal-overlay").css("align-items","center"),$(".modal-window").css("overflow","hidden")),this.$emit("modal-opened",t)}))},closeModal:function(){this.$emit("modal-closed"),this.showModal=!1}}};const s=(0,i(5440).Z)(o,n,[],!1,null,null,null).exports;var a=i(8071),l=new Vue({el:"#AdminCustomTable",data:function(){var t=$("#AdminCustomTablesFormScript");return{settings:JSON.parse(t.attr("data-setting")),links:JSON.parse(t.attr("data-links")),link:{},field:{},showPreview:{},parentList:{},enabledUseLoop:!0,enabledGroupValid:!0,currentParentId:null,tableId:t.attr("data-tableId"),displayPreview:!0,isAdd:t.attr("data-isAdd")}},components:{Modal:s},mounted:function(){this.isAdd||$.bcUtil.initTooltip()},computed:{isGroupLink:function(){return void 0!==this.link.custom_field&&"group"===this.link.custom_field.type},linkTypeTitle:function(){return void 0!==this.link.custom_field&&this.settings[this.link.custom_field.type].label},linkFieldTitle:function(){return void 0!==this.link.custom_field&&this.link.custom_field.title},arraySource:function(){if(!this.field.source)return[];var t=this.field.source.split("\n").map((function(t){return t.replace("\r","")}));return t.length&&""===t[0]?[]:t},linkHtmlDescription:function(){return this.link.description?this.link.description.replace("\n","<br>"):""},editFieldLinkUrl:function(){return this.field?$.bcUtil.adminBaseUrl+"bc-custom-content/custom_fields/edit/"+this.field.id:""},linkTitleById:function(){var t=this;return function(e){return t.links[e]?t.links[e].title:""}},linkFieldTitleById:function(){var t=this;return function(e){return t.links[e]?t.links[e].custom_field.title:""}},isEnabledParent:function(){return!!Object.keys(this.parentList).length&&"group"!==this.field.type}},methods:{saveTable:function(){$.bcUtil.showLoader()},openLinkDetail:function(t){return this.$refs.modalCustomLinkDetail.openModal(t),!1},linkDetailOpened:function(t){this.link=Object.assign({},this.links[t]),this.field=this.link.custom_field,this.currentParentId=this.link.parent_id;var e=this.settings[this.field.type];this.initPreview(this.link.id,e),this.loadParentList(),this.changeGroupFunction(),$.bcUtil.initTooltip({target:".modal-content .bca-help",content:".modal-content .bca-helptext"});var i=$("#CustomLinkPreview"),n=$(".modal-window"),o=$(".modal-footer");o.hide(),i.hide(),this.hideError(),i.appendTo(".modal-content"),setTimeout((function(){$(".modal-footer").css("position","fixed").css("bottom",0).css("width",n.width()),i.css("width",n.width()),o.show(),i.show()}),500),n.on("scroll",(function(){$(".modal-content").innerHeight()-n.innerHeight()<=n.scrollTop()?i.fadeOut(500):l.displayPreview&&"none"===i.css("display")&&i.fadeIn(500)}))},hidePreview:function(){this.displayPreview=!1,$("#CustomLinkPreview").fadeOut(500)},loadParentList:function(){var t=this,e=$.bcUtil.apiAdminBaseUrl+"bc-custom-content/custom_links/get_parent_list/"+this.link.custom_table_id+".json";a.Z.get(e).then((function(e){e.data.parentList?t.parentList=e.data.parentList:t.parentList={}}))},closeLinkDetail:function(){this.displayPreview=!1,$("#CustomLinkPreview").hide().appendTo("body")},initPreview:function(t,e){this.displayPreview=!0,this.showPreview.NonSupport=!1,Object.keys(this.showPreview).forEach((function(t){this.showPreview[t]=!1}),this),t&&e.preview&&"group"!==this.links[t].custom_field.type?this.showPreview[t]=!0:this.showPreview.NonSupport=!0},saveLink:function(){if(this.currentParentId!==this.link.parent_id){var t=bcI18n.confirmMessageOnSaveLink;if(!confirm(t))return}this.hideError(),$.bcUtil.showLoader();var e=$.bcUtil.apiAdminBaseUrl+"bc-custom-content/custom_links/edit/"+this.link.id+".json",i=this;$.bcToken.check((function(){a.Z.post(e,i.link,{headers:{"X-CSRF-Token":$.bcToken.key}}).then((function(t){$.bcUtil.hideLoader(),i.links[i.link.id]=i.link,i.$refs.modalCustomLinkDetail.closeModal()})).catch((function(t){$.bcUtil.hideLoader();var e=t.response.data.errors;400===t.response.status?($(".modal-content #MessageBox .alert-message").html(t.response.data.message),Object.keys(e).forEach((function(t){Object.keys(e[t]).forEach((function(i){$(".error-"+t).append(e[t][i]).show()}))}))):500===t.response.status?$(".modal-content #MessageBox .alert-message").html(t.response.data.message):$(".modal-content #MessageBox .alert-message").html("予期せぬエラーが発生しました。システム管理者に連絡してください。"),$(".modal-content #MessageBox").show()}))}))},hideError:function(){$(".modal-content .error-message").html("").hide(),$(".modal-content #MessageBox").hide()},changeGroupFunction:function(){this.link.group_valid?(this.enabledUseLoop=!1,this.link.use_loop=!1):this.enabledUseLoop=!0,this.link.use_loop?(this.enabledGroupValid=!1,this.link.group_valid=!1):this.enabledGroupValid=!0,this.link.rght-this.link.lft>1&&(this.enabledUseLoop=!1)},deleteTable:function(){if(confirm(bcI18n.confirmDeleteMessage)){$.bcUtil.showLoader();var t=$('<form method="post"/>'),e=$('<input name="_csrfToken"/>');t.attr("action",$.bcUtil.adminBaseUrl+"bc-custom-content/custom_tables/delete/"+this.tableId),t.append(e),$.bcToken.check((function(){e.val($.bcToken.key),$("body").append(t),t.submit()}),{hideLoader:!1})}}}});$((function(){$("input[name='type']").click(i),$("#CustomFieldSettingSource .draggable").draggable({scroll:!0,helper:"clone",opacity:.8,revert:"invalid",cursor:"move",connectToSortable:"#CustomFieldSettingTarget",containment:"#CustomFieldSetting",start:function(t,i){jQuery(i.helper).css({width:jQuery(this).width()});var n=$(this).find("input.custom-field-type").val();n.length>0&&$("#AdminCustomTablesFormScript").data("setting")[n].onlyOneOnTable&&e(n)&&t.preventDefault()},stop:function(e,i){requestAnimationFrame((function(){setTimeout((function(){t()}),500)}))}}),$("#CustomFieldSettingTarget").sortable({placeholder:"custom-field-content placeholder",cursor:"move",revert:!0,distance:5,containment:"#CustomFieldSetting",zIndex:55,scroll:!0,update:function(t,e){if(void 0!==$(e.item).attr("id")&&$(e.item).attr("id").match(/^InUseField/i))o();else{var i,s="template-field-"+$(e.item).attr("class").split(" ").filter((function(t){return-1!==t.indexOf("available-field-")}))[0].replace("available-field-",""),a=(i=0,$("#CustomFieldSettingTarget .custom-field-sort").each((function(){var t=Number($(this).val());t>i&&(i=t)})),i+1),l="InUseField"+a,r=$(".".concat(s)).clone().attr("id",l).addClass("in-use-field").removeClass("template "+s);e.item.after(r).remove(),$("#".concat(l," input[name='template[name]']")).attr("name","custom_links[new-".concat(a,"][name]")),$("#".concat(l," input[name='template[custom_field_id]']")).attr("name","custom_links[new-".concat(a,"][custom_field_id]")),$("#".concat(l," input[name='template[sort]']")).attr("name","custom_links[new-".concat(a,"][sort]")),$("#".concat(l," input[name='template[type]']")).attr("name","custom_links[new-".concat(a,"][type]")),$("#".concat(l," input[name='template[title]']")).attr("name","custom_links[new-".concat(a,"][title]")),$("#".concat(l," input[name='template[display_front]']")).attr("name","custom_links[new-".concat(a,"][display_front]")),$("#".concat(l," input[name='template[use_api]']")).attr("name","custom_links[new-".concat(a,"][use_api]")),$("#".concat(l," input[name='template[status]']")).attr("name","custom_links[new-".concat(a,"][status]"));var c=$("#".concat(l," input[name='custom_links[new-").concat(a,"][name]']"));"group"===c.val()&&c.val("group_field"),n(l),o()}}}),$(".custom-field-group__inner").sortable({placeholder:"custom-field-content placeholder",cursor:"move",revert:!0,distance:5,containment:"#CustomFieldSetting",zIndex:55,scroll:!0,update:function(t,e){o()}}),t(),i(),n();function t(){var t,i=(t=[],Object.keys(l.settings).forEach((function(i){l.settings[i].onlyOneOnTable&&e(i)&&t.push(i)})),t);$("#CustomFieldSettingSource .custom-field-type").each((function(){-1!==i.indexOf($(this).val())&&$(this).parent().addClass("is-deployed")}))}function e(t){var e=!1;return $("#CustomFieldSettingTarget").find("[id*='InUseField']").each((function(){var i=$(this).find("input.custom-field-type").val();if(t===i)return e=!0,!0})),e}function i(){"1"===$("input[name='type']:checked").val()?($("#SpanHasChild").hide(),$("#has-child").prop("checked",!1),$("#RowDisplayField").show()):($("#SpanHasChild").show(),$("#RowDisplayField").hide(),$("#display-field").val("title"))}function n(t){t=void 0!==t?"#"+t+" ":"",$(t+".custom-field-setting__name").keyup((function(){$(this).parent().parent().prev().find(".custom-field-content__head-text").html($(this).val())})),$(t+".custom-field-content__head-setting").click((function(){var t=$(this).parent().next();t.is(":hidden")?t.slideDown("fast"):t.slideUp("fast")})),$(t+".custom-field-content__head-delete").click((function(){$(this).parent().parent().slideUp("fast",(function(){$(this).remove(),o()}));var t=$(this).parent().parent().find("input.custom-field-type").val();!0===l.settings[t].onlyOneOnTable&&$("#CustomFieldSettingSource .custom-field-type").each((function(){$(this).val()===t&&$(this).parent().hasClass("is-deployed")&&$(this).parent().removeClass("is-deployed")}))}))}function o(){var t=1;$("#CustomFieldSettingTarget .custom-field-sort").each((function(){$(this).val(t),t++})),$(".custom-field-group").each((function(){t=1,$(this).find(".custom-field-child-sort").each((function(){$(this).val(t),t++}))}))}}))}},i={};function n(t){var o=i[t];if(void 0!==o)return o.exports;var s=i[t]={exports:{}};return e[t].call(s.exports,s,s.exports,n),s.exports}n.m=e,t=[],n.O=(e,i,o,s)=>{if(!i){var a=1/0;for(d=0;d<t.length;d++){for(var[i,o,s]=t[d],l=!0,r=0;r<i.length;r++)(!1&s||a>=s)&&Object.keys(n.O).every((t=>n.O[t](i[r])))?i.splice(r--,1):(l=!1,s<a&&(a=s));if(l){t.splice(d--,1);var c=o();void 0!==c&&(e=c)}}return e}s=s||0;for(var d=t.length;d>0&&t[d-1][2]>s;d--)t[d]=t[d-1];t[d]=[i,o,s]},n.d=(t,e)=>{for(var i in e)n.o(e,i)&&!n.o(t,i)&&Object.defineProperty(t,i,{enumerable:!0,get:e[i]})},n.g=function(){if("object"==typeof globalThis)return globalThis;try{return this||new Function("return this")()}catch(t){if("object"==typeof window)return window}}(),n.o=(t,e)=>Object.prototype.hasOwnProperty.call(t,e),n.r=t=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},n.j=7472,(()=>{var t={7472:0};n.O.j=e=>0===t[e];var e=(e,i)=>{var o,s,[a,l,r]=i,c=0;if(a.some((e=>0!==t[e]))){for(o in l)n.o(l,o)&&(n.m[o]=l[o]);if(r)var d=r(n)}for(e&&e(i);c<a.length;c++)s=a[c],n.o(t,s)&&t[s]&&t[s][0](),t[s]=0;return n.O(d)},i=self.webpackChunkbc_admin_third=self.webpackChunkbc_admin_third||[];i.forEach(e.bind(null,0)),i.push=e.bind(null,i.push.bind(i))})();var o=n.O(void 0,[5e3],(()=>n(9021)));o=n.O(o)})();
//# sourceMappingURL=form.bundle.js.map