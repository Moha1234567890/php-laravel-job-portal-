@extends('layouts.site')

@section('content')

    <section class="site-section services-section  block__62849" style="margin-top: 152px;" id="next-section">
        <div class="container">

            <div class="row">

                @if(isset($cats) && $cats->count() > 0)
                    @foreach($cats as $cat)
                <div class=" col-md-4 ">

                    <a href="service-single.html" class="text-center d-block">
                        <span class="fa fa-facebook fa-2x" style="color:#3b74ff" ><span class="icon-magnet d-block"></span></span>
                        <h3>{{$cat->name}}</h3>
                        <p>{{$cat->cat_desc}}</p>
                    </a>

                </div>
                    @endforeach
                    @endif
            </div>
        </div>
    </section>

@endsection



