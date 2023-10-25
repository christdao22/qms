@section('content')
<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="row">
            <div class="col-sm-12">
                <h3>{{ trans('app.'.$displayTitleKey) }} <button class="pull-right btn btn-sm btn-primary"
                        onclick="goFullscreen('fullscreen'); return false"><i class="fa fa-arrows-alt"
                            aria-hidden="true"></i></button></h3>
                <span class="text-danger">(enable full-screen mode and wait 10 seconds to adjust the screen)</span>
            </div>
        </div>
    </div>

    <div class="panel-body" id="fullscreen">
        <div class="media" style="height:60px;background:#ffcd03;margin-top:-20px;margin-bottom:20px">
            <div class="media-left hidden-xs">
                <img class="media-object" style="height:60px;" src="{{ asset('public/assets/img/icons/logo1.png') }}"
                    alt="Logo">
            </div>
            <div class="media-body" style="color:#ffffff">
                <h4 class="media-heading" style="font-size:50px;line-height:60px;color:#000000;">
                    <marquee direction="{{ (!empty($setting->direction)?$setting->direction:null) }}">
                        {{ (!empty($setting->message)?$setting->message:null) }}</marquee>
                </h4>
            </div>
        </div>
        <div class="row">
            <div id="{{ $displayMonitor }}"></div>
        </div>


        <div class="panel-footer row" style="margin-top:10px">
            @include('backend.common.info')
            <span class="col-xs-10 text-left">@yield('info.powered-by')</span>
            <span class="col-xs-2 text-right">@yield('info.version')</span>
        </div>
    </div>
</div>
@endsection

@push('scripts')

<script type="text/javascript">
    $(document).ready(function () {
        //get previous token
        var view_token = [];
        var interval = 500;

        var display = function () {
            var width = $(window).width();
            var height = $(window).height();
            var isFullScreen = document.fullScreen ||
                document.mozFullScreen ||
                document.webkitIsFullScreen || (document.msFullscreenElement != null);
            if (isFullScreen) {
                var width = $(window).width();
                var height = $(window).height();
            }

            $.ajax({
                type: 'post',
                dataType: 'json',
                url: '{{ URL::to("common/".$displayMonitor) }}',
                data: {
                    _token: '<?php echo csrf_token() ?>',
                    view_token: view_token,
                    width: width,
                    height: height
                },
                success: function (data) {
                    $(`#{{ $displayMonitor }}`).html(data.result);

                    if('{{ $displayMonitor }}' == 'display1') {
                        view_token = data.view_token;
                    } else {
                        view_token = (data.all_token).map(function (item) {
                            return {
                                counter: item.counter, 
                                token: item.token
                            }
                        });
                    }

                    //notification sound
                    if (data.status) {
                        console.log(data.new_token);
                        // window.speechSynthesis.speak(new SpeechSynthesisUtterance('Payroll ' + data.new_token[0].token));
                        var url  = "{{ URL::to('') }}";
                        var lang = "{{ in_array(session()->get('locale'), $setting->languages)?session()->get('locale'):'en' }}";
                        var player = new Notification;
                        player.call(data.new_token, lang, url);
                    }

                    setTimeout(display, data.interval);
                }
            });
        };

        setTimeout(display, interval);

    });

</script>
@endpush


