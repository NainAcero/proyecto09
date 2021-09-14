<div  class="main-content">
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div class="col-12 layout-spacing">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <!--TITULO-->
                        <h4 class="text-center mb-5">Reporte de Ventas Por Fecha</h4>
                        <!--ENCABEZADO-->
                        <div class="row">
                            <div class="col-sm-12 col-md-2 col-lg-2">Fecha inicial
                                <div class="form-group">
                                    <input wire:model.lazy="fecha_ini" class="form-control flatpickr flatpickr-input active" type="text" placeholder="Haz click">
                                </div>
                            </div>

                            <div class="col-sm-12 col-md-2 col-lg-2 text-left">
                                <div class="form-group">Fecha final
                                    <input wire:model.lazy="fecha_fin" class="form-control flatpickr flatpickr-input active" type="text" placeholder="Haz click">
                                </div>
                            </div>

                            <div class="col-sm-12 col-md-1 col-lg-1 text-left">
                                <button type="submit" class="btn btn-info mt-4 mobile-only">Ver</button>
                            </div>

                            <div class="col-sm-12 col-md-1 col-lg-1 text-left ">
                                @can('reporte_ventasporfecha_exportar')
                                <button class="btn btn-dark mt-4 mobile-only">Exportar</button>
                                @endcan
                            </div>

                            <div class="col-sm-12 col-md-3 col-lg-3 offset-lg-3">
                                <b>Fecha de Consulta</b>: {{\Carbon\Carbon::now()->format('d-m-Y')}}
                                <br>
                                <b>Cantidad Registros</b>: {{ $info->count() }}
                                <br>
                                <b>Total Ingresos</b>: ${{ number_format($sumaTotal,2) }}
                            </div>

                        </div>
                        <!--/ENCABEZADO-->
                        <!--ROW TABLA-->
                        <div class="row">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered table-hover table-striped table-checkable table-highlight-head mb-4">
                                    <thead>
                                        <tr>
                                            <th class="text-center">CODIGO</th>
                                            <th class="text-center">VEHÍCULO</th>
                                            <th class="text-center">ACCESO</th>
                                            <th class="text-center">SALIDA</th>
                                            <th class="text-center">TIEMPO</th>
                                            <th class="text-center">TARIFA</th>
                                            <th class="text-center">IMPORTE</th>
                                            <th class="text-center">USUARIO</th>
                                            <th class="text-center">RENTA</th>
                                            <th class="text-center">FECHA</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($info as $r)
                                        <tr>
                                            <td class="text-center">{{$r->barcode}}</td>
                                            <td class="text-center">
                                                {{$r->vehiculo}}
                                                @if($r->descripcion !=null)
                                                    <br>"{{$r->descripcion}}"
                                                @endif
                                            </td>
                                            <td class="text-center">{{$r->acceso}}</td>
                                            <td class="text-center">{{$r->salida}}</td>
                                            <td class="text-center">{{$r->hours}} Hrs.</td>
                                            <td class="text-center">${{number_format($r->tarifa,2)}}</td>
                                            <td class="text-center">${{$r->total}}</td>
                                            <td class="text-center">{{$r->usuario}}</td>
                                            <td class="text-center" class="text-center">
                                                @if($r->vehiculo_id == null)
                                                    VISITA
                                                @else
                                                    PENSIÓN
                                                @endif
                                            </td>
                                            <td class="text-center">{{$r->created_at}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th class="text-right" colspan="9">SUMA IMPORTES:</th>
                                            <th class="text-center" colspan="1">${{ number_format($sumaTotal,2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                                {{$info->links()}}
                            </div>
                        </div>
                        <!--/ROW TABLA-->
                    </div>
                </div>
            </div>
        </div>
    <!-- CONTENT AREA -->
    </div>
</div>
<!--  END CONTENT AREA  -->

