<div id="content" class="main-content">
    <div class="layout-px-spacing">
        <!-- CONTENT AREA -->
        <div class="row layout-top-spacing">
            <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
                <div class="widget-content-area br-4">
                    <div class="widget-one">
                        <h3 class="text-center">Reporte de Ventas Diarias</h3>

                        <div class="row">
                            <div class="col-sm-12 col-md-4 col-lg-4 text-left">
                                <b>Fecha de Consulta</b>: {{\Carbon\Carbon::now()->format('d-m-Y')}}
                                <br>
                                <b>Cantidad Registros</b>: {{ $info->count() }}
                                <br>
                                <b>Total Ingresos</b>: ${{ number_format($sumaTotal,2) }}
                            </div>
                            <div class="col-sm-12 col-md-8 col-lg-8 text-right">
                                @can('reporte_ventasdiarias_exportar')
                                <button class="btn btn-sm btn-dark mt-4">Exportar</button>
                                @endcan
                            </div>
                        </div>

                        <div class="row">
                            <div class="table-responsive mt-3">
                               <table class="table table-bordered table-hover table-striped table-checkable table-highlight-head mb-4">
                                    <thead>
                                        <tr>
                                            <th class="text-center">CÓDIGO</th>
                                            <th class="text-center">VEHÍCULO</th>
                                            <th class="text-center">ACCESO</th>
                                            <th class="text-center">SALIDA</th>
                                            <th class="text-center">TIEMPO</th>
                                            <th class="text-center">TARIFA</th>
                                            <th class="text-center">IMPORTE</th>
                                            <th class="text-center">USUARIO</th>
                                            <th class="text-center">ESTATUS</th>
                                            <th class="text-center">SERVICIO</th>
                                            <th class="text-center"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($info as $r)
                                        <tr>
                                            <td class="text-center"><p class="mb-0">{{$r->barcode}}</p></td>
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
                                            <td class="text-center">
                                                ${{$r->total}}
                                            </td>
                                            <td class="text-center">{{$r->usuario}}</td>
                                            <td class="text-center">{{$r->status}}</td>
                                            <td class="text-center" class="text-center">
                                                @if($r->vehiculo_id == null)
                                                    RENTA
                                                @else
                                                    PENSIÓN
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @can('reporte_ventasdiarias_reimprimir')
                                                <a href="javascript:void(0);"
                                                onclick='var w = window.open("print/order/{{$r->id}}", "_blank", "width=100, height=100"); w.close()' data-toggle="tooltip" data-placement="top" title="Reimprimir Ticket">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                                </a>
                                                @endcan
                                            </td>
                                        </tr>
                                       @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th class="text-right" colspan="10">SUMA IMPORTES:</th>
                                            <th class="text-center" colspan="2">${{ number_format($sumaTotal,2) }}</th>
                                        </tr>
                                    </tfoot>
                               </table>
                               {{$info->links()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--  END CONTENT AREA  -->
    </div>
</div>
