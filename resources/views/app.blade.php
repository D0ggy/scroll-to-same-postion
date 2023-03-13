<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0,user-scalable=no,minimum-scale=1.0,maximum-scale=1.0">
    <title>@yield('title', '测试')</title>


    <meta name="csrf-token" content="{{ csrf_token() }}">


    <link href="https://lib.baomitu.com/twitter-bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet">

    @yield('style')
</head>
<body>

@yield('content')

<!-- 自用 js ，位于 public 目录下 assets/js/ -->

<script src="https://lib.baomitu.com/jquery/3.3.1/jquery.min.js"></script>
<script src="https://lib.baomitu.com/jquery-weui/1.2.0/js/jquery-weui.min.js"></script>
<script src="https://cdn.bootcdn.net/ajax/libs/jquery-loading-overlay/2.1.7/loadingoverlay.min.js"></script>
<script>
    // return [address]?[para1]=[val1]&[para2]=[val2]&...
    function _construct_url_str(url, datalst) {
        let str = url + "?";
        for (let para_name in datalst) {
            str = str + para_name + "=" + datalst[para_name] + "&";
        }
        return str;
    }

    function _loading(action) {
        if (action === 'show') {
            $.LoadingOverlay("show", {
                background: "rgba(0, 0, 0, 0.2)",
                size: 50,
                maxSize: 120,
                minSize: 20,
                image: '/loading.gif'
            });
        } else {
            $.LoadingOverlay("hide");
        }
    }
    let _isFunction = function (value) {
        return $.isFunction(value);
    };
</script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        error: function (res) {
        },
        success: function (result) {
        }
    });
</script>
<!-- 自定义 js -->
@yield('script')
</body>
</html>
