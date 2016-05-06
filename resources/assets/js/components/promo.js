var googletag = googletag || {};
googletag.cmd = googletag.cmd || [];
(function() {
    var gads = document.createElement('script');
    gads.async = true;
    gads.type = 'text/javascript';
    var useSSL = 'https:' == document.location.protocol;
    gads.src = (useSSL ? 'https:' : 'http:') + '//www.googletagservices.com/tag/js/gpt.js';
    var node = document.getElementsByTagName('script')[0];
    node.parentNode.insertBefore(gads, node);
})();

var promos = JSON.parse(decodeURIComponent(document.querySelector("meta[property=promos]").content))

googletag.cmd.push(function() {
    for (var promo in promos) {
        if(promos[promo].id1 && promos[promo].id2) {
            googletag.defineSlot(promos[promo].id1, [promos[promo].width, promos[promo].height], promos[promo].id2).addService(googletag.pubads());
        } 
    }
    googletag.pubads().enableSingleRequest();
    googletag.pubads().collapseEmptyDivs();
    googletag.enableServices();
    googletag.pubads().addEventListener('slotRenderEnded', function(e) {
        if ($('#'+e.slot.m.o).length) {
            return renderEnded(e.slot.m.o, e.size[0], e.size[1]);
        }
    });
});

window.onload = function(){
    for (var promo in promos) {
        if(promos[promo].id1 && promos[promo].id2) {
            if ($('#' + promos[promo].id2).length) {
                googletag.display(promos[promo].id2);
            }
        }
    }
}

function renderEnded (adunitId, width, height) {
    $('#' + adunitId).find('iframe').on('load', function(){
        var iFrame = this,
            newHeight = (($(iFrame).width() * height) / width);
        $(iFrame).contents().find('#google_image_div').css({'max-width': '100%', 'height': 'auto', 'display': 'block'});
        $(iFrame).contents().find('img').attr('onload', 'alert(\'test\');').css({'max-width': '100%', 'height': 'auto', 'display': 'block'});
        $(iFrame).css({'height': newHeight+'px'});
        $(iFrame).parents("#" + adunitId).show();
    });
}