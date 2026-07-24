<x-guest-layout>

    <x-auth-session-status 
        class="mb-3" 
        :status="session('status')" 
    />


    <form method="POST" action="{{ route('login') }}">

        @csrf


        <div class="mb-3">

            <label class="form-label">
                E-mail
            </label>


            <input 
                type="email"
                name="email"
                class="form-control"
                value="{{ old('email') }}"
                required
                autofocus
            >


            @error('email')

                <div class="text-danger small mt-1">
                    {{ $message }}
                </div>

            @enderror


        </div>



        <div class="mb-3">

            <label class="form-label">
                Senha
            </label>


            <input
                type="password"
                name="password"
                class="form-control"
                required
            >


            @error('password')

                <div class="text-danger small mt-1">
                    {{ $message }}
                </div>

            @enderror


        </div>



        <div class="form-check mb-4">

            <input 
                class="form-check-input"
                type="checkbox"
                name="remember"
                id="remember"
            >

            <label 
                class="form-check-label"
                for="remember"
            >
                Lembrar acesso
            </label>

        </div>



        <button 
            class="btn btn-primary w-100 py-2"
            type="submit"
        >

            <i class="bi bi-box-arrow-in-right"></i>

            Entrar

        </button>


    </form>


</x-guest-layout>