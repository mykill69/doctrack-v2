   @extends('home.main')
   @section('body')
       <div class="main-content">
           <section class="section">
               <div class="row row-deck">
                   <div class="col-12">
                       <div class="card position-relative text-center">
                           <!-- Logo centered on top of the card header -->
                           <img src="{{ asset('template/img/cpsu_logo.png') }}" alt="cpsu logo" width="110" height="110"
                               class="position-absolute" style="top: -50px; left: 50%; transform: translateX(-50%);">

                           <div class="card-header">
                               <!-- Add padding-top to account for the logo -->
                               <h4>Routing Slip</h4>
                           </div>

                           <div class="card-body">
                               <div class="slip-wrapper">
                                   <iframe src="{{ route('pdfSlip', ['id' => $slip->id]) }}" width="100%" height="850" style="border: none;"></iframe>
                               </div>
                           </div>
                       </div>
                   </div>


               </div>
           </section>
       </div>


       <!-- Page Specific JS File -->
   @endsection
