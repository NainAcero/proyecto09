<div class="widget-content-area ">
    <div class="widget-one">
        <form>
            <div class="row">
                <h5 class="col-sm-12 text-center">Gestionar Usuario</h5>
                <div class="form-group col-lg-4 col-md-4 col-sm-12">
                    <label >Nombre</label>
                    <input wire:model.lazy="nombre" type="text" class="form-control"  placeholder="nombre">
                    @error('nombre') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-lg-4 col-md-4 col-sm-12">
                    <label >Teléfono</label>
                    <input wire:model.lazy="telefono" type="text" class="form-control"  placeholder="10 dígitos" maxlength="10">

                </div>

                <div class="form-group col-lg-4 col-md-4 col-sm-12">
                    <label >Movil</label>
                    <input wire:model.lazy="movil" type="text" class="form-control"  placeholder="10 dígitos" maxlength="10">
                </div>
                <div class="form-group col-lg-4 col-md-4 col-sm-12">
                    <label >Email</label>
                    <input wire:model.lazy="email" type="text" class="form-control"  placeholder="correo@gmail.com">
                    @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-lg-4 col-md-4 col-sm-12">
                    <label >Tipo</label>
                    <select wire:model="tipo" class="form-control text-center">
                        <option value="Elegir" disabled="">Elegir</option>
                        <option value="Cliente" >Cliente</option>
                        <option value="Admin" >Admin</option>
                        <option value="Empleado" >Empleado</option>
                    </select>
                    @error('tipo') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-lg-4 col-md-4 col-sm-12">
                    <label >Password</label>
                    <input wire:model.lazy="password" type="password" class="form-control"  placeholder="contraseña">
                    @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-sm-12">
                    <label >Dirección</label>
                    <input wire:model.lazy="direccion" type="text" class="form-control"  placeholder="dirección...">
                </div>

            </div>
            <div class="row ">
                <div class="col-lg-5 mt-2  text-left">
                    <button type="button" wire:click="doAction(1)" class="btn btn-dark mr-1">
                        <i class="mbri-left"></i> Regresar
                    </button>
                    <button type="button"
                        wire:click="StoreOrUpdate() "
                        class="btn btn-primary ml-2">
                        <i class="mbri-success"></i> Guardar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
