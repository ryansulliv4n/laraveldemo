@extends('layout.template')
@section('content')
    <script type="text/javascript" src="{{ URL::asset('js/ajaxformhandler.js') }}"></script>
    <meta name="_token" content="{{ csrf_token() }}" />
    <h1>Create Contact</h1>
    {!! Form::open(['url' => 'contacts', 'class' => 'contactForm']) !!}
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
      <div class="entry input-group col-xs-3">
        {!! Form::text('info[]',null,['class'=>'form-control']) !!}
        <span class="input-group-btn">
          <button class="btn btn-success btn-add" type="button">
            <span class="glyphicon glyphicon-plus"></span>
          </button>
        </span>
      </div>
    </div>
    <div class="form-group">
        {!! Form::submit('Submit', ['class' => 'btn btn-primary form-control']) !!}
    </div>
    {!! Form::close() !!}
@stop
