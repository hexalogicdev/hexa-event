@extends('layouts.public')
@section('title','Activate Organization')
@section('public-content')
<section class="py-5 bg-light"><div class="container"><div class="mx-auto" style="max-width:620px"><x-ui.card title="Activate your organization" :description="'You have been invited to join '.$invitation->organization->name.' as its owner.'">
<form method="POST" action="{{ route('organization-invitations.accept',$token) }}" class="d-grid gap-3">@csrf
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
<div><label class="form-label">Name</label><input class="form-control" name="name" value="{{ old('name',$invitation->name) }}" required></div><div><label class="form-label">Email</label><input class="form-control" value="{{ $invitation->email }}" disabled></div><div><label class="form-label">Password</label><input type="password" class="form-control" name="password" required autocomplete="new-password"></div><div><label class="form-label">Confirm password</label><input type="password" class="form-control" name="password_confirmation" required autocomplete="new-password"></div><button class="btn btn-eventify">Activate account</button><p class="text-muted small mb-0">This invitation expires {{ $invitation->expires_at->diffForHumans() }} and can only be accepted once.</p></form>
</x-ui.card></div></div></section>
@endsection

