<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">

        @if($action == 1)
        <div class="widget-content-area br-4">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 text-center">
                        <h5><b>Usuarios de Sistema</b></h5>
                </div>
            </div>
        </div>
        @include('common.search', ['create' => 'usuarios_create'])
        @include('common.alerts')
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped table-checkable table-highlight-head mb-4">
                <thead>
                    <tr>
                        <th class="">NOMBRE</th>
                        <th class="">TELEFONO</th>
                        <th class="">MOVIL</th>
                        <th class="">EMAIL</th>
                        <th class="">TIPO</th>
                        <th class="">DIRECCION</th>
                        <th class="text-center">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($info as $r)
                    <tr>
                        <td><p class="mb-0">{{$r->nombre}}</p></td>
                        <td>{{$r->telefono}}</td>
                        <td>{{$r->movil}}</td>
                        <td>{{$r->email}}</td>
                        <td>{{$r->tipo}}</td>
                        <td>{{$r->direccion}}</td>
                        <td class="text-center">
                            @include('common.actions', ['edit' => 'usuarios_edit', 'destroy'=> 'usuarios_destroy'])
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{$info->links()}}
        </div>
    </div>

    @elseif($action == 2)
        @include('livewire.usuario.form')
    @endif
</div>

<script type="text/javascript">

    function Confirm(id){
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
    }, function() {
        // console.log('ID', id);
        window.livewire.emit('deleteRow', id)
        //toastr.success('info', 'Registro eliminado con éxito')
        swal.close()
    })
   }
</script>
