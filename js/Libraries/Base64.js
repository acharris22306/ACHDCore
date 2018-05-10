
//
//original Base64 code: http://www.webtoolkit.info/javascript_base64.html 
//WebToolkit licence: http://www.webtoolkit.info/license1/index.html
//CC licence: http://creativecommons.org/licenses/by/2.0/uk/
//

var Base64 = {
    // private property
    _keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
    // public method for encoding
    encode : function (input) {
        var output = "";
        var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
        var i = 0;
        input = Base64._utf8_encode(input);
        while (i < input.length) {
            chr1 = input.charCodeAt(i++);
            chr2 = input.charCodeAt(i++);
            chr3 = input.charCodeAt(i++);
            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;
            if (isNaN(chr2)) {
                enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
                enc4 = 64;
            }
            output = output +
            this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
            this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
        }
        return output;
    },
	
	base64EncodeToDataUrlString: function (stringToEncode,encodingHeader){
		var base64Data = this.encode(stringToEncode);
		return encodingHeader+base64Data;
	},
	getDataUrlHeaderFromType: function (typeOfFile){
		var dataUrlHeader = '';
		switch (typeOfFile){
			case 'svg':
				dataUrlHeader = 'data:image/svg+xml;base64,';
				break;
			case 'pdf':
				dataUrlHeader = 'data:application/pdf;base64,';
				break;
			case 'png':
				dataUrlHeader = 'data:image/png;base64,';
				break;
			case 'jpeg':
				dataUrlHeader = 'data:image/jpeg;base64,';
				break;
			case 'css':
				dataUrlHeader = 'data:text/css;base64,';
				break;
			case 'php':
				dataUrlHeader = 'data:text/php;base64,';
				break;
			case 'html':
				dataUrlHeader = 'data:text/html;base64,';
				break;
			case 'gif':
				dataUrlHeader = 'data:image/gif;base64,';
				break;
			case 'tiff':
				dataUrlHeader = 'data:image/tiff;base64,';
				break;
			case 'targa':
				dataUrlHeader = 'data:image/targa;base64,';
				break;
			case 'bmp':
				dataUrlHeader = 'data:image/bmp;base64,';
				break;
			case 'doc':
				dataUrlHeader = 'data:application/msword;base64,';
				break;
			case 'xlsx':
				dataUrlHeader = 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,';
				break;
			case 'pptx':
				dataUrlHeader = 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.presentation;base64,';
				break;
			case 'docx':
				dataUrlHeader = 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.document;base64,';
				break;
			case 'xls':
				dataUrlHeader = 'data:application/vnd.ms-excel;base64,';
				break;
			case 'odt':
				dataUrlHeader = 'data:application/vnd.oasis.opendocument.text;base64,';
				break;
			case 'ods':
				dataUrlHeader = 'data:application/vnd.oasis.opendocument.spreadsheet;base64,';
				break;
			case 'xml':
				dataUrlHeader = 'data:text/xml;base64,';
				break;
			case 'js':
				dataUrlHeader = 'data:application/x-javascript;base64,';
				break;
			case 'json':
				dataUrlHeader = 'data:application/json;base64,';
				break;
			case 'rtf':
				dataUrlHeader = 'data:application/rtf;base64,';
				break;
			case 'text':
				dataUrlHeader = 'data:text/plain;base64,';
				break;
			case 'odp':
				dataUrlHeader = 'data:application/vnd.oasis.opendocument.presentation;base64,';
				break;
			case 'ppt':
				dataUrlHeader = 'application/vnd.ms-powerpoint,';
				break;
			case 'other':
				dataUrlHeader = 'data:;base64,';
				break;
			default:
				dataUrlHeader = false;
				break;
		}
		return dataUrlHeader;
	},


	strToDataUrl: function (typeOfFile,dataToEncode){
		var encodingHeader = this.getDataUrlHeaderFromType(typeOfFile);
		var encodedData = this.base64EncodeToDataUrlString(dataToEncode,encodingHeader);
		return encodedData;
	},
	encodeAndGoToUrl: function (typeOfFile,dataToEncode){
		var url = this.strToDataUrl(typeOfFile,dataToEncode);
		window.location = url;
	},
	encodeAndOpenInNewTab: function (typeOfFile,dataToEncode){
		var url = this.strToDataUrl(typeOfFile,dataToEncode);
		open(url);
	},

    // public method for decoding
    decode : function (input) {
        var output = "";
        var chr1, chr2, chr3;  
        var enc1, enc2, enc3, enc4;
        var i = 0;
        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
        while (i < input.length) {
            enc1 = this._keyStr.indexOf(input.charAt(i++));
            enc2 = this._keyStr.indexOf(input.charAt(i++));
            enc3 = this._keyStr.indexOf(input.charAt(i++));
            enc4 = this._keyStr.indexOf(input.charAt(i++));
            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;
            output = output + String.fromCharCode(chr1);
            if (enc3 != 64) {
                output = output + String.fromCharCode(chr2);
            }
            if (enc4 != 64) {
                output = output + String.fromCharCode(chr3);
            }
        }
        output = Base64._utf8_decode(output);
        return output;
    },
    // private method for UTF-8 encoding
    _utf8_encode : function (string) {
        string = string.replace(/\r\n/g,"\n");
		var utftext = "";
        for (var n = 0; n < string.length; n++) {
            var c = string.charCodeAt(n);
            if (c < 128) {
                utftext += String.fromCharCode(c);
            } else if((c > 127) && (c < 2048)) {
		       utftext += String.fromCharCode((c >> 6) | 192);
               utftext += String.fromCharCode((c & 63) | 128);
            } else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }
        }
        return utftext;
    },
    // private method for UTF-8 decoding
    _utf8_decode : function (utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;
        while ( i < utftext.length ) {
            c = utftext.charCodeAt(i);
            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            } else if((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i+1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            } else {
                c2 = utftext.charCodeAt(i+1);
                c3 = utftext.charCodeAt(i+2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }
        }
        return string;
    }

};

// JavaScript Document