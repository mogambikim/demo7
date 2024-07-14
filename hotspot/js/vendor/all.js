'use strict';

// async-each MIT license (by Paul Miller from http://paulmillr.com).
(function(globals) {
  'use strict';
  var each = function(items, next, callback) {
    if (!Array.isArray(items)) throw new TypeError('each() expects array as first argument');
    if (typeof next !== 'function') throw new TypeError('each() expects function as second argument');
    if (typeof callback !== 'function') callback = Function.prototype; // no-op

    if (items.length === 0) return callback(undefined, items);

    var transformed = new Array(items.length);
    var count = 0;
    var returned = false;

    items.forEach(function(item, index) {
      next(item, function(error, transformedItem) {
        if (returned) return;
        if (error) {
          returned = true;
          return callback(error);
        }
        transformed[index] = transformedItem;
        count += 1;
        if (count === items.length) return callback(undefined, transformed);
      });
    });
  };

  if (typeof define !== 'undefined' && define.amd) {
    define([], function() {
      return each;
    }); // RequireJS
  } else if (typeof module !== 'undefined' && module.exports) {
    module.exports = each; // CommonJS
  } else {
    globals.asyncEach = each; // <script>
  }
})(this);

/*!
 * fullPage 2.7.8
 * https://github.com/alvarotrigo/fullPage.js
 * @license MIT licensed
 *
 * Copyright (C) 2015 alvarotrigo.com - A project by Alvaro Trigo
 */
!function(e,n){"use strict";"function"==typeof define&&define.amd?define(["jquery"],function(o){return n(o,e,e.document,e.Math)}):"undefined"!=typeof exports?module.exports=n(require("jquery"),e,e.document,e.Math):n(jQuery,e,e.document,e.Math)}("undefined"!=typeof window?window:this,function(e,n,o,t,i){"use strict";var r,a="fullpage-wrapper",l="."+a,s="fp-scrollable",c="."+s,f=".slimScrollBar",d=".slimScrollRail",u="fp-responsive",h="fp-notransition",p="fp-destroyed",v="fp-enabled",m="fp-viewing",g="active",S="."+g,w="fp-completely",y="."+w,b=".section",x="fp-section",T="."+x,C=T+S,k=T+":first",A=T+":last",L="fp-tableCell",B="."+L,M="fp-auto-height",E="fp-normal-scroll",R="fp-nav",F="#"+R,H="fp-tooltip",q="."+H,z="fp-show-active",O=".slide",D="fp-slide",I="."+D,P=I+S,V="fp-slides",W="."+V,Y="fp-slidesContainer",U="."+Y,X="fp-table",N="fp-slidesNav",K="."+N,j=K+" a",Q="fp-controlArrow",G="."+Q,J="fp-prev",Z="."+J,$=Q+" "+J,_=G+Z,ee="fp-next",ne="."+ee,oe=Q+" "+ee,te=G+ne,ie=e(n),re=e(o);e.fn.fullpage=function(s){function c(){s.css3&&(s.css3=Sn()),s.scrollBar=s.scrollBar||s.hybrid,d(),Q(),Dn.setAllowScrolling(!0),Dn.setAutoScrolling(s.autoScrolling,"internal");var n=e(C).find(P);n.length&&(0!==e(C).index(T)||0===e(C).index(T)&&0!==n.index())&&Ln(n),Ge(),gn(),ie.on("load",function(){qe()})}function f(){ie.on("scroll",de).on("hashchange",ze).blur(Ye).resize(Qe),re.keydown(Oe).keyup(Ie).on("click touchstart",F+" a",Ue).on("click touchstart",j,Xe).on("click",q,De),e(T).on("click touchstart",G,We),s.normalScrollElements&&(re.on("mouseenter",s.normalScrollElements,function(){Dn.setMouseWheelScrolling(!1)}),re.on("mouseleave",s.normalScrollElements,function(){Dn.setMouseWheelScrolling(!0)}))}function d(){s.anchors.length||(s.anchors=e(s.sectionSelector+"[data-anchor]").map(function(){return e(this).data("anchor").toString()}).get()),s.navigationTooltips.length||(s.navigationTooltips=e(s.sectionSelector+"[data-tooltip]").map(function(){return e(this).data("tooltip").toString()}).get())}function Q(){Xn.css({height:"100%",position:"relative"}),Xn.addClass(a),e("html").addClass(v),Nn=ie.height(),Xn.removeClass(p),ae(),e(T).each(function(n){var o=e(this),t=o.find(I),i=t.length;ee(o,n),ne(o,n),i>0?Z(o,t,i):s.verticalCentered&&an(o)}),s.fixedElements&&s.css3&&e(s.fixedElements).appendTo(On),s.navigation&&se(),s.scrollOverflow?("complete"===o.readyState&&ce(),ie.on("load",ce)):fe()}function Z(n,o,t){var i=100*t,r=100/t;o.wrapAll('<div class="'+Y+'" />'),o.parent().wrap('<div class="'+V+'" />'),n.find(U).css("width",i+"%"),t>1&&(s.controlArrows&&le(n),s.slidesNavigation&&hn(n,t)),o.each(function(n){e(this).css("width",r+"%"),s.verticalCentered&&an(e(this))});var a=n.find(P);a.length&&(0!==e(C).index(T)||0===e(C).index(T)&&0!==a.index())?Ln(a):o.eq(0).addClass(g)}function ee(n,o){o||0!==e(C).length||n.addClass(g),n.css("height",Nn+"px"),s.paddingTop&&n.css("padding-top",s.paddingTop),s.paddingBottom&&n.css("padding-bottom",s.paddingBottom),"undefined"!=typeof s.sectionsColor[o]&&n.css("background-color",s.sectionsColor[o]),"undefined"!=typeof s.anchors[o]&&n.attr("data-anchor",s.anchors[o])}function ne(n,o){"undefined"!=typeof s.anchors[o]&&n.hasClass(g)&&nn(s.anchors[o],o),s.menu&&s.css3&&e(s.menu).closest(l).length&&e(s.menu).appendTo(On)}function ae(){e(s.sectionSelector).each(function(){e(this).addClass(x)}),e(s.slideSelector).each(function(){e(this).addClass(D)})}function le(e){e.find(W).after('<div class="'+$+'"></div><div class="'+oe+'"></div>'),"#fff"!=s.controlArrowColor&&(e.find(te).css("border-color","transparent transparent transparent "+s.controlArrowColor),e.find(_).css("border-color","transparent "+s.controlArrowColor+" transparent transparent")),s.loopHorizontal||e.find(_).hide()}function se(){On.append('<div id="'+R+'"><ul></ul></div>');var n=e(F);n.addClass(function(){return s.showActiveTooltip?z+" "+s.navigationPosition:s.navigationPosition});for(var o=0;o<e(T).length;o++){var t="";s.anchors.length&&(t=s.anchors[o]);var i='<li><a href="#'+t+'"><span></span></a>',r=s.navigationTooltips[o];"undefined"!=typeof r&&""!==r&&(i+='<div class="'+H+" "+s.navigationPosition+'">'+r+"</div>"),i+="</li>",n.find("ul").append(i)}e(F).css("margin-top","-"+e(F).height()/2+"px"),e(F).find("li").eq(e(C).index(T)).find("a").addClass(g)}function ce(){e(T).each(function(){var n=e(this).find(I);n.length?n.each(function(){rn(e(this))}):rn(e(this))}),fe()}function fe(){var n=e(C);n.addClass(w),s.scrollOverflowHandler.afterRender&&s.scrollOverflowHandler.afterRender(n),Ee(n),Re(n),e.isFunction(s.afterLoad)&&s.afterLoad.call(n,n.data("anchor"),n.index(T)+1),e.isFunction(s.afterRender)&&s.afterRender.call(Xn)}function de(){var n;if(!s.autoScrolling||s.scrollBar){for(var t=ie.scrollTop(),i=he(t),r=0,a=t+ie.height()/2,l=o.querySelectorAll(T),c=0;c<l.length;++c){var f=l[c];f.offsetTop<=a&&(r=c)}if(ue(i)&&(e(C).hasClass(w)||e(C).addClass(w).siblings().removeClass(w)),n=e(l).eq(r),!n.hasClass(g)){io=!0;var d=e(C),u=d.index(T)+1,h=on(n),p=n.data("anchor"),v=n.index(T)+1,m=n.find(P);if(m.length)var S=m.data("anchor"),y=m.index();Qn&&(n.addClass(g).siblings().removeClass(g),e.isFunction(s.onLeave)&&s.onLeave.call(d,u,v,h),e.isFunction(s.afterLoad)&&s.afterLoad.call(n,p,v),Ee(n),nn(p,v-1),s.anchors.length&&(In=p,pn(y,S,p,v))),clearTimeout(eo),eo=setTimeout(function(){io=!1},100)}s.fitToSection&&(clearTimeout(no),no=setTimeout(function(){Qn&&s.fitToSection&&(e(C).is(n)&&requestAnimFrame(function(){Kn=!0}),Ce(e(C)),requestAnimFrame(function(){Kn=!1}))},s.fitToSectionDelay))}}function ue(n){var o=e(C).position().top,t=o+ie.height();return"up"==n?t>=ie.scrollTop()+ie.height():o<=ie.scrollTop()}function he(e){var n=e>ro?"down":"up";return ro=e,n}function pe(e,n){if(Jn.m[e]){var o,t;if("down"==e?(o="bottom",t=Dn.moveSectionDown):(o="top",t=Dn.moveSectionUp),n.length>0){if(!s.scrollOverflowHandler.isScrolled(o,n))return!0;t()}else t()}}function ve(n){var o=n.originalEvent;if(!me(n.target)&&ge(o)){s.autoScrolling&&n.preventDefault();var i=e(C),r=s.scrollOverflowHandler.scrollable(i);if(Qn&&!Wn){var a=An(o);so=a.y,co=a.x,i.find(W).length&&t.abs(lo-co)>t.abs(ao-so)?t.abs(lo-co)>ie.outerWidth()/100*s.touchSensitivity&&(lo>co?Jn.m.right&&Dn.moveSlideRight():Jn.m.left&&Dn.moveSlideLeft()):s.autoScrolling&&t.abs(ao-so)>ie.height()/100*s.touchSensitivity&&(ao>so?pe("down",r):so>ao&&pe("up",r))}}}function me(n,o){o=o||0;var t=e(n).parent();return o<s.normalScrollElementTouchThreshold&&t.is(s.normalScrollElements)?!0:o==s.normalScrollElementTouchThreshold?!1:me(t,++o)}function ge(e){return"undefined"==typeof e.pointerType||"mouse"!=e.pointerType}function Se(e){var n=e.originalEvent;if(s.fitToSection&&zn.stop(),ge(n)){var o=An(n);ao=o.y,lo=o.x}}function we(e,n){for(var o=0,i=e.slice(t.max(e.length-n,1)),r=0;r<i.length;r++)o+=i[r];return t.ceil(o/n)}function ye(o){var i=(new Date).getTime(),r=e(y).hasClass(E);if(s.autoScrolling&&!Vn&&!r){o=o||n.event;var a=o.wheelDelta||-o.deltaY||-o.detail,l=t.max(-1,t.min(1,a)),c="undefined"!=typeof o.wheelDeltaX||"undefined"!=typeof o.deltaX,f=t.abs(o.wheelDeltaX)<t.abs(o.wheelDelta)||t.abs(o.deltaX)<t.abs(o.deltaY)||!c;Gn.length>149&&Gn.shift(),Gn.push(t.abs(a)),s.scrollBar&&(o.preventDefault?o.preventDefault():o.returnValue=!1);var d=e(C),u=s.scrollOverflowHandler.scrollable(d),h=i-fo;if(fo=i,h>200&&(Gn=[]),Qn){var p=we(Gn,10),v=we(Gn,70),m=p>=v;m&&f&&(0>l?pe("down",u):pe("up",u))}return!1}s.fitToSection&&zn.stop()}function be(n,o){var t="undefined"==typeof o?e(C):o,i=t.find(W),r=i.find(I).length;if(!(!i.length||Wn||2>r)){var a=i.find(P),l=null;if(l="prev"===n?a.prev(I):a.next(I),!l.length){if(!s.loopHorizontal)return;l="prev"===n?a.siblings(":last"):a.siblings(":first")}Wn=!0,je(i,l)}}function xe(){e(P).each(function(){Ln(e(this),"internal")})}function Te(e,n){var o=e.top,t=e.top>uo,i=o-Nn+n.outerHeight();return n.outerHeight()>Nn?t||(o=i):t&&(o=i),uo=o,o}function Ce(n,o,t){requestAnimFrame(function(){var i=n.position();if("undefined"!=typeof i){var r=Te(i,n),a={element:n,callback:o,isMovementUp:t,dest:i,dtop:r,yMovement:on(n),anchorLink:n.data("anchor"),sectionIndex:n.index(T),activeSlide:n.find(P),activeSection:e(C),leavingSection:e(C).index(T)+1,localIsResizing:Kn};if(!(a.activeSection.is(n)&&!Kn||s.scrollBar&&ie.scrollTop()===a.dtop&&!n.hasClass(M))){if(a.activeSlide.length)var l=a.activeSlide.data("anchor"),c=a.activeSlide.index();s.autoScrolling&&s.continuousVertical&&"undefined"!=typeof a.isMovementUp&&(!a.isMovementUp&&"up"==a.yMovement||a.isMovementUp&&"down"==a.yMovement)&&(a=Le(a)),(!e.isFunction(s.onLeave)||a.localIsResizing||s.onLeave.call(a.activeSection,a.leavingSection,a.sectionIndex+1,a.yMovement)!==!1)&&(Fe(a.activeSection),n.addClass(g).siblings().removeClass(g),Ee(n),Qn=!1,pn(c,l,a.anchorLink,a.sectionIndex),ke(a),In=a.anchorLink,nn(a.anchorLink,a.sectionIndex))}}})}function ke(n){if(s.css3&&s.autoScrolling&&!s.scrollBar){var o="translate3d(0px, -"+n.dtop+"px, 0px)";sn(o,!0),s.scrollingSpeed?$n=setTimeout(function(){Me(n)},s.scrollingSpeed):Me(n)}else{var t=Ae(n);e(t.element).animate(t.options,s.scrollingSpeed,s.easing).promise().done(function(){s.scrollBar?setTimeout(function(){Me(n)},30):Me(n)})}}function Ae(e){var n={};return s.autoScrolling&&!s.scrollBar?(n.options={top:-e.dtop},n.element=l):(n.options={scrollTop:e.dtop},n.element="html, body"),n}function Le(n){return n.isMovementUp?e(C).before(n.activeSection.nextAll(T)):e(C).after(n.activeSection.prevAll(T).get().reverse()),Bn(e(C).position().top),xe(),n.wrapAroundElements=n.activeSection,n.dest=n.element.position(),n.dtop=n.dest.top,n.yMovement=on(n.element),n}function Be(n){n.wrapAroundElements&&n.wrapAroundElements.length&&(n.isMovementUp?e(k).before(n.wrapAroundElements):e(A).after(n.wrapAroundElements),Bn(e(C).position().top),xe())}function Me(n){Be(n),n.element.find(".fp-scrollable").mouseover(),e.isFunction(s.afterLoad)&&!n.localIsResizing&&s.afterLoad.call(n.element,n.anchorLink,n.sectionIndex+1),Re(n.element),n.element.addClass(w).siblings().removeClass(w),Qn=!0,e.isFunction(n.callback)&&n.callback.call(this)}function Ee(n){var n=He(n);n.find("img[data-src], source[data-src], audio[data-src]").each(function(){e(this).attr("src",e(this).data("src")),e(this).removeAttr("data-src"),e(this).is("source")&&e(this).closest("video").get(0).load()})}function Re(n){var n=He(n);n.find("video, audio").each(function(){var n=e(this).get(0);n.hasAttribute("autoplay")&&"function"==typeof n.play&&n.play()})}function Fe(n){var n=He(n);n.find("video, audio").each(function(){var n=e(this).get(0);n.hasAttribute("data-ignore")||"function"!=typeof n.pause||n.pause()})}function He(n){var o=n.find(P);return o.length&&(n=e(o)),n}function qe(){var e=n.location.hash.replace("#","").split("/"),o=e[0],t=e[1];o&&(s.animateAnchor?dn(o,t):Dn.silentMoveTo(o,t))}function ze(){if(!io&&!s.lockAnchors){var e=n.location.hash.replace("#","").split("/"),o=e[0],t=e[1],i="undefined"==typeof In,r="undefined"==typeof In&&"undefined"==typeof t&&!Wn;o.length&&(o&&o!==In&&!i||r||!Wn&&Pn!=t)&&dn(o,t)}}function Oe(n){clearTimeout(oo);var o=e(":focus");if(!o.is("textarea")&&!o.is("input")&&!o.is("select")&&s.keyboardScrolling&&s.autoScrolling){var t=n.which,i=[40,38,32,33,34];e.inArray(t,i)>-1&&n.preventDefault(),Vn=n.ctrlKey,oo=setTimeout(function(){Ne(n)},150)}}function De(){e(this).prev().trigger("click")}function Ie(e){jn&&(Vn=e.ctrlKey)}function Pe(e){2==e.which&&(ho=e.pageY,Xn.on("mousemove",Ke))}function Ve(e){2==e.which&&Xn.off("mousemove")}function We(){var n=e(this).closest(T);e(this).hasClass(J)?Jn.m.left&&Dn.moveSlideLeft(n):Jn.m.right&&Dn.moveSlideRight(n)}function Ye(){jn=!1,Vn=!1}function Ue(n){n.preventDefault();var o=e(this).parent().index();Ce(e(T).eq(o))}function Xe(n){n.preventDefault();var o=e(this).closest(T).find(W),t=o.find(I).eq(e(this).closest("li").index());je(o,t)}function Ne(n){var o=n.shiftKey;switch(n.which){case 38:case 33:Jn.k.up&&Dn.moveSectionUp();break;case 32:if(o&&Jn.k.up){Dn.moveSectionUp();break}case 40:case 34:Jn.k.down&&Dn.moveSectionDown();break;case 36:Jn.k.up&&Dn.moveTo(1);break;case 35:Jn.k.down&&Dn.moveTo(e(T).length);break;case 37:Jn.k.left&&Dn.moveSlideLeft();break;case 39:Jn.k.right&&Dn.moveSlideRight();break;default:return}}function Ke(e){Qn&&(e.pageY<ho&&Jn.m.up?Dn.moveSectionUp():e.pageY>ho&&Jn.m.down&&Dn.moveSectionDown()),ho=e.pageY}function je(n,o){var i=o.position(),r=o.index(),a=n.closest(T),l=a.index(T),c=a.data("anchor"),f=a.find(K),d=mn(o),u=a.find(P),h=Kn;if(s.onSlideLeave){var p=u.index(),v=tn(p,r);if(!h&&"none"!==v&&e.isFunction(s.onSlideLeave)&&s.onSlideLeave.call(u,c,l+1,p,v,r)===!1)return void(Wn=!1)}Fe(u),o.addClass(g).siblings().removeClass(g),h||Ee(o),!s.loopHorizontal&&s.controlArrows&&(a.find(_).toggle(0!==r),a.find(te).toggle(!o.is(":last-child"))),a.hasClass(g)&&pn(r,d,c,l);var m=function(){h||e.isFunction(s.afterSlideLoad)&&s.afterSlideLoad.call(o,c,l+1,d,r),Re(o),Wn=!1};if(s.css3){var w="translate3d(-"+t.round(i.left)+"px, 0px, 0px)";Je(n.find(U),s.scrollingSpeed>0).css(Mn(w)),_n=setTimeout(function(){m()},s.scrollingSpeed,s.easing)}else n.animate({scrollLeft:t.round(i.left)},s.scrollingSpeed,s.easing,function(){m()});f.find(S).removeClass(g),f.find("li").eq(r).find("a").addClass(g)}function Qe(){if(Ge(),Yn){var n=e(o.activeElement);if(!n.is("textarea")&&!n.is("input")&&!n.is("select")){var i=ie.height();t.abs(i-po)>20*t.max(po,i)/100&&(Dn.reBuild(!0),po=i)}}else clearTimeout(Zn),Zn=setTimeout(function(){Dn.reBuild(!0)},350)}function Ge(){var e=s.responsive||s.responsiveWidth,n=s.responsiveHeight,o=e&&ie.outerWidth()<e,t=n&&ie.height()<n;e&&n?Dn.setResponsive(o||t):e?Dn.setResponsive(o):n&&Dn.setResponsive(t)}function Je(e){var n="all "+s.scrollingSpeed+"ms "+s.easingcss3;return e.removeClass(h),e.css({"-webkit-transition":n,transition:n})}function Ze(e){return e.addClass(h)}function $e(e,n){var o=825,i=900;if(o>e||i>n){var r=100*e/o,a=100*n/i,l=t.min(r,a),s=l.toFixed(2);On.css("font-size",s+"%")}else On.css("font-size","100%")}function _e(n,o){s.navigation&&(e(F).find(S).removeClass(g),n?e(F).find('a[href="#'+n+'"]').addClass(g):e(F).find("li").eq(o).find("a").addClass(g))}function en(n){s.menu&&(e(s.menu).find(S).removeClass(g),e(s.menu).find('[data-menuanchor="'+n+'"]').addClass(g))}function nn(e,n){en(e),_e(e,n)}function on(n){var o=e(C).index(T),t=n.index(T);return o==t?"none":o>t?"up":"down"}function tn(e,n){return e==n?"none":e>n?"left":"right"}function rn(e){e.css("overflow","hidden");var n,o=s.scrollOverflowHandler,t=o.wrapContent(),i=e.closest(T),r=o.scrollable(e);r.length?n=o.scrollHeight(e):(n=e.get(0).scrollHeight,s.verticalCentered&&(n=e.find(B).get(0).scrollHeight));var a=Nn-parseInt(i.css("padding-bottom"))-parseInt(i.css("padding-top"));n>a?r.length?o.update(e,a):(s.verticalCentered?e.find(B).wrapInner(t):e.wrapInner(t),o.create(e,a)):o.remove(e),e.css("overflow","")}function an(e){e.addClass(X).wrapInner('<div class="'+L+'" style="height:'+ln(e)+'px;" />')}function ln(e){var n=Nn;if(s.paddingTop||s.paddingBottom){var o=e;o.hasClass(x)||(o=e.closest(T));var t=parseInt(o.css("padding-top"))+parseInt(o.css("padding-bottom"));n=Nn-t}return n}function sn(e,n){n?Je(Xn):Ze(Xn),Xn.css(Mn(e)),setTimeout(function(){Xn.removeClass(h)},10)}function cn(n){var o=Xn.find(T+'[data-anchor="'+n+'"]');return o.length||(o=e(T).eq(n-1)),o}function fn(e,n){var o=n.find(W),t=o.find(I+'[data-anchor="'+e+'"]');return t.length||(t=o.find(I).eq(e)),t}function dn(e,n){var o=cn(e);"undefined"==typeof n&&(n=0),e===In||o.hasClass(g)?un(o,n):Ce(o,function(){un(o,n)})}function un(e,n){if("undefined"!=typeof n){var o=e.find(W),t=fn(n,e);t.length&&je(o,t)}}function hn(e,n){e.append('<div class="'+N+'"><ul></ul></div>');var o=e.find(K);o.addClass(s.slidesNavPosition);for(var t=0;n>t;t++)o.find("ul").append('<li><a href="#"><span></span></a></li>');o.css("margin-left","-"+o.width()/2+"px"),o.find("li").first().find("a").addClass(g)}function pn(e,n,o,t){var i="";s.anchors.length&&!s.lockAnchors&&(e?("undefined"!=typeof o&&(i=o),"undefined"==typeof n&&(n=e),Pn=n,vn(i+"/"+n)):"undefined"!=typeof e?(Pn=n,vn(o)):vn(o)),gn()}function vn(e){if(s.recordHistory)location.hash=e;else if(Yn||Un)n.history.replaceState(i,i,"#"+e);else{var o=n.location.href.split("#")[0];n.location.replace(o+"#"+e)}}function mn(e){var n=e.data("anchor"),o=e.index();return"undefined"==typeof n&&(n=o),n}function gn(){var n=e(C),o=n.find(P),t=mn(n),i=mn(o),r=(n.index(T),String(t));o.length&&(r=r+"-"+i),r=r.replace("/","-").replace("#","");var a=new RegExp("\\b\\s?"+m+"-[^\\s]+\\b","g");On[0].className=On[0].className.replace(a,""),On.addClass(m+"-"+r)}function Sn(){var e,t=o.createElement("p"),r={webkitTransform:"-webkit-transform",OTransform:"-o-transform",msTransform:"-ms-transform",MozTransform:"-moz-transform",transform:"transform"};o.body.insertBefore(t,null);for(var a in r)t.style[a]!==i&&(t.style[a]="translate3d(1px,1px,1px)",e=n.getComputedStyle(t).getPropertyValue(r[a]));return o.body.removeChild(t),e!==i&&e.length>0&&"none"!==e}function wn(){o.addEventListener?(o.removeEventListener("mousewheel",ye,!1),o.removeEventListener("wheel",ye,!1),o.removeEventListener("MozMousePixelScroll",ye,!1)):o.detachEvent("onmousewheel",ye)}function yn(){var e,t="";n.addEventListener?e="addEventListener":(e="attachEvent",t="on");var r="onwheel"in o.createElement("div")?"wheel":o.onmousewheel!==i?"mousewheel":"DOMMouseScroll";"DOMMouseScroll"==r?o[e](t+"MozMousePixelScroll",ye,!1):o[e](t+r,ye,!1)}function bn(){Xn.on("mousedown",Pe).on("mouseup",Ve)}function xn(){Xn.off("mousedown",Pe).off("mouseup",Ve)}function Tn(){if(Yn||Un){var n=kn();e(l).off("touchstart "+n.down).on("touchstart "+n.down,Se),e(l).off("touchmove "+n.move).on("touchmove "+n.move,ve)}}function Cn(){if(Yn||Un){var n=kn();e(l).off("touchstart "+n.down),e(l).off("touchmove "+n.move)}}function kn(){var e;return e=n.PointerEvent?{down:"pointerdown",move:"pointermove"}:{down:"MSPointerDown",move:"MSPointerMove"}}function An(e){var n=[];return n.y="undefined"!=typeof e.pageY&&(e.pageY||e.pageX)?e.pageY:e.touches[0].pageY,n.x="undefined"!=typeof e.pageX&&(e.pageY||e.pageX)?e.pageX:e.touches[0].pageX,Un&&ge(e)&&s.scrollBar&&(n.y=e.touches[0].pageY,n.x=e.touches[0].pageX),n}function Ln(e,n){Dn.setScrollingSpeed(0,"internal"),"undefined"!=typeof n&&(Kn=!0),je(e.closest(W),e),"undefined"!=typeof n&&(Kn=!1),Dn.setScrollingSpeed(to.scrollingSpeed,"internal")}function Bn(e){if(s.scrollBar)Xn.scrollTop(e);else if(s.css3){var n="translate3d(0px, -"+e+"px, 0px)";sn(n,!1)}else Xn.css("top",-e)}function Mn(e){return{"-webkit-transform":e,"-moz-transform":e,"-ms-transform":e,transform:e}}function En(e,n,o){switch(n){case"up":Jn[o].up=e;break;case"down":Jn[o].down=e;break;case"left":Jn[o].left=e;break;case"right":Jn[o].right=e;break;case"all":"m"==o?Dn.setAllowScrolling(e):Dn.setKeyboardScrolling(e)}}function Rn(){Bn(0),e(F+", "+K+", "+G).remove(),e(T).css({height:"","background-color":"",padding:""}),e(I).css({width:""}),Xn.css({height:"",position:"","-ms-touch-action":"","touch-action":""}),zn.css({overflow:"",height:""}),e("html").removeClass(v),e.each(On.get(0).className.split(/\s+/),function(e,n){0===n.indexOf(m)&&On.removeClass(n)}),e(T+", "+I).each(function(){s.scrollOverflowHandler.remove(e(this)),e(this).removeClass(X+" "+g)}),Ze(Xn),Xn.find(B+", "+U+", "+W).each(function(){e(this).replaceWith(this.childNodes)}),zn.scrollTop(0);var n=[x,D,Y];e.each(n,function(n,o){e("."+o).removeClass(o)})}function Fn(e,n,o){s[e]=n,"internal"!==o&&(to[e]=n)}function Hn(){return e("html").hasClass(v)?void qn("error","Fullpage.js can only be initialized once and you are doing it multiple times!"):(s.continuousVertical&&(s.loopTop||s.loopBottom)&&(s.continuousVertical=!1,qn("warn","Option `loopTop/loopBottom` is mutually exclusive with `continuousVertical`; `continuousVertical` disabled")),s.scrollBar&&s.scrollOverflow&&qn("warn","Option `scrollBar` is mutually exclusive with `scrollOverflow`. Sections with scrollOverflow might not work well in Firefox"),s.continuousVertical&&s.scrollBar&&(s.continuousVertical=!1,qn("warn","Option `scrollBar` is mutually exclusive with `continuousVertical`; `continuousVertical` disabled")),void e.each(s.anchors,function(n,o){var t=re.find("[name]").filter(function(){return e(this).attr("name")&&e(this).attr("name").toLowerCase()==o.toLowerCase()}),i=re.find("[id]").filter(function(){return this.id&&this.id.toLowerCase()==o.toLowerCase()});(i.length||t.length)&&(qn("error","data-anchor tags can not have the same value as any `id` element on the site (or `name` element for IE)."),i.length&&qn("error",'"'+o+'" is is being used by another element `id` property'),t.length&&qn("error",'"'+o+'" is is being used by another element `name` property'))}))}function qn(e,n){console&&console[e]&&console[e]("fullPage: "+n)}if(e("html").hasClass(v))return void Hn();var zn=e("html, body"),On=e("body"),Dn=e.fn.fullpage;s=e.extend({menu:!1,anchors:[],lockAnchors:!1,navigation:!1,navigationPosition:"right",navigationTooltips:[],showActiveTooltip:!1,slidesNavigation:!1,slidesNavPosition:"bottom",scrollBar:!1,hybrid:!1,css3:!0,scrollingSpeed:700,autoScrolling:!0,fitToSection:!0,fitToSectionDelay:1e3,easing:"easeInOutCubic",easingcss3:"ease",loopBottom:!1,loopTop:!1,loopHorizontal:!0,continuousVertical:!1,normalScrollElements:null,scrollOverflow:!1,scrollOverflowHandler:r,touchSensitivity:5,normalScrollElementTouchThreshold:5,keyboardScrolling:!0,animateAnchor:!0,recordHistory:!0,controlArrows:!0,controlArrowColor:"#fff",verticalCentered:!0,resize:!1,sectionsColor:[],paddingTop:0,paddingBottom:0,fixedElements:null,responsive:0,responsiveWidth:0,responsiveHeight:0,sectionSelector:b,slideSelector:O,afterLoad:null,onLeave:null,afterRender:null,afterResize:null,afterReBuild:null,afterSlideLoad:null,onSlideLeave:null},s),Hn(),e.extend(e.easing,{easeInOutCubic:function(e,n,o,t,i){return(n/=i/2)<1?t/2*n*n*n+o:t/2*((n-=2)*n*n+2)+o}}),e.extend(e.easing,{easeInQuart:function(e,n,o,t,i){return t*(n/=i)*n*n*n+o}}),Dn.setAutoScrolling=function(n,o){Fn("autoScrolling",n,o);var t=e(C);s.autoScrolling&&!s.scrollBar?(zn.css({overflow:"hidden",height:"100%"}),Dn.setRecordHistory(to.recordHistory,"internal"),Xn.css({"-ms-touch-action":"none","touch-action":"none"}),t.length&&Bn(t.position().top)):(zn.css({overflow:"visible",height:"initial"}),Dn.setRecordHistory(!1,"internal"),Xn.css({"-ms-touch-action":"","touch-action":""}),Bn(0),t.length&&zn.scrollTop(t.position().top))},Dn.setRecordHistory=function(e,n){Fn("recordHistory",e,n)},Dn.setScrollingSpeed=function(e,n){Fn("scrollingSpeed",e,n)},Dn.setFitToSection=function(e,n){Fn("fitToSection",e,n)},Dn.setLockAnchors=function(e){s.lockAnchors=e},Dn.setMouseWheelScrolling=function(e){e?(yn(),bn()):(wn(),xn())},Dn.setAllowScrolling=function(n,o){"undefined"!=typeof o?(o=o.replace(/ /g,"").split(","),e.each(o,function(e,o){En(n,o,"m")})):n?(Dn.setMouseWheelScrolling(!0),Tn()):(Dn.setMouseWheelScrolling(!1),Cn())},Dn.setKeyboardScrolling=function(n,o){"undefined"!=typeof o?(o=o.replace(/ /g,"").split(","),e.each(o,function(e,o){En(n,o,"k")})):s.keyboardScrolling=n},Dn.moveSectionUp=function(){var n=e(C).prev(T);n.length||!s.loopTop&&!s.continuousVertical||(n=e(T).last()),n.length&&Ce(n,null,!0)},Dn.moveSectionDown=function(){var n=e(C).next(T);n.length||!s.loopBottom&&!s.continuousVertical||(n=e(T).first()),n.length&&Ce(n,null,!1)},Dn.silentMoveTo=function(e,n){requestAnimFrame(function(){Dn.setScrollingSpeed(0,"internal")}),Dn.moveTo(e,n),requestAnimFrame(function(){Dn.setScrollingSpeed(to.scrollingSpeed,"internal")})},Dn.moveTo=function(e,n){var o=cn(e);"undefined"!=typeof n?dn(e,n):o.length>0&&Ce(o)},Dn.moveSlideRight=function(e){be("next",e)},Dn.moveSlideLeft=function(e){be("prev",e)},Dn.reBuild=function(n){if(!Xn.hasClass(p)){Kn=!0,requestAnimFrame(function(){Kn=!0});var o=ie.outerWidth();Nn=ie.height(),s.resize&&$e(Nn,o),e(T).each(function(){var n=e(this).find(W),o=e(this).find(I);s.verticalCentered&&e(this).find(B).css("height",ln(e(this))+"px"),e(this).css("height",Nn+"px"),s.scrollOverflow&&(o.length?o.each(function(){rn(e(this))}):rn(e(this))),o.length>1&&je(n,n.find(P))});var t=e(C),i=t.index(T);i&&Dn.silentMoveTo(i+1),Kn=!1,requestAnimFrame(function(){Kn=!1}),e.isFunction(s.afterResize)&&n&&s.afterResize.call(Xn),e.isFunction(s.afterReBuild)&&!n&&s.afterReBuild.call(Xn)}},Dn.setResponsive=function(n){var o=On.hasClass(u);n?o||(Dn.setAutoScrolling(!1,"internal"),Dn.setFitToSection(!1,"internal"),e(F).hide(),On.addClass(u)):o&&(Dn.setAutoScrolling(to.autoScrolling,"internal"),Dn.setFitToSection(to.autoScrolling,"internal"),e(F).show(),On.removeClass(u))};var In,Pn,Vn,Wn=!1,Yn=navigator.userAgent.match(/(iPhone|iPod|iPad|Android|playbook|silk|BlackBerry|BB10|Windows Phone|Tizen|Bada|webOS|IEMobile|Opera Mini)/),Un="ontouchstart"in n||navigator.msMaxTouchPoints>0||navigator.maxTouchPoints,Xn=e(this),Nn=ie.height(),Kn=!1,jn=!0,Qn=!0,Gn=[],Jn={};Jn.m={up:!0,down:!0,left:!0,right:!0},Jn.k=e.extend(!0,{},Jn.m);var Zn,$n,_n,eo,no,oo,to=e.extend(!0,{},s);e(this).length&&(c(),f());var io=!1,ro=0,ao=0,lo=0,so=0,co=0,fo=(new Date).getTime();n.requestAnimFrame=function(){return n.requestAnimationFrame||n.webkitRequestAnimationFrame||n.mozRequestAnimationFrame||n.oRequestAnimationFrame||n.msRequestAnimationFrame||function(e){e()}}();var uo=0,ho=0,po=Nn;Dn.destroy=function(n){Dn.setAutoScrolling(!1,"internal"),Dn.setAllowScrolling(!1),Dn.setKeyboardScrolling(!1),Xn.addClass(p),clearTimeout(_n),clearTimeout($n),clearTimeout(Zn),clearTimeout(eo),clearTimeout(no),ie.off("scroll",de).off("hashchange",ze).off("resize",Qe),re.off("click",F+" a").off("mouseenter",F+" li").off("mouseleave",F+" li").off("click",j).off("mouseover",s.normalScrollElements).off("mouseout",s.normalScrollElements),e(T).off("click",G),clearTimeout(_n),clearTimeout($n),n&&Rn()}};var ae={afterRender:function(e){var n=e.find(V),o=e.find(c);n.length&&(o=n.find(P)),o.mouseover()},create:function(e,n){e.find(c).slimScroll({allowPageScroll:!0,height:n+"px",size:"10px",alwaysVisible:!0})},isScrolled:function(e,n){return"top"===e?!n.scrollTop():"bottom"===e?n.scrollTop()+1+n.innerHeight()>=n[0].scrollHeight:void 0},scrollable:function(e){return e.find(W).length?e.find(P).find(c):e.find(c)},scrollHeight:function(e){return e.find(c).get(0).scrollHeight},remove:function(e){e.find(c).children().first().unwrap().unwrap(),e.find(f).remove(),e.find(d).remove()},update:function(e,n){e.find(c).css("height",n+"px").parent().css("height",n+"px")},wrapContent:function(){return'<div class="'+s+'"></div>'}};r=ae});



/*! Copyright (c) 2011 Piotr Rochala (http://rocha.la)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 *
 * Version: 1.3.2 (modified for fullpage.js)
 *
 */
(function(f){jQuery.fn.extend({slimScroll:function(g){var a=f.extend({width:"auto",height:"250px",size:"7px",color:"#000",position:"right",distance:"1px",start:"top",opacity:.4,alwaysVisible:!1,disableFadeOut:!1,railVisible:!1,railColor:"#333",railOpacity:.2,railDraggable:!0,railClass:"slimScrollRail",barClass:"slimScrollBar",wrapperClass:"slimScrollDiv",allowPageScroll:!1,wheelStep:20,touchScrollStep:200,borderRadius:"7px",railBorderRadius:"7px"},g);this.each(function(){function s(d){d=d||window.event;
var c=0;d.wheelDelta&&(c=-d.wheelDelta/120);d.detail&&(c=d.detail/3);f(d.target||d.srcTarget||d.srcElement).closest("."+a.wrapperClass).is(b.parent())&&m(c,!0);d.preventDefault&&!k&&d.preventDefault();k||(d.returnValue=!1)}function m(d,f,g){k=!1;var e=d,h=b.outerHeight()-c.outerHeight();f&&(e=parseInt(c.css("top"))+d*parseInt(a.wheelStep)/100*c.outerHeight(),e=Math.min(Math.max(e,0),h),e=0<d?Math.ceil(e):Math.floor(e),c.css({top:e+"px"}));l=parseInt(c.css("top"))/(b.outerHeight()-c.outerHeight());
e=l*(b[0].scrollHeight-b.outerHeight());g&&(e=d,d=e/b[0].scrollHeight*b.outerHeight(),d=Math.min(Math.max(d,0),h),c.css({top:d+"px"}));b.scrollTop(e);b.trigger("slimscrolling",~~e);u();p()}function C(){window.addEventListener?(this.addEventListener("DOMMouseScroll",s,!1),this.addEventListener("mousewheel",s,!1)):document.attachEvent("onmousewheel",s)}function v(){r=Math.max(b.outerHeight()/b[0].scrollHeight*b.outerHeight(),D);c.css({height:r+"px"});var a=r==b.outerHeight()?"none":"block";c.css({display:a})}
function u(){v();clearTimeout(A);l==~~l?(k=a.allowPageScroll,B!=l&&b.trigger("slimscroll",0==~~l?"top":"bottom")):k=!1;B=l;r>=b.outerHeight()?k=!0:(c.stop(!0,!0).fadeIn("fast"),a.railVisible&&h.stop(!0,!0).fadeIn("fast"))}function p(){a.alwaysVisible||(A=setTimeout(function(){a.disableFadeOut&&w||x||y||(c.fadeOut("slow"),h.fadeOut("slow"))},1E3))}var w,x,y,A,z,r,l,B,D=30,k=!1,b=f(this);if(b.parent().hasClass(a.wrapperClass)){var n=b.scrollTop(),c=b.parent().find("."+a.barClass),h=b.parent().find("."+
a.railClass);v();if(f.isPlainObject(g)){if("height"in g&&"auto"==g.height){b.parent().css("height","auto");b.css("height","auto");var q=b.parent().parent().height();b.parent().css("height",q);b.css("height",q)}if("scrollTo"in g)n=parseInt(a.scrollTo);else if("scrollBy"in g)n+=parseInt(a.scrollBy);else if("destroy"in g){c.remove();h.remove();b.unwrap();return}m(n,!1,!0)}}else{a.height="auto"==g.height?b.parent().height():g.height;n=f("<div></div>").addClass(a.wrapperClass).css({position:"relative",
overflow:"hidden",width:a.width,height:a.height});b.css({overflow:"hidden",width:a.width,height:a.height});var h=f("<div></div>").addClass(a.railClass).css({width:a.size,height:"100%",position:"absolute",top:0,display:a.alwaysVisible&&a.railVisible?"block":"none","border-radius":a.railBorderRadius,background:a.railColor,opacity:a.railOpacity,zIndex:90}),c=f("<div></div>").addClass(a.barClass).css({background:a.color,width:a.size,position:"absolute",top:0,opacity:a.opacity,display:a.alwaysVisible?
"block":"none","border-radius":a.borderRadius,BorderRadius:a.borderRadius,MozBorderRadius:a.borderRadius,WebkitBorderRadius:a.borderRadius,zIndex:99}),q="right"==a.position?{right:a.distance}:{left:a.distance};h.css(q);c.css(q);b.wrap(n);b.parent().append(c);b.parent().append(h);a.railDraggable&&c.bind("mousedown",function(a){var b=f(document);y=!0;t=parseFloat(c.css("top"));pageY=a.pageY;b.bind("mousemove.slimscroll",function(a){currTop=t+a.pageY-pageY;c.css("top",currTop);m(0,c.position().top,!1)});
b.bind("mouseup.slimscroll",function(a){y=!1;p();b.unbind(".slimscroll")});return!1}).bind("selectstart.slimscroll",function(a){a.stopPropagation();a.preventDefault();return!1});h.hover(function(){u()},function(){p()});c.hover(function(){x=!0},function(){x=!1});b.hover(function(){w=!0;u();p()},function(){w=!1;p()});b.bind("touchstart",function(a,b){a.originalEvent.touches.length&&(z=a.originalEvent.touches[0].pageY)});b.bind("touchmove",function(b){k||b.originalEvent.preventDefault();b.originalEvent.touches.length&&
(m((z-b.originalEvent.touches[0].pageY)/a.touchScrollStep,!0),z=b.originalEvent.touches[0].pageY)});v();"bottom"===a.start?(c.css({top:b.outerHeight()-c.outerHeight()}),m(0,!0)):"top"!==a.start&&(m(f(a.start).position().top,null,!0),a.alwaysVisible||c.hide());C()}});return this}});jQuery.fn.extend({slimscroll:jQuery.fn.slimScroll})})(jQuery);


/* Knob JS */
(function(e){if(typeof define==="function"&&define.amd){define(["jquery"],e)}else{e(jQuery)}})(function(e){"use strict";var t={},n=Math.max,r=Math.min;t.c={};t.c.d=e(document);t.c.t=function(e){return e.originalEvent.touches.length-1};t.o=function(){var n=this;this.o=null;this.$=null;this.i=null;this.g=null;this.v=null;this.cv=null;this.x=0;this.y=0;this.w=0;this.h=0;this.$c=null;this.c=null;this.t=0;this.isInit=false;this.fgColor=null;this.pColor=null;this.dH=null;this.cH=null;this.eH=null;this.rH=null;this.scale=1;this.relative=false;this.relativeWidth=false;this.relativeHeight=false;this.$div=null;this.run=function(){var t=function(e,t){var r;for(r in t){n.o[r]=t[r]}n._carve().init();n._configure()._draw()};if(this.$.data("kontroled"))return;this.$.data("kontroled",true);this.extend();this.o=e.extend({min:this.$.data("min")!==undefined?this.$.data("min"):0,max:this.$.data("max")!==undefined?this.$.data("max"):100,stopper:true,readOnly:this.$.data("readonly")||this.$.attr("readonly")==="readonly",cursor:this.$.data("cursor")===true&&30||this.$.data("cursor")||0,thickness:this.$.data("thickness")&&Math.max(Math.min(this.$.data("thickness"),1),.01)||.35,lineCap:this.$.data("linecap")||"butt",width:this.$.data("width")||200,height:this.$.data("height")||200,displayInput:this.$.data("displayinput")==null||this.$.data("displayinput"),displayPrevious:this.$.data("displayprevious"),fgColor:this.$.data("fgcolor")||"#87CEEB",inputColor:this.$.data("inputcolor"),font:this.$.data("font")||"Arial",fontWeight:this.$.data("font-weight")||"bold",inline:false,step:this.$.data("step")||1,rotation:this.$.data("rotation"),draw:null,change:null,cancel:null,release:null,format:function(e){return e},parse:function(e){return parseFloat(e)}},this.o);this.o.flip=this.o.rotation==="anticlockwise"||this.o.rotation==="acw";if(!this.o.inputColor){this.o.inputColor=this.o.fgColor}if(this.$.is("fieldset")){this.v={};this.i=this.$.find("input");this.i.each(function(t){var r=e(this);n.i[t]=r;n.v[t]=n.o.parse(r.val());r.bind("change blur",function(){var e={};e[t]=r.val();n.val(n._validate(e))})});this.$.find("legend").remove()}else{this.i=this.$;this.v=this.o.parse(this.$.val());this.v===""&&(this.v=this.o.min);this.$.bind("change blur",function(){n.val(n._validate(n.o.parse(n.$.val())))})}!this.o.displayInput&&this.$.hide();this.$c=e(document.createElement("canvas")).attr({width:this.o.width,height:this.o.height});this.$div=e('<div style="'+(this.o.inline?"display:inline;":"")+"width:"+this.o.width+"px;height:"+this.o.height+"px;"+'"></div>');this.$.wrap(this.$div).before(this.$c);this.$div=this.$.parent();if(typeof G_vmlCanvasManager!=="undefined"){G_vmlCanvasManager.initElement(this.$c[0])}this.c=this.$c[0].getContext?this.$c[0].getContext("2d"):null;if(!this.c){throw{name:"CanvasNotSupportedException",message:"Canvas not supported. Please use excanvas on IE8.0.",toString:function(){return this.name+": "+this.message}}}this.scale=(window.devicePixelRatio||1)/(this.c.webkitBackingStorePixelRatio||this.c.mozBackingStorePixelRatio||this.c.msBackingStorePixelRatio||this.c.oBackingStorePixelRatio||this.c.backingStorePixelRatio||1);this.relativeWidth=this.o.width%1!==0&&this.o.width.indexOf("%");this.relativeHeight=this.o.height%1!==0&&this.o.height.indexOf("%");this.relative=this.relativeWidth||this.relativeHeight;this._carve();if(this.v instanceof Object){this.cv={};this.copy(this.v,this.cv)}else{this.cv=this.v}this.$.bind("configure",t).parent().bind("configure",t);this._listen()._configure()._xy().init();this.isInit=true;this.$.val(this.o.format(this.v));this._draw();return this};this._carve=function(){if(this.relative){var e=this.relativeWidth?this.$div.parent().width()*parseInt(this.o.width)/100:this.$div.parent().width(),t=this.relativeHeight?this.$div.parent().height()*parseInt(this.o.height)/100:this.$div.parent().height();this.w=this.h=Math.min(e,t)}else{this.w=this.o.width;this.h=this.o.height}this.$div.css({width:this.w+"px",height:this.h+"px"});this.$c.attr({width:this.w,height:this.h});if(this.scale!==1){this.$c[0].width=this.$c[0].width*this.scale;this.$c[0].height=this.$c[0].height*this.scale;this.$c.width(this.w);this.$c.height(this.h)}return this};this._draw=function(){var e=true;n.g=n.c;n.clear();n.dH&&(e=n.dH());e!==false&&n.draw()};this._touch=function(e){var r=function(e){var t=n.xy2val(e.originalEvent.touches[n.t].pageX,e.originalEvent.touches[n.t].pageY);if(t==n.cv)return;if(n.cH&&n.cH(t)===false)return;n.change(n._validate(t));n._draw()};this.t=t.c.t(e);r(e);t.c.d.bind("touchmove.k",r).bind("touchend.k",function(){t.c.d.unbind("touchmove.k touchend.k");n.val(n.cv)});return this};this._mouse=function(e){var r=function(e){var t=n.xy2val(e.pageX,e.pageY);if(t==n.cv)return;if(n.cH&&n.cH(t)===false)return;n.change(n._validate(t));n._draw()};r(e);t.c.d.bind("mousemove.k",r).bind("keyup.k",function(e){if(e.keyCode===27){t.c.d.unbind("mouseup.k mousemove.k keyup.k");if(n.eH&&n.eH()===false)return;n.cancel()}}).bind("mouseup.k",function(e){t.c.d.unbind("mousemove.k mouseup.k keyup.k");n.val(n.cv)});return this};this._xy=function(){var e=this.$c.offset();this.x=e.left;this.y=e.top;return this};this._listen=function(){if(!this.o.readOnly){this.$c.bind("mousedown",function(e){e.preventDefault();n._xy()._mouse(e)}).bind("touchstart",function(e){e.preventDefault();n._xy()._touch(e)});this.listen()}else{this.$.attr("readonly","readonly")}if(this.relative){e(window).resize(function(){n._carve().init();n._draw()})}return this};this._configure=function(){if(this.o.draw)this.dH=this.o.draw;if(this.o.change)this.cH=this.o.change;if(this.o.cancel)this.eH=this.o.cancel;if(this.o.release)this.rH=this.o.release;if(this.o.displayPrevious){this.pColor=this.h2rgba(this.o.fgColor,"0.4");this.fgColor=this.h2rgba(this.o.fgColor,"0.6")}else{this.fgColor=this.o.fgColor}return this};this._clear=function(){this.$c[0].width=this.$c[0].width};this._validate=function(e){var t=~~((e<0?-.5:.5)+e/this.o.step)*this.o.step;return Math.round(t*100)/100};this.listen=function(){};this.extend=function(){};this.init=function(){};this.change=function(e){};this.val=function(e){};this.xy2val=function(e,t){};this.draw=function(){};this.clear=function(){this._clear()};this.h2rgba=function(e,t){var n;e=e.substring(1,7);n=[parseInt(e.substring(0,2),16),parseInt(e.substring(2,4),16),parseInt(e.substring(4,6),16)];return"rgba("+n[0]+","+n[1]+","+n[2]+","+t+")"};this.copy=function(e,t){for(var n in e){t[n]=e[n]}}};t.Dial=function(){t.o.call(this);this.startAngle=null;this.xy=null;this.radius=null;this.lineWidth=null;this.cursorExt=null;this.w2=null;this.PI2=2*Math.PI;this.extend=function(){this.o=e.extend({bgColor:this.$.data("bgcolor")||"#EEEEEE",angleOffset:this.$.data("angleoffset")||0,angleArc:this.$.data("anglearc")||360,inline:true},this.o)};this.val=function(e,t){if(null!=e){e=this.o.parse(e);if(t!==false&&e!=this.v&&this.rH&&this.rH(e)===false){return}this.cv=this.o.stopper?n(r(e,this.o.max),this.o.min):e;this.v=this.cv;this.$.val(this.o.format(this.v));this._draw()}else{return this.v}};this.xy2val=function(e,t){var i,s;i=Math.atan2(e-(this.x+this.w2),-(t-this.y-this.w2))-this.angleOffset;if(this.o.flip){i=this.angleArc-i-this.PI2}if(this.angleArc!=this.PI2&&i<0&&i>-.5){i=0}else if(i<0){i+=this.PI2}s=i*(this.o.max-this.o.min)/this.angleArc+this.o.min;this.o.stopper&&(s=n(r(s,this.o.max),this.o.min));return s};this.listen=function(){var t=this,i,s,o=function(e){e.preventDefault();var o=e.originalEvent,u=o.detail||o.wheelDeltaX,a=o.detail||o.wheelDeltaY,f=t._validate(t.o.parse(t.$.val()))+(u>0||a>0?t.o.step:u<0||a<0?-t.o.step:0);f=n(r(f,t.o.max),t.o.min);t.val(f,false);if(t.rH){clearTimeout(i);i=setTimeout(function(){t.rH(f);i=null},100);if(!s){s=setTimeout(function(){if(i)t.rH(f);s=null},200)}}},u,a,f=1,l={37:-t.o.step,38:t.o.step,39:t.o.step,40:-t.o.step};this.$.bind("keydown",function(i){var s=i.keyCode;if(s>=96&&s<=105){s=i.keyCode=s-48}u=parseInt(String.fromCharCode(s));if(isNaN(u)){s!==13&&s!==8&&s!==9&&s!==189&&(s!==190||t.$.val().match(/\./))&&i.preventDefault();if(e.inArray(s,[37,38,39,40])>-1){i.preventDefault();var o=t.o.parse(t.$.val())+l[s]*f;t.o.stopper&&(o=n(r(o,t.o.max),t.o.min));t.change(t._validate(o));t._draw();a=window.setTimeout(function(){f*=2},30)}}}).bind("keyup",function(e){if(isNaN(u)){if(a){window.clearTimeout(a);a=null;f=1;t.val(t.$.val())}}else{t.$.val()>t.o.max&&t.$.val(t.o.max)||t.$.val()<t.o.min&&t.$.val(t.o.min)}});this.$c.bind("mousewheel DOMMouseScroll",o);this.$.bind("mousewheel DOMMouseScroll",o)};this.init=function(){if(this.v<this.o.min||this.v>this.o.max){this.v=this.o.min}this.$.val(this.v);this.w2=this.w/2;this.cursorExt=this.o.cursor/100;this.xy=this.w2*this.scale;this.lineWidth=this.xy*this.o.thickness;this.lineCap=this.o.lineCap;this.radius=this.xy-this.lineWidth/2;this.o.angleOffset&&(this.o.angleOffset=isNaN(this.o.angleOffset)?0:this.o.angleOffset);this.o.angleArc&&(this.o.angleArc=isNaN(this.o.angleArc)?this.PI2:this.o.angleArc);this.angleOffset=this.o.angleOffset*Math.PI/180;this.angleArc=this.o.angleArc*Math.PI/180;this.startAngle=1.5*Math.PI+this.angleOffset;this.endAngle=1.5*Math.PI+this.angleOffset+this.angleArc;var e=n(String(Math.abs(this.o.max)).length,String(Math.abs(this.o.min)).length,2)+2;this.o.displayInput&&this.i.css({width:(this.w/2+4>>0)+"px",height:(this.w/3>>0)+"px",position:"absolute","vertical-align":"middle","margin-top":(this.w/3>>0)+"px","margin-left":"-"+(this.w*3/4+2>>0)+"px",border:0,background:"none",font:this.o.fontWeight+" "+(this.w/e>>0)+"px "+this.o.font,"text-align":"center",color:this.o.inputColor||this.o.fgColor,padding:"0px","-webkit-appearance":"none"})||this.i.css({width:"0px",visibility:"hidden"})};this.change=function(e){this.cv=e;this.$.val(this.o.format(e))};this.angle=function(e){return(e-this.o.min)*this.angleArc/(this.o.max-this.o.min)};this.arc=function(e){var t,n;e=this.angle(e);if(this.o.flip){t=this.endAngle+1e-5;n=t-e-1e-5}else{t=this.startAngle-1e-5;n=t+e+1e-5}this.o.cursor&&(t=n-this.cursorExt)&&(n=n+this.cursorExt);return{s:t,e:n,d:this.o.flip&&!this.o.cursor}};this.draw=function(){var e=this.g,t=this.arc(this.cv),n,r=1;e.lineWidth=this.lineWidth;e.lineCap=this.lineCap;if(this.o.bgColor!=="none"){e.beginPath();e.strokeStyle=this.o.bgColor;e.arc(this.xy,this.xy,this.radius,this.endAngle-1e-5,this.startAngle+1e-5,true);e.stroke()}if(this.o.displayPrevious){n=this.arc(this.v);e.beginPath();e.strokeStyle=this.pColor;e.arc(this.xy,this.xy,this.radius,n.s,n.e,n.d);e.stroke();r=this.cv==this.v}e.beginPath();e.strokeStyle=r?this.o.fgColor:this.fgColor;e.arc(this.xy,this.xy,this.radius,t.s,t.e,t.d);e.stroke()};this.cancel=function(){this.val(this.v)}};e.fn.dial=e.fn.knob=function(n){return this.each(function(){var r=new t.Dial;r.o=n;r.$=e(this);r.run()}).parent()}})


/* Vegas Image slideshow */
!function(t){"use strict";var s={slide:0,delay:5e3,preload:!1,preloadImage:!1,preloadVideo:!1,timer:!0,overlay:!1,autoplay:!0,shuffle:!1,cover:!0,color:null,align:"center",valign:"center",transition:"fade",transitionDuration:1e3,transitionRegister:[],animation:null,animationDuration:"auto",animationRegister:[],init:function(){},play:function(){},pause:function(){},walk:function(){},slides:[]},i={},e=function(i,e){this.elmt=i,this.settings=t.extend({},s,t.vegas.defaults,e),this.slide=this.settings.slide,this.total=this.settings.slides.length,this.noshow=this.total<2,this.paused=!this.settings.autoplay||this.noshow,this.$elmt=t(i),this.$timer=null,this.$overlay=null,this.$slide=null,this.timeout=null,this.transitions=["fade","fade2","blur","blur2","flash","flash2","negative","negative2","burn","burn2","slideLeft","slideLeft2","slideRight","slideRight2","slideUp","slideUp2","slideDown","slideDown2","zoomIn","zoomIn2","zoomOut","zoomOut2","swirlLeft","swirlLeft2","swirlRight","swirlRight2"],this.animations=["kenburns","kenburnsLeft","kenburnsRight","kenburnsUp","kenburnsUpLeft","kenburnsUpRight","kenburnsDown","kenburnsDownLeft","kenburnsDownRight"],this.settings.transitionRegister instanceof Array==!1&&(this.settings.transitionRegister=[this.settings.transitionRegister]),this.settings.animationRegister instanceof Array==!1&&(this.settings.animationRegister=[this.settings.animationRegister]),this.transitions=this.transitions.concat(this.settings.transitionRegister),this.animations=this.animations.concat(this.settings.animationRegister),this.support={objectFit:"objectFit"in document.body.style,transition:"transition"in document.body.style||"WebkitTransition"in document.body.style,video:t.vegas.isVideoCompatible()},this.settings.shuffle===!0&&this.shuffle(),this._init()};e.prototype={_init:function(){var s,i,e,n="BODY"===this.elmt.tagName,o=this.settings.timer,a=this.settings.overlay,r=this;this._preload(),n||(this.$elmt.css("height",this.$elmt.css("height")),s=t('<div class="vegas-wrapper">').css("overflow",this.$elmt.css("overflow")).css("padding",this.$elmt.css("padding")),this.$elmt.css("padding")||s.css("padding-top",this.$elmt.css("padding-top")).css("padding-bottom",this.$elmt.css("padding-bottom")).css("padding-left",this.$elmt.css("padding-left")).css("padding-right",this.$elmt.css("padding-right")),this.$elmt.clone(!0).children().appendTo(s),this.elmt.innerHTML=""),o&&this.support.transition&&(e=t('<div class="vegas-timer"><div class="vegas-timer-progress">'),this.$timer=e,this.$elmt.prepend(e)),a&&(i=t('<div class="vegas-overlay">'),"string"==typeof a&&i.css("background-image","url("+a+")"),this.$overlay=i,this.$elmt.prepend(i)),this.$elmt.addClass("vegas-container"),n||this.$elmt.append(s),setTimeout(function(){r.trigger("init"),r._goto(r.slide),r.settings.autoplay&&r.trigger("play")},1)},_preload:function(){var t,s,i;for(i=0;i<this.settings.slides.length;i++)(this.settings.preload||this.settings.preloadImages)&&this.settings.slides[i].src&&(s=new Image,s.src=this.settings.slides[i].src),(this.settings.preload||this.settings.preloadVideos)&&this.support.video&&this.settings.slides[i].video&&(t=this._video(this.settings.slides[i].video))},_random:function(t){return t[Math.floor(Math.random()*(t.length-1))]},_slideShow:function(){var t=this;this.total>1&&!this.paused&&!this.noshow&&(this.timeout=setTimeout(function(){t.next()},this._options("delay")))},_timer:function(t){var s=this;clearTimeout(this.timeout),this.$timer&&(this.$timer.removeClass("vegas-timer-running").find("div").css("transition-duration","0ms"),this.paused||this.noshow||t&&setTimeout(function(){s.$timer.addClass("vegas-timer-running").find("div").css("transition-duration",s._options("delay")-100+"ms")},100))},_video:function(t){var s,e,n=t.toString();return i[n]?i[n]:(t instanceof Array==!1&&(t=[t]),s=document.createElement("video"),s.preload=!0,t.forEach(function(t){e=document.createElement("source"),e.src=t,s.appendChild(e)}),i[n]=s,s)},_fadeOutSound:function(t,s){var i=this,e=s/10,n=t.volume-.09;n>0?(t.volume=n,setTimeout(function(){i._fadeOutSound(t,s)},e)):t.pause()},_fadeInSound:function(t,s){var i=this,e=s/10,n=t.volume+.09;1>n&&(t.volume=n,setTimeout(function(){i._fadeInSound(t,s)},e))},_options:function(t,s){return void 0===s&&(s=this.slide),void 0!==this.settings.slides[s][t]?this.settings.slides[s][t]:this.settings[t]},_goto:function(s){function i(){f._timer(!0),setTimeout(function(){y&&(f.support.transition?(h.css("transition","all "+_+"ms").addClass("vegas-transition-"+y+"-out"),h.each(function(){var t=h.find("video").get(0);t&&(t.volume=1,f._fadeOutSound(t,_))}),e.css("transition","all "+_+"ms").addClass("vegas-transition-"+y+"-in")):e.fadeIn(_));for(var t=0;t<h.length-4;t++)h.eq(t).remove();f.trigger("walk"),f._slideShow()},100)}"undefined"==typeof this.settings.slides[s]&&(s=0),this.slide=s;var e,n,o,a,r,h=this.$elmt.children(".vegas-slide"),d=this.settings.slides[s].src,l=this.settings.slides[s].video,u=this._options("delay"),g=this._options("align"),c=this._options("valign"),p=this._options("color")||this.$elmt.css("background-color"),m=this._options("cover")?"cover":"contain",f=this,v=h.length,y=this._options("transition"),_=this._options("transitionDuration"),w=this._options("animation"),b=this._options("animationDuration");("random"===y||y instanceof Array)&&(y=this._random(y instanceof Array?y:this.transitions)),("random"===w||w instanceof Array)&&(w=this._random(w instanceof Array?w:this.animations)),("auto"===_||_>u)&&(_=u),"auto"===b&&(b=u),e=t('<div class="vegas-slide"></div>'),this.support.transition&&y&&e.addClass("vegas-transition-"+y),this.support.video&&l?(a=this._video(l instanceof Array?l:l.src),a.loop=void 0!==l.loop?l.loop:!0,a.muted=void 0!==l.mute?l.mute:!0,a.muted===!1?(a.volume=0,this._fadeInSound(a,_)):a.pause(),o=t(a).addClass("vegas-video").css("background-color",p),this.support.objectFit?o.css("object-position",g+" "+c).css("object-fit",m).css("width","100%").css("height","100%"):"contain"===m&&o.css("width","100%").css("height","100%"),e.append(o)):(r=new Image,n=t('<div class="vegas-slide-inner"></div>').css("background-image","url("+d+")").css("background-color",p).css("background-position",g+" "+c).css("background-size",m),this.support.transition&&w&&n.addClass("vegas-animation-"+w).css("animation-duration",b+"ms"),e.append(n)),this.support.transition||e.css("display","none"),v?h.eq(v-1).after(e):this.$elmt.prepend(e),f._timer(!1),a?(4===a.readyState&&(a.currentTime=0),a.play(),i()):(r.src=d,r.onload=i)},shuffle:function(){for(var t,s,i=this.total-1;i>0;i--)s=Math.floor(Math.random()*(i+1)),t=this.settings.slides[i],this.settings.slides[i]=this.settings.slides[s],this.settings.slides[s]=t},play:function(){this.paused&&(this.paused=!1,this.next(),this.trigger("play"))},pause:function(){this._timer(!1),this.paused=!0,this.trigger("pause")},toggle:function(){this.paused?this.play():this.pause()},playing:function(){return!this.paused&&!this.noshow},current:function(t){return t?{slide:this.slide,data:this.settings.slides[this.slide]}:this.slide},jump:function(t){0>t||t>this.total-1||t===this.slide||(this.slide=t,this._goto(this.slide))},next:function(){this.slide++,this.slide>=this.total&&(this.slide=0),this._goto(this.slide)},previous:function(){this.slide--,this.slide<0&&(this.slide=this.total-1),this._goto(this.slide)},trigger:function(t){var s=[];s="init"===t?[this.settings]:[this.slide,this.settings.slides[this.slide]],this.$elmt.trigger("vegas"+t,s),"function"==typeof this.settings[t]&&this.settings[t].apply(this.$elmt,s)},options:function(i,e){var n=this.settings.slides.slice();if("object"==typeof i)this.settings=t.extend({},s,t.vegas.defaults,i);else{if("string"!=typeof i)return this.settings;if(void 0===e)return this.settings[i];this.settings[i]=e}this.settings.slides!==n&&(this.total=this.settings.slides.length,this.noshow=this.total<2,this._preload())}},t.fn.vegas=function(t){var s,i=arguments,n=!1;if(void 0===t||"object"==typeof t)return this.each(function(){this._vegas||(this._vegas=new e(this,t))});if("string"==typeof t){if(this.each(function(){var e=this._vegas;if(!e)throw new Error("No Vegas applied to this element.");"function"==typeof e[t]&&"_"!==t[0]?s=e[t].apply(e,[].slice.call(i,1)):n=!0}),n)throw new Error('No method "'+t+'" in Vegas.');return void 0!==s?s:this}},t.vegas={},t.vegas.defaults=s,t.vegas.isVideoCompatible=function(){return!/(Android|webOS|Phone|iPad|iPod|BlackBerry|Windows Phone)/i.test(navigator.userAgent)}}(window.jQuery||window.Zepto);
//# sourceMappingURL=vegas.min.js.map


/*	--------------------------------------------------------------------
	MaxImage 2.0 (Fullscreen Slideshow for use with jQuery Cycle Plugin)
	--------------------------------------------------------------------
	
	Examples and documentation at: http://www.aaronvanderzwan.com/maximage/2.0/
	Copyright (c) 2007-2012 Aaron Vanderzwan
	Dual licensed under the MIT and GPL licenses.
	
	NOTES:
	This plugin is intended to simplify the creation of fullscreen 
	background slideshows.  It is intended to be used alongside the 
	jQuery Cycle plugin: 
	http://jquery.malsup.com/cycle/
	
	If you simply need a fullscreen background image, please
	refer to the following document for ways to do this that
	are much more simple:
	http://css-tricks.com/perfect-full-page-background-image/
	
	If you have any questions please contact Aaron Vanderzwan
	at http://www.aaronvanderzwan.com/blog/
	Documentation at:
	http://blog.aaronvanderzwan.com/2012/07/maximage-2-0/
	
	HISTORY:
	MaxImage 2.0 is a project first built as jQuery MaxImage Plugin 
	(http://www.aaronvanderzwan.com/maximage/). Once CSS3 came along, 
	the background-size:cover solved the problem MaxImage
	was intended to solve.  However, fully customizable
	fullscreen slideshows is still fairly complex and I have not
	found any helpers for integrating with the jQuery Cycle Plugin.
	MaxCycle is intended to solve this problem.
	
	TABLE OF CONTENTS:
	@Modern
		@setup
		@resize
		@preload
	@Old
		@setup
		@preload
		@onceloaded
		@maximage
		@windowresize
		@doneresizing
	@Cycle
		@setup
	@Adjust
		@center
		@fill
		@maxcover
		@maxcontain
	@Utils
		@browser_tests
		@construct_slide_object
		@sizes
	@modern_browser
	@debug
		
*/
/*!	
 * Maximage Version: 2.0.8 (16-Jan-2012) - http://www.aaronvanderzwan.com/maximage/2.0/
 */



(function ($) {
	"use strict";
	$.fn.maximage = function (settings, helperSettings) {

		var config;

		if (typeof settings == 'object' || settings === undefined) config = $.extend( $.fn.maximage.defaults, settings || {} );
		if (typeof settings == 'string') config = $.fn.maximage.defaults;
		
		/*jslint browser: true*/
		$.Body = $('body');
		$.Window = $(window);
		$.Scroll = $('html, body');
		$.Events = {
			RESIZE: 'resize'
		};
		
		this.each(function() {
			var $self = $(this),
				preload_count = 0,
				imageCache = [];
			
			/* --------------------- */
			
			// @Modern
			
			/* 
			MODERN BROWSER NOTES:
				Modern browsers have CSS3 background-size option so we setup the DOM to be the following structure for cycle plugin:
				div = cycle
					div = slide with background-size:cover
					div = slide with background-size:cover
					etc.
			*/
			
			var Modern = {
				setup: function(){
					if($.Slides.length > 0){
						// Setup images
						for(var i in $.Slides) {
							// Set our image
							var $img = $.Slides[i];
							
							// Create a div with a background image so we can use CSS3's position cover (for modern browsers)
							$self.append('<div class="mc-image ' + $img.theclass + '" title="' + $img.alt + '" style="background-image:url(\'' + $img.url + '\');' + $img.style + '" data-href="'+ $img.datahref +'">'+ $img.content +'</div>');
						}
						
						// Begin our preload process (increments itself after load)
						Modern.preload(0);
						
						// If using Cycle, this resets the height and width of each div to always fill the window; otherwise can be done with CSS
						Modern.resize();
					}
				},
				preload: function(n){
					// Preload all of the images but never show them, just use their completion so we know that they are done
					// 		and so that the browser can cache them / fade them in smoothly
					
					// Create new image object
					var $img = $('<img/>');
					$img.load(function() {
						// Once the first image has completed loading, start the slideshow, etc.
						if(preload_count==0) {
							// Only start cycle after first image has loaded
							Cycle.setup();
							
							// Run user defined onFirstImageLoaded() function
							config.onFirstImageLoaded();
						}
						
						// preload_count starts with 0, $.Slides.length starts with 1
						if(preload_count==($.Slides.length-1)) {
							// If we have just loaded the final image, run the user defined function onImagesLoaded()
							config.onImagesLoaded( $self );
						}else{ 
							// Increment the counter
							preload_count++;
							
							// Load the next image
							Modern.preload(preload_count);
						}
					});
					
					// Set the src... this triggers begin of load
					$img[0].src = $.Slides[n].url;
					
					// Push to external array to avoid cleanup by aggressive garbage collectors
					imageCache.push($img[0]);
				},
				resize: function(){
					// Cycle sets the height of each slide so when we resize our browser window this becomes a problem.
					//  - the cycle option 'slideResize' has to be set to false otherwise it will trump our resize
					$.Window
						.bind($.Events.RESIZE,
						function(){
							// Remove scrollbars so we can take propper measurements
							$.Scroll.addClass('mc-hide-scrolls');
							
							// Set vars so we don't have to constantly check it
							$.Window
								.data('h', Utils.sizes().h)
								.data('w', Utils.sizes().w);
							
							// Set container and slides height and width to match the window size
							$self
								.height($.Window.data('h')).width($.Window.data('w'))
								.children()
								.height($.Window.data('h')).width($.Window.data('w'));
							
							// This is special noise for cycle (cycle has separate height and width for each slide)
							$self.children().each(function(){
								this.cycleH = $.Window.data('h');
								this.cycleW = $.Window.data('w');
							});
							
							// Put the scrollbars back to how they were
							$($.Scroll).removeClass('mc-hide-scrolls');
						});
				}
			}
			
			
			
			/* --------------------- */
			
			// @Old
			
			/* 
			OLD BROWSER NOTES:
				We setup the dom to be the following structure for cycle plugin on old browsers:
				div = cycle
					div = slide
						img = full screen size image
					div = slide
						img = full screen size image
					etc.
			*/
			
			var Old = {
				setup: function(){
					var c, t, $div;
					
					// Clear container
					if($.BrowserTests.msie && !config.overrideMSIEStop){
						// Stop IE from continually trying to preload images that we already removed
						document.execCommand("Stop", false);
					}
					$self.html('');
					
					$.Body.addClass('mc-old-browser');
					
					if($.Slides.length > 0){
						// Remove scrollbars so we can take propper measurements
						$.Scroll.addClass('mc-hide-scrolls');
						
						// Cache our new dimensions
						$.Window
							.data('h', Utils.sizes().h)
							.data('w', Utils.sizes().w);
						
						// Add our loading div to the DOM
						$('body').append($("<div></div>").attr("class", "mc-loader").css({'position':'absolute','left':'-9999px'}));
						
						//  Loop through slides
						for(var j in $.Slides) {
							// Determine content (if container or image)
							if($.Slides[j].content.length == 0){
								c = '<img src="' + $.Slides[j].url + '" />';
							}else{
								c = $.Slides[j].content;
							}
							
							// Create Div
							$div = $("<div>" + c + "</div>").attr("class", "mc-image mc-image-n" + j + " " + $.Slides[j].theclass);
							
							// Add new container div to the DOM
							$self.append( $div );
							
							// Account for slides without images
							if($('.mc-image-n' + j).children('img').length == 0){
							}else{
								// Add first image to loader to get that started
								$('div.mc-loader').append( $('.mc-image-n' + j).children('img').first().clone().addClass('not-loaded') );
							}
						}
						
						// Begin preloading
						Old.preload();
						
						// Setup the resize function to listen for window changes
						Old.windowResize();
					}
				},
				preload: function(){
					// Intervals to tell if an images have loaded
					var t = setInterval(function() {
						$('.mc-loader').children('img').each(function(i){
							// Check if image is loaded
							var $img = $(this);
							
							// Loop through not-loaded images
							if($img.hasClass('not-loaded')){
								if( $img.height() > 0 ){
									// Remove Dom notice
									$(this).removeClass('not-loaded');
									
									// Set the dimensions
									var $img1 = $('div.mc-image-n' + i).children('img').first();
									
									$img1
										.data('h', $img.height())
										.data('w', $img.width())
										.data('ar', ($img.width() / $img.height()));
									
									// Go on
									Old.onceLoaded(i)
								}
							}
						});
					
						if( $('.not-loaded').length == 0){
							// Remove our loader element because all of our images are now loaded
							$('.mc-loader').remove();
							
							// Clear interval when all images are loaded
							clearInterval(t);
						}
					}, 1000);
				},
				onceLoaded: function(m){
					// Do maximage magic
					Old.maximage(m);
					
					// Once the first image has completed loading, start the slideshow, etc.
					if(m == 0) {
						// If we changed the visibility before, make sure it is back on
						$self.css({'visibility':'visible'});
						
						// Run user defined onFirstImageLoaded() function
						config.onFirstImageLoaded();
					
					// After everything is done loading, clean up
					}else if(m == $.Slides.length - 1){
						// Only start cycle after the first image has loaded
						Cycle.setup();
						
						// Put the scrollbars back to how they were
						$($.Scroll).removeClass('mc-hide-scrolls');
						
						// If we have just loaded the final image, run the user defined function onImagesLoaded()
						config.onImagesLoaded( $self );
						
						if(config.debug) {
							debug(' - Final Maximage - ');debug($self);
						}
					}
				},
				maximage: function(p){
					// Cycle sets the height of each slide so when we resize our browser window this becomes a problem.
					//  - the cycle option 'slideResize' has to be set to false otherwise it will trump our resize
					$('div.mc-image-n' + p)
						.height($.Window.data('h'))
						.width($.Window.data('w'))
						.children('img')
						.first()
						.each(function(){
							Adjust.maxcover($(this));
						});
				},
				windowResize: function(){
					$.Window
						.bind($.Events.RESIZE,
						function(){
							clearTimeout(this.id);
							this.id = setTimeout(Old.doneResizing, 200);
						});
				},
				doneResizing: function(){
					// The final resize (on finish)
					// Remove scrollbars so we can take propper measurements
					$($.Scroll).addClass('mc-hide-scrolls');
					
					// Cache our window's new dimensions
					$.Window
						.data('h', Utils.sizes().h)
						.data('w', Utils.sizes().w);
					
					// Set the container's height and width
					$self.height($.Window.data('h')).width($.Window.data('w'))
					
					// Set slide's height and width to match the window size
					$self.find('.mc-image').each(function(n){
						Old.maximage(n);
					});
					
					// Update cycle's ideas of what our slide's height and width should be
					var curr_opts = $self.data('cycle.opts');
					if(curr_opts != undefined){
						curr_opts.height = $.Window.data('h');
						curr_opts.width = $.Window.data('w');
						jQuery.each(curr_opts.elements, function(index, item) {
						    item.cycleW = $.Window.data('w');
							item.cycleH = $.Window.data('h');
						});
					}
					
					// Put the scrollbars back to how they were
					$($.Scroll).removeClass('mc-hide-scrolls');
				}
			}
			
			
			/* --------------------- */
			
			// @Cycle
			
			var Cycle = {
				setup: function(){
					var h,w;
					
					$self.addClass('mc-cycle');
					
					// Container sizes (if not set)
					$.Window
						.data('h', Utils.sizes().h)
						.data('w', Utils.sizes().w);
					
					// Prefer CSS Transitions
					jQuery.easing.easeForCSSTransition = function(x, t, b, c, d, s) {
						return b+c;
					};
					
					var cycleOptions = $.extend({
						fit:1,
						containerResize:0,
						height:$.Window.data('h'),
						width:$.Window.data('w'),
						slideResize: false,
						easing: ($.BrowserTests.cssTransitions && config.cssTransitions ? 'easeForCSSTransition' : 'swing')
					}, config.cycleOptions);
					
					$self.cycle( cycleOptions );
				}
			}
			
			
			
			/* --------------------- */
			
			// @Adjust = Math to center and fill all elements
			
			var Adjust = {
				center: function($item){
					// Note: if alignment is 'left' or 'right' it can be controlled with CSS once verticalCenter 
					// 	and horizontal center are set to false in the plugin options
					if(config.verticalCenter){
						$item.css({marginTop:(($item.height() - $.Window.data('h'))/2) * -1})
					}
					if(config.horizontalCenter){
						$item.css({marginLeft:(($item.width() - $.Window.data('w'))/2) * -1});
					}
				},
				fill: function($item){
					var $storageEl = $item.is('object') ? $item.parent().first() : $item;
					
					if(typeof config.backgroundSize == 'function'){
						// If someone wants to write their own fill() function, they can: example customBackgroundSize.html
						config.backgroundSize( $item );
					}else if(config.backgroundSize == 'cover'){
						if($.Window.data('w') / $.Window.data('h') < $storageEl.data('ar')){
							$item
								.height($.Window.data('h'))
								.width(($.Window.data('h') * $storageEl.data('ar')).toFixed(0));
						}else{
							$item
								.height(($.Window.data('w') / $storageEl.data('ar')).toFixed(0))
								.width($.Window.data('w'));
						}
					}else if(config.backgroundSize == 'contain'){
						if($.Window.data('w') / $.Window.data('h') < $storageEl.data('ar')){
							$item
								.height(($.Window.data('w') / $storageEl.data('ar')).toFixed(0))
								.width($.Window.data('w'));
						}else{
							$item
								.height($.Window.data('h'))
								.width(($.Window.data('h') * $storageEl.data('ar')).toFixed(0));
						}
					}else{
						debug('The backgroundSize option was not recognized for older browsers.');
					}
				},
				maxcover: function($item){
					Adjust.fill($item);
					Adjust.center($item);
				},
				maxcontain: function($item){
					Adjust.fill($item);
					Adjust.center($item);
				}
			}
			
			
			
			/* --------------------- */
			
			// @Utils = General utilities for the plugin
			
			var Utils = {
				browser_tests: function(){
					var $div = $('<div />')[0],
						vendor = ['Moz', 'Webkit', 'Khtml', 'O', 'ms'],
						p = 'transition',
						obj = {
							cssTransitions: false,
							cssBackgroundSize: ( "backgroundSize" in $div.style && config.cssBackgroundSize ), // Can override cssBackgroundSize in options
							html5Video: false,
							msie: false
						};
					
					// Test for CSS Transitions
					if(config.cssTransitions){
						if(typeof $div.style[p] == 'string') { obj.cssTransitions = true }
					
						// Tests for vendor specific prop
						p = p.charAt(0).toUpperCase() + p.substr(1);
						for(var i=0; i<vendor.length; i++) {
							if(vendor[i] + p in $div.style) { obj.cssTransitions = true; }
						}
					}
					
					// Check if we can play html5 videos
					if( !!document.createElement('video').canPlayType ) {
						obj.html5Video = true;
					}
					
					// Check for MSIE since we lost $.browser in jQuery
					obj.msie = (Utils.msie() !== undefined);
					
					
					if(config.debug) {
						debug(' - Browser Test - ');debug(obj);
					}
					
					return obj;
				},
				construct_slide_object: function(){
					var obj = new Object(),
						arr = new Array(),
						temp = '';
					
					$self.children().each(function(i){
						var $img = $(this).is('img') ? $(this).clone() : $(this).find('img').first().clone();
						
						// reset obj
						obj = {};
						
						// set attributes to obj
						obj.url = $img.attr('src');
						obj.title = $img.attr('title') != undefined ? $img.attr('title') : '';
						obj.alt = $img.attr('alt') != undefined ? $img.attr('alt') : '';
						obj.theclass = $img.attr('class') != undefined ? $img.attr('class') : '';
						obj.styles = $img.attr('style') != undefined ? $img.attr('style') : '';
						obj.orig = $img.clone();
						obj.datahref = $img.attr('data-href') != undefined ? $img.attr('data-href') : '';
						obj.content = "";
						
						// Setup content for within container
						if($(this).find('img').length > 0){
							if($.BrowserTests.cssBackgroundSize){
								$(this).find('img').first().remove();
							}
							obj.content = $(this).html();
						}
						
						// Stop loading image so we can load them sequentiallyelse{
						$img[0].src = "";
						
						// Remove original object (only on nonIE. IE hangs if you remove an image during load)
						if($.BrowserTests.cssBackgroundSize){
							$(this).remove();
						}
						
						// attach obj to arr
						arr.push(obj);
					});
					
					
					if(config.debug) {
						debug(' - Slide Object - ');debug(arr);
					}
					return arr;
				},
				msie: function(){
				    var undef,
				        v = 3,
				        div = document.createElement('div'),
				        all = div.getElementsByTagName('i');

				    while (
				        div.innerHTML = '<!--[if gt IE ' + (++v) + ']><i></i><![endif]-->',
				        all[0]
				    );
					
				    return v > 4 ? v : undef;
				},
				sizes: function(){
					var sizes = {h:0,w:0};
					
					if(config.fillElement == "window"){
						sizes.h = $.Window.height();
						sizes.w = $.Window.width();
					}else{
						var $fillElement = $self.parents(config.fillElement).first();
						
						// Height
						if($fillElement.height() == 0 || $fillElement.data('windowHeight') == true){
							$fillElement.data('windowHeight',true);
							sizes.h = $.Window.height();
						}else{
							sizes.h = $fillElement.height();
						}
					
						// Width
						if($fillElement.width() == 0 || $fillElement.data('windowWidth') == true){
							$fillElement.data('windowWidth',true);
							sizes.w = $.Window.width();
						}else{
							sizes.w = $fillElement.width();
						}
					}
					
					return sizes;
				}
			}
			
			
			
			/* --------------------- */
			
			// @Instantiation
			
			// Helper Function
			// Run tests to see what our browser can handle
			$.BrowserTests = Utils.browser_tests();
			
			if(typeof settings == 'string'){
				// TODO: Resize object fallback for old browsers,  If we are trying to size an HTML5 video and our browser doesn't support it
				if($.BrowserTests.html5Video || !$self.is('video')) {
					var to, 
						$storageEl = $self.is('object') ? $self.parent().first() : $self; // Can't assign .data() to '<object>'
					
					if( !$.Body.hasClass('mc-old-browser') )
						$.Body.addClass('mc-old-browser');
					
					// Cache our window's new dimensions
					$.Window
						.data('h', Utils.sizes().h)
						.data('w', Utils.sizes().w);
				
					// Please include height and width attributes on your html elements
					$storageEl
						.data('h', $self.height())
						.data('w', $self.width())
						.data('ar', $self.width() / $self.height());
				
					// We want to resize these elements with the window
					$.Window
						.bind($.Events.RESIZE,
						function(){
							// Cache our window's new dimensions
							$.Window
								.data('h', Utils.sizes().h)
								.data('w', Utils.sizes().w);
						
							// Limit resize runs
							to = $self.data('resizer');
							clearTimeout(to);
							to = setTimeout( Adjust[settings]($self), 200 );
							$self.data('resizer', to);
						});
				
					// Initial run
					Adjust[settings]($self);
				}
			}else{
				// Construct array of image objects for us to use
				$.Slides = Utils.construct_slide_object();
				
				// If we are allowing background-size:cover run Modern
				if($.BrowserTests.cssBackgroundSize){
					if(config.debug) debug(' - Using Modern - ');
					Modern.setup();
				}else{
					if(config.debug) debug(' - Using Old - ');
					Old.setup();
				}
			}
		});
		
		// private function for debugging
		function debug($obj) {
			if (window.console && window.console.log) {
				window.console.log($obj);
			}
		}
	}
	
	// Default options
	$.fn.maximage.defaults = {
		debug: false,
		cssBackgroundSize: true,  // Force run the functionality used for newer browsers
		cssTransitions: true,  // Force run the functionality used for old browsers
		verticalCenter: true, // Only necessary for old browsers
		horizontalCenter: true, // Only necessary for old browsers
		scaleInterval: 20, // Only necessary for old browsers
		backgroundSize: 'cover', // Only necessary for old browsers (this can be function)
		fillElement: 'window', // Either 'window' or a CSS selector for a parent element
		overrideMSIEStop: false, // This gives the option to not 'stop' load for MSIE (stops coded background images from loading so we can preload)... 
								 // If setting this option to true, please beware of IE7/8 "Stack Overflow" error but if there are more than 13 slides
								 // The description of the bug: http://blog.aaronvanderzwan.com/forums/topic/stack-overflow-in-ie-7-8/#post-33038
		onFirstImageLoaded: function(){},
		onImagesLoaded: function(){}
	}
})(jQuery);

/* okvideo by okfocus ~ v2.3.2 ~ https://github.com/okfocus/okvideo */
function vimeoPlayerReady(){options=jQuery(window).data("okoptions");var a=jQuery("#okplayer")[0];player=$f(a),window.setTimeout(function(){jQuery("#okplayer").css("visibility","visible")},2e3),player.addEvent("ready",function(){OKEvents.v.onReady(),OKEvents.utils.isMobile()?OKEvents.v.onPlay():(player.addEvent("play",OKEvents.v.onPlay),player.addEvent("pause",OKEvents.v.onPause),player.addEvent("finish",OKEvents.v.onFinish)),player.api("play")})}function onYouTubePlayerAPIReady(){options=jQuery(window).data("okoptions"),player=new YT.Player("okplayer",{videoId:options.video?options.video.id:null,playerVars:{autohide:1,autoplay:0,disablekb:options.keyControls,cc_load_policy:options.captions,controls:options.controls,enablejsapi:1,fs:0,modestbranding:1,origin:window.location.origin||window.location.protocol+"//"+window.location.hostname,iv_load_policy:options.annotations,loop:options.loop,showinfo:0,rel:0,wmode:"opaque",hd:options.hd},events:{onReady:OKEvents.yt.ready,onStateChange:OKEvents.yt.onStateChange,onError:OKEvents.yt.error}})}var player,OKEvents,options;!function(a){"use strict";var b="data:image/gif;base64,R0lGODlhAQABAPABAP///wAAACH5BAEKAAAALAAAAAABAAEAAAICRAEAOw%3D%3D";a.okvideo=function(c){"object"!=typeof c&&(c={video:c});var d=this;d.init=function(){d.options=a.extend({},a.okvideo.options,c),null===d.options.video&&(d.options.video=d.options.source),d.setOptions();var e=d.options.target||a("body"),f=e[0]==a("body")[0]?"fixed":"absolute";e.css({position:"relative"});var g=3===d.options.controls?-999:"auto",h='<div id="okplayer-mask" style="position:'+f+';left:0;top:0;overflow:hidden;z-index:-998;height:100%;width:100%;"></div>';OKEvents.utils.isMobile()?e.append('<div id="okplayer" style="position:'+f+";left:0;top:0;overflow:hidden;z-index:"+g+';height:100%;width:100%;"></div>'):(3===d.options.controls&&e.append(h),1===d.options.adproof?e.append('<div id="okplayer" style="position:'+f+";left:-10%;top:-10%;overflow:hidden;z-index:"+g+';height:120%;width:120%;"></div>'):e.append('<div id="okplayer" style="position:'+f+";left:0;top:0;overflow:hidden;z-index:"+g+';height:100%;width:100%;"></div>')),a("#okplayer-mask").css("background-image","url("+b+")"),null===d.options.playlist.list?"youtube"===d.options.video.provider?d.loadYouTubeAPI():"vimeo"===d.options.video.provider&&(d.options.volume/=100,d.loadVimeoAPI()):d.loadYouTubeAPI()},d.setOptions=function(){for(var b in this.options)this.options[b]===!0&&(this.options[b]=1),this.options[b]===!1&&(this.options[b]=3);null===d.options.playlist.list&&(d.options.video=d.determineProvider()),a(window).data("okoptions",d.options)},d.loadYouTubeAPI=function(){d.insertJS("//www.youtube.com/player_api")},d.loadYouTubePlaylist=function(){player.loadPlaylist(d.options.playlist.list,d.options.playlist.index,d.options.playlist.startSeconds,d.options.playlist.suggestedQuality)},d.loadVimeoAPI=function(){a("#okplayer").replaceWith(function(){return'<iframe src="//player.vimeo.com/video/'+d.options.video.id+"?api=1&title=0&byline=0&portrait=0&playbar=0&loop="+d.options.loop+"&autoplay="+(1===d.options.autoplay?1:0)+'&player_id=okplayer" frameborder="0" style="'+a(this).attr("style")+'visibility:hidden;background-color:black;" id="'+a(this).attr("id")+'"></iframe>'}),d.insertJS("//origin-assets.vimeo.com/js/froogaloop2.min.js",function(){vimeoPlayerReady()})},d.insertJS=function(a,b){var c=document.createElement("script");b&&(c.readyState?c.onreadystatechange=function(){("loaded"===c.readyState||"complete"===c.readyState)&&(c.onreadystatechange=null,b())}:c.onload=function(){b()}),c.src=a;var d=document.getElementsByTagName("script")[0];d.parentNode.insertBefore(c,d)},d.determineProvider=function(){var a=document.createElement("a");if(a.href=d.options.video,/youtube.com/.test(d.options.video))return{provider:"youtube",id:a.href.slice(a.href.indexOf("v=")+2).toString()};if(/vimeo.com/.test(d.options.video))return{provider:"vimeo",id:a.href.split("/")[3].toString()};if(/[-A-Za-z0-9_]+/.test(d.options.video)){var b=new String(d.options.video.match(/[-A-Za-z0-9_]+/));if(11==b.length)return{provider:"youtube",id:b.toString()};for(var c=0;c<d.options.video.length;c++)if("number"!=typeof parseInt(d.options.video[c]))throw"not vimeo but thought it was for a sec";return{provider:"vimeo",id:d.options.video}}throw"OKVideo: Invalid video source"},d.init()},a.okvideo.options={source:null,video:null,playlist:{list:null,index:0,startSeconds:0,suggestedQuality:"default"},disableKeyControl:1,captions:0,loop:1,hd:1,volume:0,adproof:!1,unstarted:null,onFinished:null,onReady:null,onPlay:null,onPause:null,buffering:null,controls:!1,autoplay:!0,annotations:!0,cued:null},a.fn.okvideo=function(b){return b.target=this,this.each(function(){new a.okvideo(b)})}}(jQuery),OKEvents={yt:{ready:function(a){a.target.setVolume(options.volume),1===options.autoplay&&(options.playlist.list?player.loadPlaylist(options.playlist.list,options.playlist.index,options.playlist.startSeconds,options.playlist.suggestedQuality):a.target.playVideo()),OKEvents.utils.isFunction(options.onReady)&&options.onReady()},onStateChange:function(a){switch(a.data){case-1:OKEvents.utils.isFunction(options.unstarted)&&options.unstarted();break;case 0:OKEvents.utils.isFunction(options.onFinished)&&options.onFinished(),options.loop&&a.target.playVideo();break;case 1:OKEvents.utils.isFunction(options.onPlay)&&options.onPlay();break;case 2:OKEvents.utils.isFunction(options.onPause)&&options.onPause();break;case 3:OKEvents.utils.isFunction(options.buffering)&&options.buffering();break;case 5:OKEvents.utils.isFunction(options.cued)&&options.cued();break;default:throw"OKVideo: received invalid data from YT player."}},error:function(a){throw a}},v:{onReady:function(){OKEvents.utils.isFunction(options.onReady)&&options.onReady()},onPlay:function(){OKEvents.utils.isMobile()||player.api("setVolume",options.volume),OKEvents.utils.isFunction(options.onPlay)&&options.onPlay()},onPause:function(){OKEvents.utils.isFunction(options.onPause)&&options.onPause()},onFinish:function(){OKEvents.utils.isFunction(options.onFinish)&&options.onFinish()}},utils:{isFunction:function(a){return"function"==typeof a?!0:!1},isMobile:function(){return navigator.userAgent.match(/(iPhone|iPod|iPad|Android|BlackBerry)/)?!0:!1}}};

/* OWL Caroussel */
!function(t,e,i,s){function n(e,i){this.settings=null,this.options=t.extend({},n.Defaults,i),this.$element=t(e),this.drag=t.extend({},p),this.state=t.extend({},u),this.e=t.extend({},g),this._plugins={},this._supress={},this._current=null,this._speed=null,this._coordinates=[],this._breakpoint=null,this._width=null,this._items=[],this._clones=[],this._mergers=[],this._invalidated={},this._pipe=[],t.each(n.Plugins,t.proxy(function(t,e){this._plugins[t[0].toLowerCase()+t.slice(1)]=new e(this)},this)),t.each(n.Pipe,t.proxy(function(e,i){this._pipe.push({filter:i.filter,run:t.proxy(i.run,this)})},this)),this.setup(),this.initialize()}function o(t){if(t.touches!==s)return{x:t.touches[0].pageX,y:t.touches[0].pageY};if(t.touches===s){if(t.pageX!==s)return{x:t.pageX,y:t.pageY};if(t.pageX===s)return{x:t.clientX,y:t.clientY}}}function r(t){var e,s,n=i.createElement("div"),o=t;for(e in o)if(s=o[e],"undefined"!=typeof n.style[s])return n=null,[s,e];return[!1]}function a(){return r(["transition","WebkitTransition","MozTransition","OTransition"])[1]}function h(){return r(["transform","WebkitTransform","MozTransform","OTransform","msTransform"])[0]}function l(){return r(["perspective","webkitPerspective","MozPerspective","OPerspective","MsPerspective"])[0]}function c(){return"ontouchstart"in e||!!navigator.msMaxTouchPoints}function d(){return e.navigator.msPointerEnabled}var p,u,g;p={start:0,startX:0,startY:0,current:0,currentX:0,currentY:0,offsetX:0,offsetY:0,distance:null,startTime:0,endTime:0,updatedX:0,targetEl:null},u={isTouch:!1,isScrolling:!1,isSwiping:!1,direction:!1,inMotion:!1},g={_onDragStart:null,_onDragMove:null,_onDragEnd:null,_transitionEnd:null,_resizer:null,_responsiveCall:null,_goToLoop:null,_checkVisibile:null},n.Defaults={items:3,loop:!1,center:!1,mouseDrag:!0,touchDrag:!0,pullDrag:!0,freeDrag:!1,margin:0,stagePadding:0,merge:!1,mergeFit:!0,autoWidth:!1,startPosition:0,rtl:!1,smartSpeed:250,fluidSpeed:!1,dragEndSpeed:!1,responsive:{},responsiveRefreshRate:200,responsiveBaseElement:e,responsiveClass:!1,fallbackEasing:"swing",info:!1,nestedItemSelector:!1,itemElement:"div",stageElement:"div",themeClass:"owl-theme",baseClass:"owl-carousel",itemClass:"owl-item",centerClass:"center",activeClass:"active"},n.Width={Default:"default",Inner:"inner",Outer:"outer"},n.Plugins={},n.Pipe=[{filter:["width","items","settings"],run:function(t){t.current=this._items&&this._items[this.relative(this._current)]}},{filter:["items","settings"],run:function(){var t=this._clones,e=this.$stage.children(".cloned");(e.length!==t.length||!this.settings.loop&&t.length>0)&&(this.$stage.children(".cloned").remove(),this._clones=[])}},{filter:["items","settings"],run:function(){var t,e,i=this._clones,s=this._items,n=this.settings.loop?i.length-Math.max(2*this.settings.items,4):0;for(t=0,e=Math.abs(n/2);e>t;t++)n>0?(this.$stage.children().eq(s.length+i.length-1).remove(),i.pop(),this.$stage.children().eq(0).remove(),i.pop()):(i.push(i.length/2),this.$stage.append(s[i[i.length-1]].clone().addClass("cloned")),i.push(s.length-1-(i.length-1)/2),this.$stage.prepend(s[i[i.length-1]].clone().addClass("cloned")))}},{filter:["width","items","settings"],run:function(){var t,e,i,s=this.settings.rtl?1:-1,n=(this.width()/this.settings.items).toFixed(3),o=0;for(this._coordinates=[],e=0,i=this._clones.length+this._items.length;i>e;e++)t=this._mergers[this.relative(e)],t=this.settings.mergeFit&&Math.min(t,this.settings.items)||t,o+=(this.settings.autoWidth?this._items[this.relative(e)].width()+this.settings.margin:n*t)*s,this._coordinates.push(o)}},{filter:["width","items","settings"],run:function(){var e,i,s=(this.width()/this.settings.items).toFixed(3),n={width:Math.abs(this._coordinates[this._coordinates.length-1])+2*this.settings.stagePadding,"padding-left":this.settings.stagePadding||"","padding-right":this.settings.stagePadding||""};if(this.$stage.css(n),n={width:this.settings.autoWidth?"auto":s-this.settings.margin},n[this.settings.rtl?"margin-left":"margin-right"]=this.settings.margin,!this.settings.autoWidth&&t.grep(this._mergers,function(t){return t>1}).length>0)for(e=0,i=this._coordinates.length;i>e;e++)n.width=Math.abs(this._coordinates[e])-Math.abs(this._coordinates[e-1]||0)-this.settings.margin,this.$stage.children().eq(e).css(n);else this.$stage.children().css(n)}},{filter:["width","items","settings"],run:function(t){t.current&&this.reset(this.$stage.children().index(t.current))}},{filter:["position"],run:function(){this.animate(this.coordinates(this._current))}},{filter:["width","position","items","settings"],run:function(){var t,e,i,s,n=this.settings.rtl?1:-1,o=2*this.settings.stagePadding,r=this.coordinates(this.current())+o,a=r+this.width()*n,h=[];for(i=0,s=this._coordinates.length;s>i;i++)t=this._coordinates[i-1]||0,e=Math.abs(this._coordinates[i])+o*n,(this.op(t,"<=",r)&&this.op(t,">",a)||this.op(e,"<",r)&&this.op(e,">",a))&&h.push(i);this.$stage.children("."+this.settings.activeClass).removeClass(this.settings.activeClass),this.$stage.children(":eq("+h.join("), :eq(")+")").addClass(this.settings.activeClass),this.settings.center&&(this.$stage.children("."+this.settings.centerClass).removeClass(this.settings.centerClass),this.$stage.children().eq(this.current()).addClass(this.settings.centerClass))}}],n.prototype.initialize=function(){if(this.trigger("initialize"),this.$element.addClass(this.settings.baseClass).addClass(this.settings.themeClass).toggleClass("owl-rtl",this.settings.rtl),this.browserSupport(),this.settings.autoWidth&&this.state.imagesLoaded!==!0){var e,i,n;if(e=this.$element.find("img"),i=this.settings.nestedItemSelector?"."+this.settings.nestedItemSelector:s,n=this.$element.children(i).width(),e.length&&0>=n)return this.preloadAutoWidthImages(e),!1}this.$element.addClass("owl-loading"),this.$stage=t("<"+this.settings.stageElement+' class="owl-stage"/>').wrap('<div class="owl-stage-outer">'),this.$element.append(this.$stage.parent()),this.replace(this.$element.children().not(this.$stage.parent())),this._width=this.$element.width(),this.refresh(),this.$element.removeClass("owl-loading").addClass("owl-loaded"),this.eventsCall(),this.internalEvents(),this.addTriggerableEvents(),this.trigger("initialized")},n.prototype.setup=function(){var e=this.viewport(),i=this.options.responsive,s=-1,n=null;i?(t.each(i,function(t){e>=t&&t>s&&(s=Number(t))}),n=t.extend({},this.options,i[s]),delete n.responsive,n.responsiveClass&&this.$element.attr("class",function(t,e){return e.replace(/\b owl-responsive-\S+/g,"")}).addClass("owl-responsive-"+s)):n=t.extend({},this.options),(null===this.settings||this._breakpoint!==s)&&(this.trigger("change",{property:{name:"settings",value:n}}),this._breakpoint=s,this.settings=n,this.invalidate("settings"),this.trigger("changed",{property:{name:"settings",value:this.settings}}))},n.prototype.optionsLogic=function(){this.$element.toggleClass("owl-center",this.settings.center),this.settings.loop&&this._items.length<this.settings.items&&(this.settings.loop=!1),this.settings.autoWidth&&(this.settings.stagePadding=!1,this.settings.merge=!1)},n.prototype.prepare=function(e){var i=this.trigger("prepare",{content:e});return i.data||(i.data=t("<"+this.settings.itemElement+"/>").addClass(this.settings.itemClass).append(e)),this.trigger("prepared",{content:i.data}),i.data},n.prototype.update=function(){for(var e=0,i=this._pipe.length,s=t.proxy(function(t){return this[t]},this._invalidated),n={};i>e;)(this._invalidated.all||t.grep(this._pipe[e].filter,s).length>0)&&this._pipe[e].run(n),e++;this._invalidated={}},n.prototype.width=function(t){switch(t=t||n.Width.Default){case n.Width.Inner:case n.Width.Outer:return this._width;default:return this._width-2*this.settings.stagePadding+this.settings.margin}},n.prototype.refresh=function(){if(0===this._items.length)return!1;(new Date).getTime();this.trigger("refresh"),this.setup(),this.optionsLogic(),this.$stage.addClass("owl-refresh"),this.update(),this.$stage.removeClass("owl-refresh"),this.state.orientation=e.orientation,this.watchVisibility(),this.trigger("refreshed")},n.prototype.eventsCall=function(){this.e._onDragStart=t.proxy(function(t){this.onDragStart(t)},this),this.e._onDragMove=t.proxy(function(t){this.onDragMove(t)},this),this.e._onDragEnd=t.proxy(function(t){this.onDragEnd(t)},this),this.e._onResize=t.proxy(function(t){this.onResize(t)},this),this.e._transitionEnd=t.proxy(function(t){this.transitionEnd(t)},this),this.e._preventClick=t.proxy(function(t){this.preventClick(t)},this)},n.prototype.onThrottledResize=function(){e.clearTimeout(this.resizeTimer),this.resizeTimer=e.setTimeout(this.e._onResize,this.settings.responsiveRefreshRate)},n.prototype.onResize=function(){return this._items.length?this._width===this.$element.width()?!1:this.trigger("resize").isDefaultPrevented()?!1:(this._width=this.$element.width(),this.invalidate("width"),this.refresh(),void this.trigger("resized")):!1},n.prototype.eventsRouter=function(t){var e=t.type;"mousedown"===e||"touchstart"===e?this.onDragStart(t):"mousemove"===e||"touchmove"===e?this.onDragMove(t):"mouseup"===e||"touchend"===e?this.onDragEnd(t):"touchcancel"===e&&this.onDragEnd(t)},n.prototype.internalEvents=function(){var i=(c(),d());this.settings.mouseDrag?(this.$stage.on("mousedown",t.proxy(function(t){this.eventsRouter(t)},this)),this.$stage.on("dragstart",function(){return!1}),this.$stage.get(0).onselectstart=function(){return!1}):this.$element.addClass("owl-text-select-on"),this.settings.touchDrag&&!i&&this.$stage.on("touchstart touchcancel",t.proxy(function(t){this.eventsRouter(t)},this)),this.transitionEndVendor&&this.on(this.$stage.get(0),this.transitionEndVendor,this.e._transitionEnd,!1),this.settings.responsive!==!1&&this.on(e,"resize",t.proxy(this.onThrottledResize,this))},n.prototype.onDragStart=function(s){var n,r,a,h;if(n=s.originalEvent||s||e.event,3===n.which||this.state.isTouch)return!1;if("mousedown"===n.type&&this.$stage.addClass("owl-grab"),this.trigger("drag"),this.drag.startTime=(new Date).getTime(),this.speed(0),this.state.isTouch=!0,this.state.isScrolling=!1,this.state.isSwiping=!1,this.drag.distance=0,r=o(n).x,a=o(n).y,this.drag.offsetX=this.$stage.position().left,this.drag.offsetY=this.$stage.position().top,this.settings.rtl&&(this.drag.offsetX=this.$stage.position().left+this.$stage.width()-this.width()+this.settings.margin),this.state.inMotion&&this.support3d)h=this.getTransformProperty(),this.drag.offsetX=h,this.animate(h),this.state.inMotion=!0;else if(this.state.inMotion&&!this.support3d)return this.state.inMotion=!1,!1;this.drag.startX=r-this.drag.offsetX,this.drag.startY=a-this.drag.offsetY,this.drag.start=r-this.drag.startX,this.drag.targetEl=n.target||n.srcElement,this.drag.updatedX=this.drag.start,("IMG"===this.drag.targetEl.tagName||"A"===this.drag.targetEl.tagName)&&(this.drag.targetEl.draggable=!1),t(i).on("mousemove.owl.dragEvents mouseup.owl.dragEvents touchmove.owl.dragEvents touchend.owl.dragEvents",t.proxy(function(t){this.eventsRouter(t)},this))},n.prototype.onDragMove=function(t){var i,n,r,a,h,l;this.state.isTouch&&(this.state.isScrolling||(i=t.originalEvent||t||e.event,n=o(i).x,r=o(i).y,this.drag.currentX=n-this.drag.startX,this.drag.currentY=r-this.drag.startY,this.drag.distance=this.drag.currentX-this.drag.offsetX,this.drag.distance<0?this.state.direction=this.settings.rtl?"right":"left":this.drag.distance>0&&(this.state.direction=this.settings.rtl?"left":"right"),this.settings.loop?this.op(this.drag.currentX,">",this.coordinates(this.minimum()))&&"right"===this.state.direction?this.drag.currentX-=(this.settings.center&&this.coordinates(0))-this.coordinates(this._items.length):this.op(this.drag.currentX,"<",this.coordinates(this.maximum()))&&"left"===this.state.direction&&(this.drag.currentX+=(this.settings.center&&this.coordinates(0))-this.coordinates(this._items.length)):(a=this.coordinates(this.settings.rtl?this.maximum():this.minimum()),h=this.coordinates(this.settings.rtl?this.minimum():this.maximum()),l=this.settings.pullDrag?this.drag.distance/5:0,this.drag.currentX=Math.max(Math.min(this.drag.currentX,a+l),h+l)),(this.drag.distance>8||this.drag.distance<-8)&&(i.preventDefault!==s?i.preventDefault():i.returnValue=!1,this.state.isSwiping=!0),this.drag.updatedX=this.drag.currentX,(this.drag.currentY>16||this.drag.currentY<-16)&&this.state.isSwiping===!1&&(this.state.isScrolling=!0,this.drag.updatedX=this.drag.start),this.animate(this.drag.updatedX)))},n.prototype.onDragEnd=function(e){var s,n,o;if(this.state.isTouch){if("mouseup"===e.type&&this.$stage.removeClass("owl-grab"),this.trigger("dragged"),this.drag.targetEl.removeAttribute("draggable"),this.state.isTouch=!1,this.state.isScrolling=!1,this.state.isSwiping=!1,0===this.drag.distance&&this.state.inMotion!==!0)return this.state.inMotion=!1,!1;this.drag.endTime=(new Date).getTime(),s=this.drag.endTime-this.drag.startTime,n=Math.abs(this.drag.distance),(n>3||s>300)&&this.removeClick(this.drag.targetEl),o=this.closest(this.drag.updatedX),this.speed(this.settings.dragEndSpeed||this.settings.smartSpeed),this.current(o),this.invalidate("position"),this.update(),this.settings.pullDrag||this.drag.updatedX!==this.coordinates(o)||this.transitionEnd(),this.drag.distance=0,t(i).off(".owl.dragEvents")}},n.prototype.removeClick=function(i){this.drag.targetEl=i,t(i).on("click.preventClick",this.e._preventClick),e.setTimeout(function(){t(i).off("click.preventClick")},300)},n.prototype.preventClick=function(e){e.preventDefault?e.preventDefault():e.returnValue=!1,e.stopPropagation&&e.stopPropagation(),t(e.target).off("click.preventClick")},n.prototype.getTransformProperty=function(){var t,i;return t=e.getComputedStyle(this.$stage.get(0),null).getPropertyValue(this.vendorName+"transform"),t=t.replace(/matrix(3d)?\(|\)/g,"").split(","),i=16===t.length,i!==!0?t[4]:t[12]},n.prototype.closest=function(e){var i=-1,s=30,n=this.width(),o=this.coordinates();return this.settings.freeDrag||t.each(o,t.proxy(function(t,r){return e>r-s&&r+s>e?i=t:this.op(e,"<",r)&&this.op(e,">",o[t+1]||r-n)&&(i="left"===this.state.direction?t+1:t),-1===i},this)),this.settings.loop||(this.op(e,">",o[this.minimum()])?i=e=this.minimum():this.op(e,"<",o[this.maximum()])&&(i=e=this.maximum())),i},n.prototype.animate=function(e){this.trigger("translate"),this.state.inMotion=this.speed()>0,this.support3d?this.$stage.css({transform:"translate3d("+e+"px,0px, 0px)",transition:this.speed()/1e3+"s"}):this.state.isTouch?this.$stage.css({left:e+"px"}):this.$stage.animate({left:e},this.speed()/1e3,this.settings.fallbackEasing,t.proxy(function(){this.state.inMotion&&this.transitionEnd()},this))},n.prototype.current=function(t){if(t===s)return this._current;if(0===this._items.length)return s;if(t=this.normalize(t),this._current!==t){var e=this.trigger("change",{property:{name:"position",value:t}});e.data!==s&&(t=this.normalize(e.data)),this._current=t,this.invalidate("position"),this.trigger("changed",{property:{name:"position",value:this._current}})}return this._current},n.prototype.invalidate=function(t){this._invalidated[t]=!0},n.prototype.reset=function(t){t=this.normalize(t),t!==s&&(this._speed=0,this._current=t,this.suppress(["translate","translated"]),this.animate(this.coordinates(t)),this.release(["translate","translated"]))},n.prototype.normalize=function(e,i){var n=i?this._items.length:this._items.length+this._clones.length;return!t.isNumeric(e)||1>n?s:e=this._clones.length?(e%n+n)%n:Math.max(this.minimum(i),Math.min(this.maximum(i),e))},n.prototype.relative=function(t){return t=this.normalize(t),t-=this._clones.length/2,this.normalize(t,!0)},n.prototype.maximum=function(t){var e,i,s,n=0,o=this.settings;if(t)return this._items.length-1;if(!o.loop&&o.center)e=this._items.length-1;else if(o.loop||o.center)if(o.loop||o.center)e=this._items.length+o.items;else{if(!o.autoWidth&&!o.merge)throw"Can not detect maximum absolute position.";for(revert=o.rtl?1:-1,i=this.$stage.width()-this.$element.width();(s=this.coordinates(n))&&!(s*revert>=i);)e=++n}else e=this._items.length-o.items;return e},n.prototype.minimum=function(t){return t?0:this._clones.length/2},n.prototype.items=function(t){return t===s?this._items.slice():(t=this.normalize(t,!0),this._items[t])},n.prototype.mergers=function(t){return t===s?this._mergers.slice():(t=this.normalize(t,!0),this._mergers[t])},n.prototype.clones=function(e){var i=this._clones.length/2,n=i+this._items.length,o=function(t){return t%2===0?n+t/2:i-(t+1)/2};return e===s?t.map(this._clones,function(t,e){return o(e)}):t.map(this._clones,function(t,i){return t===e?o(i):null})},n.prototype.speed=function(t){return t!==s&&(this._speed=t),this._speed},n.prototype.coordinates=function(e){var i=null;return e===s?t.map(this._coordinates,t.proxy(function(t,e){return this.coordinates(e)},this)):(this.settings.center?(i=this._coordinates[e],i+=(this.width()-i+(this._coordinates[e-1]||0))/2*(this.settings.rtl?-1:1)):i=this._coordinates[e-1]||0,i)},n.prototype.duration=function(t,e,i){return Math.min(Math.max(Math.abs(e-t),1),6)*Math.abs(i||this.settings.smartSpeed)},n.prototype.to=function(i,s){if(this.settings.loop){var n=i-this.relative(this.current()),o=this.current(),r=this.current(),a=this.current()+n,h=0>r-a?!0:!1,l=this._clones.length+this._items.length;a<this.settings.items&&h===!1?(o=r+this._items.length,this.reset(o)):a>=l-this.settings.items&&h===!0&&(o=r-this._items.length,this.reset(o)),e.clearTimeout(this.e._goToLoop),this.e._goToLoop=e.setTimeout(t.proxy(function(){this.speed(this.duration(this.current(),o+n,s)),this.current(o+n),this.update()},this),30)}else this.speed(this.duration(this.current(),i,s)),this.current(i),this.update()},n.prototype.next=function(t){t=t||!1,this.to(this.relative(this.current())+1,t)},n.prototype.prev=function(t){t=t||!1,this.to(this.relative(this.current())-1,t)},n.prototype.transitionEnd=function(t){return t!==s&&(t.stopPropagation(),(t.target||t.srcElement||t.originalTarget)!==this.$stage.get(0))?!1:(this.state.inMotion=!1,void this.trigger("translated"))},n.prototype.viewport=function(){var s;if(this.options.responsiveBaseElement!==e)s=t(this.options.responsiveBaseElement).width();else if(e.innerWidth)s=e.innerWidth;else{if(!i.documentElement||!i.documentElement.clientWidth)throw"Can not detect viewport width.";s=i.documentElement.clientWidth}return s},n.prototype.replace=function(e){this.$stage.empty(),this._items=[],e&&(e=e instanceof jQuery?e:t(e)),this.settings.nestedItemSelector&&(e=e.find("."+this.settings.nestedItemSelector)),e.filter(function(){return 1===this.nodeType}).each(t.proxy(function(t,e){e=this.prepare(e),this.$stage.append(e),this._items.push(e),this._mergers.push(1*e.find("[data-merge]").andSelf("[data-merge]").attr("data-merge")||1)},this)),this.reset(t.isNumeric(this.settings.startPosition)?this.settings.startPosition:0),this.invalidate("items")},n.prototype.add=function(t,e){e=e===s?this._items.length:this.normalize(e,!0),this.trigger("add",{content:t,position:e}),0===this._items.length||e===this._items.length?(this.$stage.append(t),this._items.push(t),this._mergers.push(1*t.find("[data-merge]").andSelf("[data-merge]").attr("data-merge")||1)):(this._items[e].before(t),this._items.splice(e,0,t),this._mergers.splice(e,0,1*t.find("[data-merge]").andSelf("[data-merge]").attr("data-merge")||1)),this.invalidate("items"),this.trigger("added",{content:t,position:e})},n.prototype.remove=function(t){t=this.normalize(t,!0),t!==s&&(this.trigger("remove",{content:this._items[t],position:t}),this._items[t].remove(),this._items.splice(t,1),this._mergers.splice(t,1),this.invalidate("items"),this.trigger("removed",{content:null,position:t}))},n.prototype.addTriggerableEvents=function(){var e=t.proxy(function(e,i){return t.proxy(function(t){t.relatedTarget!==this&&(this.suppress([i]),e.apply(this,[].slice.call(arguments,1)),this.release([i]))},this)},this);t.each({next:this.next,prev:this.prev,to:this.to,destroy:this.destroy,refresh:this.refresh,replace:this.replace,add:this.add,remove:this.remove},t.proxy(function(t,i){this.$element.on(t+".owl.carousel",e(i,t+".owl.carousel"))},this))},n.prototype.watchVisibility=function(){function i(t){return t.offsetWidth>0&&t.offsetHeight>0}function s(){i(this.$element.get(0))&&(this.$element.removeClass("owl-hidden"),this.refresh(),e.clearInterval(this.e._checkVisibile))}i(this.$element.get(0))||(this.$element.addClass("owl-hidden"),e.clearInterval(this.e._checkVisibile),this.e._checkVisibile=e.setInterval(t.proxy(s,this),500))},n.prototype.preloadAutoWidthImages=function(e){var i,s,n,o;i=0,s=this,e.each(function(r,a){n=t(a),o=new Image,o.onload=function(){i++,n.attr("src",o.src),n.css("opacity",1),i>=e.length&&(s.state.imagesLoaded=!0,s.initialize())},o.src=n.attr("src")||n.attr("data-src")||n.attr("data-src-retina")})},n.prototype.destroy=function(){this.$element.hasClass(this.settings.themeClass)&&this.$element.removeClass(this.settings.themeClass),this.settings.responsive!==!1&&t(e).off("resize.owl.carousel"),this.transitionEndVendor&&this.off(this.$stage.get(0),this.transitionEndVendor,this.e._transitionEnd);for(var s in this._plugins)this._plugins[s].destroy();(this.settings.mouseDrag||this.settings.touchDrag)&&(this.$stage.off("mousedown touchstart touchcancel"),t(i).off(".owl.dragEvents"),this.$stage.get(0).onselectstart=function(){},this.$stage.off("dragstart",function(){return!1})),this.$element.off(".owl"),this.$stage.children(".cloned").remove(),this.e=null,this.$element.removeData("owlCarousel"),this.$stage.children().contents().unwrap(),this.$stage.children().unwrap(),this.$stage.unwrap()},n.prototype.op=function(t,e,i){var s=this.settings.rtl;switch(e){case"<":return s?t>i:i>t;case">":return s?i>t:t>i;case">=":return s?i>=t:t>=i;case"<=":return s?t>=i:i>=t}},n.prototype.on=function(t,e,i,s){t.addEventListener?t.addEventListener(e,i,s):t.attachEvent&&t.attachEvent("on"+e,i)},n.prototype.off=function(t,e,i,s){t.removeEventListener?t.removeEventListener(e,i,s):t.detachEvent&&t.detachEvent("on"+e,i)},n.prototype.trigger=function(e,i,s){var n={item:{count:this._items.length,index:this.current()}},o=t.camelCase(t.grep(["on",e,s],function(t){return t}).join("-").toLowerCase()),r=t.Event([e,"owl",s||"carousel"].join(".").toLowerCase(),t.extend({relatedTarget:this},n,i));return this._supress[e]||(t.each(this._plugins,function(t,e){e.onTrigger&&e.onTrigger(r)}),this.$element.trigger(r),this.settings&&"function"==typeof this.settings[o]&&this.settings[o].apply(this,r)),r},n.prototype.suppress=function(e){t.each(e,t.proxy(function(t,e){this._supress[e]=!0},this))},n.prototype.release=function(e){t.each(e,t.proxy(function(t,e){delete this._supress[e]},this))},n.prototype.browserSupport=function(){if(this.support3d=l(),this.support3d){this.transformVendor=h();var t=["transitionend","webkitTransitionEnd","transitionend","oTransitionEnd"];this.transitionEndVendor=t[a()],this.vendorName=this.transformVendor.replace(/Transform/i,""),this.vendorName=""!==this.vendorName?"-"+this.vendorName.toLowerCase()+"-":""}this.state.orientation=e.orientation},t.fn.owlCarousel=function(e){return this.each(function(){t(this).data("owlCarousel")||t(this).data("owlCarousel",new n(this,e))})},t.fn.owlCarousel.Constructor=n}(window.Zepto||window.jQuery,window,document),function(t,e){var i=function(e){this._core=e,this._loaded=[],this._handlers={"initialized.owl.carousel change.owl.carousel":t.proxy(function(e){if(e.namespace&&this._core.settings&&this._core.settings.lazyLoad&&(e.property&&"position"==e.property.name||"initialized"==e.type))for(var i=this._core.settings,s=i.center&&Math.ceil(i.items/2)||i.items,n=i.center&&-1*s||0,o=(e.property&&e.property.value||this._core.current())+n,r=this._core.clones().length,a=t.proxy(function(t,e){this.load(e)},this);n++<s;)this.load(r/2+this._core.relative(o)),r&&t.each(this._core.clones(this._core.relative(o++)),a)},this)},this._core.options=t.extend({},i.Defaults,this._core.options),this._core.$element.on(this._handlers)};i.Defaults={lazyLoad:!1},i.prototype.load=function(i){var s=this._core.$stage.children().eq(i),n=s&&s.find(".owl-lazy");!n||t.inArray(s.get(0),this._loaded)>-1||(n.each(t.proxy(function(i,s){var n,o=t(s),r=e.devicePixelRatio>1&&o.attr("data-src-retina")||o.attr("data-src");this._core.trigger("load",{element:o,url:r},"lazy"),o.is("img")?o.one("load.owl.lazy",t.proxy(function(){o.css("opacity",1),this._core.trigger("loaded",{element:o,url:r},"lazy")},this)).attr("src",r):(n=new Image,n.onload=t.proxy(function(){o.css({"background-image":"url("+r+")",opacity:"1"}),this._core.trigger("loaded",{element:o,url:r},"lazy")},this),n.src=r)},this)),this._loaded.push(s.get(0)))},i.prototype.destroy=function(){var t,e;for(t in this.handlers)this._core.$element.off(t,this.handlers[t]);for(e in Object.getOwnPropertyNames(this))"function"!=typeof this[e]&&(this[e]=null)},t.fn.owlCarousel.Constructor.Plugins.Lazy=i}(window.Zepto||window.jQuery,window,document),function(t){var e=function(i){this._core=i,this._handlers={"initialized.owl.carousel":t.proxy(function(){this._core.settings.autoHeight&&this.update()},this),"changed.owl.carousel":t.proxy(function(t){this._core.settings.autoHeight&&"position"==t.property.name&&this.update()},this),"loaded.owl.lazy":t.proxy(function(t){this._core.settings.autoHeight&&t.element.closest("."+this._core.settings.itemClass)===this._core.$stage.children().eq(this._core.current())&&this.update()},this)},this._core.options=t.extend({},e.Defaults,this._core.options),this._core.$element.on(this._handlers)};e.Defaults={autoHeight:!1,autoHeightClass:"owl-height"},e.prototype.update=function(){this._core.$stage.parent().height(this._core.$stage.children().eq(this._core.current()).height()).addClass(this._core.settings.autoHeightClass)},e.prototype.destroy=function(){var t,e;for(t in this._handlers)this._core.$element.off(t,this._handlers[t]);for(e in Object.getOwnPropertyNames(this))"function"!=typeof this[e]&&(this[e]=null)},t.fn.owlCarousel.Constructor.Plugins.AutoHeight=e}(window.Zepto||window.jQuery,window,document),function(t,e,i){var s=function(e){this._core=e,this._videos={},this._playing=null,this._fullscreen=!1,this._handlers={"resize.owl.carousel":t.proxy(function(t){this._core.settings.video&&!this.isInFullScreen()&&t.preventDefault()},this),"refresh.owl.carousel changed.owl.carousel":t.proxy(function(){this._playing&&this.stop()},this),"prepared.owl.carousel":t.proxy(function(e){var i=t(e.content).find(".owl-video");i.length&&(i.css("display","none"),this.fetch(i,t(e.content)))},this)},this._core.options=t.extend({},s.Defaults,this._core.options),this._core.$element.on(this._handlers),this._core.$element.on("click.owl.video",".owl-video-play-icon",t.proxy(function(t){this.play(t)},this))};s.Defaults={video:!1,videoHeight:!1,videoWidth:!1},s.prototype.fetch=function(t,e){var i=t.attr("data-vimeo-id")?"vimeo":"youtube",s=t.attr("data-vimeo-id")||t.attr("data-youtube-id"),n=t.attr("data-width")||this._core.settings.videoWidth,o=t.attr("data-height")||this._core.settings.videoHeight,r=t.attr("href");if(!r)throw new Error("Missing video URL.");if(s=r.match(/(http:|https:|)\/\/(player.|www.)?(vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com))\/(video\/|embed\/|watch\?v=|v\/)?([A-Za-z0-9._%-]*)(\&\S+)?/),s[3].indexOf("youtu")>-1)i="youtube";else{if(!(s[3].indexOf("vimeo")>-1))throw new Error("Video URL not supported.");i="vimeo"}s=s[6],this._videos[r]={type:i,id:s,width:n,height:o},e.attr("data-video",r),this.thumbnail(t,this._videos[r])},s.prototype.thumbnail=function(e,i){var s,n,o,r=i.width&&i.height?'style="width:'+i.width+"px;height:"+i.height+'px;"':"",a=e.find("img"),h="src",l="",c=this._core.settings,d=function(t){n='<div class="owl-video-play-icon"></div>',s=c.lazyLoad?'<div class="owl-video-tn '+l+'" '+h+'="'+t+'"></div>':'<div class="owl-video-tn" style="opacity:1;background-image:url('+t+')"></div>',e.after(s),e.after(n)};return e.wrap('<div class="owl-video-wrapper"'+r+"></div>"),this._core.settings.lazyLoad&&(h="data-src",l="owl-lazy"),a.length?(d(a.attr(h)),a.remove(),!1):void("youtube"===i.type?(o="http://img.youtube.com/vi/"+i.id+"/hqdefault.jpg",d(o)):"vimeo"===i.type&&t.ajax({type:"GET",url:"http://vimeo.com/api/v2/video/"+i.id+".json",jsonp:"callback",dataType:"jsonp",success:function(t){o=t[0].thumbnail_large,d(o)}}))},s.prototype.stop=function(){this._core.trigger("stop",null,"video"),this._playing.find(".owl-video-frame").remove(),this._playing.removeClass("owl-video-playing"),this._playing=null},s.prototype.play=function(e){this._core.trigger("play",null,"video"),this._playing&&this.stop();var i,s,n=t(e.target||e.srcElement),o=n.closest("."+this._core.settings.itemClass),r=this._videos[o.attr("data-video")],a=r.width||"100%",h=r.height||this._core.$stage.height();"youtube"===r.type?i='<iframe width="'+a+'" height="'+h+'" src="http://www.youtube.com/embed/'+r.id+"?autoplay=1&v="+r.id+'" frameborder="0" allowfullscreen></iframe>':"vimeo"===r.type&&(i='<iframe src="http://player.vimeo.com/video/'+r.id+'?autoplay=1" width="'+a+'" height="'+h+'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>'),o.addClass("owl-video-playing"),this._playing=o,s=t('<div style="height:'+h+"px; width:"+a+'px" class="owl-video-frame">'+i+"</div>"),n.after(s)},s.prototype.isInFullScreen=function(){var s=i.fullscreenElement||i.mozFullScreenElement||i.webkitFullscreenElement;return s&&t(s).parent().hasClass("owl-video-frame")&&(this._core.speed(0),this._fullscreen=!0),s&&this._fullscreen&&this._playing?!1:this._fullscreen?(this._fullscreen=!1,!1):this._playing&&this._core.state.orientation!==e.orientation?(this._core.state.orientation=e.orientation,!1):!0},s.prototype.destroy=function(){var t,e;this._core.$element.off("click.owl.video");for(t in this._handlers)this._core.$element.off(t,this._handlers[t]);for(e in Object.getOwnPropertyNames(this))"function"!=typeof this[e]&&(this[e]=null)},t.fn.owlCarousel.Constructor.Plugins.Video=s}(window.Zepto||window.jQuery,window,document),function(t,e,i,s){var n=function(e){this.core=e,this.core.options=t.extend({},n.Defaults,this.core.options),this.swapping=!0,this.previous=s,this.next=s,this.handlers={"change.owl.carousel":t.proxy(function(t){"position"==t.property.name&&(this.previous=this.core.current(),this.next=t.property.value)},this),"drag.owl.carousel dragged.owl.carousel translated.owl.carousel":t.proxy(function(t){this.swapping="translated"==t.type},this),"translate.owl.carousel":t.proxy(function(){this.swapping&&(this.core.options.animateOut||this.core.options.animateIn)&&this.swap()},this)},this.core.$element.on(this.handlers)};n.Defaults={animateOut:!1,animateIn:!1},n.prototype.swap=function(){if(1===this.core.settings.items&&this.core.support3d){this.core.speed(0);var e,i=t.proxy(this.clear,this),s=this.core.$stage.children().eq(this.previous),n=this.core.$stage.children().eq(this.next),o=this.core.settings.animateIn,r=this.core.settings.animateOut;this.core.current()!==this.previous&&(r&&(e=this.core.coordinates(this.previous)-this.core.coordinates(this.next),s.css({left:e+"px"}).addClass("animated owl-animated-out").addClass(r).one("webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend",i)),o&&n.addClass("animated owl-animated-in").addClass(o).one("webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend",i))}},n.prototype.clear=function(e){t(e.target).css({left:""}).removeClass("animated owl-animated-out owl-animated-in").removeClass(this.core.settings.animateIn).removeClass(this.core.settings.animateOut),this.core.transitionEnd()},n.prototype.destroy=function(){var t,e;for(t in this.handlers)this.core.$element.off(t,this.handlers[t]);for(e in Object.getOwnPropertyNames(this))"function"!=typeof this[e]&&(this[e]=null)},t.fn.owlCarousel.Constructor.Plugins.Animate=n}(window.Zepto||window.jQuery,window,document),function(t,e,i){var s=function(e){this.core=e,this.core.options=t.extend({},s.Defaults,this.core.options),this.handlers={"translated.owl.carousel refreshed.owl.carousel":t.proxy(function(){this.autoplay()
},this),"play.owl.autoplay":t.proxy(function(t,e,i){this.play(e,i)},this),"stop.owl.autoplay":t.proxy(function(){this.stop()},this),"mouseover.owl.autoplay":t.proxy(function(){this.core.settings.autoplayHoverPause&&this.pause()},this),"mouseleave.owl.autoplay":t.proxy(function(){this.core.settings.autoplayHoverPause&&this.autoplay()},this)},this.core.$element.on(this.handlers)};s.Defaults={autoplay:!1,autoplayTimeout:5e3,autoplayHoverPause:!1,autoplaySpeed:!1},s.prototype.autoplay=function(){this.core.settings.autoplay&&!this.core.state.videoPlay?(e.clearInterval(this.interval),this.interval=e.setInterval(t.proxy(function(){this.play()},this),this.core.settings.autoplayTimeout)):e.clearInterval(this.interval)},s.prototype.play=function(){return i.hidden===!0||this.core.state.isTouch||this.core.state.isScrolling||this.core.state.isSwiping||this.core.state.inMotion?void 0:this.core.settings.autoplay===!1?void e.clearInterval(this.interval):void this.core.next(this.core.settings.autoplaySpeed)},s.prototype.stop=function(){e.clearInterval(this.interval)},s.prototype.pause=function(){e.clearInterval(this.interval)},s.prototype.destroy=function(){var t,i;e.clearInterval(this.interval);for(t in this.handlers)this.core.$element.off(t,this.handlers[t]);for(i in Object.getOwnPropertyNames(this))"function"!=typeof this[i]&&(this[i]=null)},t.fn.owlCarousel.Constructor.Plugins.autoplay=s}(window.Zepto||window.jQuery,window,document),function(t){"use strict";var e=function(i){this._core=i,this._initialized=!1,this._pages=[],this._controls={},this._templates=[],this.$element=this._core.$element,this._overrides={next:this._core.next,prev:this._core.prev,to:this._core.to},this._handlers={"prepared.owl.carousel":t.proxy(function(e){this._core.settings.dotsData&&this._templates.push(t(e.content).find("[data-dot]").andSelf("[data-dot]").attr("data-dot"))},this),"add.owl.carousel":t.proxy(function(e){this._core.settings.dotsData&&this._templates.splice(e.position,0,t(e.content).find("[data-dot]").andSelf("[data-dot]").attr("data-dot"))},this),"remove.owl.carousel prepared.owl.carousel":t.proxy(function(t){this._core.settings.dotsData&&this._templates.splice(t.position,1)},this),"change.owl.carousel":t.proxy(function(t){if("position"==t.property.name&&!this._core.state.revert&&!this._core.settings.loop&&this._core.settings.navRewind){var e=this._core.current(),i=this._core.maximum(),s=this._core.minimum();t.data=t.property.value>i?e>=i?s:i:t.property.value<s?i:t.property.value}},this),"changed.owl.carousel":t.proxy(function(t){"position"==t.property.name&&this.draw()},this),"refreshed.owl.carousel":t.proxy(function(){this._initialized||(this.initialize(),this._initialized=!0),this._core.trigger("refresh",null,"navigation"),this.update(),this.draw(),this._core.trigger("refreshed",null,"navigation")},this)},this._core.options=t.extend({},e.Defaults,this._core.options),this.$element.on(this._handlers)};e.Defaults={nav:!1,navRewind:!0,navText:["prev","next"],navSpeed:!1,navElement:"div",navContainer:!1,navContainerClass:"owl-nav",navClass:["owl-prev","owl-next"],slideBy:1,dotClass:"owl-dot",dotsClass:"owl-dots",dots:!0,dotsEach:!1,dotData:!1,dotsSpeed:!1,dotsContainer:!1,controlsClass:"owl-controls"},e.prototype.initialize=function(){var e,i,s=this._core.settings;s.dotsData||(this._templates=[t("<div>").addClass(s.dotClass).append(t("<span>")).prop("outerHTML")]),s.navContainer&&s.dotsContainer||(this._controls.$container=t("<div>").addClass(s.controlsClass).appendTo(this.$element)),this._controls.$indicators=s.dotsContainer?t(s.dotsContainer):t("<div>").hide().addClass(s.dotsClass).appendTo(this._controls.$container),this._controls.$indicators.on("click","div",t.proxy(function(e){var i=t(e.target).parent().is(this._controls.$indicators)?t(e.target).index():t(e.target).parent().index();e.preventDefault(),this.to(i,s.dotsSpeed)},this)),e=s.navContainer?t(s.navContainer):t("<div>").addClass(s.navContainerClass).prependTo(this._controls.$container),this._controls.$next=t("<"+s.navElement+">"),this._controls.$previous=this._controls.$next.clone(),this._controls.$previous.addClass(s.navClass[0]).html(s.navText[0]).hide().prependTo(e).on("click",t.proxy(function(){this.prev(s.navSpeed)},this)),this._controls.$next.addClass(s.navClass[1]).html(s.navText[1]).hide().appendTo(e).on("click",t.proxy(function(){this.next(s.navSpeed)},this));for(i in this._overrides)this._core[i]=t.proxy(this[i],this)},e.prototype.destroy=function(){var t,e,i,s;for(t in this._handlers)this.$element.off(t,this._handlers[t]);for(e in this._controls)this._controls[e].remove();for(s in this.overides)this._core[s]=this._overrides[s];for(i in Object.getOwnPropertyNames(this))"function"!=typeof this[i]&&(this[i]=null)},e.prototype.update=function(){var t,e,i,s=this._core.settings,n=this._core.clones().length/2,o=n+this._core.items().length,r=s.center||s.autoWidth||s.dotData?1:s.dotsEach||s.items;if("page"!==s.slideBy&&(s.slideBy=Math.min(s.slideBy,s.items)),s.dots||"page"==s.slideBy)for(this._pages=[],t=n,e=0,i=0;o>t;t++)(e>=r||0===e)&&(this._pages.push({start:t-n,end:t-n+r-1}),e=0,++i),e+=this._core.mergers(this._core.relative(t))},e.prototype.draw=function(){var e,i,s="",n=this._core.settings,o=(this._core.$stage.children(),this._core.relative(this._core.current()));if(!n.nav||n.loop||n.navRewind||(this._controls.$previous.toggleClass("disabled",0>=o),this._controls.$next.toggleClass("disabled",o>=this._core.maximum())),this._controls.$previous.toggle(n.nav),this._controls.$next.toggle(n.nav),n.dots){if(e=this._pages.length-this._controls.$indicators.children().length,n.dotData&&0!==e){for(i=0;i<this._controls.$indicators.children().length;i++)s+=this._templates[this._core.relative(i)];this._controls.$indicators.html(s)}else e>0?(s=new Array(e+1).join(this._templates[0]),this._controls.$indicators.append(s)):0>e&&this._controls.$indicators.children().slice(e).remove();this._controls.$indicators.find(".active").removeClass("active"),this._controls.$indicators.children().eq(t.inArray(this.current(),this._pages)).addClass("active")}this._controls.$indicators.toggle(n.dots)},e.prototype.onTrigger=function(e){var i=this._core.settings;e.page={index:t.inArray(this.current(),this._pages),count:this._pages.length,size:i&&(i.center||i.autoWidth||i.dotData?1:i.dotsEach||i.items)}},e.prototype.current=function(){var e=this._core.relative(this._core.current());return t.grep(this._pages,function(t){return t.start<=e&&t.end>=e}).pop()},e.prototype.getPosition=function(e){var i,s,n=this._core.settings;return"page"==n.slideBy?(i=t.inArray(this.current(),this._pages),s=this._pages.length,e?++i:--i,i=this._pages[(i%s+s)%s].start):(i=this._core.relative(this._core.current()),s=this._core.items().length,e?i+=n.slideBy:i-=n.slideBy),i},e.prototype.next=function(e){t.proxy(this._overrides.to,this._core)(this.getPosition(!0),e)},e.prototype.prev=function(e){t.proxy(this._overrides.to,this._core)(this.getPosition(!1),e)},e.prototype.to=function(e,i,s){var n;s?t.proxy(this._overrides.to,this._core)(e,i):(n=this._pages.length,t.proxy(this._overrides.to,this._core)(this._pages[(e%n+n)%n].start,i))},t.fn.owlCarousel.Constructor.Plugins.Navigation=e}(window.Zepto||window.jQuery,window,document),function(t,e){"use strict";var i=function(s){this._core=s,this._hashes={},this.$element=this._core.$element,this._handlers={"initialized.owl.carousel":t.proxy(function(){"URLHash"==this._core.settings.startPosition&&t(e).trigger("hashchange.owl.navigation")},this),"prepared.owl.carousel":t.proxy(function(e){var i=t(e.content).find("[data-hash]").andSelf("[data-hash]").attr("data-hash");this._hashes[i]=e.content},this)},this._core.options=t.extend({},i.Defaults,this._core.options),this.$element.on(this._handlers),t(e).on("hashchange.owl.navigation",t.proxy(function(){var t=e.location.hash.substring(1),i=this._core.$stage.children(),s=this._hashes[t]&&i.index(this._hashes[t])||0;return t?void this._core.to(s,!1,!0):!1},this))};i.Defaults={URLhashListener:!1},i.prototype.destroy=function(){var i,s;t(e).off("hashchange.owl.navigation");for(i in this._handlers)this._core.$element.off(i,this._handlers[i]);for(s in Object.getOwnPropertyNames(this))"function"!=typeof this[s]&&(this[s]=null)},t.fn.owlCarousel.Constructor.Plugins.Hash=i}(window.Zepto||window.jQuery,window,document);