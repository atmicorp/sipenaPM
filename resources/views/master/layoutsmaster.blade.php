<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Perancangan Manufaktur | Politeknik ATMI Surakarta </title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('css/fontawesome-free/css/all.min.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="{{asset('css/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{asset('css/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- JQVMap -->
  <link rel="stylesheet" href="{{asset('css/jqvmap/jqvmap.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('css/adminlte.min.css')}}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{asset('css/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
  
  <!-- summernote -->
  <link rel="stylesheet" href="{{asset('css/summernote/summernote-bs4.min.css')}}">
  <link rel="icon" href="{{asset('images/atmi.png')}}?v=2" type="image/x-icon">
  <!-- Crop Image -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
  @yield('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="{{asset('images/atmi.png')}}" alt="AtmiLogo" height="100" width="100">
  </div>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Notifications Dropdown Menu -->
      <!-- <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge">15</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">15 Notifications</span>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-envelope mr-2"></i> 4 new messages
            <span class="float-right text-muted text-sm">3 mins</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-users mr-2"></i> 8 friend requests
            <span class="float-right text-muted text-sm">12 hours</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-file mr-2"></i> 3 new reports
            <span class="float-right text-muted text-sm">2 days</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
        </div>
      </li> -->
      
      <!-- Profile Button -->
        <li class="nav-item dropdown">
          <a class="nav-link d-flex align-items-center" data-toggle="dropdown" href="#">
            
            <!-- operator ternary (?:) untuk memilih antara foto yang ada dan foto default. -->
            <img src="{{ Auth::user()->details->photo ? 'data:image/jpeg;base64,' . Auth::user()->details->photo : asset('images/pp.jpg') }}" class="img-circle elevation-2 mb-2" alt="Default User Image" style="width: 30px; height: 30px;">
          
            <p class="mb-0">
              <i class="fas fa-caret-down ml-2"></i>
            </p>
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <div class="text-center p-3" style="background-color: #d9f3fa">
            <!-- operator ternary (?:) untuk memilih antara foto yang ada dan foto default. -->
            <img src="{{ Auth::user()->details->photo ? 'data:image/jpeg;base64,' . Auth::user()->details->photo : asset('images/pp.jpg') }}" class="img-circle elevation-2 mb-2" alt="Default User Image" style="width: 60px; height: 60px;">
              <p class="mb-0">
                {{ Auth::user()->name }}
              </p>
            </div>
            <div class="dropdown-divider"></div>
              <a href="{{ route('myprofile') }}" class="dropdown-item">
                <i class="fas fa-user mr-2"></i> My Profile
              </a>
              <div class="dropdown-divider"></div>
              
              <a href="{{ route('logout') }}" class="dropdown-item">
                <i class="fas fa-sign-out-alt mr-2"></i> Sign Out
              </a>
          </div>
        </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-light-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <img src="{{asset('images/atmi.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">Penilaian Mahasiswa</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-2  mb-2 d-flex">
        <div class="info">
        <h5><strong>{{ strtoupper(Auth::user()->roles->first()->name) }}</strong></h5>
        </div>
      </div>
     

     
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('myprofile') }}" class="nav-link {{ request()->routeIs('myprofile') ? 'active' : '' }}">
            <i class="nav-icon fas fa-user"></i>
              <p>
                My Profile
              </p>
            </a>
          </li>

          @hasanyrole('Dosen|Admin')

          <li class="nav-header">FORM PENILAIAN MAGANG</li>
          <li class="nav-item {{ request()->routeIs('penilaianmagang') || request()->routeIs('hasilpenilaianmaganguntukdosen') || request()->routeIs('aspekpenilaiansp') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->routeIs('penilaianmagang') || request()->routeIs('hasilpenilaianmaganguntukdosen') || request()->routeIs('aspekpenilaiansp') ? 'active' : '' }}">
              <i class="nav-icon fas fa-briefcase"></i>
              <p>Magang</p>
              <i class="fas fa-angle-left right"></i>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('penilaianmagang') }}" class="nav-link {{ request()->routeIs('penilaianmagang') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Penilaian</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('hasilpenilaianmaganguntukdosen') }}" class="nav-link {{ request()->routeIs('hasilpenilaianmaganguntukdosen') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Hasil Penilaian</p>
                </a>
              </li>
            </ul>
          </li>


          <li class="nav-header">FORM PENILAIAN TA</li>

                <!-- Sidang Proposal -->
                <li class="nav-item {{ request()->routeIs('penilaianTA', 'hasilpenilaianTAuntukdosen') && request()->route('id') == 1 ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('penilaianTA', 'hasilpenilaianTAuntukdosen') && request()->route('id') == 1 ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>Sidang Proposal</p>
                        <i class="fas fa-angle-left right"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('penilaianTA', ['id' => 1]) }}" class="nav-link {{ request()->routeIs('penilaianTA') && request()->route('id') == 1 ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Penilaian</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('hasilpenilaianTAuntukdosen', ['id' => 1]) }}" class="nav-link {{ request()->routeIs('hasilpenilaianTAuntukdosen') && request()->route('id') == 1 ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Hasil Penilaian</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Sidang Seminar Hasil -->
                <li class="nav-item {{ request()->routeIs('penilaianTA', 'hasilpenilaianTAuntukdosen') && request()->route('id') == 2 ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('penilaianTA', 'hasilpenilaianTAuntukdosen') && request()->route('id') == 2 ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Sidang Seminar Proges</p>
                        <i class="fas fa-angle-left right"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('penilaianTA', ['id' => 2]) }}" class="nav-link {{ request()->routeIs('penilaianTA') && request()->route('id') == 2 ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Penilaian</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('hasilpenilaianTAuntukdosen', ['id' => 2]) }}" class="nav-link {{ request()->routeIs('hasilpenilaianTAuntukdosen') && request()->route('id') == 2 ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Hasil Penilaian</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Sidang Pendadaran -->
                <li class="nav-item {{ request()->routeIs('penilaianTA', 'hasilpenilaianTAuntukdosen') && request()->route('id') == 3 ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('penilaianTA', 'hasilpenilaianTAuntukdosen') && request()->route('id') == 3 ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chalkboard-teacher"></i>
                        <p>Sidang Pendadaran</p>
                        <i class="fas fa-angle-left right"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('penilaianTA', ['id' => 3]) }}" class="nav-link {{ request()->routeIs('penilaianTA') && request()->route('id') == 3 ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Penilaian</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('hasilpenilaianTAuntukdosen', ['id' => 3]) }}" class="nav-link {{ request()->routeIs('hasilpenilaianTAuntukdosen') && request()->route('id') == 3 ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Hasil Penilaian</p>
                            </a>
                        </li>
                    </ul>
                </li>
          @endhasanyrole

          @role('Admin')

          
          <li class="nav-header"><strong>MANAGE DATA MAGANG</strong></li>
            <li class="nav-item  {{ request()->routeIs('penempatanmagang') || request()->routeIs('viewpenempatanmagang') || request()->routeIs('setupdatamagang') || request()->routeIs('aspekpenilaian')  || request()->routeIs('resetpenilaianmagang') || request()->routeIs('hasilpenilaianmagang')? 'menu-open' : '' }} ">
              <a href="#" class="nav-link {{ request()->routeIs('penempatanmagang') || request()->routeIs('viewpenempatanmagang') || request()->routeIs('setupdatamagang') ||request()->routeIs('aspekpenilaian') ||request()->routeIs('hasilpenilaianmagang') || request()->routeIs('resetpenilaianmagang') ? 'active' : '' }}">
              <i class="nav-icon fas fa-network-wired"></i>
                <p>
                Konfigurasi Magang
                </p>
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
            <ul class="nav nav-treeview">
              <!-- <li class="nav-item">
              <a href="{{ route('penempatanmagang') }}" class="nav-link {{ request()->routeIs('penempatanmagang') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Tambah Peserta Magang</p>
                </a>
              </li> -->
              <li class="nav-item">
                <a href="{{route('viewpenempatanmagang')}}" class="nav-link {{ request()->routeIs('viewpenempatanmagang') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Data Magang</p>
                </a>
                <a href="{{route('aspekpenilaian')}}" class="nav-link {{ request()->routeIs('aspekpenilaian') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Data Aspek Penilaian</p>
                </a>
                <a href="{{route('hasilpenilaianmagang')}}" class="nav-link {{ request()->routeIs('hasilpenilaianmagang') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Hasil Penilaian Magang</p>
                </a>
                <a href="{{route('resetpenilaianmagang')}}" class="nav-link {{ request()->routeIs('resetpenilaianmagang') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Reset Penilaian Magang</p>
                </a>
              </li>
            </ul>
          </li>


          <li class="nav-header"><strong>MANAGE TUGAS AKHIR</strong></li>
          <li class="nav-item">
            <a href="{{route('manageTA')}}" class="nav-link {{ request()->routeIs('manageTA') ? 'active' : '' }}">
              <i class="nav-icon fas fa-tasks"></i>
              <p>
                Konfigurasi TA
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{route('aspekpenilaianindividu')}}" class="nav-link {{ request()->routeIs('aspekpenilaianindividu') ? 'active' : '' }}">
              <i class="nav-icon  fas fa-clipboard-list"></i>
              <p>
                Aspek Penilaian Individu
              </p>
            </a>
          </li>

          <!-- --------- -->
          <li class="nav-item {{ request()->routeIs('setupjadwalta', 'aspekpenilaianta', 'hasilpenilaianta') && request()->route('id') == 1 ? 'menu-open' : '' }}">
              <a href="#" class="nav-link {{ request()->routeIs('setupjadwalta', 'aspekpenilaianta', 'hasilpenilaianta') && request()->route('id') == 1 ? 'active' : '' }}">
                  <i class="nav-icon fas fa-file-alt"></i>
                  <p>Sidang Proposal</p>
                  <i class="fas fa-angle-left right"></i>
              </a>
              <ul class="nav nav-treeview">
                  <li class="nav-item">
                      <a href="{{ route('setupjadwalta', ['id' => 1]) }}" class="nav-link {{ request()->routeIs('setupjadwalta') && request()->route('id') == 1 ? 'active' : '' }}">
                          <i class="far fa-circle nav-icon"></i>
                          <p>Jadwal Presentasi</p>
                      </a>
                  </li>
              </ul>
              <ul class="nav nav-treeview">
                  <li class="nav-item">
                      <a href="{{ route('aspekpenilaianta', ['id' => 1]) }}" class="nav-link {{ request()->routeIs('aspekpenilaianta') && request()->route('id') == 1 ? 'active' : '' }}">
                          <i class="far fa-circle nav-icon"></i>
                          <p>Aspek Penilaian</p>
                      </a>
                  </li>
              </ul>
              <ul class="nav nav-treeview">
                  <li class="nav-item">
                      <a href="{{ route('hasilpenilaianta', ['id' => 1]) }}" class="nav-link {{ request()->routeIs('hasilpenilaianta') && request()->route('id') == 1 ? 'active' : '' }}">
                          <i class="far fa-circle nav-icon"></i>
                          <p>Hasil Penilaian</p>
                      </a>
                  </li>
              </ul>
              
          </li>

        <li class="nav-item {{ request()->routeIs('setupjadwalta', 'aspekpenilaianta', 'hasilpenilaianta') && request()->route('id') == 2 ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->routeIs('setupjadwalta', 'aspekpenilaianta', 'hasilpenilaianta') && request()->route('id') == 2 ? 'active' : '' }}">
                <i class="nav-icon fas fa-chart-bar"></i>
                <p>Sidang Seminar Progres</p>
                <i class="fas fa-angle-left right"></i>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('setupjadwalta', ['id' => 2]) }}" class="nav-link {{ request()->routeIs('setupjadwalta') && request()->route('id') == 2 ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Jadwal Presentasi</p>
                    </a>
                </li>
            </ul>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('aspekpenilaianta', ['id' => 2]) }}" class="nav-link {{ request()->routeIs('aspekpenilaianta') && request()->route('id') == 2 ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Aspek Penilaian</p>
                    </a>
                </li>
            </ul>
            <ul class="nav nav-treeview">
                  <li class="nav-item">
                      <a href="{{ route('hasilpenilaianta', ['id' => 2]) }}" class="nav-link {{ request()->routeIs('hasilpenilaianta') && request()->route('id') == 2 ? 'active' : '' }}">
                          <i class="far fa-circle nav-icon"></i>
                          <p>Hasil Penilaian</p>
                      </a>
                  </li>
              </ul>
        </li>

        <li class="nav-item {{ request()->routeIs('setupjadwalta', 'aspekpenilaianta', 'hasilpenilaianta') && request()->route('id') == 3 ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->routeIs('setupjadwalta', 'aspekpenilaianta', 'hasilpenilaianta') && request()->route('id') == 3 ? 'active' : '' }}">
                <i class="nav-icon fas fa-chalkboard-teacher"></i>
                <p>Sidang Pendadaran</p>
                <i class="fas fa-angle-left right"></i>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('setupjadwalta', ['id' => 3]) }}" class="nav-link {{ request()->routeIs('setupjadwalta') && request()->route('id') == 3 ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Jadwal Presentasi</p>
                    </a>
                </li>
            </ul>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('aspekpenilaianta', ['id' => 3]) }}" class="nav-link {{ request()->routeIs('aspekpenilaianta') && request()->route('id') == 3 ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Aspek Penilaian</p>
                    </a>
                </li>
            </ul>
            <ul class="nav nav-treeview">
                  <li class="nav-item">
                      <a href="{{ route('hasilpenilaianta', ['id' => 3]) }}" class="nav-link {{ request()->routeIs('hasilpenilaianta') && request()->route('id') == 3 ? 'active' : '' }}">
                          <i class="far fa-circle nav-icon"></i>
                          <p>Hasil Penilaian</p>
                      </a>
                  </li>
              </ul>
        </li>

          <!-- ------------- -->


        
          <li class="nav-header"><strong>MANAGE DATA USER</strong></li>
            <li class="nav-item  {{ request()->routeIs('datauser')  ? 'menu-open' : '' }}">
              <a href="#" class="nav-link {{ request()->routeIs('datauser')  ? 'active' : '' }}">
              <i class="nav-icon fas fa-user"></i>
                <p>
                Konfigurasi User
                </p>
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
            <ul class="nav nav-treeview">
              <!-- <li class="nav-item">
              <a href="" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Tambah Data</p>
                </a>
              </li> -->
              <li class="nav-item">
                <a href="{{route('datauser')}}" class="nav-link {{ request()->routeIs('datauser') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Data User</p>
                  
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-header">RESET ALL DATA</li>
          <li class="nav-item {{ request()->routeIs('reset.view') ? 'menu-open' : '' }}">
              <a href="{{ route('reset.view') }}" 
                class="nav-link bg-danger text-white {{ request()->routeIs('reset.view') ? 'active' : '' }}" 
                onclick="return confirm('Apakah Anda yakin ingin mereset semua data?');">
                  <i class="nav-icon fas fa-sync"></i>
                  <p>Reset Data</p>
              </a>
          </li>
          @endrole

          @role('Mahasiswa')
         
          <li class="nav-header">FORM TUGAS AKHIR</li>

                <!-- Sidang Proposal -->
                <li class="nav-item {{ request()->routeIs('formTAmhs') && request()->route('id') == 1 ? 'menu-open' : '' }}">
                    <a href="{{ route('formTAmhs', ['id' => 1]) }}" class="nav-link {{ request()->routeIs('formTAmhs') && request()->route('id') == 1 ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>Sidang Proposal</p>     
                    </a> 
                </li>

                <!-- Sidang Seminar Hasil -->
                <li class="nav-item {{ request()->routeIs('formTAmhs') && request()->route('id') == 2 ? 'menu-open' : '' }}">
                    <a href="{{ route('formTAmhs', ['id' => 2]) }}" class="nav-link {{ request()->routeIs('formTAmhs') && request()->route('id') == 2 ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Sidang Seminar Progres</p>      
                    </a>
                </li>

                <!-- Sidang Pendadaran -->
                <li class="nav-item {{ request()->routeIs('formTAmhs') && request()->route('id') == 3 ? 'menu-open' : '' }}">
                    <a href="{{ route('formTAmhs', ['id' => 3]) }}" class="nav-link {{ request()->routeIs('formTAmhs') && request()->route('id') == 3 ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chalkboard-teacher"></i>
                        <p>Sidang Pendadaran</p>
                    </a>
                </li>

                <li class="nav-header">Download Formulir Revisi</li>
                <li class="nav-item">
                  <a href="{{ route('view_dokumen_magang') }}" class="nav-link {{ request()->routeIs('view_dokumen_magang') ? 'active' : '' }}" target="_blank">
                    <i class="nav-icon fas fa-file-pdf"></i>
                    <p>
                      Magang
                    </p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="{{ route('view_dokumen_ta', ['id' => 1]) }}" class="nav-link {{ request()->routeIs('view_dokumen_ta') ? 'active' : '' }}" target="_blank">
                    <i class="nav-icon fas fa-file-pdf"></i>
                    <p>
                      Sidang Proposal
                    </p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="{{ route('view_dokumen_ta', ['id' => 2]) }}" class="nav-link {{ request()->routeIs('view_dokumen_ta') ? 'active' : '' }}" target="_blank">
                    <i class="nav-icon fas fa-file-pdf"></i>
                    <p>
                      Sidang Seminar Progres
                    </p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="{{ route('view_dokumen_ta', ['id' => 3]) }}" class="nav-link {{ request()->routeIs('view_dokumen_ta') ? 'active' : '' }}" target="_blank">
                    <i class="nav-icon fas fa-file-pdf"></i>
                    <p>
                      Sidang Pendadaran
                    </p>
                  </a>
                </li>
          @endrole



        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  @yield('content')

  <footer class="main-footer">
    <strong>Copyright &copy; 2024-2025 <a href="#">IT YKBS</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 3.2.0
    </div>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{asset('js/jquery/jquery.min.js')}}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{asset('js/jquery-ui/jquery-ui.min.js')}}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{asset('js/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- ChartJS -->
<script src="{{asset('js/chart.js/Chart.min.js')}}"></script>
<!-- Sparkline -->
<script src="{{asset('js/sparklines/sparkline.js')}}"></script>
<!-- JQVMap -->
<script src="{{asset('js/jqvmap/jquery.vmap.min.js')}}"></script>
<script src="{{asset('js/jqvmap/maps/jquery.vmap.usa.js')}}"></script>
<!-- jQuery Knob Chart -->
<script src="{{asset('js/jquery-knob/jquery.knob.min.js')}}"></script>
<!-- daterangepicker -->
<script src="{{asset('js/moment/moment.min.js')}}"></script>
<script src="{{asset('js/daterangepicker/daterangepicker.js')}}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{asset('js/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
<!-- Summernote -->
<script src="{{asset('css/summernote/summernote-bs4.min.js')}}"></script>
<!-- overlayScrollbars -->
<script src="{{asset('css/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('dist/js/adminlte.js')}}"></script>
@yield('scripts')

</body>
</html>
