<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">

        <div class="widget-content-area br-4">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 text-center">
                        <h5><b>Búsqueda de Ticket Extraviado</b></h5>
                    </div>
                </div>
            </div>
            <!--BUSQUEDAS-->
    		<div class="row  mb-4 mt-3">
    			<div class="col-lg-4 col-md-4 col-sm-12">
    				<div class="input-group ">
    					<div class="input-group-prepend">
    						<span class="input-group-text"><i class="la la-search la-lg"></i></span>
    					</div>
    					<input type="text" wire:model="search" class="form-control" placeholder="Datos del vehículo.." >
    				</div>
    			</div>
    		</div>
            <!--TABLA-->
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped table-checkable table-highlight-head mb-4">
                   <thead>
                        <tr>
                            <th class="text-center">CÓDIGO</th>
                            <th class="text-center">ENTRADA</th>
                            <th class="text-center">DATOS DEL COCHE</th>
                            <th class="text-center">#PLACA</th>
                            <th class="text-center">TIPO</th>
                            <th class="text-center">TOTAL AL MOMENTO</th>
                            <th class="text-center">DAR SALIDA</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($info as $r)
                        <tr>
                            <td class="text-center">{{$r->barcode}}</td>
                            <td class="text-center">{{\Carbon\Carbon::parse($r->acceso)->format('d/m/Y h:m:s')}}</td>
                            <td class="text-center">{{$r->descripcion}}</td>
                            <td class="text-center">{{$r->placa}}</td>
                            <td class="text-center">
                                @if($r->vehiculo_id == null)
                                    VISITA
                                @else
                                    RENTA
                                @endif
                            </td>
                            <td class="text-center">${{$r->pago}} + $50.00 multa</td>
                            <td class="text-center" class="text-center">
                                @can('extraviados_salidas')
                                <a href="#" class="btn btn-dark btn-sm" onclick="eventCheckOut('{{$r->barcode}}')"
                                    title="COBRAR Y DAR SALIDA DEL VEHÍCULO">
                                    <i class="la la-check la-2x"></i>
                                </a>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{$info->links()}}
            </div>
            <input type="hidden" id="barcode"/>
             <!--MODAL-->
            <div class="modal fade" id="modalSalida" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" >Confirmar</h5>
                        </div>
                        <div class="modal-body">
                            <h4 class="text-danger">¿Confirmas dar salida al vehículo?</h4>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-dark" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cancelar</button>
                            <button type="button" class="btn btn-primary saveSalida">Aceptar</button>

                        </div>
                    </div>
                </div>
            </div>
        <!--/modal-->
        </div>
    </div>
</div>


<script type="text/javascript">

    function eventCheckOut(barcode) {
       $('#barcode').val(barcode)
       $('#modalSalida').modal('show')
    }

   document.addEventListener('DOMContentLoaded', function () {
       $('body').on('click','.saveSalida', function() {
          var code = $('#barcode').val()
          $('#modalSalida').modal('hide')
          window.livewire.emit('doCheckOut',code, 2 )
      })
   })

</script>
