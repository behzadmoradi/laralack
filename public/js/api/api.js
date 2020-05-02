function setApiUriAndToken(uri, userInfo) {
    var json = $.parseJSON(userInfo);
    baseUrl = uri;
    securityToken = json['api_token'];
    userAuthorizedId = json['id'];
}

function get(url, successHandler = null, errorHandler = null, data = null, cache = false, dataType = 'json') {
    $.ajax({
        url: baseUrl + url,
        method: "GET",
        contentType: "application/json; charset=utf-8",
        data: data,
        dataType: dataType,
        cache: cache,
        headers: {
            'Accept': 'application/json',
            "Authorization": "Bearer " + securityToken
        },
        success: function (data) {
            if (successHandler) {
                successHandler(data);
            }
        },
        error: function (data) {
            if (errorHandler) {
                errorHandler(data);
            }
        }
    });
}

function post(url, successHandler = null, errorHandler = null, data = null, cache = false, dataType = 'json') {
    $.ajax({
        url: baseUrl + url,
        method: "POST",
        contentType: "application/json; charset=utf-8",
        data: data,
        dataType: dataType,
        cache: cache,
        headers: {
            'Accept': 'application/json',
            'Content-type': 'application/x-www-form-urlencoded',
            "Authorization": "Bearer " + securityToken
        },
        success: function (data) {
            if (successHandler) {
                successHandler(data);
            }
        },
        error: function (data) {
            if (errorHandler) {
                errorHandler(data);
            }
        }
    });
}

function put(url, successHandler = null, errorHandler = null, data = null, cache = false, dataType = 'json') {
    $.ajax({
        url: baseUrl + url,
        method: "PUT",
        contentType: "application/json; charset=utf-8",
        data: data,
        dataType: dataType,
        cache: cache,
        headers: {
            'Content-type': 'application/x-www-form-urlencoded',
            'Accept': 'application/json',
            "Authorization": "Bearer " + securityToken
        },
        success: function (data) {
            if (successHandler) {
                successHandler(data);
            }
            if (data['data']['profile_is_updated']) {
                $('.username').text(data['data']['user_info']['name']);
            }
        },
        error: function (data) {
            if (errorHandler) {
                errorHandler(data);
            }
        }
    });
}

function del(url, successHandler = null, errorHandler = null, data = null, cache = false, dataType = 'json') {
    $.ajax({
        url: baseUrl + url,
        method: "DELETE",
        contentType: "application/json; charset=utf-8",
        data: data,
        dataType: dataType,
        cache: cache,
        headers: {
            'Accept': 'application/json',
            "Authorization": "Bearer " + securityToken
        },
        success: function (data) {
            if (successHandler) {
                successHandler(data);
            }
        },
        error: function (data) {
            if (errorHandler) {
                errorHandler(data);
            }
        }
    });
}
