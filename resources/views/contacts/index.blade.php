@extends('layout/template')

@section('content')
 <meta name="_token" content="{{ csrf_token() }}" />
 <script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
 <script src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
 <script src="//cdn.datatables.net/1.10.10/js/jquery.dataTables.min.js"></script>
 <script type="text/javascript" src="{{ URL::asset('js/contacts.js') }}"></script>
 <script type="text/javascript" src="{{ URL::asset('js/ajaxformhandler.js') }}"></script>
 <div id="dialog"></div>
 <h1>Contacts</h1>
 <a href="{{url('/contacts/create')}}" class="btn btn-success dialog">Create Contact</a>
 <hr>
 <table id="contactTable" class="table table-striped table-bordered table-hover">
     <thead>
     <tr class="bg-info">
         <th>First Name</th>
         <th>Last Name</th>
         <th>Email</th>
         <th>Phone</th>
         <th></th>
         <th></th>
     </tr>
     </thead>
     <tbody>
     @foreach ($contacts as $contact)
         <tr>
             <td>{{ $contact->firstname }}</td>
             <td>{{ $contact->lastname }}</td>
             <td>{{ $contact->email }}</td>
             <td>{{ $contact->phone }}</td>
             <td class="editLink"><a href="{{route('contacts.edit',$contact->id)}}" class="btn btn-warning dialog">Edit</a></td>
             <td class="deleteLink">
             {!! Form::open(['method' => 'DELETE', 'route'=>['contacts.destroy', $contact->id], 'class'=>'contactForm']) !!}
             {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
             {!! Form::close() !!}
             </td>
         </tr>
     @endforeach

     </tbody>
 </table>
@endsection
