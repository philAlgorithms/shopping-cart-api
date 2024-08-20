<x-layout.bootstrap>
    <form action="/auth/login/admin" method="POST">
        @csrf

        {{ json_encode(request()->all()) }}
        @if (request('failed'))
            <div class="pam _3-95 _9ay3 uiBoxRed" role="alert" tabindex="0" id="error_box">
                <div class="fsl fwb fcb">Wrong credentials</div>
                <div>Invalid username or password</div>
            </div>
        @endif
        <img class="mb-4" src="https://getbootstrap.com/docs/4.0/assets/brand/bootstrap-solid.svg" alt=""
            width="72" height="72">
        <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
        <label for="user_mail" class="sr-only">Email address</label>
        <input type="email" id="user_email" class="form-control" placeholder="Email address" required autofocus>
        <label for="user_password" class="sr-only">Password</label>
        <input id="user_password" type="password" class="form-control" placeholder="Password" required>
        <div class="checkbox mb-3">
            <label>
                <input type="checkbox" value="remember-me"> Remember me
            </label>
        </div>
        <div class="text-center">
		    <button type="button" id="login" class="btn bg-gradient-info w-100 mt-4 mb-0">Sign in</button>

		    <button class="btn bg-gradient-primary w-100 mt-4 mb-0 d-none" id="btn-loading" type="button" disabled>
			<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
  			<span id="load-text"> Please wait...</span>
		    </button>
                  </div>
        <p class="mt-5 mb-3 text-muted">&copy; 2017-2018</p>
    </form>
    <x-slot:scripts>
        <script src="{{ url('/js/auth/login/buyer.js') }}"></script>
    </x-slot>
</x-layout.bootstrap>
