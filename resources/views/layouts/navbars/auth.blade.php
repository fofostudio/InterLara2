<div class="sidebar" data-color="black" data-active-color="danger">
    <div class="logo">
        <!-- Logo para pantallas pequeñas -->
        <a href="#" class="simple-text logo-mini d-block d-md-none">
            <div class="logo-image-small">
                <img src="{{ asset('paper') }}/img/logo-small.png">
            </div>
        </a>

        <!-- Logo y punto para pantallas normales -->
        <div class="d-none d-md-block">
            <a href="#" class="simple-text logo-normal">
                <img src="{{ asset('paper') }}/img/logo-large.png" alt="InterRapidisimo" class="img-fluid" style="padding: 10px">
            </a>
            @if (auth()->user()->point)
                <strong class="text-white d-block mt-2 text-center">{{ __('Punto:') }} {{ auth()->user()->point->number }}</strong>
            @else
                <strong class="text-white d-block mt-2 text-center">{{ __('Sin punto asignado') }}</strong>
            @endif
        </div>

        <!-- Texto InterRapidisimo para pantallas pequeñas -->
        <a href="#" class="simple-text logo-normal d-block d-md-none">
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
                    <p>{{ __('Registrar Movimiento') }}</p>
                </a>
            </li>
            <li class="{{ $elementActive == 'guides' ? 'active' : '' }}">
                <a href="{{ route('guides.index') }}">
                    <i class="nc-icon nc-delivery-fast"></i>
                    <p>{{ __('Registrar Guias') }}</p>
                </a>
            </li>
            <li class="{{ $elementActive == 'excel_upload' ? 'active' : '' }}">
                <a href="{{ route('excel.upload.form') }}">
                    <i class="nc-icon nc-delivery-fast"></i>
                    <p>{{ __('Cargar Excel') }}</p>
                </a>
            </li>

            <li class="{{ $elementActive == 'cash_closing' ? 'active' : '' }}">
                <a href="{{ route('cash_closings.index', 'dashboard') }}">
                    <i class="nc-icon nc-cart-simple"></i>
                    <p>{{ __('Cerrar Caja') }}</p>
                </a>
            </li>
            <li
                class="{{ $elementActive == 'paymentsRep' || $elementActive == 'guideReg' || $elementActive == 'salesRep' || $elementActive == 'logApp' ? 'active' : '' }}">
                <a data-toggle="collapse" aria-expanded="true" href="#laravelExamples3">
                    <i class="nc-icon  nc-delivery-fast"></i>
                    <p>
                        {{ __('Generar Reportes') }}
                        <b class="caret"></b>
                    </p>
                </a>
                <div class="collapse show" id="laravelExamples3">
                    <ul class="nav">
                        <li class="{{ $elementActive == 'paymentsRep' ? 'active' : '' }}">
                            <a href="{{ route('debts.pending') }}">
                                <span class="sidebar-mini-icon">{{ __('DS') }}</span>
                                <span class="sidebar-normal">{{ __(' Deudas & Pagos en Sistema ') }}</span>
                            </a>
                        </li>
                        <li class="{{ $elementActive == 'guideRep' ? 'active' : '' }}">
                            <a href="{{ route('profile.edit') }}">
                                <span class="sidebar-mini-icon">{{ __('GR') }}</span>
                                <span class="sidebar-normal">{{ __(' Guias Registradas ') }}</span>
                            </a>
                        </li>
                        <li class="{{ $elementActive == 'salesRep' ? 'active' : '' }}">
                            <a href="{{ route('page.index', 'user') }}">
                                <span class="sidebar-mini-icon">{{ __('RV') }}</span>
                                <span class="sidebar-normal">{{ __(' Reporte Ventas ') }}</span>
                            </a>
                        </li>
                        <li class="{{ $elementActive == 'logApp' ? 'active' : '' }}">
                            <a href="{{ route('page.index', 'user') }}">
                                <span class="sidebar-mini-icon">{{ __('RA') }}</span>
                                <span class="sidebar-normal">{{ __(' Registro Aplicacion ') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="{{ $elementActive == 'user' || $elementActive == 'profile' ? 'active' : '' }}">
                <a data-toggle="collapse" aria-expanded="true" href="#laravelExamples">
                    <i class="nc-icon nc-settings"></i>
                    <p>
                        {{ __('Gestion App') }}
                        <b class="caret"></b>
                    </p>
                </a>
                <div class="collapse show" id="laravelExamples">
                    <ul class="nav">
                        <li class="{{ $elementActive == 'paymentsMang' ? 'active' : '' }}">
                            <a href="{{ route('debts.pending') }}">
                                <span class="sidebar-mini-icon">{{ __('PM') }}</span>
                                <span class="sidebar-normal">{{ __(' Pagos & Movimientos ') }}</span>
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
                                <span class="sidebar-normal">{{ __(' Gestion de Usuarios ') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @if (auth()->user()->role_id == '1')
                <li
                    class="{{ $elementActive == 'userAdmin' || $elementActive == 'points' || $elementActive == 'roles' ? 'active' : '' }}">
                    <a data-toggle="collapse" aria-expanded="true" href="#laravelExamples2">
                        <i class="nc-icon nc-settings"></i>
                        <p>
                            {{ __('SuperAdmin') }}
                            <b class="caret"></b>
                        </p>
                    </a>
                    <div class="collapse show" id="laravelExamples2">
                        <ul class="nav">
                            <li class="{{ $elementActive == 'points' ? 'active' : '' }}">
                                <a href="{{ route('superadmin.points.index') }}">
                                    <span class="sidebar-mini-icon">{{ __('GP') }}</span>
                                    <span class="sidebar-normal">{{ __(' Gestion de Puntos ') }}</span>
                                </a>
                            </li>
                            <li class="{{ $elementActive == 'roles' ? 'active' : '' }}">
                                <a href="{{ route('superadmin.roles.index') }}">
                                    <span class="sidebar-mini-icon">{{ __('GR') }}</span>
                                    <span class="sidebar-normal">{{ __(' Gestion de Roles ') }}</span>
                                </a>
                            </li>
                            <li class="{{ $elementActive == 'userAdmin' ? 'active' : '' }}">
                                <a href="{{ route('superadmin.users.index') }}">
                                    <span class="sidebar-mini-icon">{{ __('SA') }}</span>
                                    <span class="sidebar-normal">{{ __(' Gestion de Usuarios Super Admin ') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif

        </ul>
    </div>
</div>
