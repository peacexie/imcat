
var __bind = function(fn, me) {
    return function() {
        return fn.apply(me, arguments);
    };
},
__slice = [].slice;

(function($, window) {
var ResizeCols;
ResizeCols = (function() {
    ResizeCols.prototype.defaults = {
        store: window.store,
        rigidSizing: false
    };
    function ResizeCols(otab, options) {
        this.mousedown = __bind(this.mousedown, this);
        var _this = this;
        this.options = $.extend({}, this.defaults, options);
        this.otab = otab;
        this.tableId = this.otab.data('resizeTab-id');
        this.createHandles();
        this.restore(); // restore Column Width
        this.syncWidths();
        $(window).on('resize.rc', (function() {
            return _this.syncWidths();
        }));
    }
    ResizeCols.prototype.destroy = function() {
        this.hdlObj.remove();
        this.otab.removeData('resizeCols');
        return $(window).off('.rc');
    };
    ResizeCols.prototype.createHandles = function() {
        var _this = this;
        this.otab.before((this.hdlObj = $("<div class='rc-hdlcont' />")));
        this.otab.find('tr th').each(function(i, el) {
            var handle;
            if (_this.otab.find('tr th').eq(i + 1).length === 0 
                || (_this.otab.find('tr th').eq(i).attr('data-noresize') != null) 
                || (_this.otab.find('tr th').eq(i + 1).attr('data-noresize') != null)) {
                return;
            }
            handle = $("<div class='rc-handle' />");
            handle.data('th', $(el));
            return handle.appendTo(_this.hdlObj);
        });
        return this.hdlObj.on('mousedown', '.rc-handle', this.mousedown);
    };
    ResizeCols.prototype.syncWidths = function() {
        var _this = this;
        this.hdlObj.width(this.otab.width());
        return this.hdlObj.find('.rc-handle').each(function(_, el) {
            return $(el).css({
                left: $(el).data('th').outerWidth() + ($(el).data('th').offset().left - _this.hdlObj.offset().left),
                height: _this.otab.height()
            });
        });
    };
    ResizeCols.prototype.saveWidths = function() {
        var _this = this;
        return this.otab.find('tr th').each(function(_, el) {
            var id;
            if ($(el).attr('data-noresize') == null) {
                id = _this.tableId + '-' + $(el).data('resizeCol-id');
                if (_this.options.store != null) {
                    return store.set(id, $(el).width());
                }
            }
        });
    };
    ResizeCols.prototype.restore = function() {
        var _this = this;
        return this.otab.find('tr th').each(function(_, el) {
            var id, width;
            id = _this.tableId + '-' + $(el).data('resizeCol-id');
            if ((_this.options.store != null) && (width = store.get(id))) {
                return $(el).width(width);
            }
        });
    };
    ResizeCols.prototype.mousedown = function(e) {
        var curGrip, leftCol, rightCol, idx, leftStart, rightStart, _this = this;
        e.preventDefault();
        this.startPos = e.pageX;
        curGrip = $(e.currentTarget);
        leftCol = curGrip.data('th');
        leftStart = leftCol.width();
        idx = this.otab.find('tr th').index(curGrip.data('th'));
        rightCol = this.otab.find('tr th').eq(idx + 1);
        rightStart = rightCol.width();
        $(document).on('mousemove.rc',
        function(e) {
            var dif, newLeft, newRight;
            dif = e.pageX - _this.startPos;
            newRight = rightStart - dif;
            newLeft = leftStart + dif;
            if (_this.options.rigidSizing 
                && ((parseInt(rightCol[0].style.width) < rightCol.width()) && (newRight < rightCol.width()))
                || ((parseInt(leftCol[0].style.width) < leftCol.width()) && (newLeft < leftCol.width()))) {
                return;
            }
            leftCol.width(newLeft);
            rightCol.width(newRight);
            return _this.syncWidths();
        });
        return $(document).one('mouseup',
        function() {
            $(document).off('mousemove.rc');
            return _this.saveWidths();
        });
    };
    return ResizeCols;
})();
return $.fn.extend({
    resizeCols: function() {
        var args, option;
        option = arguments[0],
        args = 2 <= arguments.length ? __slice.call(arguments, 1) : [];
        return this.each(function() {
            var otab, data;
            otab = $(this);
            data = otab.data('resizeCols');
            if (!data) {
                otab.data('resizeCols', (data = new ResizeCols(otab, option)));
            }
            if (typeof option === 'string') {
                return data[option].apply(data, args);
            }
        });
    }
});
})(window.jQuery, window);

function utabResize(oid){
    $("head").append("<style type='text/css'>.rc-hdlcont{position:relative;}\n.rc-handle{position:absolute;width:9px;cursor:ew-resize;*cursor:pointer;margin-left:-4px;}</style>");
    if(!oid) oid = '.tblist';
    var no1 = 0;
    $(oid).each(function() {
        var tid = $(this).data('resizeTab-id');
        if(!tid) $(this).attr('data-resizeTab-id','resize-tab'+no1);
        no1++; var no2 = 0;
        $(this).find('tr th').each(function() {
            var tid2 = $(this).data('resizeCol-id');
            if(!tid2) $(this).attr('data-resizeCol-id','resizeCol'+no1+'-'+no2);
            no2++;
        });
    });
    $(oid).resizeCols({});
}
$(function () {
    utabResize();
});
