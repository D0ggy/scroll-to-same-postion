@extends('app')

@section('title', '列表页')

@section('style')
    <style>
        body, html {
            height: 100%;
        }

        #app {
            height: 100%;
            display: flex;
            display: -webkit-flex;
            flex-direction: column;
            -webkit-flex-direction: column;
        }

        .scrollBox {
            overflow-y: auto;
        }
    </style>
@endsection

@section('content')
    <input type="hidden" name="url_get_article_ajax" value="{{route('ajax_list')}}">
    <div id="app">
        <div class="container scrollBox">
            <div class="col-md-10 col-md-offset-1">
                <div id="list">

                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')

    <script>
        let _LOADING = false;
        const ID_LIST = 'list';
        let URL_GET_ARTICLE = $('input[name="url_get_article_ajax"]').val();
        let current_page = 1;
        const CLASS_RECORD_SCROLLTOP = '.scrollBox';

        let str_page = sessionStorage.getItem('str_page');

        let ajaxArticle = function () {
            if (_LOADING === true) {
                return;
            }
            _LOADING = true;
            if (current_page == '1') {
                $('#' + ID_LIST).html('');
            }
            _loading('show');
            $.ajax({
                url: _construct_url_str(URL_GET_ARTICLE, {
                    ['page']: current_page
                }),
                type: 'POST',
                async: true,
                dataType: 'json',
            }).done(function (data, textStatus, jqXHR) {
                renderArticles(data);
                current_page = current_page + 1;
                _LOADING = false;
                _loading();
            }).fail(function (jqXHR, textStatus, errorThrown) {
                _LOADING = false;
                _loading();
            });
        };

        $(function () {
            loadContent();
            $(CLASS_RECORD_SCROLLTOP).infinite(400).on("infinite", ajaxArticle);
        });

        function loadContent() {
            let ajax_item_list = [];
            let old_page_num = '';

            console.log('str_page = ' + str_page);
            if (str_page) {
                old_page_num = str_page;
            }

            console.log('old_page_num=' + old_page_num);
            if (old_page_num === '') {
                let url_str = _construct_url_str(URL_GET_ARTICLE, {['page']: current_page,});
                let obj = {['url_str']: url_str};
                ajax_item_list.push(obj);
            } else {
                for (let i = 1; i < old_page_num; i++) {
                    let url_str = _construct_url_str(URL_GET_ARTICLE, {['page']: i});
                    let obj = {['url_str']: url_str};
                    ajax_item_list.push(obj);
                }
                sessionStorage.setItem('str_page', '');
            }


            let callback_after_all_call = function () {
                console.log('全部加载完成');
                _loading('show');
                _LOADING = false;
                // 加载所有内容后，滚动滚动条到之前的位置
                let resultListBoxScrollTop = sessionStorage.getItem('resultListBoxScrollTop');
                $(CLASS_RECORD_SCROLLTOP).scrollTop(resultListBoxScrollTop);
                sessionStorage.setItem('resultListBoxScrollTop', '');
                _loading();
            };
            let callback_success = function (data) {
                // 执行一部分逻辑
                renderArticles(data);
                current_page = current_page + 1;
                _LOADING = false;
                _loading();
            };
            let callback_fail = function () {
                _LOADING = false;
                alert('Server access error!');
            };

            if (ajax_item_list.length === 1) {
                ajaxArticle();
            } else {
                _run_ajax_sequential(ajax_item_list, callback_success, callback_fail, callback_after_all_call, null);
            }

        }


        function saveSettingToCache() {
            sessionStorage.setItem('str_page', current_page);
            sessionStorage.setItem('resultListBoxScrollTop', $(CLASS_RECORD_SCROLLTOP).scrollTop());
            return;
        }


        function _run_ajax_sequential(
            ajax_item_list, callback_success,
            callback_fail, callback_after_all_call,
            callback_fail_after_all_call
        ) {
            // function to trigger the ajax call
            let ajax_request = function (item) {
                let deferred = $.Deferred();
                $.ajax({
                    url: item['url_str'],
                    dataType: "json",
                    type: 'POST',
                    success: function (data) {
                        // do something here
                        if (_isFunction(callback_success)) callback_success(data);
                        // mark the ajax call as completed
                        deferred.resolve(data);
                    },
                    error: function (error) {
                        // mark the ajax call as failed
                        if (_isFunction(callback_fail)) callback_fail(error);
                        deferred.reject(error);
                    }
                });

                return deferred.promise();
            };

            let looper = $.Deferred().resolve();

            // go through each item and call the ajax function
            $.when.apply($, $.map(ajax_item_list, function (item, i) {
                looper = looper.then(function () {
                    // trigger ajax call with item data
                    return ajax_request(item);
                });
                return looper;
            })).then(function () {
                // run this after all ajax calls have completed
                if (_isFunction(callback_after_all_call)) callback_after_all_call();
                return;
                //console.log('Done!');
            }).fail(function () {
                if (_isFunction(callback_fail_after_all_call)) callback_fail_after_all_call();
                _loading();
                return;
            });
        }


        function renderArticles(data) {
            // 渲染页面
            for (let i = 0; i < data.length; i++) {
                let $item_detail = $('<div>');

                let article_detail = data[i];

                $('<div>').text(article_detail.title)
                    .appendTo($item_detail);

                $('<a>').text('详情')
                    .attr('href', "javascript:void(0);")
                    .addClass('btn btn-success')
                    .on('click', function (e) {
                        saveSettingToCache();
                        window.open('/detail/' + article_detail.id, '_self');
                    })
                    .addClass('oneHref')
                    .css({"text-decoration": "invert"})
                    .appendTo($item_detail);
                $item_detail.append('<hr>');
                $("#"+ID_LIST).append($item_detail);
            }
            _LOADING = false;
            return;
        }

        function openPage(url) {
            saveSettingToCache();
            window.open(url, '_self');
            return;
        }

    </script>
@endsection
