<footer class="footer footer-black  footer-white ">
    <div class="container-fluid">
        <div class="row">
            <nav class="footer-nav">
                <ul>
                    <li>
                        <a href="{{ route('dashboard') }}">
                            {{ __('Panel de Control') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('debts.index') }}">
                            {{ __('Pagos & Movimientos') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('guides.index') }}">
                            {{ __('Registro Guias') }}
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="credits ml-auto">
                <span class="copyright">
                    ©
                    <script>
                        document.write(new Date().getFullYear())
                    </script>{{ __(', hecho con ') }}<i class="fa fa-heart heart"></i>{{ __(' por ') }}<a class="@if(Auth::guest()) text-white @endif" href="https://www.instagram.com/fofo_studio" target="_blank">{{ __('Fofo Studio') }}</a>
                </span>
            </div>
        </div>
    </div>
</footer>
