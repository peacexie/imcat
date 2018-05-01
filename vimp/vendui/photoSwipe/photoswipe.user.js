
var pswpTpl = '\
		<figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">\
		  <a href="{img}" itemprop="contentUrl" data-size="640x480">\
			  <img src="{img}" itemprop="thumbnail" alt="" />\
		  </a>\
		  <figcaption itemprop="caption description"></figcaption>\
		</figure>\
';

var pswpFmt = function(html) {
    html = html.replace(/<img.*?src="([^"]+)"[^>]*zw="(\d+)" zh="(\d+)".*?>/ig, function(m,p,zw,wh) {
        //console.log(m+':'+p,zw,wh);
        return pswpTpl.replace(/\{img\}/g,p).replace('640x480',zw+'x'+wh);
    });
    return html;
}

var pswpHtml = '\
    <div class="pswp__bg"></div>\
    <div class="pswp__scroll-wrap">\
        <div class="pswp__container">\
            <div class="pswp__item"></div><div class="pswp__item"></div><div class="pswp__item"></div>\
        </div>\
        <div class="pswp__ui pswp__ui--hidden">\
            <div class="pswp__top-bar">\
                <div class="pswp__counter"></div>\
                <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>\
                <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>\
                <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>\
                <div class="pswp__preloader"><div class="pswp__preloader__icn">\
                        <div class="pswp__preloader__cut"><div class="pswp__preloader__donut"></div></div>\
                </div></div>\
            </div>\
            <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">\
                <div class="pswp__share-tooltip"></div>\
            </div>\
            <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button>\
            <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button>\
            <div class="pswp__caption"><div class="pswp__caption__center"></div></div>\
        </div>\
    </div>\
';

var pswpInit = function(gallerySelector) {
    $('.pswp').html(pswpHtml);
    var parseThumbnailElements = function(el) {
        var thumbElements = $(el).find('figure'), //.childNodes,
                numNodes = thumbElements.length,
                items = [], figureEl, linkEl, size, item;
        for(var i = 0; i < numNodes; i++) {
            figureEl = thumbElements[i]; // <figure> element
            if(figureEl.nodeType !== 1) {
                continue;
            } //console.log(figureEl);
            linkEl = $(figureEl).find('a')[0]; // <a> element
            size = linkEl.getAttribute('data-size').split('x');
            item = {
                src: linkEl.getAttribute('href'),
                w: parseInt(size[0], 10),
                h: parseInt(size[1], 10)
            };
            if(figureEl.children.length > 1) {
                item.title = figureEl.children[1].innerHTML;
            }
            if(linkEl.children.length > 0) {
                item.msrc = linkEl.children[0].getAttribute('src');
            }
            item.el = figureEl; // save link to element for getThumbBoundsFn
            items.push(item);
        }
        return items;
    };
    // find nearest parent element
    var closest = function closest(el, fn) {
        return el && ( fn(el) ? el : closest(el.parentNode, fn) );
    };
    // triggers when user clicks on thumbnail
    var onThumbnailsClick = function(e) {
        e = e || window.event;
        var eTarget = e.target || e.srcElement;
        var clickedListItem = closest(eTarget, function(el) {
            return (el.tagName && el.tagName.toUpperCase() === 'FIGURE');
        });
        if(!clickedListItem) {
            return;
        }
		e.preventDefault ? e.preventDefault() : e.returnValue = false;
        var Gallerys = $(gallerySelector), // 播放多个这里处理???
                childNodes = $(Gallerys).find('figure'),
                numChildNodes = childNodes.length,
                nodeIndex = 0, index;
        for (var i = 0; i < numChildNodes; i++) {
            if(childNodes[i].nodeType !== 1) {
                continue;
            }
            if(childNodes[i] === clickedListItem) {
                index = nodeIndex;
                break;
            }
            nodeIndex++;
        }
        if(index >= 0) {
            openPhotoSwipe( index, Gallerys );
        }
        return false;
    };
    // parse picture index and gallery index from URL (#&pid=1&gid=2)
    var photoswipeParseHash = function() {
        var hash = window.location.hash.substring(1),
                params = {};
        if(hash.length < 5) {
            return params;
        }
        var vars = hash.split('&');
        for (var i = 0; i < vars.length; i++) {
            if(!vars[i]) {
                continue;
            }
            var pair = vars[i].split('=');
            if(pair.length < 2) {
                continue;
            }
            params[pair[0]] = pair[1];
        }
        if(params.gid) {
            params.gid = parseInt(params.gid, 10);
        }
        return params;
    };
    var openPhotoSwipe = function(index, galleryElement, disableAnimation, fromURL) {
        var pswpElement = document.querySelectorAll('.pswp')[0],
                gallery, options, items;
        items = parseThumbnailElements(galleryElement);
		var firstGElm = (typeof(galleryElement[0]) !== "undefined") ? galleryElement[0] : galleryElement;
        options = {
            galleryUID: firstGElm.getAttribute('data-pswp-uid'),
            getThumbBoundsFn: function(index) {
                var thumbnail = items[index].el.getElementsByTagName('img')[0], // find thumbnail
                        pageYScroll = window.pageYOffset || document.documentElement.scrollTop,
                        rect = thumbnail.getBoundingClientRect();
                return {x:rect.left, y:rect.top + pageYScroll, w:rect.width};
            }
        };
        if(fromURL) {
            if(options.galleryPIDs) {
                for(var j = 0; j < items.length; j++) {
                    if(items[j].pid == index) {
                        options.index = j;
                        break;
                    }
                }
            } else {
                options.index = parseInt(index, 10) - 1;
            }
        } else {
            options.index = parseInt(index, 10);
        }
        if( isNaN(options.index) ) {
            return;
        }
        if(disableAnimation) {
            options.showAnimationDuration = 0;
        }
        gallery = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options);
        gallery.init();
    };
    // loop through all gallery elements and bind events
    var galleryElements = document.querySelectorAll( gallerySelector );
    for(var i = 0, l = galleryElements.length; i < l; i++) {
        galleryElements[i].setAttribute('data-pswp-uid', i+1);
        galleryElements[i].onclick = onThumbnailsClick;
    }
    // Parse URL and open gallery if it contains #&pid=3&gid=1
    var hashData = photoswipeParseHash();
    if(hashData.pid && hashData.gid) {
        openPhotoSwipe( hashData.pid ,  galleryElements[ hashData.gid - 1 ], true, true );
    }
};
