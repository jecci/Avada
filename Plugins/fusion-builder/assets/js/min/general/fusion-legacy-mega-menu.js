var fusionNavMegamenuPosition=function(e){var n=jQuery(e),o=n.closest("nav"),u=o.hasClass("awb-menu_column")?"column":"row";o.hasClass("awb-menu_flyout")||n.find(".fusion-megamenu-wrapper")&&n.closest(".awb-menu").length&&(o.removeClass("mega-menu-loading"),o.hasClass("collapse-enabled")?n.find(".fusion-megamenu-wrapper").each(function(e,n){jQuery(n).css("left",0)}):(n.find(".fusion-megamenu-wrapper").each(function(e,n){var i,s,a,t,r,f,l,m,d=jQuery(n),w=d.closest("li.fusion-megamenu-menu"),c=d.find(".fusion-megamenu-holder"),h=d.closest(".fusion-row"),g=jQuery("body").hasClass("rtl");"row"===u?d.hasClass("fusion-megamenu-fullwidth")?(g&&d.css("right","auto"),window.avadaScrollBarWidth=window.avadaGetScrollBarWidth(),window.avadaScrollBarWidth&&d.css("width","calc("+c.width()+" - "+window.avadaGetScrollBarWidth()+"px)"),d.offset({left:(jQuery(window).width()-d.outerWidth())/2})):h.length&&(r=h.width(),m=(l=void 0!==(f=h.offset())?f.left:0)+r,i=w.offset(),t=d.outerWidth(),s=i.left+w.outerWidth(),a=0,!g&&i.left+t>m?(a=t===jQuery(window).width()?-1*i.left:t>r?l-i.left+(r-t)/2:-1*(i.left-(m-t)),d.css("left",a)):g&&s-t<l&&(a=t===jQuery(window).width()?s-t:t>r?s-m+(r-t)/2:-1*(t-(s-l)),d.css("right",a))):(d.css("top",0),d.css(o.hasClass("expand-left")?"right":"left","100%"))}),setTimeout(function(){o.removeClass("mega-menu-loaded")},50)))},fusionMegaMenuNavRunAll=function(){var e=jQuery(".awb-menu_em-hover.awb-menu_desktop:not( .awb-menu_flyout ) .fusion-megamenu-menu");e.each(function(){fusionNavSubmenuDirection(this)}),e.on("mouseenter focusin",function(){fusionNavMegamenuPosition(this)}),jQuery(window).trigger("fusion-position-menus")};jQuery(window).on("fusion-element-render-fusion_menu load",function(){fusionMegaMenuNavRunAll()}),jQuery(window).on("fusion-resize-horizontal fusion-position-menus",function(){jQuery(".awb-menu .fusion-megamenu-wrapper").each(function(e,n){fusionNavMegamenuPosition(jQuery(n).parent())})});