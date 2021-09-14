<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
        @if($action == 1)
        <div class="widget-content-area br-4">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 text-center">
                        <h5><b>Cajones de Estacionamiento</b></h5>
                    </div>
                </div>
            </div>

            @include('common.search', ['create' => 'tarifas_create'])
            @include('common.alerts')

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped table-checkable table-highlight-head mb-4">
                    <thead>
                        <tr>
                            <th class="">ID</th>
                            <th class="">DESCRIPCIÓN</th>
                            <th class="">ESTATUS</th>
                            <th class="">TIPO</th>
                            <th class="text-center">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($info as $r)
                            <tr>
                                <td><p class="mb-0">{{$r->id}}</p></td>
                                <td>{{$r->descripcion}}</td>
                                <td>{{$r->status}}</td>
                                <td>{{$r->tipo}}</td>
                                <td class="text-center">
                                    @include('common.actions', ['edit' => 'cajones_edit', 'destroy'=> 'cajones_destroy'])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$info->links()}}
            </div>
        </div>
        @elseif($action == 2)
            @include('livewire.cajones.form')
        @endif
    </div>
</div>

<script type="text/javascript">

    function Confirm (id) {
       let me = this
       swal({
            title: 'CONFIRMAR',
            text: '¿DESEAS ELIMINAR EL REGISTRO?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            closeOnConfirm: false
        },
        function() {
            console.log('ID', id);
            window.livewire.emit('deleteRow', id)
            //toastr.success('info', 'Registro eliminado con éxito')
            swal.close()
        })
   }
</script>
