@extends('layouts.backend')
@section('title', trans('app.auto_token'))

@section('content')
{{ $display }}
<div class="panel panel-primary" id="toggleScreenArea">
    <div class="panel-heading pt-0 pb-0">
        <ul class="row m-0 list-inline">
            <li class="col-xs-6 col-sm-4 p-0 text-left">
                <img src="{{ asset('public/assets/img/icons/logo.jpg') }}" width="210" height="50">
            </li>
            <li class="col-xs-4 col-sm-4 hidden-xs" id="screen-title">
                <h3 class="mt-1 pt-1">{{ trans('app.auto_token') }}</h3>
            </li>
            <li class="col-xs-6 col-sm-4 p-1 text-right">
                <div class="mt-1 pt-1">
                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#infoModal">
                      <i class="fa fa-info-circle"></i>
                    </button>
                    @if ($display->keyboard_mode)
                    <div class="disabled btn btn-success btn-sm" title="Keyboard Mode Enable">
                        <i class="fa fa-keyboard-o"></i>&nbsp;&nbsp;<i class="fa fa-check"></i>
                    </div>
                    @else
                    <div class="disabled btn btn-danger btn-sm" title="Keyboard Mode Disabled">
                        <i class="fa fa-keyboard-o"></i>&nbsp;&nbsp;<i class="fa fa-times"></i>
                    </div>
                    @endif
                    <button id="toggleScreen" class="btn btn-sm btn-primary"><i class="fa fa-arrows-alt"></i></button>
                </div>
            </li>
        </ul>
    </div>

    <div class="panel-body">
        <div class="col-sm-12" id="screen-content">
            @foreach ($departmentList as $department)
                <div class="p-1 m-1 btn btn-primary capitalize text-center">
                    <button
                        type="button"
                        class="p-1 m-1 btn btn-primary capitalize text-center"
                        style="min-width: 15vw;white-space: pre-wrap;box-shadow:0px 0px 0px 2px#<?= substr(dechex(crc32($department->name)), 0, 6); ?>"
                        data-toggle="modal"
                        data-target="#tokenModal"
                        data-department-id="{{ $department->department_id }}"
                        data-counter-id="{{ $department->counter_id }}"
                        data-user-id="{{ $department->user_id }}"
                        >
                            <h5>{{ $department->name }}</h5>
                            @if ($display->show_officer)
                            <h6>{{ $department->officer }}</h6>
                            @endif
                    </button>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="infoModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="infoModalLabel"><?= trans('app.note') ?></h4>
      </div>
      <div class="modal-body">
        <p><strong class="label label-warning"> Note 1 </strong> &nbsp; To display a department on the auto token setting page, you need to set up it in Auto Token Setting page.
        </p>
        <p><strong class="label label-warning"> Note 2 </strong> &nbsp;
            You can create a token by click on a key of the keyboard.
            Enable <span class='label label-success'>Keyboard Mode</span> from the display setting page.
            To create a token for a department, press on the key which you have denoted in the <strong>key for keyboard mode</strong> field in the add department page.
            The <strong>key for keyboard mode</strong> filed is also used to manage the token serial.
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade auto-queue" tabindex="-1" id="tokenModal" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      {{ Form::open(['url' => 'admin/token/auto', 'class' => 'AutoFrm']) }}
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">{{ trans('app.user_information') }}</h4>
      </div>
      <div class="modal-body" style="display: ">
        <div class="" style="margin-bottom: 30px">
                <p><strong>Note:</strong> Please fill out the following details accurately, especially your full name and school affiliation to ensure that we can provide you with a Certificate of Appearance.</p>
                <p>Your information will be kept confidential and used solely for certificate issuance purposes.</p>
        </div>

        <div class="form-group">
            <label for="client_name">Full Name</label>
            <input type="text" name="client_name" class="form-control" placeholder="Enter full name" required>
            <span class="text-danger">The name field is required!</span>
        </div>
        <div class="form-group">
            <label for="school_id">School</label>
            <select name="school_id" id="school_id" class="text-capitalize" required>
                <option value="" readonly>Select school</option>
                <option id="others" value="others">Others</option>
                @foreach ($schools as $school)
                    <option value="{{ $school->id }}">{{ $school->school_name }}</option>
                @endforeach
            </select>
            <span class="text-danger">The school field is required!</span>
        </div>
        <div class="form-group hidden" id="other_field">
            <label for="other_institution">School/Company</label>
            <input type="text" name="other_institution" id="other_institution" class="form-control" placeholder="Enter School/Company">
        </div>

        <div class="">
            <button type="submit" class="user-info-btn btn-primary">{{ trans('app.submit') }}</button>
        </div>

        <input type="hidden" name="department_id">
        <input type="hidden" name="counter_id">
        <input type="hidden" name="user_id">
      {{ Form::close() }}
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection

@push("scripts")
@push("certificates")

<script type="text/javascript">
(function($) {
    $('.modal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        $('input[name=department_id]').val(button.data('department-id'));
        $('input[name=counter_id]').val(button.data('counter-id'));
        $('input[name=user_id]').val(button.data('user-id'));

        $("input[name=client_name]").val("");
        $("textarea[name=note]").val("");
        // $('.modal button[type=submit]').addClass('hidden');
    });

    $('.modal').on('hide.bs.modal', function () {
        $('.modal-backdrop').remove();
    });

    $("input[name=client_name], textarea[name=note]").on('keyup change', function(e){
        var valid = false;
        var mobileErrorMessage = "";
        var noteErrorMessage = "";
        var mobile = $('input[name=client_name]').val();
        var note   = $('textarea[name=note]').val();

        if ($('input[name=client_name]').length)
        {
            if (mobile == '')
            {
                mobileErrorMessage = "The Name field is required!";
                valid = false;
            }
            else if(!$.isNumeric(mobile))
            {
                mobileErrorMessage = "The Name is incorrect!";
                valid = false;
            }
            // else if (mobile.length >= 15 || mobile.length < 7)
            // {
            //     mobileErrorMessage = "The Name must be between 7-15 digits";
            //     valid = false;
            // }
            else
            {
                mobileErrorMessage = "";
                valid = true;
            }
        }

        if ($('textarea[name=note]').length)
        {
            if (note == '')
            {
                noteErrorMessage = "The Note field is required!";
                valid = false;
            }
            else if (note.length >= 255 || note.length < 0)
            {
                noteErrorMessage = "The Note must be between 1-255 characters";
                valid = false;
            }
            else
            {
                noteErrorMessage = "";
                valid = true;
            }
        }


        if(!valid && mobileErrorMessage.length > 0)
        {
            $('.modal button[type=submit]').addClass('hidden');
        }
        else if(!valid && noteErrorMessage.length > 0)
        {
            $('.modal button[type=submit]').addClass('hidden');
        }
        else
        {
            $(this).next().html(" ");
            $('.modal button[type=submit]').removeClass('hidden');
        }
        $('textarea[name=note]').next().html(noteErrorMessage);
        $('input[name=client_name]').next().html(mobileErrorMessage);

    });

    var frm = $(".AutoFrm");
    frm.on('submit', function(e){
        e.preventDefault();
        $(".modal").modal('hide');
        var formData = new FormData($(this)[0]);
        ajax_request(formData);
    });

    function ajax_request(formData)
    {
        $.ajax({
            url: '{{ url("admin/token/auto") }}',
            type: 'post',
            dataType: 'json',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            contentType: false,
            cache: false,
            processData: false,
            data:  formData,
            success: function(data)
            {
                if (data.status)
                {
                    printThis(certAppearance(data.token, data.setting));

                    $("input[name=client_name]").val("");
                    $("textarea[name=note]").val("");
                    $('.modal button[type=submit]').addClass('hidden');
                }
            },
            error: function(xhr)
            {
                alert('wait...');
            }
        });
    }

    $("body #toggleScreen").on("click", function(){
        if ( $("body #cm-menu").is(":hidden") )
        {
            $("body #cm-menu").show();
            $("body #cm-header").show();
            $("body .cm-footer").removeClass('hide');
            $("body.cm-1-navbar #global").removeClass('p-0');
            $("body .container-fluid").removeClass('m-0 p-0');
            $("body .panel").removeClass('m-0');
            $("body #toggleScreenArea #screen-note").show();
            $("body .panel-heading h3").text("{{ trans('app.auto_token') }}");

            $("body #toggleScreenArea #screen-content").attr({'style': ''});
            $("body #toggleScreen").html('<i class="fa fa-arrows-alt"></i>');
        }
        else
        {
            $("body #cm-menu").hide();
            $("body #cm-header").hide();
            $("body .cm-footer").addClass('hide');
            $("body.cm-1-navbar #global").addClass('p-0');
            $("body .container-fluid").addClass('m-0 p-0');
            $("body .panel").addClass('m-0');
            $("body .panel-heading h3").text($('.cm-navbar>.cm-flex').text());

            $("body #toggleScreenArea #screen-note").hide();
            $("body #toggleScreenArea #screen-content").attr({'style': 'width:100%;text-align:center'});
            $("body #toggleScreen").html('<i class="fa fa-arrows"></i>');
        }
    });


    $('body').on("keydown", function (e) {
        var key = e.key;
        var code = e.keyCode;

        if ($('.modal.in').length == 0 && '{{$display->keyboard_mode}}'==1 && ((code >= 48 && code <=57) ||  (code >= 96 && code <=105) || (code >= 65 && code <=90)))
        {
            var keyList = '<?= $keyList; ?>';
            $.each(JSON.parse(keyList), function (id, obj) {
                if (obj.key == key) {
                    // check form and ajax submit
                    @if($display->sms_alert || $display->show_note)
                        var modal = $('#tokenModal');
                        modal.modal('show');
                        modal.find('input[name=department_id]').val(obj.department_id);
                        modal.find('input[name=counter_id]').val(obj.counter_id);
                        modal.find('input[name=user_id]').val(obj.user_id);
                        modal.find("input[name=client_name]").val("");
                        modal.find("textarea[name=note]").val("");
                        modal.find('.modal button[type=submit]').addClass('hidden');
                    @else
                        var formData = new FormData();
                        formData.append("department_id", obj.department_id);
                        formData.append("counter_id", obj.counter_id);
                        formData.append("user_id", obj.user_id);
                        ajax_request(formData);
                        return false;
                    @endif
                }
            });
        }
    });

    $('#school_id').on('change', function() {
        let val = this.value;
        if(val == 'others') {
            $('#other_field').removeClass('hidden');
        } else {
            $('#other_field').addClass('hidden');
            $('#other_field input').val('');
        }
    });
}(jQuery));
</script>
@endpush

