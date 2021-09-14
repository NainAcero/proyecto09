<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
            <div class="widget-content-area br-4">
                <div class="widget-one">
                   <ul class="nav nav-pills mb-3 mt-3" role="tablist">
                        <li class="nav-item ">
                            <a class="nav-link {{ $tab == 'roles' ? 'active' : '' }}" wire:click="$set('tab', 'roles')" id="tabRoles"  data-toggle="pill" href="#roles_content" role="tab">
                                <i class="la la-user la-2x"></i> ROLES</a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link {{ $tab == 'permisos' ? 'active' : '' }}" wire:click="$set('tab', 'permisos')" id="tabPermisos"  data-toggle="pill" href="#permisos_content" role="tab"  >
                                <i class="la la-key la-2x"></i> PERMISOS</a>
                        </li>
                    </ul>

                    <div class="tab-content" >

                        @if($tab == 'roles')
                            @include('livewire.permisos.roles')
                        @else ($tab == 'permisos')
                            @include('livewire.permisos.permisos')
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    function showRole(role) {
        var data = JSON.parse(role)
        $('#roleName').val(data['name'])
        $('#roleId').val(data['id'])
    }

    function clearRoleSelected() {
        $('#roleName').val('')
        $('#roleId').val(0)
        $('#roleName').focus()
    }

    function showPermission(permission) {
        var data = JSON.parse(permission)
        $('#permisoName').val(data['name'])
        $('#permisoId').val(data['id'])
    }

    function clearPermissionSelected() {
        $('#permisoName').val('')
        $('#permisoId').val(0)
        $('#permisoName').focus()
    }

    function Confirm(id,eventName) {
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
            window.livewire.emit(eventName, id)
            toastr.success('info', 'Registro eliminado con éxito')
            $('#permisoId').val(0)
            $('#permisoName').val('')
            $('#roleId').val(0)
            $('#roleName').val('')
            swal.close()
        })
    }

    function AsignarRoles() {
        console.clear()

        var rolesList = [];
        $('#tblRoles').find('input[type=checkbox]:checked').each(function () {
            rolesList.push(($(this).attr("data-name")));
        });
        // console.log(rolesList)

        if(rolesList.length < 1 ) {
            toastr.error('', 'Selecciona al menos un role')
            return;
        }
        else if( $('#userId option:selected').val() == 'Seleccionar' ) {
            toastr.error('', 'Selecciona el usuario')
            return;
        }

        window.livewire.emit('AsignarRoles', rolesList)
    }

    function AsignarPermisos() {
        if( $('#roleSelected option:selected').val() == 'Seleccionar' ) {
            toastr.error('', 'Selecciona el role')
            return;
        }

        var permisosList = [];
        $('#tblPermisos').find('input[type=checkbox]:checked').each(function () {
            permisosList.push(($(this).attr("data-name")));
        });

        if(permisosList.length < 1 ) {
            toastr.error('', 'Selecciona al menos un permiso')
            return;
        }

        window.livewire.emit('AsignarPermisos', permisosList, $('#roleSelected option:selected').val()) //*
    }


    document.addEventListener('DOMContentLoaded', function () {

        window.livewire.on('msg-ok', msgText => {
            $('#permisoId').val(0)
            $('#permisoName').val('')
            $('#roleId').val(0)
            $('#roleName').val('')
        })

        $('body').on('click','.check-all', function(){ //*

            var state = $(this).is(':checked') ? true : false

            $("#tblPermisos").find('input[type=checkbox]').each(function (e) {

                $(this).prop('checked', state)
            })
        })
    })

</script>
