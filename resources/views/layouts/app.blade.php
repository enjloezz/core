@extends('layouts.master')

@section('body_class', 'skin-blue sidebar-mini ' . ((\Session::has('collapse')) ? 'sidebar-collapse' : ''))

@section('body')
    <div class="wrapper">

    <!-- Main Header -->
            @auth
        <header class="main-header">

            <a href="/" class="logo">

                <span class="logo-mini"><img src="/images/liman_logo_white.png" alt="logo" height="35px" style="margin-top:5px"></span>

                <span class="logo-lg"><img src="/images/liman_logo_white.png" alt="logo" height="35px" style="float: left;margin-top:5px"><b>Liman MYS</b></span>
            </a>
            <!-- Header Navbar -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button"
                   onclick="request('{{route('set_collapse')}}',new FormData(),null)">
                </a>

                <!-- Navbar Right Menu -->
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            {{__("Dil")}}
                          </a>
                          <ul class="dropdown-menu">
                            <li>
                              @if (session('locale') === "tr")
                                <a href="javascript:void(0)" style="cursor: not-allowed;"><b>Türkçe</b></a>
                                <a href="{{route('set_locale', ['locale' => 'en'])}}">English</a>
                              @elseif (session('locale') === "en")
                                <a href="{{route('set_locale', ['locale' => 'tr'])}}">Türkçe</a>
                                <a href="javascript:void(0)" style="cursor: not-allowed;"><b>English</b></a>
                              @endif
                            </li>
                          </ul>
                        </li>
                        <li style="color:white;line-height: 50px">
                            {{__("Versiyon : ") . env('APP_VERSION')}}
                        </li>
                        <!-- Notifications: style can be found in dropdown.less -->
                        <li id="notifications-menu" class="dropdown notifications-menu">
                            @include('l.notifications')
                        </li>

                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <span class="hidden-xs">{{auth()->user()->name}}</span>
                            </a>

                            <ul class="dropdown-menu">
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div>
                                        {{__("Giriş Yapılan son ip adresi : ") . auth()->user()->last_login_ip}}</br>
                                        {{__("Giriş Tarihi : ") . auth()->user()->last_login_at}}<br><br>
                                    </div>
                                    <div class="pull-left">
                                        <a href="{{route('my_profile')}}"
                                           class="btn btn-default btn-flat">{{__("Profil")}}</a>
                                    </div>
                                    <div class="pull-right">
                                        <a onclick="request('/cikis',new FormData(),null)"
                                           class="btn btn-default btn-flat">{{__("Çıkış Yap")}}</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <aside class="main-sidebar">

            <section class="sidebar">

                <ul class="sidebar-menu" data-widget="tree">
                    <!-- Sidebar Menu -->
                    @if(\Illuminate\Support\Facades\DB::table("user_favorites")->where(["user_id" => auth()->user()->id])->count())
                        <li class="header">{{__("Favori Sunucular")}}</li>
                        @foreach(\App\Server::find(\Illuminate\Support\Facades\DB::table("user_favorites")->where(["user_id" => auth()->user()->id])->pluck("server_id")->toArray()) as $favorite)
                            <li class="treeview">
                                <a href="#">
                                    <i class="fa fa-server "></i>
                                    <span>{{$favorite->name}}</span>
                                    <span class="pull-right-container">
                                            <i class="fa fa-angle-left pull-right"></i>
                                        </span>
                                </a>
                                <ul class="treeview-menu">
                                    <li class="">
                                        <a href="/sunucular/{{$favorite->id}}">
                                            <i class="fa fa-info "></i>
                                            <span>{{__("Sunucu Detayları")}}</span>
                                        </a>
                                    </li>

                                    @foreach(\App\Extension::find($favorite->extensions()->pluck("id")->toArray()) as $extension)
                                    @if(\App\Permission::can(auth()->user()->id,'extension',$extension->id) == false)
                                        @continue
                                    @endif
                                        <li class="">
                                            <a href="/l/{{$extension->id}}/{{$favorite->city}}/{{$favorite->id}}">
                                                <i class="fa fa-{{$extension->icon}} "></i>
                                                <span>{{__($extension->name)}}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                    @if($favorite->type == "linux_ssh")
                                        <li class="">
                                            <a onclick="terminal('{{$favorite->id}}','{{$favorite->name}}')" href="#">
                                                <i class="fa fa-info "></i>
                                                <span>{{__("Terminal")}}</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>

                            </li>
                        @endforeach
                    @endif
                    <li class="header">{{__("Sunucular")}}</li>
                    <li class="">
                        <a href="/sunucular">
                            <i class="fa fa-server "></i>
                            <span>{{__("Sunucular")}}</span>
                        </a>
                    </li>
                    @if(count(extensions()))
                      <li class="header">{{__("Eklentiler")}}</li>
                      @foreach(extensions() as $extension)
                          <li class="ext_nav" @if($loop->iteration > env('NAV_EXTENSION_HIDE_COUNT', 10)) style="display:none;"@endif>
                              <a href="/l/{{$extension->id}}">
                                  <i class="fa fa-{{$extension->icon}} "></i>
                                  <span>{{__($extension->name)}}</span>
                              </a>
                          </li>
                      @endforeach
                      @if(count(extensions()) > env('NAV_EXTENSION_HIDE_COUNT', 10))
                        <li class="ext_nav_more_less">
                            <a href="javascript:void(0)">
                                <span>{{__('...daha fazla')}}</span>
                            </a>
                        </li>
                      @endif
                    @endif

                    @if(auth()->user()->isAdmin())
                        <li class="header">{{__("Yönetim Paneli")}}</li>
                        <li class="">
                            <a href="/eklentiler">
                                <i class="fa fa-plus "></i>
                                <span>{{__("Eklentiler")}}</span>
                            </a>
                        </li>
                        <li class="">
                            <a href="/betikler">
                                <i class="fa fa-subscript "></i>
                                <span>{{__("Betikler")}}</span>
                            </a>
                        </li>
                        <li class="">
                            <a href="/ayarlar">
                                <i class="fa fa-cog "></i>
                                <span>{{__("Ayarlar")}}</span>
                            </a>
                        </li>
                        <li class="">
                            <a href="/talepler">
                                <i class="fa fa-plus "></i>
                                <span>{{__("Yetki Talepleri")}}</span>
                                @if(\App\LimanRequest::where('status',0)->count())
                                    <span class="pull-right-container">
                                        <small class="label pull-right bg-green">{{\App\LimanRequest::where('status',0)->count()}}</small>
                                    </span>
                                @endif
                            </a>
                        </li>
                    @else
                        <li class="header">{{__("Yetki Talebi")}}</li>
                        <li class="">
                            <a href="/taleplerim">
                                <i class="fa fa-key "></i>
                                <span>{{__("Taleplerim")}}</span>
                            </a>
                        </li>
                    @endif

                    <li class="header">{{__("Ayarlar")}}</li>
                    <li class="">
                        <a href="/kasa">
                            <i class="fa fa-key "></i>
                            <span>{{__("Kasa")}}</span>
                        </a>
                    </li>
                    <li class="">
                        <a href="/widgetlar">
                            <i class="fa fa-pie-chart" aria-hidden="true"></i>
                            <span>{{__("Widgetlar")}}</span>
                        </a>
                    </li>

                </ul>
                <!-- /.sidebar-menu -->
            </section>
            <!-- /.sidebar -->
        </aside>
        @endauth
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
          @if(auth()->check() && auth()->user()->status == "1")
            <div class="alert alert-warning" style="padding: 10px 25px 10px 15px !important; font-size: 12px; border-radius: 0px; margin-bottom: 0px; text-align: center; text-shadow: 3px 2px 3px rgba(255,255,255,.2); border: 0px; -webkit-box-shadow: 0px 0px 15px -2px rgba(0,0,0,0.75); -moz-box-shadow: 0px 0px 15px -2px rgba(0,0,0,0.75); box-shadow: 0px 0px 15px -2px rgba(0,0,0,0.75);">
              {{__("Tam yetkili yönetici hesabı ile giriş yaptınız, sisteme zarar verebilirsiniz.")}}
            </div>
          @endif
            <!-- Content Header (Page header) -->
            @if (trim($__env->yieldContent('content_header')))
              <section class="content-header">
                @yield('content_header')
              </section>
            @endif

            <!-- Main content -->
            <section class="content">
              <div class="row">
                <div class="col-md-12">
                  @yield('content')
                </div>
              </div>
            </section>
        </div>
        <!-- /.content-wrapper -->

    </div>

    @include('l.modal-iframe',[
        "id" => "terminal",
        "url" => '',
        "title" => ""
    ])
@stop

@section('adminlte_js')
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
@stop
