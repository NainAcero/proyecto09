<div>
    @if($section == 1)
    <div id="content" class="main-content">
        <div class="layout-px-spacing">
            <!--Sección Rentas -->
            <div class="row" id="cancel-row">
                <div class="col-lg-12 layout-spacing layout-top-spacing">
                    <div class="statbox widget box box-shadow">
                        <div class="widget-header">
                            <div class="row">
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12 mt-3">
                                    <h3 class="text-center">Rentas</h3>
                                </div>
                            </div>
                        </div>
                        <div class="widget-content widget-content-area">
                            <div class="row mt-1">

                                <div class="col-lg-4 col-md-4 col-sm-12">
                                    @can('rentas_buscar')
                                    <div class="input-group mb-4">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="la la-barcode"></i></span>
                                        </div>
                                        <input type="text" id="code" wire:keydown.enter="$emit('doCheckOut','',2)" wire:model="barcode"
                                        class="form-control" maxlength="9" placeholder="Escanea el código de barras" autofocus>
                                        <div class="input-group-append">
                                            <span wire:click="$set('barcode','')" class="input-group-text " style="cursor:pointer; "><i class="la la-remove la-lg "></i> </span>
                                        </div>
                                    </div>
                                    @endcan
                                </div>

                                <div class="col-lg-4 col-md-4 col-sm-12">
                                    @can('rentas_ticket_visita')
                                    <button wire:click.prevent="TicketVisita()" class="btn btn-primary btn-lg btn-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                    TICKET DE VISITA</button>
                                    @endcan
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12">
                                    @can('rentas_ticket_renta')
                                    <button wire:click="$set('section', 3)" class="btn btn-warning btn-lg btn-block">TICKET DE RENTA
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                    </button>
                                    @endcan
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col">
                                    <div class="row">
                                        @foreach($cajones as  $c)
                                        @can('rentas_entradas_salidas')
                                        <div class="col-lg-2 col-md-2 col-sm-6 xs-6">

                                            @if($c->status =='DISPONIBLE')
                                            <span
                                            id="{{$c->tarifa_id}}"  style="cursor: pointer;"
                                            data-status="{{$c->status}}" data-id="{{$c->id}}"
                                            onclick="openModal('{{$c->tarifa_id}}','{{$c->id}}')"
                                            class="badge-chip badge-success mt-3 mb-3 ml-2 btncajon bs-popover" >

                                            @else
                                            <span
                                            id="{{$c->tarifa_id}}" style="cursor: pointer;"
                                            data-status="{{$c->status}}" data-id="{{$c->id}}" data-barcode="{{$c->barcode}}"
                                            onclick="eventCheckOut('doCheckOut','{{$c->barcode}}','2')"
                                            class="badge-chip badge-danger mt-3 mb-3 ml-2 btncajon bs-popover ">
                                            @endif

                                            <img src="images/{{$c->imagen}}" alt="Person" width="96" height="96">
                                            <span class="text">{{$c->descripcion}}</span>
                                        </span>

                                    </div>
                                    @endcan
                                    @endforeach
                                </div>
                            </div>

                            <input type="hidden" id="tarifa"/>
                            <input type="hidden" id="cajon"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--modal-->
        <div class="modal fade" id="modalRenta" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" >Descripción del Vehículo</h5>
                    </div>
                    <div class="modal-body">
                        <input type="text"  wire:keydown.enter="$emit('doCheckIn', $('#tarifa').val(),  $('#cajon').val(), 'DISPONIBLE', $('#comment').val() )" id="comment" maxlength="30" class="form-control"  autofocus>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-dark" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cancelar</button>
                        <button type="button" class="btn btn-primary saveRenta">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @elseif($section == 2)
        @include('livewire.rentas.salidas')

    @elseif($section == 3)
        @include('livewire.rentas.ticketrenta')

    @endif
</div>

<script>
    function openModal(tarifa,cajon) {
        $('#tarifa').val(tarifa)
        $('#cajon').val(cajon)

        $('#modalRenta').modal('show')
    }

    function eventCheckOut(eventName,barcode, actionValue) {
        window.livewire.emit(eventName, barcode, actionValue)
        $('#modalRenta').modal('hide')
        $('#comment').val('')
    }

    document.addEventListener('DOMContentLoaded', function () {
        $('body').on('click','.saveRenta', function() {
            var ta = $('#tarifa').val()
            var ca = $('#cajon').val()
            $('#modalRenta').modal('hide')
            window.livewire.emit('doCheckIn',ta, ca, 'DISPONIBLE', $.trim($('#comment').val()))
        })

        window.livewire.on('print', ticket => {
            var ruta = "{{url('print/order')}}" + '/' + ticket
            var w = window.open(ruta, "_blank", "width=100, height=100");
        })

        window.livewire.on('print-pension', ticketP => {
            var ruta = "{{url('ticket/pension')}}" + '/' + ticketP
            var w = window.open(ruta, "_blank", "width=100, height=100");
        })

        window.livewire.on('getin-ok', resultText => {
            $('#comment').val('')
            $('#modalRenta').modal('hide')
        })
    })

</script>
