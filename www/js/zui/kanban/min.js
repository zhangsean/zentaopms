/*!
 * ZUI: ZUI Kanban View - v1.10.0 - 2022-06-15
 * http://openzui.com
 * GitHub: https://github.com/easysoft/zui.git 
 * Copyright (c) 2022 cnezsoft.com; Licensed MIT
 */
!function(){"use strict";function e(e,a){return n&&!a?requestAnimationFrame(e):setTimeout(e,a||0)}function a(e){return n?cancelAnimationFrame(e):void clearTimeout(e)}var n="function"==typeof window.requestAnimationFrame;$.zui({asap:e,clearAsap:a})}(),function(e){"use strict";function a(a,n){"string"==typeof a&&(a=e(a)),a instanceof e&&(a=a[0]);var t=a.getBoundingClientRect(),r=window.innerHeight||document.documentElement.clientHeight,i=window.innerWidth||document.documentElement.clientWidth;if(n)return t.left>=0&&t.top>=0&&t.left+t.width<=i&&t.top+t.height<=r;var d=t.top<=r&&t.top+t.height>=0,o=t.left<=i&&t.left+t.width>=0;return d&&o}var n="zui.virtualRender",t=function(a,r){"function"==typeof r&&(r={render:r});var i=this;i.name=n,i.$=e(a),i.options=r=e.extend({trigger:"scroll resize"},t.DEFAULTS,this.$.data(),r),i.rendered=!1;var d=r.container;"function"==typeof d&&(d=d(i));var o=e(d?d:window);i.tryRender()||(i.$container=o,i.scrollListener=i.tryRender.bind(i),r.pendingClass&&i.$.addClass(r.pendingClass),o.on(r.trigger,i.scrollListener))};t.prototype.tryRender=function(){var n=this;return!(n.rendered||!a(n.$))&&(n.renderTaskID&&e.zui.clearAsap(n.renderTaskID),n.renderTaskID=e.zui.asap(function(){n.renderTaskID=null;var e=n.options.render(n.$);e!==!1&&(n.rendered=!0,n.destroy())},n.options.delay),!0)},t.prototype.destroy=function(){var a=this;a.renderTaskID&&e.zui.clearAsap(a.renderTaskID),a.scrollListener&&(a.$container.off(a.options.trigger,a.scrollListener),a.scrollListener=null);var t=a.options.pendingClass;t&&a.$.removeClass(t),a.$.removeData(n)},t.DEFAULTS={pendingClass:"virtual-pending"},e.fn.virtualRender=function(a){return this.each(function(){var r=e(this),i=r.data(n);if(i){if("string"==typeof a)return i[a]();i.destroy()}r.data(n,i=new t(this,a))})},e.zui.isElementInViewport=a}(jQuery),function(e){"use strict";var a="zui.virtuallist",n=function(t,r){"function"==typeof r&&(r={render:r});var i=this;i.name=a,i.$=e(t),i.options=r=e.extend({},n.DEFAULTS,this.$.data(),r),i.scrollTop=this.options.scrollTop||0,i.render(r.list,r.scrollTop);var d=0;i.$.on("scroll."+a,function(){if(i.$.data("ignore-next-scroll"))return i.$.data("ignore-next-scroll",!1);if(!i.rendering){var a=i.$.scrollTop();a!==i.scrollTop&&(i.scrollTop=a,d&&e.zui.clearAsap(d),d=e.zui.asap(function(){d=0,i.render()}))}}),i.$.on("resize."+a,function(){i.rendering||(d&&e.zui.clearAsap(d),d=e.zui.asap(function(){d=0,i.render()}))})};n.prototype.render=function(a,n){var t=this;a?t.list=a:a=t.list,n&&e.extend(this.options,n),t.rendering=!0;var r=t.scrollTop,i=t.$,d=t.options.itemClassName,o=t.options.itemHeight,s=t.options.countPerRow,l=Number.parseFloat(i.css("padding-top"),10),c=Array.isArray(a),u="number"==typeof a?a:a.length,h=i.outerHeight(),p="virtual-list-item-expired",b=i.children(".virtual-list-holder-top"),v=i.children(".virtual-list-holder-bottom");b.length||(b=e('<div class="virtual-list-holder-top" style="width:100%">').prependTo(i)),v.length||(v=e('<div class="virtual-list-holder-bottom" style="width:100%">').appendTo(i)),i.children("."+d).addClass(p).addClass("hidden");var f=Math.max(0,s*Math.floor((r-l)/o-1)),m=Math.min(u-1,1+s*Math.ceil((r-l+h)/o)),g=Math.ceil(f/s)*o,k=Math.ceil((u-m-1)/s)*o;b.height(g);for(var C=f;C<=m;C++){var x=t.options.render(C,a,c?a[C]:null);x.addClass(d).removeClass(p).removeClass("hidden")}return v.height(k),i.children("."+p).remove(),b.prependTo(i),v.appendTo(i),t.scrollHeight=t.$[0].scrollHeight,t.rendering=!1,r=Math.min(t.scrollHeight-h,Math.max(g,r)),t.scrollTop=r,r!==t.$.scrollTop()&&t.$.data("ignore-next-scroll",!0).scrollTop(r),!0},n.prototype.destroy=function(){that.$.off("."+a)},n.DEFAULTS={itemClassName:"virtual-list-item",countPerRow:1},e.fn.virtualList=function(t){return this.each(function(){var r=e(this),i=r.data(a);if(i){if("string"==typeof t)return i[t]();i.destroy()}r.data(a,i=new n(this,t))})}}(jQuery),function(e){"use strict";var a="zui.kanban",n="object"==typeof CSS&&CSS.supports("display","flex"),t=function(n,r){var i=this;if(i.name=a,i.$=e(n).addClass("kanban"),r=i.setOptions(e.extend({},t.DEFAULTS,this.$.data(),r)),r.onAction){var d=function(a){var n=e(this);r.onAction(n.data("action"),n,a,i)};i.$.on("click",".action",d).on("dblclick",".action-dbc",d)}var o=r.droppable;if("auto"===o&&(o=!r.readonly),o){var s=r.sortable;"function"==typeof s?s={finish:s}:s&&"object"!=typeof s&&(s={});var l=0;"function"==typeof o?o={drop:o}:"object"!=typeof o&&(o={});var c=e.extend({dropOnMouseleave:!0,selector:".kanban-item",target:".kanban-lane-col:not(.kanban-col-sorting)",mouseButton:"left"},o,{before:function(a){if(o.before){var n=o.before(a);if(n===!1)return n}if(s){var t=a.element.closest(".kanban-lane-items");t.closest(".kanban-col").addClass("kanban-col-sorting"),i._sortResult=null,i._$sortItems=t;var r=t.data("zui.sortable");r||t.sortable(e.extend({},s,{selector:".kanban-item",trigger:".kanban-card",dragCssClass:"kanban-item-sorting",noShadow:!0,finish:function(e){e.changed&&e.list.length>1&&(i._sortResult=e)}})).triggerHandler(a.event)}},drop:function(e){o.drop&&o.drop(e),r.onAction&&r.onAction("dropItem",e.element,e,i)},start:function(a){i.$.addClass("kanban-dragging"),l&&clearTimeout(l),l=setTimeout(function(){e(a.shadowElement).addClass("in"),l=0},50),o.start&&o.start(a)},always:function(e){if(i.$.removeClass("kanban-dragging"),l&&(clearTimeout(l),l=0),s){var a=i._sortResult;i._$sortItems.sortable("destroy").closest(".kanban-col").removeClass("kanban-col-sorting"),a&&s.finish&&a.target&&a.target.closest(".kanban-lane-col")[0]===a.element.closest(".kanban-lane-col")[0]&&s.finish(i._sortResult)}o.always&&o.always(e)}});i.$.droppable(c)}r.onCreate&&r.onCreate(i)};t.prototype.setOptions=function(a){var t=this,r=e.extend({},t.options,{data:t.data},a);t.options=r,r.useFlex&&!n&&(r.useFlex=!1),t.$.toggleClass("no-flex",!r.useFlex).toggleClass("use-flex",!!r.useFlex);var i=!!e.fn.virtualRender&&r.virtualize;return i&&("object"!=typeof i&&(i={lane:!0}),t.virtualize=e.extend({},i)),t.data=r.data||[],t.render(t.data),r},t.prototype.render=function(e){var a=this;e&&(a.data=e),a.data&&!Array.isArray(a.data)&&(a.data=[a.data]);var n=a.options,t=a.data||[];n.beforeRender&&n.beforeRender(a,t),a.$.toggleClass("kanban-readonly",!!n.readonly).toggleClass("kanban-no-lane-name",!!n.noLaneName),a.$.children(".kanban-board").addClass("kanban-expired"),a.maxKanbanBoardWidth=0;for(var r=0;r<t.length;++r)a.renderKanban(r);a.$.children(".kanban-expired").remove(),n.fluidBoardWidth&&t.length>1&&a.$.children(".kanban-board").css("min-width",a.maxKanbanBoardWidth),n.onRender&&n.onRender(a)},t.prototype.layoutKanban=function(e,a){for(var n=this,t=n.options,r=t.noLaneName?0:t.laneNameWidth,i=0,d={},o=!1,s=[],l=0;l<e.columns.length;++l){var c=e.columns[l];if(d[c.type])console.error('ZUI kanban error: duplicated column type "'+c.type+'" definition.');else{if(d[c.type]=c,c.$cardsCount=0,c.$kanbanData=e,c.$index=i,c.id||(c.id=c.type),c.asParent)o=!0,c.subs=[];else if(i++,c.parentType){var u=d[c.parentType];c.$subIndex=u.subs.length,u.subs.push(c)}s.push(c)}}e.columns=s;var h=e.id,p=n.$,b=t.minColWidth*i+r;a=a||p.children('.kanban-board[data-id="'+h+'"]'),a.css(t.fluidBoardWidth?"min-width":"width",b),n.maxKanbanBoardWidth=Math.max(n.maxKanbanBoardWidth,b),e.$layout={minWidth:b,laneNameWidth:r,columnsCount:i,hasParentCol:o,columnsMap:d,columnWidth:100/i};for(var v=e.cardsPerRow||t.cardsPerRow,f=function(e,a,r){if(e.asParent)return 0;r=r||a.items||a.cards||{};var i=r[e.type]||[];if(e.$cardsCount+=i.length,e.parentType){var o=d[e.parentType];o.$cardsCount+=i.length}var s=Math.ceil(i.length/(e.cardsPerRow||a.cardsPerRow||v))*(t.cardHeight+t.cardSpace)+t.cardSpace,l=a.$parent?t.maxSubColHeight||t.maxColHeight:t.maxColHeight,c=a.$parent?t.minSubColHeight||t.minColHeight:t.minColHeight,u="auto"===l?s:Math.max(c,Math.min(l,s));return t.calcColHeight&&(u=t.calcColHeight(e,a,i,u,n)),u},m=e.lanes||[],l=0;l<m.length;++l){var g=m[l];g.kanban=h,g.$index=l,g.$kanbanData=e,g.$height=0;var k=g.subLanes;if(k){g.$height=0;for(var C=0;C<k.length;++C){var x=k[C];x.kanban=h,x.$parent=g,x.$index=C,x.$kanbanData=e,x.$height=0;for(var y=x.items||x.cards||{},$=0;$<s.length;++$)x.$height=Math.max(x.$height,f(s[$],x,y));g.$height+=x.$height,C>0&&t.subLaneSpace&&(g.$height+=t.subLaneSpace)}}else for(var y=g.items||g.cards||{},C=0;C<s.length;++C)g.$height=Math.max(g.$height,f(s[C],g,y))}},t.prototype.renderKanban=function(a){var n=this;if("number"==typeof a)a=n.data[a];else{var t=n.data.findIndex(function(e){return e.id===a.id});if(t>-1){var r=n.data[t];a=e.extend(r,a),n.data[t]=a}else n.data.push(a)}a.id||(a.id=e.zui.uuid());var i=a.id,d=n.options,o=n.$,s=o.children('.kanban-board[data-id="'+i+'"]');s.length?s.removeClass("kanban-expired"):s=e('<div class="kanban-board" data-id="'+i+'"></div>').appendTo(o),n.layoutKanban(a,s),n.renderKanbanHeader(a,s),s.children(".kanban-lane").addClass("kanban-expired");for(var l=a.lanes||[],c=null,u=0;u<l.length;++u){var h=l[u];n.renderLane(h,c,a.columns,s,a),c=h}s.children(".kanban-expired").remove(),d.onRenderKanban&&d.onRenderKanban(s,a,n)},t.prototype.renderKanbanHeader=function(a,n){var t=this,r=t.options,i=a.$layout.hasParentCol;n=n||t.$.children('.kanban-board[data-id="'+a.id+'"]');var d=n.children(".kanban-header");d.length||(d=e('<div class="kanban-header"><div class="kanban-cols kanban-header-cols"></div></div>').prependTo(n),r.useFlex||d.addClass("clearfix")),d.css("height",(i?2:1)*r.headerHeight).toggleClass("kanban-header-has-parent",!!i);var o=d.children(".kanban-cols");o.css("left",a.$layout.laneNameWidth).children(".kanban-col").addClass("kanban-expired");for(var s=a.columns,l=a.$layout.columnsMap||{},c=null,u=null,h=0;h<s.length;++h){var p=s[h];if(p.asParent)t.renderHeaderParentCol(s[h],o,c,a),c=p,u=null;else if(p.parentType){var b=l[p.parentType];t.renderHeaderCol(s[h],o,b,u,a),u=p}else t.renderHeaderCol(s[h],o,null,c,a),c=p,u=null}o.find(".kanban-expired").remove(),r.onRenderHeader&&r.onRenderHeader(o,a)},t.prototype.renderHeaderParentCol=function(a,n,t,r){var i=this,d=i.options,o=n.children('.kanban-header-parent-col[data-id="'+a.id+'"]'),s=t?n.children('.kanban-header-col[data-id="'+t.id+'"]:not(.kanban-expired)'):null;o.length?o.removeClass("kanban-expired").find(".kanban-header-sub-cols>.kanban-col").addClass("kanban-expired"):o=e(['<div class="kanban-col kanban-header-col kanban-header-parent-col" data-id="'+a.id+'">','<div class="kanban-header-col">','<div class="title">','<i class="icon"></i>','<span class="text"></span>',d.showCount?'<span class="count"></span>':"","</div>","</div>",'<div class="kanban-header-sub-cols">',"</div>","</div>"].join("")),s&&s.length?s.after(o):n.prepend(o),o.data("col",a).attr("data-type",a.type);var l=r.$layout.columnWidth;d.useFlex?o.css("flex",a.subs.length+" "+a.subs.length+" "+l*a.subs.length+"%"):o.css({width:l*a.subs.length+"%",left:a.$index*l+"%"});var c=o.children(".kanban-header-col");c.find(".title>.icon").attr("class","icon icon-"+(a.icon||""));var u=c.find(".title>.text").text(a.name).attr("title",a.name);if(a.color&&u.css("color",a.color),d.showCount){var h=void 0!==a.count?a.count:a.$cardsCount;d.showZeroCount||h||(h="");var p=c.find(".title>.count").text(h);d.onRenderCount&&d.onRenderCount(p,h,a,i)}d.onRenderHeaderCol&&d.onRenderHeaderCol(o,a,n,r)},t.prototype.renderHeaderCol=function(a,n,t,r,i){var d=this,o=d.options;if(a.parentType&&t){var s=n.children('.kanban-header-parent-col[data-id="'+t.id+'"]');n=s.children(".kanban-header-sub-cols")}var l=n.children('.kanban-header-col[data-id="'+a.id+'"]'),c=r?n.children('.kanban-header-col[data-id="'+r.id+'"]:not(.kanban-expired)'):null;l.length?l.removeClass("kanban-expired"):l=e(['<div class="kanban-col kanban-header-col" data-id="'+a.id+'">','<div class="title action-dbc" data-action="editCol">','<i class="icon"></i>','<span class="text"></span>',o.showCount?'<span class="count"></span>':"","</div>",'<div class="actions"></div>',"</div>"].join("")),c&&c.length?c.after(l):n.prepend(l),l.data("col",a).attr("data-type",a.type);var u=t?100/t.subs.length:i.$layout.columnWidth;o.useFlex?l.css("flex","1 1 "+u+"%"):l.css({left:(t?a.$subIndex:a.$index)*u+"%",width:u+"%"}),l.find(".title>.icon").attr("class","icon icon-"+(a.icon||""));var h=l.find(".title>.text").text(a.name).attr("title",a.name);if(a.color&&h.css("color",a.color),o.showCount){var p=void 0!==a.count?a.count:a.$cardsCount;o.showZeroCount||p||(p="");var b=l.find(".title>.count").text(p);o.onRenderCount&&o.onRenderCount(b,p,a,d)}o.onRenderHeaderCol&&o.onRenderHeaderCol(l,a,n,i)},t.prototype.renderLane=function(a,t,r,i,d){var o=this,s=o.options;i=i||o.$.children('.kanban-board[data-id="'+a.kanban+'"]');var l=i.children('.kanban-lane[data-id="'+a.id+'"]'),c=t?i.children('.kanban-lane[data-id="'+t.id+'"]:not(.kanban-expired)'):null;l.length?l.removeClass("kanban-expired"):(l=e('<div class="kanban-lane" data-id="'+a.id+'"></div>'),n||l.addClass("clearfix")),c&&c.length?c.after(l):i.children(".kanban-header").after(l);var u=a.subLanes?a.subLanes.length:0;l.attr("data-index",a.$index).data("lane",a).toggleClass("has-sub-lane",u>0).css({height:a.$height||"auto"}),o.virtualizeRender(d,"lane",l,function(){if(!s.noLaneName){var n=l.children('.kanban-lane-name[data-id="'+a.id+'"]');n.length||(n=e('<div class="kanban-lane-name action-dbc" data-action="editLaneName" data-id="'+a.id+'"></div>').prependTo(l)),n.empty().css("width",s.laneNameWidth).attr("title",a.name).append(e('<span class="text" />').text(a.name)),a.color&&n.css("background-color",a.color),s.onRenderLaneName&&s.onRenderLaneName(n,a,i,r,d)}l.children(".kanban-cols,.kanban-sub-lanes").addClass("kanban-expired");var t;t=a.subLanes?o.renderSubLanes(a,r,l,d):o.renderLaneCols(r,a.items||a.cards||{},l,a,d),s.useFlex||t.css("left",d.$layout.laneNameWidth),l.children(".kanban-expired").remove()},{lane:a,columns:r,kanban:d})},t.prototype.virtualizeRender=function(a,n,t,r,i){var d=this,o=d.virtualize,s=o?o[n]:null;return s?("function"==typeof s&&(s=s(i,t)),"number"==typeof s&&t.height(s),void t.virtualRender(e.extend({render:r},d.options.virtualRenderOptions))):r()},t.prototype.renderSubLanes=function(a,n,t,r){var i=this,d=t.children(".kanban-sub-lanes");d.length?d.removeClass("kanban-expired"):d=e('<div class="kanban-sub-lanes"></div>').appendTo(t),d.children(".kanban-sub-lane").addClass("kanban-expired");for(var o=0;o<a.subLanes.length;++o)i.renderSubLane(a.subLanes[o],n,d,r,o);return d.children(".kanban-expired").remove(),d},t.prototype.renderSubLane=function(a,t,r,i,d){var o=r.children('.kanban-sub-lane[data-id="'+a.id+'"]');o.length?o.removeClass("kanban-expired"):(o=e('<div class="kanban-sub-lane" data-id="'+a.id+'"></div>').appendTo(r),n||o.addClass("clearfix")),o.attr("data-index",d).data("lane",a).css({height:a.$height||"auto"}),o.children(".kanban-col").addClass("kanban-expired");var s=a.items||a.cards;s&&this.renderLaneCols(t,s,o,a,i),o.children(".kanban-expired").remove()},t.prototype.renderLaneCols=function(a,n,t,r,i){var d=this,o=t.children(".kanban-cols");o.length?o.removeClass("kanban-expired"):o=e('<div class="kanban-cols kanban-'+(r.$parent?"sub-":"")+'lane-cols"></div>').appendTo(t),o.children(".kanban-col").addClass("kanban-expired");for(var s=null,l=0;l<a.length;++l){var c=a[l];if(!c.asParent){for(var u=d.renderLaneCol(c,o,s),h=n[c.type]||[],p=0;p<h.length;++p){var b=h[p];b.$index=p,b.order=+b.order,Number.isNaN(b.order)&&(b.order=p)}h.sort(function(e,a){var n=e.order-a.order;return 0!==n?n:e.$index-a.$index}),d.renderColumnCards(c,h,u,r,i),s=c}}return o.children(".kanban-expired").remove(),o},t.prototype.renderColumnCards=function(e,a,n,t,r){var i=this,d=i.options,o=n.find(".kanban-lane-items"),s=e.cardsPerRow||t.cardsPerRow||r.cardsPerRow||d.cardsPerRow;if(d.virtualCardList){var l=o.data("zui.virtuallist");l?l.render(a,{countPerRow:s,itemHeight:d.cardHeight+d.cardSpace}):o.virtualList({countPerRow:s,itemHeight:d.cardHeight+d.cardSpace,list:a,itemClassName:"kanban-item",render:function(a,n,d){var d=n[a],s=a>0?n[a-1]:null;return d.$index=a,d.$col=e,d.$lane=t,i.renderCard(d,o,s,e,t,r)}})}else{o.children(".kanban-item").addClass("kanban-expired");for(var c=0;c<a.length;++c){var u=a[c],h=c>0?a[c-1]:null;u.$index=c,u.$col=e,u.$lane=t,i.renderCard(u,o,h,e,t,r)}o.children(".kanban-expired").remove()}o.css("padding",d.cardSpace/2).toggleClass("kanban-items-grid",s>1).attr("data-cards-per-row",s).data("cards",a)},t.prototype.renderLaneCol=function(a,n,t){var r=this,i=r.options,d=n.children('.kanban-lane-col[data-id="'+a.id+'"]'),o=t?n.children('.kanban-lane-col[data-id="'+t.id+'"]:not(.kanban-expired)'):null;d.length?d.removeClass("kanban-expired"):(d=e(['<div class="kanban-col kanban-lane-col" data-id="'+a.id+'">','<div class="kanban-lane-items scrollbar-hover"></div>',"</div>"].join("")),r.options.readonly||d.append(['<div class="kanban-lane-actions">','<button class="btn btn-default btn-block action" type="button" data-action="addItem"><span class="text-muted"><i class="icon icon-plus"></i> '+r.options.addItemText+"</span></button>","</div>"].join("")),i.laneItemsClass&&d.find(".kanban-lane-items").addClass(i.laneItemsClass),i.laneColClass&&d.addClass(i.laneColClass)),o&&o.length?o.after(d):n.prepend(d),d.attr({"data-parent":a.parentType?a.parentType:null,"data-type":a.type}).data("col",a);var s=a.$kanbanData.$layout.columnWidth;return i.useFlex?d.css("flex","1 1 "+s+"%"):d.css({left:a.$index*s+"%",width:s+"%"}),d},t.prototype.renderCard=function(a,n,t,r,i,d){var o=this.options,s=n.children('.kanban-item[data-id="'+a.id+'"]'),l=t?n.children('.kanban-item[data-id="'+t.id+'"]:not(.kanban-expired)'):null;s.length?s.removeClass("kanban-expired"):(s=e('<div class="kanban-item" data-id="'+a.id+'"></div>'),o.wrapCard&&s.append('<div class="kanban-card"></div>')),l&&l.length?l.after(s):n.prepend(s);var c=r.cardsPerRow||i.cardsPerRow||d.cardsPerRow||o.cardsPerRow;s.data("item",a).css({padding:o.cardSpace/2,width:c>1?100/c+"%":""});var u=o.wrapCard?s.children(".kanban-card"):s;u.css("height",o.cardHeight);var h=o.cardRender||o.itemRender;if(h)h(a,u,r,i,d);else{var p=u.find(".title");p.length||(p=e('<div class="title"></div>').appendTo(u)),p.text(a.name||a.title)}return s},t.DEFAULTS={minColWidth:100,maxColHeight:400,minColHeight:90,minSubColHeight:40,subLaneSpace:2,laneNameWidth:20,headerHeight:32,cardHeight:40,cardSpace:10,cardsPerRow:1,wrapCard:!0,fluidBoardWidth:!0,addItemText:"添加条目",useFlex:!0,droppable:"auto",laneColClass:"",showCount:!0},e.fn.kanban=function(n){return this.each(function(){var r=e(this),i=r.data(a),d="object"==typeof n&&n;i||r.data(a,i=new t(this,d)),"string"==typeof n&&i[n]()})},t.NAME=a,e.fn.kanban.Constructor=t}(jQuery);