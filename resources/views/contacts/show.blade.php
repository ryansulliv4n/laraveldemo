@extends('layout/template')
@section('content')
    <h1>Contact</h1>

    <form class="form-horizontal">
        <div class="form-group">
            <label for="firstname" class="col-sm-2 control-label">First Name</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="firstname" placeholder={{$contact->firstname}} readonly>
            </div>
        </div>
        <div class="form-group">
            <label for="lastname" class="col-sm-2 control-label">Last Name</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="lastname" placeholder={{$contact->lastname}} readonly>
            </div>
        </div>
        <div class="form-group">
            <label for="email" class="col-sm-2 control-label">Email</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="email" placeholder={{$contact->email}} readonly>
            </div>
        </div>
        <div class="form-group">
            <label for="phone" class="col-sm-2 control-label">Phone</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="phone" placeholder={{$contact->phone}} readonly>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <a href="{{ url('contact')}}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </form>
@stop
