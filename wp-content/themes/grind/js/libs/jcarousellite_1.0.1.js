/* modified version of jCarouselLite 1.0.1 */
(function($) {
$.fn.jCarouselLite = function(o) {
    o = $.extend({
        btnPrev: null,
        btnNext: null,
        btnGo: null,
        mouseWheel: false,
        auto: null,
        hoverPause: false,
        speed: 200,
        easing: null,
        vertical: false,
        circular: true,
        visible: 1,
        start: 0,
        scroll: 1,
        beforeStart: null,
        afterEnd: null
    }, o || {});

    return this.each(function() {                           // Returns the element collection. Chainable.

        var running = false, animCss=o.vertical?"top":"left", sizeCss=o.vertical?"height":"width";
        var div = $(this), ul = $("ul", div), tLi = $("li", ul), tl = tLi.size(), v = o.visible;

        if(o.circular) {
            ul.prepend(tLi.slice(tl-v+1).clone()) // add last visible part, to the begin of the ul 
              .append(tLi.slice(0,o.scroll).clone());  // add first visble part, to the end of the ul
            o.start += v-1; //the script added an item at the front, move the start position with one part
        }

        var li = $("li", ul), itemLength = li.size(), curr = o.start;
        div.css("visibility", "visible");

        li.css({overflow: "hidden", float: o.vertical ? "none" : "left"});
        ul.css({margin: "0", padding: "0", position: "relative", "list-style-type": "none", "z-index": "1"});
        div.css({overflow: "hidden", position: "relative", "z-index": "2", left: "0px"});

        var liSize = o.vertical ? height(li) : width(li);   // Full li size(incl margin)-Used for animation
        var ulSize = liSize * itemLength;                   // size of full ul(total length, not just for the visible items)
        var divSize = liSize * v;                           // size of entire div(total length for just the visible items)

        li.css({width: li.width(), height: li.height()});
        ul.css(sizeCss, ulSize+"px").css(animCss, -(curr*liSize));

        div.css(sizeCss, divSize+"px");                     // Width of the DIV. length of visible images

        if(o.btnPrev) {
            $(o.btnPrev).click(function() {
                return go(curr-o.scroll);
            });
        }


        if(o.btnNext) {
            $(o.btnNext).click(function() {
                return go(curr+o.scroll);
            });
        }

        if(o.btnGo)
            $.each(o.btnGo, function(i, val) {
                $(val).click(function() {
                	return go(o.circular ? (o.visible+i-1) : i);
                });
            });

        if(o.mouseWheel && div.mousewheel)
            div.mousewheel(function(e, d) {
                return d>0 ? go(curr-o.scroll) : go(curr+o.scroll);
            });

        var autoInterval;

        function startAuto() {
          stopAuto();
          autoInterval = setInterval(function() {
                  go(curr+o.scroll);
              }, o.auto+o.speed);
        };

        function stopAuto() {
            clearInterval(autoInterval);
        };

        if(o.auto) {
            if(o.hoverPause) {
            	//scriptbreaker extention add the hover pause to the children instead of the element itself
                div.children().hover(function(){stopAuto();}, function(){startAuto();});
            }
            startAuto();
        };

        function vis() {
          li.removeClass("current");
          return li.slice(curr).slice(0,v).addClass("current");
        };

        function go(to) {
            if(!running) {
            	
                if(o.beforeStart)
                    o.beforeStart.call(this, vis());

                if(o.circular) {            // If circular we are in first or last, then goto the other end
                    if(to<0) {           // If before range, then go around
                        ul.css(animCss, -( (curr + tl) * liSize)+"px");
                        curr = to + tl;
                    } else if(to>itemLength-v) { // If beyond range, then come around
                        ul.css(animCss, -( (curr - tl) * liSize ) + "px" );
                        curr = to - tl;
                    } else curr = to;
                } else {                    // If non-circular and to points to first or last, we just return.
                    if(to<0 || to>itemLength-v) return;
                    else curr = to;
                }                           // If neither overrides it, the curr will still be "to" and we can proceed.

                running = true;

                ul.animate(
                    animCss == "left" ? { left: -(curr*liSize) } : { top: -(curr*liSize) } , o.speed, o.easing,
                    function() {
                        if(o.afterEnd)
                            o.afterEnd.call(this, vis(), curr, o.btnGo);
                        running = false;
                    }
                );

            }
            return false;
        };
    });
};


function css(el, prop) {
    return parseInt($.css(el[0], prop)) || 0;
};
function width(el) {
    return  el[0].offsetWidth + css(el, 'marginLeft') + css(el, 'marginRight');
};
function height(el) {
    return el[0].offsetHeight + css(el, 'marginTop') + css(el, 'marginBottom');
};

})(jQuery);