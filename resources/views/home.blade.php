@extends('inspinia_v27.app2')

@section('title', $title)

@section('css_plugins')
@endsection

@section('css')
    <style type="text/css">
    </style>
@endsection

@section('content')
  <div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
      <h2>{{ $title }}</h2>
      <ol class="breadcrumb">
        <li>
          <a href="{{ url('/home') }}">{{ $home }}</a>
        </li>
        <li>
          {{ $sistema }}
        </li>
        <li class="active">
          <strong>{{ $modulo }}</strong>
        </li>
      </ol>
    </div>
  </div>

  <div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
      <div class="col-lg-12">
        <div class="tabs-container">
          <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#tab-1"><i class="fa fa-address-card"></i>Información personal</a></li>
            <li class=""><a data-toggle="tab" href="#tab-2"><i class="fa fa-user"></i>Cambiar contraseña</a></li>
          </ul>
          <div class="tab-content">
            <div id="tab-1" class="tab-pane active">
              <div class="panel-body">
                <strong>Lorem ipsum dolor sit amet, consectetuer adipiscing</strong>

                <p>A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of
                    existence in this spot, which was created for the bliss of souls like mine.</p>

                <p>I am so happy, my dear friend, so absorbed in the exquisite sense of mere tranquil existence, that I neglect my talents. I should be incapable of drawing a single stroke at
                    the present moment; and yet I feel that I never was a greater artist than now. When.</p>
              </div>
            </div>
            <div id="tab-2" class="tab-pane">
              <div class="panel-body">
                <strong>Donec quam felis</strong>

                <p>Thousand unknown plants are noticed by me: when I hear the buzz of the little world among the stalks, and grow familiar with the countless indescribable forms of the insects
                    and flies, then I feel the presence of the Almighty, who formed us in his own image, and the breath </p>

                <p>I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine. I am so happy, my dear friend, so absorbed in the exquisite
                    sense of mere tranquil existence, that I neglect my talents. I should be incapable of drawing a single stroke at the present moment; and yet.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('js_plugins')

@endsection

@section('js')
  <script>

  </script>
@endsection
