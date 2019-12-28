
(function (window) {
        var htmlAddEventListener = function (source, eventName, callback, useCapture) {
        if (arguments.length < 4)
            useCapture = false;
        if ((eventName === "load") && (typeof source.onload === "undefined")) {
            var onreadystatechangeEventHandler = function () {
                if (source.readyState === "loaded" || source.readyState === "complete") {
                    callback.call();
                    if (source.removeEventListener)
                        source.removeEventListener("readystatechange", onreadystatechangeEventHandler);
                    else if (source.detachEvent)
                        source.detachEvent("onreadystatechange", onreadystatechangeEventHandler);
                    else
                        source.onreadystatechange = null;
                }
            };

            if (source.addEventListener)
                source.addEventListener("readystatechange", onreadystatechangeEventHandler);
            else if (source.attachEvent)
                source.attachEvent("onreadystatechange", onreadystatechangeEventHandler);
            else
                source.onreadystatechange = onreadystatechangeEventHandler;
        }
        else {
            if (source.addEventListener) {
                source.addEventListener(eventName, callback, useCapture);
            }
            else if (source.attachEvent) {
                source.attachEvent("on" + eventName, callback, useCapture);
            }
        }
    };
    var htmlRemoveEventListener = function (source, eventName, callback, useCapture) {
        if (arguments.length < 4)
            useCapture = false;
        if (source.removeEventListener) {
            source.removeEventListener(eventName, callback, useCapture);
        }
        else if (source.detachEvent) {
            source.detachEvent("on" + eventName, callback, useCapture);
        }
    };
    function sendXHRRequest(url, onreadystatechange) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.overrideMimeType('application/json');
        xmlhttp.open("GET", url, true);       
        xmlhttp.withCredentials = true;
        if(onreadystatechange)
            xmlhttp.onreadystatechange = onreadystatechange;
        xmlhttp.send();
    };    
    function encode_utf8( s )
    {
      return unescape( encodeURIComponent( s ) );
    };
    function decode_utf8( s )
    {
      return decodeURIComponent( escape( s ) );
    };
    function isInViewport (boxElement, cb) {        
//        return;
//        if(that.templateConfig.timing_group && that.templateConfig.timing_group.useVisibiltyQuartile) {
            try {
                var that = this;
                boxElement.ownerDocument.defaultView.visibilityScriptArguments = new Object();         
                boxElement.ownerDocument.defaultView.visibilityScriptArguments.cb = cb;
                boxElement.ownerDocument.defaultView.visibilityScriptArguments.boxElement = boxElement;
                console.log("isInViewport");
                boxElement.ownerDocument.defaultView.visibilityScriptArguments.direct = true;
                var visibilityTrackerTag = document.createElement("script");
                visibilityTrackerTag.type = "text/javascript";
                visibilityTrackerTag.src = "https://contents.adpaths.com/v3/tools/Vpaid/adwVisibilityQuartile.js";
                if(boxElement.tagName.toLowerCase() == "iframe") {
                    var iframeDoc = boxElement.contentDocument ? boxElement.contentDocument : (boxElement.contentWindow ? boxElement.contentWindow.document : boxElement.document);
                    if (iframeDoc.head !== null && 0) {
                        iframeDoc.head.appendChild(visibilityTrackerTag);
                    } else if (iframeDoc.body) {
                        iframeDoc.body.appendChild(visibilityTrackerTag);
                    }
                } else {
                    boxElement.appendChild(visibilityTrackerTag);                
                }
            } catch (e) {
                console.log("adwdebugJS :customInit catch", e);
            }
//        }
    };
    
    var adwaysLibScriptTag = null;
    var delegateScriptTag = null;
    var analyticsScriptTag = null;
//    var effectScriptTag = null;
    var that;
    var adwaysLibLoaded = false;
    var analyticsLibLoaded = false;

    NonLinearAd = function () {
        this.delegate = null;
        this._slot = null;
        this._videoSlot = null;
        this.VPAIDVersion = "2.0";
        // iAb object for player events listeners
        this.listeners = new Array();
        this.duration = -2;
        this.remainingTime = -2;
        this.quartiles = {};
        this.quartiles.zero = false;
        this.quartiles.first = false;
        this.quartiles.mid = false;
        this.quartiles.third = false;
        this.quartiles.last = false;
        this.finalStop = false;
        this.p2s = null;
        this.s2p = null;
        this.videoStarted = false;
        this.tracker = null;
        this.trueViewed = false;
        this.delegateClassname = null;
        this.delegateUrl = null;
        this.domain = null;
        that = this;
        this.completionValue = 0;
        this.adImpressionDispatched = false;
        this.layer = null;
        this.fiframe = null;
        this.fiframeDoc = null;
        this.minTime = Number.NaN;
        this.maxTime = Number.NaN;
        this.width = Number.NaN;
        this.height = Number.NaN;
        this.viewMode = null;
        this.floorPrice = 0;
        this.criteoTag = null;
        this.antvoiceTag = null;
        this.privacyLogo = null;
        this.nativeObjectDescription = null;
        this.initReady = false;
        this.cascadeConfig = null;
        this.quantumAdCallUrl = "";
        this.criteoBannerid = "";
        this.criteoZoneid = "";
        this.appnexusPlacementID = "";
        this.taboolaPubID = "";
        this.antvoiceUsePublisherScript = false;
        this.smartScriptURL = false;
        this.widgetID = "";
        this.currentSSP = "";
        this.templateConfig = null;
        this.visibilityInterval = null;
        this.isVisible = false;
        this.visibleTimer = 0;
        this.visibilityTrackers = new Array();
        this.visibilityIABSent = false;
        this.vpaidParameters = {}; // Contient toutes les query données au vpaid
        this.instantCurrentTime = 0;                        
        this.instantTimer = null;
        this.instantIntervalTime = 250;
        
        this.swipeDetect = function (el, callback, constraint = null){
            var touchsurface = el,
            swipedir,
            startX,
            startY,
            distX,
            distY,
            threshold = 150, //required min distance traveled to be considered swipe
            restraint = 100, // maximum distance allowed at the same time in perpendicular direction
            allowedTime = 300, // maximum time allowed to travel that distance
            elapsedTime,
            startTime,
            handleswipe = callback || function(swipedir){}
            touchsurface.addEventListener('touchstart', function(e){
                var touchobj = e.changedTouches[0]
                swipedir = 'none'
                dist = 0
                startX = touchobj.pageX
                startY = touchobj.pageY
                startTime = new Date().getTime() // record time when finger first makes contact with surface
                // e.preventDefault()
            }, true)
            touchsurface.addEventListener('touchmove', function(e){
                // e.preventDefault() // prevent scrolling when inside DIV
            }, true)
            touchsurface.addEventListener('touchend', function(e){
                var touchobj = e.changedTouches[0]
                distX = touchobj.pageX - startX // get horizontal dist traveled by finger while in contact with surface
                distY = touchobj.pageY - startY // get vertical dist traveled by finger while in contact with surface
                elapsedTime = new Date().getTime() - startTime // get time elapsed
                if (elapsedTime <= allowedTime){ // first condition for awipe met
                    if (Math.abs(distX) >= threshold && Math.abs(distY) <= restraint){ // 2nd condition for horizontal swipe met
                        swipedir = (distX < 0)? 'left' : 'right' // if dist traveled is negative, it indicates left swipe
                    }
                    else if (Math.abs(distY) >= threshold && Math.abs(distX) <= restraint){ // 2nd condition for vertical swipe met
                        swipedir = (distY < 0)? 'up' : 'down' // if dist traveled is negative, it indicates up swipe
                    }
                }
                if(constraint === null ||  (constraint === swipedir)) {
                    handleswipe(swipedir);                    
                }
                // e.preventDefault()
            }, true)
        }   
        this.swipeRightdetect =function (el, callback, constraint) {
            return this.swipeDetect(el, callback, 'right');
        };
        this.swipeLeftdetect =function (el, callback, constraint) {
            return this.swipeDetect(el, callback, 'left');
        };
        this.swipeUpDetect =function (el, callback, constraint) {
            return this.swipeDetect(el, callback, 'up');
        };        
        this.swipeDowndetect =function (el, callback, constraint) {
            return this.swipeDetect(el, callback, 'down');
        };
        
        var queryStr = 'creativeType=nonlinear&useDefaultVideoSlot=1&iab=undefined&publicationId=70DQ1KN&kw_adways_keywords=btnoel&iab=undefined&kw_adways_ratio=169';
        if(queryStr.charAt(0) === '?')
        {
            queryStr = queryStr.substr(1);
        }
        var queryArr = queryStr.split('&');

        for (var q = 0; q < queryArr.length; q++) {
            var qArr = queryArr[q].split('=');
            var key = qArr[0];
            var value = queryArr[q].substring(key.length+1);
            this.vpaidParameters[key] = value;
//            console.log(key, value);
        }
        
        try {
            this.templateConfig = {"config":{"in_content_version":true,"use_SSP":true},"timing_group":{"timing_cta_select":"instant","usePlayState":false,"apparition_duration":15,"visibilityTiming":2,"useVisibilty":false,"useVisibiltyQuartile":false},"display_tracking":{"display_tracking_impression":{"number_of_value_display_tracking_impression":1,"value_display_tracking_impression_0":""},"display_tracking_visibility":{"number_of_value_display_tracking_visibility":1,"value_display_tracking_visibility_0":""}},"trueview_group":{"type_trueview":"second_tv","trueview":2},"design":{"design_card":{"background_card":"#ffffff","card_image_alternative_timing":3000,"card_image_alternative":"name","cardImageAlternativeMention":"","background_card_image_format":"fill","background_card_image_alternative_format":"fill"},"design_drawer":{"drawer_sponsor_color":"#000000","drawer_sponsor_size":4.5,"product_title_color":"#ffffff","product_title_font_size":4.5,"product_description_font_size":4.5,"product_description_font_color":"#ffffff","cta_text_value":"Voir plus","cta_text_font_size":4.3,"ctaTextAnimated":true,"cta_boder_color":"#ffffff","cta_boder_size":2,"drawer_background_color":"#000000","drawer_background_opacity":30},"design_close_btn":{"closeBtnSize":"27","close_btn_color":"#ffffff","close_background_color":"#000000","close_background_opacity":100},"drawer_size":39,"position_group":{"positions_select_unite":"px","drawer_offset_y":130},"splashscreen":false},"ssp_group":{"ssp_number":1,"ssp_unit_0":{"add_call_0":"headerbidding","zoneid0":"1458244","sspEndpoint0":"22139939","appnexusPlacementID_0":"17318861"},"floorPrice":0},"custom_group":{"text_node":{"text_title":"Chaussures montantes","text_content":"121,60 \u20ac - RENDEZ VOUS DECO","text_sponsor":"Adways"},"click_behavior":{"sponsor_click_url":"http:\/\/adways.com\/","content_click_url":"http:\/\/adways.com\/"},"images_node_set":{"drawer_image_url":"\/\/d1w7fcd279xrse.cloudfront.net\/vT7RfXp\/2018\/03\/5ab280e44ead9.jpg","card_image_url":"\/\/d3f95noifdf2nj.cloudfront.net\/kiwi\/image_popup.jpg"}}};
//            console.log("templateConfig ok", this.templateConfig );
        } catch (e) {
            this.templateConfig = null;
            console.log("templateConfig not ok", e);
        }
        this.iab = 'undefined';
        this.expandedState = true;

        this.sizeWatcherTimer = null;
        this.sizeWatcherListener = function () {
            that.updatePosition();
        };

        this.customDurationTimer = null;
        this.customDurationTimerListener = function () {
            that.finalStopCb();
        };

        this.adwaysLibScriptTagLoadCb = function () {
//            console.log("adwdebug : adwaysLibScriptTagLoadCb");
            adwaysLibLoaded = true;
            htmlRemoveEventListener(adwaysLibScriptTag, "load", that.adwaysLibScriptTagLoadCb);
            that.loadAd();
        };
        this.delegateScriptTagLoadCb = function () {
           // console.log("adwdebug : delegateScriptTagLoadCb");
        // TAG JS EXCEPTION
                    
            // TODO : Cas FranceTVDelegate
            if(that.delegate == null || that.p2s == null || that.s2p == null){
                htmlRemoveEventListener(delegateScriptTag, "load", that.delegateScriptTagLoadCb);
                eval("that.delegate = new " + that.delegateClassname + "(that.p2s, that.s2p, that._videoSlot)");
            }
                            that.cascadeConfig = new Array();
                                that.floorPrice = 0;
                        var sspConfigHeaderBidding = new Object();
                        sspConfigHeaderBidding.key = "headerbidding";
                        sspConfigHeaderBidding.zoneid = '1458244';
                        sspConfigHeaderBidding.appnexusPlacementID = '17318861';
                        var quantumAdConfig = '22139939';
                        try {
                            quantumAdConfig = JSON.parse(quantumAdConfig);
                        } catch (e) {
                            console.log("quantumAdConfig = ", quantumAdConfig);
                        }
                        if (quantumAdConfig instanceof Object) {
                            if (that.iab !== '') {
                                if (quantumAdConfig[that.iab]) {
                                    sspConfigHeaderBidding.sspEndpoint = quantumAdConfig[that.iab];
                                } else if (quantumAdConfig['nocat']) {
                                    sspConfigHeaderBidding.sspEndpoint = quantumAdConfig['nocat'];
                                } else {
                                    var that2 = that;
                                    //                                var errorCb = function () {
                                    that2.dispatchEvent("AdError", 915);
                                    //                                };
                                    //                                that.tracker.sendData({event_type: "error", event_name: "915", cbFunction: errorCb});
                                }
                            } else if (quantumAdConfig['nocat']) {
                                sspConfigHeaderBidding.sspEndpoint = quantumAdConfig['nocat'];
                            } else {
                                var that2 = that;
                                //                            var errorCb = function () {
                                that2.dispatchEvent("AdError", 915);
                                //                            };
                                //                            that.tracker.sendData({event_type: "error", event_name: "915", cbFunction: errorCb});
                            }
                        } else {
                            sspConfigHeaderBidding.sspEndpoint = quantumAdConfig;
                        }
                        that.cascadeConfig.push(sspConfigHeaderBidding);
                                //        console.log(that.cascadeConfig);
                that.waitForInitialisation();
            };
        this.analyticsScriptTagLoadCb = function () {
//            console.log("adwdebug : analyticsScriptTagLoadCb");
            analyticsLibLoaded = true;
            htmlRemoveEventListener(analyticsScriptTag, "load", that.analyticsScriptTagLoadCb);            
            var domain = (that.domain!==null)?that.domain:"";
            that.tracker = new window.adways.analytics.Tracker({
                record_interface: "generic",
                creative_format: "WMqd0rN",
                creative_id: "70DQ1KN",
                x_domain: domain,
                random_number: function () {
                    return Math.random();
                }
            });
            that.loadAd();
        };
    };

    NonLinearAd.prototype.prepareManualData = function () {
            if (typeof this.nativeObjectDescription === "undefined" || this.nativeObjectDescription === null) {
            this.nativeObjectDescription = {
                "products": [{
                        "title": "Chaussures montantes",
                        "description": "121,60 € - RENDEZ VOUS DECO",
                        "price": "",
                        "call_to_action": "Voir plus",
                        "click_url": "http://adways.com/",
                        "image": {
                            "url": "//d3f95noifdf2nj.cloudfront.net/kiwi/image_popup.jpg",
                            "width": 400,
                            "height": 400
                        }
                    }],
                "advertiser": {
                    "logo": {
                        "url": "//d1w7fcd279xrse.cloudfront.net/vT7RfXp/2018/03/5ab280e44ead9.jpg",
                        "width": 200,
                        "height": 200
                    },
                    "logo_click_url": "http://adways.com/",
                    "description": "Adways",
                    "domain": "",
                    "legal_text": ""
                },
                "privacy": {
                    "optout_click_url": "http://digitaladvertisingalliance.org/",
                    "optout_image_url": "https://d1tvn48knwz507.cloudfront.net/icons/nai_small.png"
                }
            };
        }
    };
            NonLinearAd.prototype.cascadeAdCallInit = function () {
            this.currentSSP = "";                
            //            console.log("cascadeAdCallInit");
            if (this.cascadeConfig !== null && this.cascadeConfig.length > 0) {
                var sspUnitConfig = this.cascadeConfig.shift();
                //                console.log(sspUnitConfig);
                this.currentSSP = sspUnitConfig.key;
                if (sspUnitConfig.key === "smart-agence-v2") {
                    this.smartScriptURL = sspUnitConfig.scriptURL;
                    this.smartAdCallInit();
                } else if (sspUnitConfig.key === "criteo") {
                    this.criteoBannerid = sspUnitConfig.criteoBannerid;
                    this.criteoZoneid = sspUnitConfig.zoneid;
                    this.criteoAdCallInit();
                } else if (sspUnitConfig.key === "appnexus") {
                    this.appnexusPlacementID = sspUnitConfig.appnexusPlacementID;
                    this.appnexusAdCallInit();
                } else if (sspUnitConfig.key === "antvoice") {
                    this.antvoiceUsePublisherScript = sspUnitConfig.usePublisherScript;
                    this.antvoiceAdCallInit();
                } else if (sspUnitConfig.key === "quantum") {
                    this.quantumAdCallUrl = sspUnitConfig.sspEndpoint;
                    this.quantumAdCallInit();
                } else if (sspUnitConfig.key === "headerbidding") {
                    this.quantumAdCallUrl = sspUnitConfig.sspEndpoint;
                    this.criteoZoneid = sspUnitConfig.zoneid;
                    this.appnexusPlacementID = sspUnitConfig.appnexusPlacementID;
                    this.headerBiddingAdCallInit();
                } else if (sspUnitConfig.key === "outbrain") {
                    this.widgetID = sspUnitConfig.widgetID;
                    this.outbrainPublisherName = sspUnitConfig.publisherName;
                    this.outbrainPublisherLocation = sspUnitConfig.publisherLocation;
                    this.outbrainAdCallInit();
                } else if (sspUnitConfig.key === "ligatus") {
                    this.ligatusID = sspUnitConfig.ligatusID;
                    this.ligatusUseTemplate = sspUnitConfig.ligatusUseTemplate;
                    this.ligatusAdCallInit();
                } else if (sspUnitConfig.key === "taboola") {
                    this.taboolaPubID = sspUnitConfig.taboolaPubID;
                    this.taboolaAdCallInit();
                } else if (sspUnitConfig.key === "manual") {
                    this.prepareManualData();
                    this.init();
                }
            }
        };
                NonLinearAd.prototype.outbrainAdCallInit = function () {
            var adCallUrl = "//widgets.outbrain.com/outbrain.js";
            var that = this;
            var basePermalink = "https://play.adpaths.com/";
            var sourceLocation = this.outbrainPublisherLocation;
            var sourceName = this.outbrainPublisherName;
            // var permalink = "http://www.adways.com/cnn/http://us.cnn.com/2018/07/25/politics/trump-juncker-tariffs-trade/index.html";
            var permalink = basePermalink + sourceLocation + "/" + sourceName + "/" + window.location.href;
            var installationKey = "ADWAYQ3JAQQ4P8NFM8A4HLHFB";

            var outbrain_callback = function (json) {
                //                that.tracker.sendData({event_type: "addcallback", event_name: "outbrain"});   
                if (!json.doc || json.doc.length < 1) {
                    if (that.cascadeConfig !== null && that.cascadeConfig.length > 0) {
                        that.cascadeAdCallInit();
                    } else {
                        var that2 = that;
                        //                        var errorCb = function () {
                        that2.dispatchEvent("AdError", 911);
                        //                        };
                        //                        that.tracker.sendData({event_type: "error", event_name: "911", cbFunction: errorCb});
                    }
                }
                else {
                    that.tracker.sendData({event_type: "addcallbackOK", event_name: "outbrain"});
                    that.nativeObjectDescription = {};
                    if (that.widgetID != 'JS_2') {
                        var doc = json.doc[0];
                        that.nativeObjectDescription.products = [{}];
                        that.nativeObjectDescription.products[0].image = {};
                        that.nativeObjectDescription.advertiser = {};
                        that.nativeObjectDescription.advertiser.logo = {};
                        that.nativeObjectDescription.products[0].title = doc.source_name;
                        that.nativeObjectDescription.products[0].description = doc.content;
                        that.nativeObjectDescription.products[0].click_url = doc.url;
                        that.nativeObjectDescription.products[0].image.url = doc.thumbnail.url;
                        that.nativeObjectDescription.products[0].image.width = doc.thumbnail.width;
                        that.nativeObjectDescription.products[0].image.height = doc.thumbnail.height;
//                        that.nativeObjectDescription.advertiser.description = doc.adv_name;
                        that.nativeObjectDescription.advertiser.description = doc.source_name;
                    } else {
                        // console.log('json callback outbrain', json);
                        var doc = json.doc;
                        that.nativeObjectDescription.products = [];
                        that.nativeObjectDescription.advertiser = [];
                        for (var i = 0; i < doc.length; i++) {
                            var tempAdvertiser = {};

                            tempAdvertiser.logo = {};
//                            tempAdvertiser.description = doc[i].adv_name;
                            tempAdvertiser.description = doc[i].source_name;

                            that.nativeObjectDescription.advertiser.push(tempAdvertiser);

                            var tempProduct = {};
                            tempProduct.image =
                                tempProduct.image = {};
                            tempProduct.title = doc[i].source_name;
                            tempProduct.description = doc[i].content;
                            tempProduct.click_url = doc[i].url;
                            tempProduct.image.url = doc[i].thumbnail.url;
                            tempProduct.image.width = doc[i].thumbnail.width;
                            tempProduct.image.height = doc[i].thumbnail.height;

                            that.nativeObjectDescription.products.push(tempProduct);
                        }
                    }
                    that.nativeObjectDescription.privacy = {};
                    that.nativeObjectDescription.privacy.optout_click_url = "http://digitaladvertisingalliance.org/";
                    that.nativeObjectDescription.privacy.optout_image_url = "https://d1tvn48knwz507.cloudfront.net/icons/nai_small.png";

                    that.init();
                }
            };

            var scriptTagLoadCb = function (e) {
                var request_data = {
                    permalink: permalink,
                    widgetId: that.widgetID,
                    isSecured: true,
                    installationKey: installationKey
                };
                OBR.extern.callRecs(request_data, outbrain_callback);
            };


            this.tracker.sendData({event_type: "addcall", event_name: "outbrain"});
            var outbrainTag = window.document.createElement("script");
            outbrainTag.src = adCallUrl;
            outbrainTag.type = "application/javascript";
            outbrainTag.async = true;
            function requestErrorListener(e) {
                console.log(e);
                if (that.cascadeConfig !== null && that.cascadeConfig.length > 0) {
                    that.cascadeAdCallInit();
                } else {
                    var errorCode = "920";
                    var that2 = that;
                    //                    var errorCb = function () {
                    that2.dispatchEvent("AdError", errorCode);
                    //                    };
                    //                    that.tracker.sendData({event_type: "error", event_name: errorCode, cbFunction: errorCb});
                }
            }
            outbrainTag.addEventListener("error", requestErrorListener);
            outbrainTag.addEventListener("load", scriptTagLoadCb);
            window.document.getElementsByTagName("head")[0].appendChild(outbrainTag);

        };
                NonLinearAd.prototype.quantumAdCallInit = function () {
            var adCallUrl = this.quantumAdCallUrl;
            var jsonRequest = new window.adways.ajax.Request();
            jsonRequest.setURL(adCallUrl);
            jsonRequest.setMethod("GET");
            jsonRequest.getXHR().withCredentials = true;
            jsonRequest.setContentType("text/plain");
            var that = this;
            var requestDoneListener = function (evt) {
                if (jsonRequest !== null && jsonRequest.getState() === window.adways.ajax.states.DONE) {
                    //                    that.tracker.sendData({event_type: "addcallback", event_name: "quantum"});   
                    jsonRequest.removeEventListener(window.adways.ajax.events.STATE_CHANGED, requestDoneListener);
                    var rawResponseText = jsonRequest.getResponseText();
                    jsonRequest = null;
                    var responseObject = JSON.parse(rawResponseText);
                    //                    console.log(responseObject);
                    if (responseObject !== null && typeof (responseObject.cobj) !== "undefined" && responseObject.cobj !== null) {
                        that.nativeObjectDescription = {};
                        that.nativeObjectDescription.impression = [];
                        that.nativeObjectDescription.clickTrakers = [];
                        if (responseObject !== null && typeof (responseObject.nurl) !== "undefined" && responseObject.nurl !== null) {
                            that.nativeObjectDescription.impression.push(responseObject.nurl);
                        }
                        //                        if (responseObject !== null && typeof (responseObject.sync) !== "undefined" && responseObject.sync !== null) {
                        //                            that.nativeObjectDescription.impression.push(responseObject.sync);
                        //                        }                                                               
                        if (typeof (responseObject.sync) !== "undefined" && responseObject.sync !== null && responseObject.sync.length > 0) {
                            for (var j = 0; j < responseObject.sync.length; j++) {
                                that.nativeObjectDescription.impression.push(responseObject.sync[j]);
                            }
                        }
                        that.tracker.sendData({event_type: "addcallbackOK", event_name: "quantum_criteo"});
                        var cobj = responseObject.cobj;
                        that.nativeObjectDescription.products = [{}];
                        that.nativeObjectDescription.products[0].image = {};
                        that.nativeObjectDescription.advertiser = {};
                        that.nativeObjectDescription.advertiser.logo = {};
                        if (typeof (cobj.title) !== "undefined") {//product titre
                            that.nativeObjectDescription.products[0].title = cobj.title;
                        }
                        if (typeof (cobj.description) !== "undefined") { //product desc
                            that.nativeObjectDescription.products[0].description = cobj.description;
                        }
                        if (typeof (cobj.image_url) !== "undefined") { //produit image
                            that.nativeObjectDescription.products[0].image.url = cobj.image_url;
                        }
                        if (typeof (cobj.adomain) !== "undefined") { //sponsor name
                            that.nativeObjectDescription.advertiser.description = cobj.adomain;
                        }
                        if (typeof (cobj.click_url) !== "undefined") { //product clic url
                            that.nativeObjectDescription.products[0].click_url = cobj.click_url;
                        }
                        if (typeof (cobj.privacy) !== "undefined") { //privacy
                            that.nativeObjectDescription.privacy = cobj.privacy;
                        }
                        if (typeof (cobj.impression_pixels) !== "undefined" && cobj.impression_pixels.length > 0) { //impression pixels
                            //                            that.nativeObjectDescription.impression = cobj.impression_pixels;
                            for (var j = 0; j < cobj.impression_pixels.length; j++) {
                                that.nativeObjectDescription.impression.push(cobj.impression_pixels[j]);
                            }
                        }
                        if (typeof (cobj.link) !== "undefined" && typeof (cobj.link.clicktrackers) !== "undefined") { //click tracker
                            that.nativeObjectDescription.clickTrakers = cobj.link.clicktrackers;
                        }
                        that.init();
                    } else if (responseObject !== null && typeof (responseObject.native) !== "undefined" && responseObject.native !== null) {
                        that.nativeObjectDescription = {};
                        that.nativeObjectDescription.impression = [];
                        that.nativeObjectDescription.clickTrakers = [];
                        if (responseObject !== null && typeof (responseObject.nurl) !== "undefined" && responseObject.nurl !== null) {
                            that.nativeObjectDescription.impression.push(responseObject.nurl);
                        }
                        //                        if (responseObject !== null && typeof (responseObject.sync) !== "undefined" && responseObject.sync !== null) {
                        //                            that.nativeObjectDescription.impression.push(responseObject.sync);
                        //                        }                                                            
                        if (typeof (responseObject.sync) !== "undefined" && responseObject.sync !== null && responseObject.sync.length > 0) {
                            for (var j = 0; j < responseObject.sync.length; j++) {
                                that.nativeObjectDescription.impression.push(responseObject.sync[j]);
                            }
                        }
                        that.tracker.sendData({event_type: "addcallbackOK", event_name: "quantum"});
                        var natives = responseObject.native;
                        that.nativeObjectDescription.products = [{}];
                        that.nativeObjectDescription.products[0].image = {};
                        that.nativeObjectDescription.advertiser = {};
                        that.nativeObjectDescription.advertiser.logo = {};
                        if (natives.assets !== null && natives.assets.length > 0) {
                            for (var i = 0; i < natives.assets.length; i++) {
                                var native = natives.assets[i];
                                switch (native.id) {
                                    case 1 : //product titre
                                        that.nativeObjectDescription.products[0].title = native.title.text;
                                        break;
                                    case 2 : //sponsor image url
                                        that.nativeObjectDescription.advertiser.logo.url = native.img.url;
                                        that.nativeObjectDescription.advertiser.logo.width = native.img.w;
                                        that.nativeObjectDescription.advertiser.logo.height = native.img.h;
                                        //                                        that.nativeObjectDescription.advertiser.logo.width = 400;
                                        //                                        that.nativeObjectDescription.advertiser.logo.height = 400;
                                        break;
                                    case 3 : //product desc
                                        that.nativeObjectDescription.products[0].description = native.data.value;
                                        break;
                                    case 4 : //produit image
                                        that.nativeObjectDescription.products[0].image.url = native.img.url;
                                        that.nativeObjectDescription.products[0].image.width = native.img.w;
                                        that.nativeObjectDescription.products[0].image.height = native.img.h;
                                        break;
                                    case 10 : //sponsor name
                                        that.nativeObjectDescription.advertiser.description = native.data.value;
                                        break;
                                    case 2003 : //product clic url
                                        that.nativeObjectDescription.products[0].click_url = native.data.value;
                                        break;
                                }
                            }
                        }
                        if (natives.imptrackers && natives.imptrackers.length > 0) {
                            for (var j = 0; j < natives.imptrackers.length; j++) {
                                that.nativeObjectDescription.impression.push(natives.imptrackers[j]);
                            }
                        }
                        if (natives.link && natives.link.url) {
                            //that.nativeObjectDescription.advertiser.logo_click_url = natives.link.url;
                            that.nativeObjectDescription.products[0].click_url = natives.link.url;
                            if (natives.link.clicktrackers) {
                                that.nativeObjectDescription.clickTrakers = natives.link.clicktrackers;
                            }
                        }
                        that.init();
                    } else {
                        if (that.cascadeConfig !== null && that.cascadeConfig.length > 0) {
                            that.cascadeAdCallInit();
                        } else {
                            var that2 = that;
                            //                            var errorCb = function () {
                            that2.dispatchEvent("AdError", 911);
                            //                            };
                            //                            that.tracker.sendData({event_type: "error", event_name: "911", cbFunction: errorCb});
                        }
                    }
                }
            };
            this.tracker.sendData({event_type: "addcall", event_name: "quantum"});
            jsonRequest.addEventListener(window.adways.ajax.events.STATE_CHANGED, requestDoneListener);
            jsonRequest.load();
        };
                NonLinearAd.prototype.ligatusAdCallInit = function () {
            if(typeof this.ligatusUseTemplate == 'undefined' || !this.ligatusUseTemplate){
                var adCallUrl = "https://adx.ligadx.com/?s=1&pid=" + this.ligatusID;
                var jsonRequest = new window.adways.ajax.Request();
                jsonRequest.setURL(adCallUrl);
                jsonRequest.setMethod("GET");
                jsonRequest.getXHR().withCredentials = true;
                jsonRequest.setContentType("text/plain");
                var that = this;
                var requestDoneListener = function (evt) {
                    if (jsonRequest !== null && jsonRequest.getState() === window.adways.ajax.states.DONE) {
                        //                    that.tracker.sendData({event_type: "addcallback", event_name: "quantum"});   
                        jsonRequest.removeEventListener(window.adways.ajax.events.STATE_CHANGED, requestDoneListener);
                        var rawResponseText = jsonRequest.getResponseText();
                        jsonRequest = null;
                        var responseObject = JSON.parse(rawResponseText);
                        if (responseObject !== null && typeof (responseObject.tags) !== "undefined" && responseObject.tags !== null && responseObject.tags[0] !== null && typeof (responseObject.tags[0].campaigns) !== "undefined" && responseObject.tags[0].campaigns[0] !== null && responseObject.tags[0].campaigns.length > 0) {
                            that.tracker.sendData({event_type: "addcallbackOK", event_name: "ligatus"});
                            var tag = responseObject.tags[0];
                            var campagne = responseObject.tags[0].campaigns[0];
                            that.nativeObjectDescription = {};
                            that.nativeObjectDescription.impression = [];
                            that.nativeObjectDescription.clickTrakers = [];
                            if (typeof campagne['imp-trackers'] != 'undefined' && campagne['imp-trackers'] !== null) {
                                that.nativeObjectDescription.impression = campagne['imp-trackers'];
                                for (var i = 0; i < that.nativeObjectDescription.impression.length; i++) {
                                    var impressionTraker = that.nativeObjectDescription.impression[i];      
                                    if (impressionTraker.indexOf("&ts=") === -1) {
                                        that.nativeObjectDescription.impression[i] = impressionTraker + "&ts=" + Date.now();
                                    }
                                }
                            }   
                            if (typeof tag['visibility-tracker'] != 'undefined' && tag['visibility-tracker'] !== null) {                            
                                var visibilityTraker = tag['visibility-tracker'];      
                                if (visibilityTraker.indexOf("&ts=") === -1) {
                                    visibilityTraker = visibilityTraker + "&ts=" + Date.now();
                                }
                                that.nativeObjectDescription.impression.push(visibilityTraker);
    //                            that.nativeObjectDescription.impression.push(tag['visibility-tracker']);
                            }
                            that.nativeObjectDescription.products = [{}];
                            that.nativeObjectDescription.products[0].image = {};
                            that.nativeObjectDescription.advertiser = {};
                            that.nativeObjectDescription.advertiser.logo = {};
                            if (typeof (campagne.title) !== "undefined") {//product titre
                                that.nativeObjectDescription.products[0].title = campagne.title;
                            }
                            if (typeof (campagne.teaser) !== "undefined") { //product desc
                                that.nativeObjectDescription.products[0].description = campagne.teaser;
                            }
                            if (typeof (campagne['image-url']) !== "undefined") { //produit image
                                that.nativeObjectDescription.products[0].image.url = campagne['image-url'];
                            }
                            if (typeof (campagne.debug.adomain) !== "undefined" && campagne.debug.adomain != null) { //sponsor name
                                that.nativeObjectDescription.advertiser.description = campagne.debug.adomain[0];
                            }
                            if (typeof (campagne.link) !== "undefined") { //product clic url
                                that.nativeObjectDescription.products[0].click_url = campagne.link;
                            }
                            if (typeof (campagne['click-trackers']) !== "undefined" && campagne['click-trackers'] !== null) { //click tracker
                                that.nativeObjectDescription.clickTrakers = campagne['click-trackers'];
                                for (var i = 0; i < that.nativeObjectDescription.clickTrakers.length; i++) {
                                    var clickTraker = that.nativeObjectDescription.clickTrakers[i];      
                                    var eventTrackerPos = clickTraker.indexOf("helios.ligatus.com/click");
                                    if (eventTrackerPos !== -1 && clickTraker.indexOf("&ts=") === -1) {
                                        that.nativeObjectDescription.clickTrakers[i] = clickTraker + "&ts=" + Date.now();
                                    }
                                }
                            }
                            
                            if (typeof (campagne.cta) !== "undefined") { //produit image
                                that.nativeObjectDescription.products[0].call_to_action = campagne.cta;
                            }
                            if (typeof (campagne.privacy) !== "undefined") { //privacy
                                that.nativeObjectDescription.privacy = new Object();
                                that.nativeObjectDescription.privacy.optout_click_url = campagne.privacy.link;
                                that.nativeObjectDescription.privacy.optout_image_url = campagne.privacy.logo_url;
                            }
                            that.init();
                        } else {
                            if (that.cascadeConfig !== null && that.cascadeConfig.length > 0) {
                                that.cascadeAdCallInit();
                            } else {
                                var that2 = that;
                                //                            var errorCb = function () {
                                that2.dispatchEvent("AdError", 911);
                                //                            };
                                //                            that.tracker.sendData({event_type: "error", event_name: "911", cbFunction: errorCb});
                            }
                        }
                    }
                };
                this.tracker.sendData({event_type: "addcall", event_name: "ligatus"});
                jsonRequest.addEventListener(window.adways.ajax.events.STATE_CHANGED, requestDoneListener);
                jsonRequest.load();
            }else{
                this.tracker.sendData({event_type: "addcall", event_name: "ligatus"});
                this.nativeObjectDescription = {};
                this.nativeObjectDescription.ligatusScript = "https://a.ligatus.com/?s=1&t=js&ids=" + this.ligatusID;
                this.init();
            }
        };
                NonLinearAd.prototype.taboolaAdCallInit = function () {
                var apiKey = '132fc0f9050a048b4fb033318b5700cd03e53f92';
                var sourceURL = window.location.href;
                var sourceID = window.location.pathname+window.location.search;
                try {
                    sourceURL = window.top.location.href;
                    sourceID = window.top.location.pathname + window.top.location.search;                    
                } catch (e) {
                    
                }
                var placementName = 'Adways Inkroll';
                var pubID = this.taboolaPubID;
                var adCallUrl = 'https://api.taboola.com/1.2/json/'+pubID+'/recommendations.get?app.apikey='+apiKey+'&app.type=desktop&source.type=video&source.id='+sourceID+'&source.url='+sourceURL+'&placement.name='+placementName+'&placement.visible="false"&placement.rec-count=1&placement.organic-type=mix&placement.thumbnail.width=640&placement.thumbnail.height=480&user.session=init';
                    
                var jsonRequest = new window.adways.ajax.Request();
                jsonRequest.setURL(adCallUrl);
                jsonRequest.setMethod("GET");
                jsonRequest.getXHR().withCredentials = true;
                jsonRequest.setContentType("application/json");
                var that = this;
                var requestDoneListener = function (evt) {
                    var xmlhttp = evt.target;
                    if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
                        var rawResponseText = xmlhttp.responseText;
                        if(rawResponseText === "") {                            
                            if (that.cascadeConfig !== null && that.cascadeConfig.length > 0) {
                                that.cascadeAdCallInit();
                            } else {
                                var that2 = that;
                                that2.dispatchEvent("AdError", 911);
                            }
                            return;
                        }
                        var responseObject = JSON.parse(rawResponseText);
                        if (responseObject !== null && typeof (responseObject.list) !== "undefined" && responseObject.list !== null && responseObject.list.length > 0) {
                            that.tracker.sendData({event_type: "addcallbackOK", event_name: "taboola"});
                            var campagne = responseObject.list[0];
                            that.nativeObjectDescription = {};
                            that.nativeObjectDescription.impressionXHR = [];
                            that.nativeObjectDescription.clickTrakersXHR = [];
                            var availableURL =  'https://api.taboola.com/1.2/json/'+that.taboolaPubID+'/recommendations.notify-available?app.type=desktop&app.apikey='+apiKey+'&response.id='+responseObject.id+'&response.session='+responseObject.session;
                            that.nativeObjectDescription.impressionXHR.push(availableURL);
                            var visibleURL =    'https://api.taboola.com/1.2/json/'+that.taboolaPubID+'/recommendations.notify-visible?app.type=desktop&app.apikey='+apiKey+'&response.id='+responseObject.id+'&response.session='+responseObject.session;
                            that.nativeObjectDescription.impressionXHR.push(visibleURL);
    //                        
                            that.nativeObjectDescription.products = [{}];
                            that.nativeObjectDescription.products[0].image = {};
                            that.nativeObjectDescription.advertiser = {};
                            that.nativeObjectDescription.advertiser.logo = {};
                            if (typeof (campagne.name) !== "undefined") {//product titre
                                that.nativeObjectDescription.products[0].title = campagne.name;
//                                that.nativeObjectDescription.products[0].description = campagne.name;
                            }
                            if (typeof (campagne.description) !== "undefined") { //product desc
                                that.nativeObjectDescription.products[0].description = campagne.description;
                            }
                            if (typeof (campagne.thumbnail) !== "undefined" && campagne.thumbnail !== null && campagne.thumbnail.length > 0) {
                                that.nativeObjectDescription.products[0].image = campagne.thumbnail[0];
                            }
                            if (typeof (campagne.branding) !== "undefined") { //sponsor name
                                that.nativeObjectDescription.advertiser.description = campagne.branding;
                            }
                            if (typeof (campagne.url) !== "undefined") { //product clic url
                                that.nativeObjectDescription.products[0].click_url = campagne.url;
                            }
                            
                            //click tracker
                            var url = 'https://api.taboola.com/1.2/json/'+that.taboolaPubID+'/recommendations.notify-click?app.type=desktop&app.apikey='+apiKey+'&response.id='+responseObject.id+'&response.session='+responseObject.session+'&item.type='+responseObject.list[0].type+'&item.id='+responseObject.list[0].id;
                            that.nativeObjectDescription.clickTrakersXHR.push(url);
                            
                            if (typeof (campagne.disclosure) !== "undefined") { //privacy
                                that.nativeObjectDescription.privacy = new Object();
                                that.nativeObjectDescription.privacy.optout_click_url = campagne.disclosure;
                                that.nativeObjectDescription.privacy.optout_image_url = "https://d1tvn48knwz507.cloudfront.net/icons/nai_small.png";
                            }
//                            console.log(responseObject);
//                            console.log(that.nativeObjectDescription);
                            that.init();
                        } else {
                            if (that.cascadeConfig !== null && that.cascadeConfig.length > 0) {
                                that.cascadeAdCallInit();
                            } else {
                                var that2 = that;
                                that2.dispatchEvent("AdError", 911);
                            }
                        }
                    }
                };
                this.tracker.sendData({event_type: "addcall", event_name: "taboola"});
                sendXHRRequest(adCallUrl, requestDoneListener);
            
        };
                NonLinearAd.prototype.criteoAdCallInit = function () {
            var bannerid = this.criteoBannerid;
            var that = this;
            window.Criteo = window.Criteo || {};
            window.Criteo.events = window.Criteo.events || [];
            var addCallbackTimeout = true;
            var requestBidCB = function (res) {
                addCallbackTimeout = false;
                //                that.tracker.sendData({event_type: "addcallback", event_name: "criteo"});
                //                console.log('requestBidCB', res);
                if (res && res.length > 0 && res[0].nativePayload) {
                    //                    console.log('requestBidCB OK');
                    var response = res[0].nativePayload;
                    that.tracker.sendData({event_type: "addcallbackOK", event_name: "criteo"});
                    that.nativeObjectDescription = {};
                    if (typeof (response.advertiser) !== "undefined") {
                        that.nativeObjectDescription.advertiser = response.advertiser;
                    }
                    if (typeof (response.privacy) !== "undefined") {
                        that.nativeObjectDescription.privacy = response.privacy;
                    }
                    if (typeof (response.impression_pixels) !== "undefined") {
                        that.nativeObjectDescription.impression = response.impression_pixels;
                    }
                    if (typeof (response.products) !== "undefined" && response.products.length > 0) {
                        that.nativeObjectDescription.products = response.products;
                        that.init();
                    }
                } else {
                    if (that.cascadeConfig !== null && that.cascadeConfig.length > 0) {
                        that.cascadeAdCallInit();
                    } else {
                        var errorCode = "920";
                        var that2 = that;
                        //                        var errorCb = function () {
                        that2.dispatchEvent("AdError", errorCode);
                        //                        };
                        //                        that.tracker.sendData({event_type: "error", event_name: errorCode, cbFunction: errorCb});
                    }
                }
            }
            var adUnits = {
                "placements": [
                    {
                        "slotid": "ad-unit-0",
                        "zoneid": that.criteoZoneid,
                        "nativeCallback": function (assets) {
                            that.tracker.sendData({event_type: "addcallbackOKAlternative", event_name: "criteo"});
                            // Custom function to render the native ad.
                            //                        console.log('nativeCallback', assets);
                        }
                    }
                ]
            };

            if (bannerid !== '') {
                adUnits.placements.bannerid = bannerid;
            }

            Criteo.events.push(function () {
                //                console.log("Criteo.events.push");
                // Define the price band range
                //                Criteo.SetLineItemRanges("0..10:0.01;10..25:0.05;25..50:0.10;50..100:0.25");
                Criteo.SetLineItemRanges("0..1:1;1..15:0.25");
                // Call Criteo and execute the callback function for a given timeout
                Criteo.RequestBids(adUnits, requestBidCB, 700);
                setTimeout(
                    function () {
                        //                        console.log("setTimeout criteo");
                        if (addCallbackTimeout) {
                            //                            that.tracker.sendData({event_type: "addCallbackTimeout", event_name: "criteo"});
                            if (that.cascadeConfig !== null && that.cascadeConfig.length > 0) {
                                that.cascadeAdCallInit();
                            } else {
                                var errorCode = "920";
                                var that2 = that;
                                //                                var errorCb = function () {
                                that2.dispatchEvent("AdError", errorCode);
                                //                                };
                                //                                that.tracker.sendData({event_type: "error", event_name: errorCode, cbFunction: errorCb});
                            }
                        }
                    }, 1000);
            });

            this.tracker.sendData({event_type: "addcall", event_name: "criteo"});
            this.criteoTag = window.document.createElement("script");
            this.criteoTag.src = "https://static.criteo.net/js/ld/publishertag.js";
            ;
            this.criteoTag.type = "application/javascript";
            this.criteoTag.async = true;
            function requestErrorListener(e) {
                console.log(e);
                if (that.cascadeConfig !== null && that.cascadeConfig.length > 0) {
                    that.cascadeAdCallInit();
                } else {
                    var errorCode = "920";
                    var that2 = that;
                    //                    var errorCb = function () {
                    that2.dispatchEvent("AdError", errorCode);
                    //                    };
                    //                    that.tracker.sendData({event_type: "error", event_name: errorCode, cbFunction: errorCb});
                }
            }
            this.criteoTag.addEventListener("error", requestErrorListener);
            window.document.getElementsByTagName("head")[0].appendChild(this.criteoTag);
        };
                NonLinearAd.prototype.headerBiddingAdCallInit = function () {
            //            console.log('headerBiddingAdCallInit');          
            var that = this;
            window.pbjs = window.pbjs || {};
            window.pbjs.que = window.pbjs.que || [];
            var bidCallback = function (bidResponses) {
                               // console.log('bidCallback', bidResponses);
                //                that.tracker.sendData({event_type: "addcallback", event_name: "headerbidding"});

                if (typeof bidResponses['ad-unit-0'] != 'undefined') {
                    var winner = pbjs.getAdserverTargetingForAdUnitCode('ad-unit-0');
                    var sspWinner = winner['hb_bidder'];
                    var adId = winner['hb_adid'];
                    var cpm = parseFloat(winner['hb_pb']);
                    var assets = null;

                                       // console.log('winner : ', winner, bidResponses['ad-unit-0']['bids']);

                    if (that.floorPrice === null || that.floorPrice <= cpm) {
                        var appnexusAlias = "";
                        switch (sspWinner) {
                            case 'quantum' :
                            case 'improvedigital' :
                                // assets : 
                                for (var i = 0; i < bidResponses['ad-unit-0']['bids'].length; i++) {
                                    var curr = bidResponses['ad-unit-0']['bids'][i];
                                    if (curr['adId'] === adId) {
//                                        that.currentSSP += "-quantum";
                                        if (typeof (curr['cobj']) !== "undefined" && curr['cobj'] !== null) {
                                            var responseObject = curr;
                                            that.nativeObjectDescription = {};
                                            that.nativeObjectDescription.impression = [];
                                            that.nativeObjectDescription.clickTrakers = [];
                                            if (typeof (responseObject.nurl) !== "undefined" && responseObject.nurl !== null) {
                                                that.nativeObjectDescription.impression.push(responseObject.nurl);
                                            }
                                            //                                            if (typeof (responseObject.sync) !== "undefined" && responseObject.sync !== null) {
                                            //                                                that.nativeObjectDescription.impression.push(responseObject.sync);
                                            //                                            }                                            
                                            if (typeof (responseObject.sync) !== "undefined" && responseObject.sync !== null && responseObject.sync.length > 0) {
                                                for (var j = 0; j < responseObject.sync.length; j++) {
                                                    that.nativeObjectDescription.impression.push(responseObject.sync[j]);
                                                }
                                            }
                                            //                                        that.tracker.sendData({event_type: "addcallbackOK", event_name: "quantum_criteo"});
                                            that.tracker.sendData({event_type: "addcallbackOK", event_name: "headerbidding", event_data: "quantum"});
                                            var cobj = curr['cobj'];
                                            that.nativeObjectDescription.products = [{}];
                                            that.nativeObjectDescription.products[0].image = {};
                                            that.nativeObjectDescription.advertiser = {};
                                            that.nativeObjectDescription.advertiser.logo = {};
                                            if (typeof (cobj.title) !== "undefined") {//product titre                                                
                                                that.nativeObjectDescription.products[0].title = cobj.title;
                                            }
                                            if (typeof (cobj.description) !== "undefined") { //product desc
                                                that.nativeObjectDescription.products[0].description = cobj.description;
                                            }
                                            if (typeof (cobj.image_url) !== "undefined") { //produit image
                                                that.nativeObjectDescription.products[0].image.url = cobj.image_url;
                                            }
                                            if (typeof (cobj.adomain) !== "undefined") { //sponsor name
                                                that.nativeObjectDescription.advertiser.description = cobj.adomain;
                                            }
                                            if (typeof (cobj.click_url) !== "undefined") { //product clic url
                                                that.nativeObjectDescription.products[0].click_url = cobj.click_url;
                                            }
                                            if (typeof (cobj.privacy) !== "undefined") { //privacy
                                                that.nativeObjectDescription.privacy = cobj.privacy;
                                            }
                                            if (typeof (cobj.impression_pixels) !== "undefined") { //impression pixels
                                                //                                            that.nativeObjectDescription.impression = cobj.impression_pixels;
                                                for (var j = 0; j < cobj.impression_pixels.length; j++) {
                                                    that.nativeObjectDescription.impression.push(cobj.impression_pixels[j]);
                                                }
                                            }
                                            if (typeof (cobj.imp_trackers) !== "undefined") { //impression pixels
                                                for (var j = 0; j < cobj.imp_trackers.length; j++) {
                                                    that.nativeObjectDescription.impression.push(cobj.imp_trackers[j]);
                                                }
                                            }
                                            if (typeof (cobj.link) !== "undefined" && typeof (cobj.link.clicktrackers) !== "undefined") { //click tracker
                                                that.nativeObjectDescription.clickTrakers = cobj.link.clicktrackers;
                                            }
                                            that.currentSSP += "-quantum";
                                            that.init();
                                        }
                                        else {
                                            assets = curr['native'];
                                            that.nativeObjectDescription = {};
                                            that.nativeObjectDescription.impression = [];
                                            that.nativeObjectDescription.clickTrakers = [];
                                            if (curr !== null && typeof (curr.nurl) !== "undefined" && curr.nurl !== null) {
                                                that.nativeObjectDescription.impression.push(curr.nurl);
                                            }
                                            //                                            if (curr !== null && typeof (curr.sync) !== "undefined" && curr.sync !== null) {
                                            //                                                that.nativeObjectDescription.impression.push(curr.sync);
                                            //                                            }                                            
                                            if (curr !== null && typeof (curr.sync) !== "undefined" && curr.sync !== null && curr.sync.length > 0) {
                                                for (var j = 0; j < curr.sync.length; j++) {
                                                    that.nativeObjectDescription.impression.push(curr.sync[j]);
                                                }
                                            }
                                            that.tracker.sendData({event_type: "addcallbackOK", event_name: "headerbidding", event_data: "quantum"});
                                            var natives = assets;
                                            that.nativeObjectDescription.products = [{}];
//                                            that.nativeObjectDescription.products[0].image = {};
                                            that.nativeObjectDescription.advertiser = {};
                                            that.nativeObjectDescription.advertiser.logo = {};                                            
                                            
                                            if (typeof (natives.title) !== "undefined") {//product titre
                                                var title = natives.title;
                                                try {
                                                    title = decode_utf8(title);
                                                } catch(e) {
                                                    console.log("decode_utf8 title malformed");
                                                }
                                                that.nativeObjectDescription.products[0].title = title;
                                            }
                                            if (typeof (natives.body) !== "undefined") { //product desc
                                                var description = natives.body;
                                                try {
                                                    description = decode_utf8(description);
                                                } catch(e) {
                                                    console.log("decode_utf8 description malformed");
                                                }
                                                that.nativeObjectDescription.products[0].description = description;
                                            }
                                            if (typeof (natives.image) !== "undefined") { //produit image
                                                that.nativeObjectDescription.products[0].image = natives.image;
                                            }
                                            if (typeof (natives.sponsoredBy) !== "undefined") { //sponsor name
                                                that.nativeObjectDescription.advertiser.description = natives.sponsoredBy;
                                            }
                                            if (typeof (natives.clickUrl) !== "undefined") { //product clic url
                                                that.nativeObjectDescription.products[0].click_url = natives.clickUrl;
                                            }
                                            if (natives.impressionTrackers && natives.impressionTrackers.length > 0) {
                                                for (var j = 0; j < natives.impressionTrackers.length; j++) {
                                                    that.nativeObjectDescription.impression.push(natives.impressionTrackers[j]);
                                                }
                                            }
                                            if (natives.clickTrackers && natives.clickTrackers.length > 0) {
                                                for (var j = 0; j < natives.clickTrackers.length; j++) {
                                                    that.nativeObjectDescription.clickTrakers.push(natives.clickTrackers[j]);
                                                }
                                            }
//                                            if (natives.assets !== null && natives.assets.length > 0) {
//                                                for (var i = 0; i < natives.assets.length; i++) {
//                                                    var native = natives.assets[i];
//                                                    switch (native.id) {
//                                                        case 1 : //product titre
//                                                            that.nativeObjectDescription.products[0].title = native.title.text;
//                                                            break;
//                                                        case 2 : //sponsor image url
//                                                            that.nativeObjectDescription.advertiser.logo.url = native.img.url;
//                                                            that.nativeObjectDescription.advertiser.logo.width = native.img.w;
//                                                            that.nativeObjectDescription.advertiser.logo.height = native.img.h;
//                                                            //                                        that.nativeObjectDescription.advertiser.logo.width = 400;
//                                                            //                                        that.nativeObjectDescription.advertiser.logo.height = 400;
//                                                            break;
//                                                        case 3 : //product desc
//                                                            that.nativeObjectDescription.products[0].description = native.data.value;
//                                                            break;
//                                                        case 4 : //produit image
//                                                            that.nativeObjectDescription.products[0].image.url = native.img.url;
//                                                            that.nativeObjectDescription.products[0].image.width = native.img.w;
//                                                            that.nativeObjectDescription.products[0].image.height = native.img.h;
//                                                            break;
//                                                        case 10 : //sponsor name
//                                                            that.nativeObjectDescription.advertiser.description = native.data.value;
//                                                            break;
//                                                        case 2003 : //product clic url
//                                                            that.nativeObjectDescription.products[0].click_url = native.data.value;
//                                                            break;
//                                                    }
//                                                }
//                                            }
//                                            if (natives.imptrackers && natives.imptrackers.length > 0) {
//                                                for (var j = 0; j < natives.imptrackers.length; j++) {
//                                                    that.nativeObjectDescription.impression.push(natives.imptrackers[j]);
//                                                }
//                                            }
//                                            if (natives.link && natives.link.url) {
//                                                //that.nativeObjectDescription.advertiser.logo_click_url = natives.link.url;
//                                                that.nativeObjectDescription.products[0].click_url = natives.link.url;
//                                                if (natives.link.clicktrackers) {
//                                                    that.nativeObjectDescription.clickTrakers = natives.link.clicktrackers;
//                                                }
//                                            }
                                            that.currentSSP += "-quantum";
                                            that.init();
                                        }
                                        break;
                                    }
                                }
                                break;
                            case 'criteo' :
                                var win = window;
                                for (var i = 0; i < 10; ++i) {
                                    if (win.criteo_prebid_native_slots) {
//                                        that.currentSSP += "-criteo";
                                        var responseSlot = win.criteo_prebid_native_slots[adId];
                                        if (typeof responseSlot === "undefined") {
                                            if (Object.keys(win.criteo_prebid_native_slots).length > 0) {
                                                responseSlot = win.criteo_prebid_native_slots[Object.keys(win.criteo_prebid_native_slots)[0]];
                                            }
                                        }
                                        if (typeof responseSlot !== "undefined") {
                                            assets = responseSlot.payload;
                                            that.tracker.sendData({event_type: "addcallbackOK", event_name: "headerbidding", event_data: "criteo"});
                                            that.nativeObjectDescription = {};
                                            if (typeof (assets.advertiser) !== "undefined") {
                                                that.nativeObjectDescription.advertiser = assets.advertiser;
                                            }
                                            if (typeof (assets.privacy) !== "undefined") {
                                                that.nativeObjectDescription.privacy = assets.privacy;
                                            }
                                            if (typeof (assets.impression_pixels) !== "undefined") {
                                                that.nativeObjectDescription.impression = assets.impression_pixels;
                                            }
                                            if (typeof (assets.products) !== "undefined" && assets.products.length > 0) {
                                                that.nativeObjectDescription.products = assets.products;                                                
                                                that.currentSSP += "-criteo";
                                                that.init();
                                            }
                                        }
                                        break;
                                    } else {
                                        win = win.parent;
                                    }
                                }
                                break;
                            case 'appnexus' :
                                appnexusAlias = "appnexus";
                            case 'quantumAppnexus' :
                                if(appnexusAlias === '') appnexusAlias = "quantumAppnexus";
                                for (var i = 0; i < bidResponses['ad-unit-0']['bids'].length; i++) {
                                    var curr = bidResponses['ad-unit-0']['bids'][i];
                                    if (curr['adId'] === adId) {
                                        var nativeAd = curr['native'];
                                        // console.log('nativeAd appnexus', nativeAd);
                                        if(typeof nativeAd === 'undefined') {
                                            console.log("appnexus not nativeAd", curr);
                                            if (that.cascadeConfig !== null && that.cascadeConfig.length > 0) {
                                                that.cascadeAdCallInit();
                                            } else {
                                                var errorCode = "920";
                                                var that2 = that;
                                                that2.dispatchEvent("AdError", errorCode);
                                            }
                                        } else {
                                            that.tracker.sendData({event_type: "addcallbackOK", event_name: "headerbidding", event_data: appnexusAlias});
                                            that.nativeObjectDescription = {};
                                            that.nativeObjectDescription.impression = [];
                                            that.nativeObjectDescription.javascriptTrackers = [];
                                            that.nativeObjectDescription.clickTrakers = [];
                                            that.nativeObjectDescription.products = [{}];
                                            that.nativeObjectDescription.products[0].image = {};
                                            that.nativeObjectDescription.advertiser = {};
                                            that.nativeObjectDescription.advertiser.logo = {};
                                            that.nativeObjectDescription.privacy = {};

                                            that.nativeObjectDescription.products[0].title = nativeAd.title;
                                            if(nativeAd.icon) {
                                                that.nativeObjectDescription.advertiser.logo.url = nativeAd.icon.url;
                                                that.nativeObjectDescription.advertiser.logo.width = nativeAd.icon.width;
                                                that.nativeObjectDescription.advertiser.logo.height = nativeAd.icon.height;
                                            }
                                            that.nativeObjectDescription.products[0].description = nativeAd.body;
                                            if(nativeAd.image) {
                                                that.nativeObjectDescription.products[0].image.url = nativeAd.image.url;
                                                that.nativeObjectDescription.products[0].image.width = nativeAd.image.width;
                                                that.nativeObjectDescription.products[0].image.height = nativeAd.image.height;
                                            }
                                            if (typeof (nativeAd.body) !== "undefined") {
                                                that.nativeObjectDescription.advertiser.description  = nativeAd.body;
                                            }
                                            if (typeof (nativeAd.body2) !== "undefined") {
                                                that.nativeObjectDescription.advertiser.description  = nativeAd.body2;
                                            }
                                            that.nativeObjectDescription.products[0].click_url = nativeAd.clickUrl;
                                            that.nativeObjectDescription.products[0].call_to_action = nativeAd.cta;
                                            that.nativeObjectDescription.privacy.optout_click_url = nativeAd.privacyLink;

                                            if (nativeAd.impressionTrackers && nativeAd.impressionTrackers.length > 0) {
                                                for (var j = 0; j < nativeAd.impressionTrackers.length; j++) {
                                                    that.nativeObjectDescription.impression.push(nativeAd.impressionTrackers[j]);
                                                }
                                            }
                                            if (nativeAd.clickTrackers && nativeAd.clickTrackers.length > 0) {
                                                for (var j = 0; j < nativeAd.clickTrackers.length; j++) {
                                                    that.nativeObjectDescription.clickTrakers.push(nativeAd.clickTrackers[j]);
                                                }
                                            }
                                            try {
                                                for (var j = 0; j < nativeAd.javascriptTrackers.length; j++) {
                                                    var b = nativeAd.javascriptTrackers[j].split('<script type="text/javascript" async="true" data-src="');
                                                    for (var i=1;i<b.length; i++) {
                                                        if(b[i].length>0){
                                                            that.visibilityTrackers.push(b[i].replace(/["]/g, "").replace('></script>', ''));
                                                                                                                    }
                                                    }
                                                    var b = nativeAd.javascriptTrackers[j].split('<script src="');
                                                    for (var i=1;i<b.length; i++) {
                                                        if(b[i].length>0){
                                                            that.visibilityTrackers.push(b[i].replace(/["]/g, "").replace('></script>', ''));
                                                                                                                    }
                                                    }
                                                }
                                            } catch(e) {
                                                console.log(e);
                                            }
//                                            that.currentSSP += "-appnexus";
                                            that.currentSSP += "-" + appnexusAlias;
                                            // console.log('that.nativeObjectDescription', that.nativeObjectDescription);
                                            that.init();
                                        }
                                        break;
                                    }
                                }
                                break;
                        }
                    } else {
                        if (that.cascadeConfig !== null && that.cascadeConfig.length > 0) {
                            that.cascadeAdCallInit();
                        } else {
                            var errorCode = "920";
                            var that2 = that;
                            //                            var errorCb = function () {
                            that2.dispatchEvent("AdError", errorCode);
                            //                            };
                            //                            that.tracker.sendData({event_type: "error", event_name: errorCode, cbFunction: errorCb});
                        }
                    }
                    //                    console.log('assets : ', assets);                    
                } else {
                    if (that.cascadeConfig !== null && that.cascadeConfig.length > 0) {
                        that.cascadeAdCallInit();
                    } else {
                        var errorCode = "920";
                        var that2 = that;
                        //                        var errorCb = function () {
                        that2.dispatchEvent("AdError", errorCode);
                        //                        };
                        //                        that.tracker.sendData({event_type: "error", event_name: errorCode, cbFunction: errorCb});
                    }
                }
            }
            pbjs.que.push(function () {

                //                console.log('push');
                
                               // pbjs.setConfig({
                               //     debug: false
                               // });

//                var adUnits = {
//                    code: "ad-unit-0",
//                    mediaType: 'native',
//                    sizes: [
//                        [360, 360]
//                    ],
//                    bids: [
//                        {
//                            bidder: "quantumAppnexus",
//                            params: {
//                                placementId: 16427864,
//                                allowSmallerSizes : true,
//                            }
//                        }
//                    ]
//                };
//                pbjs.addAdUnits(adUnits);
//                pbjs.aliasBidder("appnexus","quantumAppnexus");   
                
                var adUnits = {
                    code: "ad-unit-0",
                    mediaType: 'native',
//                    mediaTypes: {
//                        native: {
//                            type: 'image'
//                        }
//                    },
                    sizes: [
                        [360, 360]
                    ],
                    bids: [
                        {
                            bidder: "criteo",
                            params: {
                                zoneId: that.criteoZoneid,
                                nativeCallback: function (assets) { // just to specify that is a native ad
                                }
                            }
                        },{
                            bidder: "improvedigital",
                            params: {
                                placementId: that.quantumAdCallUrl,
                                nativeCallback: function (assets) { // just to specify that is a native ad
                                }
                            }
                        }, {
                            bidder: "appnexus",
                            params: {
                                placementId: that.appnexusPlacementID,//that.appnexusPlacementID,  // exemple ID : 13144370
                                allowSmallerSizes : true,
                            }
                        }
                    ]
                };
                pbjs.addAdUnits(adUnits);
                
                pbjs.requestBids({
                    bidsBackHandler: bidCallback
                });
            });

            this.tracker.sendData({event_type: "addcall", event_name: "headerbidding"});
            this.prebidTag = window.document.createElement("script");
            this.prebidTag.src = "https://contents.adpaths.com/v3/tools/Vpaid/prebid2.31.0.js";
            this.prebidTag.type = "application/javascript";
            this.prebidTag.async = true;
            function requestErrorListener(e) {
                console.log(e);
                if (that.cascadeConfig !== null && that.cascadeConfig.length > 0) {
                    that.cascadeAdCallInit();
                } else {
                    var errorCode = "920";
                    var that2 = that;
                    //                    var errorCb = function () {
                    that2.dispatchEvent("AdError", errorCode);
                    //                    };
                    //                    that.tracker.sendData({event_type: "error", event_name: errorCode, cbFunction: errorCb});
                }
            }
            this.prebidTag.addEventListener("error", requestErrorListener);
            //            this.prebidTag.addEventListener("load", function() {console.log('script prebid loaded');});
            window.document.getElementsByTagName("head")[0].appendChild(this.prebidTag);
        };

            NonLinearAd.prototype.antvoiceAdCallInit = function () {
            //Permet de tracker les comportement utilisateurs
            var that = this;
            if (!this.antvoiceUsePublisherScript) {
                if (!window.av3w)
                {
                    window.av3w = {};
                }
                window.av3w.productUrl = location.href;
                if (window.self != window.top)
                {
                    window.av3w.productUrl = document.referrer;
                }
                window.av3w.project = "adways";
                var _i = document.createElement("img");
                var _iURL = "https://ads.avads.net/v1/tracking?type=behavior&owner=" + window.av3w.project + "&url=" + encodeURIComponent(window.av3w.productUrl) + "&act=view&market=FR&lang=fr-FR&cat=www";
                _i.src = _iURL;
                function requestErrorListener(e) {
                    console.log(e);
                    if (that.cascadeConfig !== null && that.cascadeConfig.length > 0) {
                        that.cascadeAdCallInit();
                    } else {
                        var errorCode = "920";
                        var that2 = that;
                        //                            var errorCb = function () {
                        that2.dispatchEvent("AdError", errorCode);
                        //                            };
                        //                            that.tracker.sendData({event_type: "error", event_name: errorCode, cbFunction: errorCb});
                    }
                }
                _i.addEventListener("error", requestErrorListener);
                window.document.getElementsByTagName("head")[0].appendChild(_i);
                if (!window.antvoice_variable)
                {
                    window.antvoice_variable = {};
                }
                window.antvoice_variable.project = "adways";
            } else {
                if (typeof window.av3w == "undefined") {
                    try {
                        var parentWindow = window;
                        while (typeof parentWindow.av3w == "undefined" && parentWindow != parentWindow.parent)
                            parentWindow = parentWindow.parent;
                        if (typeof parentWindow.av3w != "undefined")
                            window.av3w = parentWindow.av3w;
                    } catch (err) {
                        if (that.cascadeConfig !== null && that.cascadeConfig.length > 0) {
                            that.cascadeAdCallInit();
                        } else {
                            var errorCode = "940";
                            var that2 = that;
                            //                                var errorCb = function () {
                            that2.dispatchEvent("AdError", errorCode);
                            //                                };
                            //                                that.tracker.sendData({event_type: "error", event_name: errorCode, cbFunction: errorCb});
                        }
                        return -1;
                    }
                    if (typeof window.av3w === "undefined") {
                        if (that.cascadeConfig !== null && that.cascadeConfig.length > 0) {
                            that.cascadeAdCallInit();
                        } else {
                            var errorCode = "930";
                            var that2 = that;
                            //                                var errorCb = function () {
                            that2.dispatchEvent("AdError", errorCode);
                            //                                };
                            //                                that.tracker.sendData({event_type: "error", event_name: errorCode, cbFunction: errorCb});
                        }
                        return -1;
                    }
                }
                if (!window.antvoice_variable)
                {
                    window.antvoice_variable = {};
                }
                window.antvoice_variable.project = window.av3w.publisher

            }
            if (!srEnsureReady) {
                function srEnsureReady(callback) {
                    if (!window.srReady) {
                        window.setTimeout(function () {
                            srEnsureReady(callback);
                        }, 50);
                    }
                    else {
                        callback();
                    }
                };
            }

            //Chargement de la librairie JS AntVoice            
            this.tracker.sendData({event_type: "addcall", event_name: "antvoice"});
            this.antvoiceTag = window.document.createElement("script");
            this.antvoiceTag.src = "https://js.avads.net/sr-" + window.antvoice_variable.project + ".js";
            ;
            this.antvoiceTag.type = "application/javascript";
            function requestErrorListener(e) {
                console.log(e);
                if (that.cascadeConfig !== null && that.cascadeConfig.length > 0) {
                    that.cascadeAdCallInit();
                } else {
                    var errorCode = "920";
                    var that2 = that;
                    //                        var errorCb = function () {
                    that2.dispatchEvent("AdError", errorCode);
                    //                        };
                    //                        that.tracker.sendData({event_type: "error", event_name: errorCode, cbFunction: errorCb});
                }
            }
            this.antvoiceTag.addEventListener("error", requestErrorListener);
            window.document.getElementsByTagName("head")[0].appendChild(this.antvoiceTag);

            var addCallbackTimeout = true;
            setTimeout(
                function () {
                    if (addCallbackTimeout) {
                        //                            that.tracker.sendData({event_type: "addCallbackTimeout", event_name: "antvoice"});
                        if (that.cascadeConfig !== null && that.cascadeConfig.length > 0) {
                            that.cascadeAdCallInit();
                        } else {
                            var errorCode = "920";
                            var that2 = that;
                            //                                var errorCb = function () {
                            that2.dispatchEvent("AdError", errorCode);
                            //                                };
                            //                                that.tracker.sendData({event_type: "error", event_name: errorCode, cbFunction: errorCb});
                        }
                    }
                }, 1000);

            //AntVoice recommendation Call V2
            // Async Call to AntVoice Ad Recommendation
            srEnsureReady(
                function () {
                    addCallbackTimeout = false;
                    //                        that.tracker.sendData({event_type: "addcallback", event_name: "antvoice"});
                    _sr.getReco([{
                            areaId: "ADWAYS_NATIVE_AD",
                            location: "Unspecified",
                            rendering: {
                                onSuccess: function (data) {
                                    // Templating actions here (or use defined function instead)
                                    //                                    console.log("Something to recommend");
                                    //                                    console.log(JSON.stringify(data));  

                                    var responseObject = data;
                                    //                                console.log(responseObject);
                                    if (responseObject !== null && responseObject.assets !== null) {
                                        that.tracker.sendData({event_type: "addcallbackOK", event_name: "antvoice"});
                                        that.nativeObjectDescription = {};
                                        that.nativeObjectDescription.products = [{}];
                                        if (responseObject.assets.title !== null)
                                            that.nativeObjectDescription.products[0].title = responseObject.assets.title;
                                        if (responseObject.assets.summary !== null)
                                            that.nativeObjectDescription.products[0].description = responseObject.assets.summary;
                                        if (responseObject.assets.click_url !== null)
                                            that.nativeObjectDescription.products[0].click_url = responseObject.assets.click_url;
                                        if (responseObject.assets.image !== null) {
                                            that.nativeObjectDescription.products[0].image = {};
                                            that.nativeObjectDescription.products[0].image.url = responseObject.assets.image;
                                            that.nativeObjectDescription.products[0].image.width = 400;
                                            that.nativeObjectDescription.products[0].image.height = 400;
                                        }
                                        that.nativeObjectDescription.advertiser = {};
                                        that.nativeObjectDescription.advertiser.logo = {};
                                        if (responseObject.assets.sponsor_logo !== null) {
                                            that.nativeObjectDescription.advertiser.logo.url = responseObject.assets.sponsor_logo;
                                            that.nativeObjectDescription.advertiser.logo.width = 400;
                                            that.nativeObjectDescription.advertiser.logo.height = 400;
                                        }
                                        if (responseObject.assets.sponsor_name !== null) {
                                            that.nativeObjectDescription.advertiser.description = responseObject.assets.sponsor_name;
                                        }
                                        if (responseObject.assets.sponsor_url !== null) {
                                            that.nativeObjectDescription.advertiser.logo_click_url = responseObject.assets.sponsor_url;
                                        }
                                        if (responseObject.assets.recoId !== null) {
                                            that.nativeObjectDescription.recoId = responseObject.assets.recoId;
                                        }
                                        if (responseObject.assets.catalogId !== null) {
                                            that.nativeObjectDescription.catalogId = responseObject.assets.catalogId;
                                        }
                                        if (responseObject.assets.productId !== null) {
                                            that.nativeObjectDescription.productId = responseObject.assets.productId;
                                        }
                                        if (responseObject.imp_trackers !== null) {
                                            that.nativeObjectDescription.impression = responseObject.imp_trackers;
                                        }
                                        that.init();
                                    } else {
                                        var that2 = that;
                                        //                                        var errorCb = function () {
                                        that2.dispatchEvent("AdError", 911);
                                        //                                        };
                                        //                                        that.tracker.sendData({event_type: "error", event_name: "911", cbFunction: errorCb});
                                    }
                                },
                                onError: function (data) {
                                    if (that.cascadeConfig !== null && that.cascadeConfig.length > 0) {
                                        that.cascadeAdCallInit();
                                    } else {
                                        // Fallback actions here (or use defined function instead)
                                        //                                        console.log("Nothing to recommend");
                                        //                                        console.log(JSON.stringify(data));                                    
                                        var errorCode = "911";
                                        var that2 = that;
                                        //                                        var errorCb = function () {
                                        that2.dispatchEvent("AdError", errorCode);
                                        //                                        };
                                        //                                        that.tracker.sendData({event_type: "error", event_name: errorCode, cbFunction: errorCb});
                                    }
                                    return -1;
                                }
                            }
                            , tracking: {
                                subTracker: "70DQ1KN"
                            }
                        }]);
                }
            );

        };
            NonLinearAd.prototype.smartAdCallInit = function () {
            var that = this;
            var requestDoneListener = function (evt) {
                if (fiframe.contentDocument.URL == "about:blank")
                    return -1;
                var nativeDataDiv = fiframe.contentDocument.querySelector("#adw_sas_nativeDataDiv");
                if (nativeDataDiv == null) {
                    var that2 = that;
                    if (that.cascadeConfig !== null && that.cascadeConfig.length > 0) {
                        that.cascadeAdCallInit();
                    } else {
                        //                        var errorCb = function () {
                        that2.dispatchEvent("AdError", 910);
                        //                        };
                        //                        that.tracker.sendData({event_type: "error", event_name: "910", cbFunction: errorCb});
                    }
                } else {
                    fiframe.removeEventListener("load", requestDoneListener);
                    //                                        console.log("innerHTML",nativeDataDiv.innerHTML);
                    var responseObject = JSON.parse(nativeDataDiv.innerHTML);
                    //                                        console.log(responseObject);
                    //                    that.tracker.sendData({event_type: "addcallback", event_name: "smart-agence-v2"});
                    if (responseObject !== null && responseObject.assets !== null) {
                        that.tracker.sendData({event_type: "addcallbackOK", event_name: "smart-agence-v2"});
                        that.nativeObjectDescription = {};
                        that.nativeObjectDescription.products = [{}];
                        if (responseObject.assets.title !== null)
                            that.nativeObjectDescription.products[0].title = responseObject.assets.title;
                        if (responseObject.assets.description !== null)
                            that.nativeObjectDescription.products[0].description = responseObject.assets.description;
                        if (responseObject.assets.click_url !== null)
                            that.nativeObjectDescription.products[0].click_url = responseObject.assets.click_url;
                        if (responseObject.assets.image !== null) {
                            that.nativeObjectDescription.products[0].image = {};
                            that.nativeObjectDescription.products[0].image.url = responseObject.assets.image;
                            that.nativeObjectDescription.products[0].image.width = 400;
                            that.nativeObjectDescription.products[0].image.height = 400;
                        }
                        that.nativeObjectDescription.advertiser = {};
                        that.nativeObjectDescription.advertiser.logo = {};
                        if (responseObject.assets.sponsor_logo !== null) {
                            that.nativeObjectDescription.advertiser.logo.url = responseObject.assets.sponsor_logo;
                            that.nativeObjectDescription.advertiser.logo.width = 400;
                            that.nativeObjectDescription.advertiser.logo.height = 400;
                        }
                        if (responseObject.assets.sponsor_name !== null) {
                            that.nativeObjectDescription.advertiser.description = responseObject.assets.sponsor_name;
                        }
                        if (responseObject.assets.sponsor_url !== null) {
                            that.nativeObjectDescription.advertiser.logo_click_url = responseObject.assets.sponsor_url;
                        }
                        //                        if (responseObject.assets.click_url !== null)
                        //                            that.nativeObjectDescription.logo_click_url = responseObject.assets.click_url;

                        if (responseObject.imp_trackers !== null) {
                            that.nativeObjectDescription.impression = responseObject.imp_trackers;
                        }

                        that.init();
                    } else {
                        var that2 = that;
                        if (that.cascadeConfig !== null && that.cascadeConfig.length > 0) {
                            that.cascadeAdCallInit();
                        } else {
                            //                            var errorCb = function () {
                            that2.dispatchEvent("AdError", 911);
                            //                            };
                            //                            that.tracker.sendData({event_type: "error", event_name: "911", cbFunction: errorCb});
                        }
                    }
                }
            };
            try {
                var fiframe = this._slot.ownerDocument.createElement("iframe");
                fiframe.style.border = "0px",
                    fiframe.style.overflow = "hidden",
                    //                fiframe.style.display = "none",
                    fiframe.scrolling = "no";
                fiframe.addEventListener("load", requestDoneListener);
                this._slot.ownerDocument.body.appendChild(fiframe);
                var a = '<html><head><scr' + 'ipt type="application/javascript" src="' + this.smartScriptURL + '"></scr' + 'ipt></head><body></body></html>';
                this.fiframeDoc = fiframe.contentDocument ? fiframe.contentDocument : (fiframe.contentWindow ? fiframe.contentWindow.document : fiframe.document);
                this.fiframeDoc.open("text/html");
                this.fiframeDoc.write(a);
                this.fiframeDoc.close();
            } catch (e) {
                //                console.log("error :", e);
                var that = this;
                if (that.cascadeConfig !== null && that.cascadeConfig.length > 0) {
                    that.cascadeAdCallInit();
                } else {
                    //                    var errorCb = function () {
                    that.dispatchEvent("AdError", 912);
                    //                    };
                    //                    this.tracker.sendData({event_type: "error", event_name: "912", cbFunction: errorCb});
                }
            }
            this.tracker.sendData({event_type: "addcall", event_name: "smart-agence-v2"});
        };
        NonLinearAd.prototype.buildFiFrame = function (container = null, cb = null) {
        var mainContainer = (container !== null) ? container : this._slot;
        this.fiframe = mainContainer.ownerDocument.createElement("iframe");

        this.fiframe.sandbox = "allow-same-origin allow-scripts allow-popups allow-forms";
        this.fiframe.style.setProperty("border", "0px", "important");
        this.fiframe.style.setProperty("overflow", "hidden", "important");
        this.fiframe.style.setProperty("scrolling", "no", "important");
        this.fiframe.style.setProperty("position", "absolute", "important");
        this.fiframe.style.setProperty("top", "0px", "important");
        this.fiframe.style.setProperty("left", "0px", "important");
        this.fiframe.style.setProperty("width", "0%", "important");
        this.fiframe.style.setProperty("height", "0%", "important");
        this.fiframe.style.setProperty("max-width", "none", "important");
        this.fiframe.style.setProperty("max-height", "none", "important");
        this.fiframe.id = 'vpaidIframe';
        var that = this;         
//        console.log('adwDebug : vpaidIframe appendChild');        
        
        var loadedFunction = function() {
//            console.log('adwDebug : loadedFunction', that.fiframe, that.fiframeDoc);
            if(that.fiframeDoc) {
                that.fiframe.removeEventListener("load", loadedFunction); 
                cb();
            }
        };
        var cb = null;
        if(arguments.length>1 && arguments[1] !== null) {
            cb = arguments[1];
//            console.log('adwDebug : vpaidIframe appendChild with cb');
            this.fiframe.addEventListener("load", loadedFunction); 
        } 
        mainContainer.appendChild(this.fiframe); 
        var a = "<html><head></head><body></body></html>";
        that.fiframeDoc = that.fiframe.contentDocument ? that.fiframe.contentDocument : (that.fiframe.contentWindow ? that.fiframe.contentWindow.document : that.fiframe.document);
        that.fiframeDoc.open("text/html");
        that.fiframeDoc.write(a);
        that.fiframeDoc.close();
        that.fiframeDoc.body.style.margin = 0;
        that.fiframeDoc.body.style.border = 0;
        that.fiframeDoc.body.style.padding = 0;        
//        for(var i=0; i<that.visibilityTrackers.length; i++) {
//            var visibilityTrackerTag = document.createElement("script");
//            visibilityTrackerTag.type = "text/javascript";
//            visibilityTrackerTag.src = that.visibilityTrackers[i];
//            if (that.fiframeDoc.head !== null) {
//                that.fiframeDoc.head.appendChild(visibilityTrackerTag);
//            }
//        }      
    };
    
    NonLinearAd.prototype.initAd = function (width, height, viewMode, desiredBitrate, creativeData, environmentVars) {
//        console.log("adwdebug : initAd", environmentVars);
//        this.dispatchEvent("AdLoaded");
        this.width = width;
        this.height = height;
        this.viewMode = viewMode;

        if (typeof creativeData.AdParameters !== "undefined" && creativeData.AdParameters !== null && creativeData.AdParameters !== "") {
            var givenConfigs = JSON.parse(creativeData.AdParameters);
            if (typeof givenConfigs.visibility !== "undefined") {
                this.visibilityTrackers = givenConfigs.visibility;
            }
        }

        this.prepareCrea(environmentVars);
        this.loadLib();
    };

    NonLinearAd.prototype.prepareCrea = function (environmentVars) {
        if (window.document.body !== null) {
            window.document.body.style.margin = 0;
            window.document.body.style.border = 0;
            window.document.body.style.padding = 0;
            window.document.body.style.background = "transparent";
        }
        
        this._videoSlot = environmentVars.videoSlot;
        if(environmentVars.p2s && environmentVars.s2p && environmentVars.delegate){
            this.p2s = environmentVars.p2s;
            this.s2p = environmentVars.s2p;
            this.delegate = environmentVars.delegate;
            this.delegateClassname = this.delegate.constructor.name;
        } else if (typeof environmentVars.slot !== "undefined" && environmentVars.slot !== null && 
                environmentVars.slot.p2s && environmentVars.slot.s2p && environmentVars.slot.delegate) {
            this.p2s = environmentVars.slot.p2s;
            this.s2p = environmentVars.slot.s2p;
            this.delegate = environmentVars.slot.delegate;
            this.delegateClassname = this.delegate.constructor.name;
        }
        
        if(environmentVars.domain){
            this.domain = environmentVars.domain;
        } else if (typeof environmentVars.slot !== "undefined" && environmentVars.slot !== null && 
                environmentVars.slot.domain) {
            this.domain = environmentVars.slot.domain;
        }
        
        if (typeof environmentVars.slot !== "undefined" && environmentVars.slot !== null) {
            this._slot = environmentVars.slot;
        } else {
            if (typeof (this._videoSlot.getContainer) == "function" && this._videoSlot.getContainer() !== null) {
                this._slot = this._videoSlot.getContainer();
            } else {
                this._slot = this._videoSlot;
            }
        }      
        //Wibbitz hack
        try{
            var parentVideoSlot = window.frameElement ;
            while(parentVideoSlot.getElementsByClassName("sbt-placeholder").length < 1 && parentVideoSlot.parentNode !== null) {
                parentVideoSlot = parentVideoSlot.parentNode;
            }
            if (parentVideoSlot.getElementsByClassName("sbt-placeholder").length > 0) {
                var container = parentVideoSlot.getElementsByClassName("sbt-placeholder")[0];     
//                console.log("sbt-placeholder found", container); 
                if (container.getElementsByTagName("video").length>0) {
                    this._videoSlot = container.getElementsByTagName("video")[0];
//                    console.log("_videoSlot found", this._videoSlot);
                    this._slot = this._videoSlot; 
                }
            }
        } catch (e) {
//            console.log("sbt-placeholder not found");
        }
        //end Wibbitz hack
        //6play hack
        try {
            if(location.hostname.match('6play.fr')){                       
                this._videoSlot = this._videoSlot.ownerDocument.getElementsByTagName('video')[0];        
                this._slot = this._videoSlot; 
            }                   
        } catch (e) {
            console.log("initAd 6play failed");
        }
        //end 6play hack
        //developer.jwplayer.com hack
        try {
            if(location.hostname.match('developer.jwplayer.com')){     
                var iframes = this._videoSlot.offsetParent.getElementsByClassName('jw-vpaid-iframe');
                if(iframes.length>0) {
                    iframes[0].style.setProperty("width", "100%", "important");
                    iframes[0].style.setProperty("height", "100%", "important");
                    const ro = new ResizeObserver(entries => {
                        iframes[0].style.setProperty("width", "100%", "important");
                        iframes[0].style.setProperty("height", "100%", "important");
                      });
                    ro.observe(iframes[0]);
                }
            }                   
        } catch (e) {
            console.log("initAd developer.jwplayer.com failed");
        }

        //potin hack
        try {
            var parentWindow = window;
            while ((parentWindow.document.getElementsByClassName('unmute_mobile_limitation').length<1 && 
                    parentWindow.document.getElementById('unmute_mobile_limitation') === null)
                && parentWindow !== parentWindow.parent)
                parentWindow = parentWindow.parent;
            if(parentWindow.document.getElementsByClassName('unmute_mobile_limitation').length>0) {
                var muteDiv = parentWindow.document.getElementsByClassName('unmute_mobile_limitation')[0];  
                muteDiv.style.setProperty("display", "none", "important");
            }
            if(parentWindow.document.getElementById('unmute_mobile_limitation') !== null) {
                var muteDiv = parentWindow.document.getElementById('unmute_mobile_limitation');  
                muteDiv.style.setProperty("display", "none", "important");
            }
        } catch (e) {
            console.log("initAd potin failed");
        }
        //end potin hack
    };

    NonLinearAd.prototype.loadLib = function () {
//        console.log("adwdebug : loadLib");
        scwWindow = window;
        if (loaderWindow !== null) {
            scwWindow = loaderWindow;
        }
        if ((typeof scwWindow.adways.scw === "undefined") && adwaysLibScriptTag !== null) {
//            console.log("adwdebug : loadLib adways");
            htmlAddEventListener(adwaysLibScriptTag, "load", this.adwaysLibScriptTagLoadCb);
        } else {
            adwaysLibLoaded = true;
        }
        
        analyticsWindow = window;
        if (loaderWindow !== null) {
            analyticsWindow = loaderWindow;
        }
        if (typeof analyticsWindow.adways === "undefined" || typeof analyticsWindow.adways.analytics === "undefined") {
//            console.log("adwdebug : loadLib analytics");
            htmlAddEventListener(analyticsScriptTag, "load", this.analyticsScriptTagLoadCb);
        } else {
            analyticsLibLoaded = true;
            var domain = (this.domain!==null)?this.domain:"";
            this.tracker = new analyticsWindow.adways.analytics.Tracker({
                record_interface: "generic",
                creative_format: "WMqd0rN",
                creative_id: "70DQ1KN",
                x_domain: domain,
                random_number: function () {
                    return Math.random();
                }
            });
        }
        this.loadAd();
    };

    NonLinearAd.prototype.loadAd = function () {
        if (loaderWindow !== null) {
            window.adways = loaderWindow.adways;
        }
//        console.log("adwdebug : this.adwaysLibLoaded", adwaysLibLoaded, "this.analyticsLibLoaded", analyticsLibLoaded);
        if (adwaysLibLoaded && analyticsLibLoaded) {
            var iab = this.iab;
            if (iab !== '')
                this.tracker.sendData({event_type: "iab", event_name: "loadAd", event_data: iab});

//            console.log("adwdebug : loadAd libs charges");
            if(this.s2p == null || this.p2s == null || this.delegate == null){
                this.s2p = new adways.interactive.SceneControllerWrapper();
                this.p2s = new adways.interactive.SceneControllerWrapper();
                this.resizeAd(this.width, this.height, this.viewMode);
                var playerDetector = new adways.playerHelpers.PlayerDetector();
                var playerDetectorRes = playerDetector.playerClassFromPlayerAPI(this._videoSlot);
                if (playerDetectorRes === "noplayer") {
    //                playerDetectorRes = "noplayer";
                    this._videoSlot = new Object();
                    this._videoSlot.overlay = this._slot;
                    this.delegateUrl = "https://play.adpaths.com/libs/delegates/noplayer.js";
                    this.delegateClassname = "NoPlayerDelegate";
                    this.buildDelegate();
                } else {
                    this.requestPlayerClassFromJSConstant(playerDetectorRes);
                }
            }else{
                this.delegateScriptTagLoadCb();
            }
        }
    };
//----------------------------------------------------------------------------------------------------------------------------------------------------
    NonLinearAd.prototype.requestPlayerClassFromJSConstant = function (playerDetectorRes) {
//        console.log("adwdebug : requestPlayerClassFromJSConstant");
        if (playerDetectorRes === "")
            return -1;
//        var playerClassGetURL = "https://d1afeohcmx2qm4.cloudfront.net/player-class?filter-js_constant=" + playerDetectorRes.toUpperCase();
        var playerClassGetURL = "https://d1afeohcmx2qm4.cloudfront.net/player-class?filter-js_constant=" + playerDetectorRes.toUpperCase();

        if (typeof (adways.tweaks.forceProtocol) !== "undefined") {
            if (playerClassGetURL.search(/^http[s]?\:\/\//) === -1) {
                playerClassGetURL = adways.tweaks.forceProtocol + ":" + playerClassGetURL;
            }
        }

        var playerClassRequest = new adways.ajax.Request();
        playerClassRequest.setURL(playerClassGetURL);
        playerClassRequest.setMethod("GET");
        playerClassRequest.addHeader("Accept", "application/json");
        playerClassRequest.setContentType("application/json");
        var that = this;
        var requestDoneListener = function (evt) {
            if (playerClassRequest !== null && playerClassRequest.getState() === adways.ajax.states.DONE) {
                playerClassRequest.removeEventListener(adways.ajax.events.STATE_CHANGED, requestDoneListener);
                var responseText = playerClassRequest.getResponseText();
                playerClassRequest = null;
                var responseParsed = null;
                responseParsed = JSON.parse(responseText);
                if (responseParsed["_embedded"] && responseParsed["_embedded"]["collection"]
                    && responseParsed["_embedded"]["collection"][0]) {
                    that.delegateUrl = responseParsed["_embedded"]["collection"][0]["delegate_url"];
                    if (!that.delegateUrl.match("^\/[\/\/]+")) {
                        if (that.delegateUrl[0] === "/") {
                            that.delegateUrl = that.delegateUrl.substr(1, that.delegateUrl.length);
                        }
                        that.delegateUrl = "https://play.adpaths.com/" + that.delegateUrl;
                    }

                    that.delegateClassname = responseParsed["_embedded"]["collection"][0]["delegate_classname"];
                    that.buildDelegate();
                }
            }
        };
        playerClassRequest.addEventListener(adways.ajax.events.STATE_CHANGED, requestDoneListener);
        playerClassRequest.load();
        return 1;
    };

//----------------------------------------------------------------------------------------------------------------------------------------------------
    NonLinearAd.prototype.buildDelegate = function () {
//        console.log("adwdebug : buildDelegate");
        delegateScriptTag = document.createElement("script");
        if (typeof (adways.tweaks.isIE) === "number" && adways.tweaks.isIE <= 8)
            delegateScriptTag.type = "text/javascript";
        else
            delegateScriptTag.type = "application/javascript";
        var delegateScriptTagSrc = this.delegateUrl;
        if (typeof (adways.tweaks.forceProtocol) !== "undefined") {
            if (delegateScriptTagSrc.search(/^http[s]?\:\/\//) === -1) {
                delegateScriptTagSrc = adways.tweaks.forceProtocol + ":" + delegateScriptTagSrc;
            }
        }
        delegateScriptTag.src = delegateScriptTagSrc;
        adways.misc.html.addEventListener(delegateScriptTag, "load", this.delegateScriptTagLoadCb);
        document.getElementsByTagName("head")[0].appendChild(delegateScriptTag);
        return 1;
    };

    NonLinearAd.prototype.init = function () {
       // console.log("adwdebug : init");
        this.initBegin();
        this.initEnd();
//        var boxElement = this.fiframe;
//        this.trackVisibility(boxElement, false, null);
        this.checkVisibility();
    };
    
    NonLinearAd.prototype.getVisibilityElement = function () {
        return this.fiframe;
    };

    NonLinearAd.prototype.checkVisibility = function () {
//        console.log("adwDebug checkVisibility");
        var boxElement = this.getVisibilityElement();
        
		var that = this;
		boxElement.ownerDocument.defaultView.visibilityScriptArguments = new Object();         
		boxElement.ownerDocument.defaultView.visibilityScriptArguments.cb = function() {
            if(!that.visibilityIABSent) {
                that.visibilityIABSent = true;
                that.tracker.sendData({event_type: "state", event_name: "visible_IAB"});
            }
		}
		boxElement.ownerDocument.defaultView.visibilityScriptArguments.boxElement = boxElement;
        this.visibilityTrackers.push("https://contents.adpaths.com/v3/tools/Vpaid/adwVisibility.js")
        
        if(that.templateConfig.display_tracking && that.templateConfig.display_tracking.display_tracking_visibility) {
            for(var id in that.templateConfig.display_tracking.display_tracking_visibility) {
                var display_tracking_visibility = that.templateConfig.display_tracking.display_tracking_visibility[id];
                if( typeof display_tracking_visibility === 'string') {
                    this.visibilityTrackers.push(display_tracking_visibility)
                }
            }
        }
        
        for(var i=0; i<this.visibilityTrackers.length; i++) {
            var visibilityTrackerTag = document.createElement("script");
            visibilityTrackerTag.type = "text/javascript";
            visibilityTrackerTag.src = this.visibilityTrackers[i];
            if(boxElement.tagName.toLowerCase() == "iframe") {
                var iframeDoc = boxElement.contentDocument ? boxElement.contentDocument : (boxElement.contentWindow ? boxElement.contentWindow.document : boxElement.document);
                if (iframeDoc.head !== null && 0) {
//                    console.log("adwDebug checkVisibility iframeDoc.head", iframeDoc);
                    iframeDoc.head.appendChild(visibilityTrackerTag);
                } else if (iframeDoc.body) {
//                    console.log("adwDebug checkVisibility iframeDoc.body", iframeDoc);
                    iframeDoc.body.appendChild(visibilityTrackerTag);
                }
            } else {
//                console.log("adwDebug checkVisibility boxElement", boxElement);
                boxElement.appendChild(visibilityTrackerTag);                
            }
        }   
        //this.trackVisibility(boxElement, false, null);
    };

    NonLinearAd.prototype.initBegin = function () {
        var that = this;
        this.layer = new adways.interactive.Layer(adways.hv.layerIds.HOTSPOT);
        if (this._slot == this._videoSlot) {
//            this.s2p.pause(true);
            this.s2p.layerAdded(this.layer);
            var adContainer = this.layer.getDomElement();
            this._slot = adContainer;
//            adContainer.style.position = "";
        }
        this.buildFiFrame();
        this.updatePosition();
    };    
    NonLinearAd.prototype.waitForInitialisation = function () {
        // console.log('waitForInitialisation');
        // TAG JS EXCEPTION
                        this.minTime = this.p2s.getCurrentTime().valueOf();
            if (this.delegateClassname === "NoPlayerDelegate") {
                this.minTime = 0;
            }
            this.duration = 15;
            this.maxTime = this.minTime + this.duration;
            if (this.delegateClassname === "NoPlayerDelegate") {
            this.p2s.setCurrentTime(0, true);
            var that = this;
			if(this.minTime>0) {
				setTimeout(function() {
						that.p2s.setCurrentTime(that.minTime+0.1, true);
					}, that.minTime*1000
                );
            }
        }
        this.waitForInitCurrentTimeChangedCb();
        this.p2s.addEventListener(window.adways.resource.events.CURRENT_TIME_CHANGED, this.waitForInitCurrentTimeChangedCb, this);
    };

    NonLinearAd.prototype.initEnd = function () {
                this.minTime = this.p2s.getCurrentTime().valueOf();
            if (this.delegateClassname === "NoPlayerDelegate") {
                this.minTime = 0;
            }
            this.duration = 15;
            this.maxTime = this.minTime + this.duration;
    //                        console.log("adwDebug : initEnd", this.minTime, this.duration, this.maxTime);
            var that = this;
        this.p2s.addEventListener(window.adways.resource.events.STREAM_SIZE_CHANGED, this.updatePosition, this);
        this.p2s.addEventListener(window.adways.resource.events.RENDER_SIZE_CHANGED, this.updatePosition, this);
        this.p2s.addEventListener(window.adways.resource.events.PLAYER_SIZE_CHANGED, this.updatePosition, this);
        this.p2s.addEventListener(window.adways.resource.events.RENDER_TRANSFORM_CHANGED, this.updatePosition, this);
        this.p2s.addEventListener(window.adways.resource.events.STREAM_TRANSFORM_CHANGED, this.updatePosition, this);
        this.p2s.addEventListener(window.adways.resource.events.PLAY_STATE_CHANGED, this.playStateChangedCb, this);
        this.p2s.addEventListener(window.adways.resource.events.VOLUME_CHANGED, this.volumeChangedCb, this);
        this.p2s.addEventListener(window.adways.resource.events.MUTED_CHANGED, this.muteChangedCb, this);
        this.p2s.addEventListener(window.adways.resource.events.FULLSCREEN_CHANGED, this.fullscreenChangedCb, this);
        this.dispatchEvent("AdLoaded");
        this.currentTimeChangedCb();
        this.p2s.addEventListener(window.adways.resource.events.CURRENT_TIME_CHANGED, this.currentTimeChangedCb, this);
//        this.tracker.sendData({event_type: "state", event_name: "loaded"});
    };

    NonLinearAd.prototype.setPlayState = function () {
        if (arguments[0] === window.adways.resource.playStates.PLAYING) {
            this.dispatchAdStarted();
            this.dispatchEvent("AdPlaying");
//            this.tracker.sendData({event_type: "state", event_name: "play"});
        } else if (arguments[0] === window.adways.resource.playStates.PAUSED) {
//            this.tracker.sendData({event_type: "state", event_name: "pause"});
            this.dispatchEvent("AdPaused");
            var currentTime = this.getCurrentTime() - this.minTime;
            if (this.duration !== 0 && (currentTime + 0.75) >= this.duration) {
                this.finalStopCb();
            }
        }
    };

    NonLinearAd.prototype.finalStopCb = function () {
        if (this.finalStop)
            return;
        this.finalStop = true;
        var that = this;
        if(that.templateConfig.timing_group && that.templateConfig.timing_group.useVisibiltyQuartile)
            isInViewport(this.getVisibilityElement(), function() {that.tracker.sendData({event_type: "visible", event_name: "completion", event_data: 100, event_unit: "%"});});
//        var stopCb = function () {
        var that2 = that;
        var destroyCb = function() {
                that2.dispatchEvent("AdVideoComplete");
                that2.dispatchEvent("AdStopped");
        };
        var completionAndStopCb = function () {
            that2.destroy(destroyCb);
        };
        that.tracker.sendData({event_type: "completion", event_name: "70DQ1KN", event_data: 100, event_unit: "%", cbFunction: completionAndStopCb});
//        };
//        this.tracker.sendData({event_type: "state", event_name: "stop", cbFunction: stopCb});
    };
    NonLinearAd.prototype.durationChangedCb = function () {
        var newDuration = this.p2s.getDuration();
        if (newDuration !== 0 && this.duration !== newDuration) {
            if ("instant" === "always") {
                this.duration = this.p2s.getDuration();
                this.dispatchEvent("AdDurationChange", this.duration);
            } else if ("instant" === "%") {
                var estimatedDuration = this.p2s.getDuration();
                if (this.delegateClassname === "NoPlayerDelegate") {
                    estimatedDuration = '0';
                }
                this.minTime = Math.max('0', 0);
                this.maxTime = '15';
                this.minTime *= estimatedDuration;
                this.maxTime *= estimatedDuration;
                this.duration = this.maxTime - this.minTime;
            }
        }
    };
    NonLinearAd.prototype.fullscreenChangedCb = function (e) {
        if (this.p2s.getFullscreen().valueOf()) {
            this.tracker.sendData({event_type: "state", event_name: "enterFullscreen"});
            this.dispatchEvent("AdEnterFullscreen", this.duration);
        } else {
            this.tracker.sendData({event_type: "state", event_name: "exitFullscreen"});
            this.dispatchEvent("AdExitFullscreen", this.duration);
        }
    };

    NonLinearAd.prototype.updatePosition = function () {
        if (!this.fiframe)
            return;
        var playerSize = this.p2s.getPlayerSize().valueOf(); //[width, height] en pixel
        var renderTransform = this.p2s.getRenderTransform().valueOf(); //[1,0,0,1,left,top] en pixel
        var streamTransform = this.p2s.getStreamTransform().valueOf(); //[1,0,0,1,left,top] en pixel
        var origin = new Array(renderTransform[4] + streamTransform[4], renderTransform[5] + streamTransform[5]);
        var streamSize = this.p2s.getStreamSize().valueOf();

        var position = new Array(0, 0);

        var width = playerSize[0];
        var height = playerSize[1];

        this.fiframe.style.setProperty("width", width + "px", "important");
        this.fiframe.style.setProperty("height", height + "px", "important");
        this.fiframe.style.setProperty("position", "absolute", "important");


        this.fiframe.style.setProperty("left", "0px", "important");
        this.fiframe.style.setProperty("top", "0px", "important");

        this.fiframe.style.setProperty("right", "0px", "important");
        this.fiframe.style.setProperty("bottom", "0px", "important");
//        this.fiframe.id = "vpaidIframe";
    };
    
    NonLinearAd.prototype.getCurrentTime = function () {
        var currentTime = this.p2s.getCurrentTime().valueOf();
        if ("instant" == "instant" && this.instantTimer !== null) { 
            currentTime = this.instantCurrentTime;
        }
        return currentTime;
    }
    NonLinearAd.prototype.dispatchAdImpression = function () {
        this.adImpressionDispatched = true;
        this.dispatchAdStarted();
        this.dispatchEvent("AdImpression");  
        if(this.templateConfig.display_tracking && this.templateConfig.display_tracking.display_tracking_impression) {
            for(var id in this.templateConfig.display_tracking.display_tracking_impression) {
                var display_tracking_impression = this.templateConfig.display_tracking.display_tracking_impression[id];
                if(typeof display_tracking_impression === 'string') {
                    (new Image(0, 0)).src = display_tracking_impression;
                }
            }
        }        
        this.tracker.sendData({event_type: "state", event_name: "impression"});
        var iab = this.iab;
        if (iab !== '')
            this.tracker.sendData({event_type: "iab", event_name: "impression", event_data: iab});
        if (typeof (this.nativeObjectDescription) !== "undefined" && this.nativeObjectDescription != null && typeof (this.nativeObjectDescription.impression) !== "undefined") {
            var impressionPixels = this.nativeObjectDescription.impression;
            for (var i = 0; i < impressionPixels.length; i++) {
                var impressionPixel = impressionPixels[i];
                if (impressionPixel.url && typeof impressionPixel.url == "string") {
                    (new Image(0, 0)).src = impressionPixel.url;
                } else if (typeof impressionPixel == "string") {
                    (new Image(0, 0)).src = impressionPixel;
                }
            }
        }
        if (typeof (this.nativeObjectDescription) !== "undefined" && this.nativeObjectDescription != null && typeof (this.nativeObjectDescription.impressionXHR) !== "undefined") {
            var impressionsXHR = this.nativeObjectDescription.impressionXHR;
            for (var i = 0; i < impressionsXHR.length; i++) {
                sendXHRRequest(impressionsXHR[i]);
            }
        }
    };
    
    NonLinearAd.prototype.currentTimeChangedCb = function () {
        var that = this;
        var currentTime = this.getCurrentTime();
//        console.log("adwDebug : currentTimeChangedCb currentTime", currentTime);
        var relativeCurrentTime = currentTime;        
                        
        if (isNaN(this.minTime)) {
            this.minTime = this.p2s.getCurrentTime().valueOf();
        }
         
        if (isNaN(this.minTime) || this.minTime > currentTime) {
//             console.log("adwDebug ", this.minTime, currentTime);
            this.fiframe.style.display = "none";
            return;
        }
        else {
            relativeCurrentTime = currentTime - this.minTime;
        }

        var newRemainingTime = parseFloat((this.duration - relativeCurrentTime).toFixed(2));
        if (this.duration !== -2 && !isNaN(newRemainingTime) && newRemainingTime !== this.remainingTime) {
            this.remainingTime = newRemainingTime;
            this.dispatchEvent("AdRemainingTimeChange");
            if ("instant" !== "always" && "instant" !== "never"
                && "instant" !== "instant") {
                var typeTime = "instant";
                var time_start = '0';
                var time_end = '15';
                if (typeTime === "%") {
                    var estimatedDuration = this.p2s.getDuration();
                    if (this.delegateClassname === "NoPlayerDelegate") {
                        estimatedDuration = 60;
                    }
                    time_start *= estimatedDuration;
                    time_end *= estimatedDuration;
                }
                var insideValues = false;
                if (this.templateConfig && this.templateConfig.timing_group.timing_cta_select == "interval-s") {
                    for (var i = 0; i < this.templateConfig.timing_group.interval_number; i++) {
                        var interval = this.templateConfig.timing_group['interval_unit_' + i];
                        var iBegin = interval['time_start_second_' + i];
                        var iEnd = interval['time_end_second_' + i];
                        insideValues = (currentTime > iBegin && currentTime < iEnd);
                        if (insideValues) {
                            if (!this.expandedState) {
                                this.expandAd();
                            }
                            break;
                        }
                    }
                    if (!insideValues && this.expandedState) {
                        this.collapseAd();
                    }
                } else {
                    insideValues = (currentTime > time_start && currentTime < time_end);
                }
                if (insideValues) {
                    this.fiframe.style.display = "block";
                    if (!this.adImpressionDispatched) {
                        this.updatePosition();
                        this.dispatchAdImpression();
                        
                        if (this.sizeWatcherTimer === null && this.delegateClassname === "NoPlayerDelegate")
                            this.sizeWatcherTimer = setInterval(this.sizeWatcherListener, 250);
                    }
                } else {
                    // console.log("else");
                    this.fiframe.style.display = "none";
                }
            } else if ("instant" == "always"
                || "instant" == "instant") {
                if (!this.adImpressionDispatched) {
                    this.fiframe.style.display = "block";
                    this.updatePosition();
                    this.dispatchAdImpression();                
                    if (this.delegateClassname !== "NoPlayerDelegate" && "instant" == "instant") {
                        this.minTime = 0;
                        this.duration = 15;
                        var playPauseState = false;
                        if (this.templateConfig && this.templateConfig.timing_group.usePlayState) {
                            playPauseState = true;
                        }
                        this.maxTime = this.minTime + this.duration;
                        this.instantTimerCB = function () {
                            if(playPauseState) {
                                if(that.p2s.getPlayState().valueOf() === window.adways.resource.playStates.PAUSED) {
                                    return;
                                }
                            }
                            that.instantCurrentTime += that.instantIntervalTime / 1000;
//                            console.log("instantTimerCB", that.instantCurrentTime);
                            that.currentTimeChangedCb();
                            if (that.instantCurrentTime >= that.duration) {	
                                that.instantCurrentTime = that.duration;	
                                if(that.instantTimer !== null) {
                                    clearInterval(that.instantTimer);
                                    that.instantTimer = null;
                                }
                            }
                        };
                        this.p2s.removeEventListener(window.adways.resource.events.CURRENT_TIME_CHANGED, this.currentTimeChangedCb, this);
                        this.instantTimer = setInterval(this.instantTimerCB, this.instantIntervalTime);
//                        if (this.customDurationTimer === null)
//                            this.customDurationTimer = setTimeout(this.customDurationTimerListener, 15 * 1000);
                    }

                    if (this.sizeWatcherTimer === null && this.delegateClassname === "NoPlayerDelegate")
                        this.sizeWatcherTimer = setInterval(this.sizeWatcherListener, 250);                    
                }
            }

            if (typeof this.tracker != "undefined" && !isNaN(this.duration)) { // envoi completion et trueview
                if (relativeCurrentTime >= 0.1 && !this.quartiles.zero) {
//                     console.log("started");
                    this.completionValue = 0;
                    this.tracker.sendData({event_type: "completion", event_name: "70DQ1KN", event_data: this.completionValue, event_unit: "%"});
                    this.quartiles.zero = true;
                    this.dispatchEvent("AdVideoStart");
                    if(that.templateConfig.timing_group && that.templateConfig.timing_group.useVisibiltyQuartile)
                        isInViewport(this.getVisibilityElement(), function() {that.tracker.sendData({event_type: "visible", event_name: "completion", event_data: that.completionValue, event_unit: "%"});});
                }
                if (relativeCurrentTime >= (this.duration * 0.25) && !this.quartiles.first) {
                    this.completionValue = 25;
                    this.tracker.sendData({event_type: "completion", event_name: "70DQ1KN", event_data: this.completionValue, event_unit: "%"});
                    this.quartiles.first = true;
                    this.dispatchEvent("AdVideoFirstQuartile");
                    if(that.templateConfig.timing_group && that.templateConfig.timing_group.useVisibiltyQuartile)
                        isInViewport(this.getVisibilityElement(), function() {that.tracker.sendData({event_type: "visible", event_name: "completion", event_data: that.completionValue, event_unit: "%"});});
                }
                if (relativeCurrentTime >= (this.duration * 0.50) && !this.quartiles.mid) {
                    this.completionValue = 50;
                    this.tracker.sendData({event_type: "completion", event_name: "70DQ1KN", event_data: this.completionValue, event_unit: "%"});
                    this.quartiles.mid = true;
                    this.dispatchEvent("AdVideoMidpoint");
                    if(that.templateConfig.timing_group && that.templateConfig.timing_group.useVisibiltyQuartile)
                        isInViewport(this.getVisibilityElement(), function() {that.tracker.sendData({event_type: "visible", event_name: "completion", event_data: that.completionValue, event_unit: "%"});});

                }
                if (relativeCurrentTime >= (this.duration * 0.75) && !this.quartiles.third) {
                    this.completionValue = 75;
                    this.tracker.sendData({event_type: "completion", event_name: "70DQ1KN", event_data: this.completionValue, event_unit: "%"});
                    this.quartiles.third = true;
                    this.dispatchEvent("AdVideoThirdQuartile");
                    if(that.templateConfig.timing_group && that.templateConfig.timing_group.useVisibiltyQuartile)
                        isInViewport(this.getVisibilityElement(), function() {that.tracker.sendData({event_type: "visible", event_name: "completion", event_data: that.completionValue, event_unit: "%"});});
                }
                if ((relativeCurrentTime + 0.75) >= this.duration && !this.quartiles.last) {
                    this.finalStopCb();
                }
                        var trueviewTime = '2';
                    if ("second_tv" === "percentage_tv")
                        trueviewTime *= this.duration;
                    if (relativeCurrentTime >= trueviewTime && !this.trueViewed) {
                        this.trueViewed = true;
                        trueviewTime = Math.floor(trueviewTime);
                        this.tracker.sendData({event_type: "state", event_name: "trueview", event_data: trueviewTime, event_unit: "s"});
                    }
                }
        } else {
            // console.log("autre else", this.duration, newRemainingTime, this.remainingTime);
        }
    };

    NonLinearAd.prototype.waitForInitCurrentTimeChangedCb = function () {
//         console.log("waitForInitCurrentTimeChangedCb");   
        if (this.initReady)
            return;
        var currentTime = this.p2s.getCurrentTime().valueOf();
        var relativeCurrentTime = currentTime;
                        
        if (isNaN(this.minTime)) {
            this.minTime = this.p2s.getCurrentTime().valueOf();
        }
         
        if (isNaN(this.minTime) || this.minTime > currentTime) {
            return;
        }
        else {
            relativeCurrentTime = currentTime - this.minTime;
        }
        var newRemainingTime = parseFloat((this.duration - relativeCurrentTime).toFixed(2));
        if (this.duration !== -2 && !isNaN(newRemainingTime)) {
            if ("instant" !== "always" && "instant" !== "never"
                && "instant" !== "instant") {
                var typeTime = "instant";
                var time_start = '0';
                var time_end = '15';
                if (typeTime === "%") {
                    var estimatedDuration = this.p2s.getDuration();
                    if (this.delegateClassname === "NoPlayerDelegate") {
                        estimatedDuration = 60;
                    }
                    time_start *= estimatedDuration;
                    time_end *= estimatedDuration;
                }
                var insideValues = false;
                if (this.templateConfig && this.templateConfig.timing_group.timing_cta_select == "interval-s") {
                    for (var i = 0; i < this.templateConfig.timing_group.interval_number; i++) {
                        var interval = this.templateConfig.timing_group['interval_unit_' + i];
                        var iBegin = interval['time_start_second_' + i];
                        var iEnd = interval['time_end_second_' + i];
                        insideValues = (currentTime > iBegin && currentTime < iEnd);
                        if (insideValues) {
                            break;
                        }
                    }
                } else {
                    insideValues = (currentTime > time_start && currentTime < time_end);
                }
                if (insideValues) {
                    this.customInit();
                }
            } else if ("instant" == "always" ||
                "instant" == "instant") {
                this.customInit();
            }
        }
    };

    NonLinearAd.prototype.trackVisibility = function(boxElement, trackPlayerVisibility, cb) {  
        var tmpVisible = false;
        try {
            function tryObserve() {   
//                console.log("adwdebug : tryObserve");
                var observer = null;
                var numSteps = 20.0;
                function createObserver() {
                    var options = {
                        root: null,
                        rootMargin: "0px",
                        threshold: buildThresholdList()
//                        threshold:  [0, 0.25, 0.5, 0.75, 1]
                    };
                    observer = new IntersectionObserver(handleIntersect, options);
                    observer.observe(boxElement);
                };
                function buildThresholdList() {
                    var thresholds = [];
                    for (var i = 1.0; i <= numSteps; i++) {
                        var ratio = i / numSteps;
                        thresholds.push(ratio);
                    }
                    thresholds.push(0);
                    return thresholds;
                };
                function handleIntersect(entries, observer) {
                    entries.forEach(function (entry) {
                        var percent = (trackPlayerVisibility) ? that.templateConfig.timing_group.visibilityPercent : 50;
//                         console.log("adwdebug : handleIntersect", entry.intersectionRatio, boxElement);
                        if (entry.intersectionRatio >= percent / 100) {
//                            console.log("adwdebug : visible");
                            if(trackPlayerVisibility) {
                                observer.unobserve(boxElement);
                            } else {
                                // Début tracking durée de visibilité
                                if(!that.isVisible && !tmpVisible) {
                                    tmpVisible = true;
                                    that.visibilityInterval = setInterval(function() {
                                        that.visibleTimer ++;
                                        if(that.visibleTimer >= 2) {
                                            if(!that.visibilityIABSent) {
                                                that.visibilityIABSent = true;
                                                that.tracker.sendData({event_type: "state", event_name: "visible_IAB"});
                                            }
                                            that.isVisible = true;
                                            clearInterval(that.visibilityInterval);
                                            observer.unobserve(boxElement);
                                        }
                                    }, 1000);
                                }
                            }
                            if(cb != null)
                                cb();
                        } else {
//                             console.log('not visible');
                            tmpVisible = false;
                            clearInterval(that.visibilityInterval);
                        }
                    });
                };
                if ((that.templateConfig && that.templateConfig.timing_group.useVisibilty) || !trackPlayerVisibility) {
//                    if (adways.misc.html.userAgent.UA.browser[0].identifier === adways.misc.html.userAgent.FIREFOX) {
//                        if(cb != null)
//                            cb();
//                    } else {
                        createObserver();
//                    }
                } else {
//                    createObserver();
                    if(cb != null)
                        cb();
                }
            }
            if (document.visibilityState == "visible") {
//                console.log("adwdebug : visibilityState visible");
                tryObserve();
            } else {
                function handleVisibilityChange() {
//                    console.log("adwdebug : handleVisibilityChange");
                    if (document.visibilityState == "visible") {
                        htmlRemoveEventListener(document, "visibilitychange", handleVisibilityChange);
                        tryObserve();
                    }
                }
                htmlAddEventListener(document, "visibilitychange", handleVisibilityChange);
            }
        } catch (e) {
            // console.log("adwdebug :customInit catch", e);
            cb();
        }
    };

    NonLinearAd.prototype.customInit = function () {
        var that = this;
//        console.log("adwdebug : customInit");
        this.initReady = true;
        this.p2s.removeEventListener(window.adways.resource.events.CURRENT_TIME_CHANGED, this.waitForInitCurrentTimeChangedCb, this);
        var boxElement = this._slot;
//        if (that.delegateClassname === "VideoJSDelegate") {
//            boxElement = this._slot.el();
//        }        
        if (typeof this.delegate.getVideoElement == "function") {
            boxElement = this.delegate.getVideoElement();
        }         
        var cb = this.customInitOK;

        this.trackVisibility(boxElement, true, cb);
    };

    NonLinearAd.prototype.customInitOK = function () {
//        console.log("adwdebug : customInitOK");
                that.cascadeAdCallInit();    };

    NonLinearAd.prototype.muteChangedCb = function (e) {
        if (this.p2s.getMuted().valueOf()) {
            this.dispatchEvent("AdMuted");
        } else {
            this.dispatchEvent("AdUnmuted");
        }
    };
    NonLinearAd.prototype.volumeChangedCb = function (e) {
        this.dispatchEvent("AdVolumeChange");
    };
    NonLinearAd.prototype.playStateChangedCb = function (e) {
        if (this.videoStarted) {
            playstate = this.p2s.getPlayState().valueOf();
            this.setPlayState(playstate);
        }
    };
    NonLinearAd.prototype.dispatchAdStarted = function () {
//        console.log("adwdebug : dispatchAdStarted");
        if (!this.videoStarted) {
            this.videoStarted = true;
            //        this.dispatchEvent("AdVideoStart");
            this.dispatchEvent("AdStarted");
//            console.log("AdStarted");
//            this.tracker.sendData({event_type: "state", event_name: "start"});
        }
    };

    NonLinearAd.prototype.startAd = function () {
//        this.dispatchEvent("AdStarted");
//        console.log("adwdebug : startAd",(new Date() - startTime) / 1000);
        if (this.delegateClassname === "NoPlayerDelegate") { //hack pour mettre en play le nodelegate
            this.s2p.play(true);
            this.dispatchEvent("AdPlaying");
        }
    };

    NonLinearAd.prototype.getAdLinear = function () {
        return false;
    };
    NonLinearAd.prototype.stopAd = function (e, p) {
//        console.log("stopAd");
        this.destroy();
        this.dispatchEvent("AdStopped");
//        this.tracker.sendData({event_type: "state", event_name: "stop"});
    };
    NonLinearAd.prototype.getAdDuration = function () {
        return this.duration;
    };
    NonLinearAd.prototype.getAdRemainingTime = function () {
        return this.remainingTime;
    };
    NonLinearAd.prototype.setAdVolume = function (val) {
        if (val < 0)
            val = 0;
        if (val > 1)
            val = 1;
        if (this.s2p != null) {
            this.s2p.setVolume(val);
            if (val > 0) {
                this.s2p.unmute(true);
            }
        }
    };
    NonLinearAd.prototype.getAdVolume = function () {
        this.p2s.getVolume().valueOf();
    };
    NonLinearAd.prototype.resizeAd = function (width, height, viewMode) {
//        if (viewMode === "fullscreen" && !this.p2s.getFullscreen().valueOf()) {
//            this.s2p.enterFullscreen(true);
//        } else if (this.p2s.getFullscreen().valueOf()) {
//            this.s2p.exitFullscreen(true);
//        }

        if (this.delegateClassname === "NoPlayerDelegate"
            && this.delegate !== null) {        
            this.delegate.setSizes(width, height); 
        }
        this.updatePosition();
    };
    NonLinearAd.prototype.pauseAd = function () {
        this.s2p.pause(true);
    };
    NonLinearAd.prototype.resumeAd = function () {
        this.s2p.play(true);
    };
    NonLinearAd.prototype.expandAd = function () {
        this.expandedState = true;
        this.tracker.sendData({event_type: "state", event_name: "expandAd"});
        this.dispatchEvent("AdExpandedChange");
//        console.log('expandAd');
    };
    NonLinearAd.prototype.getAdExpanded = function (val) {
        return this.expandedState;
    };
    NonLinearAd.prototype.getAdSkippableState = function (val) {
    };
    NonLinearAd.prototype.collapseAd = function () {
        this.expandedState = false;
        this.tracker.sendData({event_type: "state", event_name: "collapseAd"});
        this.dispatchEvent("AdExpandedChange");
//        console.log('collapseAd');     
    };

    NonLinearAd.prototype.skipAd = function () {
//        this.dispatchEvent("AdSkipped");
        var that = this;
        var destroyCb = function() {
                that.dispatchEvent("AdSkipped");
                that.dispatchEvent("AdStopped");
        };
        var statSentCb = function () {
            that.destroy(destroyCb);
        };
        this.tracker.sendData({event_type: "state", event_name: "skip",
            completion_value: this.completionValue,
            completion_ref: "70DQ1KN", cbFunction: statSentCb});
    };
    NonLinearAd.prototype.handshakeVersion = function (version) {
        return this.VPAIDVersion;
    };
    NonLinearAd.prototype.getAdIcons = function () {
    };
    NonLinearAd.prototype.getAdWidth = function () {
        return (typeof this.fiframe !== "undefined") ? this.fiframe.offsetWidth : 0;
    };
    NonLinearAd.prototype.getAdHeight = function () {
        return (typeof this.fiframe !== "undefined") ? this.fiframe.offsetHeight : 0;
    };
    NonLinearAd.prototype.subscribe = function (fn, evt, inst) {
        if (typeof (this.listeners[evt]) === "undefined")
            this.listeners[evt] = new Array();
        var tmpObj = new Object();
        tmpObj.fcn = fn;
        tmpObj.inst = (arguments.length > 2 ? inst : null);
        this.listeners[evt][this.listeners[evt].length] = tmpObj;
    };
    NonLinearAd.prototype.unsubscribe = function (evt) {
        try {
            if (typeof (this.listeners[evt]) !== "undefined")
                delete this.listeners[evt];
        }
        catch (e) {
            console.warn(e);
        }
    };
    NonLinearAd.prototype.dispatchEvent = function (evt) {
        var args = new Array();
        for (var i = 1; i < arguments.length; i++)
            args.push(arguments[i]);
        if (typeof (this.listeners[evt]) !== "undefined") {
            for (var i = 0; i < this.listeners[evt].length; i++) {
                this.listeners[evt][i].fcn.apply(this.listeners[evt][i].inst, args);
            }
        }
    };

////----------------------------------------------------------------------------------------------------------------------------------------------------
    NonLinearAd.prototype.destroy = function () {        
        if (this.s2p !== null) {
            var layers = this.s2p.layersToArray();
            while(layers.length>0) {
                var layer = layers.pop();
                this.s2p.layerRemoved(layer);
            }
        }
        if(this.instantTimer !== null) {
            clearInterval(this.instantTimer);
        }                                    
//        console.log("adwdebug : destroy");
        if (this.drawerObject)
            this.drawerObject.destroy();

        if (this.sizeWatcherTimer) {
            clearInterval(this.sizeWatcherTimer);
            this.sizeWatcherTimer = null;
        }
        if (this.customDurationTimer) {
            clearInterval(this.customDurationTimer);
            this.customDurationTimer = null;
        }
        //potin hack
        try {
            var parentWindow = window;
            while ((parentWindow.document.getElementsByClassName('unmute_mobile_limitation').length<1 && 
                    parentWindow.document.getElementById('unmute_mobile_limitation') === null)
                && parentWindow !== parentWindow.parent)
                parentWindow = parentWindow.parent;
            if(parentWindow.document.getElementsByClassName('unmute_mobile_limitation').length>0) {
                var muteDiv = parentWindow.document.getElementsByClassName('unmute_mobile_limitation')[0];  
                muteDiv.style.setProperty("display", "block", "important");
            }
            if(parentWindow.document.getElementById('unmute_mobile_limitation') !== null) {
                var muteDiv = parentWindow.document.getElementById('unmute_mobile_limitation');  
                muteDiv.style.setProperty("display", "block", "important");
            }
        } catch (e) {
            console.log("initAd potin failed");
        }
        //end potin hack
        
        if (this.delegate !== null)
            window.adways.destruct(this.delegate);
        if (this.p2s !== null) {
            this.p2s.removeEventListener(window.adways.resource.events.PLAY_STATE_CHANGED, this.playStateChangedCb, this);
            this.p2s.removeEventListener(window.adways.resource.events.VOLUME_CHANGED, this.volumeChangedCb, this);
            this.p2s.removeEventListener(window.adways.resource.events.MUTED_CHANGED, this.muteChangedCb, this);
            this.p2s.removeEventListener(window.adways.resource.events.CURRENT_TIME_CHANGED, this.currentTimeChangedCb, this);
            this.p2s.removeEventListener(window.adways.resource.events.FULLSCREEN_CHANGED, this.fullscreenChangedCb, this);
            if ("instant" === "always" || "instant" === "%") {
                this.p2s.removeEventListener(window.adways.resource.events.DURATION_CHANGED, this.durationChangedCb, this);
            }
            this.p2s.removeEventListener(window.adways.resource.events.STREAM_SIZE_CHANGED, this.updatePosition, this);
            this.p2s.removeEventListener(window.adways.resource.events.RENDER_SIZE_CHANGED, this.updatePosition, this);
            this.p2s.removeEventListener(window.adways.resource.events.PLAYER_SIZE_CHANGED, this.updatePosition, this);
            this.p2s.removeEventListener(window.adways.resource.events.RENDER_TRANSFORM_CHANGED, this.updatePosition, this);
            this.p2s.removeEventListener(window.adways.resource.events.STREAM_TRANSFORM_CHANGED, this.updatePosition, this);
            window.adways.destruct(this.p2s);
        }
        if (this.s2p !== null)
            window.adways.destruct(this.s2p);
        this.delegate = null;
        this.s2p = null;
        this.p2s = null;
        if (this.fiframe !== null && this.fiframe.parentNode !== null)
            this.fiframe.parentNode.removeChild(this.fiframe);
    };
    var loaderWindow = null;
    if (typeof window.adways === "undefined") {
        try {
            var parentWindow = window;
            while (typeof parentWindow.adways === "undefined" && parentWindow !== parentWindow.parent)
                parentWindow = parentWindow.parent;
            if (typeof parentWindow.adways !== "undefined") {
                window.adways = parentWindow.adways;
                loaderWindow = parentWindow;
            }
        } catch (err) {

        }
        if (window.adways === undefined)
            window.adways = new Object();
    }

    var scwWindow = window;
    if (loaderWindow !== null) {
        scwWindow = loaderWindow;
    }
    if (typeof scwWindow.adways.scw === "undefined") {
        adwaysLibScriptTag = scwWindow.document.createElement("script");
        adwaysLibScriptTag.type = "text/javascript";
        adwaysLibScriptTag.src = "https://play.adpaths.com/libs/scw/release.min.js";
        if (scwWindow.document.body !== null) {
            scwWindow.document.body.appendChild(adwaysLibScriptTag);
        } else if (window.document.head !== null) {
            scwWindow.document.head.appendChild(adwaysLibScriptTag);
        }
    }
    
    var analyticsWindow = window;
    if (loaderWindow !== null) {
        analyticsWindow = loaderWindow;
    }
    if (typeof analyticsWindow.adways.analytics === "undefined") {
        analyticsScriptTag = analyticsWindow.document.createElement("script");
        analyticsScriptTag.type = "text/javascript";
        analyticsScriptTag.src = "https://www.adwstats.com/sdk.js";
        if (window.document.body !== null) {
            analyticsWindow.document.body.appendChild(analyticsScriptTag);
        } else if (window.document.head !== null) {
            analyticsWindow.document.head.appendChild(analyticsScriptTag);
        }
    }                                                
    var headHTML = window.document.getElementsByTagName("head")[0].innerHTML;

        

    DrawerNonLinearAd = function() {
        NonLinearAd.apply(this, arguments);    
        this.drawerObject = null;
        this.customConfig = null;
        if(this.vpaidParameters && this.vpaidParameters.customConfig) {
            try {
                this.customConfig = JSON.parse(decodeURIComponent(this.vpaidParameters.customConfig));
            } catch (e) {
                
            }
        }
        var that = this;
        this.clickRedirect = function (link, eventName) {
            var forceRedirect = (arguments.length>2?arguments[2]:false);
            that.dispatchEvent("AdClickThru", link, "", false);
            if (typeof that.tracker != 'undefined') {
                var completion = Math.round((that.p2s.getCurrentTime().valueOf() / that.duration) * 10) * 10;
                var currentSSP = (typeof(that.currentSSP)!= 'undefined'?that.currentSSP:'');
                var redirectURL = that.tracker.getRedirectURL(link, {event_type: 'interaction', event_data: currentSSP, event_name: eventName, completion_value: completion, completion_ref: '70DQ1KN'});
//                console.log("clickRedirect");
                var iab = that.iab;
                if(iab !== '') that.tracker.sendData({event_type: "iab", event_name: "interaction", event_data: iab});
                
                if (forceRedirect) {
//                    window.open(redirectURL, '_blank');
                    document.location.href = redirectURL;
                } else {
                    window.open(redirectURL, '_blank');                    
                }
            } else {
                window.open(link);
            }
        };

        this.clickTracking = function() {
            that.dispatchEvent("AdClickThru", "", "", false);
            // var redirectURL = that.tracker.getRedirectURL(link, {event_type: 'interaction', event_data: currentSSP, event_name: eventName, completion_value: completion, completion_ref: '70DQ1KN'});
            var completion = Math.round((that.p2s.getCurrentTime().valueOf() / that.duration) * 10) * 10;
            var currentSSP = (typeof(that.currentSSP)!= 'undefined'?that.currentSSP:'');
            that.tracker.sendData({event_type: "interaction", event_name: "url", event_data: currentSSP, completion_value: completion, completion_ref: '70DQ1KN'});
//                console.log("clickRedirect");
            var iab = that.iab;
            if(iab !== '') that.tracker.sendData({event_type: "iab", event_name: "interaction", event_data: iab});
        }
    };
    //DrawerNonLinearAd.prototype = new NonLinearAd();
    DrawerNonLinearAd.prototype = Object.create(NonLinearAd.prototype);
    
    DrawerNonLinearAd.prototype.initBegin = function() {
        NonLinearAd.prototype.initBegin.apply(this, arguments); 
        
        DrawerInContent = function(container, nonLinearAd) {
            Drawer.apply(this, arguments);
            this.nonLinearAd = nonLinearAd;
        };
        
        DrawerInContent.prototype = Object.create(Drawer.prototype);
        
        DrawerInContent.prototype.callClickTrackers = function () {
            if (typeof (this.nonLinearAd.nativeObjectDescription) !== "undefined" && typeof (this.nonLinearAd.nativeObjectDescription.clickTrakers) !== "undefined") {
                var clickTrakers = this.nonLinearAd.nativeObjectDescription.clickTrakers;
                for (var i = 0; i < clickTrakers.length; i++) {
                    var clickTraker = clickTrakers[i];
                    if (typeof clickTraker == "string") {                           
                        var imageClick = new Image(1, 1);
                        imageClick.src = clickTraker;
                    }
                }
            }  
            if (typeof (this.nonLinearAd.nativeObjectDescription) !== "undefined" && typeof (this.nonLinearAd.nativeObjectDescription.clickTrakersXHR) !== "undefined") {
                var clickTrakersXHR = this.nonLinearAd.nativeObjectDescription.clickTrakersXHR;
                for (var i = 0; i < clickTrakersXHR.length; i++) {
                    sendXHRRequest(clickTrakersXHR[i]);
                }
            }            
        };
        
        DrawerInContent.prototype.openSponsorURL = function () {
            this.callClickTrackers();
            if (typeof this.nonLinearAd.nativeObjectDescription.advertiser !== 'undefined' && this.nonLinearAd.nativeObjectDescription.advertiser !== null 
                && this.nonLinearAd.nativeObjectDescription.advertiser.logo_click_url !== null && typeof this.nonLinearAd.nativeObjectDescription.advertiser.logo_click_url !== 'undefined'
                && this.nonLinearAd.nativeObjectDescription.advertiser.logo_click_url !== 'native_sponsorUrl') {
                var link = this.nonLinearAd.nativeObjectDescription.advertiser.logo_click_url;
                this.nonLinearAd.clickRedirect(link, "sponsor");
            } else if (typeof this.nonLinearAd.nativeObjectDescription.products !== 'undefined' && this.nonLinearAd.nativeObjectDescription.products.length>0
                && this.nonLinearAd.nativeObjectDescription.products[0].click_url && typeof this.nonLinearAd.nativeObjectDescription.products[0].click_url !== 'undefined') {
                var link = this.nonLinearAd.nativeObjectDescription.products[0].click_url;
                this.nonLinearAd.clickRedirect(link, "sponsor");
            } 
        };        
        
        DrawerInContent.prototype.openProductURL = function () {
            this.callClickTrackers();
            if (typeof this.nonLinearAd.nativeObjectDescription.products !== 'undefined' && this.nonLinearAd.nativeObjectDescription.products.length>0
                && this.nonLinearAd.nativeObjectDescription.products[0].click_url && typeof this.nonLinearAd.nativeObjectDescription.products[0].click_url !== 'undefined') {
                var link = this.nonLinearAd.nativeObjectDescription.products[0].click_url;
                this.nonLinearAd.clickRedirect(link, "url");
            } else if (typeof this.nonLinearAd.nativeObjectDescription.advertiser !== 'undefined' && this.nonLinearAd.nativeObjectDescription.advertiser !== null 
                && this.nonLinearAd.nativeObjectDescription.advertiser.logo_click_url !== null && typeof this.nonLinearAd.nativeObjectDescription.advertiser.logo_click_url !== 'undefined'
                && this.nonLinearAd.nativeObjectDescription.advertiser.logo_click_url !== 'native_sponsorUrl') {
                var link = this.nonLinearAd.nativeObjectDescription.advertiser.logo_click_url;
                this.nonLinearAd.clickRedirect(link, "url");
            }
        };
        
        DrawerInContent.prototype.openPrivacyURL = function () {
            var link = this.privacyURL;
            //var link = 'http://digitaladvertisingalliance.org/';
            if (typeof this.nonLinearAd.tracker != 'undefined') {
                var completion = Math.round((this.nonLinearAd.p2s.getCurrentTime().valueOf() / this.nonLinearAd.duration) * 10) * 10;
                var currentSSP = (typeof(this.nonLinearAd.currentSSP)!= 'undefined'?this.nonLinearAd.currentSSP:'');
                var redirectURL = this.nonLinearAd.tracker.getRedirectURL(link, {event_type: 'state', event_data: currentSSP, event_name: "privacy", completion_value: completion, completion_ref: '70DQ1KN'});          
                window.open(redirectURL, '_blank');           
            } else {
                window.open(link);
            }
        };       
        
        DrawerInContent.prototype.openAdwLink = function () {
            var link = "http://adways.com";
            if (typeof this.nonLinearAd.tracker != 'undefined') {
                var completion = Math.round((this.nonLinearAd.p2s.getCurrentTime().valueOf() / this.nonLinearAd.duration) * 10) * 10;
                var redirectURL = this.nonLinearAd.tracker.getRedirectURL(link, {event_type: 'state', event_name: "adwayscom", completion_value: completion, completion_ref: '70DQ1KN'});          
                window.open(redirectURL, '_blank');           
            } else {
                window.open(link);
            }
        };       

        DrawerInContent.prototype.trackClickLigatus = function () {
            this.nonLinearAd.clickTracking();
        };

        DrawerInContent.prototype.closeBtnAction = function () {
            var that = this;
            var closeCb = function () {
//                that.nonLinearAd.fiframe.style.display = "none";
                that.nonLinearAd.skipAd();
            };
            var completion = Math.round((this.nonLinearAd.p2s.getCurrentTime().valueOf() / this.nonLinearAd.duration) * 10) * 10;
            this.nonLinearAd.tracker.sendData({event_type: 'state', event_name: 'close', completion_value: completion, completion_ref: '70DQ1KN', cbFunction: closeCb});
        };
        
        this.drawerObject = new DrawerInContent({container : this.fiframeDoc.body, p2s : this.p2s, s2p : this.s2p, tracker : this.tracker}, this);
        var that = this;
        var buildHTMLCB = function() {
            that.drawerObject.init(that.nativeObjectDescription);
            that.swipeRightdetect(that.drawerObject.firstContainer, function() {
                that.skipAd();
            });
        }
        this.drawerObject.buildHTML(buildHTMLCB);

//        this.swipeDetect(this.drawerObject.firstContainer, function(swipedir) {
//            console.log(swipedir);
//        });
        /* debug Antvoice */
        if (typeof this.nativeObjectDescription.recoId !== 'undefined' 
            || typeof this.nativeObjectDescription.catalogId !== 'undefined'
            || typeof this.nativeObjectDescription.productId !== 'undefined' ) {

            if (typeof this.nativeObjectDescription.recoId !== 'undefined')
                this.drawerObject.firstContainer.setAttribute("data-sr-recoGuid", this.nativeObjectDescription.recoId);
            if (typeof this.nativeObjectDescription.catalogId !== 'undefined')
                this.drawerObject.firstContainer.setAttribute("data-sr-catalog", this.nativeObjectDescription.catalogId);
            if (typeof this.nativeObjectDescription.productId !== 'undefined')
                this.drawerObject.firstContainer.setAttribute("data-sr-id", this.nativeObjectDescription.productId);
        }
        /* fin debug Antvoice */
    };
    
    DrawerNonLinearAd.prototype.updatePosition = function () {
        if (!this.fiframe)
            return;
        var playerSize = this.p2s.getPlayerSize().valueOf(); //[width, height] en pixel
        var renderSize = this.p2s.getRenderSize().valueOf();
//        console.log("playerSize", playerSize, "renderSize", renderSize, "renderSize", this.p2s.getStreamSize().valueOf());
        var renderTransform = this.p2s.getRenderTransform().valueOf(); //[1,0,0,1,left,top] en pixel
//        var streamTransform = this.p2s.getStreamTransform().valueOf(); //[1,0,0,1,left,top] en pixel
//        var origin = new Array(renderTransform[4] + streamTransform[4], renderTransform[5] + streamTransform[5]);
        var origin = new Array(renderTransform[4], renderTransform[5]);
//        var streamSize = this.p2s.getStreamSize().valueOf();
        var streamSize = renderSize;
        var offset = new Array(10, 10);
        //var position = new Array(0, 0);
//        var position = new Array(0,  offset[1] + 130); 
        var position = new Array(0,  130); 

        var width = streamSize[0] * 0.39;
        var height = streamSize[0] * 0.162513;
//        this.fiframe.style.width = width + "px";
//        this.fiframe.style.height = height + "px";
//        this.fiframe.style.position = "absolute";

        const minWidth = 270;
        const minHeight = 112;
        // TAILLE MINIMUM
        // if(width >= minWidth && height >= minHeight) {
            this.fiframe.style.setProperty("width", width + "px", "important");
            this.fiframe.style.setProperty("height", height + "px", "important");
        // }else{
        //     this.fiframe.style.setProperty("width", minWidth + "px", "important");
        //     this.fiframe.style.setProperty("height", minHeight + "px", "important");
        // }

            this.fiframe.style.setProperty("position", "absolute", "important");
    //        this.fiframe.style.left = (origin[0] - position[0] + streamSize[0] - this.fiframe.offsetWidth) + "px";
    //        this.fiframe.style.top = (origin[1] - position[1] + streamSize[1] - this.fiframe.offsetHeight) + "px";
            this.fiframe.style.setProperty("left", (origin[0] - position[0] + streamSize[0] - this.fiframe.offsetWidth) + "px", "important");
            this.fiframe.style.setProperty("top", (origin[1] - position[1] + streamSize[1] - this.fiframe.offsetHeight) + "px", "important");     
            
            var percentPosition =  false;
            
            if(this.customConfig && this.customConfig.position && this.customConfig.position.top) {
                percentPosition = true;
                position[1] = this.customConfig.position.top;
            }
            if(percentPosition) {
                if(position[1] > 50) {
                    var top = origin[1] + (1 - (position[1] / 100)) * streamSize[1];
                } else if(position[1] === 50){                    
                    var top = origin[1] + (position[1] / 100 * streamSize[1]) - height / 2;
                    this.fiframe.style.setProperty("top", top + "px", "important");
                } else {
                    var top = origin[1] + (1 - (position[1] / 100)) * streamSize[1] - height;
                }
                this.fiframe.style.setProperty("top", top + "px", "important");
            }            

    //        this.fiframe.style.right = "auto";
    //        this.fiframe.style.bottom = "auto";
            this.fiframe.style.setProperty("right", "auto", "important");
            this.fiframe.style.setProperty("bottom", "auto", "important");
            this.fiframe.id = "vpaidIframe";


    };
////----------------------------------------------------------------------------------------------------------------------------------------------------
    DrawerNonLinearAd.prototype.preDestroy = function () {        
        if (this.drawerObject) {
            this.drawerObject.destroy(null, false);
            this.drawerObject = null;
        }
    };
    
    ////----------------------------------------------------------------------------------------------------------------------------------------------------
    DrawerNonLinearAd.prototype.destroy = function (cb) {
        var that = this;
        if (this.drawerObject) {
            this.drawerObject.destroy(function() { // animation terminée, on peut dispatch les events
                if(cb)
                    cb();
                NonLinearAd.prototype.destroy.apply(that, cb);    
            });
        } else {
            if(cb)
                cb();
            NonLinearAd.prototype.destroy.apply(that, cb);  
        }
    };
    
    getVPAIDAd = function () {
        return new DrawerNonLinearAd();
    };

    
const CARD_TRANSITION_DURATION = "500ms";
const TIMEOUT_CHECK_DOM_LIGATUS = 10;

var Drawer = function (config) {
    this.config = config;
    this.ligatusDoc = null;
    this.container = config.container;
    this.closeState = true;
    this.closeTimeout = null;
    this.tracker = typeof config.tracker != 'undefined' ? config.tracker : null;

    this.sponsorURL = null;
    this.productURL = null;
    this.privacyURL = null;

    this.firstContainer = null;
    this.mainContainer = null;
    this.drawerContent = null;
    this.drawer = null;
    this.sponsor = null;
    this.sponsorName = null;
    this.product = null;
    this.productDesc = null;
    this.productCTA = null;    
    this.productDescTitle = null;
    this.pDescTitle = null;

    this.card = null;
    this.imgProductActive = true;
    // Dans la card
    this.productImg = null;
    this.sponsorImg = null;

    this.productTitle = null;
    this.privacyLogo = null;
    this.adwaysLogo = null;
    this.closeElement = null;

    this.pTitle = null;
    this.pDesc = null;
    this.pCTA = null;
    this.pName = null;

    // Gestion ligatus
    this.ligatusTag = null;
    this.ligatusMainContainer = null;
    this.teaser0 = null;
    this.headline0 = null;
    this.captureClickDiv = null;
    this.intervalCheckLigatus = null;
    this.numberCheckLigatus = 0;
    this.adcallBackSent = false;
    this.switchTxt = null;
    this.titleDisplayed = true;

    var that = this;

    this.updateSizePositionCB = function() {
        that.updateSizePosition();
    };

    this.openCloseCB = function () {
        that.openClose();
    };

    this.displayPastilleCB = function() {
        that.openPastille();
    };

    this.openCB = function () {
        that.open();
    };

    this.switchCard = function() {
        // console.log("switchCard");
        if(that.imgProductActive){ // on affiche l'image du produit 
            that.imgProductActive = false;
            adways.misc.html.removeCSSClass(that.productImg, 'hidden');
            adways.misc.html.addCSSClass(that.productImg, 'visible');
        }else{ // on affiche soit le nom de l'annonceur soit le logo de l'annonceur
            that.imgProductActive = true;
            adways.misc.html.removeCSSClass(that.productImg, 'visible');
            adways.misc.html.addCSSClass(that.productImg, 'hidden');
        }
    };
    
    this.closeCB = function () {
        that.close();
    };

    this.prepareCloseCB = function () {
        that.prepareClose();
    };
    this.trackClickLigatusCB = function() {
        that.trackClickLigatus();
    };
    this.openLinkCB = function(e) {
        e.preventDefault();
        e.stopPropagation();
        if(!that.imgProductActive)
            that.openProductURL();
        else
            that.openSponsorURL();
    };
    this.openSponsorURLCB = function (e) {
        e.preventDefault();
        e.stopPropagation();
        that.openSponsorURL();
    };
    this.openProductURLCB = function (e) {
        e.preventDefault();
        e.stopPropagation();
        that.openProductURL();
    };
    this.openPrivacyURLCB = function (e) {
        e.preventDefault();
        e.stopPropagation();
        that.openPrivacyURL();
    };

    this.closeBtnCB = function (e) {
        e.preventDefault();
        e.stopPropagation();
        that.closeBtnAction();
    };

    this.displayLigatusCB = function() {
        that.displayLigatus();
    };

    this.openAdwLinkCB = function(e) {
        e.preventDefault();
        e.stopPropagation();
        that.openAdwLink();
    };

    this.switchTxtFc = function() {
        if(that.titleDisplayed) {
            if(that.ligatusTag != null){
                that.headline0.style.display = 'none';
                that.teaser0.style.display = 'block';
            }else{
                if(that.productDesc.parentNode!==null) {
                    that.productTitle.style.display = 'none';
                    that.productDesc.style.display = 'block';
                }
            }
        }else{
            if(that.ligatusTag != null){
                that.headline0.style.display = 'block';
                that.teaser0.style.display = 'none';
            }else{
                if(that.productDesc.parentNode!==null) {
                    that.productTitle.style.display = 'block';
                    that.productDesc.style.display = 'none';
                }
            }
        }
        that.titleDisplayed = !that.titleDisplayed;
    };

    if(typeof this.config.p2s != 'undefined'){
        this.config.p2s.addEventListener(adways.resource.events.STREAM_SIZE_CHANGED, this.updateSizePositionCB);
        this.config.p2s.addEventListener(adways.resource.events.PLAYER_SIZE_CHANGED, this.updateSizePositionCB);
        this.config.p2s.addEventListener(adways.resource.events.STREAM_TRANSFORM_CHANGED, this.updateSizePositionCB);
        this.updateSizePosition();
    }else {
        window.addEventListener('resize', this.updateSizePositionCB);
    }


};

Drawer.prototype.destroy = function (cb, animation = true) {

    var that = this;
    if(animation) {
        // animation disparition
        adways.misc.html.removeCSSClass(that.drawer, 'open');
        adways.misc.html.addCSSClass(that.drawer, 'close');
        setTimeout(function() {
            adways.misc.html.removeCSSClass(that.mainContainer, 'open');
            adways.misc.html.addCSSClass(that.mainContainer, 'close');
            setTimeout(function() {
                adways.misc.html.removeClickListener(that.card, that.openLinkCB);
                adways.misc.html.removeClickListener(that.drawer, that.openProductURLCB);
                adways.misc.html.removeClickListener(that.productCTA, that.openProductURLCB);
                adways.misc.html.removeClickListener(that.privacyLogo, that.openPrivacyURLCB);
                adways.misc.html.removeClickListener(that.closeElement, that.closeBtnCB);

                try {
                if (that.fiframe !== null && that.fiframe.parentNode !== null)
                    that.fiframe.parentNode.removeChild(that.fiframe);
                } catch(e) {
                    console.log("Drawer.prototype.destroy", e.message);
                }
                delete that.fiframe;
                
                if(cb)
                    cb();
            }, 1000)
        }, 500);
    } else {
        adways.misc.html.removeClickListener(that.card, that.openLinkCB);
        adways.misc.html.removeClickListener(that.drawer, that.openProductURLCB);
        adways.misc.html.removeClickListener(that.productCTA, that.openProductURLCB);
        adways.misc.html.removeClickListener(that.privacyLogo, that.openPrivacyURLCB);
        adways.misc.html.removeClickListener(that.closeElement, that.closeBtnCB);

        try {
        if (that.fiframe !== null && that.fiframe.parentNode !== null)
            that.fiframe.parentNode.removeChild(that.fiframe);
        } catch(e) {
            console.log("Drawer.prototype.destroy", e.message);
        }
        delete that.fiframe;
    }

};

Drawer.prototype.updateSizePosition = function () {
    if(!this.fiframe)
        return;
    var that = this;
    var reduceTxt = function() {
                    var div = that.fiframe.contentDocument.getElementById("card");
            if(that.fiframe.contentDocument.getElementById("sponsor-name")) {
                var p = that.fiframe.contentDocument.getElementById("sponsor-name").children[0];

                p.style.fontSize = '4.5vw';
                // Réduire la taille du texte si besoin
                var fontSize = '4.5';
                while(p.offsetHeight > div.offsetHeight || p.offsetWidth > div.offsetWidth) { 
                    p.style.fontSize = fontSize + 'vw';
                    fontSize -= 0.1;
                }
            }
        
        // var div2 = that.fiframe.contentDocument.getElementById("product-desc");
        // var p2 = that.fiframe.contentDocument.getElementById("product-desc").children[0];
        // p2.style.fontSize = '4.5vw';
        // // Réduire la taille du texte si besoin
        // var fontSize2 = '4.5';
        // console.log('p.offsetHeight', p2.offsetHeight, div2.offsetHeight);
        // while(p2.offsetHeight > div2.offsetHeight || p2.offsetWidth > div2.offsetWidth) { 
        //     p2.style.fontSize = fontSize2 + 'vw';
        //     fontSize2 -= 0.1;
        // }
    }

    var repositionCta = function() {
        var headline = that.ligatusDoc.getElementsByClassName('drawer-headline')[0];
        var cta = that.ligatusDoc.getElementsByClassName('product-cta')[0];
        if(cta)
            cta.style.left = (headline.offsetWidth - cta.offsetWidth) /2 + "px";
    }
    if(this.ligatusTag == null)
        setTimeout(reduceTxt(), 500);
    else
        repositionCta();
};

Drawer.prototype.setContainer = function (container) {
    this.container = container;
};

Drawer.prototype.closeBtnAction = function () {
    this.destroy();
};

Drawer.prototype.openAdwLink = function () {
    window.open('http://adways.com', '_blank');
};

// Drawer.prototype.openClose = function () {
//     if (this.closeState) {
//         this.open();
//     } else {
//         this.close();
//     }
// };

Drawer.prototype.open = function () {
    // Apparition du "tiroir"
    // clearTimeout(this.closeTimeout);
    // this.closeState = false;
    adways.misc.html.removeCSSClass(this.drawer, 'close');
    adways.misc.html.addCSSClass(this.drawer, 'open');
    // adways.misc.html.removeCSSClass(this.drawerContent, 'close');
    // adways.misc.html.addCSSClass(this.drawerContent, 'open');
    
    // adways.misc.html.addCSSClass(this.drawerHandler, 'open');  
    if(this.switchTxt == null)
        this.switchTxt = setInterval(this.switchTxtFc, 3000);
    if("name" == 'name' && (!this.config.advertiser || !this.config.advertiser.description || this.config.advertiser.description == null || this.config.advertiser.description == ''))
        return;
    setInterval(this.switchCard, 3000);
};

Drawer.prototype.openPastille = function () {
    // Apparition de la pastille
    this.container.style.opacity = 1;
    // this.drawer.style.display = 'block';
    adways.misc.html.removeCSSClass(this.mainContainer, 'close');
    adways.misc.html.addCSSClass(this.mainContainer, 'open');
    setTimeout(this.openCB, 1000);
};

Drawer.prototype.displayLigatus = function () {
    // Apparition de la pastille version ligatus (sans le skin pastille dans un 1er temps)
    // Destruction du précédent dom this.container


    this.ligatusTag = window.document.createElement("script");
    this.ligatusTag.src = this.config.ligatusScript;
    this.ligatusTag.type = "application/javascript";

    this.destroy(null, false);

    this.fiframe = this.container.ownerDocument.createElement("iframe");
    this.fiframe.style.border = "0px",
    this.fiframe.style.overflow = "hidden",
    this.fiframe.scrolling = "no";
    this.fiframe.style.position = 'absolute';
    this.fiframe.style.right = '0px';
    this.fiframe.style.left = '0px';
    this.fiframe.style.width = '100%';
    this.fiframe.style.height = '100%';

    var that = this;
    this.fiframe.addEventListener("load", function(e) {
        var a = "<html><head>";
        a += '<link href="https://contents.adpaths.com/v3/pastille/contexts/studio/css/ligatus.css" rel="stylesheet" type="text/css">';
        a += "</head><body></body></html>";

        that.ligatusDoc = that.fiframe.contentDocument ? that.fiframe.contentDocument : (that.fiframe.contentWindow ? that.fiframe.contentWindow.document : that.fiframe.document);
        that.ligatusDoc.open("text/html");
        that.ligatusDoc.write(a);
        that.ligatusDoc.close();

        that.ligatusDoc.body.style.margin = 0;
        that.ligatusDoc.body.style.border = 0;
        that.ligatusDoc.body.style.padding = 0;

        that.mainContainer = document.createElement('div');
        that.mainContainer.id = "adw-main-container";
        that.ligatusDoc.body.appendChild(that.mainContainer);

        // CAPTER le clic
        adways.misc.html.addClickListener(that.mainContainer, that.trackClickLigatusCB, true);

        // Croix de fermeture
        that.closeElement = that.container.ownerDocument.createElement('div');
        that.closeElement.id = 'adw-ligatus-close';
        that.closeElement.style.width = '27px';
        that.closeElement.style.height = '27px';
        that.closeElement.style.display = 'none';
        // that.closeElement.innerHTML = "X";
        that.ligatusDoc.body.appendChild(that.closeElement);
        adways.misc.html.addClickListener(that.closeElement, that.closeBtnCB);

        // Logo adways
        that.adwaysLogo = that.container.ownerDocument.createElement('div');
        that.adwaysLogo.id = 'adways-logo';
        that.ligatusDoc.body.appendChild(that.adwaysLogo);
        // adways.misc.html.addClickListener(that.adwaysLogo, that.openAdwLinkCB);
        adways.misc.html.addClickListener(that.adwaysLogo, that.openProductURLCB);
        

        that.ligatusMainContainer = document.createElement('div');
        that.ligatusMainContainer.id = "adw-ligatus-container";
        that.mainContainer.appendChild(that.ligatusMainContainer);
        
        that.ligatusMainContainer.appendChild(that.ligatusTag);

        var openLigatusPastille = function() {
            that.container.style.opacity = 1;
            var drawer = that.ligatusDoc.getElementById('txt0');
            drawer.style.transitionProperty = "all";
            drawer.style.transitionDuration = "0.5s";    
            that.mainContainer.style.transitionProperty = "all";
            that.mainContainer.style.transitionDuration = "1s";   
            adways.misc.html.addCSSClass(that.mainContainer, 'open');
            setTimeout(function() {
                adways.misc.html.addCSSClass(drawer, 'open');
                that.updateSizePosition();
                if(that.switchTxt == null)
                    that.switchTxt = setInterval(that.switchTxtFc, 3000);
                that.closeElement.style.display = 'block';
            }, 1000);
        }

        var reconstructCSS = function() {
            // Re-CSS pour affichage en mode pastille
            var txt0 = that.ligatusDoc.getElementById('txt0');
            that.teaser0 = that.ligatusDoc.getElementsByClassName('ligatus_cuc_teaser')[0];
            that.headline0 = that.ligatusDoc.getElementsByClassName('ligatus_cuc_headline')[0];
            var cta0 = that.ligatusDoc.getElementsByClassName('ligatus_cuc_cta')[0];
            var img0 = that.ligatusDoc.getElementsByClassName('ligatus_cuc_img')[0];
            // var privacy0 = that.ligatusDoc.getElementById('oba_110825_107067');
            var privacyContent = that.ligatusDoc.getElementsByClassName('lig_innerLayer')[0];

            // console.log('txt0', txt0);
            // console.log('headline0', headline0);
            // console.log('img0', img0);
            // console.log('privacy0', privacy0);

            img0.classList.add("card");
                        img0.style.backgroundSize= "cover";
                        txt0.classList.add("drawer");
            that.headline0.classList.add("drawer-headline");
            that.headline0.style.fontSize = '4.5vw';
            that.headline0.setAttribute("title", that.headline0.innerHTML);
            that.teaser0.style.display = 'none';
            that.teaser0.style.fontSize = '4.5vw'; 
            that.teaser0.classList.add("drawer-teaser");
            that.teaser0.setAttribute("title", that.teaser0.innerHTML);
            if(cta0)
                cta0.classList.add("product-cta");
            privacyContent.classList.add("ligatus-privacy-content");
           
            setTimeout(openLigatusPastille, 500);
        }

        var checkLigatusDOM = function() {
            that.numberCheckLigatus ++;
            var ligatusFrame = that.ligatusDoc.querySelectorAll('[id^=ligatusframe]')[0]; // pas le bon id
            if(typeof ligatusFrame != 'undefined' && ligatusFrame != null){ // Ligatus à été inséré dans le DOM
                clearInterval(that.intervalCheckLigatus); 
                if(that.tracker != null && !that.adcallBackSent){
                    that.adcallBackSent = true;
                    that.tracker.sendData({event_type: "addcallbackOK", event_name: "ligatus"});
                }
                reconstructCSS();
            }else if(that.numberCheckLigatus >= TIMEOUT_CHECK_DOM_LIGATUS){ // Ligatus n'a pas été inséré dans le DOM au bout du timeout, on arrête tout avant qu'il soit trop tard
                clearInterval(that.intervalCheckLigatus);
                that.destroy(null, false);
            }

        };

        that.intervalCheckLigatus = setInterval(checkLigatusDOM, 1000);

    });
   
    this.container.appendChild(this.fiframe);

};


// Drawer.prototype.close = function () {
// //    console.log("close");
//     this.closeState = true;
//     adways.misc.html.addCSSClass(this.drawer, 'close');
//     adways.misc.html.removeCSSClass(this.drawer, 'open');
//     adways.misc.html.addCSSClass(this.drawerContent, 'close');
//     adways.misc.html.removeCSSClass(this.drawerContent, 'open');
    
//     adways.misc.html.removeCSSClass(this.drawerHandler, 'open');   
// };

Drawer.prototype.openSponsorURL = function () {
    window.open(this.sponsorURL, '_blank');
};
Drawer.prototype.openProductURL = function () {
    window.open(this.productURL, '_blank');
};
Drawer.prototype.openPrivacyURL = function () {
    window.open(this.privacyURL, '_blank');
};
Drawer.prototype.trackClickLigatus = function () {
    // window.open(this.productURL, '_blank');
};

Drawer.prototype.buildHTML = function (cb) {
    this.fiframe = this.container.ownerDocument.createElement("iframe");
    this.fiframe.style.border = "0px",
        this.fiframe.style.overflow = "hidden",
        this.fiframe.scrolling = "no";
    this.fiframe.style.position = 'absolute';
    this.fiframe.style.right = '0px';
    this.fiframe.style.left = '0px';
    this.fiframe.style.width = '100%';
    this.fiframe.style.height = '100%';

    this.container.style.opacity = 0;
    this.container.appendChild(this.fiframe);

    var a = "<html><head></head><body></body></html>";
    var doc = this.fiframe.contentDocument ? this.fiframe.contentDocument : (this.fiframe.contentWindow ? this.fiframe.contentWindow.document : this.fiframe.document);
    doc.open("text/html");
    doc.write(a);
    doc.close();

    var cssScriptTag = document.createElement("link");
    cssScriptTag.type = "text/css";
    cssScriptTag.rel = "stylesheet";
    cssScriptTag.href = "https://contents.adpaths.com/v3/pastille/contexts/studio/css/drawer.css";

    var that = this;
    var cssScriptTagLoadCb = function() {
        cssScriptTag.removeEventListener('load', cssScriptTagLoadCb);
        that.firstContainer = that.container.ownerDocument.createElement('div');
        that.firstContainer.id = 'first-container';
    //    that.container.appendChild(that.mainContainer);
        doc.body.appendChild(that.firstContainer);

        that.mainContainer = that.container.ownerDocument.createElement('div');
        that.mainContainer.id = 'main-container';
        adways.misc.html.addCSSClass(that.mainContainer, 'close');
    //    that.container.appendChild(that.mainContainer);
        that.firstContainer.appendChild(that.mainContainer);

        that.drawer = that.container.ownerDocument.createElement('div');
        that.drawer.id = 'drawer';
        // that.drawer.style.display = 'none';
        adways.misc.html.addCSSClass(that.drawer, 'close');
        that.mainContainer.appendChild(that.drawer);

        that.drawerContent = that.container.ownerDocument.createElement('div');
        that.drawerContent.id = 'drawer-content';
        // adways.misc.html.addCSSClass(that.drawerContent, 'close');
        // that.drawer.appendChild(that.drawerContent);


        // that.sponsor = that.container.ownerDocument.createElement('div');
        // that.sponsor.id = 'sponsor';
        // that.drawerContent.appendChild(that.sponsor);

        that.product = that.container.ownerDocument.createElement('div');
        that.product.id = 'product';
        that.drawer.appendChild(that.product);

        // that.drawer.appendChild(that.pName);
        
        that.productDesc = that.container.ownerDocument.createElement('div');
        that.productDesc.id = 'product-desc';
        that.productDesc.style.display = "none"; // pour le switch entre le texte et la description plus tard
        that.pDesc = that.container.ownerDocument.createElement('p');
        that.pDesc.innerHTML = '';
        that.productDesc.appendChild(that.pDesc);

        
        that.productCTA = that.container.ownerDocument.createElement('div');
        that.productCTA.id = 'product-cta';
        that.drawer.appendChild(that.productCTA);
        that.pCTA = that.container.ownerDocument.createElement('p');
        that.pCTA.innerHTML = 'Voir plus';
        if("1")
            adways.misc.html.addCSSClass(that.pCTA, 'scale');
        that.productCTA.appendChild(that.pCTA);

        that.card = that.container.ownerDocument.createElement('div');
        that.card.id = 'card';
        that.mainContainer.appendChild(that.card);



        that.productImg = that.container.ownerDocument.createElement('div');
        that.productImg.id = 'product-img';
        adways.misc.html.addCSSClass(that.drawer, 'visible');
        that.productImg.style.backgroundColor = '#ffffff';
        that.card.appendChild(that.productImg);

        
        that.sponsorMention = that.container.ownerDocument.createElement('div');
        that.sponsorName = that.container.ownerDocument.createElement('div');
        that.sponsorName.id = 'sponsor-name';
        that.pName = that.container.ownerDocument.createElement('p');
        that.pName.innerHTML = 'Sponsor Name';
        that.sponsorName.appendChild(that.pName);
        that.sponsorName.style.color = '#000000';
        that.sponsorName.style.fontSize = '4.5vw';
        that.sponsorName.style.backgroundColor = '#ffffff';
        that.card.appendChild(that.sponsorName);

        
                that.productImg.style.backgroundSize= "cover";
                        if(that.sponsorImg != null)
            that.sponsorImg.style.backgroundSize= "cover";
        
        that.productTitle = that.container.ownerDocument.createElement('div');
        that.productTitle.id = 'product-title';

        that.pTitle = that.container.ownerDocument.createElement('p');
        that.pTitle.innerHTML = '';
        that.productTitle.appendChild(that.pTitle);

        // that.productTitle.style.height A CHANGER POUR LAISSER LE TITRE SUR DEUX LIGNES

        that.pTitle.style.color = '#ffffff';
        that.pTitle.style.fontSize = '4.5vw';

        that.pDesc.style.color = '#ffffff';
        that.pDesc.style.fontSize = '4.5vw';

        that.product.appendChild(that.productTitle);
        that.product.appendChild(that.productDesc);

        that.closeElement = that.container.ownerDocument.createElement('div');
        that.closeElement.id = 'close';
        that.closeElement.style.width = '27px';
        that.closeElement.style.height = '27px';
        that.mainContainer.appendChild(that.closeElement);

        that.privacyLogo = that.container.ownerDocument.createElement('div');
        that.privacyLogo.id = 'privacy-logo';
        that.mainContainer.appendChild(that.privacyLogo);

        that.adwaysLogo = that.container.ownerDocument.createElement('div');
        that.adwaysLogo.id = 'adways-logo';
        that.mainContainer.appendChild(that.adwaysLogo);
        // adways.misc.html.addClickListener(that.adwaysLogo, that.openAdwLinkCB);
        adways.misc.html.addClickListener(that.adwaysLogo, that.openProductURLCB);

        adways.misc.html.addClickListener(that.card, that.openLinkCB);
        adways.misc.html.addEventListener(that.productCTA, "click", that.openProductURLCB);
        adways.misc.html.addClickListener(that.drawer, that.openProductURLCB);
        adways.misc.html.addClickListener(that.privacyLogo, that.openPrivacyURLCB);
        adways.misc.html.addClickListener(that.closeElement, that.closeBtnCB);

        
    //    adways.misc.html.addClickListener(that.card, that.openProductURLCB);   

        if(adways.misc.html.userAgent.UA.browser[0].identifier !== adways.misc.html.userAgent.SAFARI) {
            that.drawer.style.transitionProperty = "all";
            that.drawer.style.transitionDuration = "0.5s";    
            that.mainContainer.style.transitionProperty = "all";
            that.mainContainer.style.transitionDuration = "1s";    
            that.productImg.style.transitionProperty = "all";
            that.productImg.style.transitionDuration = CARD_TRANSITION_DURATION;       
        }

        that.card.style.backgroundColor = '#ffffff';
        
        that.drawerContent.style.backgroundColor = 'rgba(0,0,0,0.3)';
        
        that.productCTA.style.borderColor = '#ffffff';
        that.productCTA.style.borderSize = '2';
        that.productCTA.style.fontSize = '4.3vw';
        that.productCTA.style.bottom = 'calc(-1vw - 2.15vw - 3px)';

        that.closeElement.style.backgroundColor = 'rgba(0,0,0,1)';
        
        var styleElem = that.mainContainer.ownerDocument.head.appendChild(that.mainContainer.ownerDocument.createElement("style"));
        styleElem.innerHTML = "#close:after {background-color: #ffffff}";
        styleElem = that.mainContainer.ownerDocument.head.appendChild(that.mainContainer.ownerDocument.createElement("style"));
        styleElem.innerHTML = "#close:before {background-color: #ffffff}";
            
        // that.drawerHandler.style.backgroundColor = '#000000';
        styleElem = that.mainContainer.ownerDocument.head.appendChild(that.mainContainer.ownerDocument.createElement("style"));
        if(cb)
            cb();
    }
    cssScriptTag.addEventListener('load', cssScriptTagLoadCb);
    doc.head.appendChild(cssScriptTag);

    doc.body.style.margin = 0;
    doc.body.style.border = 0;
    doc.body.style.padding = 0;

//        this.container.appendChild(this.mainContainer);

   
};

Drawer.prototype.openPrivacyURL = function () {
    window.open(this.privacyURL, '_blank');
};

Drawer.prototype.init = function (config) {
    console.log('config',config);
    this.config = config;
    var that = this;
    var gif = null;

    var setConfig = function() {
        if(gif != null)
            gif.style.display = 'none';
        if(typeof config.ligatusScript != 'undefined' && config.ligatusScript != null) {
            setTimeout(that.displayLigatusCB, 100);
        }else{  
            var downloadQueue = [];
            var initDisplayPastille = function() {
                setTimeout(that.displayPastilleCB, 100);
                setTimeout(that.updateSizePositionCB, 500);
            };
            if (config.products && config.products.length > 0) {
                if (that.config.products[0].image && that.config.products[0].image.url){
                    that.productImg.style.backgroundImage = "url(" + that.config.products[0].image.url + ")";
                    downloadQueue.push(that.config.products[0].image.url);
                }
                if (that.config.advertiser && that.config.advertiser.logo && that.config.advertiser.logo.url && that.sponsorImg != null){
                    that.sponsorImg.style.backgroundImage = "url(" + that.config.advertiser.logo.url + ")";
                    downloadQueue.push(that.config.advertiser.logo.url);
                }
        //        if (that.config.privacy && that.config.privacy.output_image_url)
        //            that.privacyLogo.style.backgroundImage = "url(" + that.config.privacy.output_image_url + ")";
                if (that.config.privacy && that.config.privacy.optout_image_url){
                    that.privacyLogo.style.backgroundImage = "url(" + that.config.privacy.optout_image_url + ")";
                    downloadQueue.push(that.config.privacy.optout_image_url);
                }

                if (that.config.advertiser && that.config.advertiser && that.config.advertiser.logo_click_url)
                    that.sponsorURL = that.config.advertiser.logo_click_url;
                if (that.config.products[0].click_url)
                    that.productURL = that.config.products[0].click_url;
        //        if (that.config.privacy && that.config.privacy.output_click_url)
        //            that.privacyURL = that.config.privacy.output_click_url;
                if (that.config.privacy && that.config.privacy.optout_click_url)
                    that.privacyURL = that.config.privacy.optout_click_url;

                if (that.config.products[0].title) {
                    that.pTitle.innerHTML = that.config.products[0].title;
                    that.pTitle.setAttribute('title', that.config.products[0].title);
                }
                if (that.config.products[0].description){
                    that.pDesc.innerHTML = that.config.products[0].description;
                    that.pDesc.setAttribute('title', that.config.products[0].description);
                } else {
                    that.product.removeChild(that.productDesc);
                }
                if (that.config.products[0].call_to_action)
                    that.pCTA.innerHTML = that.config.products[0].call_to_action;
                if (that.config.advertiser && that.config.advertiser.description && that.pName != null) {
                    var sponsorTxt = "";
                                        sponsorTxt += that.config.advertiser.description;
                    that.pName.innerHTML = sponsorTxt;
                    that.pName.setAttribute('title', that.config.advertiser.description);
                }
                var loadAllImages = function(cb) {
                    var imgLoaded = 0;
                    if(downloadQueue.length > 0) {
                        for (var i = 0; i < downloadQueue.length; i++) {
                            var path = downloadQueue[i];
                            var img = new Image();
                            img.addEventListener("load", function() {
                                imgLoaded ++;
                                if(imgLoaded == downloadQueue.length) {
                                    cb();
                                }
                            }, false);
                            img.addEventListener("error", function(e) {
                                console.log('Error loading one image', e);
                                imgLoaded ++;
                                if(imgLoaded == downloadQueue.length) {
                                    cb();
                                }
                            }, false);
                            img.src = path;
                        }
                    }else{
                        cb()
                    }
                };
                loadAllImages(initDisplayPastille);
            }
            else
                return -1;
        }
    }
    // Apparition du splashscreen si configuré dans la créa
            setConfig();
    
};  
document.getElementsByTagName("head")[0].innerHTML = headHTML; 
        if (window.adways === undefined) {
    window.adways = new Object();
}

if (window.adways.playerHelpers === undefined) {
    window.adways.playerHelpers = new Object();
}

window.adways.playerHelpers.PlayerDetector = function() {
    this._detectors = new Array();
    this._detectors.push({
        priority: 100,
        detector: new window.adways.playerHelpers.JWPlayer7Detector()
    });
    this._detectors.push({
        priority: 100,
        detector: new window.adways.playerHelpers.YoutubeDetector()
    });
    this._detectors.push({
        priority: 50,
        detector: new window.adways.playerHelpers.YoutubePMDetector()
    });
    this._detectors.push({
        priority: 50,
        detector: new window.adways.playerHelpers.BrightcoveDetector()
    });
    this._detectors.push({
        priority: 60,
        detector: new window.adways.playerHelpers.JWPlayer6Detector()
    });
    this._detectors.push({
        priority: 50,
        detector: new window.adways.playerHelpers.JWPlayer8Detector()
    });
    this._detectors.push({
        priority: 100,
        detector: new window.adways.playerHelpers.VimeoDetector()
    });
    this._detectors.push({
        priority: 100,
        detector: new window.adways.playerHelpers.DailymotionDetector()
    });
    this._detectors.push({
        priority: 100,
        detector: new window.adways.playerHelpers.DailymotionSDKDetector()
    });
    this._detectors.push({
        priority: 100,
        detector: new window.adways.playerHelpers.DailymotionComDetector()
    });
    this._detectors.push({
        priority: 50,
        detector: new window.adways.playerHelpers.OoyalaDetector()
    });
    this._detectors.push({
        priority: 50,
        detector: new window.adways.playerHelpers.OoyalaV4Detector()
    });
    this._detectors.push({
        priority: 100,
        detector: new window.adways.playerHelpers.BrainsonicDetector()
    });
    this._detectors.push({
        priority: 30,
        detector: new window.adways.playerHelpers.VideoJSDetector()
    });
    this._detectors.push({
        priority: 20,
        detector: new window.adways.playerHelpers.HTML5Detector()
    });
    this._detectors.push({
        priority: 100,
        detector: new window.adways.playerHelpers.FranceTVDetector()
    });
    this._detectors.push({
        priority: 100,
        detector: new window.adways.playerHelpers.thePlatformDetector()
    });
    this._detectors.push({
        priority: 80,
        detector: new window.adways.playerHelpers.ViouslyDetector()
    });
    this._detectors.push({
        priority: 70,
        detector: new window.adways.playerHelpers.ViouslyPlayerDetector()
    });
    this._detectors.push({
        priority: 100,
        detector: new window.adways.playerHelpers.VidazooDetector()
    });
    this._sortDetectors();
};

window.adways.playerHelpers.PlayerDetector.prototype.playerClassFromPlayerAPI = function(playerAPI) {
    var detected = false;
    for (var i = 0; i < this._detectors.length && !(detected = this._detectors[i].detector.detect(playerAPI)); i++)
        ;
    if (detected) {
        return this._detectors[i].detector.getPlayerClass();
    }
    return "noplayer";
};

window.adways.playerHelpers.PlayerDetector.prototype._sortDetectors = function() {
    this._detectors.sort(function(a, b) {
        if (a.priority < b.priority) {
            return 1;
        } else if (a.priority > b.priority) {
            return -1;
        }
        return 0;
    });
};

window.adways.playerHelpers.JWPlayer7Detector = function() {
    this._playerClass = "jwplayer7";
};

window.adways.playerHelpers.JWPlayer7Detector.prototype.getPlayerClass = function() {
    return this._playerClass;
};

window.adways.playerHelpers.JWPlayer7Detector.prototype.detect = function(playerAPI) {
    if (playerAPI.version !== undefined && typeof playerAPI.version !== 'function') {
        var ret = playerAPI.version.match('7\..*\.jwplayer\..*');
        if (ret !== null && ret[0] === playerAPI.version) {
            return true;
        }
    }
    return false;
};

window.adways.playerHelpers.JWPlayer8Detector = function() {
    this._playerClass = "jwplayer8";
};

window.adways.playerHelpers.JWPlayer8Detector.prototype.getPlayerClass = function() {
    return this._playerClass;
};

window.adways.playerHelpers.JWPlayer8Detector.prototype.detect = function(playerAPI) {
    if (playerAPI.version !== undefined && typeof playerAPI.version !== 'function') {
//        console.log("JWPlayer8Detector", playerAPI.version);
        var ret = playerAPI.version.match('8\..*\.jwplayer\..*');
        if (ret !== null && ret[0] === playerAPI.version) {
            return true;
        }
    }
    return false;
};
//

window.adways.playerHelpers.YoutubeDetector = function() {
    this._playerClass = "youtube";
};

window.adways.playerHelpers.YoutubeDetector.prototype.getPlayerClass = function() {
    return this._playerClass;
};

window.adways.playerHelpers.YoutubeDetector.prototype.detect = function(playerAPI) {
    if (playerAPI.getApiInterface !== undefined &&
            playerAPI.getVideoUrl !== undefined && typeof playerAPI.getVideoUrl === 'function' &&
            playerAPI.getVideoEmbedCode !== undefined && typeof playerAPI.getVideoEmbedCode === 'function') {
        var videoUrl = playerAPI.getVideoUrl();
        var videoEmbedCode = playerAPI.getVideoEmbedCode();
        var tmp1 = videoUrl.match('.*youtube.*\/watch?.*');
        var tmp2 = videoEmbedCode.match('.*src=.*www.youtube.com.*');
        if (tmp1 !== null && tmp2 != null &&
                tmp1[0] === videoUrl &&
                tmp2[0] === videoEmbedCode) {
            return true;
        }
    }
    return false;
};

//

window.adways.playerHelpers.YoutubePMDetector = function() {
    this._playerClass = "youtubePM";
};

window.adways.playerHelpers.YoutubePMDetector.prototype.getPlayerClass = function() {
    return this._playerClass;
};

window.adways.playerHelpers.YoutubePMDetector.prototype.detect = function(playerAPI) {
    if (playerAPI.src !== undefined && typeof playerAPI.src !== 'function') {
        var tmp = playerAPI.src.match('.*www.youtube.com/embed.*');
        if (tmp !== null && tmp[0] === playerAPI.src) {
            var tmp2 = playerAPI.src.match('.*enablejsapi.*');
            if (tmp2 !== null) {
                return true;
            }
        }
    }
    return false;
};

//

window.adways.playerHelpers.BrightcoveDetector = function() {
    this._playerClass = "brightcove";
};

window.adways.playerHelpers.BrightcoveDetector.prototype.getPlayerClass = function() {
    return this._playerClass;
};

window.adways.playerHelpers.BrightcoveDetector.prototype.detect = function(playerAPI) {
    if (playerAPI.toJSON !== undefined && typeof playerAPI.toJSON === 'function') {
        var json = playerAPI.toJSON();
        if (json['data-account'] !== undefined &&
                json['data-player'] !== undefined) {
            return true;
        }
    }
    return false;
};

//

window.adways.playerHelpers.JWPlayer6Detector = function() {
    this._playerClass = "jwplayer6";
};

window.adways.playerHelpers.JWPlayer6Detector.prototype.getPlayerClass = function() {
    return this._playerClass;
};

window.adways.playerHelpers.JWPlayer6Detector.prototype.detect = function(playerAPI) {
    if (playerAPI.releaseState !== undefined &&
            playerAPI.setCurrentCaptions !== undefined &&
            playerAPI.registerPlugin !== undefined &&
            playerAPI.loadInstream !== undefined &&
            playerAPI.getLockState !== undefined) {
        return true;
    }
    return false;
};

//

window.adways.playerHelpers.VimeoDetector = function() {
    this._playerClass = "vimeo";
};

window.adways.playerHelpers.VimeoDetector.prototype.getPlayerClass = function() {
    return this._playerClass;
};

window.adways.playerHelpers.VimeoDetector.prototype.detect = function(playerAPI) {
    if (playerAPI.src !== undefined && typeof playerAPI.src !== 'function') {
        var tmp = playerAPI.src.match('.*player.vimeo.com.*');
        if (tmp !== null && tmp[0] === playerAPI.src) {
            return true;
        }
    }
    return false;
};

//

window.adways.playerHelpers.DailymotionDetector = function() {
    this._playerClass = "dailymotion";
};

window.adways.playerHelpers.DailymotionDetector.prototype.getPlayerClass = function() {
    return this._playerClass;
};

window.adways.playerHelpers.DailymotionDetector.prototype.detect = function(playerAPI) {
    if (playerAPI.src !== undefined && typeof playerAPI.src !== 'function' && typeof (playerAPI.apiReady) === "undefined") {
        var tmp = playerAPI.src.match('.*dailymotion.com/embed.*');
        if (tmp !== null && tmp[0] === playerAPI.src) {
            return true;
        }
    }
    return false;
};

window.adways.playerHelpers.DailymotionComDetector = function() {
    this._playerClass = "dailymotionsdk";
};

window.adways.playerHelpers.DailymotionComDetector.prototype.getPlayerClass = function() {
    return this._playerClass;
};

window.adways.playerHelpers.DailymotionComDetector.prototype.detect = function(playerAPI) {
    if (typeof playerAPI.baseURI !== 'undefined') {
        var tmp = playerAPI.baseURI.match('.*dailymotion.com.*');
        if (tmp !== null && playerAPI.getElementsByClassName("dmp_Player").length > 0) {
            return true;
        }
    }
    return false;
};

window.adways.playerHelpers.DailymotionSDKDetector = function() {
    this._playerClass = "dailymotionsdk";
};

window.adways.playerHelpers.DailymotionSDKDetector.prototype.getPlayerClass = function() {
    return this._playerClass;
};

window.adways.playerHelpers.DailymotionSDKDetector.prototype.detect = function(playerAPI) {
    if (playerAPI.src !== undefined && typeof playerAPI.src !== 'function' && typeof (playerAPI.apiReady) !== "undefined") {
        var tmp = playerAPI.src.match('.*dailymotion.com/embed.*');
        if (tmp !== null && tmp[0] === playerAPI.src) {
            return true;
        }
    }
    return false;
};


window.adways.playerHelpers.OoyalaV4Detector = function() {
    this._playerClass = "ooyala";
};

window.adways.playerHelpers.OoyalaV4Detector.prototype.getPlayerClass = function() {
    return this._playerClass;
};

window.adways.playerHelpers.OoyalaV4Detector.prototype.detect = function(playerAPI) {
    if (playerAPI.getCurrentItemClosedCaptionsLanguages !== undefined &&
            playerAPI.getBitratesAvailable !== undefined &&
            playerAPI.updateAsset !== undefined &&
            playerAPI.getPlayheadTime !== undefined &&
            playerAPI.getElementId !== undefined &&
            playerAPI.setCurrentItemEmbedCode === undefined) {
        return true;
    }
    return false;
};

//

window.adways.playerHelpers.OoyalaDetector = function() {
    this._playerClass = "ooyala";
};

window.adways.playerHelpers.OoyalaDetector.prototype.getPlayerClass = function() {
    return this._playerClass;
};

window.adways.playerHelpers.OoyalaDetector.prototype.detect = function(playerAPI) {
    if (playerAPI.getCurrentItemClosedCaptionsLanguages !== undefined &&
            playerAPI.getBitratesAvailable !== undefined &&
            playerAPI.getPlayheadTime !== undefined &&
            playerAPI.shouldDisplayCuePointMarkers !== undefined &&
            playerAPI.setCurrentItemEmbedCode !== undefined) {
        return true;
    }
    return false;
};

//

window.adways.playerHelpers.BrainsonicDetector = function() {
    this._playerClass = "brainsonic";
};

window.adways.playerHelpers.BrainsonicDetector.prototype.getPlayerClass = function() {
    return this._playerClass;
};

window.adways.playerHelpers.BrainsonicDetector.prototype.detect = function(playerAPI) {
    if (playerAPI.getVersion !== undefined && typeof playerAPI.getVersion === 'function') {
        var version = playerAPI.getVersion();
        var ret = version.match('brainsonic-.*');
        if (ret !== null && ret[0] === version) {
            return true;
        }
    }
    return false;
};

//

window.adways.playerHelpers.HTML5Detector = function() {
    this._playerClass = "html5";
};

window.adways.playerHelpers.HTML5Detector.prototype.getPlayerClass = function() {
    return this._playerClass;
};

window.adways.playerHelpers.HTML5Detector.prototype.detect = function(playerAPI) {
    if (playerAPI.tagName !== undefined && typeof playerAPI.tagName !== 'function' &&
            playerAPI.paused !== undefined && typeof playerAPI.paused !== 'function') {
        if (playerAPI.tagName === "VIDEO" && playerAPI.src !== '') {
            return true;
        }
    }
    return false;
};

//

window.adways.playerHelpers.VideoJSDetector = function() {
    this._playerClass = "videojs";
};

window.adways.playerHelpers.VideoJSDetector.prototype.getPlayerClass = function() {
    return this._playerClass;
};

window.adways.playerHelpers.VideoJSDetector.prototype.detect = function(playerAPI) {
    if (playerAPI.contentEl !== undefined && typeof playerAPI.contentEl === 'function') {
        var tmp = playerAPI.contentEl().className.match('.*video-js.*');
//        if (tmp !== null && tmp[0] === playerAPI.contentEl().className) {
        if (tmp !== null && playerAPI.contentEl().className.indexOf(tmp[0]) > -1) {
            return true;
        }
        var tmp = playerAPI.contentEl().className.match(/vjs-tech/i);
        if (tmp !== null && playerAPI.contentEl().className.indexOf(tmp[0]) > -1) {
            return true;
        }
    }
    return false;
};

//

window.adways.playerHelpers.FranceTVDetector = function() {
    this._playerClass = "francetv";
};

window.adways.playerHelpers.FranceTVDetector.prototype.getPlayerClass = function() {
    return this._playerClass;
};

window.adways.playerHelpers.FranceTVDetector.prototype.detect = function(playerAPI) {
    if (typeof playerAPI.getCurrentMedia === 'function' &&
            typeof playerAPI.getPlayerContainer === 'function' &&
            playerAPI.getPlayerContainer().length > 0 &&
            playerAPI.getPlayerContainer()[0].className.match(/jqp\-/) != null
            ) {
        return true;
    }
    return false;
};

//

window.adways.playerHelpers.thePlatformDetector = function() {
    this._playerClass = "theplatform";
};

window.adways.playerHelpers.thePlatformDetector.prototype.getPlayerClass = function() {
    return this._playerClass;
};

window.adways.playerHelpers.thePlatformDetector.prototype.detect = function(playerAPI) {
    if (typeof playerAPI.controller === 'object' && 
            playerAPI.controller !== null &&
            typeof playerAPI.controller.widgetId === 'string' &&
            typeof playerAPI.controller.getReleaseState === 'function'
            ) {
        return true;
    }
    return false;
};

window.adways.playerHelpers.ViouslyDetector = function() {
    this._playerClass = "viously";
};

window.adways.playerHelpers.ViouslyDetector.prototype.getPlayerClass = function() {
    return this._playerClass;
};

window.adways.playerHelpers.ViouslyDetector.prototype.detect = function(playerAPI) {
    if (playerAPI.parentNode 
            && playerAPI.parentNode.id
            && playerAPI.parentNode.id == "player"
            && playerAPI.ownerDocument.body.className.match("player-body")
            && playerAPI.ownerDocument.body.className.match("player-state-")
            && playerAPI.ownerDocument.getElementById("controls")) {
        this._playerAPI = playerAPI;
        return true;
    }
    return false;
};

window.adways.playerHelpers.ViouslyPlayerDetector = function() {
    this._playerClass = "viously";
};

window.adways.playerHelpers.ViouslyPlayerDetector.prototype.getPlayerClass = function() {
    return this._playerClass;
};

window.adways.playerHelpers.ViouslyPlayerDetector.prototype.detect = function(playerAPI) {
    try {
        var players = playerAPI.getElementsByClassName('c-player');
        if(players.length > 0 &&  players[0].id == 'player' && players[0].childNodes.length>0 && players[0].childNodes[0].tagName.toLowerCase() === 'video') {
            this._playerAPI = players[0].childNodes[0];        
            return true;
        }
        return false;
    } catch (e) {
        return false;
    }
};

window.adways.playerHelpers.VidazooDetector = function() {
    this._playerClass = "vidazoo";
};

window.adways.playerHelpers.VidazooDetector.prototype.getPlayerClass = function() {
    return this._playerClass;
};

window.adways.playerHelpers.VidazooDetector.prototype.detect = function(playerAPI) {
    if (playerAPI.parentNode 
            && playerAPI.parentNode.className.match("sbt-placeholder")) {
        this._playerAPI = playerAPI;
        return true;
    }
    return false;
};
try {
    if (typeof window.adwVPAIDReadyCb === "undefined") {
        var parentWindow = window;
        while (typeof parentWindow.adwVPAIDReadyCb === "undefined" && parentWindow !== parentWindow.parent)
            parentWindow = parentWindow.parent;
        if (typeof parentWindow.adwVPAIDReadyCb !== "undefined")
            parentWindow.adwVPAIDReadyCb(window);
    } else {
        window.adwVPAIDReadyCb(window);
    }
} catch (err) {    
    console.log("error with adwVPAIDReadyCb");
}
try {
    if( location.hostname.match("googleusercontent") ||  location.hostname.match("creative-preview-an")){
        window.ADW_SLOT_ID = "adw-preview-banner-" + new Date().getTime();
        if (window.frameElement == null) {
            //document.write("<div id='" + window.ADW_SLOT_ID + "'><\/div>");            
            var adwSlotId = window.ADW_SLOT_ID; 
            var adwSlot = document.createElement("div");
            adwSlot.id = adwSlotId;		
            var currentScript = document.currentScript;
            var node = document.body;
            if(currentScript && currentScript.parentNode != document.head) {
              node = currentScript.parentNode;
            }
            node.appendChild(adwSlot);
        }
        var w = window;
        var main = function(tracker) {
            try {
                w.topEl = w.top;
            } catch (e) {
                w.topEl = w.parent;
            }
            var that = this;
            this.resizeCB = function () {
                that.customSlot.style.width = document.body.offsetWidth + "px";
            };
            this.customSlot = null;
            this.topInitWidth = 0;
            if (!this.initCustomSlot()) {
                return;
            }
            var that = this;
            this.currentZindex = 0;
            this.topRatio = 0;
            this.topHeight = 0;
            this.topEl = null;
            this.computeRealSizes();
            this.createTopElement();
        };
        main.prototype.initCustomSlot = function() {
            try {
                w.topEl.document.body.style.overflowX = "hidden";
            } catch (e) {
                w.topEl = w.parent;
            }
            if (w.frameElement == null) {
                var tmp = w.document.getElementById(w.ADW_SLOT_ID);
                if (tmp != null) {
                    this.customSlot = tmp.parentNode;
                    this.topInitWidth = this.customSlot.offsetWidth;
                    if(this.topInitWidth === 0) {
                        this.topInitWidth = document.body.offsetWidth;
                    }
                } else {
                    return false;
                }
            } else {
                this.customSlot = w.frameElement.parentNode;
                this.topInitWidth = w.frameElement.offsetWidth;
                w.frameElement.style.width = "0";
                w.frameElement.style.height = "0";
                w.frameElement.style.position = "absolute";
                this.customSlot.style.left = "0";
            }
            this.customSlot.style.position = "relative";
            this.customSlot.style.top = "0";
            if(this.customSlot.style.width == "") {
                this.resizeCB();
                window.addEventListener("resize", this.resizeCB);  
            }
            return true;
        };
        main.prototype.createTopElement = function () {
            this.customSlot.style.height = this.topHeight + "px";
            this.topEl = w.document.createElement("div");
            this.topEl.className = "adw-top-banner";
            this.topEl.style.height = "100%";
            this.topEl.style.position = "relative";
            this.topEl.style.backgroundColor = "#222222";
            this.topEl.style.backgroundImage = 'url("https://d1tvn48knwz507.cloudfront.net/banners/adways_background_728x90.png")';
            this.topEl.style.backgroundPosition = "center";
            this.topEl.style.backgroundRepeat = "no-repeat";
            this.topEl.style.backgroundSize = "contain";
            this.topEl.style.overflow = "hidden";
            this.topEl.style.cursor = "pointer";
            this.topEl.style.top = "0";
            this.customSlot.appendChild(this.topEl);
            this.topEl.style.width = "100%";
            this.topEl.style.left = "0";
            this.topEl.addEventListener("click", function () {
                window.open("https://www.adways.com", "_blank");
            });
        };
        main.prototype.computeRealSizes = function () {
            this.topRatio = (728 / 90) + "";         
                this.topHeight = parseFloat(this.topInitWidth / this.topRatio + "");  
        };
        new main();
    }
} catch (err) {    
    console.log("error with preview-banner");
}
}(window));