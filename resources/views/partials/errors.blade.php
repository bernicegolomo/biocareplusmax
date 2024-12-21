<div class="row">
    @if (session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{session()->get('error')}}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if (session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{session()->get('success')}}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if (session()->has('verify'))

    <div class="alert alert-danger mt-2" role="alert">
     <form id="resend" method="Post" action="{{route('verify.resend')}}"> @csrf Check your email for verification Link or <input type="hidden" value="{{session()->get('email')}}" name="mail"/><input type="hidden" value=" {{session()->get('user')}}" name="user"/> <input type="submit" style=" background: none; border: none; color: #1a0dab;text-decoration: underline;cursor: pointer; " value="Resend Link"></form>
    </div>
    <script>
        var button = document.createElement('send');
            button.preventDefault();
        let myfunction = () => {

            var form = document.getElementById('resend')
            form.submit();
        }
    </script>
    @endif
    @if ($errors->has('name'))
        <div class="col-lg-12 mb-3">
            <span class="text-danger">{{ $errors->first('name') }}</span>
        </div>
    @endif

    @if ($errors->has('email'))
        <div class="col-lg-12 mb-3">
            <span class="text-danger">{{ $errors->first('email') }}</span>
        </div>
    @endif

    @if ($errors->has('address'))
        <div class="col-lg-12 mb-3">
            <span class="text-danger">{{ $errors->first('address') }}</span>
        </div>
    @endif

    @if ($errors->has('phone'))
        <div class="col-lg-12 mb-3">
            <span class="text-danger">{{ $errors->first('phone') }}</span>
        </div>
    @endif

    @if ($errors->has('country'))
        <div class="col-lg-12 mb-3">
            <span class="text-danger">{{ $errors->first('country') }}</span>
        </div>
    @endif

    @if ($errors->has('nin'))
        <div class="col-lg-12 mb-3">
            <span class="text-danger">{{ $errors->first('nin') }}</span>
        </div>
    @endif

    @if ($errors->has('password'))
        <div class="col-lg-12 mb-3">
            <span class="text-danger">{{ $errors->first('password') }}</span>
        </div>
    @endif

    @if ($errors->has('cpassword'))
        <div class="col-lg-12 mb-3">
            <span class="text-danger">{{ $errors->first('cpassword') }}</span>
        </div>
    @endif
</div>
