var RunetId = new function () {
    var instance;

    // Конструктор
    function RunetId() {
        if (!instance) {
            instance = this;

            this.url = 'https://runet-id.com/oauth/main/dialog/';
            this.recoverUrl = 'https://runet-id.com/oauth/main/recover/';
            this.width = 620;
            this.height = 662;
            this.PopUpWindow = null;

            this.apiKey = '';
            this.rState = '';
            this.backUrl = location.href;
        }

        return instance;
    }

    // Публичные методы
    RunetId.prototype.init = function (options) {
        this.apiKey = options.apiKey;
        //this.rState = options.rState;

        if (typeof(options.backUrl) != 'undefined') {
            this.backUrl = options.backUrl;
        }
    };

    RunetId.prototype.login = function (fast, backUrl) {
        var self = this;

        var windowDimensions = self.windowDimensions();
        var left = (windowDimensions.width - self.width) / 2;
        var top = (windowDimensions.height - self.height) / 2;
        if (self.PopUpWindow != null) {
            self.PopUpWindow.close();
        }
        if (typeof(backUrl) != 'undefined') {
            self.backUrl = backUrl;
        }
        self.PopUpWindow = window.open(self.getUrl(fast), 'RUNETID', 'menubar=no,width=' + self.width + ',height=' + self.height + ',toolbar=no,left=' + left + ',top=' + top);
    };

    RunetId.prototype.getUrl = function (fast) {
        var self = this;
        var url = self.url + '?apikey=' + encodeURIComponent(self.apiKey) + '&url=' + encodeURIComponent(self.backUrl);
        if (typeof fast != "undefined" && fast) {
            url += '&fast=' + encodeURIComponent(fast);
        }
        return url;
    };

    RunetId.prototype.recover = function () {
        var self = this;

        var windowDimensions = self.windowDimensions();
        var left = (windowDimensions.width - self.width) / 2;
        var top = (windowDimensions.height - self.height) / 2;
        if (self.PopUpWindow != null) {
            self.PopUpWindow.close();
        }
        self.PopUpWindow = window.open(self.getRecoverUrl(), 'RUNETID', 'menubar=no,width=' + self.width + ',height=' + self.height + ',toolbar=no,left=' + left + ',top=' + top);
    };

    RunetId.prototype.getRecoverUrl = function () {
        var self = this;

        return self.recoverUrl + '?apikey=' + encodeURIComponent(self.apiKey) + '&url=' + encodeURIComponent(self.backUrl);
    };

    RunetId.prototype.windowDimensions = function () {
        var myWidth = 0, myHeight = 0;
        if (typeof( window.innerWidth ) == 'number') {
            //Non-IE
            myWidth = window.innerWidth;
            myHeight = window.innerHeight;
        } else if (document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight )) {
            //IE 6+ in 'standards compliant mode'
            myWidth = document.documentElement.clientWidth;
            myHeight = document.documentElement.clientHeight;
        } else if (document.body && ( document.body.clientWidth || document.body.clientHeight )) {
            //IE 4 compatible
            myWidth = document.body.clientWidth;
            myHeight = document.body.clientHeight;
        }
        return {width: myWidth, height: myHeight};
    };

    return RunetId;
};
