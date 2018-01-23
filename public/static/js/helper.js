var url = location.href.split('/');
var APP_URL = (url.length >= 3) ? '/' + url[3] : 'demo';
var CONTROLLER_URL = (url.length >= 4) ? APP_URL + '/' + url[4] : 'index';
var FUNCTION_URL = (url.length >= 5) ? CONTROLLER_URL + '/' + url[5] : 'index';