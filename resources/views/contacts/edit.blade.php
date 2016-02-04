@extends('layout.template')
@section('content')
    <script type="text/javascript" src="{{ URL::asset('js/ajaxformhandler.js') }}"></script>
    <meta name="_token" content="{{ csrf_token() }}" />
    <h1>Edit Contact</h1>
    {!! Form::model($contact,['method' => 'PATCH','route'=>['contacts.update',$contact->id],'class'=>'contactForm']) !!}
    <div class="form-group">
        {!! Form::label('First Name', 'First Name:') !!}
        {!! Form::text('firstname',null,['class'=>'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('Last Name', 'Last Name:') !!}
        {!! Form::text('lastname',null,['class'=>'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('Email', 'Email:') !!}
        {!! Form::text('email',null,['class'=>'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('Phone', 'Phone:') !!}
        {!! Form::text('phone',null,['class'=>'form-control']) !!}
    </div>
    {!! Form::label('Info', 'Additional Info:') !!}
    <div id="additionalInfo" class="form-group">
      @foreach ($info_fields as $info)
        <div class="entry input-group col-xs-3">
          <input class="form-control" name="info[]" type="text" value="{{ $info }}">
          <span class="input-group-btn">
            <button class="btn btn-remove btn-danger" type="button">
              <span class="glyphicon glyphicon-minus"></span>
            </button>
        </div>
      @endforeach
      <div class="entry input-group col-xs-3">
        <input class="form-control" name="info[]" type="text">
        <span class="input-group-btn">
          <button class="btn btn-success btn-add" type="button">
            <span class="glyphicon glyphicon-plus"></span>
          </button>
        </span>
      </div>
    </div>
    <div class="form-group">
        {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
    </div>
    {!! Form::close() !!}
@stop
