<div class="crad">
    <div class="card-header">
        <h3 class="card-title">{{ $user->name }}</h3>
    </div>
    <div class="card-body">
        <img class="rounded img-fluid" src="{{ Gravatar::get($user->email, ['size' => 500]) }}" alt="">
    </div>
</div>
@include('user_follow.follow_button')