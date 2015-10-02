{!! Form::open(array('id' => 'message', 'url' => route('message.store', [$user_from, $user_to]))) !!}

<div class="form-group">

    {!! Form::textarea('body', null, [
        'class' => 'form-control input-md',
        'placeholder' => trans('message.create.field.body.title'),
        'rows' => 5
    ]) !!}
        
</div>

<div class="row">

    <div class="col-md-6 col-md-offset-6">

        <div class="form-group">

        {!! Form::submit(trans('message.create.submit.title'), [
            'class' => 'btn btn-primary btn-md btn-block'
        ]) !!}
        
        </div>

    </div>

</div>

{!! Form::close() !!}