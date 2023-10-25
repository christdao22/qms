@extends('layouts.backend')
@section('title', trans('app.manual_token') )

@section('content')
<div class="panel panel-primary">

    <div class="panel-heading">
        <ul class="row list-inline m-0">
            <li class="col-xs-10 xs-view p-0 text-left" id="screen-title">
                <h3>{{ trans('app.manual_token') }}</h3>
            </li>
            <li class="col-xs-2 p-0 text-right">
                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#infoModal">
                  <i class="fa fa-info-circle"></i>
                </button>
            </li>
        </ul>
    </div>

    <div class="panel-body">

        <div id="output" class="hide alert alert-danger alert-dismissible fade in shadowed mb-1"></div>

        {{ Form::open(['url' => 'admin/token/create', 'class'=>'manualFrm mt-1  col-md-7 col-sm-8']) }}
            <div class="form-group">
                <label for="client_name">Full Name</label>
                <input type="text" name="client_name" class="form-control" placeholder="Enter full name" required>
                {{-- <span class="text-danger">The name field is required!</span> --}}
            </div>
            <div class="form-group">
                <label for="school_id">School</label>
                <select name="school_id" id="school_id" required>
                    <option value="" readonly>Select school</option>
                    <option id="others" value="others">Others</option>
                    @foreach ($schools as $school)
                        <option value="{{ $school->id }}">{{ $school->school_name }}</option>
                    @endforeach
                </select>
                {{-- <span class="text-danger">The school field is required!</span> --}}
            </div>
            <div class="form-group hidden" id="other_field">
                <label for="other_institution">School/Company</label>
                <input type="text" name="other_institution" id="other_institution" class="form-control" placeholder="Enter School/Company">
            </div>

            <div class="form-group @error('department_id') has-error @enderror">
                <label for="department_id">{{ trans('app.department') }} <i class="text-danger">*</i></label><br/>
                {{ Form::select('department_id', $departments, null, ['placeholder' => 'Select Option', 'class'=>'select2 form-control', 'id'=>'department_id']) }}<br/>
                <span class="text-danger">{{ $errors->first('department_id') }}</span>
            </div>

            <div class="form-group @error('counter_id') has-error @enderror">
                <label for="user">{{ trans('app.counter') }} <i class="text-danger">*</i></label><br/>
                {{ Form::select('counter_id', $counters, null, ['placeholder' => 'Select Option', 'class'=>'select2 form-control', 'id'=>'counter_id', 'disabled']) }}<br/>
                <span class="text-danger">{{ $errors->first('counter_id') }}</span>
            </div>

            <div class="form-group @error('user_id') has-error @enderror">
                <label for="user">{{ trans('app.officer') }} <i class="text-danger">*</i></label><br/>
                {{ Form::select('user_id', $officers, null, ['placeholder' => 'Select Option', 'class'=>'select2 form-control', 'id'=>'user_id', 'disabled'])}}<br/>
            </div>

            @if($display->show_note)
            <div class="form-group @error('note') has-error @enderror">
                <label for="note">{{ trans('app.note') }} <i class="text-danger">*</i></label>
                <textarea name="note" id="note" class="form-control"  placeholder="{{ trans('app.note') }}">{{ old('note') }}</textarea>
                <span class="text-danger">{{ $errors->first('note') }}</span>
            </div>
            @endif

            <div class="checkbox">
                <label>
                    <input type="checkbox" name="is_vip" value="1"> {{ trans('app.is_vip') }}
                </label>
            </div>

            <div class="form-group">
                <button class="button btn btn-info" type="reset"><span>Reset</span></button>
                <button class="button btn btn-success" type="submit"><span>Submit</span></button>
            </div>

        {{ Form::close() }}
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
       <p><strong class="label label-warning"> Note 1 </strong> &nbsp;
            <strong>SMS Alert {!! (!empty($display->sms_alert)?("<span class='label label-success'>Active</span>"):("<span class='label label-warning'>Deactive</span>")) !!} </strong>
                        To active or deactive SMS Alert, please change the status of SMS Alert in Setting->Display Settings page
        </p>
        <p><strong class="label label-warning"> Note 2 </strong> &nbsp;
            <strong>Show Note {!! (!empty($display->show_note)?("<span class='label label-success'>Active</span>"):("<span class='label label-warning'>Deactive</span>")) !!} </strong>
            To display note, please change the status of Show Note in Setting->Display Settings page
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push("scripts")
@push("certificates")

<script type="text/javascript">
(function() {

    var frm = $(".manualFrm");
    frm.on('submit', function(e){
      e.preventDefault();

      $.ajax({
        url: $(this).attr('action'),
        type: $(this).attr('method'),
        dataType: 'json',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        contentType: false,
        cache: false,
        processData: false,
        data:  new FormData($(this)[0]),
        success: function(data)
        {
            if (data.status)
            {
                printThis(certAppearance(data.token, data.setting));
            }
            else
            {
                $("#output").html(data.exception).removeClass('hide');
            }
        },
        error: function(xhr)
        {
            alert('failed...');
        }
      });
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

    $('#department_id').on('change', function () {
        let department_id = this.value;
        $.ajax({
            url: `getCounter/${department_id}`,
            type: 'get',
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(data)
            {
                $('#counter_id').removeAttr('disabled').empty();
                $('#user_id').removeAttr('disabled').empty();
                $.each(data, function(key, value) {
                    $('#counter_id').append($("<option></option>").attr("value",value.counter_id).text(value.counter_name));
                });
                $('#counter_id').trigger('change');
            },
            error: function(xhr)
            {
                alert('failed...');
            }
        });
    });

    $('#counter_id').on('change', function () {
        let counter_id = this.value;
        $.ajax({
            url: `getOfficer/${counter_id}`,
            type: 'get',
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(data)
            {
                $('#user_id').empty();
                $.each(data, function(key, value) {
                    $('#user_id').append($("<option></option>").attr("value",value.user_id).text(value.firstname + ' ' + value.lastname));
                });
                $('#user_id').prop('selected', true);
            },
            error: function(xhr)
            {
                alert('failed...');
            }
        });
    });
})();
</script>
@endpush

