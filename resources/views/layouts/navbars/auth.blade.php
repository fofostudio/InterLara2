<div class="sidebar" data-color="black" data-active-color="danger">
    <div class="logo">
        <a href="http://www.creative-tim.com" class="simple-text logo-mini">
            <div class="logo-image-small">
                <img src="{{ asset('paper') }}/img/logo-small.png">
            </div>
        </a>
        <a href="http://www.creative-tim.com" class="simple-text logo-normal">
            {{ __('InterRapidisimo') }}
        </a>
    </div>
    <div class="sidebar-wrapper">
        <ul class="nav">
            <li class="{{ $elementActive == 'dashboard' ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}">
                    <i class="nc-icon nc-bank"></i>
                    <p>{{ __('Panel de Control') }}</p>
                </a>
            </li>
            <li class="{{ $elementActive == 'debts' ? 'active' : '' }}">
                <a href="{{ route('debts.index') }}">
                    <i class="nc-icon nc-money-coins"></i>
                    <p>{{ __('Pagos & Movimientos') }}</p>
                </a>
            </li>
            <li class="{{ $elementActive == 'guides' ? 'active' : '' }}">
                <a href="{{ route('guides.index') }}">
                    <i class="nc-icon nc-delivery-fast"></i>
                    <p>{{ __('Registro Guias') }}</p>
                </a>
            </li>
            <li class="{{ $elementActive == 'excel_upload' ? 'active' : '' }}">
                <a href="{{ route('excel.upload.form') }}">
                    <i class="nc-icon nc-delivery-fast"></i>
                    <p>{{ __('Subir Registro Sistema') }}</p>
                </a>
            </li>

            <li class="{{ $elementActive == 'cash_closing' ? 'active' : '' }}">
                <a href="{{ route('cash_closings.index', 'dashboard') }}">
                    <i class="nc-icon nc-cart-simple"></i>
                    <p>{{ __('Cierre Caja') }}</p>
                </a>
            </li>

            <li class="{{ $elementActive == 'reports' ? 'active' : '' }}">
                <a href="{{ route('guides.index') }}">
                    <i class="nc-icon nc-delivery-fast"></i>
                    <p>{{ __('Generar Reportes') }}</p>
                </a>
            </li>
            <li class="{{ $elementActive == 'user' || $elementActive == 'profile' ? 'active' : '' }}">
                <a data-toggle="collapse" aria-expanded="true" href="#laravelExamples">
                    <i class="nc-icon nc-settings"></i>
                    <p>
                        {{ __('Gestion App') }}
                        <b class="caret"></b>
                    </p>
                </a>
                <div class="collapse" id="laravelExamples">
                    <ul class="nav">
                        <li class="{{ $elementActive == 'paymentsMang' ? 'active' : '' }}">
                            <a href="{{ route('profile.edit') }}">
                                <span class="sidebar-mini-icon">{{ __('PM') }}</span>
                                <span class="sidebar-normal">{{ __(' Pagos & Movimientos ') }}</span>
                            </a>
                        </li>
                        <li class="{{ $elementActive == 'usersMang' ? 'active' : '' }}">
                            <a href="{{ route('profile.edit') }}">
                                <span class="sidebar-mini-icon">{{ __('US') }}</span>
                                <span class="sidebar-normal">{{ __(' Usuarios Sistema ') }}</span>
                            </a>
                        </li>
                        <li class="{{ $elementActive == 'profile' ? 'active' : '' }}">
                            <a href="{{ route('profile.edit') }}">
                                <span class="sidebar-mini-icon">{{ __('UP') }}</span>
                                <span class="sidebar-normal">{{ __(' Mi Perfil') }}</span>
                            </a>
                        </li>
                        <li class="{{ $elementActive == 'user' ? 'active' : '' }}">
                            <a href="{{ route('page.index', 'user') }}">
                                <span class="sidebar-mini-icon">{{ __('UM') }}</span>
                                <span class="sidebar-normal">{{ __(' User Management ') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</div>
